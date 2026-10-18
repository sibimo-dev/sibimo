
## Bug A: SELESAI
- File: sibimo-admin/src/services/letter-create.service.js baris 402 (appendNested)
- Fix: guard instanceof Date, format YYYY-MM-DD dari getFullYear/getMonth/getDate (zona waktu lokal)
- Commit di repo sibimo-admin: 4c91125
- sibimo-public: belum dicek apakah punya appendNested yang sama (cek di Tahap 3/4)

## TODO (ditunda)
- sibimo-admin letter-create.service.js baris 60: default tanggal "hari ini" pakai toISOString (bisa terbaca kemarin antara 00:00-07:00 WIB). Ganti ke komponen lokal.
- Tahap 3: tes tanggal wajib pakai new Date(2023, 4, 17) dan TZ=Asia/Jakarta.

## Bug B: SELESAI (level fungsi)
- sibimo-admin/src/services/letter-create.service.js: ROLE_KEY_PREFIX + matchLabelWithRole (prefix/suffix peran, label frontend boleh bagian dari label backend)
- Tes sementara: sibimo-admin/tests/bug-b-role-label.mjs (22 pass), memakai SALINAN fungsi, belum di-commit
- Konvensi key backend tidak seragam: groom_*/bride_*, groom_father_*, father_*/mother_*, surat kematian nama_ibu/nama_saksi_1 (suffix)
- Belum terbukti end-to-end: Tahap 3 harus menguji mapFieldsToBackend -> viewData -> HTML
- sibimo-public: cek apakah punya salinan matchLabelWithRole/mapFieldsToBackend yang sama

## Bug B: commit final
- sibimo-admin: e34b15f (kode di letter-create.service.js), e0e78e8 (tes tests/bug-b-role-label.mjs, 22 pass).
- Catatan: tes memakai SALINAN matchLabelWithRole; Tahap 3 harus menggantinya dengan tes yang mengimpor dari service.
- Bug A (4c91125) dan B: jangan disentuh lagi kecuali tes Tahap 3 membuktikan masalah.
- Berikutnya: Bug C, D, J (kuota agent habis, refresh 25/10/2026 17:34).
