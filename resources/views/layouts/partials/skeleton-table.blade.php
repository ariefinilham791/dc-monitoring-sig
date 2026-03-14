{{-- Skeleton placeholder for a table (e.g. 5 rows). Use with @include when needed. --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    @for($c = 0; $c < ($cols ?? 4); $c++)
                        <th class="border-0"><div class="skeleton skeleton-text" style="width: 80%;"></div></th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @for($r = 0; $r < ($rows ?? 5); $r++)
                    <tr>
                        @for($c = 0; $c < ($cols ?? 4); $c++)
                            <td><div class="skeleton skeleton-text"></div></td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
