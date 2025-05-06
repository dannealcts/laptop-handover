<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoryAssignment extends Model
{
    use HasFactory;

    // ✅ Specify the correct table name
    protected $table = 'accessories_assignments';

    protected $fillable = [
        'laptop_request_id',
        'accessory_name',
        'quantity',
    ];

    public function laptopRequest()
    {
        return $this->belongsTo(LaptopRequest::class);
    }
}
