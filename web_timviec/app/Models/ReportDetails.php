<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDetails extends Model
{
    use HasFactory;
    protected $table='report_details';
    protected $fillable=[
        'report_id',
        'sending_details_id',
    ];
    public function report(){
        return $this->belongsTo(Report::class);
    }
    public function send(){
        return $this->belongto(Sending::class);
    }
}
