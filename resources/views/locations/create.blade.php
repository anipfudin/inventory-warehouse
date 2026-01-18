@extends('layouts.app')

@section('title', 'Tambah Lokasi Stok')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tambah Lokasi Stok Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('locations.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="code" class="form-label">Kode Lokasi</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lokasi</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="zone" class="form-label">Zone</label>
                                    <input type="text" class="form-control @error('zone') is-invalid @enderror" id="zone" name="zone" value="{{ old('zone') }}">
                                    @error('zone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="aisle" class="form-label">Aisle</label>
                                    <input type="text" class="form-control @error('aisle') is-invalid @enderror" id="aisle" name="aisle" value="{{ old('aisle') }}">
                                    @error('aisle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="rack" class="form-label">Rack</label>
                                    <input type="text" class="form-control @error('rack') is-invalid @enderror" id="rack" name="rack" value="{{ old('rack') }}">
                                    @error('rack') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('locations.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
