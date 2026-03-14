<!-- meta -->
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- title -->
<title>{{ $title ?? 'Prompt' }}</title>
<meta content="Coderthemes" name="author" />
<meta content="A fully featured multi purpose template" name="description" />

<!-- theme favicon -->
<link rel="shortcut icon" href="/images/favicon.png">

@php
    $mapboxToken = config('services.mapbox.access_token');
@endphp
@if($mapboxToken)
<script>window.MAPBOX_ACCESS_TOKEN = @json($mapboxToken);</script>
@endif
