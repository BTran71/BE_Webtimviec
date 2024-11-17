<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workexperience extends Model
{
    use HasFactory;
    protected $table='work_experience';
    public function Profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
