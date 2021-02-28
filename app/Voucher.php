<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
	use SoftDeletes;
    //
    protected $keyType = 'string';
    protected $fillable = ['id', 'off', 'type', 'valid_to', 'status'];
}
