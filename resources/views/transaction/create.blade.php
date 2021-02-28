@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Buat Transaksi</div>

                <div class="card-body">
                    <p class="text-center">Formulir Transaksi</p>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('messages') }}
                        </div>
                    @endif
                    <form action="{{ route('transaction.store') }}" method="post">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="inputAddress">Nama Pelanggan</label>
                                <input type="text" class="form-control" id="inputAddress" value="{{ old('nama') }}" name="nama" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputwhatsapp">WhatsApp Pelanggan</label>
                                <input type="tel" class="form-control" id="inputwhatsapp" value="{{ old('whatsapp') }}" name="whatsapp" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="inputPackages">Paket</label>
                                <select class="custom-select" id="inputPackages" name="paket" required>
                                    <option selected disabled>Pilih Paket</option>
                                    @foreach ($paket as $pkt)
                                        <option value="{{ $pkt->id }}">{{ $pkt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputPrice">Harga /kg</label>
                                <input type="text" class="form-control" id="inputPrice" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPayment">Metode Pembayaran</label>
                            <select class="custom-select" id="inputPayment" value="{{ old('payment') }}" name="payment" required>
                                <option selected disabled>Pilih Paket</option>
                                <option value="cash">Bayar sekarang</option>
                                <option value="debt">Bayar belakang</option>
                            </select>
                        </div>
                        <hr>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="inputWeigth">Berat</label>
                                <input type="number" class="form-control" id="inputWeigth" value="{{ old('weight') }}" name="weight" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="inputPricePay">Harga Bayar <small>( Berdasarkan berat )</small></label>
                                <input type="text" class="form-control" id="inputPricePay" readonly>
                            </div>
                        </div>
                        <div id="discount">
                            <hr>
                            <p class="text-center">Voucher Diskon</p>
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label for="inputVoucher">Kode Voucher</label>
                                    <input type="text" class="form-control" id="inputVoucher" value="{{ old('voucher') }}" name="voucher">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="inputDiscount">Jumlah Potongan</label>
                                    <input type="text" class="form-control" id="inputDiscount" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-7">
                                <h5>Total Bayar</h5>
                            </div>
                            <div class="col-md-1">
                                Rp.
                            </div>
                            <div class="col-md-4">
                                <span style="font-size: 2rem" id="totalbayar"> 0 </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Buat Pesanan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#discount').hide();
    var price, pricepay, disc, totalbayar;
    price = 0;
    pricepay = 0;
    disc = 0;
    $('#inputPackages').change(function() {
        $.get('/addon/package/'+this.value, function (data) {
            price = data.price;
            $('#inputPrice').val(data.price);
        });
    });

    $('#inputWeigth').keyup(function() {
        pricepay = this.value*price;
        $('#inputPricePay').val(pricepay);
        $('#totalbayar').text(pricepay);
    });

    $('#inputVoucher').focusout(function() {
        if(price !== 0 || pricepay !== 0) {
            $.get('/addon/voucher/'+this.value, function (data) {

                if(data.message) {
                    alert(data.message);
                } else {
                    if(data.type == 'percent') {
                        disc = pricepay*data.off;
                    } else {
                        disc = data.off;
                    }

                    if(disc > pricepay) {

                        $('#inputDiscount').val(pricepay);
                        totalbayar = pricepay-pricepay;
                        $('#totalbayar').text(totalbayar);

                    } else {

                        $('#inputDiscount').val(disc);
                        totalbayar = pricepay-disc;
                        $('#totalbayar').text(totalbayar);

                    }
                }
            });
        } else {
            alert('Harap lengkapi formulir transaksi terlebih dahulu');
        }
    });


    document.onkeydown = function(e) {
        if (e.keyCode === 113 ) {
            if( $('#discount').css('display') == 'none' ) {
                $('#discount').show();
            } else {
                $('#discount').hide();
            }
        }
    };
</script>
@endsection
