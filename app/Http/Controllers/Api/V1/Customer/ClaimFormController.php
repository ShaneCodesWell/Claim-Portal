<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use App\Support\ClaimFormSchema;
use Dedoc\Scramble\Attributes\Group;

#[Group('Customer - Claim forms', weight: 11)]
class ClaimFormController extends Controller
{
    public function index()
    {
        return response()->json(['data' => collect(ClaimFormSchema::types())
            ->map(fn($t) => ['type' => $t, 'label' => ClaimFormSchema::definition($t)['label']])
            ->values()]);
    }

    /**
     * Fields of one claim form
     *
     * @response array{data: array{type: string, label: string, event_field: string, server_fields: string[], fields: array<int, array{name: string, label: string, type: string, required: bool, required_when: array{field: string, in: string[]}|null, options: string[]|null, item_fields: string[]|null}>, documents: array{accepted: string[], max_size_kb: int, max_files: int}, notes: string|null}}
     */
    public function show(string $type)
    {
        if (! ClaimFormSchema::has($type)) {
            return ApiResponse::error('not_found', 'No claim form for this type.', 404);
        }

        $def = ClaimFormSchema::definition($type);

        return response()->json(['data' => [
            'type'          => $type,
            'label'         => $def['label'],
            /**
             * must fall inside the policy period
             * @var string
             */
            'event_field'   => $def['event_field'],
            /**
             *ignored if sent; taken from the policy
             * @var array
             */
            'server_fields' => $def['server_fields'],
            'fields'        => collect($def['fields'])->map(function ($f, $name) {
                $when = null;

                if (! empty($f['conditional']) && preg_match('/^required_if:form_data\.([^,]+),(.+)$/', $f['conditional'], $m)) {
                    $when = ['field' => $m[1], 'in' => explode(',', $m[2])];
                }

                return [
                    'name'          => $name,
                    'label'         => $f['label'],
                    'type'          => $f['type'],
                    'required'      => $f['required'],
                    'required_when' => $when,
                    'options'       => $f['options'] ?? null,
                    'item_fields'   => isset($f['items']) ? array_keys($f['items']) : null,
                ];
            })->values(),
            'documents'     => config('claim_forms.documents'),
            'notes'         => $def['notes'] ?? null,
        ]]);
    }
}
