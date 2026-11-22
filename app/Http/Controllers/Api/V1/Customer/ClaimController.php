<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Enums\ClaimSource;
use App\Enums\ClaimStatus;
use App\Http\Controllers\Api\V1\Concerns\ResolvesOwnedResources;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ClaimResource;
use App\Models\Claim;
use App\Services\ClaimService;
use App\Support\ApiResponse;
use App\Support\ClaimFormSchema;
use App\Support\PolicyRisks;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClaimController extends Controller
{
    use ResolvesOwnedResources;

    public function __construct(private ClaimService $claims) {}

    public function index(Request $request)
    {
        $request->validate([
            'status'        => ['nullable', Rule::in(ClaimStatus::all())],
            'policy_id'     => ['nullable', 'integer'],
            'updated_since' => ['nullable', 'date'],
            'per_page'      => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $claims = Claim::whereIn('customer_id', $this->ownedIds($request))
            ->when(
                $request->filled('status'),
                fn($q) => $q->where('status', $request->status),
                fn($q) => $q->where('status', '!=', ClaimStatus::CANCELLED)
            )
            ->when($request->filled('policy_id'), fn($q) => $q->where('policy_id', $request->policy_id))
            ->when($request->filled('updated_since'), fn($q) => $q->where('updated_at', '>=', Carbon::parse($request->updated_since)))
            ->with(['policy', 'pendingInfoRequest'])
            ->orderByDesc('updated_at')->orderByDesc('id')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return ClaimResource::collection($claims);
    }

    public function show(Request $request, int $claim)
    {
        $claim = $this->claimFor($request, $claim)
            ->load(['policy', 'pendingInfoRequest', 'documents', 'activities']);

        return new ClaimResource($claim);
    }

    public function store(Request $request)
    {
        $customer = $request->user();
        $docs     = config('claim_forms.documents');

        $data = $request->validate([
            'policy_id'   => ['required', 'integer'],
            'risk_id'     => ['nullable', 'string', 'max:50'],
            'claim_type'  => ['required', Rule::in(ClaimFormSchema::types())],
            'form_data'   => ['required', 'array'],
            'documents'   => ['nullable', 'array', 'max:' . $docs['max_files']],
            'documents.*' => ['file', 'max:' . $docs['max_size_kb'], 'mimes:' . implode(',', $docs['accepted'])],
        ]);

        $idem = $request->header('Idempotency-Key');

        if ($idem !== null && (strlen($idem) < 8 || strlen($idem) > 100)) {
            return ApiResponse::error('invalid_idempotency_key', 'Idempotency-Key must be 8 to 100 characters.', 422);
        }

        $cacheKey = $idem ? 'api-idem:' . $customer->id . ':' . hash('sha256', $idem) : null;

        try {
            if (! $cacheKey) {
                return $this->created($this->submit($request, $data));
            }

            return Cache::lock($cacheKey . ':lock', 30)->block(10, function () use ($request, $data, $cacheKey) {
                if ($id = Cache::get($cacheKey)) {
                    $existing = Claim::whereIn('customer_id', $this->ownedIds($request))->find($id);

                    if ($existing) {
                        return $this->created($existing->load(['policy', 'documents', 'activities']), 200)
                            ->header('Idempotent-Replay', 'true');
                    }
                }

                $claim = $this->submit($request, $data);
                Cache::put($cacheKey, $claim->id, now()->addDay());

                return $this->created($claim);
            });
        } catch (LockTimeoutException) {
            return ApiResponse::error('request_in_progress', 'A request with this Idempotency-Key is still running.', 409);
        }
    }

    public function uploadDocuments(Request $request, int $claim)
    {
        $claim = $this->claimFor($request, $claim);
        $docs  = config('claim_forms.documents');

        if (in_array($claim->status, array_merge(ClaimStatus::terminal(), ClaimStatus::notActive()), true)) {
            return ApiResponse::error('claim_closed', 'Documents can no longer be added to this claim.', 409);
        }

        $request->validate([
            'documents'   => ['required', 'array', 'max:' . $docs['max_files']],
            'documents.*' => ['file', 'max:' . $docs['max_size_kb'], 'mimes:' . implode(',', $docs['accepted'])],
        ]);

        $this->claims->attachDocuments(
            claim: $claim,
            files: $request->file('documents'),
            uploadedBy: null,
            type: 'supporting',
            uploadedByCustomer: $request->user(),
        );

        $claim->touch(); // so updated_since polling sees document-only changes

        return $this->created($claim->load(['policy', 'documents', 'activities']), 201);
    }

    public function respondInfo(Request $request, int $claim)
    {
        $claim = $this->claimFor($request, $claim);

        $data = $request->validate(['response_note' => ['nullable', 'string', 'max:1000']]);

        try {
            $this->claims->respondToInfoRequest($claim, $request->user(), $data['response_note'] ?? null);
        } catch (\DomainException $e) {
            return ApiResponse::error('no_open_request', $e->getMessage(), 409);
        }

        return new ClaimResource($claim->refresh()->load(['policy', 'pendingInfoRequest', 'documents', 'activities']));
    }

    public function cancel(Request $request, int $claim)
    {
        $claim = $this->claimFor($request, $claim);

        $data = $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        if (! in_array($claim->status, ClaimStatus::customerCancellable(), true)) {
            return ApiResponse::error('not_cancellable', 'A claim can only be cancelled by the customer while it is Submitted.', 409);
        }

        $this->claims->cancel($claim, $request->user(), $data['note'] ?? null);

        return new ClaimResource($claim->refresh()->load(['policy', 'pendingInfoRequest', 'documents', 'activities']));
    }

    // ── internals ─────────────────────────────────────────────────────────

    private function submit(Request $request, array $data): Claim
    {
        $customer = $request->user();
        $policy   = $this->policyFor($request, (int) $data['policy_id']);
        $type     = $data['claim_type'];

        if (ClaimFormSchema::fromClass($policy->business_class_name) !== $type) {
            $this->fail('claim_type_mismatch', 'claim_type does not match the policy class.');
        }

        if ($unknown = ClaimFormSchema::unknownFields($type, $data['form_data'])) {
            $this->fail('unknown_fields', 'form_data contains fields this form does not define: ' . implode(', ', $unknown) . '.');
        }

        Validator::make(['form_data' => $data['form_data']], ClaimFormSchema::rules($type))->validate();

        $formData = ClaimFormSchema::normalise($type, ClaimFormSchema::clean($type, $data['form_data']));

        // The event must fall inside the policy period (an expired policy is fine for an earlier event)
        if ($event = ClaimFormSchema::eventDate($type, $formData)) {
            if ($error = ClaimFormSchema::periodError($policy, $event)) {
                $this->fail('event_outside_cover', $error);
            }
        }

        // risk_id is the plate number; translate it to the key the payload uses internally
        $index   = PolicyRisks::index($policy);
        $riskRef = $data['risk_id'] ?? null;

        if ($type === 'motor' && count($index) > 1 && $riskRef === null) {
            $this->fail('risk_required', 'This is a fleet policy. Send the registration number of the vehicle as risk_id.');
        }

        if ($riskRef !== null) {
            $riskKey = $index[PolicyRisks::norm($riskRef)] ?? null;

            if ($riskKey === null) {
                $this->fail('invalid_risk', 'risk_id does not match a vehicle on this policy.');
            }
        } else {
            $riskKey = count($index) === 1 ? reset($index) : null;
        }

        $formData['declaration_date'] ??= today()->toDateString();

        // Client values first, then the server's: the server wins for the fields it owns
        $formData = array_merge(
            ['fullname' => $customer->name, 'email' => $customer->email, 'phone' => $customer->phone],
            $formData,
            ClaimFormSchema::serverFields($type, $policy, $riskKey),
        );

        if ($type === 'motor' && ($formData['driver_type'] ?? null) === 'self') {
            $formData['driver_fullname'] ??= $customer->name;
        }

        if ($riskKey !== null) {
            $formData['_risk_id'] = $riskKey;
        }

        $claim = $this->claims->register(
            customer: $customer,
            policy: $policy,
            claimType: $type,
            formData: $formData,
            source: ClaimSource::CUSTOMER_PORTAL,
            riskId: $riskKey,
        );

        if ($request->hasFile('documents')) {
            $this->claims->attachDocuments(
                claim: $claim,
                files: $request->file('documents'),
                uploadedBy: null,
                type: 'supporting',
                uploadedByCustomer: $customer,
            );
        }

        return $claim->load(['policy', 'documents', 'activities']);
    }

    private function created(Claim $claim, int $status = 201)
    {
        return (new ClaimResource($claim))->response()->setStatusCode($status);
    }

    private function fail(string $code, string $message): never
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(ApiResponse::error($code, $message, 422));
    }
}
