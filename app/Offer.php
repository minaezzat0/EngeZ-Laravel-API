<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    //
    protected $fillable=['content','job_id','user_id','record_deleted','offer_amount','status'];

   public function job()
   {
       return $this->belongsTo('App\Job');
   }
}
