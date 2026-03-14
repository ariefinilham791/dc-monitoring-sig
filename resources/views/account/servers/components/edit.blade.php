@extends('layouts.base', ['title' => 'Edit Spesifikasi'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('server.show', $server) }}" class="text-muted text-decoration-none small">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="align-text-bottom me-1"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali ke {{ $server->hostname }}
                </a>
                <h3 class="mt-2 mb-0">Edit Spesifikasi</h3>
                <p class="text-muted small mb-0">{{ $server_component->componentType->name ?? 'Component' }} — {{ $server_component->label ?? '' }}</p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('server.components.update', [$server, $server_component]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label text-muted small">Tipe</label>
                                <input type="text" class="form-control form-control-sm bg-light" value="{{ $server_component->componentType->name ?? '–' }}" readonly disabled>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $server_component->label ?? '') }}" required maxlength="100" placeholder="Disk 0, RAM 1">
                                @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        @php
                            $attrs = $server_component->componentType->attributes ?? [];
                            $values = $server_component->values ?? [];
                        @endphp
                        @if(count($attrs) > 0)
                            <hr class="my-3">
                            <label class="form-label">Spesifikasi</label>
                            <div class="row g-3">
                                @foreach($attrs as $attr)
                                    @php
                                        $slug = $attr['slug'] ?? \Illuminate\Support\Str::slug($attr['name'] ?? '');
                                        $name = $attr['name'] ?? $slug;
                                        $val = $values[$slug] ?? '';
                                    @endphp
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <label for="attr_{{ $slug }}" class="form-label small">{{ $name }}</label>
                                        <input type="text" class="form-control form-control-sm" id="attr_{{ $slug }}" name="attr_{{ $slug }}" value="{{ old('attr_' . $slug, $val) }}" placeholder="{{ $name }}">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('server.show', $server) }}" class="btn btn-outline-secondary">Batal</a>
                            <form action="{{ route('server.components.destroy', [$server, $server_component]) }}" method="POST" class="d-inline ms-auto form-delete-component">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script-bottom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.form-delete-component').forEach(function(f) {
        f.addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'Yakin hapus spesifikasi ini?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, hapus' }).then(function(r) { if (r.isConfirmed) form.submit(); });
            } else { if (confirm('Yakin hapus?')) form.submit(); }
        });
    });
});
</script>
@endsection
