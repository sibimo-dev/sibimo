@if (!empty($signature['image']))
    <img
        src="{{ $signature['image'] }}"
        alt="Tanda tangan digital"
        class="signature-image"
        style="display:block; width:auto; max-width:120pt; max-height:48pt; margin:0 auto; object-fit:contain;"
    >
@endif
