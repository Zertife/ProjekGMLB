<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'returned_at',
        'condition_on_return',
        'notes',
    ];

    protected $casts = [
        'returned_at' => 'date',
    ];

    /**
     * Log pengembalian milik satu peminjaman.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
