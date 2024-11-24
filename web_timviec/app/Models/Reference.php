<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    use HasFactory;
    protected $table='reference_information';
    protected $fillable=[
        'name',
        'company_name',
        'phone_number',
        'position',
        'profile_id',
    ];
    protected $hidden=[
        'profile_id',
    ];
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
