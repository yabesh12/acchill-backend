<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingServiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'booking_service_items';

    protected $fillable = [
        'booking_id',
        'service_id',
        'service_name',
        'unit_price',
        'quantity',
        'total_price',
        'discount',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'service_id' => 'integer',
        'unit_price' => 'double',
        'quantity' => 'integer',
        'total_price' => 'double',
        'discount' => 'double',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id')->withTrashed();
    }
}
