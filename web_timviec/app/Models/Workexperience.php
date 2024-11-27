<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workexperience extends Model
{
    use HasFactory;
    protected $table='work_experience';
    protected $fillable=[
        'company_name',
        'job_position',
        'start_time',
        'end_time',
        'description',
        'profile_id',
    ];
    protected $hidden=[
        'profile_id',
    ];
    public function profile()
    {
        return $this->belongsTo(Profile::class,'profile_id',);
    }
}
