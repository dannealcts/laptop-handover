<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Laptop;
use App\Models\HandoverHistory;

class LaptopRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'replacement_part',
        'upgrade_type',
        'justification',
        'other_replacement',
        'other_justification',
        'status',
        'laptop_id',
        'assigned_laptop_id',
        'target_laptop_id',
        'completed_at',
        'assigned_part',
        'assigned_quantity',
        'signed_form',
    ];

    /**
     * The user who made the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The laptop assigned to the request (if any).
     */
    public function laptop()
    {
        return $this->belongsTo(Laptop::class);
    }

    /**
     * The handover record for this request.
     */
    public function handover()
    {
        return $this->hasOne(HandoverHistory::class);
    }

    // Assign Laptop ID to Laptop Request
    public function assignedLaptop()
    {
        return $this->belongsTo(Laptop::class, 'assigned_laptop_id');
    }
    
    public function targetLaptop()
    {
        return $this->belongsTo(Laptop::class, 'target_laptop_id');
    }

    // Accessories Assignment
    public function accessories()
    {
        return $this->hasMany(AccessoryAssignment::class);
    }

}