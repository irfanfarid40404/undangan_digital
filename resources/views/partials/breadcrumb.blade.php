@php
    $items = $items ?? [];
@endphp
@if(count($items))
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            @foreach($items as $i => $crumb)
                @if(!empty($crumb['url']) && $i < count($items) - 1)
                    <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
