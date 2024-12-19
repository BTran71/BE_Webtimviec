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
        'rank',//cấp độ
        'employer_id',
        'package_id',
        'invoice_id',
    ];
    public function employer(){
        return $this->belongsTo(Employer::class);
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
    public function industry(){
        return $this->hasMany(IndustryNews::class);
    }
    public function language(){
        return $this->hasMany(LanguageNews::class);
    }
    public function information(){
        return $this->hasMany(InfoNews::class);
    }
    public function send(){
        return $this->belongsToMany(Profile::class, 'sending_details', 'recruitment_news_id', 'profile_id')
                    ->withPivot('senddate', 'status'); // Lấy thêm các cột trong bảng trung gian;
    }
    protected $hidden=[
        'employer_id',
        'industry_id',
    ];
}
