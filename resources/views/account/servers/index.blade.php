@extends('layouts.base', ['title' => 'Server - List'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h3 class="mb-0">Server</h3>
                    <p class="mt-1 mb-0 text-muted">Kelola daftar server Anda</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddServer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1 align-middle"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Server
                </button>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">

                    {{-- Desktop: Tabel --}}
                    <div class="d-none d-md-block table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">Hostname</th>
                                    <th class="border-0">OS / Fungsi</th>
                                    <th class="border-0">IP Address</th>
                                    <th class="border-0">Lokasi</th>
                                    <th class="border-0">Tipe</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($servers as $server)
                                    <tr>
                                        <td class="ps-4 fw-medium">{{ $server->hostname }}</td>
                                        <td>{{ $server->os ?? '–' }}</td>
                                        <td><code class="text-body small">{{ $server->ip_address ?? '–' }}</code></td>
                                        <td>{{ $server->location?->name ?? '–' }}</td>
                                        <td><span class="badge bg-info">{{ \App\Models\Server::serverTypeLabels()[$server->server_type] ?? $server->server_type }}</span></td>
                                        <td>
                                            @php
                                                $badge = match($server->physical_status) {
                    'active' => 'success',
                    'maintenance' => 'warning',
                    'decommissioned' => 'secondary',
                    'inactive' => 'secondary',
                    default => 'secondary'
                };
                                            @endphp
                                            <span class="badge bg-{{ $badge }}">{{ \App\Models\Server::statusLabels()[$server->physical_status] ?? $server->physical_status }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex gap-1 justify-content-end flex-wrap">
                                                <a href="{{ route('server.show', $server) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                                <a href="{{ route('server.edit', $server) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                <form action="{{ route('server.destroy', $server) }}" method="POST" class="d-inline form-delete-server">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">Belum ada server. Klik <strong>Tambah Server</strong> untuk menambah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile: Card --}}
                    <div class="d-md-none p-3">
                        @forelse($servers as $server)
                            <div class="card border mb-3 server-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0 fw-semibold">{{ $server->hostname }}</h6>
                                        @php
                                            $badge = match($server->physical_status) {
                    'active' => 'success',
                    'maintenance' => 'warning',
                    'decommissioned' => 'secondary',
                    'inactive' => 'secondary',
                    default => 'secondary'
                };
                                        @endphp
                                        <span class="badge bg-{{ $badge }}">{{ \App\Models\Server::statusLabels()[$server->physical_status] ?? $server->physical_status }}</span>
                                    </div>
                                    @if($server->os)
                                        <p class="text-muted small mb-1">{{ $server->os }}</p>
                                    @endif
                                    <p class="text-muted small mb-1"><code>{{ $server->ip_address ?? '–' }}</code></p>
                                    <p class="text-muted small mb-2">{{ $server->location?->name ?? '–' }} · {{ \App\Models\Server::serverTypeLabels()[$server->server_type] ?? $server->server_type }}</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('server.show', $server) }}" class="btn btn-sm btn-primary">Detail</a>
                                        <a href="{{ route('server.edit', $server) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('server.destroy', $server) }}" method="POST" class="d-inline form-delete-server">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted py-4 mb-0">Belum ada server. Klik <strong>Tambah Server</strong> untuk menambah.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- Modal Tambah Server --}}
    <div class="modal fade" id="modalAddServer" tabindex="-1" aria-labelledby="modalAddServerLabel" aria-hidden="true" @if($errors->any()) data-bs-open="1" @endif>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="modalAddServerLabel">Tambah Server</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0 pb-4">
                    <form action="{{ route('server.store') }}" method="POST" id="formAddServer">
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
                                @foreach(\App\Models\Location::where('is_active', true)->orderBy('name')->get() as $loc)
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
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2" placeholder="Opsional">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="sort_order" class="form-label">Urutan</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script-bottom')
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: @json(session('success')),
        confirmButtonColor: '#335EEA',
        timer: 2500,
        timerProgressBar: true
    });
});
</script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalEl = document.getElementById('modalAddServer');
    if (modalEl && modalEl.getAttribute('data-bs-open') === '1') {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
    document.querySelectorAll('.form-delete-server').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Yakin hapus server ini?',
                text: 'Data tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus'
            }).then(function(result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
</script>
@endsection
