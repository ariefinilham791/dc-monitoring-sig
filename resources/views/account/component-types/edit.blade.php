@extends('layouts.base', ['title' => 'Edit Tipe Component'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('component-type.index') }}" class="text-muted text-decoration-none small">← Kembali ke Tipe Component</a>
                <h3 class="mt-2 mb-0">Edit Tipe: {{ $componentType->name }}</h3>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('component-type.update', $componentType) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="name" class="form-label">Nama tipe <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $componentType->name) }}" required maxlength="100">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $componentType->slug) }}" maxlength="100">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Atribut (field dinamis)</label>
                            <div id="attr-rows">
                                @php $attrs = $componentType->attributes ?? []; @endphp
                                @if(count($attrs) > 0)
                                    @foreach($attrs as $a)
                                        <div class="input-group mb-2 attr-row flex-wrap">
                                            <input type="text" class="form-control" name="attr_name[]" value="{{ $a['name'] ?? $a['slug'] ?? '' }}" placeholder="Nama atribut">
                                            <button type="button" class="btn btn-outline-secondary btn-remove-attr flex-shrink-0" title="Hapus">×</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 attr-row flex-wrap">
                                        <input type="text" class="form-control" name="attr_name[]" placeholder="Nama atribut">
                                        <button type="button" class="btn btn-outline-secondary btn-remove-attr flex-shrink-0" title="Hapus">×</button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-attr">+ Tambah atribut</button>
                        </div>
                        <div class="mb-4">
                            <label for="sort_order" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $componentType->sort_order ?? 0) }}" min="0" style="max-width: 120px;">
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
    var addBtn = document.getElementById('btn-add-attr');
    addBtn && addBtn.addEventListener('click', function() {
        var row = document.createElement('div');
        row.className = 'input-group mb-2 attr-row flex-wrap';
        row.innerHTML = '<input type="text" class="form-control" name="attr_name[]" placeholder="Nama atribut">' +
            '<button type="button" class="btn btn-outline-secondary btn-remove-attr flex-shrink-0" title="Hapus">×</button>';
        container.appendChild(row);
        row.querySelector('.btn-remove-attr').addEventListener('click', function() { row.remove(); });
    });
    container && container.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-attr')) e.target.closest('.attr-row').remove();
    });
});
</script>
@endsection
