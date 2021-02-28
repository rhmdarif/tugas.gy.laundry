@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Transaksi</div>

                <div class="card-body">
                    @if (session('status') == 'error')
                        <div class="alert alert-danger" role="alert">
                            {{ session('msg') }}
                        </div>
                    @endif

                    <form action="{{ route('transactions.store2') }}" method="post">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="key" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="button-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Cari</button>
                            </div>
                        </div>
                    </form>

                    <table class="table table-bordered">
                        <tbody>
                            @foreach (collect($transaksi)->except([
                                'id', 'proses', 'discount', 'payment_status', 'staff_in', 'staff_out', 'updated_at'
                            ]) as $key => $val)
                                <tr>
                                    <th scope="row">{{ $key }}</th>
                                    <td>{{ $val }}</td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
