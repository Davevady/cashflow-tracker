@php
    $icon = $icon ?? '';
    $classBased = is_string($icon) && (str_starts_with($icon, 'flaticon-') || str_starts_with($icon, 'icon-'));
@endphp
@if($classBased)
    <i class="{{ $icon }}"></i>
@else
    {{ $icon }}
@endif


