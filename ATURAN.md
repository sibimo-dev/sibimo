# Konteks
3 repo SIBIMO (surat Kaluruhan Bimomartani): sibimo=backend Laravel, sibimo-admin & sibimo-public=frontend. Branch: audit-surat.
Laporan: AUDIT-SURAT.md (2961 baris, JANGAN baca utuh). Baca baris 11-102 (ringkasan) dan 2816-2961 (akar masalah). Detail surat (103-2815) hanya per surat yang dikerjakan, cari pakai grep -n "<kode>".
Baseline: 85 surat, 188 field terbuang, 3 salah-petak, 45 key blade kosong. Target: turun signifikan pada audit ulang.

# Aturan
- Perbaiki di akar (satu tempat), jangan tambal per surat (mis. satu fungsi plain() untuk tanggal, satu aturan pemetaan berprefix peran, satu pembangkit field orang tua).
- C/D: tambah field di katalog backend + alias di normalizeComplexTemplateForm() + (bila perlu) FIELD_OVERRIDES di letterFieldMap.js. Key yang disimpan backend HARUS sama dengan yang dibaca personBlock()/blade.
- Jangan ubah tampilan blade kecuali perlu agar membaca key yang benar. Jangan hapus field lama; tambah alias.
- Dilarang: perintah destruktif DB (migrate:fresh, truncate, delete massal), menyentuh .env. Seeder/migrasi baru boleh asal idempotent. Jangan jalankan seeder sendiri; tulis di CATATAN-PERBAIKAN.md bahwa saya perlu backup DB dan menjalankannya.
- Perubahan definisi isian di sibimo-admin disalin ke sibimo-public bila file sama ada. Laporkan file yang disinkronkan.
- Tiap perubahan beri alasan yang merujuk baris AUDIT-SURAT.md.
- Teks tercetak di surat tetap bahasa Indonesia.
- Keputusan ambigu: JANGAN menebak, catat di "Perlu keputusan" di PROGRES.md.
- Bug A (parsial) dan E (perlu verifikasi) belum terbukti: jangan ubah kode sebelum dibuktikan tes.
- Bug J kontradiktif: verifikasi di kode ($marriageCodes, TEMPLATE_BY_CODE) sebelum mengubah.
- Kode "0 masalah" belum tentu aman: surat dengan slug tidak cocok (Bug G/I) tidak ikut diaudit.
- SKKM: jangan diputuskan sendiri.
- Commit per kelas masalah. Jangan klaim selesai sebelum tes hijau dan audit ulang membaik.
- Kuota hampir habis: commit yang sudah jadi, tulis progres di PROGRES.md (selesai / berikutnya).
