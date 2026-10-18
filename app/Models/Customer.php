<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Support\NameMatcher;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'external_customer_id',
        'genova_customer_code',
        'glims_customer_code',
        'name',
        'email',
        'phone',
        'password',
        'last_synced_at',
        'local_password',
        'local_password_set_at',
        'sources',
        'raw_payload',
    ];

    protected $casts = [
        'sources'               => 'array',
        'raw_payload'           => 'array',
        'last_synced_at'        => 'datetime',
        'local_password_set_at' => 'datetime',
    ];

    public function policies()
    {
        return $this->hasMany(Policy::class);
    }

    /**
     * Returns all local Customer IDs that belong to the same real-world person.
     * Same phone + different source system = same person, different record.
     */
    // Latest Addition: records that share a phone number were combined on the dashboard
    // without checking names. Now a record is only combined when the names match too, so a
    // business or a family member on the same phone is no longer mixed in.
    // =====================================================================================
    public function resolvedCustomerIds(): array
    {
        $ids = [$this->id];

        if (! $this->phone) {
            return $ids;
        }

        $mySources = $this->sources ?? ['genova'];

        // Records with the same phone from a different system...
        $candidates = static::where('phone', $this->phone)
            ->where('id', '!=', $this->id)
            ->where(function ($query) use ($mySources) {
                foreach ($mySources as $source) {
                    $query->where(function ($q) use ($source) {
                        $q->whereJsonDoesntContain('sources', $source)
                            ->orWhereNull('sources');
                    });
                }
            })
            ->get(['id', 'name']);

        // ...but only the ones that are really the same person
        $relatedIds = $candidates
            ->filter(fn($candidate) => NameMatcher::matches($this->name, $candidate->name))
            ->pluck('id')
            ->toArray();

        return array_unique(array_merge($ids, $relatedIds));
    }
}
