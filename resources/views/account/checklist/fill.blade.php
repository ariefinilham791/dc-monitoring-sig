@extends('layouts.base', ['title' => 'Isi Checklist'])

@section('css')
<style>
.checklist-mobile-bar { left: 0; right: 0; bottom: 0; z-index: 1020; padding-bottom: max(0.75rem, env(safe-area-inset-bottom)); }
.checklist-mobile-spacer { height: 88px; }
.checklist-fill-form .card-body { font-size: 0.875rem; }
.checklist-fill-form .form-label { font-size: 0.75rem; }
.checklist-fill-form .checklist-row-fields { --bs-gutter-x: 0.5rem; --bs-gutter-y: 0.5rem; }
.checklist-status-btns { display: inline-flex; flex-wrap: wrap; gap: 0.25rem; }
.checklist-status-btns .btn { font-size: 0.8125rem; border-width: 1px; }
.checklist-status-btns .btn-status-pending { color: #5a6268; border-color: #ced4da; }
.checklist-status-btns .btn-status-pending:hover { background: #e9ecef; border-color: #adb5bd; }
.checklist-status-btns .btn-check:checked + .btn-status-pending { background: #5a6268; color: #fff; border-color: #5a6268; }
.checklist-status-btns .btn-status-ok { color: #146c43; border-color: #a3cfbb; }
.checklist-status-btns .btn-status-ok:hover { background: #d1e7dd; border-color: #75b798; }
.checklist-status-btns .btn-check:checked + .btn-status-ok { background: #146c43; color: #fff; border-color: #146c43; }
.checklist-status-btns .btn-status-warning { color: #b66200; border-color: #ffc896; }
.checklist-status-btns .btn-status-warning:hover { background: #ffe5d0; border-color: #fd7e14; }
.checklist-status-btns .btn-check:checked + .btn-status-warning { background: #e8590c; color: #fff; border-color: #e8590c; }
.checklist-status-btns .btn-status-error { color: #b02a37; border-color: #f1aeb5; }
.checklist-status-btns .btn-status-error:hover { background: #f8d7da; border-color: #dc3545; }
.checklist-status-btns .btn-check:checked + .btn-status-error { background: #b02a37; color: #fff; border-color: #b02a37; }
</style>
@endsection

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative overflow-hidden bg-gradient2 py-4 px-3 pb-6">
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('checklist.index') }}" class="text-muted text-decoration-none small d-inline-flex align-items-center py-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali ke Checklist
                </a>
                <h3 class="mt-2 mb-1">{{ $server->hostname }}</h3>
                @if($server->os)
                    <p class="text-muted mb-0">{{ $server->os }}</p>
                @endif
                <p class="text-muted mb-0">Periode {{ $round->period_label }}</p>
            </div>

            @if($server->components->isEmpty())
                <div class="alert alert-warning">
                    Server ini belum memiliki komponen (Disk, RAM, CPU, PSU, dll). Tambahkan dulu di <a href="{{ route('server.show', $server) }}">Detail Server</a>.
                </div>
            @else
                <form action="{{ route('checklist.store', $serverRoundCheck) }}" method="POST" id="checklist-form" class="checklist-fill-form">
                    @csrf
                    <div class="row g-2">
                        @foreach($server->components as $comp)
                            @php
                                $item = $itemsByComponent->get((int) $comp->id);
                                $result = old("result.{$comp->id}", $item ? $item->result : 'pending');
                                $usedPct = old("used_pct.{$comp->id}", $item && $item->used_pct !== null ? $item->used_pct : '');
                                $freePct = old("free_pct.{$comp->id}", $item && $item->free_pct !== null ? $item->free_pct : '');
                                $notes = old("notes.{$comp->id}", $item ? $item->notes : '');
                                $typeSlug = strtolower(trim($comp->componentType->slug ?? ''));
                                $isStatusOnly = !empty($comp->componentType->status_only);
                            @endphp
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light border-0 py-2 px-3">
                                        <span class="fw-600 small">{{ $comp->display_name }}</span>@if($comp->spec_summary)<span class="text-muted small"> ({{ $comp->spec_summary }})</span>@endif
                                        @if($isStatusOnly && $typeSlug === 'psu')<span class="text-muted small d-block mt-1">Cek status saja; daya (Watt) dari spesifikasi di atas.</span>@endif
                                        @if($isStatusOnly && in_array($typeSlug, ['disk', 'volume', 'storage']))<span class="text-muted small d-block mt-1">Cek status saja (volume/disk).</span>@endif
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row checklist-row-fields align-items-end">
                                            <div class="col-auto">
                                                <label class="form-label small text-muted mb-1 d-block">Status</label>
                                                <div class="btn-group btn-group-sm checklist-status-btns" role="group">
                                                    @php $labels = \App\Models\ServerRoundCheckItem::resultLabels(); @endphp
                                                    <input type="radio" class="btn-check" name="result[{{ $comp->id }}]" value="pending" id="result-{{ $comp->id }}-pending" {{ $result === 'pending' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-secondary btn-status-pending" for="result-{{ $comp->id }}-pending">{{ $labels['pending'] }}</label>
                                                    <input type="radio" class="btn-check" name="result[{{ $comp->id }}]" value="ok" id="result-{{ $comp->id }}-ok" {{ $result === 'ok' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success btn-status-ok" for="result-{{ $comp->id }}-ok">{{ $labels['ok'] }}</label>
                                                    <input type="radio" class="btn-check" name="result[{{ $comp->id }}]" value="warning" id="result-{{ $comp->id }}-warning" {{ $result === 'warning' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-warning btn-status-warning" for="result-{{ $comp->id }}-warning">{{ $labels['warning'] }}</label>
                                                    <input type="radio" class="btn-check" name="result[{{ $comp->id }}]" value="error" id="result-{{ $comp->id }}-error" {{ $result === 'error' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger btn-status-error" for="result-{{ $comp->id }}-error">{{ $labels['error'] }}</label>
                                                </div>
                                            </div>
                                            @if(!$isStatusOnly)
                                            <div class="col-6 col-sm-4 col-md-2">
                                                <label class="form-label small text-muted mb-1">% Terpakai</label>
                                                <input type="number" name="used_pct[{{ $comp->id }}]" class="form-control form-control-sm" min="0" max="100" step="0.01" placeholder="-" value="{{ $usedPct !== '' && $usedPct !== null ? $usedPct : '' }}" inputmode="decimal">
                                            </div>
                                            <div class="col-6 col-sm-4 col-md-2">
                                                <label class="form-label small text-muted mb-1">% Kosong</label>
                                                <input type="number" name="free_pct[{{ $comp->id }}]" class="form-control form-control-sm" min="0" max="100" step="0.01" placeholder="-" value="{{ $freePct !== '' && $freePct !== null ? $freePct : '' }}" inputmode="decimal">
                                            </div>
                                            @endif
                                            <div class="col min-w-0">
                                                <label class="form-label small text-muted mb-1">Catatan</label>
                                                <input type="text" name="notes[{{ $comp->id }}]" class="form-control form-control-sm" maxlength="1000" placeholder="Opsional" value="{{ $notes }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop: inline buttons --}}
                    <div class="mt-3 d-none d-md-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Checklist</button>
                        <a href="{{ route('checklist.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>

                {{-- Mobile: sticky bottom bar (touch-friendly) --}}
                <div class="d-md-none fixed-bottom bg-white border-top shadow-sm p-2 checklist-mobile-bar">
                    <div class="container">
                        <div class="d-flex gap-2">
                            <a href="{{ route('checklist.index') }}" class="btn btn-outline-secondary flex-grow-1 py-2">Batal</a>
                            <button type="submit" form="checklist-form" class="btn btn-primary flex-grow-1 py-2">Simpan</button>
                        </div>
                    </div>
                </div>
                <div class="d-md-none checklist-mobile-spacer"></div>
            @endif
        </div>
    </section>
@endsection

@section('script-bottom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('checklist-form');
    if (form) {
        var pristine = true;
        form.addEventListener('change', function() { pristine = false; });
        form.addEventListener('input', function() { pristine = false; });
        form.querySelectorAll('.checklist-status-btns .btn').forEach(function(btn) {
            btn.addEventListener('click', function() { pristine = false; });
        });
        window.addEventListener('beforeunload', function(e) {
            if (!pristine) e.preventDefault();
        });
        form.addEventListener('submit', function() { pristine = true; });
    }
});
</script>
@endsection
