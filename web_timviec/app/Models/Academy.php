<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Academy extends Model
{
    use HasFactory;
    protected $table='academy_information';
    protected $fillable=[
        'schoolname',
        'major',
        'degree',
        'start_time',
        'end_time',
        'profile_id',
    ];
    protected $hidden=['profile_id',];
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
