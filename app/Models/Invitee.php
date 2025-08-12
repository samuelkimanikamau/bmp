<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invitee extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'email', 'phone', 'password', 'status'];

    public function ticket()
{
    return $this->hasOne(\App\Models\Ticket::class);
}

public function mpesaLogs()
{
    return $this->hasMany(MpesaLog::class);
}

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}