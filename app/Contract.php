<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    //
    protected $fillable=['job_id','title','desc','price','user_id','freelancer_id'];
    public function job()
    {
        return $this->belongsTo('App\Job');
    }
    public function user()
    {
        return $this->belongsTo('App\User');

    }
    public function freelancer()
    {
        return $this->belongsTo('App\User')->where('role','freelancer');

    }
    
}

