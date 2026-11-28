@if (!empty($signature['image']) || !empty($signature['stamp']))
    <div style="position:relative; width:170pt; height:64pt; margin:0 auto;">
        @if (!empty($signature['image']))
            <img
                src="{{ $signature['image'] }}"
                alt="Tanda tangan digital"
                class="signature-image"
                style="position:absolute; left:0; top:0; display:block; width:170pt; height:64pt; object-fit:contain;"
            >
        @endif
        @if (!empty($signature['stamp']))
            <img
                src="{{ $signature['stamp'] }}"
                alt="Stempel kalurahan"
                style="position:absolute; left:4pt; top:0; display:block; width:auto; max-width:76pt; max-height:62pt; margin:0; object-fit:contain; opacity:.82;"
            >
        @endif
    </div>
@endif

