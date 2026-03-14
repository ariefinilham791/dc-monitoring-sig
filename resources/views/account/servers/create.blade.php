@extends('layouts.base', ['title' => 'Tambah Server'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('server.index') }}" class="text-muted text-decoration-none small">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="align-text-bottom me-1"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali ke daftar server
                </a>
                <h3 class="mt-2 mb-0">Tambah Server</h3>
                <p class="text-muted mb-0">Isi data server baru</p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('server.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="hostname" class="form-label">Hostname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('hostname') is-invalid @enderror" id="hostname" name="hostname" value="{{ old('hostname') }}" required maxlength="100" placeholder="server01">
                                @error('hostname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="ip_address" class="form-label">IP Address</label>
                                <input type="text" class="form-control @error('ip_address') is-invalid @enderror" id="ip_address" name="ip_address" value="{{ old('ip_address') }}" maxlength="45" placeholder="192.168.1.1">
                                @error('ip_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="location_id" class="form-label">Lokasi <span class="text-danger">*</span></label>
                            <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                            @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="os" class="form-label">OS</label>
                            <input type="text" class="form-control @error('os') is-invalid @enderror" id="os" name="os" value="{{ old('os') }}" maxlength="100" placeholder="Ubuntu 22.04">
                            @error('os')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Tipe Server</label>
                            <div class="status-pills">
                                <input type="radio" name="server_type" id="st_physical" value="physical" {{ old('server_type', 'physical') === 'physical' ? 'checked' : '' }} class="status-pill-input">
                                <label for="st_physical" class="status-pill status-pill-secondary">Physical</label>
                                <input type="radio" name="server_type" id="st_virtual" value="virtual" {{ old('server_type') === 'virtual' ? 'checked' : '' }} class="status-pill-input">
                                <label for="st_virtual" class="status-pill status-pill-info">Virtual</label>
                                <input type="radio" name="server_type" id="st_cloud" value="cloud" {{ old('server_type') === 'cloud' ? 'checked' : '' }} class="status-pill-input">
                                <label for="st_cloud" class="status-pill status-pill-info">Cloud</label>
                            </div>
                            @error('server_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="status-pills">
                                <input type="radio" name="physical_status" id="ps_active" value="active" {{ old('physical_status', 'active') === 'active' ? 'checked' : '' }} class="status-pill-input">
                                <label for="ps_active" class="status-pill status-pill-success">Active</label>
                                <input type="radio" name="physical_status" id="ps_maintenance" value="maintenance" {{ old('physical_status') === 'maintenance' ? 'checked' : '' }} class="status-pill-input">
                                <label for="ps_maintenance" class="status-pill status-pill-warning">Maintenance</label>
                                <input type="radio" name="physical_status" id="ps_inactive" value="inactive" {{ old('physical_status') === 'inactive' ? 'checked' : '' }} class="status-pill-input">
                                <label for="ps_inactive" class="status-pill status-pill-secondary">Inactive</label>
                                <input type="radio" name="physical_status" id="ps_decommissioned" value="decommissioned" {{ old('physical_status') === 'decommissioned' ? 'checked' : '' }} class="status-pill-input">
                                <label for="ps_decommissioned" class="status-pill status-pill-secondary">Decommissioned</label>
                            </div>
                            @error('physical_status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Opsional">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="sort_order" class="form-label">Urutan</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('server.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
