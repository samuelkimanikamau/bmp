<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaLog extends Model
{
    protected $fillable = [
        'invitee_id','merchant_id','checkout_id','phone_number',
        'status','message','transaction_id','amount',
    ];

    public function invitee() {
        return $this->belongsTo(Invitee::class);
    }
}