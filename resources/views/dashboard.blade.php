@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h1 class="text-3xl font-bold">Dashboard</h1>
    <p class="text-gray-600">Selamat datang, {{ Auth::user()->name }}!</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Item</h6>
                <h2 class="card-text">{{ $totalItems }}</h2>
                <small class="text-muted">Barang dalam sistem</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card suppliers">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Supplier</h6>
                <h2 class="card-text">{{ $totalSuppliers }}</h2>
                <small class="text-muted">Supplier aktif</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card locations">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Lokasi</h6>
                <h2 class="card-text">{{ $totalLocations }}</h2>
                <small class="text-muted">Lokasi penyimpanan</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card value">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Nilai Stok</h6>
                <h2 class="card-text">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h2>
                <small class="text-muted">Nilai inventory</small>
            </div>
        </div>
    </div>
</div>

<!-- Pending Transactions -->
<div class="row mb-4">
    @if(Auth::user()->isAdmin())
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">
                    <i class="bi bi-arrow-down-circle"></i> PO Pending
                </h5>
            </div>
            <div class="card-body">
                <h2 class="text-center mb-0">{{ $pendingPO }}</h2>
                <p class="text-center text-muted mb-0">Purchase Order menunggu penerimaan</p>
                <div class="text-center mt-3">
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-warning">
                        Lihat PO
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-arrow-up-circle"></i> SO Pending
                </h5>
            </div>
            <div class="card-body">
                <h2 class="text-center mb-0">{{ $pendingSO }}</h2>
                <p class="text-center text-muted mb-0">Sales Order menunggu pengiriman</p>
                <div class="text-center mt-3">
                    <a href="{{ route('sales-orders.index') }}" class="btn btn-sm btn-outline-info">
                        Lihat SO
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
@if($lowStockItems->count() > 0)
<div class="card mb-4 border-danger">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="bi bi-exclamation-triangle"></i> Peringatan Stok Rendah
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th>Stok Saat Ini</th>
                    <th>Minimum</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockItems as $item)
                <tr>
                    <td>
                        <strong>{{ $item->name }}</strong><br>
                        <small class="text-muted">{{ $item->item_number }}</small>
                    </td>
                    <td>{{ $item->getTotalStock() }} {{ $item->unit }}</td>
                    <td>{{ $item->minimum_stock }} {{ $item->unit }}</td>
                    <td>
                        <span class="badge bg-danger">Kurang dari minimum</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('items.index') }}" class="btn btn-sm btn-danger">
            Atur Stok
        </a>
    </div>
</div>
@endif

<!-- Recent Stock Movements -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-arrow-left-right"></i> Pergerakan Stok Terbaru
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Referensi</th>
                    <th>Tipe</th>
                    <th>Item</th>
                    <th>Lokasi</th>
                    <th>Qty</th>
                    <th>User</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentMovements as $movement)
                <tr>
                    <td>
                        <strong>{{ $movement->reference_number }}</strong><br>
                        <small class="text-muted">{{ $movement->reference_type }}</small>
                    </td>
                    <td>
                        @if($movement->type === 'IN')
                            <span class="badge bg-success">
                                <i class="bi bi-arrow-down"></i> MASUK
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="bi bi-arrow-up"></i> KELUAR
                            </span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $movement->item->name }}</strong><br>
                        <small class="text-muted">{{ $movement->item->item_number }}</small>
                    </td>
                    <td>{{ $movement->location->name }}</td>
                    <td><strong>{{ $movement->quantity }}</strong></td>
                    <td>{{ $movement->createdBy->name }}</td>
                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Belum ada pergerakan stok
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
