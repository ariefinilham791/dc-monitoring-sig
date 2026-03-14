@extends('layouts.base', ['title' => 'Prompt - Log Checklist'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3">
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('checklist.index') }}" class="text-muted text-decoration-none small">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="align-text-bottom me-1"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali ke Checklist
                </a>
                <h3 class="mt-2 mb-3">Log Checklist</h3>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3 px-4">
                        <form method="get" action="{{ route('checklist.log') }}" class="row g-2 align-items-end">
                            <div class="col-auto flex-grow-1" style="min-width: 180px;">
                                <label for="log-round-id" class="form-label small text-muted mb-1">Periode</label>
                                <select name="round_id" id="log-round-id" class="form-select" onchange="this.form.submit()">
                                    @foreach($rounds as $r)
                                        <option value="{{ $r->id }}" {{ $round && $round->id === $r->id ? 'selected' : '' }}>{{ $r->period_label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($round)
                                <div class="col-auto">
                                    <label class="form-label small text-muted mb-1 d-block">&nbsp;</label>
                                    <a href="{{ route('checklist.log.export', ['round_id' => $round->id]) }}" class="btn btn-success">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1 align-middle"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        Export Excel
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

                @if($round)
                    <p class="text-muted small mb-0">Hasil gabungan periode <strong>{{ $round->period_label }}</strong></p>
                @endif
            </div>

            @if($round && $round->serverRoundChecks->isNotEmpty())
                <div class="row g-3">
                    @foreach($round->serverRoundChecks as $src)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm log-card">
                                <div class="card-header bg-light border-0 py-2 px-3 d-flex flex-wrap align-items-center gap-2">
                                    <strong class="small">{{ $src->server->hostname }}</strong>
                                    @if($src->server->os)
                                        <span class="text-muted small">{{ $src->server->os }}</span>
                                    @endif
                                    <code class="small">{{ $src->server->ip_address ?? '-' }}</code>
                                    @if($src->status === \App\Models\ServerRoundCheck::STATUS_COMPLETED && $src->completed_at)
                                        <span class="badge bg-success">Selesai {{ $src->completed_at->format('d M Y H:i') }}</span>
                                    @else
                                        <span class="badge bg-secondary">Belum selesai</span>
                                    @endif
                                    <a href="{{ route('checklist.fill', $src) }}" class="btn btn-sm btn-outline-primary ms-auto">Edit</a>
                                </div>
                                <div class="card-body p-0">
                                    @if($src->checkItems->isEmpty())
                                        <p class="text-muted small mb-0 p-4">Belum ada item checklist.</p>
                                    @else
                                        {{-- Desktop: table compact --}}
                                        <div class="d-none d-md-block table-responsive log-checklist-table-wrap">
                                            <table class="table table-sm table-bordered mb-0 log-checklist-table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Komponen</th>
                                                        <th>Hasil</th>
                                                        <th>% Terpakai</th>
                                                        <th>% Kosong</th>
                                                        <th>Catatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($src->checkItems as $item)
                                                        @php
                                                            $badge = match($item->result) {
                                                                'ok' => 'success',
                                                                'warning' => 'warning',
                                                                'error' => 'danger',
                                                                default => 'secondary'
                                                            };
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <span class="d-block">{{ $item->serverComponent->display_name ?? '#' . $item->server_component_id }}</span>
                                                                @if($item->serverComponent && $item->serverComponent->spec_summary)
                                                                    <span class="text-muted small">{{ $item->serverComponent->spec_summary }}</span>
                                                                @endif
                                                            </td>
                                                            <td><span class="badge bg-{{ $badge }}">{{ \App\Models\ServerRoundCheckItem::resultLabels()[$item->result] ?? $item->result }}</span></td>
                                                            <td>{{ $item->used_pct !== null ? $item->used_pct . '%' : '-' }}</td>
                                                            <td>{{ $item->free_pct !== null ? $item->free_pct . '%' : '-' }}</td>
                                                            <td>{{ $item->notes ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        {{-- Mobile: card list --}}
                                        <div class="d-md-none">
                                            @foreach($src->checkItems as $item)
                                                @php
                                                    $badge = match($item->result) {
                                                        'ok' => 'success',
                                                        'warning' => 'warning',
                                                        'error' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <div class="border-bottom px-4 py-3">
                                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                                        <div>
                                                            <span class="fw-medium d-block">{{ $item->serverComponent->display_name ?? '#' . $item->server_component_id }}</span>
                                                            @if($item->serverComponent && $item->serverComponent->spec_summary)
                                                                <span class="text-muted small">{{ $item->serverComponent->spec_summary }}</span>
                                                            @endif
                                                        </div>
                                                        <span class="badge bg-{{ $badge }}">{{ \App\Models\ServerRoundCheckItem::resultLabels()[$item->result] ?? $item->result }}</span>
                                                    </div>
                                                    <div class="small text-muted mt-1">
                                                        Terpakai: {{ $item->used_pct !== null ? $item->used_pct . '%' : '-' }} &middot; Kosong: {{ $item->free_pct !== null ? $item->free_pct . '%' : '-' }}
                                                    </div>
                                                    @if($item->notes)
                                                        <p class="small mb-0 mt-1">{{ $item->notes }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        @if($round)
                            Belum ada data checklist untuk periode {{ $round->period_label }}.
                        @else
                            Pilih periode di dropdown atau belum ada round.
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>

    <style>
    .log-card .card-header { font-size: 0.875rem; }
    .log-checklist-table { font-size: 0.8125rem; }
    .log-checklist-table th,
    .log-checklist-table td { padding: 0.35rem 0.5rem; vertical-align: middle; }
    .log-checklist-table .badge { font-size: 0.7rem; }
    </style>
@endsection
