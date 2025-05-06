<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class HandoverHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'laptop_request_id',
        'laptop_id',
        'handover_date',
    ];

    public function request()
{
    return $this->belongsTo(LaptopRequest::class, 'laptop_request_id');
}

    public function laptop()
    {
        return $this->belongsTo(Laptop::class);
    }
}


