@extends('layouts.app')

@section('title', 'Detail Sales Order')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ $salesOrder->so_number }} - Detail</h5>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-{{ $salesOrder->status === 'draft' ? 'secondary' : ($salesOrder->status === 'pending' ? 'warning' : 'success') }}">
                        {{ ucfirst($salesOrder->status) }}
                    </span>
                </dd>

                <dt class="col-sm-3">Tanggal Required</dt>
                <dd class="col-sm-9">{{ $salesOrder->required_date?->format('d/m/Y') ?? '-' }}</dd>

                <dt class="col-sm-3">Total Amount</dt>
                <dd class="col-sm-9">Rp {{ number_format($salesOrder->total_amount, 0, ',', '.') }}</dd>

                <dt class="col-sm-3">Dibuat Oleh</dt>
                <dd class="col-sm-9">{{ $salesOrder->createdBy->name }}</dd>

                <dt class="col-sm-3">Catatan</dt>
                <dd class="col-sm-9">{{ $salesOrder->notes ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    <!-- Items -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Item dalam SO</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item Number</th>
                        <th>Nama</th>
                        <th>Qty Diminta</th>
                        <th>Qty Dikirim</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesOrder->details as $detail)
                        <tr>
                            <td><strong>{{ $detail->item->item_number }}</strong></td>
                            <td>{{ $detail->item->name }}</td>
                            <td>{{ $detail->quantity_requested }}</td>
                            <td>{{ $detail->quantity_shipped }}</td>
                            <td>Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada item</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            <h5>Total: Rp {{ number_format($salesOrder->total_amount, 0, ',', '.') }}</h5>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex gap-2">
        @if($salesOrder->status === 'pending' && Auth::user()->isAdmin())
            <form action="{{ route('sales-orders.ship', $salesOrder) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Yakin akan mengirim semua item?')">
                    <i class="bi bi-check-circle"></i> Kirim Barang
                </button>
            </form>
        @endif
        <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection
