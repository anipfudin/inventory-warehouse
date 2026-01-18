@extends('layouts.app')

@section('title', 'Detail Item')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Detail Item: {{ $item->name }}</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Item Number</dt>
                        <dd class="col-sm-9">{{ $item->item_number }}</dd>

                        <dt class="col-sm-3">Nama</dt>
                        <dd class="col-sm-9">{{ $item->name }}</dd>

                        <dt class="col-sm-3">Deskripsi</dt>
                        <dd class="col-sm-9">{{ $item->description ?? '-' }}</dd>

                        <dt class="col-sm-3">Supplier</dt>
                        <dd class="col-sm-9">{{ $item->supplier->name }}</dd>

                        <dt class="col-sm-3">Unit</dt>
                        <dd class="col-sm-9">{{ $item->unit }}</dd>

                        <dt class="col-sm-3">Harga Unit</dt>
                        <dd class="col-sm-9">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</dd>

                        <dt class="col-sm-3">Stock Minimum</dt>
                        <dd class="col-sm-9">{{ $item->minimum_stock }}</dd>

                        <dt class="col-sm-3">Total Stock</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-info" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                {{ $totalStock }}
                            </span>
                        </dd>
                    </dl>

                    <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Stok Berdasarkan Lokasi</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Lokasi</th>
                        <th>Kode</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr>
                            <td>{{ $stock->location->name }}</td>
                            <td><code>{{ $stock->location->code }}</code></td>
                            <td>{{ $stock->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada stok</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
