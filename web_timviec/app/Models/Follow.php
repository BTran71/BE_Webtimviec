<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    protected $table = 'follow';

    public function employer()
    {
        return $this->belongsTo(employer::class);
    }
    
    public function candidate()
    {
        return $this->belongsTo(candidate::class);
    }
}
