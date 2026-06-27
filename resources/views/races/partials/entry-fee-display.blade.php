@php
    $categories = $edition?->entryCategories ?? collect();
@endphp

@if($categories->isNotEmpty())
    @foreach($categories as $cat)
        @php
            $kmStr = rtrim(rtrim(number_format((float) $cat->distance_km, 3, '.', ''), '0'), '.');
        @endphp
        <div @if(! $loop->first) style="margin-top:0.25rem;" @endif>
            {{ $cat->name }} · {{ $kmStr }}km · {{ number_format($cat->entry_fee) }}원
        </div>
    @endforeach
@elseif(filled($edition?->entry_fee) && is_numeric($edition->entry_fee))
    {{ number_format((int) $edition->entry_fee) }}원~
@elseif(filled($edition?->entry_fee))
    {{ $edition->entry_fee }}
@else
    -
@endif
