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
                            <div class="spec-form-inner">
                                <div class="row g-3 mb-0">
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label for="comp_type_id" class="form-label spec-label">Tipe</label>
                                        <select class="form-select form-select-sm @error('component_type_id') is-invalid @enderror" id="comp_type_id" name="component_type_id" required>
                                            <option value="">Pilih tipe …</option>
                                            @foreach($componentTypes as $ct)
                                                <option value="{{ $ct->id }}" data-attrs="{{ e(json_encode($ct->attributes ?? [])) }}" {{ old('component_type_id') == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('component_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label for="comp_label" class="form-label spec-label">Label</label>
                                        <input type="text" class="form-control form-control-sm @error('label') is-invalid @enderror" id="comp_label" name="label" value="{{ old('label') }}" required maxlength="100" placeholder="Contoh: Disk 0, RAM 1">
                                        @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">Tambah</button>
                                    </div>
                                </div>
                                <div class="spec-dynamic-wrap mt-3" id="comp-spec-block" style="display: none;">
                                    <label class="form-label spec-label d-block mb-2" id="comp-spec-label">Spesifikasi</label>
                                    <div id="comp-dynamic-attrs" class="row g-2 g-md-3"></div>
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
                            {{-- Desktop: toolbar + table --}}
                            <div class="d-none d-md-block mb-2">
                                <button type="submit" form="form-bulk-delete-components" class="btn btn-sm btn-outline-danger form-bulk-delete-components-btn" disabled>Hapus terpilih</button>
                            </div>
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
                                                <td class="text-end spec-td-action">
                                                    <div class="d-flex gap-1 justify-content-end align-items-center flex-nowrap">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-duplicate-component" title="Duplikat"
                                                            data-type-id="{{ $comp->component_type_id }}"
                                                            data-type-name="{{ e($comp->componentType->name ?? '') }}"
                                                            data-label="{{ e($comp->label ?? '') }}"
                                                            data-values="{{ json_encode($comp->values ?? []) }}"
                                                            data-attrs="{{ e(json_encode($comp->componentType->attributes ?? [])) }}">Duplikat</button>
                                                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-component" title="Edit"
                                                            data-component-id="{{ $comp->id }}"
                                                            data-type-id="{{ $comp->component_type_id }}"
                                                            data-type-name="{{ e($comp->componentType->name ?? '') }}"
                                                            data-label="{{ e($comp->label ?? '') }}"
                                                            data-values="{{ e(json_encode($comp->values ?? [])) }}"
                                                            data-attrs="{{ e(json_encode($comp->componentType->attributes ?? [])) }}">Edit</button>
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
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-duplicate-component"
                                                    data-type-id="{{ $comp->component_type_id }}"
                                                    data-type-name="{{ e($comp->componentType->name ?? '') }}"
                                                    data-label="{{ e($comp->label ?? '') }}"
                                                    data-values="{{ json_encode($comp->values ?? []) }}"
                                                    data-attrs="{{ e(json_encode($comp->componentType->attributes ?? [])) }}">Duplikat</button>
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-component"
                                                    data-component-id="{{ $comp->id }}"
                                                    data-type-id="{{ $comp->component_type_id }}"
                                                    data-type-name="{{ e($comp->componentType->name ?? '') }}"
                                                    data-label="{{ e($comp->label ?? '') }}"
                                                    data-values="{{ e(json_encode($comp->values ?? [])) }}"
                                                    data-attrs="{{ e(json_encode($comp->componentType->attributes ?? [])) }}">Edit</button>
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
            .spec-form .spec-form-inner { padding: 1.25rem 0; border-bottom: 1px solid rgba(0,0,0,.06); margin-bottom: 1rem; }
            .spec-form .spec-label { font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 0.35rem; letter-spacing: 0.01em; }
            .spec-form .form-control-sm, .spec-form .form-select-sm { font-size: 0.875rem; border-radius: 0.375rem; }
            .spec-form .spec-dynamic-wrap { padding: 1rem; background: #f8fafc; border-radius: 0.5rem; border: 1px solid #e2e8f0; }
            #modalDuplicateComponent .spec-dynamic-wrap { padding: 1rem; background: #f8fafc; border-radius: 0.5rem; border: 1px solid #e2e8f0; }
            #modalDuplicateComponent .spec-label { font-size: 0.8125rem; font-weight: 600; color: #475569; }
            #modalEditComponent .spec-dynamic-wrap { padding: 1rem; background: #f8fafc; border-radius: 0.5rem; border: 1px solid #e2e8f0; }
            #modalEditComponent .spec-label { font-size: 0.8125rem; font-weight: 600; color: #475569; }
            .spec-form .spec-dynamic-wrap .spec-label { color: #475569; }
            .spec-form-inner .form-control:focus, .spec-form-inner .form-select:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15); }
            .spec-table { font-size: 0.8125rem; table-layout: fixed; width: 100%; min-width: 32rem; }
            .spec-table .spec-th-cb { width: 2.5rem; padding: 0.25rem 0.5rem; }
            .spec-table .spec-th-type { width: 12rem; padding: 0.25rem 0.75rem; }
            .spec-table .spec-th-detail { padding: 0.25rem 0.75rem; }
            .spec-table .spec-th-action { width: 14rem; min-width: 12rem; padding: 0.25rem 0.75rem; }
            .spec-table td { padding: 0.25rem 0.75rem; vertical-align: middle; }
            .spec-table td.spec-detail { word-break: break-word; color: #495057; }
            .spec-table .spec-td-action { white-space: nowrap; }
            .spec-table .spec-td-action .btn { padding: 0.25rem 0.5rem; font-size: 0.75rem; flex-shrink: 0; }
            </style>

            {{-- Modal Duplicate Component --}}
            @if(isset($componentTypes) && $componentTypes->isNotEmpty() && !$server->components->isEmpty())
            <div class="modal fade" id="modalDuplicateComponent" tabindex="-1" aria-labelledby="modalDuplicateComponentLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalDuplicateComponentLabel">Duplikat Spesifikasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <form id="formDuplicateComponent" action="{{ route('server.components.store', $server) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p class="text-muted small mb-3">Data komponen akan diduplikat. Anda bisa mengubah label dan spesifikasi sebelum menyimpan.</p>
                                <input type="hidden" name="component_type_id" id="dup_component_type_id" value="">
                                <div class="mb-3">
                                    <label for="dup_type_display" class="form-label">Tipe</label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="dup_type_display" readonly disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="dup_label" class="form-label">Label <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="dup_label" name="label" required maxlength="100" placeholder="Contoh: Disk 0, RAM 1">
                                </div>
                                <div class="mb-0 spec-dynamic-wrap" id="dup-spec-block" style="display: none;">
                                    <label class="form-label spec-label d-block mb-2">Spesifikasi</label>
                                    <div id="dup-dynamic-attrs" class="row g-2 g-md-3"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Ya, duplikat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- Modal Edit Component --}}
            @if(isset($componentTypes) && $componentTypes->isNotEmpty() && !$server->components->isEmpty())
            <div class="modal fade" id="modalEditComponent" tabindex="-1" aria-labelledby="modalEditComponentLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditComponentLabel">Edit Spesifikasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <form id="formEditComponent" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="edit_type_display" class="form-label">Tipe</label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="edit_type_display" readonly disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_label" class="form-label">Label <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="edit_label" name="label" required maxlength="100" placeholder="Contoh: Disk 0, RAM 1">
                                </div>
                                <div class="mb-0 spec-dynamic-wrap" id="edit-spec-block" style="display: none;">
                                    <label class="form-label spec-label d-block mb-2">Spesifikasi</label>
                                    <div id="edit-dynamic-attrs" class="row g-2 g-md-3"></div>
                                </div>
                            </div>
                        </form>
                        <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <form id="formEditComponentDelete" method="POST" action="" class="d-inline form-delete-component-in-modal">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                            <div class="d-flex gap-2 ms-auto">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" form="formEditComponent" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </section>
@endsection

@section('script-bottom')
@if(isset($componentTypes) && $componentTypes->isNotEmpty())
<script>
window.__serverComponentTypeAttrs = @json($componentTypes->keyBy('id')->map(fn($ct) => $ct->attributes ?? [])->toArray());
@if(isset($server))
window.__serverComponentsBaseUrl = @json(url("account/server/{$server->id}/components"));
@else
window.__serverComponentsBaseUrl = '';
@endif
</script>
@else
<script>window.__serverComponentTypeAttrs = {}; window.__serverComponentsBaseUrl = '';</script>
@endif
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

    var compTypeSelect = document.getElementById('comp_type_id');
    var compDynamicAttrs = document.getElementById('comp-dynamic-attrs');
    if (compTypeSelect && compDynamicAttrs) {
        function renderCompAttrs() {
            var typeId = compTypeSelect.value;
            var specBlock = document.getElementById('comp-spec-block');
            if (!compDynamicAttrs) return;
            compDynamicAttrs.innerHTML = '';
            if (specBlock) specBlock.style.display = 'none';
            if (!typeId) return;
            var attrs = (window.__serverComponentTypeAttrs && window.__serverComponentTypeAttrs[typeId]) || [];
            if (!Array.isArray(attrs)) attrs = [];
            attrs.forEach(function(a) {
                var name = (a && (a.name || a.slug)) || '';
                var slug = (a && a.slug) || (name ? String(name).toLowerCase().replace(/\s+/g, '_') : '');
                if (!slug) return;
                var col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';
                col.innerHTML = '<label class="form-label spec-label">' + (name || slug) + '</label><input type="text" class="form-control form-control-sm" name="attr_' + slug + '" placeholder="' + (name || slug) + '">';
                compDynamicAttrs.appendChild(col);
            });
            if (specBlock && attrs.length > 0) specBlock.style.display = 'block';
        }
        compTypeSelect.addEventListener('change', renderCompAttrs);
        renderCompAttrs();
    }

    var selectAll = document.getElementById('component-select-all');
    var selectAllMob = document.getElementById('component-select-all-mob');
    var checkboxes = document.querySelectorAll('.component-cb');
    var bulkBtns = document.querySelectorAll('.form-bulk-delete-components-btn');
    var bulkForm = document.getElementById('form-bulk-delete-components');
    function updateBulkState() {
        var any = Array.prototype.slice.call(checkboxes).some(function(c) { return c.checked; });
        var all = any && checkboxes.length === Array.prototype.slice.call(checkboxes).filter(function(c) { return c.checked; }).length;
        bulkBtns.forEach(function(btn) { btn.disabled = !any; });
        if (selectAll) selectAll.checked = all;
        if (selectAllMob) selectAllMob.checked = all;
    }
    function setAll(checked) {
        checkboxes.forEach(function(cb) { cb.checked = checked; });
        bulkBtns.forEach(function(btn) { btn.disabled = !checked; });
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
    if (bulkForm && bulkBtns.length) {
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

    var modalDuplicate = document.getElementById('modalDuplicateComponent');
    var formDuplicate = document.getElementById('formDuplicateComponent');
    var dupTypeId = document.getElementById('dup_component_type_id');
    var dupTypeDisplay = document.getElementById('dup_type_display');
    var dupLabel = document.getElementById('dup_label');
    var dupSpecBlock = document.getElementById('dup-spec-block');
    var dupDynamicAttrs = document.getElementById('dup-dynamic-attrs');
    function showDuplicateModal() {
        if (window.bootstrap && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(modalDuplicate) || new bootstrap.Modal(modalDuplicate);
            modal.show();
        } else {
            modalDuplicate.classList.add('show');
            modalDuplicate.style.display = 'block';
            modalDuplicate.setAttribute('aria-modal', 'true');
            document.body.classList.add('modal-open');
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'modalDuplicateBackdrop';
            document.body.appendChild(backdrop);
        }
    }
    function hideDuplicateModal() {
        if (window.bootstrap && bootstrap.Modal) {
            try {
                var modal = bootstrap.Modal.getInstance(modalDuplicate);
                if (modal) modal.hide();
            } catch (e) {}
        } else {
            modalDuplicate.classList.remove('show');
            modalDuplicate.style.display = 'none';
            document.body.classList.remove('modal-open');
            var backdrop = document.getElementById('modalDuplicateBackdrop');
            if (backdrop) backdrop.remove();
        }
    }
    if (modalDuplicate && formDuplicate) {
        document.querySelectorAll('.btn-duplicate-component').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var typeId = this.getAttribute('data-type-id');
                var typeName = this.getAttribute('data-type-name') || '';
                var label = this.getAttribute('data-label') || '';
                var values = {};
                try { values = JSON.parse(this.getAttribute('data-values') || '{}'); } catch (e) {}
                var attrs = (window.__serverComponentTypeAttrs && window.__serverComponentTypeAttrs[typeId]) || [];
                if (!Array.isArray(attrs)) attrs = [];
                if (attrs.length === 0) {
                    try { attrs = JSON.parse(this.getAttribute('data-attrs') || '[]'); } catch (e) { attrs = []; }
                    if (!Array.isArray(attrs)) attrs = [];
                }
                if (dupTypeId) dupTypeId.value = typeId || '';
                if (dupTypeDisplay) dupTypeDisplay.value = typeName;
                if (dupLabel) dupLabel.value = label;
                if (dupDynamicAttrs) dupDynamicAttrs.innerHTML = '';
                if (dupSpecBlock) dupSpecBlock.style.display = attrs.length ? 'block' : 'none';
                attrs.forEach(function(a) {
                    var name = (a && (a.name || a.slug)) || '';
                    var slug = (a && a.slug) || (name ? String(name).toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '') : '');
                    if (!slug) return;
                    var val = values[slug] !== undefined && values[slug] !== null ? values[slug] : (values[slug.replace(/-/g, '_')] ?? values[slug.replace(/_/g, '-')] ?? '');
                    var valStr = val !== undefined && val !== null ? String(val) : '';
                    var safeVal = valStr.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    var col = document.createElement('div');
                    col.className = 'col-12 col-sm-6 col-lg-4';
                    col.innerHTML = '<label class="form-label spec-label">' + (name || slug) + '</label><input type="text" class="form-control form-control-sm" name="attr_' + slug + '" value="' + safeVal + '" placeholder="' + (name || slug) + '">';
                    dupDynamicAttrs.appendChild(col);
                });
                showDuplicateModal();
            });
        });
        modalDuplicate.querySelectorAll('[data-bs-dismiss="modal"], .btn-close').forEach(function(closeBtn) {
            closeBtn.addEventListener('click', function() { hideDuplicateModal(); });
        });
        modalDuplicate.addEventListener('click', function(e) {
            if (e.target === modalDuplicate) hideDuplicateModal();
        });
    }

    var modalEdit = document.getElementById('modalEditComponent');
    var formEdit = document.getElementById('formEditComponent');
    var formEditDelete = document.getElementById('formEditComponentDelete');
    var editTypeDisplay = document.getElementById('edit_type_display');
    var editLabel = document.getElementById('edit_label');
    var editSpecBlock = document.getElementById('edit-spec-block');
    var editDynamicAttrs = document.getElementById('edit-dynamic-attrs');
    function showEditModal() {
        if (modalEdit && window.bootstrap && bootstrap.Modal) {
            var m = bootstrap.Modal.getInstance(modalEdit) || new bootstrap.Modal(modalEdit);
            m.show();
        } else if (modalEdit) {
            modalEdit.classList.add('show');
            modalEdit.style.display = 'block';
            document.body.classList.add('modal-open');
            var back = document.createElement('div');
            back.className = 'modal-backdrop fade show';
            back.id = 'modalEditBackdrop';
            document.body.appendChild(back);
        }
    }
    function hideEditModal() {
        if (modalEdit && window.bootstrap && bootstrap.Modal) {
            try {
                var m = bootstrap.Modal.getInstance(modalEdit);
                if (m) m.hide();
            } catch (e) {}
        } else if (modalEdit) {
            modalEdit.classList.remove('show');
            modalEdit.style.display = 'none';
            document.body.classList.remove('modal-open');
            var back = document.getElementById('modalEditBackdrop');
            if (back) back.remove();
        }
    }
    if (modalEdit && formEdit && formEditDelete) {
        document.querySelectorAll('.btn-edit-component').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var componentId = this.getAttribute('data-component-id');
                var typeId = this.getAttribute('data-type-id');
                var typeName = this.getAttribute('data-type-name') || '';
                var label = this.getAttribute('data-label') || '';
                var values = {};
                try {
                    var rawValues = this.getAttribute('data-values') || '{}';
                    rawValues = rawValues.replace(/&quot;/g, '"').replace(/&amp;/g, '&');
                    values = JSON.parse(rawValues);
                    if (Array.isArray(values)) values = {};
                } catch (e) { values = {}; }
                var attrs = (window.__serverComponentTypeAttrs && (window.__serverComponentTypeAttrs[typeId] || window.__serverComponentTypeAttrs[String(typeId)])) || [];
                if (!Array.isArray(attrs)) attrs = [];
                if (attrs.length === 0) {
                    try { attrs = JSON.parse(this.getAttribute('data-attrs') || '[]'); } catch (e) { attrs = []; }
                    if (!Array.isArray(attrs)) attrs = [];
                }
                var baseUrl = window.__serverComponentsBaseUrl || '';
                if (baseUrl && componentId) {
                    formEdit.action = baseUrl + '/' + componentId;
                    formEditDelete.action = baseUrl + '/' + componentId;
                }
                if (editTypeDisplay) editTypeDisplay.value = typeName;
                if (editLabel) editLabel.value = label;
                if (editDynamicAttrs) editDynamicAttrs.innerHTML = '';
                if (editSpecBlock) editSpecBlock.style.display = attrs.length ? 'block' : 'none';
                attrs.forEach(function(a) {
                    var name = (a && (a.name || a.slug)) || '';
                    var slug = (a && a.slug) || (name ? String(name).toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '') : '');
                    if (!slug) return;
                    var val = values[slug] !== undefined && values[slug] !== null ? values[slug] : (values[slug.replace(/-/g, '_')] ?? values[slug.replace(/_/g, '-')] ?? '');
                    var valStr = val !== undefined && val !== null ? String(val) : '';
                    var safeVal = valStr.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    var col = document.createElement('div');
                    col.className = 'col-12 col-sm-6 col-lg-4';
                    col.innerHTML = '<label class="form-label spec-label">' + (name || slug) + '</label><input type="text" class="form-control form-control-sm" name="attr_' + slug + '" value="' + safeVal + '" placeholder="' + (name || slug) + '">';
                    editDynamicAttrs.appendChild(col);
                });
                showEditModal();
            });
        });
        modalEdit.querySelectorAll('[data-bs-dismiss="modal"], .btn-close').forEach(function(closeBtn) {
            closeBtn.addEventListener('click', function() { hideEditModal(); });
        });
        modalEdit.addEventListener('click', function(e) {
            if (e.target === modalEdit) hideEditModal();
        });
        formEditDelete.addEventListener('submit', function(e) {
            e.preventDefault();
            var f = this;
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'Yakin hapus spesifikasi ini?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, hapus' })
                    .then(function(r) { if (r.isConfirmed) f.submit(); });
            } else {
                if (confirm('Yakin hapus spesifikasi ini?')) f.submit();
            }
        });
    }
});
</script>
@endsection
