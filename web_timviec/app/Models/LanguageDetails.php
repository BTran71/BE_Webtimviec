<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageDetails extends Model
{
    use HasFactory;
    protected $table='language_details';
    protected $fillable=[
        'profile_id',
        'language_id',
        'level',
        'score'
    ];
    // protected $hidden=['profile_id','language_id',];
    public function profile()
    {
        return $this->belongsToMany(Profile::class);
    }
    public function language()
    {
        return $this->belongsToMany(Language::class);
    }
}
