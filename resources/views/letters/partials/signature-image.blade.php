@if (!empty($signature['image']) || !empty($signature['stamp']))
    <div style="position:relative; display:inline-block; vertical-align:top; width:170pt; height:64pt; margin:0;">
        @if (!empty($signature['image']))
            <img
                src="{{ $signature['image'] }}"
                alt="Tanda tangan digital"
                class="signature-image"
                style="position:absolute; z-index:1; left:0; top:0; display:block; width:170pt; height:64pt; object-fit:contain;"
            >
        @endif
        @if (!empty($signature['stamp']))
            <img
                src="{{ $signature['stamp'] }}"
                alt="Stempel kalurahan"
                style="position:absolute; z-index:2; left:-18pt; top:-8pt; display:block; width:auto; max-width:100pt; max-height:88pt; margin:0; object-fit:contain; opacity:.82;"
            >
        @endif
    </div>
@endif

