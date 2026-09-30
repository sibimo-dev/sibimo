@php $upper = $upper ?? true; @endphp
<div class="section">
    <div class="section-title bold">{{ $title }}</div>
    <table class="dbrows">
        @foreach ($rows as $label => $value)
            <tr>
                <td class="lbl">{{ $label }}</td>
                <td class="sep">:</td>
                <td class="val">{{ filled($value) ? ($upper ? mb_strtoupper($value) : $value) : '' }}</td>
            </tr>
        @endforeach
    </table>
</div>