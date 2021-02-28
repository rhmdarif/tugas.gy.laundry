<?php

namespace App\Http\Controllers;

use App\Order;

use App\Package;
use App\Voucher;
use Illuminate\Http\Request;
use App\Rules\Voucher\CheckCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Http\Helpers\Curl;


class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('transaction.index', ['transaksi' => Order::paginate(10)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('transaction.create', ['paket' => Package::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'paket' => 'required|string',
            'payment' => 'required|in:cash,debt',
            'weight' => 'required|numeric'
        ]);


        if ($validator->fails()) {
            return redirect('transaction/create')
                        ->withErrors($validator)
                        ->withInput();
        } else if($request->voucher != '' AND Voucher::where('id', $request->voucher)->where('status', 0)->count() == 0) {
            $validator->errors()->add('voucher', 'Voucher diskon tidak tersedia');
        }

        if($validator->errors()->count()) {
            return redirect('transaction/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $package = Package::find($request->paket);
        $package_price = $package->price;
        $package_name = $package->name;

        $price = $request->weight*$package_price;
        $disc = 0;

        if($request->voucher != '') {
            $voucher = Voucher::find($request->voucher);

            if($voucher->type =='percent') {
                $disc = $price*$voucher->off;
            } else {
                $disc = $voucher->off;
            }

            if($voucher->valid_to == 0) {
                $voucher->status = 1;
                $voucher->save();
            }
        }

        if($disc > $price) {
            $disc = $price;
            $totalbayar = 0;
        } else {
            $totalbayar = $price-$disc;
        }


        if($request->payment == 'cash') {
            $s_pay = 1;
        } else {
            $s_pay = 0;
        }
        $order_id =  date('ym').rand('100', '999');
        Order::create([
            'order_id' => $order_id,
            'user_id' => $request->nama,
            'user_whatsapp' => $request->whatsapp,
            'weigth' => $request->weight,
            'package' => $request->paket,
            'status' => 'pending',
            'proses' => 'pending',
            'discount' => $disc,
            'price' => $totalbayar,
            'payment' => $request->payment,
            'payment_status' =>  $s_pay,
            'staff_in' => Auth::user()->office_id,
            'staff_out' => ''
        ]);

        Curl::post('http://139.180.209.196:8124/api/message/send', [
            "device" => "082285768836",
            "to" => $this->formatNomor($request->whatsapp),
            "msg" => "Hai ".$request->nama.",\n cucian anda telah kami terima dengan Id : *".$order_id."*\n\nPantau progres pengerjaan cucian anda melalu link berikut. \nhttp://localhost:8080/search/transactions",
        ], ['Authorization: Bearer ABCDE']);


        return redirect('transaction/create')->with('status', 1)->with('messages', "Transaksi telah disimpan!");
    }


	private function convert_to_62($nomor) {
		$nomor = preg_replace('/[^0-9]/', '', $nomor);

		if(substr(trim($nomor), 0, 2)=='62'){
			$hp = trim($nomor);
		} else if(substr(trim($nomor), 0, 1)=='0'){
			$hp = '62'.substr(trim($nomor), 1);
		}

		return $hp;
	}

	private function formatNomor($nomor) {
		if(preg_match('/-/', $nomor)) { // ini GRUP
			$nomor = str_replace('@g.us', '', $nomor);
			$pecah_nomor = explode('-', $nomor);
			if(count($pecah_nomor) == 2) {
				$pecah_nomor[0] = $this->convert_to_62($pecah_nomor[0]);
			}

			return implode('-', $pecah_nomor)."@g.us";
		} else { // Ini Personal Chat
			return $this->convert_to_62($nomor)."@c.us";
		}
	}

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($transaction)
    {
        //
        $query = Order::where('proses', $transaction);
        if($query->count()) {
             $r = view('transaction.show', ['transaksi' => $query->paginate(10), 'title' => $transaction]);
        } else {
            $r = view('transaction.notfound');
        }

        return $r;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit($order)
    {
        //
        $query = Order::where('order_id', $order);
        if($query->count()) {
             $datas = $query->first();
             $r = view('transaction.edit', ['order' => $datas, 'paket' => Package::all(), 'status' => ['pending', 'waiting', 'washed', 'wet', 'dried', 'rubit', 'packing', 'finish']]);
        } else {
            $r = response('asda');
        }
        return $r;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order)
    {
        //
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,waiting,washed,wet,dried,rubit,packing,finish'
        ]);


        if ($validator->fails()) {
            return back()->withErrors($validator)
                         ->withInput();
        }

        $get = Order::where('order_id', $order);

        if($get->first()->proses != 'gagal') {
            if(in_array($request->status, array('pending','waiting'))) {
                $status_proses = "pending";
            } else if(in_array($request->status, array('washed','wet','dried','rubit','packing'))) {
                $status_proses = "proses";
            } else {
                $status_proses = "selesai";
            }

            $get->update([
                'status' => $request->status,
                'proses' => $status_proses
            ]);
            //
            return back()->with(['messages_e' => "Berhasil merubah status", 'status_e' => 'success']);
        } else {
            return back()->with(['messages_e' => "Tidak dapat merubah transaksi yang digagalkan", 'status_e' => 'danger']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    public function cancel($order) {
        //
        $query = Order::where('order_id', $order);
        if($query->count()) {
            $query->update([
                'proses' => 'gagal'
            ]);

            $r = back()->with([
                'cancel_messages_s' => "Transaksi telah dibatalkan"
            ]);
        } else {
            $r = back()->with([
                'cancel_messages_e' => "Transaksi tidak dibatalkan"
            ]);
        }

        return $r;
    }

    public function uncancel($order) {
        //
        $query = Order::where('order_id', $order);
        if($query->count()) {
            $status = $query->first()->status;

            if(in_array($status, array('pending', 'waiting'))) {
                $sts = 'pending';
            } else if($status == 'finish') {
                $sts = 'selesai';
            } else {
                $sts = 'proses';
            }

            $query->update([
                'proses' => $sts
            ]);

            $r = back()->with([
                'cancel_messages_s' => "Pembatalan Transaksi telah dicabut"
            ]);
        } else {
            $r = back()->with([
                'cancel_messages_e' => "Pembatalan Transaksi tidak dicabut"
            ]);
        }

        return $r;
    }
}
