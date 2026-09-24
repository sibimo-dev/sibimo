{{--
    Surat Tindak Lanjut Permohonan Izin
    Slug disarankan: 'permit-followup-letter' (alias lama: 'surat-tindak-lanjut-izin')

    Field yang dipakai (sudah disediakan LetterPdfService::viewData()/sampleViewData()):
      $number             nomor surat
      $recipient          nama Yth (form: recipient_name)
      $recipient_address  baris "Di :" (form: recipient_address)
      $ref_number         nomor surat saudara yang ditindaklanjuti (form: ref_letter_number)
      $event['day_name']  dihitung otomatis dari event_date
      $event['date']      (form: event_date)
      $event['time']      (form: event_time)
      $event['objective'] dipakai untuk baris "Acara" (form: event_objective)
      $event['place']     (form: event_place)
      $tembusan           array nama tujuan tembusan (form: tembusan), dirender 1/2/3 di bawah
--}}
@extends('letters.layouts.base')

@section('title', 'Surat Tindak Lanjut Permohonan Izin')

@section('extra_css')
    .tli-kepala p { margin-top: 2pt; }
@endsection

@section('content')

    <div class="tli-kepala">
        <p>Nomor : {{ $number }}</p>
        <p>Hal &nbsp;&nbsp;&nbsp;: <i>Menindak Lanjuti Permohonan Izin</i></p>
    </div>

    <p class="gap-top-lg">Kepada</p>
    <p class="gap-top">Yth&nbsp;&nbsp;: {{ $recipient }}</p>
    <p>Di &nbsp;&nbsp;&nbsp;: {{ $recipient_address }}</p>

    <p class="gap-top-lg" style="text-align: justify">
        Yang bertanda tangan dibawah ini Lurah Bimomartani, Kapanewon Ngemplak, Kabupaten
        Sleman, menindak lanjuti surat saudara No. {{ $ref_number ?: '...........' }} Perihal : Permohonan
        Izin. Pada :
    </p>

    <table class="data" style="margin-top: 9pt">
        <tr>
            <td class="lbl">Hari</td>
            <td class="sep">:</td>
            <td class="val">{{ $event['day_name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Tanggal</td>
            <td class="sep">:</td>
            <td class="val">{{ $event['date'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Waktu</td>
            <td class="sep">:</td>
            <td class="val">{{ $event['time'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Acara</td>
            <td class="sep">:</td>
            <td class="val">{{ $event['objective'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Tempat</td>
            <td class="sep">:</td>
            <td class="val">{{ $event['place'] }}</td>
        </tr>
    </table>

    <p class="gap-top-lg" style="text-align: justify">
        Sehubungan dengan hal tersebut, bahwasanya Pemerintah Kalurahan Bimomartani tidak
        keberatan / mengizinkan.
    </p>

    <p class="gap-top">
        Demikian kami sampaikan atas perhatiannya kami haturkan terima kasih.
    </p>

@endsection

{{-- Tembusan tetap ditampilkan 1/2/3 walau $tembusan kosong, supaya form selalu
     punya 3 baris untuk diisi (sama seperti pola $witnesses di surat gugat cerai).
     Nomor ditulis manual di kolom "num" (lihat catatan di base.blade.php soal
     kenapa bukan <ol>/<li>). --}}
@section('tembusan')
    @php $tembusanRows = array_pad(array_slice($tembusan, 0, 3), 3, null); @endphp
    @foreach ($tembusanRows as $i => $t)
        <tr>
            <td class="num">{{ $i + 1 }}.</td>
            <td>{{ $t }}</td>
        </tr>
    @endforeach
@endsection