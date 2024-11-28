<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $table='profile';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
    public function work_ex()
    {
        return $this->hasMany(Workexperience::class);
    }
    public function reference()
    {
        return $this->hasMany(Reference::class);
    }
    public function academy()
    {
        return $this->hasMany(Academy::class);
    }
    public function languageDetails()
    {
        return $this->hasMany(LanguageDetails::class);
    }
    public function information_Details()
    {
        return $this->hasMany(InfoDetails::class);
    }
    public function workplaceDetails()
    {
        return $this->hasMany(Workplace_Profile::class);
    }
    public function industries()
    {
        return $this->hasMany(Industry_Profile::class);
    }
    protected $fillable=[
        'fullname',
        'email',
        'image',
        'phone_number',
        'gender',
        'skills',
        'day_ofbirth',
        'salary',
        'experience',
        'address',
        'candidate_id',
        'isLock',
    ];
    protected $hidden=[
        'candidate_id',
    ];
    
}
