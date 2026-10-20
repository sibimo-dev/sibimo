@php $upper = $upper ?? false; @endphp
<div class="section">
    <div class="section-title bold">{{ $title }}</div>
    <table class="dbrows">
        @foreach ($rows as $label => $value)
            <tr>
                <td class="lbl">{{ $label }}</td>
                <td class="sep">:</td>
                <td class="val">{{ filled($value) ? $value : '' }}</td>
            </tr>
        @endforeach
    </table>
</div>
