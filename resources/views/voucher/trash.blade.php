@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Voucher Diskon</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            Voucher {{ session('messages')->kode }} telah dihapus. Ingin dibatalkan?? <a  href="#"
                            onclick="event.preventDefault();document.getElementById('restore-{{ session('messages')->kode }}').submit();">Hapus</a>
                        
                            <form id="restore-{{ session('messages')->kode }}" action="{{ route('voucher.restore', session('messages')->kode) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE');
                            </form>

                        </div>
                    @endif

                    <form action="{{ route('transaction.store') }}" method="post">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="transaction" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="button-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Cari</button>
                            </div>
                        </div>
                    </form>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                            <th scope="col">Kode Voucher</th>
                            <th scope="col">Potongan</th>
                            <th scope="col">Jenis Potongan</th>
                            <th scope="col">Target</th>
                            <th scope="col">Status Voucher</th>
                            <th scope="col">Tgl. Update</th>
                            <th scope="col">Tgl. Transaksi</th>
                            <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($voucher as $voc)
                                <tr>
                                    <th scope="row"><span id="voc-{{ $voc->id }}"></span><span class="badge badge-primary" onclick="show('{{ $voc->id }}')" id="btn-{{ $voc->id }}">mau lihat??</span></th>
                                    <td>{{ $voc->off }}</td>
                                    <td>{{ $voc->type }}</td>
                                    <td>{{ $voc->valid_to }}</td>
                                    <td>
                                        @if ($voc->status == 0)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>{{ $voc->updated_at }}</td>
                                    <td>{{ $voc->created_at }}</td>
                                    <td>
                                        <a href="{{ route('voucher.restore', ['voucher'=> $voc->id]) }}">Restore</a>  | 
                                    
                                        <a href="{{ route('voucher.delpermanen', ['voucher'=> $voc->id]) }}">Hapus Permanen</a> 

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="float-md-right">
                        {{ $voucher->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function show(id) {
        if($('#voc-'+id).css('display') == 'none' ) {
            $('#btn-'+id).text('tutup');
            $('#voc-'+id).text(id);
            $('#voc-'+id).show();
        } else {
            $('#btn-'+id).text('buka');
            $('#voc-'+id).hide();
        }
    }
</script>
@endsection
