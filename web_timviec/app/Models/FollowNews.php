<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowNews extends Model
{
    use HasFactory;
    protected $table = 'follownews';

    public function news()
    {
        return $this->belongsTo(RecruitmentNews::class);
    }
    
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
    protected $fillable=[
        'recruitment_news_id',
        'candidate_id',
        'status'
    ];
}
