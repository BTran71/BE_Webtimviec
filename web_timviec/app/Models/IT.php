<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IT extends Model
{
    use HasFactory;
    protected $table='information_technology';

    protected $fillable = [
        'name',
    ];
    public function information_Details()
    {
        return $this->hasMany(InfoDetails::class);
    }
}
