@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Supplier</h1>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Supplier
        </a>
        @endif
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Kota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td><strong>{{ $supplier->name }}</strong></td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>{{ $supplier->city }}</td>
                            <td>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data supplier</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $suppliers->links() }}
    </div>
@endsection
