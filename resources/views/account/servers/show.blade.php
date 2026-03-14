@extends('layouts.base', ['title' => 'Detail Server'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('server.index') }}" class="text-muted text-decoration-none small">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="align-text-bottom me-1"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali ke daftar server
                </a>
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3 mt-2">
                    <div>
                        <h3 class="mb-1">{{ $server->hostname }}</h3>
                        @php
                            $badge = match($server->physical_status) {
                                'active' => 'success',
                                'maintenance' => 'warning',
                                'decommissioned' => 'secondary',
                                'inactive' => 'secondary',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $badge }} me-1">{{ \App\Models\Server::statusLabels()[$server->physical_status] ?? $server->physical_status }}</span>
                        <span class="badge bg-info">{{ \App\Models\Server::serverTypeLabels()[$server->server_type] ?? $server->server_type }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 w-100 w-sm-auto">
                        <a href="{{ route('server.edit', $server) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('server.destroy', $server) }}" method="POST" class="d-inline form-delete-server">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-3 col-md-2 text-muted fw-normal">IP Address</dt>
                        <dd class="col-sm-9 col-md-10"><code class="bg-light px-2 py-1 rounded">{{ $server->ip_address ?? '–' }}</code></dd>

                        <dt class="col-sm-3 col-md-2 text-muted fw-normal">Lokasi</dt>
                        <dd class="col-sm-9 col-md-10">{{ $server->location?->name ?? '–' }}</dd>

                        <dt class="col-sm-3 col-md-2 text-muted fw-normal">OS / Fungsi</dt>
                        <dd class="col-sm-9 col-md-10">{{ $server->os ?? '–' }}</dd>

                        <dt class="col-sm-3 col-md-2 text-muted fw-normal">Tipe</dt>
                        <dd class="col-sm-9 col-md-10">{{ \App\Models\Server::serverTypeLabels()[$server->server_type] ?? $server->server_type }}</dd>

                        <dt class="col-sm-3 col-md-2 text-muted fw-normal">Status</dt>
                        <dd class="col-sm-9 col-md-10"><span class="badge bg-{{ $badge }}">{{ \App\Models\Server::statusLabels()[$server->physical_status] ?? $server->physical_status }}</span></dd>

                        @if($server->notes)
                            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Catatan</dt>
                            <dd class="col-sm-9 col-md-10">{{ $server->notes }}</dd>
                        @endif

                        <dt class="col-sm-3 col-md-2 text-muted fw-normal">Ditambah</dt>
                        <dd class="col-sm-9 col-md-10">{{ $server->created_at->format('d M Y, H:i') }}</dd>

                        <dt class="col-sm-3 col-md-2 text-muted fw-normal">Terakhir diubah</dt>
                        <dd class="col-sm-9 col-md-10">{{ $server->updated_at->format('d M Y, H:i') }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Spesifikasi Server --}}
            <div class="card border-0 shadow-sm mb-4 spec-card">
                <div class="card-header bg-white border-0 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 py-3 px-3 px-md-4">
                    <h5 class="mb-0 fw-600">Spesifikasi Server</h5>
                    <a href="{{ route('component-type.index') }}" class="btn btn-sm btn-outline-secondary align-self-start align-self-sm-center">Kelola tipe</a>
                </div>
                <div class="card-body p-4">
                    @if(isset($componentTypes) && $componentTypes->isNotEmpty())
                        <form action="{{ route('server.components.store', $server) }}" method="POST" id="form-add-component" class="spec-form mb-4">
                            @csrf
                            <div class="spec-form-inner rounded-2 border bg-light bg-opacity-50 p-3">
                                <div class="row g-3 align-items-end">
                                    <div class="col-12 col-md-4 col-lg-3">
                                        <label for="comp_type_id" class="form-label spec-label">Tipe</label>
                                        <select class="form-select form-select-sm @error('component_type_id') is-invalid @enderror" id="comp_type_id" name="component_type_id" required>
                                            <option value="">Pilih tipe …</option>
                                            @foreach($componentTypes as $ct)
                                                <option value="{{ $ct->id }}" data-attrs="{{ json_encode($ct->attributes ?? []) }}" {{ old('component_type_id') == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('component_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12 col-md-4 col-lg-3">
                                        <label for="comp_label" class="form-label spec-label">Label</label>
                                        <input type="text" class="form-control form-control-sm @error('label') is-invalid @enderror" id="comp_label" name="label" value="{{ old('label') }}" required maxlength="100" placeholder="Contoh: Disk 0, RAM 1">
                                        @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12 col-md col-lg-6" id="comp-spec-block">
                                        <label class="form-label spec-label d-block" id="comp-spec-label" style="display: none;">Spesifikasi</label>
                                        <div id="comp-dynamic-attrs" class="row g-2 g-md-3"></div>
                                    </div>
                                    <div class="col-12 col-md-auto col-lg-auto order-last order-md-0">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 w-md-auto px-3">Tambah</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <p class="text-muted small mb-0">Belum ada tipe. <a href="{{ route('component-type.create') }}">Buat tipe dulu</a> (Disk, RAM, CPU, PSU) beserta atributnya.</p>
                    @endif

                    @if($server->components->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <p class="small mb-0">Belum ada spesifikasi.</p>
                            <p class="small mb-0">Pilih tipe, isi label dan spesifikasi di atas, lalu klik Tambah.</p>
                        </div>
                    @else
                        <form id="form-bulk-delete-components" method="POST" action="{{ route('server.components.destroy.bulk', $server) }}">
                            @csrf
                            @method('DELETE')
                            {{-- Desktop: table --}}
                            <div class="d-none d-md-block table-responsive rounded border">
                                <table class="table table-sm table-hover align-middle mb-0 spec-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="spec-th-cb"><input type="checkbox" class="form-check-input" id="component-select-all" title="Pilih semua"></th>
                                            <th class="spec-th-type">Komponen</th>
                                            <th class="spec-th-detail">Spesifikasi</th>
                                            <th class="spec-th-action text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($server->components as $comp)
                                            <tr>
                                                <td><input type="checkbox" class="form-check-input component-cb" name="components[]" value="{{ $comp->id }}" form="form-bulk-delete-components"></td>
                                                <td><span class="fw-medium">{{ $comp->componentType->name ?? '–' }}</span> <span class="text-muted">{{ $comp->label ?? $comp->name ?? '' }}</span></td>
                                                <td class="spec-detail">
                                                    @if(!empty($comp->values) && is_array($comp->values))
                                                        @php $pairs = []; foreach($comp->values as $k => $v) { if ((string)$v !== '') $pairs[] = $k . ': ' . $v; } @endphp
                                                        {{ implode(' · ', $pairs) ?: '–' }}
                                                    @else
                                                        –
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end align-items-center flex-wrap">
                                                        <a href="{{ route('server.components.edit', [$server, $comp]) }}" class="btn btn-sm btn-outline-primary" title="Edit">Edit</a>
                                                        <form action="{{ route('server.components.destroy', [$server, $comp]) }}" method="POST" class="d-inline form-delete-component">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">Hapus</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- Mobile: cards --}}
                            <div class="d-md-none">
                                <div class="mb-2">
                                    <input type="checkbox" class="form-check-input" id="component-select-all-mob" title="Pilih semua">
                                    <label for="component-select-all-mob" class="form-check-label small">Pilih semua</label>
                                </div>
                                @foreach($server->components as $comp)
                                    <div class="card border mb-2 spec-mobile-card">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start gap-2 mb-2">
                                                <input type="checkbox" class="form-check-input component-cb mt-1" name="components[]" value="{{ $comp->id }}" form="form-bulk-delete-components">
                                                <div class="flex-grow-1 min-w-0">
                                                    <span class="fw-medium">{{ $comp->componentType->name ?? '–' }}</span>
                                                    <span class="text-muted">{{ $comp->label ?? $comp->name ?? '' }}</span>
                                                    <div class="small text-muted mt-1">
                                                        @if(!empty($comp->values) && is_array($comp->values))
                                                            @php $pairs = []; foreach($comp->values as $k => $v) { if ((string)$v !== '') $pairs[] = $k . ': ' . $v; } @endphp
                                                            {{ implode(' · ', $pairs) ?: '–' }}
                                                        @else
                                                            –
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
                                                <a href="{{ route('server.components.edit', [$server, $comp]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="{{ route('server.components.destroy', [$server, $comp]) }}" method="POST" class="d-inline form-delete-component">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="mt-2">
                                    <button type="submit" form="form-bulk-delete-components" class="btn btn-sm btn-outline-danger form-bulk-delete-components-btn" disabled>Hapus terpilih</button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
            <style>
            .spec-card .card-header { font-size: 0.9375rem; }
            .spec-form .spec-label { font-size: 0.8125rem; font-weight: 500; color: #495057; margin-bottom: 0.25rem; }
            .spec-form .form-control-sm, .spec-form .form-select-sm { font-size: 0.875rem; }
            .spec-form-inner .form-control:focus, .spec-form-inner .form-select:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15); }
            .spec-table { font-size: 0.8125rem; }
            .spec-table .spec-th-cb { width: 2.25rem; padding: 0.5rem 0.75rem; }
            .spec-table .spec-th-type { padding: 0.5rem 0.75rem; min-width: 8rem; }
            .spec-table .spec-th-detail { padding: 0.5rem 0.75rem; max-width: 20rem; }
.spec-table .spec-th-action { min-width: 8rem; padding: 0.5rem 0.75rem; }
.spec-table td { padding: 0.5rem 0.75rem; vertical-align: middle; }
.spec-table .spec-detail { max-width: 20rem; word-break: break-word; color: #495057; }
            </style>

            {{-- Checklist --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 border-bottom py-3">
                    <h5 class="mb-0">Checklist Server</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('server.checklist.store', $server) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-8">
                                <label for="item_title" class="form-label small">Item <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror" id="item_title" name="title" value="{{ old('title') }}" required maxlength="255" placeholder="Contoh: Cek disk space, Backup database">
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6 col-md-2">
                                <label for="item_sort" class="form-label small">Urutan</label>
                                <input type="number" class="form-control form-control-sm" name="sort_order" id="item_sort" value="{{ old('sort_order', 0) }}" min="0">
                            </div>
                            <div class="col-6 col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Tambah</button>
                            </div>
                        </div>
                    </form>

                    @if($server->checklistItems->isEmpty())
                        <p class="text-muted small mb-0">Belum ada item checklist. Tambah di form atas.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($server->checklistItems as $item)
                                <li class="list-group-item d-flex flex-wrap align-items-center gap-2 px-0 py-3">
                                    <form action="{{ route('server.checklist.toggle', [$server, $item]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" title="{{ $item->is_checked ? 'Tandai belum selesai' : 'Tandai selesai' }}">
                                            @if($item->is_checked)
                                                <span class="text-success">✓</span>
                                            @else
                                                <span class="text-muted">○</span>
                                            @endif
                                        </button>
                                    </form>
                                    <span class="{{ $item->is_checked ? 'text-decoration-line-through text-muted' : '' }} flex-grow-1 min-w-0">{{ $item->title }}</span>
                                    <form action="{{ route('server.checklist.destroy', [$server, $item]) }}" method="POST" class="d-inline form-delete-checklist">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script-bottom')
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: '#335EEA', timer: 2000, timerProgressBar: true });
    }
});
</script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
    function confirmDelete(form, msg) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var f = this;
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'Yakin hapus?', text: msg, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, hapus' })
                    .then(function(r) { if (r.isConfirmed) f.submit(); });
            } else {
                if (confirm(msg)) f.submit();
            }
        });
    }
    document.querySelectorAll('.form-delete-server').forEach(function(f) { confirmDelete(f, 'Yakin hapus server ini? Data tidak dapat dikembalikan.'); });
    document.querySelectorAll('.form-delete-component').forEach(function(f) { confirmDelete(f, 'Yakin hapus component ini?'); });
    document.querySelectorAll('.form-delete-checklist').forEach(function(f) { confirmDelete(f, 'Yakin hapus item checklist ini?'); });

    var compTypeSelect = document.getElementById('comp_type_id');
    var compDynamicAttrs = document.getElementById('comp-dynamic-attrs');
    var compSpecLabel = document.getElementById('comp-spec-label');
    if (compTypeSelect && compDynamicAttrs) {
        function renderCompAttrs() {
            var opt = compTypeSelect.options[compTypeSelect.selectedIndex];
            compDynamicAttrs.innerHTML = '';
            if (compSpecLabel) compSpecLabel.style.display = 'none';
            if (!opt || !opt.value) return;
            var attrs = [];
            try { attrs = JSON.parse(opt.getAttribute('data-attrs') || '[]'); } catch (e) {}
            attrs.forEach(function(a) {
                var name = a.name || a.slug || '';
                var slug = a.slug || (a.name ? name.toLowerCase().replace(/\s+/g, '_') : '');
                if (!slug) return;
                var col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';
                col.innerHTML = '<label class="form-label spec-label">' + name + '</label><input type="text" class="form-control form-control-sm" name="attr_' + slug + '" placeholder="' + name + '">';
                compDynamicAttrs.appendChild(col);
            });
            if (attrs.length > 0 && compSpecLabel) compSpecLabel.style.display = 'block';
        }
        compTypeSelect.addEventListener('change', renderCompAttrs);
        renderCompAttrs();
    }

    var selectAll = document.getElementById('component-select-all');
    var selectAllMob = document.getElementById('component-select-all-mob');
    var checkboxes = document.querySelectorAll('.component-cb');
    var bulkBtn = document.querySelector('.form-bulk-delete-components-btn');
    var bulkForm = document.getElementById('form-bulk-delete-components');
    function updateBulkState() {
        var any = Array.prototype.slice.call(checkboxes).some(function(c) { return c.checked; });
        var all = any && checkboxes.length === Array.prototype.slice.call(checkboxes).filter(function(c) { return c.checked; }).length;
        if (bulkBtn) bulkBtn.disabled = !any;
        if (selectAll) selectAll.checked = all;
        if (selectAllMob) selectAllMob.checked = all;
    }
    function setAll(checked) {
        checkboxes.forEach(function(cb) { cb.checked = checked; });
        if (bulkBtn) bulkBtn.disabled = !checked;
        if (selectAll) selectAll.checked = checked;
        if (selectAllMob) selectAllMob.checked = checked;
    }
    if (checkboxes.length) {
        if (selectAll) selectAll.addEventListener('change', function() { setAll(selectAll.checked); });
        if (selectAllMob) selectAllMob.addEventListener('change', function() { setAll(selectAllMob.checked); });
        checkboxes.forEach(function(cb) {
            cb.addEventListener('change', updateBulkState);
        });
    }
    if (bulkForm && bulkBtn) {
        bulkForm.addEventListener('submit', function(e) {
            var n = document.querySelectorAll('.component-cb:checked').length;
            if (n === 0) { e.preventDefault(); return; }
            e.preventDefault();
            var f = this;
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'Yakin hapus ' + n + ' component terpilih?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, hapus' })
                    .then(function(r) { if (r.isConfirmed) f.submit(); });
            } else {
                if (confirm('Yakin hapus ' + n + ' component terpilih?')) f.submit();
            }
        });
    }
});
</script>
@endsection
