<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public const STATUS_ACTIVE = 'Active';
    public const STATUS_USED   = 'Used';

    protected $fillable = ['invitee_id', 'number', 'issued_at', 'status', 'meta'];

    protected $casts = [
        'issued_at' => 'datetime',
        'meta'      => 'array',
    ];

    public function invitee()
    {
        return $this->belongsTo(Invitee::class);
    }

    // Helper to mark as used
    public function markUsed(): bool
    {
        if ($this->status === self::STATUS_USED) {
            return true;
        }
        return $this->update(['status' => self::STATUS_USED]);
    }

    // Scopes
    public function scopeActive($q) { return $q->where('status', self::STATUS_ACTIVE); }
    public function scopeUsed($q)   { return $q->where('status', self::STATUS_USED); }
}
