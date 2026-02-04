<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['user_id', 'amount', 'currency', 'status', 'due_date', 'description'];

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getIsExpiredAttribute()
    {
        return $this->status === 'pending'
            && $this->due_date
            && \Carbon\Carbon::parse($this->due_date)->isPast()
            && !\Carbon\Carbon::parse($this->due_date)->isToday();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
