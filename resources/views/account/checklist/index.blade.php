@extends('layouts.base', ['title' => 'Checklist'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h3 class="mb-0">Checklist Bulanan</h3>
                    <p class="mt-1 mb-0 text-muted">Periode {{ $round->period_label }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('checklist.history') }}" class="btn btn-outline-secondary">History</a>
                    <a href="{{ route('checklist.log') }}" class="btn btn-outline-primary">Log Gabungan</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Perlu dicek</p>
                            <h4 class="mb-0">{{ $pending }}/{{ $totalServers }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Sudah dicek</p>
                            <h4 class="mb-0 text-success">{{ $completed }}/{{ $totalServers }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1">OK</p>
                            <h4 class="mb-0 text-success">{{ $okCount }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Warning</p>
                            <h4 class="mb-0 text-warning">{{ $warningCount }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Error</p>
                            <h4 class="mb-0 text-danger">{{ $errorCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desktop: Tabel --}}
            <div class="card border-0 shadow-sm d-none d-md-block">
                <div class="card-header bg-white border-0 border-bottom py-3 px-4">
                    <h5 class="mb-0 fw-600">Daftar Server - {{ $round->period_label }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">Hostname</th>
                                    <th class="border-0">OS / Fungsi</th>
                                    <th class="border-0">IP</th>
                                    <th class="border-0">Status Checklist</th>
                                    <th class="border-0 text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($servers as $server)
                                    @php
                                        $rc = $server->currentRoundCheck;
                                        $isCompleted = $rc && $rc->status === \App\Models\ServerRoundCheck::STATUS_COMPLETED;
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-medium">{{ $server->hostname }}</td>
                                        <td>{{ $server->os ?? '–' }}</td>
                                        <td><code class="small">{{ $server->ip_address ?? '-' }}</code></td>
                                        <td>
                                            @if($isCompleted)
                                                <span class="badge bg-success">Sudah dicek</span>
                                                @if($rc->completed_at)
                                                    <span class="text-muted small ms-1">{{ $rc->completed_at->format('d M H:i') }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Perlu dicek</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('checklist.fill', $rc) }}" class="btn btn-sm {{ $isCompleted ? 'btn-outline-primary' : 'btn-primary' }}">
                                                {{ $isCompleted ? 'Lihat / Edit' : 'Isi Checklist' }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">Belum ada server. Tambah server dari menu Server.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Mobile: Card list --}}
            <div class="d-md-none">
                <h5 class="fw-600 mb-3">Daftar Server - {{ $round->period_label }}</h5>
                @forelse($servers as $server)
                    @php
                        $rc = $server->currentRoundCheck;
                        $isCompleted = $rc && $rc->status === \App\Models\ServerRoundCheck::STATUS_COMPLETED;
                    @endphp
                    <div class="card border-0 shadow-sm mb-3 checklist-server-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0 fw-semibold">{{ $server->hostname }}</h6>
                                @if($isCompleted)
                                    <span class="badge bg-success">Sudah dicek</span>
                                @else
                                    <span class="badge bg-secondary">Perlu dicek</span>
                                @endif
                            </div>
                            @if($server->os)
                                <p class="text-muted small mb-1">{{ $server->os }}</p>
                            @endif
                            <p class="text-muted small mb-2"><code>{{ $server->ip_address ?? '-' }}</code></p>
                            @if($isCompleted && $rc->completed_at)
                                <p class="text-muted small mb-3">{{ $rc->completed_at->format('d M Y H:i') }}</p>
                            @endif
                            <a href="{{ route('checklist.fill', $rc) }}" class="btn {{ $isCompleted ? 'btn-outline-primary' : 'btn-primary' }} w-100 py-2">
                                {{ $isCompleted ? 'Lihat / Edit' : 'Isi Checklist' }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center text-muted py-5">
                            Belum ada server. Tambah server dari menu Server.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
