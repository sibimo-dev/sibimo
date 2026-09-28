@php
    $marker = $marker ?? 'number';
    $start  = $start ?? 1;
    $bold   = $bold ?? true;
@endphp
<table class="rows">
    @foreach ($rows as $label => $value)
        <tr>
            @isset($tag)<td class="tag">{{ $loop->first ? $tag : '' }}</td>@endisset
            <td class="no">@if ($marker === 'number'){{ $start + $loop->index }}.@elseif ($marker === 'bullet')&bull;@endif</td>
            <td class="lbl {{ $bold && $loop->first ? 'bold' : '' }}">{{ $label }}</td>
            <td class="sep">:</td>
            <td class="val {{ $bold && $loop->first ? 'bold' : '' }}">{{ filled($value) ? mb_strtoupper($value) : '' }}</td>
        </tr>
    @endforeach
</table>