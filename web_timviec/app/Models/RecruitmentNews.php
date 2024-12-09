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
        'quantity',// số lượng
        'workingmodel',
        'qualifications',//bằng cấp
        'requirements',
        'employer_id',
        'industry_id',
        'package_id',
        'invoice_id',
    ];
    public function employer(){
        return $this->belongsTo(Employer::class);
    }
    public function industry(){
        return $this->belongsTo(Industry::class);
    }
    public function jobposting(){
        return $this->belongsTo(JobPosting::class);
    }
    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }
    public function workplacenews(){
        return $this->hasMany(WorkplaceNews::class);
    }
    protected $hidden=[
        'employer_id',
        'industry_id',
    ];
}
