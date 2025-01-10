<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $table='report_information';
    protected $fillable=[
        'content',
    ];
    public function reportdetails(){
        return $this->hasMany(RecruitmentNews::class);
    }
}
