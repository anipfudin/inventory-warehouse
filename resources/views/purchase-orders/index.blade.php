@extends('layouts.app')

@section('title', 'Purchase Orders - Barang Masuk')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Barang Masuk (Purchase Order)</h1>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat PO Baru
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Tanggal Delivery</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                        <tr>
                            <td><strong>{{ $po->po_number }}</strong></td>
                            <td>{{ $po->supplier->name }}</td>
                            <td>{{ $po->delivery_date?->format('d/m/Y') ?? '-' }}</td>
                            <td>Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $po->status === 'draft' ? 'secondary' : ($po->status === 'pending' ? 'warning' : 'success') }}">
                                    {{ ucfirst($po->status) }}
                                </span>
                            </td>
                            <td>{{ $po->createdBy->name }}</td>
                            <td>
                                <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($po->status === 'draft')
                                    <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @elseif($po->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#receiveModal{{ $po->id }}">
                                        <i class="bi bi-check-circle"></i> Terima
                                    </button>

                                    <!-- Modal Terima Barang -->
                                    <div class="modal fade" id="receiveModal{{ $po->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Terima Barang</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('purchase-orders.receive', $po) }}" method="POST">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data PO</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $purchaseOrders->links() }}
    </div>
@endsection
