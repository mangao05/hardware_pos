<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'room_details' => 'array'
    ];

    // public function room()
    // {
    //     return $this->belongsTo(Room::class);
    // }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_reservation')->withTimestamps();
    }
}
