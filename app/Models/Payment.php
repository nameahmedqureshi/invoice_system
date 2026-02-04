<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['invoice_id', 'gateway', 'transaction_id', 'amount', 'status', 'payload'];

    protected $casts = [
        'payload' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
