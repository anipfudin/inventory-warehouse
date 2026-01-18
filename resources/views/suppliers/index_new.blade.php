@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Data Supplier</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Supplier
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Kota</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>{{ $supplier->city }}</td>
                    <td>
                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i> Lihat
                        </a>
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
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

<div class="d-flex justify-content-end mt-3">
    {{ $suppliers->links() }}
</div>
@endsection
