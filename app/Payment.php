<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable= ['phone', 'status' ,'transaction_id','mpesa_code', 'amount'];
}
