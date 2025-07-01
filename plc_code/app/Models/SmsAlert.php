<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsAlert extends Model
{
    protected $fillable = [
        'recipient',
        'text',
        'sent_at',
        'gateway_response',
        'subject',
    ];
}
