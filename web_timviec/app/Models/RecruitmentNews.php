<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitmentNews extends Model
{
    use HasFactory;
    protected $table='recruitment_news';
    protected $fillable=[
        'title',
        'describe',
        'posteddate',
        'benefit',
        'salary',
        'deadline',
        'status',
        'experience',
        'skills',
        'quantity',
        'workingmodel',
        'qualifications',
        'requirements',
        'employer_id',
        'industry_id',
    ];
    public function employer(){
        return $this->belongsTo(Employer::class);
    }
    public function industry(){
        return $this->belongsTo(Industry::class);
    }
    
}
