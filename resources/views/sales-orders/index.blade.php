@extends('layouts.app')

@section('title', 'Sales Orders - Barang Keluar')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Barang Keluar (Sales Order)</h1>
        <a href="{{ route('sales-orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat SO Baru
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>SO Number</th>
                        <th>Tanggal Required</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesOrders as $so)
                        <tr>
                            <td><strong>{{ $so->so_number }}</strong></td>
                            <td>{{ $so->required_date?->format('d/m/Y') ?? '-' }}</td>
                            <td>Rp {{ number_format($so->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $so->status === 'draft' ? 'secondary' : ($so->status === 'pending' ? 'warning' : 'success') }}">
                                    {{ ucfirst($so->status) }}
                                </span>
                            </td>
                            <td>{{ $so->createdBy->name }}</td>
                            <td>
                                <a href="{{ route('sales-orders.show', $so) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($so->status === 'draft')
                                    <a href="{{ route('sales-orders.edit', $so) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('sales-orders.destroy', $so) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @elseif($so->status === 'pending' && Auth::user()->isAdmin())
                                    <form action="{{ route('sales-orders.ship', $so) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Yakin akan mengirim semua item?')">
                                            <i class="bi bi-check-circle"></i> Kirim
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data SO</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $salesOrders->links() }}
    </div>
@endsection
