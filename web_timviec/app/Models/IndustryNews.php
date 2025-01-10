<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryNews extends Model
{
    use HasFactory;
    protected $table='industrynews';
    protected $fillable=[
        'industry_id', 
        'recruitment_news_id',
        'score',
        'experience',
    ];
    // protected $hidden=['profile_id','language_id',];
    public function news()
    {
        return $this->belongsTo(RecruitmentNews::class);
    }
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
