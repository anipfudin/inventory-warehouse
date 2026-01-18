@extends('layouts.app')

@section('title', 'Detail Purchase Order')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ $purchaseOrder->po_number }} - Detail</h5>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Supplier</dt>
                <dd class="col-sm-9">{{ $purchaseOrder->supplier->name }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-{{ $purchaseOrder->status === 'draft' ? 'secondary' : ($purchaseOrder->status === 'pending' ? 'warning' : 'success') }}">
                        {{ ucfirst($purchaseOrder->status) }}
                    </span>
                </dd>

                <dt class="col-sm-3">Tanggal Delivery</dt>
                <dd class="col-sm-9">{{ $purchaseOrder->delivery_date?->format('d/m/Y') ?? '-' }}</dd>

                <dt class="col-sm-3">Total Amount</dt>
                <dd class="col-sm-9">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</dd>

                <dt class="col-sm-3">Dibuat Oleh</dt>
                <dd class="col-sm-9">{{ $purchaseOrder->createdBy->name }}</dd>

                <dt class="col-sm-3">Catatan</dt>
                <dd class="col-sm-9">{{ $purchaseOrder->notes ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    <!-- Items -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Item dalam PO</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item Number</th>
                        <th>Nama</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrder->details as $detail)
                        <tr>
                            <td><strong>{{ $detail->item->item_number }}</strong></td>
                            <td>{{ $detail->item->name }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada item</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            <h5>Total: Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</h5>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex gap-2">
        @if($purchaseOrder->status === 'pending')
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#receiveModal">
                <i class="bi bi-check-circle"></i> Terima Barang
            </button>

            <!-- Modal Terima Barang -->
            <div class="modal fade" id="receiveModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Terima Barang</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Lokasi Penyimpanan</label>
                                    <select class="form-control" name="location_id" required>
                                        <option value="">Pilih Lokasi</option>
                                        @foreach(\App\Models\Location::all() as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Terima Barang</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection
