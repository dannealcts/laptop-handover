<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_tag',
        'brand',
        'model',
        'serial_number',
        'specs',
        'status',
    
    ];

    public function requests()
{
    return $this->hasMany(\App\Models\LaptopRequest::class);
}

}
