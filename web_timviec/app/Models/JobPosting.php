<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;
    protected $table='job_posting_packages';
    protected $fillable=[
        'name',
        'type',
        'price',
        'describe',
    ];
    public function news()
    {
        return $this->hasMany(RecruitmentNews::class);
    }
}
