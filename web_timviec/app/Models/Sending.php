<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sending extends Model
{
    use HasFactory;
    protected $table='sending_details';
    protected $fillable=[
        'recruitment_news_id' ,
        'profile_id',
        'senddate',
        'status',
        'name'
    ];
    public function news(){
        return $this->belongsTo(RecruitmentNews::class);
    }
    public function profile(){
        return $this->belongsToMany(Profile::class);
    }
}
