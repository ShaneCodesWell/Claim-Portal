<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'external_customer_id',
        'external_customer_code',
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
    public function resolvedCustomerIds(): array
    {
        $ids = [$this->id];

        if (! $this->phone) {
            return $ids;
        }

        $mySources = $this->sources ?? ['genova'];

        $relatedIds = static::where('phone', $this->phone)
            ->where('id', '!=', $this->id)
            ->where(function ($query) use ($mySources) {
                foreach ($mySources as $source) {
                    $query->where(function ($q) use ($source) {
                        $q->whereJsonDoesntContain('sources', $source)
                            ->orWhereNull('sources');
                    });
                }
            })
            ->pluck('id')
            ->toArray();

        return array_unique(array_merge($ids, $relatedIds));
    }
}
