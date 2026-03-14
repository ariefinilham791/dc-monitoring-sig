@extends('layouts.base', ['title' => 'Prompt - History Checklist'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('checklist.index') }}" class="text-muted text-decoration-none small">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="align-text-bottom me-1"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali ke Checklist
                </a>
                <h3 class="mt-2 mb-0">History Checklist</h3>
                <p class="text-muted mt-1 mb-0">Periode-periode bulan sebelumnya</p>
            </div>

            <div class="d-none d-md-block card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">Periode</th>
                                    <th class="border-0">Jumlah server dalam round</th>
                                    <th class="border-0 text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rounds as $r)
                                    <tr>
                                        <td class="ps-4 fw-medium">{{ $r->period_label }}</td>
                                        <td>{{ $r->server_round_checks_count }} server</td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('checklist.log', ['round_id' => $r->id]) }}" class="btn btn-sm btn-outline-primary">Lihat Log</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-5">Belum ada history.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-md-none">
                @forelse($rounds as $r)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-600">{{ $r->period_label }}</h6>
                                <p class="text-muted small mb-0 mt-1">{{ $r->server_round_checks_count }} server</p>
                            </div>
                            <a href="{{ route('checklist.log', ['round_id' => $r->id]) }}" class="btn btn-primary">Lihat Log</a>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center text-muted py-5">Belum ada history.</div>
                    </div>
                @endforelse
            </div>

            @if($rounds->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $rounds->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
