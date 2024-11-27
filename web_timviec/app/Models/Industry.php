<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;
    protected $table='industry';

    protected $fillable = [
        'industry_name',
    ];
    public function industryDetails()
    {
        return $this->hasMany(Industry_Profile::class);
    }
}
