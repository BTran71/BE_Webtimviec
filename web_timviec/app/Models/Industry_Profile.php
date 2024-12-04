<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry_Profile extends Model
{
    use HasFactory;
    protected $table='industry_profile';
    protected $fillable=[
        'profile_id',
        'industry_id'
    ];
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
