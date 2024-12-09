<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkplaceNews extends Model
{
    use HasFactory;
    protected $table='workspace_news';
    protected $fillable=[
        'workplace_id', 
        'recruitment_news_id',
        'homeaddress',
    ];
    // protected $hidden=['profile_id','language_id',];
    public function news()
    {
        return $this->belongsTo(RecruitmentNews::class);
    }
    public function workplace()
    {
        return $this->belongsTo(Workplace::class);
    }
}
