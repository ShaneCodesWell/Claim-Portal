<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Policy;
use Illuminate\Support\Facades\Log;
use App\Services\GenovaApiService;
use App\Services\GlimsApiService;
use App\Support\NameMatcher;
use App\Support\Sandbox;

class CustomerIdentityService
{
    public function __construct(
        private GenovaApiService $genova,
        private GlimsApiService $glims,
    ) {}

    /**
     * @return array{phone: string, profiles: array<int, array>}|null
     */
    public function lookup(string $identifier, string $loginType = 'mobile_no'): ?array
    {
        $identifier = preg_replace('/[\x{00A0}\x{FEFF}]+/u', '', trim($identifier));

        if ($loginType === 'mobile_no') {
            $identifier = $this->normalizePhone($identifier);
        }

        // Sandbox: only the seeded ECLTEST customers, never Genova or GLIMS
        if (Sandbox::active()) {
            $customer = $loginType === 'mobile_no'
                ? Customer::where('phone', $identifier)->where('genova_customer_code', 'like', 'ECLTEST%')->first()
                : null;

            return $customer
                ? $this->result($customer->phone, [[
                    'code'   => $customer->genova_customer_code,
                    'name'   => $customer->name,
                    'phone'  => $customer->phone,
                    'source' => 'genova',
                ]])
                : null;
        }

        // 1. Genova API
        $verified = $this->verifyWithGenova($identifier, $loginType);

        if ($verified) {
            $phone = $verified['phone'] ?? ($loginType === 'mobile_no' ? $identifier : null);

            return $phone
                ? $this->result($phone, $this->resolveProfiles($phone, $verified['user_id']))
                : null;
        }

        // 2. Local Genova copy (API down or no match)
        $local = $this->resolveGenovaLocalProfiles($identifier, $loginType);

        if ($local) {
            return $this->result($local[0]['phone'], $local);
        }

        // 3. GLIMS (phone only)
        if ($loginType === 'mobile_no') {
            $glims = $this->resolveGlimsProfiles($identifier);

            if ($glims) {
                return $this->result($glims[0]['phone'] ?? $identifier, $glims);
            }
        }

        return null;
    }

    /** Find or create the local Customer for a chosen profile (same rules as the web login). */
    public function resolveCustomer(string $phone, array $profile): ?Customer
    {
        $code      = $profile['code'] ?? null;
        $source    = $this->normaliseSource($profile['source'] ?? 'genova');
        $secondary = $profile['secondary_code'] ?? null;

        if (! $code) {
            return null;
        }

        $secondarySource = $secondary
            ? $this->normaliseSource($profile['secondary_source'] ?: ($source === 'glims' ? 'genova' : 'glims'))
            : null;

        $primaryColumn   = $this->sourceColumn($source);
        $secondaryColumn = $secondarySource ? $this->sourceColumn($secondarySource) : null;
        $name            = $profile['name'] ?? null;

        $customer = Customer::where($primaryColumn, $code)->first();

        if (! $customer && $secondary && $secondaryColumn) {
            $customer = Customer::where($secondaryColumn, $secondary)->first();
        }

        if ($customer) {
            $updates = [];

            if (empty($customer->phone)) {
                $updates['phone'] = $phone;
            }
            if (empty($customer->name) || $customer->name === 'Unknown') {
                $updates['name'] = $name ?? $customer->name;
            }
            if (empty($customer->{$primaryColumn})) {
                $updates[$primaryColumn] = $code;
            } elseif ((string) $customer->{$primaryColumn} !== (string) $code) {
                Log::warning('API resolveCustomer: customer already has a different primary code', [
                    'customer_id' => $customer->id,
                    'column' => $primaryColumn,
                ]);
            }
            if ($secondary && $secondaryColumn && empty($customer->{$secondaryColumn})) {
                $updates[$secondaryColumn] = $secondary;
            }

            $current = $customer->sources ?? [];
            $sources = $current;
            foreach (array_filter([$source, $secondarySource]) as $s) {
                if (! in_array($s, $sources, true)) {
                    $sources[] = $s;
                }
            }
            if ($sources !== $current) {
                $updates['sources'] = $sources;
            }

            if ($updates) {
                $customer->update($updates);
            }

            return $customer->fresh();
        }

        $attributes = [
            $primaryColumn => $code,
            'name'         => $name ?? 'Unknown',
            'phone'        => $phone,
            'email'        => null,
            'sources'      => array_values(array_filter([$source, $secondarySource])),
        ];

        if ($secondary && $secondaryColumn) {
            $attributes[$secondaryColumn] = $secondary;
        }

        return Customer::create($attributes);
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '233') && strlen($digits) === 12) {
            return '0' . substr($digits, 3);
        }
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return $digits;
        }
        if (strlen($digits) === 9) {
            return '0' . $digits;
        }

        return $digits;
    }

    public function maskPhone(string $phone): string
    {
        return strlen($phone) >= 7 ? substr($phone, 0, 3) . '***' . substr($phone, -4) : '***';
    }

    // ── internals (ported from AuthController) ────────────────────────────

    private function result(string $phone, array $profiles): ?array
    {
        $profiles = collect($profiles)
            ->map(fn($p) => [
                'code'             => (string) ($p['code'] ?? ''),
                'name'             => $p['name'] ?? 'Unknown',
                'source'           => $this->normaliseSource($p['source'] ?? 'genova'),
                'secondary_code'   => $p['secondary_code'] ?? null,
                'secondary_source' => $p['secondary_source'] ?? null,
            ])
            ->filter(fn($p) => $p['code'] !== '')
            ->values()
            ->all();

        return $profiles ? ['phone' => $phone, 'profiles' => $profiles] : null;
    }

    private function verifyWithGenova(string $identifier, string $loginType): ?array
    {
        try {
            $response = $this->genova->customerVerification($identifier, $loginType);

            if ($response->failed()) {
                return null;
            }

            $first = ($response->json('data.content') ?? [])[0] ?? null;

            return $first ? [
                'user_id' => $first['customer_api_identity'] ?? null,
                'phone'   => $first['phone_number'] ?? null,
            ] : null;
        } catch (\Throwable $e) {
            Log::error('API verifyWithGenova: ' . $e->getMessage());
            return null;
        }
    }

    private function resolveProfiles(string $phone, int|string|null $userId): array
    {
        $profiles = [];

        foreach ($this->resolveGenovaProfiles($phone, $userId) as $profile) {
            $profile['sources'] = [$profile['source']];
            $profiles[]         = $profile;
        }

        foreach ($this->resolveGlimsProfiles($phone) as $gp) {
            $gpCode = (string) ($gp['code'] ?? '');

            $alreadyKnown = collect($profiles)->contains(
                fn($p) => (string) ($p['code'] ?? '') === $gpCode
                    || (string) ($p['secondary_code'] ?? '') === $gpCode
            );

            if ($gpCode === '' || $alreadyKnown) {
                continue;
            }

            $gpSystem = $this->normaliseSource($gp['source'] ?? '');
            $same     = [];

            foreach ($profiles as $index => $existing) {
                if (NameMatcher::matches($existing['name'] ?? '', $gp['name'] ?? '')) {
                    $same[] = $index;
                }
            }

            if (count($same) === 1) {
                $i           = $same[0];
                $existing    = $profiles[$i];
                $otherSystem = $this->normaliseSource($existing['source'] ?? '') !== $gpSystem;

                if ($otherSystem && empty($existing['secondary_code'])) {
                    $profiles[$i]['secondary_code']   = $gp['code'];
                    $profiles[$i]['secondary_source'] = $gp['source'];
                    $profiles[$i]['sources'][]        = $gp['source'];
                }

                continue;
            }

            $gp['sources'] = [$gp['source']];
            $profiles[]    = $gp;
        }

        return array_values($profiles);
    }

    private function resolveGenovaProfiles(string $phone, int|string|null $userId): array
    {
        $profiles = [];

        try {
            $response = $this->genova->getPolicies($phone, 'phone_number');

            if ($response->successful()) {
                foreach ($response->json('data.content') ?? [] as $entry) {
                    if (empty($entry['code'])) {
                        continue;
                    }

                    $profiles[] = [
                        'code'   => $entry['code'],
                        'name'   => $entry['name'] ?? 'Unknown',
                        'phone'  => $entry['phone_number'] ?? $phone,
                        'source' => 'genova',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('API resolveGenovaProfiles failed', ['error' => $e->getMessage()]);
        }

        return $profiles;
    }

    private function resolveGlimsProfiles(string $phone): array
    {
        try {
            $profiles = $this->glims->resolveProfilesByPhone($phone);

            if (! empty($profiles)) {
                return $profiles;
            }
        } catch (\Throwable $e) {
            Log::warning('API resolveGlimsProfiles: API lookup failed', ['error' => $e->getMessage()]);
        }

        $local = Customer::where('phone', $phone)
            ->whereJsonContains('sources', 'glims')
            ->whereNotNull('glims_customer_code')
            ->first();

        return $local ? [[
            'code'   => $local->glims_customer_code,
            'name'   => $local->name,
            'phone'  => $local->phone,
            'source' => 'glims_local',
        ]] : [];
    }

    private function resolveGenovaLocalProfiles(string $identifier, string $loginType): array
    {
        try {
            $customer = null;

            switch ($loginType) {
                case 'mobile_no':
                    $customer = Customer::whereJsonContains('sources', 'genova')
                        ->where('phone', $identifier)->first();
                    break;

                case 'policy_number':
                    $customer = Policy::where('source', 'genova')
                        ->where('policy_number', $identifier)->with('customer')->first()?->customer;
                    break;

                case 'vehicle_number':
                    $customer = Policy::where('source', 'genova')
                        ->whereRaw(
                            "EXISTS (SELECT 1 FROM jsonb_array_elements(raw_payload::jsonb) elem WHERE elem->>'vehicle_number' = ?)",
                            [$identifier]
                        )->with('customer')->first()?->customer;
                    break;
            }

            if (! $customer || empty($customer->genova_customer_code)) {
                return [];
            }

            return [[
                'code'   => $customer->genova_customer_code,
                'name'   => $customer->name,
                'phone'  => $customer->phone,
                'source' => 'genova_local',
            ]];
        } catch (\Throwable $e) {
            Log::error('API resolveGenovaLocalProfiles error: ' . $e->getMessage());
            return [];
        }
    }

    private function normaliseSource(?string $source): string
    {
        return str_starts_with((string) $source, 'glims') ? 'glims' : 'genova';
    }

    private function sourceColumn(?string $source): string
    {
        return $this->normaliseSource($source) === 'glims' ? 'glims_customer_code' : 'genova_customer_code';
    }
}
