{{-- Skeleton placeholder for card grids (e.g. while content would load). Use with @include when needed. --}}
<div class="row g-3 skeleton-cards">
    @for($i = 0; $i < ($count ?? 4); $i++)
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="skeleton skeleton-text mb-2" style="width: 60%;"></div>
                    <div class="skeleton skeleton-title"></div>
                </div>
            </div>
        </div>
    @endfor
</div>
