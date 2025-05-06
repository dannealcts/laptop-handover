<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'laptop_id',
        'reason',
        'status',
        'laptop_request_id',
        'received_at', // include this if you're saving the date manually
    ];

    protected $casts = [
        'received_at' => 'datetime', // This allows ->format() to work properly
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function laptop()
    {
        return $this->belongsTo(\App\Models\Laptop::class, 'laptop_id');
    }

    /**
     * Relationship to LaptopRequest (via laptop_request_id)
     */
    public function request()
    {
        return $this->belongsTo(LaptopRequest::class);
    }
}
