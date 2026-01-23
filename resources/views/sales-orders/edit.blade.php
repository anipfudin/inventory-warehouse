@extends('layouts.app')

@section('title', 'Edit Sales Order')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ $salesOrder->so_number }} - Edit</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sales-orders.update', $salesOrder) }}" method="POST">
                @csrf @method('PUT')
                
                <div class="mb-3">
                    <label for="required_date" class="form-label">Tanggal Required</label>
                    <input type="date" class="form-control @error('required_date') is-invalid @enderror" id="required_date" name="required_date" value="{{ $salesOrder->required_date?->format('Y-m-d') }}">
                    @error('required_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Catatan</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ $salesOrder->notes }}</textarea>
                    @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tambah Item -->
    @if($salesOrder->status === 'draft')
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Tambah Item</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sales-orders.add-item', $salesOrder) }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_id" class="form-label">Item</label>
                                <select class="form-control @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required onchange="updateStockInfo()">
                                    <option value="">Pilih Item</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" data-stock="{{ $item->getTotalStock() }}">
                                            {{ $item->item_number }} - {{ $item->name }} (Stock: {{ $item->getTotalStock() }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <small id="stockInfo" class="form-text text-muted"></small>
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
            <h5 class="mb-0">Item dalam SO</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item Number</th>
                        <th>Nama</th>
                        <th>Qty Diminta</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesOrder->details as $detail)
                        <tr>
                            <td><strong>{{ $detail->item->item_number }}</strong></td>
                            <td>
                                {{ $detail->item->name }}
                                <br>
                                <small class="text-muted">Stock: {{ $detail->item->getTotalStock() }}</small>
                            </td>
                            <td>{{ $detail->quantity_requested }}</td>
                            <td>Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            <td>
                                @if($salesOrder->status === 'draft')
                                    <form action="{{ route('sales-orders.remove-item', [$salesOrder, $detail->id]) }}" method="POST" class="d-inline">
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
            <h5>Total: Rp {{ number_format($salesOrder->total_amount, 0, ',', '.') }}</h5>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex gap-2">
        @if($salesOrder->status === 'draft')
            <form action="{{ route('sales-orders.confirm', $salesOrder) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-circle"></i> Konfirmasi SO
                </button>
            </form>
        @endif
        <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    @push('scripts')
    <script>
        function updateStockInfo() {
            const select = document.getElementById('item_id');
            const option = select.options[select.selectedIndex];
            const stock = option.getAttribute('data-stock');
            document.getElementById('stockInfo').textContent = stock ? `Stock tersedia: ${stock}` : '';
        }
    </script>
    @endpush
@endsection
