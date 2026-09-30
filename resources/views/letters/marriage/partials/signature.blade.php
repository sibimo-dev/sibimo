<table class="sign">
    <tr>
        <td class="sign-l">&nbsp;</td>
        <td class="sign-r" style="text-align:left; padding-left:50pt; padding-right:0; white-space:nowrap">{{ $signature['city'] }}, {{ $dateLong ?? $signature['date_long'] }}</td>
    </tr>
    <tr>
        <td class="sign-l">{{ $leftTitle ?? '' }}</td>
        <td class="sign-r" style="text-align:left; padding-left:50pt; padding-right:0">{{ $rightTitle ?? mb_strtoupper($signer['position']) }}</td>
    </tr>
    <tr><td colspan="2" class="sign-space">&nbsp;</td></tr>
    <tr>
        <td class="sign-l bold">{{ isset($leftName) ? mb_strtoupper($leftName) : '' }}</td>
        <td class="sign-r bold" style="text-align:left; padding-left:50pt; padding-right:0">{{ isset($rightName) ? mb_strtoupper($rightName) : $signer['name'] }}</td>
    </tr>
</table>