@push('styles')
    table.pt { border-collapse: collapse; }
    table.pt td { padding: 0 0 3pt 0; vertical-align: top; }
    table.pt td.pn { width: 18pt; }
@endpush
@php $pp = 'letters.marriage.partials.'; $m = $marriage; $b = $m['bride']; @endphp
<div class="judul u">SURAT PERNYATAAN BELUM MENIKAH LAGI</div>
<p style="margin-top:22pt">Yang bertanda tangan di bawah ini, saya :</p>
<div style="margin-top:6pt">
@include($pp.'rows', ['bold' => false, 'rows' => [
    'Nama'                     => $b['name'],
    'NIK'                      => $b['nik'],
    'Jenis Kelamin'            => $b['gender'],
    'Tempat dan tanggal lahir' => $b['birth'],
    'Warganegara'              => $b['citizenship'],
    'Agama'                    => $b['religion'],
    'Pekerjaan'                => $b['occupation'],
    'Pendidikan Terakhir'      => $b['education'],
    'Alamat'                   => $b['address'],
    'Status Perkawinan'        => $b['status'],
]])
</div>

<p style="margin-top:16pt">dengan ini menyatakan dengan sebenar-benarnya bahwa:</p>
<table class="pt" style="margin-top:4pt">
    @foreach ([
        'saya belum menikah lagi;',
        'kebenaran materi/isi surat pernyataan ini sepenuhnya menjadi tanggung jawab saya;',
        'apabila di kemudian hari ternyata pernyataan saya tidak benar, maka saya bersedia diproses secara hukum sesuai ketentuan peraturan perundang-undangan yang berlaku.',
    ] as $i => $t)
        <tr><td class="pn">{{ $i + 1 }}.</td><td>{{ $t }}</td></tr>
    @endforeach
</table>

<p style="margin-top:16pt">Demikian surat pernyataan ini dibuat untuk dapat digunakan sebagaimana mestinya.</p>

<table class="sign" style="margin-top:22pt">
    <tr>
        <td class="sign-l">NO: {{ $m['letter_number'] }}</td>
        <td class="sign-r">&nbsp;</td>
    </tr>
    <tr>
        <td class="sign-l">TGL: {{ $signature['date'] }}</td>
        <td class="sign-r" style="text-align:left; padding-left:50pt; padding-right:0; white-space:nowrap">{{ $signature['city'] }}, {{ $signature['date_long'] }}</td>
    </tr>
    <tr>
        <td class="sign-l">{{ mb_strtoupper($signer['position'] ?? '') }}</td>
        <td class="sign-r" style="text-align:left; padding-left:50pt; padding-right:0">Yang Menyatakan</td>
    </tr>
    <tr><td colspan="2" class="sign-space">&nbsp;</td></tr>
    <tr>
        <td class="sign-l bold">{{ $signer['name'] }}</td>
        <td class="sign-r bold" style="text-align:left; padding-left:50pt; padding-right:0">{{ mb_strtoupper($b['name'] ?? '') }}</td>
    </tr>
</table>