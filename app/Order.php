<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = ['order_id', 'user_id', 'user_whatsapp', 'weigth', 'package', 'status', 'proses', 'discount', 'price', 'payment', 'payment_status', 'staff_in', 'staff_out'];

    public function paket() {
        return $this->hasOne('App\Package', 'id', 'package');
    }
}
