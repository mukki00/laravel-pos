@extends('layouts.admin')

@section('title', 'Edit Order')
@section('content-header', 'Edit Order')

@section('content')
    <div class="card">
    <div class="card-body">
        <form action="{{ route('orders.update', $order) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="Id">Id</label>
                <input type="text" name="Id" class="form-control @error('Id') is-invalid @enderror" id="Id"
                    placeholder="Id" value="{{ old('Id', $order->id) }}" disabled>
                @error('Id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name"
                    placeholder="customer_name" value="{{ old('customer_name',$order->getCustomerName()) }}" disabled>
                @error('customer_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="Total">Total</label>
                <input type="text" name="Total" class="form-control @error('Total') is-invalid @enderror" id="Total"
                    placeholder="Total" value="{{ old('Total', $order->formattedTotal()) }}" disabled>
                @error('Total')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="amount">Received Amount</label>
                <input type="text" name="amount" class="form-control @error('amount') is-invalid @enderror" id="amount"
                    placeholder="amount" value="{{ old('amount', $order->formattedReceivedAmount()) }}">
                @error('amount')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <button class="btn btn-primary" type="submit">Update</button>
    </form> 
    </div>
    </div>


@endsection

@section('js')
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(document).ready(function () {
        bsCustomFileInput.init();
    });
</script>
@endsection