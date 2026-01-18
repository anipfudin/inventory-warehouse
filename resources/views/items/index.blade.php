@extends('layouts.app')

@section('title', 'Item')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Item</h1>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Item
        </a>
        @endif
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item Number</th>
                        <th>Nama</th>
                        <th>Supplier</th>
                        <th>Unit</th>
                        <th>Harga Unit</th>
                        <th>Stock Min</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td><strong>{{ $item->item_number }}</strong></td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->supplier->name }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td>{{ $item->minimum_stock }}</td>
                            <td>
                                <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data item</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $items->links() }}
    </div>
@endsection
