@extends('layouts.app')

@section('title', 'Lokasi Stok')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Lokasi Stok</h1>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('locations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Lokasi
        </a>
        @endif
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Zone</th>
                        <th>Aisle</th>
                        <th>Rack</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $location)
                        <tr>
                            <td><strong>{{ $location->code }}</strong></td>
                            <td>{{ $location->name }}</td>
                            <td>{{ $location->zone ?? '-' }}</td>
                            <td>{{ $location->aisle ?? '-' }}</td>
                            <td>{{ $location->rack ?? '-' }}</td>
                            <td>
                                <a href="{{ route('locations.show', $location) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('locations.edit', $location) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('locations.destroy', $location) }}" method="POST" class="d-inline">
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
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data lokasi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $locations->links() }}
    </div>
@endsection
