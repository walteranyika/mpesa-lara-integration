<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable= ['phone', 'status' ,'request_id','user_id', 'amount','merchant_reference'];
}
