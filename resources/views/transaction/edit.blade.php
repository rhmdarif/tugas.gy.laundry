@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Ubah Transaksi</div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if (session('cancel_messages_s'))
                        <div class="alert alert-success">
                            {{ session('cancel_messages_s') }}
                        </div>
                    @endif
                    @if (session('cancel_messages_e'))
                        <div class="alert alert-danger">
                            {{ session('cancel_messages_e') }}
                        </div>
                    @endif

                    @if (session('status_e') AND session('messages_e'))
                        <div class="alert alert-{{ session('status_e') }}">
                            {{ session('messages_e') }}
                        </div>
                    @endif
                    <div id="msg"></div>

                    <form action="{{ route('transaction.update', $order->order_id) }}" method="post">
                        @csrf
                        @method("PATCH")
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="inputPackages">Nama Pelanggan</label>
                                <input type="text" class="form-control" id="inputAddress" value="{{ $order->user_id }}" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputPrice">ID Pesanan</label>
                                <input type="text" class="form-control" id="inputAddress" value="{{ $order->order_id }}" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="inputPackages">Paket</label>
                                <select class="custom-select" id="inputPackages" name="paket" disabled>
                                    <option selected disabled>Pilih Paket</option>
                                    @foreach ($paket as $pkt)
                                        <option value="{{ $pkt->id }}">{{ $pkt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputPrice">Harga /kg</label>
                                <input type="text" class="form-control" id="inputPrice" value="{{ $order->paket->price }}" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="inputPayment">Metode Pembayaran</label>
                                <select class="custom-select" id="inputPayment" disabled>
                                    <option selected disabled>Pilih Paket</option>
                                    <option value="cash">Bayar sekarang</option>
                                    <option value="debt">Bayar belakang</option>
                                </select>
                            </div>
                            
    
                            <div class="form-group col-md-4">
                                <label for="inputPricePay">Status Pembayaran</label>
                                <input type="text" class="form-control" id="inputPricePay" value="
@if ($order->payment_status == 1) 
Lunas
@else
Belum Bayar
@endif" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputWeigth">Berat</label>
                            <input type="number" class="form-control" min="1" id="inputWeigth" value="{{ $order->weigth }}" disabled>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="inputPayment">Status</label>
                            <input type="text" class="form-control" value="{{ strtoupper($order->proses) }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="inputPayment">Sub - Status</label>
                            <select class="custom-select" id="inputStatus" name="status" required>
                                <option disabled>Pilih Status</option>
                                @foreach ($status as $sts)
                                    <option value="{{ $sts }}">{{ strtoupper($sts) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-7">
                                <h6>Harga Bayar</h6>
                            </div>
                            <div class="col-md-1">
                                Rp.
                            </div>
                            <div class="col-md-4">
                                <span style="font-size: 1.6rem" id="harga"> {{ $order->weigth*$order->paket->price }} </span>
                            </div>


                            <div class="col-md-7">
                                <h6>Diskon</h6>
                            </div>
                            <div class="col-md-1">
                                Rp.
                            </div>
                            <div class="col-md-4">
                                <span style="font-size: 1.6rem" id="diskon"> {{ $order->discount }} </span>
                            </div>
                            <hr>

                            <div class="col-md-7 font-weight-bolder">
                                <h5>Total Bayar</h5>
                            </div>
                            <div class="col-md-1">
                                Rp.
                            </div>
                            <div class="col-md-4 font-weight-bolder">
                                <span style="font-size: 2rem" id="totalbayar"> {{ $order->price }} </span>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary btn-block">Perbaharui Status</button>
                        <hr>
                        @if ($order->proses == 'gagal') 
                            <button type="button" class="btn btn-success btn-block" id="uncancelTrx">Cabut Pembatalan</button>
                        @else
                            <button type="button" class="btn btn-danger btn-block" id="cancelTrx">Batalkan Transaksi</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#discount').hide();
    var price, pricepay, disc, totalbayar;
    price = {{ $order->paket->price }};
    pricepay = 0;
    disc = {{ $order->discount }};

    $('#inputPackages').change(function() {
        $.get('/addon/package/'+this.value, function (data) {
            price = data.price;
            $('#harga').text(price);

            pricepay = this.value*price;
            pricepay -= disc;
            $('#totalbayar').text(pricepay);
        });
    });

    $('#inputWeigth').keyup(function() {
        pricepay = this.value*price;
        $('#harga').text(pricepay);

        pricepay -= disc;
        $('#totalbayar').text(pricepay);
    });
    
    $('#cancelTrx').click(function() {
        var confirm = window.confirm('Apakah anda yakin ingin membatalkan transaksi ini ??');

        if(confirm == true) {
            window.location.href="{{ route('transaction.cancel', $order->order_id)}}";
        } else {
            $('#msg').html('<div class="alert alert-danger"> Permintaan dibatalkan! </div>');
        }
    });
    
    $('#uncancelTrx').click(function() {
        var confirm = window.confirm('Apakah anda yakin ingin mencabut pembatalan transaksi ini ??');

        if(confirm == true) {
            window.location.href="{{ route('transaction.uncancel', $order->order_id)}}";
        } else {
            $('#msg').html('<div class="alert alert-danger"> Permintaan dibatalkan! </div>');
        }
    });
    
    selectElement('inputPackages', '{{ $order->package }}');
    selectElement('inputPayment', '{{ $order->payment }}');
    selectElement('inputStatus', '{{ $order->status }}');

    function selectElement(id, valueToSelect) {    
        let element = document.getElementById(id);
        element.value = valueToSelect;
    }
</script>
@endsection
