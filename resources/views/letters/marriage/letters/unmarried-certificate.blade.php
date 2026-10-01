@push('styles')
    .uc { font-size: 11.5pt; }
    .uc table.rows { width: 100%; margin-left: 0; }
    .uc td.lbl { width: 170pt; }
    .uc table.head td { padding: 1px 0; vertical-align: top; }
@endpush
@php $pp = 'letters.marriage.partials.'; $m = $marriage; $b = $m['bride']; $uc = $m['unmarried_certificate']; @endphp
<div class="uc">

<table class="head" style="width:100%">
    <tr>
        <td class="bold" style="width:140pt">KANTOR KALURAHAN</td>
        <td style="width:14pt">:</td>
        <td>BIMOMARTANI</td>
    </tr>
    <tr>
        <td class="bold">KAPANEWON</td>
        <td>:</td>
        <td>NGEMPLAK</td>
    </tr>
    <tr>
        <td class="bold">KABUPATEN/KOTA</td>
        <td>:</td>
        <td>SLEMAN</td>
    </tr>
</table>

<div class="judul u" style="margin-top:18pt">SURAT KETERANGAN BELUM PERNAH MENIKAH</div>
<div class="nomor">Nomor: {{ $uc['letter_number'] }}</div>

<p style="margin-top:14pt">Saya, yang bertanda tangan di bawah ini :</p>
@include($pp.'rows', ['bold' => false, 'rows' => [
    'Nama'    => $signer['name'],
    'Jabatan' => null,
]])

<p style="margin-top:10pt">dengan ini menerangkan bahwa :</p>
@include($pp.'rows', ['bold' => false, 'rows' => [
    'Nama lengkap dan alias'   => $b['name'],
    'NIK'                      => $b['nik'],
    'Tempat dan tanggal lahir' => $b['birth'],
    'Jenis kelamin'            => $b['gender'],
    'Agama'                    => $b['religion'],
    'Pekerjaan'                => $b['occupation'],
    'Tempat tinggal'           => $b['address'],
]])

<p style="margin-top:10pt">pada saat Surat Keterangan Belum Pernah Menikah ini diterbitkan, yang bersangkutan benar-benar BELUM PERNAH MENIKAH.</p>
<p style="margin-top:8pt">Demikianlah surat keterangan ini dibuat untuk dapat digunakan sebagaimana mestinya.</p>

@include($pp.'signature', ['dateLong' => $uc['date'], 'rightTitle' => 'KAMITUWA'])
</div>