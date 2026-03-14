@extends('layouts.base', ['title' => 'Isi Checklist'])

@section('css')
<style>
.checklist-mobile-bar { left: 0; right: 0; bottom: 0; z-index: 1020; padding-bottom: max(0.75rem, env(safe-area-inset-bottom)); }
.checklist-mobile-spacer { height: 88px; }
.checklist-fill-form .card-body { font-size: 0.875rem; }
.checklist-fill-form .form-label { font-size: 0.75rem; }
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
                                $item = $itemsByComponent->get($comp->id);
                                $result = old("result.{$comp->id}", $item ? $item->result : 'pending');
                                $usedPct = old("used_pct.{$comp->id}", $item && $item->used_pct !== null ? $item->used_pct : '');
                                $freePct = old("free_pct.{$comp->id}", $item && $item->free_pct !== null ? $item->free_pct : '');
                                $notes = old("notes.{$comp->id}", $item ? $item->notes : '');
                            @endphp
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light border-0 py-2 px-3">
                                        <span class="fw-600 small">{{ $comp->display_name }}</span>@if($comp->spec_summary)<span class="text-muted small"> ({{ $comp->spec_summary }})</span>@endif
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-12 col-sm-6 col-md-4">
                                                <label class="form-label small text-muted mb-0">Hasil</label>
                                                <select name="result[{{ $comp->id }}]" class="form-select form-select-sm" required>
                                                    @foreach(\App\Models\ServerRoundCheckItem::resultLabels() as $val => $label)
                                                        <option value="{{ $val }}" {{ $result === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6 col-sm-3 col-md-2">
                                                <label class="form-label small text-muted mb-0">% Terpakai</label>
                                                <input type="number" name="used_pct[{{ $comp->id }}]" class="form-control form-control-sm" min="0" max="100" step="0.01" placeholder="-" value="{{ $usedPct !== '' && $usedPct !== null ? $usedPct : '' }}" inputmode="decimal">
                                            </div>
                                            <div class="col-6 col-sm-3 col-md-2">
                                                <label class="form-label small text-muted mb-0">% Kosong</label>
                                                <input type="number" name="free_pct[{{ $comp->id }}]" class="form-control form-control-sm" min="0" max="100" step="0.01" placeholder="-" value="{{ $freePct !== '' && $freePct !== null ? $freePct : '' }}" inputmode="decimal">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label class="form-label small text-muted mb-0">Catatan</label>
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
