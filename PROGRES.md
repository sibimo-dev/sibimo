
## Bug A: SELESAI
- File: sibimo-admin/src/services/letter-create.service.js baris 402 (appendNested)
- Fix: guard instanceof Date, format YYYY-MM-DD dari getFullYear/getMonth/getDate (zona waktu lokal)
- Commit di repo sibimo-admin: 4c91125
- sibimo-public: belum dicek apakah punya appendNested yang sama (cek di Tahap 3/4)

## TODO (ditunda)
- sibimo-admin letter-create.service.js baris 60: default tanggal "hari ini" pakai toISOString (bisa terbaca kemarin antara 00:00-07:00 WIB). Ganti ke komponen lokal.
- Tahap 3: tes tanggal wajib pakai new Date(2023, 4, 17) dan TZ=Asia/Jakarta.
