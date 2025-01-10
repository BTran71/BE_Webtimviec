<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoNews extends Model
{
    use HasFactory;
    protected $table='informationnews';
    protected $fillable=[
        'it_id', 
        'recruitment_news_id',
        'score',
    ];
    // protected $hidden=['profile_id','language_id',];
    public function news()
    {
        return $this->belongsTo(RecruitmentNews::class);
    }
    public function info()
    {
        return $this->belongsTo(IT::class);
    }
}
