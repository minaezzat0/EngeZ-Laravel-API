<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    //
    protected $fillable=['email','subject','message'];

    public function user(){
        return $this->belongsTo('App\User');
    }
}
