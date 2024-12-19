<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageNews extends Model
{
    use HasFactory;
    protected $table='languagenews';
    protected $fillable=[
        'language_id', 
        'recruitment_news_id',
    ];
    // protected $hidden=['profile_id','language_id',];
    public function news()
    {
        return $this->belongsTo(RecruitmentNews::class);
    }
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
