<table class="sign">
    <tr>
        <td class="sign-l">&nbsp;</td>
        <td class="sign-r" style="text-align:left; padding-left:50pt; padding-right:0; white-space:nowrap">{{ $signature['city'] }}, {{ $dateLong ?? $signature['date_long'] }}</td>
    </tr>
    <tr>
        <td class="sign-l">{{ $leftTitle ?? '' }}</td>
        <td class="sign-r" style="text-align:left; padding-left:50pt; padding-right:0">{{ $rightTitle ?? $signer['position'] }}</td>
    </tr>
    @if (!empty($signature['image']))
        <tr>
            <td class="sign-l">&nbsp;</td>
            <td class="sign-r" style="text-align:left; padding-left:50pt; padding-right:0">
                <img class="ttd-image" src="{{ $signature['image'] }}" alt="Tanda Tangan" style="max-height: 44pt; max-width: 120pt; display: block;">
            </td>
        </tr>
    @else
        <tr><td colspan="2" class="sign-space">&nbsp;</td></tr>
    @endif
    <tr>
        <td class="sign-l bold">{{ isset($leftName) ? $leftName : '' }}</td>
        <td class="sign-r bold" style="text-align:left; padding-left:50pt; padding-right:0">{{ isset($rightName) ? $rightName : $signer['name'] }}</td>
    </tr>
</table>
