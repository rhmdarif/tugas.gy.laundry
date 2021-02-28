<?php

namespace App\Http\Controllers;

use App\Voucher;
use Illuminate\Http\Request;
use Validator;

class VouchersController extends Controller
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
        $query = Voucher::get();
        if($query->count()) {
             $r = view('voucher.index', ['voucher' => Voucher::paginate(10)]);
        } else {
            $r = view('voucher.notfound');
        }

        return $r;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash()
    {
        //
        $query = Voucher::onlyTrashed();
        if($query->count()) {
             $r = view('voucher.trash', ['voucher' => $query->paginate(10)]);
        } else {
            $r = view('voucher.notfound');
        }

        return $r;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function restore($voucher)
    {
        //
        $query = Voucher::onlyTrashed()->where('id', $voucher);
        if($query->count()) {
            $query->restore();
             $r = back()->with([  'status2' => true, 'messages' => 'Telah di kembalikan' ]);
        } else {
            $r = view('voucher.notfound');
        }

        return $r;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delpermanen($voucher)
    {
        //
        $query = Voucher::onlyTrashed()->where('id', $voucher);
        if($query->count()) {
            $query->forceDelete();
            $r = redirect('voucher/trash')->with([  'status2' => true, 'messages' => 'Telah di hapus permanen' ]);
        } else {
            $r = view('voucher.notfound');
        }

        return $r;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('voucher.create');
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
            'kode' => 'required|alpha_num|unique:vouchers,id',
            'potongan' => 'required|numeric',
            'target' => 'required|in:0,1',
            'status' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return redirect('voucher/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        if($request->kode == '') {
            $kode = Str::random(13);
        } else {
            $kode = $request->kode;
        }

        if($request->potongan <= 100) {
            if($request->potongan < 1) {
                $cutoff = $request->potongan*100;
            } else {
                $cutoff = $request->potongan;
            }

            $type = "percent";
        } else {
            $type = "cut";
            $cutoff = $request->potongan;
        }

        if($request->target == 0) {
            $target = "Satu Orang";
        } else {
            $target = "Banyak Orang";
        }

        if($request->status == 0) {
            $status = "Aktif";
        } else {
            $status = "Tidak Aktif";
        }

        Voucher::create([
            'id' => $kode,
            'off' => $cutoff,
            'type' => $type,
            'valid_to' => $request->target,
            'status' => $request->status
        ]);

        return redirect('voucher/create')->with(['status' => true, 'messages' => [
                                                                                'kode' => "Kode Voucher : ".$kode,
                                                                                'potongan' => "Potongan Harga : ".$cutoff,
                                                                                'tipe' => "Jenis Voucher : ".strtoupper($type),
                                                                                'target' => "Berlaku Untuk : ".$target,
                                                                                'status' => "Kode Voucher : ".$status,
                                                                              ]
                                                ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function edit(Voucher $voucher)
    {
        //
        return view('voucher.edit', compact('voucher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Voucher $voucher)
    {
        //

        $validator = Validator::make($request->all(), [
            'potongan' => 'required|numeric',
            'target' => 'required|in:0,1',
            'status' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return redirect('voucher/edit')
                        ->withErrors($validator)
                        ->withInput();
        }

        $kode = $voucher->id;

        if($request->potongan <= 100) {
            if($request->potongan < 1) {
                $cutoff = $request->potongan*100;
            } else {
                $cutoff = $request->potongan;
            }

            $type = "percent";
        } else {
            $type = "cut";
            $cutoff = $request->potongan;
        }

        if($request->target == 0) {
            $target = "Satu Orang";
        } else {
            $target = "Banyak Orang";
        }

        if($request->status == 0) {
            $status = "Aktif";
        } else {
            $status = "Tidak Aktif";
        }

        $voucher->update([
            'off' => $cutoff,
            'type' => $type,
            'valid_to' => $request->target,
            'status' => $request->status
        ]);

        return redirect()->route('voucher.edit', $voucher)->with(['status' => true, 'messages' => [
                                                                                'kode' => "Kode Voucher : ".$kode,
                                                                                'potongan' => "Potongan Harga : ".$cutoff,
                                                                                'tipe' => "Jenis Voucher : ".strtoupper($type),
                                                                                'target' => "Berlaku Untuk : ".$target,
                                                                                'status' => "Kode Voucher : ".$status,
                                                                              ]
                                                ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Voucher $voucher)
    {
        //
        $voucher->delete();
        return redirect('voucher')->with([  'status' => true, 'kode' => $voucher->id
                                        ]);
    }
}
