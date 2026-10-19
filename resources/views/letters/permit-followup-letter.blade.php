{{--
    Surat Tindak Lanjut Permohonan Izin
    Slug disarankan: 'permit-followup-letter' (alias lama: 'surat-tindak-lanjut-izin')

    Field yang dipakai (disediakan LetterPdfService::viewData()/sampleViewData()):
      $number               nomor surat
      $recipient            Yth. — TEKS BEBAS BANYAK BARIS (dipisah Enter), tiap baris dicetak sendiri (form: recipientName)
      $recipient_address    baris "Di :" (form: recipientAddress)
      $ref_number           nomor surat saudara yang ditindaklanjuti (form: refLetterNumber)
      $event['day_name']    hari mulai, dihitung otomatis dari event_date
      $event['end_day_name']hari selesai (opsional), dihitung otomatis dari event_end_date
      $event['date_text']   tanggal mulai, sudah diformat "27 Agustus 2026" (cadangan: $event['date'] mentah)
      $event['end_date_text'] tanggal selesai (opsional, format sama; cadangan: $event['end_date'] mentah)
      $event['time']        waktu mulai
      $event['end_time']    waktu selesai (opsional, teks bebas mis. "18.00 WIB" / "Selesai")
      $event['objective']   dipakai untuk baris "Acara"
      $event['place']       tempat
      $tembusan_1..10       tembusan per poin (form: carbonCopies, bisa ditambah, maks. 10); minimal 3 baris bernomor tetap tampil
--}}
@extends('letters.layouts.base')

@section('title', 'Surat Tindak Lanjut Permohonan Izin')

@section('extra_css')
    .tli-kepala p { margin-top: 2pt; }

    /* "Yth :" dan "Di :" pakai label+value dalam tabel supaya baris ke-2, ke-3 dst.
       rata di bawah isi baris pertama (bukan mentok ke margin kiri). */
    table.tujuan { width: 100%; border-collapse: collapse; margin-top: 2pt; }
    table.tujuan td { padding: 0; vertical-align: top; }
    table.tujuan td.lbl { width: 28pt; }
    table.tujuan td.sep { width: 9pt; }
@endsection

@section('content')
    @php
        // Yth: teks banyak baris -> satu <div> per baris, baris kosong dibuang. Jumlah baris tidak dibatasi.
        $yth = array_values(array_filter(
            array_map('trim', preg_split('/\R/u', (string) ($recipient ?? ''))),
            fn ($line) => $line !== ''
        ));

        // Hari: "Kamis" atau, bila ada tanggal selesai di hari yang berbeda, "Kamis – Sabtu".
        $hari = $event['day_name'] ?? '';
        $hariAkhir = $event['end_day_name'] ?? null;
        if (filled($hariAkhir) && $hariAkhir !== $hari) {
            $hari .= ' – ' . $hariAkhir;
        }

        // Tanggal: "27 – 29 Agustus 2026" bila bulan & tahun sama; bila beda, ditulis lengkap dua sisi.
        $tanggal = $event['date_text'] ?? $event['date'] ?? '';
        $tanggalAkhir = $event['end_date_text'] ?? $event['end_date'] ?? null;
        if (filled($tanggalAkhir) && $tanggalAkhir !== $tanggal) {
            $pola = '/^(\d{1,2})\s+(\S+)\s+(\d{4})$/u';
            if (preg_match($pola, $tanggal, $a) && preg_match($pola, $tanggalAkhir, $b) && $a[2] === $b[2] && $a[3] === $b[3]) {
                $tanggal = $a[1] . ' – ' . $b[1] . ' ' . $b[2] . ' ' . $b[3];
            } else {
                $tanggal .= ' – ' . $tanggalAkhir;
            }
        }

        // Waktu: "16.00 WIB" atau, bila ada waktu selesai, "16.00 WIB s/d 18.00 WIB" / "16.00 WIB s/d Selesai".
        $waktu = $event['time'] ?? '';
        if (filled($event['end_time'] ?? null)) {
            $waktu .= ' s/d ' . $event['end_time'];
        }
    @endphp

    <div class="tli-kepala">
        <p>Nomor : {{ $number }}</p>
        <p>Hal &nbsp;&nbsp;&nbsp;: <i>Menindak Lanjuti Permohonan Izin</i></p>
    </div>

    <p class="gap-top-lg">Kepada</p>
    <table class="tujuan">
        <tr>
            <td class="lbl">Yth</td>
            <td class="sep">:</td>
            <td>
                @forelse ($yth as $line)
                    <div>{{ $line }}</div>
                @empty
                    <div>&nbsp;</div>
                @endforelse
            </td>
        </tr>
        <tr>
            <td class="lbl">Di</td>
            <td class="sep">:</td>
            <td>{{ $recipient_address ?? '' }}</td>
        </tr>
    </table>

    <p class="gap-top-lg" style="text-align: justify">
        Yang bertanda tangan dibawah ini Lurah Bimomartani, Kapanewon Ngemplak, Kabupaten
        Sleman, menindak lanjuti surat saudara No. {{ $ref_number ?: '...........' }} Perihal : Permohonan
        Izin. Pada :
    </p>

    <table class="data" style="margin-top: 9pt">
        <tr>
            <td class="lbl">Hari</td>
            <td class="sep">:</td>
            <td class="val">{{ $hari }}</td>
        </tr>
        <tr>
            <td class="lbl">Tanggal</td>
            <td class="sep">:</td>
            <td class="val">{{ $tanggal }}</td>
        </tr>
        <tr>
            <td class="lbl">Waktu</td>
            <td class="sep">:</td>
            <td class="val">{{ $waktu }}</td>
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

{{-- Tembusan per poin: tiap nomor dibaca langsung dari variabelnya ($tembusan_1 .. $tembusan_10), tanpa memecah baris.
     Poin bertambah sesuai yang diisi; minimal 3 baris bernomor tetap tampil walau kosong, supaya ada tempat menulis
     tangan. Nomor ditulis manual di kolom "num" (lihat catatan di base.blade.php soal kenapa bukan <ol>/<li>).
     Bila service lama masih mengirim array $tembusan, dipakai sebagai cadangan. --}}
@section('tembusan')
    @php
        $vars = get_defined_vars();
        $tembusanRows = [];
        for ($n = 1; $n <= 10; $n++) {
            $tembusanRows[] = $vars['tembusan_' . $n] ?? (($tembusan ?? [])[$n - 1] ?? null);
        }
        // Buang baris kosong di ujung, lalu pastikan minimal 3 baris.
        while (count($tembusanRows) > 3 && ! filled(end($tembusanRows))) {
            array_pop($tembusanRows);
        }
        $tembusanRows = array_pad($tembusanRows, 3, null);
    @endphp
    @foreach ($tembusanRows as $i => $t)
        <tr>
            <td class="num">{{ $i + 1 }}.</td>
            <td>{{ $t }}</td>
        </tr>
    @endforeach
@endsection