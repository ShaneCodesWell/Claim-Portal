<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimDraftDocument extends Model
{
    /** @use HasFactory<\Database\Factories\ClaimDraftDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'claim_draft_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'type'
    ];

    public function draft(): BelongsTo
    {
        return $this->belongsTo(ClaimDraft::class, 'claim_draft_id');
    }
}
