@extends('layouts.base', ['title' => 'Tipe Component'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h3 class="mb-0">Tipe Component</h3>
                    <p class="mt-1 mb-0 text-muted">Kelola tipe component global (Disk, RAM, CPU, PSU) beserta atributnya</p>
                </div>
                <a href="{{ route('component-type.create') }}" class="btn btn-primary">+ Tambah Tipe</a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">Nama</th>
                                    <th class="border-0">Slug</th>
                                    <th class="border-0">Atribut</th>
                                    <th class="border-0 text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($componentTypes as $ct)
                                    <tr>
                                        <td class="ps-4 fw-medium">{{ $ct->name }}</td>
                                        <td><code class="small">{{ $ct->slug }}</code></td>
                                        <td>
                                            @if(!empty($ct->attributes))
                                                @foreach($ct->attributes as $attr)
                                                    <span class="badge bg-light text-dark me-1">{{ $attr['name'] ?? $attr['slug'] ?? '' }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">–</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('component-type.edit', $ct) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form action="{{ route('component-type.destroy', $ct) }}" method="POST" class="d-inline form-delete-ct">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">Belum ada tipe. Klik Tambah Tipe (mis. Disk, RAM, CPU, PSU).</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script-bottom')
@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', function() { if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: '#335EEA', timer: 2000 }); });</script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.form-delete-ct').forEach(function(f) {
        f.addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'Yakin hapus tipe ini?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, hapus' }).then(function(r) { if (r.isConfirmed) form.submit(); });
            } else { if (confirm('Yakin hapus?')) form.submit(); }
        });
    });
});
</script>
@endsection
