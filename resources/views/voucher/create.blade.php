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
                        <div class="alert alert-success text-center">
                            <h5> Berhasil membuat Voucher </h5>
                            @foreach (session('messages') as $msg)
                                <span>{{ $msg }}</span><br>
                            @endforeach
                        </div>
                    @endif
                    <form action="{{ route('voucher.store') }}" method="post">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="inputKode">Kode Voucher</label>
                                <input type="tel" class="form-control" id="inputKode" name="kode" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputPotongan">Potongan</label>
                                <input type="tel" class="form-control" id="inputPotongan" name="potongan" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputJenis">Jenis Potongan</label>
                                <input type="text" class="form-control" id="inputJenis" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputTarget">Pengguna</label>
                                <select class="custom-select" id="inputTarget" name="target" required>
                                    <option selected disabled>Pilih Paket</option>
                                    <option value="0">One Time / Sekali Pakai</option>
                                    <option value="1">Hingga Status dirubah</option>
                                </select>
                            </div>
    
                            <div class="form-group col-md-6">
                                <label for="InputStatus">Status</small></label>
                                <select class="custom-select" id="InputStatus" name="status" required>
                                    <option disabled>Pilih Paket</option>
                                    <option value="0" selected>Aktif</option>
                                    <option value="1">Tidak aktif</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Buat Voucher</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#inputPotongan').keyup(function() {
        if(this.value <= 100) {
            var cutoff = this.value;

            if(cutoff < 1) {
                cutoff = cutoff*100;
            }

            $('#inputJenis').val('Potongan sebesar '+cutoff+'% dari total biaya');
        } else {
            $('#inputJenis').val('Potongan sebesar Rp. '+this.value);
        }
    });
</script>
@endsection
