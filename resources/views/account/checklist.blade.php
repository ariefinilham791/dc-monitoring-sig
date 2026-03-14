@extends('layouts.base', ['title' => 'Prompt - Checklist'])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <!-- page-content start -->
    <section class="position-relative overflow-hidden bg-gradient2 py-3 px-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="mb-0">Checklist</h3>
                        <p class="mt-1 fw-medium">Manage your checklists</p>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-0">Konten checklist bisa ditambahkan di sini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- page-content end -->

@endsection
