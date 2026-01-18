@extends('layouts.app')

@section('title', 'Detail Lokasi')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Detail Lokasi: {{ $location->name }}</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Kode</dt>
                        <dd class="col-sm-9">{{ $location->code }}</dd>

                        <dt class="col-sm-3">Nama</dt>
                        <dd class="col-sm-9">{{ $location->name }}</dd>

                        <dt class="col-sm-3">Zone</dt>
                        <dd class="col-sm-9">{{ $location->zone ?? '-' }}</dd>

                        <dt class="col-sm-3">Aisle</dt>
                        <dd class="col-sm-9">{{ $location->aisle ?? '-' }}</dd>

                        <dt class="col-sm-3">Rack</dt>
                        <dd class="col-sm-9">{{ $location->rack ?? '-' }}</dd>
                    </dl>

                    <a href="{{ route('locations.edit', $location) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('locations.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Stok di Lokasi Ini</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item Number</th>
                        <th>Nama Item</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr>
                            <td><strong>{{ $stock->item->item_number }}</strong></td>
                            <td>{{ $stock->item->name }}</td>
                            <td>{{ $stock->item->unit }}</td>
                            <td>{{ $stock->quantity }}</td>
                            <td>{{ $stock->last_updated?->format('d/m/Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada stok</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $stocks->links() }}
    </div>
@endsection
