<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workplace extends Model
{
    use HasFactory;
    protected $table='workplace';
    protected $fillable = [
        'city',
    ];
    public function workplaceDetails(){
         return $this->hasMany(Workplace_Profile::class); 
    }
}
