@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Item</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('items.update', $item) }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="mb-3">
                            <label for="item_number" class="form-label">Nomor Item</label>
                            <input type="text" class="form-control @error('item_number') is-invalid @enderror" id="item_number" name="item_number" value="{{ old('item_number', $item->item_number) }}" required>
                            @error('item_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Item</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select class="form-control @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @if(old('supplier_id', $item->supplier_id) == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit</label>
                                    <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit', $item->unit) }}" required>
                                    @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit_price" class="form-label">Harga Unit</label>
                                    <input type="number" class="form-control @error('unit_price') is-invalid @enderror" id="unit_price" name="unit_price" value="{{ old('unit_price', $item->unit_price) }}" step="0.01" required>
                                    @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="minimum_stock" class="form-label">Stock Minimum</label>
                            <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" required>
                            @error('minimum_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
