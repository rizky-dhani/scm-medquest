<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerialNumber extends Model
{
    protected $guarded = ['id'];
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
