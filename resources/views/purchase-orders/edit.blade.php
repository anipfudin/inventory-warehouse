@extends('layouts.app')

@section('title', 'Edit Purchase Order')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ $purchaseOrder->po_number }} - Edit</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST">
                @csrf @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select class="form-control @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @if($purchaseOrder->supplier_id == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="delivery_date" class="form-label">Tanggal Delivery</label>
                        <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" id="delivery_date" name="delivery_date" value="{{ $purchaseOrder->delivery_date?->format('Y-m-d') }}">
                        @error('delivery_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Catatan</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ $purchaseOrder->notes }}</textarea>
                    @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tambah Item -->
    @if($purchaseOrder->status === 'draft')
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Tambah Item</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('purchase-orders.add-item', $purchaseOrder) }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_id" class="form-label">Item</label>
                                <select class="form-control @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required>
                                    <option value="">Pilih Item</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->item_number }} - {{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('item_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                                @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Detail Items -->
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
                        <th>Aksi</th>
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
                            <td>
                                @if($purchaseOrder->status === 'draft')
                                    <form action="{{ route('purchase-orders.remove-item', [$purchaseOrder, $detail->id]) }}" method="POST" class="d-inline">
                                        @csrf @method('POST')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
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
            <h5>Total: Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</h5>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex gap-2">
        @if($purchaseOrder->status === 'draft')
            <form action="{{ route('purchase-orders.confirm', $purchaseOrder) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-circle"></i> Konfirmasi PO
                </button>
            </form>
        @endif
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection
