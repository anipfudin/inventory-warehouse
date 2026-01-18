@extends('layouts.app')

@section('title', 'Buat Sales Order')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Buat Sales Order Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales-orders.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="required_date" class="form-label">Tanggal Required</label>
                            <input type="date" class="form-control @error('required_date') is-invalid @enderror" id="required_date" name="required_date">
                            @error('required_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"></textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Buat SO</button>
                            <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
