@extends('layouts.base', ['title' => 'Tambah Tipe Component'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('component-type.index') }}" class="text-muted text-decoration-none small">Kembali ke Tipe Component</a>
                <h3 class="mt-2 mb-0">Tambah Tipe Component</h3>
                <p class="text-muted small mb-0">Contoh: Disk, RAM, CPU, PSU. Setiap tipe punya atribut dinamis (Size, Serial Number, dll).</p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('component-type.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="name" class="form-label">Nama tipe *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Disk, RAM, CPU, PSU">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="slug" class="form-label">Slug (opsional)</label>
                                <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}" maxlength="100" placeholder="disk, ram">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Atribut (field dinamis)</label>
                            <p class="text-muted small">Nama field yang muncul saat input component: Size, Serial Number, Capacity, dll.</p>
                            <div id="attr-rows">
                                <div class="input-group mb-2 attr-row flex-wrap">
                                    <input type="text" class="form-control" name="attr_name[]" placeholder="Mis. Size">
                                    <button type="button" class="btn btn-outline-secondary btn-remove-attr flex-shrink-0">Hapus</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="btn-add-attr">+ Tambah atribut</button>
                        </div>
                        <div class="mb-4">
                            <label for="sort_order" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" style="max-width: 120px;">
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('component-type.index') }}" class="btn btn-outline-secondary">Batal</a>
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
    var container = document.getElementById('attr-rows');
    document.getElementById('btn-add-attr').addEventListener('click', function() {
        var row = document.createElement('div');
        row.className = 'input-group mb-2 attr-row flex-wrap';
        row.innerHTML = '<input type="text" class="form-control" name="attr_name[]" placeholder="Nama atribut">' +
            '<button type="button" class="btn btn-outline-secondary btn-remove-attr flex-shrink-0">Hapus</button>';
        container.appendChild(row);
        row.querySelector('.btn-remove-attr').onclick = function() { row.remove(); };
    });
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-attr')) e.target.closest('.attr-row').remove();
    });
});
</script>
@endsection
