<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoDetails extends Model
{
    use HasFactory;
    protected $table='technology_details';
    protected $fillable=[
        'profile_id',
        'it_id',
        'score'
    ];
    // protected $hidden=['profile_id','it_id'];
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
    public function technology()
    {
        return $this->belongsTo(IT::class);
    }
}
