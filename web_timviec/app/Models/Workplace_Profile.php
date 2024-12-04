<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workplace_Profile extends Model
{
    use HasFactory;
    protected $table='workspace_profile';
    protected $fillable=[
        'profile_id',
        'workplace_id'
    ];
    // protected $hidden=['profile_id','workplace_id'];
    public function profile()
    {
        return $this->belongsToMany(Profile::class,'profile_id');
    }
    public function workplace()
    {
        return $this->belongsToMany(Workplace::class,'workplace_id');
    }
}
