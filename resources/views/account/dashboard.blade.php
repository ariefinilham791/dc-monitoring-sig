@extends('layouts.base', ['title' => 'Prompt - Home'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="mb-4">
                <h3 class="mb-0">Dashboard</h3>
                <p class="mt-1 fw-medium text-muted">Ringkasan server dan checklist bulanan</p>
            </div>

            {{-- Summary cards --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Total Server</p>
                            <h4 class="mb-0">{{ $totalServers }}</h4>
                            <a href="{{ route('server.index') }}" class="small text-primary text-decoration-none mt-1 d-inline-block">Lihat server</a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Lokasi</p>
                            <h4 class="mb-0">{{ $totalLocations }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Tipe Komponen</p>
                            <h4 class="mb-0">{{ $totalComponentTypes }}</h4>
                            <a href="{{ route('component-type.index') }}" class="small text-primary text-decoration-none mt-1 d-inline-block">Kelola tipe</a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1">Checklist {{ $round->period_label }}</p>
                            <h4 class="mb-0">{{ $completed }}/{{ $totalInRound }}</h4>
                            <a href="{{ route('checklist.index') }}" class="small text-primary text-decoration-none mt-1 d-inline-block">Buka checklist</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts row --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 border-bottom py-3 px-4">
                            <h5 class="mb-0 fw-600">Checklist {{ $round->period_label }}</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 260px;">
                            <canvas id="chartChecklistProgress" width="250" height="250"></canvas>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0 px-4 pb-4">
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Perlu dicek: <strong>{{ $pending }}</strong></span>
                                <span class="text-success">Sudah: <strong>{{ $completed }}</strong></span>
                            </div>
                            <a href="{{ route('checklist.index') }}" class="btn btn-sm btn-outline-primary w-100 mt-2">Lihat Checklist</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 border-bottom py-3 px-4">
                            <h5 class="mb-0 fw-600">Hasil Pengecekan</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 260px;">
                            <canvas id="chartChecklistResult" width="250" height="250"></canvas>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0 px-4 pb-4">
                            <div class="d-flex flex-wrap gap-2 small justify-content-center">
                                <span class="text-success">OK: {{ $okCount }}</span>
                                <span class="text-warning">Warning: {{ $warningCount }}</span>
                                <span class="text-danger">Error: {{ $errorCount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 border-bottom py-3 px-4">
                            <h5 class="mb-0 fw-600">Server per Tipe</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 260px;">
                            <canvas id="chartServerType" width="250" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Server yang perlu dicek --}}
            @if($pendingChecks->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h5 class="mb-0 fw-600">Perlu dicek bulan ini</h5>
                        <a href="{{ route('checklist.index') }}" class="btn btn-sm btn-outline-primary">Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($pendingChecks as $rc)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-medium">{{ $rc->server->hostname }}</span>
                                    <a href="{{ route('checklist.fill', $rc) }}" class="btn btn-sm btn-primary">Isi Checklist</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endsection

@section('script-bottom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartOpt = { responsive: true, maintainAspectRatio: true };

    // 1. Checklist progress (donut): Perlu vs Sudah
    new Chart(document.getElementById('chartChecklistProgress'), {
        type: 'doughnut',
        data: {
            labels: ['Perlu dicek', 'Sudah dicek'],
            datasets: [{
                data: [{{ $pending }}, {{ $completed }}],
                backgroundColor: ['#6c757d', '#198754'],
                borderWidth: 0
            }]
        },
        options: {
            ...chartOpt,
            cutout: '60%',
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 2. Hasil pengecekan (donut): OK, Warning, Error
    var resultData = [{{ $okCount }}, {{ $warningCount }}, {{ $errorCount }}];
    var resultSum = resultData.reduce((a,b) => a+b, 0);
    var resultLabels = ['OK', 'Warning', 'Error'];
    var resultColors = ['#198754', '#ffc107', '#dc3545'];
    if (resultSum === 0) { resultData = [1]; resultLabels = ['Belum ada data']; resultColors = ['#dee2e6']; }
    new Chart(document.getElementById('chartChecklistResult'), {
        type: 'doughnut',
        data: {
            labels: resultLabels,
            datasets: [{
                data: resultData,
                backgroundColor: resultColors,
                borderWidth: 0
            }]
        },
        options: {
            ...chartOpt,
            cutout: '60%',
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 3. Server per tipe (donut)
    new Chart(document.getElementById('chartServerType'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($serversByType->pluck('label')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($serversByType->pluck('count')->toArray()) !!},
                backgroundColor: ['#0d6efd', '#0dcaf0', '#6f42c1'],
                borderWidth: 0
            }]
        },
        options: {
            ...chartOpt,
            cutout: '60%',
            plugins: { legend: { position: 'bottom' } }
        }
    });

});
</script>
@endsection
