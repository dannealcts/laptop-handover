<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'purchase_date',
        'upgrade_notification_status',
    
    ];

    public function requests()
    {
        return $this->hasMany(\App\Models\LaptopRequest::class);
    }
    
    public function isEligibleForUpgrade(): bool
    {
        if (!$this->purchase_date) {
            return false; // No purchase date means no upgrade eligibility
        }

        return Carbon::parse($this->purchase_date)->addYears(5)->isPast();
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

}
