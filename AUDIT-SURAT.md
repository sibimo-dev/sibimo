# AUDIT SURAT SIBIMO

**Dibuat:** 18/10/2026, 15.16.56  
**Total surat diaudit:** 85  

> Skrip audit: `sibimo/audit-surat.mjs`  
> Tidak ada perubahan pada file aplikasi.

---

## RINGKASAN EKSEKUTIF

| Kode | Slug | Field Terbuang | Salah-Petak | Key Blade Kosong | Field Date Rentan |
|------|------|:-:|:-:|:-:|:-:|
| `SPPWNI` | `relocation-cover-letter` | 🔴 5 | ✅ 0 | 🔴 18 | 🟡 1 |
| `SPPWF` | `relocation-certificate-form` | 🔴 8 | ✅ 0 | 🔴 13 | 🟡 1 |
| `SPTTS` | `temporary-stay-application-form` | 🔴 17 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPSKG` | `lease-offer-letter` | 🔴 16 | ✅ 0 | ✅ 0 | 🟡 2 |
| `SBP` | `research-response-letter` | 🔴 13 | ✅ 0 | ✅ 0 | 🟡 3 |
| `PNL` | `marriage-registration-data-sheet` | 🔴 11 | ✅ 0 | ✅ 0 | 🟡 3 |
| `FPDN` | `marriage-registration-data-sheet` | 🔴 9 | 🔴 2 | ✅ 0 | 🟡 3 |
| `SRBBM` | `fuel-recommendation-letter` | 🟡 1 | ✅ 0 | 🔴 7 | ✅ 0 |
| `SMLPI` | `permit-followup-letter` | 🔴 8 | ✅ 0 | ✅ 0 | 🟡 2 |
| `SKWKA` | `heir-power-of-attorney-letter` | 🔴 7 | ✅ 0 | ✅ 0 | 🟡 2 |
| `SKC` | `power-of-attorney-letter` | 🔴 7 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPBNI` | `identity-discrepancy-statement-letter` | 🔴 6 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKDT` | `resident-arrival-form` | 🔴 3 | ✅ 0 | 🔴 3 | 🟡 1 |
| `SKD` | `domicile-certificate` | 🔴 5 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKTS` | `temporary-resident-request` | 🔴 5 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKUM` | `general-statement-letter` | 🔴 4 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKK` | `event-permit-letter` | 🔴 4 | ✅ 0 | ✅ 0 | 🟡 2 |
| `SPKTP` | `ktp-application-form` | 🔴 4 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SGC` | `divorce-lawsuit-letter` | 🟡 2 | ✅ 0 | 🔴 2 | ✅ 0 |
| `LK` | `birth-report-statement` | 🔴 4 | ✅ 0 | ✅ 0 | ✅ 0 |
| `N6P` | `n6` | 🔴 4 | ✅ 0 | ✅ 0 | 🟡 2 |
| `SKWNP` | `guardian-statement` | 🔴 4 | ✅ 0 | ✅ 0 | 🟡 1 |
| `SKTH` | `land-price-certificate-letter` | 🔴 4 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPKLC` | `letter-c-data-statement-letter` | 🔴 4 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKBK` | `unmarried-status-letter` | 🔴 3 | ✅ 0 | ✅ 0 | 🟡 1 |
| `SPPD` | `duty-travel-order-letter` | 🔴 3 | ✅ 0 | ✅ 0 | 🟡 2 |
| `SPTMDK` | `population-document-statement-letter` | 🔴 3 | ✅ 0 | ✅ 0 | 🟡 1 |
| `SPU` | `general-cover-letter` | 🔴 3 | ✅ 0 | ✅ 0 | 🟡 1 |
| `SKP` | `income-permit-letter` | 🟡 2 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKJG` | `travel-permit-letter-gov` | 🟡 2 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKCK` | `skck-referral-letter` | 🟡 1 | ✅ 0 | 🔴 1 | ✅ 0 |
| `SPKIA` | `kia-application-form` | 🟡 2 | ✅ 0 | ✅ 0 | 🟡 1 |
| `SKDPAK` | `population-service-authorization-letter` | 🟡 2 | ✅ 0 | ✅ 0 | 🟡 2 |
| `DKU` | `death-general-statement` | 🟡 2 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKTA` | `land-origin-certificate-letter` | 🟡 2 | ✅ 0 | ✅ 0 | 🟡 1 |
| `SPDP` | `statement-population-data-change` | 🟡 2 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKTMS` | `sktm-school` | 🟡 1 | ✅ 0 | ✅ 0 | 🟡 1 |
| `SKAK` | `birth-certificate-power-of-attorney` | 🟡 1 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPTJMPSI` | `spousal-relationship-responsibility-statement` | 🟡 1 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPAKL` | `birth-certificate-referral-letter` | 🟡 1 | ✅ 0 | ✅ 0 | ✅ 0 |
| `FBWNI` | `family-biodata-form` | ✅ 0 | ✅ 0 | 🔴 1 | ✅ 0 |
| `RL` | `legalization-register` | 🟡 1 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKBKN` | `never-married-certificate-letter` | 🟡 1 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPTJP` | `unregistered-marriage-responsibility-letter` | ✅ 0 | 🔴 1 | ✅ 0 | 🟡 3 |
| `SKU` | `business-permit-letter` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKUMG` | `general-statement-letter-gov` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKTM` | `sktm-general` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKJ` | `travel-permit-letter` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKJV` | `travel-permit-letter-vill` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPKIAF` | `population-occurrence-registration-form` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `PNP` | `registration-form` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `N2P` | `n2` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `PAK` | `birth-certificate-application-form` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `FPK` | `birth-report-form` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `PPKT` | `late-birth-registration-approval-decree` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `LKLD` | `out-of-domicile-birth-report` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPBMLP` | `unmarried-statement` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKDN` | `marriage-certificate-duplicate-letter` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `N1P` | `n1` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `N4P` | `n4` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `N5P` | `n5` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKWHP` | `judge-guardian` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPTKP` | `health-referral` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKNNP` | `numpang-nikah` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `N1L` | `marriage-introduction-letter` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `N4L` | `bride-groom-consent-letter` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKKL` | `birth-attestation-letter` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `LPKL` | `birth-registration-report` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `DKF` | `death-report-form` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `DKA` | `death-certificate-application` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `DKR` | `death-report` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `DKS` | `death-data-statement` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `DKP` | `death-power-of-attorney` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `DKC` | `death-registration-report` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `DKH` | `death-commemoration-calculation` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKBK2` | `unmarried-certificate` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPCCP` | `bride-groom-consent-letter` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKKMP` | `death-certificate-for-marriage-letter` | ✅ 0 | ✅ 0 | ✅ 0 | 🟡 2 |
| `SKUMN` | `general-certificate-letter` | ✅ 0 | ✅ 0 | ✅ 0 | 🟡 1 |
| `SPNIK` | `marriage-application-letter` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SPNIP` | `marriage-introduction-letter` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKTNB` | `marriage-lodging-certificate-letter` | ✅ 0 | ✅ 0 | ✅ 0 | 🟡 1 |
| `SPBML` | `not-remarried-statement-letter` | ✅ 0 | ✅ 0 | ✅ 0 | 🟡 1 |
| `SPOT` | `parental-consent-letter` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |
| `SKKM` | `(tidak ada)` | ✅ 0 | ✅ 0 | ✅ 0 | ✅ 0 |

---

## DETAIL PER SURAT

### ⚠️ `SPPWNI` — Surat Pengantar Permohonan Pindah WNI

- **Slug:** `relocation-cover-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `relocation-cover-letter.js`
- **Blade template:** `relocation-cover-letter.blade.php`
- **Field frontend:** 14 | **Field backend:** 49

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `nik` | NIK Pemohon | `nik` |
| `name` | Nama Lengkap | `text` |
| `relocationTypeLabel` | Cakupan Kepindahan (subjudul formulir) | `select` |
| `familyMembers` | Anggota keluarga yang pindah | `rows` |
| `registrationOfficer` | Nama Petugas Registrasi | `text` |

#### 🕳️ Key `$form['...']'` di Blade Tidak Ada di Definisi Backend

> Key ini dibaca blade tapi tidak pernah disimpan ke `form_data` (tidak ada di seeder/katalog).

| Key di Blade |
|---|
| `origin` |
| `kk_number` |
| `kkNumber` |
| `origin_kk_number` |
| `originKkNumber` |
| `head_of_family` |
| `headOfFamily` |
| `origin_head_of_family` |
| `originHeadOfFamily` |
| `nik` |
| `name` |
| `full_name` |
| `birth_place` |
| `birthPlace` |
| `birth_date` |
| `birthDate` |
| `relocation_type_label` |
| `registration_officer` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `alamat_asal` ("Alamat Asal")
- `rt_asal` ("RT Asal")
- `rw_asal` ("RW Asal")
- `dusun_asal` ("Dusun/Dukuh/Kampung Asal")
- `desa_asal` ("Desa/Kelurahan Asal")
- `kecamatan_asal` ("Kecamatan Asal")
- `kabupaten_asal` ("Kabupaten/Kota Asal")
- `provinsi_asal` ("Provinsi Asal")
- `kode_pos_asal` ("Kode Pos Asal")
- `telepon_asal` ("Telepon Asal")
- ... dan 30 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `birthDate` ("Tanggal Lahir") → backend: `birthDate`

---

### ⚠️ `SPPWF` — Formulir Surat Pindah Warga Negara Indonesia

- **Slug:** `relocation-certificate-form`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `relocation-certificate-form.js`
- **Blade template:** `relocation-certificate-form.blade.php`
- **Field frontend:** 13 | **Field backend:** 23

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `kkNumber` | Nomor Kartu Keluarga | `kk` |
| `headOfFamily` | Nama Kepala Keluarga | `text` |
| `nik` | NIK Pemohon | `nik` |
| `name` | Nama Lengkap | `text` |
| `relocationReasonOther` | Sebutkan alasan lainnya | `text` |
| `relocationTypeLabel` | Cakupan Kepindahan (subjudul formulir) | `select` |
| `relocationType` | Jenis Kepindahan | `select` |
| `originKkStatus` | Status KK bagi yang tidak pindah | `select` |

#### 🕳️ Key `$form['...']'` di Blade Tidak Ada di Definisi Backend

> Key ini dibaca blade tapi tidak pernah disimpan ke `form_data` (tidak ada di seeder/katalog).

| Key di Blade |
|---|
| `origin` |
| `kk_number` |
| `kkNumber` |
| `originKkNumber` |
| `head_of_family` |
| `headOfFamily` |
| `originHeadOfFamily` |
| `nik` |
| `name` |
| `full_name` |
| `birthPlace` |
| `birthDate` |
| `relocation_type_label` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `origin_kk_number` ("Nomor KK Asal")
- `origin_head_of_family` ("Kepala Keluarga Asal")
- `origin_address` ("Alamat Asal")
- `origin_village` ("Desa/Kalurahan Asal")
- `origin_district` ("Kapanewon Asal")
- `origin_regency` ("Kabupaten Asal")
- `origin_province` ("Provinsi Asal")
- `destination_address` ("Alamat Tujuan")
- `destination_village` ("Desa/Kalurahan Tujuan")
- `destination_district` ("Kapanewon Tujuan")
- ... dan 8 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `birthDate` ("Tanggal Lahir") → backend: `birthDate`

---

### ⚠️ `SPTTS` — Formulir Permohonan Tinggal Sementara

- **Slug:** `temporary-stay-application-form`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `temporary-stay-application-form.js`
- **Blade template:** `temporary-stay-application-form.blade.php`
- **Field frontend:** 31 | **Field backend:** 29

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `familyCardNumber` | Nomor KK | `kk` |
| `originVillage` | Desa/Kelurahan | `text` |
| `originDistrict` | Kecamatan | `text` |
| `originRegency` | Kabupaten | `text` |
| `originProvince` | Provinsi | `text` |
| `destinationAddressLine1` | Alamat yang dituju (baris 1) | `text` |
| `destinationAddressLine2` | Alamat yang dituju (baris 2) | `text` |
| `familyMember1Name` | Nama anggota 1 | `text` |
| `familyMember1Nik` | NIK anggota 1 | `text` |
| `familyMember2Name` | Nama anggota 2 | `text` |
| `familyMember2Nik` | NIK anggota 2 | `text` |
| `familyMember3Name` | Nama anggota 3 | `text` |
| `familyMember3Nik` | NIK anggota 3 | `text` |
| `familyMember4Name` | Nama anggota 4 | `text` |
| `familyMember4Nik` | NIK anggota 4 | `text` |
| `familyMember5Name` | Nama anggota 5 | `text` |
| `familyMember5Nik` | NIK anggota 5 | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `applicant_name` ("Nama Pemohon")
- `applicant_nik` ("NIK Pemohon")
- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `gender` ("Jenis Kelamin")
- `occupation` ("Pekerjaan")
- `origin_address` ("Alamat Asal")
- `destination_address` ("Alamat Tujuan")
- `host_kk_number` ("Nomor KK Penjamin")
- `education` ("Pendidikan")
- ... dan 5 lagi

---

### ⚠️ `SPSKG` — Surat Penawaran Sewa Kontrak Gedung BRI Unit Ngemplak II

- **Slug:** `lease-offer-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `lease-offer-letter.js`
- **Blade template:** `lease-offer-letter.blade.php`
- **Field frontend:** 16 | **Field backend:** 8

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `requestNumber` | Nomor surat permintaan | `text` |
| `requestDate` | Tanggal surat permintaan | `date` |
| `requestSubject` | Perihal surat permintaan | `text` |
| `tenantName` | Nama instansi penyewa | `text` |
| `tenantRepresentative` | Nama pimpinan / penerima surat | `text` |
| `tenantPosition` | Jabatan | `text` |
| `tenantAddress` | Alamat instansi penyewa | `area` |
| `propertyName` | Nama objek sewa | `text` |
| `propertyAddress` | Alamat / lokasi objek sewa | `area` |
| `buildingArea` | Luas bangunan (m²) | `text` |
| `landArea` | Luas tanah (m²) | `text` |
| `rentPrice` | Harga sewa | `text` |
| `rentPeriod` | Jangka waktu sewa | `text` |
| `rentStartDate` | Tanggal mulai sewa | `date` |
| `paymentTerms` | Cara pembayaran | `area` |
| `otherTerms` | Ketentuan lain | `area` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `perihal` ("Hal/Perihal")
- `ditujukan_kepada` ("Ditujukan Kepada (Nama/Jabatan)")
- `lokasi_tujuan_surat` ("Lokasi Tujuan Surat")
- `nama_gedung` ("Nama Gedung/Aset yang Disewakan")
- `alamat_gedung` ("Alamat Gedung/Aset")
- `tanggal_berakhir_sewa_lama` ("Tanggal Berakhir Masa Sewa Sebelumnya")
- `harga_sewa_per_tahun` ("Harga Sewa per Tahun (Rp)")
- `lama_sewa_tahun` ("Lama Sewa (Tahun)")

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `requestDate` ("Tanggal surat permintaan") → backend: `requestDate`
- `rentStartDate` ("Tanggal mulai sewa") → backend: `rentStartDate`

---

### ⚠️ `FPDN` — Formulir Pendaftaran Data Nikah

- **Slug:** `marriage-registration-data-sheet`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `marriage-registration-data-sheet.js`
- **Blade template:** `marriage-registration-data-sheet.blade.php`
- **Field frontend:** 18 | **Field backend:** 135

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `groomPrevBin` | Bin/Binti (nama ayah) | `text` |
| `groomPrevParentName` | Nama orang tua istri terdahulu | `text` |
| `bridePrevBin` | Bin (nama ayah) | `text` |
| `bridePrevDeathDate` | Tanggal Meninggal | `date` |
| `bridePrevDeathPlace` | Tempat Meninggal | `text` |
| `groomPrevName` | Nama Lengkap | `text` |
| `groomPrevNik` | NIK | `text` |
| `bridePrevName` | Nama Lengkap | `text` |
| `bridePrevNik` | NIK | `text` |

#### ⚠️ Kemungkinan Salah-Petak (Peran Tidak Cocok)

> Field dipetakan ke backend, tapi prefix peran form ≠ prefix peran backend.
> Bisa jadi label generik ("Nama", "Agama") dipetakan ke field identitas umum alih-alih blok peran yang seharusnya.

| Key Form | Peran Form | Backend Key | Peran Backend |
|---|---|---|---|
| `groomPrevDeathDate` | groom | `deceased_death_date` | deceased |
| `groomPrevDeathPlace` | groom | `deceased_death_place` | deceased |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nationality` ("Kewarganegaraan")
- `name_alias` ("Nama Alias")
- `marital_status` ("Status Perkawinan")
- `groom_name` ("Nama Calon Suami")
- `groom_bin` ("Bin Calon Suami")
- `groom_nik` ("NIK Calon Suami")
- `groom_birth_place` ("Tempat Lahir Calon Suami")
- `groom_birth_date` ("Tanggal Lahir Calon Suami")
- `groom_citizenship` ("Kewarganegaraan Calon Suami")
- `groom_religion` ("Agama Calon Suami")
- ... dan 116 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `groomPrevDeathDate` ("Tanggal Meninggal") → backend: `groomPrevDeathDate`
- `bridePrevDeathDate` ("Tanggal Meninggal") → backend: `bridePrevDeathDate`
- `groomPrevBirthDate` ("Tanggal Lahir") → backend: `groomPrevBirthDate`

---

### ⚠️ `SRBBM` — Surat Rekomendasi Pembelian Jenis BBM Tertentu

- **Slug:** `fuel-recommendation-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `fuel-recommendation-letter.js`
- **Blade template:** `fuel-recommendation-letter.blade.php`
- **Field frontend:** 9 | **Field backend:** 11

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `fuelItems` | Rincian kebutuhan BBM | `rows` |

#### 🕳️ Key `$form['...']'` di Blade Tidak Ada di Definisi Backend

> Key ini dibaca blade tapi tidak pernah disimpan ke `form_data` (tidak ada di seeder/katalog).

| Key di Blade |
|---|
| `consumer` |
| `fuel_items` |
| `total` |
| `volume_allocation` |
| `pickup_location` |
| `distributor_number` |
| `distributor_location` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `pengecer_solar` ("Pengecer Solar")
- `bbm_jenis_tertentu` ("BBM Jenis Tertentu")
- `konsumsi_bbm` ("Konsumsi (Liter/Jam, Harian/Mingguan/Bulanan)")

---

### ⚠️ `SBP` — Surat Balasan Penelitian

- **Slug:** `research-response-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `research-response-letter.js`
- **Blade template:** `research-response-letter.blade.php`
- **Field frontend:** 14 | **Field backend:** 6

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `requestNumber` | Nomor surat permohonan | `text` |
| `requestDate` | Tanggal surat permohonan | `date` |
| `requestInstitution` | Asal instansi / perguruan tinggi | `text` |
| `researcherName` | Nama peneliti | `text` |
| `researcherNumber` | NIM / NIP / NIDN | `text` |
| `researcherStudyProgram` | Program studi / jurusan | `text` |
| `researcherPhone` | Nomor telepon | `text` |
| `researchTitle` | Judul penelitian | `area` |
| `researchLocation` | Lokasi penelitian | `text` |
| `researchStartDate` | Tanggal mulai penelitian | `date` |
| `researchEndDate` | Tanggal selesai penelitian | `date` |
| `responseResult` | Hasil tanggapan | `select` |
| `responseConditions` | Ketentuan selama penelitian | `area` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_surat_asal` ("Nomor Surat yang Ditindaklanjuti")
- `nama_mahasiswa` ("Nama Mahasiswa")
- `nim` ("NIM")
- `program_studi` ("Program Studi")
- `fakultas` ("Fakultas")

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `requestDate` ("Tanggal surat permohonan") → backend: `requestDate`
- `researchStartDate` ("Tanggal mulai penelitian") → backend: `researchStartDate`
- `researchEndDate` ("Tanggal selesai penelitian") → backend: `researchEndDate`

---

### ⚠️ `PNL` — Data Isian Pendaftaran Nikah

- **Slug:** `marriage-registration-data-sheet`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `marriage-registration-data-sheet.js`
- **Blade template:** `marriage-registration-data-sheet.blade.php`
- **Field frontend:** 18 | **Field backend:** 60

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `brideEducation` | Pendidikan Terakhir | `select` |
| `brideStatus` | Status | `select` |
| `groomPrevBin` | Bin/Binti (nama ayah) | `text` |
| `groomPrevParentName` | Nama orang tua istri terdahulu | `text` |
| `groomPrevDeathDate` | Tanggal Meninggal | `date` |
| `groomPrevDeathPlace` | Tempat Meninggal | `text` |
| `bridePrevBin` | Bin (nama ayah) | `text` |
| `bridePrevDeathDate` | Tanggal Meninggal | `date` |
| `bridePrevDeathPlace` | Tempat Meninggal | `text` |
| `groomPrevName` | Nama Lengkap | `text` |
| `bridePrevName` | Nama Lengkap | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `hari_akad` ("Hari Akad")
- `tanggal_akad` ("Tanggal Akad")
- `jam_akad` ("Jam Akad")
- `tempat_akad` ("Tempat Akad")
- `nama_catin_putra` ("Nama")
- `kewarganegaraan_catin_Putra` ("Kewarganegaraan")
- `agama_catin_putra` ("Agama")
- `pekerjaan_catin_putra` ("Pekerjaan")
- `nama_istri_terdahulu` ("Nama Istri Terdahulu")
- `bin_istri_terdahulu` ("Bin/Nama Orang Tua Istri Terdahulu")
- ... dan 43 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `groomPrevDeathDate` ("Tanggal Meninggal") → backend: `groomPrevDeathDate`
- `bridePrevDeathDate` ("Tanggal Meninggal") → backend: `bridePrevDeathDate`
- `groomPrevBirthDate` ("Tanggal Lahir") → backend: `groomPrevBirthDate`

---

### ⚠️ `SKDT` — Formulir Pendataan Penduduk Datang

- **Slug:** `resident-arrival-form`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `resident-arrival-form.js`
- **Blade template:** `resident-arrival-form.blade.php`
- **Field frontend:** 11 | **Field backend:** 23

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `originKkStatus` | Status KK bagi yang tidak Pindah | `select` |
| `relocationReasonOther` | Sebutkan alasan lainnya | `text` |
| `relocationType` | Jenis Kepindahan | `select` |

#### 🕳️ Key `$form['...']'` di Blade Tidak Ada di Definisi Backend

> Key ini dibaca blade tapi tidak pernah disimpan ke `form_data` (tidak ada di seeder/katalog).

| Key di Blade |
|---|
| `origin` |
| `destination` |
| `registration_officer` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `origin_address` ("Alamat Asal")
- `origin_village` ("Desa/Kalurahan Asal")
- `origin_district` ("Kapanewon Asal")
- `origin_regency` ("Kabupaten Asal")
- `origin_province` ("Provinsi Asal")
- `destination_address` ("Alamat Tujuan")
- `destination_village` ("Desa/Kalurahan Tujuan")
- `destination_district` ("Kapanewon Tujuan")
- `destination_regency` ("Kabupaten Tujuan")
- `destination_province` ("Provinsi Tujuan")
- ... dan 5 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `arrivalDate` ("5. Tanggal Kedatangan") → backend: `arrivalDate`

---

### ⚠️ `SMLPI` — Surat Tindak Lanjut Permohonan Izin

- **Slug:** `permit-followup-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `permit-followup-letter.js`
- **Blade template:** `permit-followup-letter.blade.php`
- **Field frontend:** 10 | **Field backend:** 13

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `permitType` | Jenis izin yang dimohonkan | `text` |
| `requestNumber` | Nomor surat permohonan | `text` |
| `requestDate` | Tanggal surat permohonan | `date` |
| `requestSubject` | Perihal surat permohonan | `text` |
| `followUpResult` | Hasil tindak lanjut | `select` |
| `followUpDate` | Tanggal tindak lanjut | `date` |
| `followUpContent` | Isi tanggapan / tindak lanjut | `area` |
| `followUpConditions` | Syarat atau ketentuan lanjutan | `area` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nama_organisasi` ("Nama Organisasi/Kegiatan Pemohon")
- `pangkalan_instansi` ("Pangkalan/Instansi Pemohon")
- `lokasi_tujuan_surat` ("Lokasi Tujuan Surat")
- `nomor_surat_asal` ("Nomor Surat yang Ditindaklanjuti")
- `perihal_surat_asal` ("Perihal Surat yang Ditindaklanjuti")
- `hari_kegiatan` ("Hari")
- `tanggal_kegiatan` ("Tanggal Kegiatan")
- `waktu_kegiatan` ("Waktu")
- `nama_acara` ("Acara/Kegiatan")
- `tempat_kegiatan` ("Tempat Kegiatan")
- ... dan 1 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `requestDate` ("Tanggal surat permohonan") → backend: `requestDate`
- `followUpDate` ("Tanggal tindak lanjut") → backend: `followUpDate`

---

### ⚠️ `SKWKA` — Surat Kuasa Antar-Warga untuk Sidang Waris

- **Slug:** `heir-power-of-attorney-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `heir-power-of-attorney-letter.js`
- **Blade template:** `heir-power-of-attorney-letter.blade.php`
- **Field frontend:** 14 | **Field backend:** 10

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `name` | Nama Lengkap | `text` |
| `nik` | NIK | `nik` |
| `deceasedName` | Nama Pewaris | `text` |
| `deceasedDeathPlace` | Meninggal Dunia di | `text` |
| `attorneyBirthPlace` | Tempat Lahir | `text` |
| `attorneyBirthDate` | Tanggal Lahir | `date` |
| `attorneyGender` | Jenis Kelamin | `select` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `condition` ("Alasan Pemberian Kuasa")
- `religion` ("Agama")
- `occupation` ("Pekerjaan")

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `birthDate` ("Tanggal Lahir") → backend: `birthDate`
- `attorneyBirthDate` ("Tanggal Lahir") → backend: `attorneyBirthDate`

---

### ⚠️ `SKC` — Surat Kuasa Pelayanan Letter C

- **Slug:** `power-of-attorney-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `power-of-attorney-letter.js`
- **Blade template:** `power-of-attorney-letter.blade.php`
- **Field frontend:** 12 | **Field backend:** 10

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `name` | Nama | `text` |
| `birthPlaceDate` | Tempat/Tgl. Lahir | `text` |
| `nik` | NIK | `text` |
| `deceasedName` | Nama Almarhum/Almarhumah | `text` |
| `deceasedDeathPlace` | Meninggal dunia di | `text` |
| `attorneyBirthPlaceDate` | Tempat/Tgl. Lahir | `text` |
| `attorneyGender` | Jenis Kelamin | `select` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `condition` ("Alasan Pemberian Kuasa")
- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `religion` ("Agama")
- `occupation` ("Pekerjaan")

---

### ⚠️ `SPBNI` — Surat Pernyataan Beda Nama/Identitas

- **Slug:** `identity-discrepancy-statement-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `identity-discrepancy-statement-letter.js`
- **Blade template:** `identity-discrepancy-statement-letter.blade.php`
- **Field frontend:** 6 | **Field backend:** 3

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `name` | Nama Lengkap | `text` |
| `nik` | NIK | `nik` |
| `address` | Alamat Sesuai KTP | `area` |
| `otherName` | Nama pada dokumen | `text` |
| `otherAddress` | Alamat pada dokumen | `area` |
| `otherNik` | NIK pada dokumen | `nik` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `perbedaan_nama` ("Perbedaan pada Nama")
- `perbedaan_alamat` ("Perbedaan pada Alamat")
- `perbedaan_nik` ("Perbedaan pada NIK")

---

### ⚠️ `SGC` — Surat Permohonan Cerai

- **Slug:** `divorce-lawsuit-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `divorce-lawsuit-letter.js`
- **Blade template:** `divorce-lawsuit-letter.blade.php`
- **Field frontend:** 3 | **Field backend:** 28

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `marriageCertNumber` | Surat Nikah (Nomor) | `text` |
| `reasons` | Alasan | `rows` |

#### 🕳️ Key `$form['...']'` di Blade Tidak Ada di Definisi Backend

> Key ini dibaca blade tapi tidak pernah disimpan ke `form_data` (tidak ada di seeder/katalog).

| Key di Blade |
|---|
| `court_name` |
| `court_city` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nama_penggugat` ("Nama")
- `tempat_lahir_penggugat` ("Tempat Lahir")
- `tanggal_lahir_penggugat` ("Tanggal Lahir")
- `agama_penggugat` ("Agama")
- `pekerjaan_penggugat` ("Pekerjaan")
- `alamat_penggugat` ("Alamat")
- `nama_tergugat` ("Nama")
- `tempat_lahir_tergugat` ("Tempat Lahir")
- `tanggal_lahir_tergugat` ("Tanggal Lahir")
- `pekerjaan_tergugat` ("Pekerjaan")
- ... dan 17 lagi

---

### ⚠️ `SKD` — Surat Keterangan Domisili

- **Slug:** `domicile-certificate`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `domicile-certificate.js`
- **Blade template:** `domicile-certificate.blade.php`
- **Field frontend:** 15 | **Field backend:** 16

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `domicileAddress` | Alamat domisili | `area` |
| `name` | Nama | `text` |
| `birthPlaceDate` | Tempat/Tgl. Lahir | `text` |
| `nik` | NIK | `text` |
| `address` | Alamat | `area` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `alamat_domisili_usaha` ("Alamat Domisili Usaha")
- `nama_pemilik` ("Nama Pemilik")
- `nik_pemilik` ("NIK Pemilik")
- `tempat_lahir_pemilik` ("Tempat Lahir")
- `tanggal_lahir_pemilik` ("Tanggal Lahir")
- `alamat_pemilik` ("Alamat Pemilik")

---

### ⚠️ `SKTS` — Surat Permohonan Tinggal Sementara

- **Slug:** `temporary-resident-request`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `temporary-resident-request.js`
- **Blade template:** `temporary-resident-request.blade.php`
- **Field frontend:** 13 | **Field backend:** 29

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `rt` | RT | `text` |
| `rw` | RW | `text` |
| `hamlet` | Dusun | `text` |
| `village` | Kalurahan | `text` |
| `district` | Kapanewon | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `applicant_name` ("Nama Pemohon")
- `applicant_nik` ("NIK Pemohon")
- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `gender` ("Jenis Kelamin")
- `occupation` ("Pekerjaan")
- `guarantor_name` ("Nama Penjamin")
- `guarantor_nik` ("NIK Penjamin")
- `religion` ("Agama")
- `marital_status` ("Status Perkawinan")
- ... dan 11 lagi

---

### ⚠️ `SKUM` — Surat Keterangan Umum

- **Slug:** `general-statement-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `general-statement-letter.js`
- **Blade template:** `general-statement-letter.blade.php`
- **Field frontend:** 9 | **Field backend:** 7

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `name` | Nama | `text` |
| `birthPlaceDate` | Tempat/Tgl. Lahir | `text` |
| `nik` | NIK | `text` |
| `address` | Alamat | `area` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")

---

### ⚠️ `SKK` — Surat Keterangan Keramaian

- **Slug:** `event-permit-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `event-permit-letter.js`
- **Blade template:** `event-permit-letter.blade.php`
- **Field frontend:** 16 | **Field backend:** 13

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `name` | Nama | `text` |
| `birthPlaceDate` | Tempat/Tgl. Lahir | `text` |
| `nik` | NIK | `text` |
| `address` | Alamat | `area` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `untuk_kegiatan_acara` ("Untuk Kegiatan/Acara")

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `eventDate` ("Pada Hari/Tanggal") → backend: `eventDate`
- `eventTime` ("Jam") → backend: `eventTime`

---

### ⚠️ `SPKTP` — Surat Permohonan KTP

- **Slug:** `ktp-application-form`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `ktp-application-form.js`
- **Blade template:** `ktp-application-form.blade.php`
- **Field frontend:** 7 | **Field backend:** 9

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `applicationType` | Permohonan KTP | `select` |
| `name` | Nama Lengkap | `text` |
| `nik` | NIK | `nik` |
| `address` | Alamat | `area` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `pemerintah_provinsi` ("Pemerintah Provinsi")
- `pemerintah_kabupaten` ("Pemerintah Kabupaten")
- `kecamatan` ("Kecamatan")
- `desa` ("Desa")
- `jenis_permohonan_ktp` ("Jenis Permohonan KTP")
- `spesimen_tanda_tangan` ("Spesimen Tanda Tangan")

---

### ⚠️ `LK` — Laporan Kelahiran

- **Slug:** `birth-report-statement`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `birth-report-statement.js`
- **Blade template:** `birth-report-statement.blade.php`
- **Field frontend:** 4 | **Field backend:** 30

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `childBirthDayName` | Hari Lahir | `select` |
| `childBirthDay` | Tanggal | `select` |
| `childBirthMonth` | Bulan | `select` |
| `childBirthYear` | Tahun | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nik_pelapor` ("NIK")
- `nama_pelapor` ("Nama Lengkap")
- `tanggal_lahir_pelapor` ("Tanggal Lahir")
- `umur_pelapor` ("Umur")
- `pekerjaan_pelapor` ("Pekerjaan")
- `alamat_pelapor` ("Alamat")
- `hubungan_dengan_bayi` ("Hubungan dengan Si Bayi")
- `nama_ibu` ("Nama")
- `alamat_ibu` ("Alamat")
- `rt_ibu` ("RT")
- ... dan 20 lagi

---

### ⚠️ `N6P` — Surat Keterangan Kematian

- **Slug:** `n6`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `n6.js`
- **Blade template:** `n6.blade.php`
- **Field frontend:** 8 | **Field backend:** 18

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `exHusbandBin` | Bin (nama ayah) | `text` |
| `exHusbandName` | Nama Lengkap | `text` |
| `exHusbandBirthPlace` | Tempat Lahir | `text` |
| `exHusbandBirthDate` | Tanggal Lahir | `date` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nama_almarhum` ("Nama Lengkap dan Alias")
- `bin_binti_almarhum` ("Bin/Binti")
- `ttl_almarhum` ("Tempat dan Tanggal Lahir")
- `kewarganegaraan_almarhum` ("Kewarganegaraan")
- `agama_almarhum` ("Agama")
- `pekerjaan_almarhum` ("Pekerjaan")
- `nama_pasangan` ("Nama Lengkap dan Alias")
- `bin_binti_pasangan` ("Bin/Binti")
- `nik_pasangan` ("NIK")
- `ttl_pasangan` ("Tempat dan Tanggal Lahir")
- ... dan 4 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `exHusbandDiedAt` ("Tanggal Meninggal") → backend: `exHusbandDiedAt`
- `exHusbandBirthDate` ("Tanggal Lahir") → backend: `exHusbandBirthDate`

---

### ⚠️ `SKWNP` — Surat Keterangan Wali Nikah

- **Slug:** `guardian-statement`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `guardian-statement.js`
- **Blade template:** `guardian-statement.blade.php`
- **Field frontend:** 9 | **Field backend:** 25

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `guardianRelation` | Hubungan dengan calon istri | `text` |
| `guardianReason` | Keterangan/alasan | `area` |
| `guardianBirthPlace` | Tempat Lahir | `text` |
| `guardianBirthDate` | Tanggal Lahir | `date` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `ttl_wali` ("Tempat dan Tanggal Lahir")
- `kewarganegaraan_wali` ("Kewarganegaraan")
- `agama_wali` ("Agama")
- `nama_mempelai_wanita` ("Nama Lengkap dan Alias")
- `binti_mempelai_wanita` ("Binti")
- `nik_mempelai_wanita` ("NIK")
- `ttl_mempelai_wanita` ("Tempat dan Tanggal Lahir")
- `kewarganegaraan_mempelai_wanita` ("Kewarganegaraan")
- `agama_mempelai_wanita` ("Agama")
- `pekerjaan_mempelai_wanita` ("Pekerjaan")
- ... dan 10 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `guardianBirthDate` ("Tanggal Lahir") → backend: `guardianBirthDate`

---

### ⚠️ `SKTH` — Surat Keterangan Harga Tanah

- **Slug:** `land-price-certificate-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `land-price-certificate-letter.js`
- **Blade template:** `land-price-certificate-letter.blade.php`
- **Field frontend:** 9 | **Field backend:** 12

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `certificateNumber` | No. Sertifikat/NIB | `text` |
| `city` | Kota/Kabupaten | `text` |
| `priceMin` | Harga terendah (Rp) | `text` |
| `priceMax` | Harga tertinggi (Rp) | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `land_certificate_number` ("Nomor Letter C")
- `land_area_in_words` ("Luas dalam Huruf")
- `land_regency` ("Kabupaten")
- `land_price_min` ("Harga Tanah Terendah")
- `land_price_max` ("Harga Tanah Tertinggi")
- `land_measurement_letter_number` ("Nomor Surat Ukur")
- `land_measurement_letter_date` ("Tanggal Surat Ukur")

---

### ⚠️ `SPKLC` — Surat Pernyataan Data Letter C

- **Slug:** `letter-c-data-statement-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `letter-c-data-statement-letter.js`
- **Blade template:** `letter-c-data-statement-letter.blade.php`
- **Field frontend:** 10 | **Field backend:** 11

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `name` | Nama | `text` |
| `nik` | NIK | `text` |
| `birthPlaceDate` | Tempat/Tgl. Lahir | `text` |
| `hamletHeadName` | Nama Dukuh yang mengetahui | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `letter_c_number` ("Nomor Letter C")
- `dukuh_name` ("Nama Dukuh")
- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `religion` ("Agama")

---

### ⚠️ `SKBK` — Surat Keterangan Belum Kawin

- **Slug:** `unmarried-status-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `unmarried-status-letter.js`
- **Blade template:** `unmarried-status-letter.blade.php`
- **Field frontend:** 8 | **Field backend:** 5

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `applicantName` | Nama Lengkap | `text` |
| `applicantNik` | NIK | `text` |
| `applicantAddress` | Alamat | `textarea` |

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `applicantBirthDate` ("Tanggal Lahir") → backend: `applicantBirthDate`

---

### ⚠️ `SPPD` — SPPD

- **Slug:** `duty-travel-order-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `duty-travel-order-letter.js`
- **Blade template:** `duty-travel-order-letter.blade.php`
- **Field frontend:** 16 | **Field backend:** 14

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `issuingOfficial` | Pejabat yang memberi perintah | `text` |
| `travelLevel` | Tingkat perjalanan | `text` |
| `duration` | Lama perjalanan | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `pejabat_berwenang` ("Pejabat yang Berwenang Memberi Perintah")

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `departureDate` ("Tanggal berangkat") → backend: `departureDate`
- `returnDueDate` ("Tanggal harus kembali") → backend: `returnDueDate`

---

### ⚠️ `SPTMDK` — Surat Pernyataan Tidak Memiliki Dokumen Kependudukan

- **Slug:** `population-document-statement-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `population-document-statement-letter.js`
- **Blade template:** `population-document-statement-letter.blade.php`
- **Field frontend:** 7 | **Field backend:** 4

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `name` | Nama Lengkap | `text` |
| `nik` | NIK | `nik` |
| `address` | Alamat (RT/RW, Kalurahan, Kapanewon) | `area` |

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `birthDate` ("Tanggal Lahir") → backend: `birthDate`

---

### ⚠️ `SPU` — Surat Pengantar Umum

- **Slug:** `general-cover-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `general-cover-letter.js`
- **Blade template:** `general-cover-letter.blade.php`
- **Field frontend:** 11 | **Field backend:** 8

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `name` | Nama | `text` |
| `nik` | NIK | `nik` |
| `address` | Alamat | `area` |

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `birthDate` ("Tanggal Lahir") → backend: `birthDate`

---

### ⚠️ `SKCK` — Surat Keterangan Mohon SKCK

- **Slug:** `skck-referral-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `skck-referral-letter.js`
- **Blade template:** `skck-referral-letter.blade.php`
- **Field frontend:** 1 | **Field backend:** 8

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `occupationNote` | Keterangan pekerjaan | `text` |

#### 🕳️ Key `$form['...']'` di Blade Tidak Ada di Definisi Backend

> Key ini dibaca blade tapi tidak pernah disimpan ke `form_data` (tidak ada di seeder/katalog).

| Key di Blade |
|---|
| `occupation_note` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")
- `jenis_kelamin` ("Jenis Kelamin")
- `status_perkawinan` ("Status Perkawinan")
- `agama` ("Agama")
- `pekerjaan` ("Pekerjaan")
- `pergi_ke` ("Pergi Ke")
- `keperluan` ("Keperluan")

---

### ⚠️ `SPTJP` — SPTJM Perkawinan Belum Tercatat

- **Slug:** `unregistered-marriage-responsibility-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `unregistered-marriage-responsibility-letter.js`
- **Blade template:** `unregistered-marriage-responsibility-letter.blade.php`
- **Field frontend:** 18 | **Field backend:** 135

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ⚠️ Kemungkinan Salah-Petak (Peran Tidak Cocok)

> Field dipetakan ke backend, tapi prefix peran form ≠ prefix peran backend.
> Bisa jadi label generik ("Nama", "Agama") dipetakan ke field identitas umum alih-alih blok peran yang seharusnya.

| Key Form | Peran Form | Backend Key | Peran Backend |
|---|---|---|---|
| `wifeOccupation` | wife | `bride_occupation` | bride |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `education` ("Pendidikan Terakhir")
- `nationality` ("Kewarganegaraan")
- `name_alias` ("Nama Alias")
- `marital_status` ("Status Perkawinan")
- `groom_name` ("Nama Calon Suami")
- `groom_bin` ("Bin Calon Suami")
- `groom_nik` ("NIK Calon Suami")
- `groom_birth_place` ("Tempat Lahir Calon Suami")
- `groom_birth_date` ("Tanggal Lahir Calon Suami")
- `groom_citizenship` ("Kewarganegaraan Calon Suami")
- ... dan 107 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `marriageDate` ("Tanggal Perkawinan Dilaksanakan") → backend: `marriageDate`
- `husbandBirthDate` ("Tanggal Lahir") → backend: `husbandBirthDate`
- `wifeBirthDate` ("Tanggal Lahir") → backend: `wifeBirthDate`

---

### ⚠️ `SKP` — Surat Keterangan Penghasilan

- **Slug:** `income-permit-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `income-permit-letter.js`
- **Blade template:** `income-permit-letter.blade.php`
- **Field frontend:** 10 | **Field backend:** 18

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `birthPlaceDate` | Tempat/Tgl. Lahir | `text` |
| `purpose` | Surat Keterangan ini dipergunakan untuk | `area` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")
- `kategori` ("Kategori")
- `nomor_kkm_krm` ("Nomor KKM/KRM")
- `dipergunakan_untuk` ("Dipergunakan Untuk")
- `tempat_lahir_anak` ("Tempat Lahir")
- `tanggal_lahir_anak` ("Tanggal Lahir")
- `jenis_kelamin_anak` ("Jenis Kelamin")
- `pendidikan_anak` ("Pendidikan")
- `kelas_semester_anak` ("Kelas/Semester")

---

### ⚠️ `SKJG` — Surat Keterangan Jalan

- **Slug:** `travel-permit-letter-gov`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `travel-permit-letter-gov.js`
- **Blade template:** `travel-permit-letter-gov.blade.php`
- **Field frontend:** 2 | **Field backend:** 8

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `kkNumber` | Nomor KK | `kk` |
| `phone` | Nomor telepon | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `purpose` ("Keperluan")
- `destination_agency` ("Instansi Tujuan")
- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `gender` ("Jenis Kelamin")
- `religion` ("Agama")
- `occupation` ("Pekerjaan")
- `address` ("Alamat")

---

### ⚠️ `SPKIA` — Surat Permohonan Penerbitan KIA

- **Slug:** `kia-application-form`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `kia-application-form.js`
- **Blade template:** `kia-application-form.blade.php`
- **Field frontend:** 15 | **Field backend:** 17

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `nik` | NIK | `nik` |
| `name` | Nama Lengkap | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nik_anak` ("NIK Anak")
- `nama_anak` ("Nama Anak")
- `alamat_anak` ("Alamat")
- `tgl_pengambilan` ("Tanggal Pengambilan")

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `birthDate` ("Tanggal Lahir") → backend: `birthDate`

---

### ⚠️ `SKDPAK` — Surat Kuasa Dalam Pelayanan Administrasi Kependudukan

- **Slug:** `population-service-authorization-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `population-service-authorization-letter.js`
- **Blade template:** `population-service-authorization-letter.blade.php`
- **Field frontend:** 13 | **Field backend:** 15

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `name` | Nama Lengkap | `text` |
| `grantorCondition` | Kondisi pemberi kuasa saat ini | `select` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nama_pemberi_kuasa` ("Nama")
- `rt_penerima_kuasa` ("RT")
- `rw_penerima_kuasa` ("RW")
- `alasan_kuasa` ("Alasan/Kondisi Pemberian Kuasa")

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `birthDate` ("Tanggal Lahir") → backend: `birthDate`
- `attorneyBirthDate` ("Tanggal Lahir") → backend: `attorneyBirthDate`

---

### ⚠️ `DKU` — Surat Keterangan Kematian Umum

- **Slug:** `death-general-statement`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `death-general-statement.js`
- **Blade template:** `death-general-statement.blade.php`
- **Field frontend:** 2 | **Field backend:** 34

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `travelTo` | Pergi Ke | `text` |
| `purpose` | Keperluan | `area` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_jenazah` ("NIK Jenazah")
- `nama_jenazah` ("Nama Jenazah")
- `jenis_kelamin_jenazah` ("Jenis Kelamin Jenazah")
- `tempat_kelahiran_jenazah` ("Tempat Kelahiran Jenazah")
- `tanggal_lahir_jenazah` ("Tanggal Lahir Jenazah")
- `ttl_jenazah` ("Tempat/Tanggal Lahir Jenazah")
- `umur_jenazah` ("Umur Jenazah")
- `agama_jenazah` ("Agama Jenazah")
- ... dan 24 lagi

---

### ⚠️ `SKTA` — Surat Keterangan Asal-Usul Tanah

- **Slug:** `land-origin-certificate-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `land-origin-certificate-letter.js`
- **Blade template:** `land-origin-certificate-letter.blade.php`
- **Field frontend:** 7 | **Field backend:** 12

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `certificateNumber` | Nomor Sertifikat Hak Milik | `text` |
| `areaInWords` | Luas (dengan huruf) | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `land_certificate_number` ("Nomor Letter C")
- `land_area_in_words` ("Luas dalam Huruf")
- `land_village` ("Kalurahan")
- `land_district` ("Kapanewon")
- `land_regency` ("Kabupaten")
- `land_price_min` ("Harga Tanah Terendah")
- `land_price_max` ("Harga Tanah Tertinggi")

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `measurementLetterDate` ("Tanggal surat ukur") → backend: `measurementLetterDate`

---

### ⚠️ `SPDP` — Surat Pernyataan Perubahan Data Penduduk

- **Slug:** `statement-population-data-change`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `statement-population-data-change.js`
- **Blade template:** `statement-population-data-change.blade.php`
- **Field frontend:** 10 | **Field backend:** 15

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `name` | Nama Lengkap | `text` |
| `nik` | NIK | `nik` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `applicant_name` ("Nama Pemohon")
- `applicant_nik` ("NIK Pemohon")
- `member_name` ("Nama Anggota Keluarga")
- `member_nik` ("NIK Anggota Keluarga")
- `member_shdk` ("Hubungan dalam Keluarga")
- `change_note` ("Catatan Perubahan")
- `other_element_label` ("Elemen Perubahan Lainnya")

---

### ⚠️ `FBWNI` — Formulir Biodata Penduduk WNI

- **Slug:** `family-biodata-form`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `(tidak ditemukan)`
- **Blade template:** `family-biodata-form.blade.php`
- **Field frontend:** 0 | **Field backend:** 11

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### 🕳️ Key `$form['...']'` di Blade Tidak Ada di Definisi Backend

> Key ini dibaca blade tapi tidak pernah disimpan ke `form_data` (tidak ada di seeder/katalog).

| Key di Blade |
|---|
| `members` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `head_name` ("Nama Kepala Keluarga")
- `address` ("Alamat Kepala Keluarga")
- `rt` ("RT")
- `rw` ("RW")
- `village` ("Desa/Kalurahan")
- `district` ("Kapanewon")
- `member_count` ("Jumlah Anggota Keluarga")
- `member_name` ("Nama Anggota Keluarga")
- `member_nik` ("NIK Anggota Keluarga")
- `kk_number` ("Nomor KK")
- ... dan 1 lagi

---

### ⚠️ `SKTMS` — Surat Keterangan Tidak Mampu Sekolah

- **Slug:** `sktm-school`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `sktm-school.js`
- **Blade template:** `sktm-school.blade.php`
- **Field frontend:** 11 | **Field backend:** 16

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `kkmNumber` | Nomor KKM/KRM | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `student_birth_place` ("Tempat Lahir Siswa")
- `student_birth_date` ("Tanggal Lahir Siswa")
- `student_address` ("Alamat Siswa")
- `purpose` ("Keperluan")
- `religion` ("Agama")
- `occupation` ("Pekerjaan")

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `studentBirthDate` ("Tanggal Lahir") → backend: `studentBirthDate`

---

### ⚠️ `SKAK` — Surat Kuasa

- **Slug:** `birth-certificate-power-of-attorney`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `birth-certificate-power-of-attorney.js`
- **Blade template:** `birth-certificate-power-of-attorney.blade.php`
- **Field frontend:** 4 | **Field backend:** 7

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `fatherName` | Nama Lengkap | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nama_pemberi_kuasa` ("Nama")
- `nama_penerima_kuasa` ("Nama")
- `pekerjaan_penerima_kuasa` ("Pekerjaan")
- `alamat_penerima_kuasa` ("Alamat")

---

### ⚠️ `SPTJMPSI` — Surat Pernyataan Tanggung Jawab Mutlak Kebenaran Sebagai Pasangan Suami Istri

- **Slug:** `spousal-relationship-responsibility-statement`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `spousal-relationship-responsibility-statement.js`
- **Blade template:** `spousal-relationship-responsibility-statement.blade.php`
- **Field frontend:** 1 | **Field backend:** 21

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `marriageWitnesses` | Saksi | `rows` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nama_pelapor` ("Nama")
- `nik_pelapor` ("NIK")
- `ttl_pelapor` ("Tempat/Tanggal Lahir")
- `pekerjaan_pelapor` ("Pekerjaan")
- `alamat_pelapor` ("Alamat")
- `status_hubungan_keluarga` ("Status Hubungan Keluarga dengan Pasangan")
- `nama_pihak_pertama` ("Nama")
- `nik_pihak_pertama` ("NIK")
- `ttl_pihak_pertama` ("Tempat/Tanggal Lahir")
- `pekerjaan_pihak_pertama` ("Pekerjaan")
- ... dan 11 lagi

---

### ⚠️ `SPAKL` — Surat Pengantar Akta Kelahiran

- **Slug:** `birth-certificate-referral-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `birth-certificate-referral-letter.js`
- **Blade template:** `birth-certificate-referral-letter.blade.php`
- **Field frontend:** 1 | **Field backend:** 6

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `childName` | Nama anak | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `gender` ("Jenis Kelamin")
- `religion` ("Agama")
- `occupation` ("Pekerjaan")
- `address` ("Alamat")

---

### ⚠️ `RL` — Register Legalisasi

- **Slug:** `legalization-register`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `legalization-register.js`
- **Blade template:** `legalization-register.blade.php`
- **Field frontend:** 2 | **Field backend:** 7

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `people` | Daftar penduduk | `rows` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `applicant_name` ("Nama Pemohon")
- `applicant_nik` ("NIK Pemohon")
- `legalization_date` ("Tanggal Legalisasi")
- `address` ("Alamat Pemohon")
- `notes` ("Keterangan")
- `rows` ("Daftar Penduduk yang Dilegalisasi")

---

### ⚠️ `SKBKN` — Surat Keterangan Belum Pernah Menikah

- **Slug:** `never-married-certificate-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `never-married-certificate-letter.js`
- **Blade template:** `never-married-certificate-letter.blade.php`
- **Field frontend:** 1 | **Field backend:** 6

#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)

> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.

| Key Form | Label | Tipe |
|---|---|---|
| `alias` | Nama alias (bila ada) | `text` |

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `gender` ("Jenis Kelamin")
- `religion` ("Agama")
- `occupation` ("Pekerjaan")
- `address` ("Alamat")

---

### ✅ `SKU` — business-permit-letter

- **Slug:** `business-permit-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `(tidak ditemukan)`
- **Blade template:** `business-permit-letter.blade.php`
- **Field frontend:** 0 | **Field backend:** 9

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `jenis_kelamin` ("Jenis Kelamin")
- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")
- `status_perkawinan` ("Status Perkawinan")
- `pekerjaan` ("Pekerjaan")
- `jenis_usaha` ("Jenis Usaha")
- `alamat_usaha` ("Alamat Usaha")
- `keperluan` ("Keperluan")
- `instansi_tujuan` ("Instansi Tujuan")

---

### ✅ `SKUMG` — general-statement-letter-gov

- **Slug:** `general-statement-letter-gov`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `(tidak ditemukan)`
- **Blade template:** `general-statement-letter-gov.blade.php`
- **Field frontend:** 0 | **Field backend:** 8

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `purpose` ("Keperluan")
- `destination_agency` ("Instansi Tujuan")
- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `gender` ("Jenis Kelamin")
- `religion` ("Agama")
- `occupation` ("Pekerjaan")
- `address` ("Alamat")

---

### ✅ `SKTM` — Surat Keterangan Tidak Mampu

- **Slug:** `sktm-general`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `sktm-general.js`
- **Blade template:** `sktm-general.blade.php`
- **Field frontend:** 3 | **Field backend:** 11

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")
- `jenis_kelamin` ("Jenis Kelamin")
- `status_perkawinan` ("Status Perkawinan")
- `agama` ("Agama")
- `pekerjaan` ("Pekerjaan")
- `keperluan` ("Keperluan")
- `instansi_tujuan` ("Instansi/Tujuan")

---

### ✅ `SKJ` — travel-permit-letter

- **Slug:** `travel-permit-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `(tidak ditemukan)`
- **Blade template:** `travel-permit-letter.blade.php`
- **Field frontend:** 0 | **Field backend:** 8

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")
- `jenis_kelamin` ("Jenis Kelamin")
- `status_perkawinan` ("Status Perkawinan")
- `agama` ("Agama")
- `pekerjaan` ("Pekerjaan")
- `pergi_ke` ("Pergi Ke")
- `maksud_dan_tujuan` ("Maksud dan Tujuan")

---

### ✅ `SKJV` — travel-permit-letter-vill

- **Slug:** `travel-permit-letter-vill`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `(tidak ditemukan)`
- **Blade template:** `travel-permit-letter-vill.blade.php`
- **Field frontend:** 0 | **Field backend:** 8

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `purpose` ("Keperluan")
- `destination_agency` ("Instansi Tujuan")
- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `gender` ("Jenis Kelamin")
- `religion` ("Agama")
- `occupation` ("Pekerjaan")
- `address` ("Alamat")

---

### ✅ `SPKIAF` — population-occurrence-registration-form

- **Slug:** `population-occurrence-registration-form`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `(tidak ditemukan)`
- **Blade template:** `population-occurrence-registration-form.blade.php`
- **Field frontend:** 0 | **Field backend:** 6

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `applicant_name` ("Nama Lengkap Pemohon")
- `applicant_nik` ("Nomor Induk Kependudukan")
- `kk_number` ("Nomor Kartu Keluarga")
- `application_types` ("Jenis Permohonan")
- `attached_documents` ("Persyaratan yang Dilampirkan")
- `application_date` ("Tanggal Pengajuan")

---

### ✅ `PNP` — Data Isian Pendaftaran Nikah

- **Slug:** `registration-form`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `registration-form.js`
- **Blade template:** `registration-form.blade.php`
- **Field frontend:** 0 | **Field backend:** 60

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `hari_akad` ("Hari Akad")
- `tanggal_akad` ("Tanggal Akad")
- `jam_akad` ("Jam Akad")
- `tempat_akad` ("Tempat Akad")
- `nama_catin_putri` ("Nama")
- `nik_catin_putri` ("NIK")
- `tempat_lahir_catin_putri` ("Tempat Lahir")
- `tanggal_lahir_catin_putri` ("Tanggal Lahir")
- `kewarganegaraan_catin_putri` ("Kewarganegaraan")
- `agama_catin_putri` ("Agama")
- ... dan 50 lagi

---

### ✅ `N2P` — Permohonan Kehendak Nikah

- **Slug:** `n2`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `n2.js`
- **Blade template:** `n2.blade.php`
- **Field frontend:** 0 | **Field backend:** 7

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `kepala_kua_tujuan` ("Ditujukan Kepada (Kepala KUA Kecamatan)")
- `nama_calon_suami` ("Nama Calon Suami")
- `nama_calon_istri` ("Nama Calon Istri")
- `hari_tanggal_akad` ("Hari/Tanggal Akad")
- `jam_akad` ("Jam Akad")
- `tempat_akad` ("Tempat Akad Nikah")
- `daftar_berkas_terlampir` ("Daftar Surat/Berkas yang Dilampirkan (otomatis dari upload)")

---

### ✅ `PAK` — Permohonan Akta Kelahiran

- **Slug:** `birth-certificate-application-form`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `birth-certificate-application-form.js`
- **Blade template:** `birth-certificate-application-form.blade.php`
- **Field frontend:** 0 | **Field backend:** 23

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_anak` ("NIK Anak")
- `nama_anak` ("Nama Lengkap")
- `tempat_lahir_anak` ("Tempat Lahir")
- `tanggal_lahir_anak` ("Tanggal Lahir")
- `jenis_kelamin_anak` ("Jenis Kelamin")
- `nik_ibu` ("NIK")
- `nama_ibu` ("Nama Lengkap")
- `alamat_ibu` ("Alamat")
- ... dan 13 lagi

---

### ✅ `FPK` — Formulir Pelaporan Kelahiran (Untuk Mendapatkan Akta Kelahiran)

- **Slug:** `birth-report-form`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `birth-report-form.js`
- **Blade template:** `birth-report-form.blade.php`
- **Field frontend:** 0 | **Field backend:** 47

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_anak` ("NIK")
- `nama_anak` ("Nama Lengkap")
- `jenis_kelamin_anak` ("Jenis Kelamin")
- `tempat_dilahirkan` ("Tempat Dilahirkan")
- `tempat_kelahiran_kota` ("Tempat Kelahiran (Kota)")
- `hari_tanggal_lahir` ("Hari/Tanggal Lahir")
- `jam_kelahiran` ("Waktu/Jam Kelahiran")
- `jenis_kelahiran` ("Jenis Kelahiran")
- ... dan 37 lagi

---

### ✅ `PPKT` — Surat Pencatatan Kelahiran Terlambat

- **Slug:** `late-birth-registration-approval-decree`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `late-birth-registration-approval-decree.js`
- **Blade template:** `late-birth-registration-approval-decree.blade.php`
- **Field frontend:** 0 | **Field backend:** 11

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_surat_pelaporan` ("Nomor Surat Pelaporan (dari Kelurahan)")
- `nama_pelapor` ("Nama Pelapor")
- `tanggal_surat_pelaporan` ("Tanggal Surat Pelaporan")
- `nama_anak` ("Nama")
- `nik_anak` ("NIK")
- `tempat_lahir_anak` ("Tempat Lahir")
- `tanggal_lahir_anak` ("Tanggal Lahir")
- `anak_ke` ("Anak Ke")
- `jenis_kelamin_anak` ("Jenis Kelamin")
- `nama_ibu` ("Nama Ibu")
- ... dan 1 lagi

---

### ✅ `LKLD` — Laporan Kelahiran Luar Domisili

- **Slug:** `out-of-domicile-birth-report`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `out-of-domicile-birth-report.js`
- **Blade template:** `out-of-domicile-birth-report.blade.php`
- **Field frontend:** 0 | **Field backend:** 48

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_anak` ("NIK")
- `nama_anak` ("Nama Lengkap")
- `jenis_kelamin_anak` ("Jenis Kelamin")
- `tempat_dilahirkan` ("Tempat Dilahirkan")
- `tempat_kelahiran_kota` ("Tempat Kelahiran (Kota)")
- `hari_tanggal_lahir` ("Hari/Tanggal Lahir")
- `jam_kelahiran` ("Waktu/Jam Kelahiran")
- `jenis_kelahiran` ("Jenis Kelahiran")
- ... dan 38 lagi

---

### ✅ `SPBMLP` — Surat Keterangan Belum Menikah Lagi

- **Slug:** `unmarried-statement`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `unmarried-statement.js`
- **Blade template:** `unmarried-statement.blade.php`
- **Field frontend:** 0 | **Field backend:** 8

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `jenis_kelamin` ("Jenis Kelamin")
- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")
- `kewarganegaraan` ("Kewarganegaraan")
- `agama` ("Agama")
- `pekerjaan` ("Pekerjaan")
- `pendidikan_terakhir` ("Pendidikan Terakhir")
- `status_perkawinan` ("Status Perkawinan")

---

### ✅ `SKDN` — Surat Pengantar Duplikat Nikah

- **Slug:** `marriage-certificate-duplicate-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `marriage-certificate-duplicate-letter.js`
- **Blade template:** `marriage-certificate-duplicate-letter.blade.php`
- **Field frontend:** 2 | **Field backend:** 8

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")
- `jenis_kelamin` ("Jenis Kelamin")
- `agama` ("Agama")
- `status_perkawinan` ("Status Perkawinan")
- `pekerjaan` ("Pekerjaan")

---

### ✅ `N1P` — Pengantar Nikah

- **Slug:** `n1`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `n1.js`
- **Blade template:** `n1.blade.php`
- **Field frontend:** 2 | **Field backend:** 23

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `jenis_kelamin` ("Jenis Kelamin")
- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")
- `kewarganegaraan` ("Kewarganegaraan")
- `agama` ("Agama")
- `pekerjaan` ("Pekerjaan")
- `pendidikan_terakhir` ("Pendidikan Terakhir")
- `nama_ayah` ("Nama Lengkap dan Alias")
- `nik_ayah` ("NIK")
- `ttl_ayah` ("Tempat dan Tanggal Lahir")
- ... dan 11 lagi

---

### ✅ `N4P` — Persetujuan Calon Pengantin

- **Slug:** `n4`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `n4.js`
- **Blade template:** `n4.blade.php`
- **Field frontend:** 0 | **Field backend:** 16

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nama_calon_suami` ("Nama Lengkap dan Alias")
- `bin_calon_suami` ("Bin")
- `nik_calon_suami` ("NIK")
- `ttl_calon_suami` ("Tempat dan Tanggal Lahir")
- `kewarganegaraan_calon_suami` ("Kewarganegaraan")
- `agama_calon_suami` ("Agama")
- `pekerjaan_calon_suami` ("Pekerjaan")
- `alamat_calon_suami` ("Alamat")
- `nama_calon_istri` ("Nama Lengkap dan Alias")
- `binti_calon_istri` ("Binti")
- ... dan 6 lagi

---

### ✅ `N5P` — Surat Izin Orang Tua

- **Slug:** `n5`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `n5.js`
- **Blade template:** `n5.blade.php`
- **Field frontend:** 0 | **Field backend:** 32

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nama_ayah` ("Nama Lengkap dan Alias")
- `bin_ayah` ("Bin")
- `nik_ayah` ("NIK")
- `ttl_ayah` ("Tempat dan Tanggal Lahir")
- `kewarganegaraan_ayah` ("Kewarganegaraan")
- `agama_ayah` ("Agama")
- `pekerjaan_ayah` ("Pekerjaan")
- `alamat_ayah` ("Alamat")
- `nama_ibu` ("Nama Lengkap dan Alias")
- `binti_ibu` ("Binti")
- ... dan 22 lagi

---

### ✅ `SKWHP` — Surat Keterangan Wali Hakim

- **Slug:** `judge-guardian`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `judge-guardian.js`
- **Blade template:** `judge-guardian.blade.php`
- **Field frontend:** 1 | **Field backend:** 17

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nama_mempelai_wanita` ("Nama Lengkap dan Alias")
- `binti_mempelai_wanita` ("Binti")
- `nik_mempelai_wanita` ("NIK")
- `ttl_mempelai_wanita` ("Tempat dan Tanggal Lahir")
- `kewarganegaraan_mempelai_wanita` ("Kewarganegaraan")
- `agama_mempelai_wanita` ("Agama")
- `pekerjaan_mempelai_wanita` ("Pekerjaan")
- `alamat_mempelai_wanita` ("Alamat")
- `nama_mempelai_pria` ("Nama Lengkap dan Alias")
- `bin_mempelai_pria` ("Bin")
- ... dan 6 lagi

---

### ✅ `SPTKP` — Surat Pengantar Tes Kesehatan

- **Slug:** `health-referral`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `health-referral.js`
- **Blade template:** `health-referral.blade.php`
- **Field frontend:** 6 | **Field backend:** 14

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `jenis_kelamin` ("Jenis Kelamin")
- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")
- `agama` ("Agama")
- `pekerjaan` ("Pekerjaan")
- `status_perkawinan` ("Status Perkawinan")
- `berlaku_mulai` ("Surat Berlaku Mulai Tanggal")
- `berlaku_sampai` ("Surat Berlaku Sampai Tanggal")

---

### ✅ `SKNNP` — numpang-nikah

- **Slug:** `numpang-nikah`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `(tidak ditemukan)`
- **Blade template:** `numpang-nikah.blade.php`
- **Field frontend:** 0 | **Field backend:** 15

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `bin_binti_warga` ("Bin/Binti")
- `tempat_lahir_warga` ("Tempat Lahir")
- `tanggal_lahir_warga` ("Tanggal Lahir")
- `kewarganegaraan_warga` ("Kewarganegaraan")
- `agama_warga` ("Agama")
- `pekerjaan_warga` ("Pekerjaan")
- `pendidikan_warga` ("Pendidikan Terakhir")
- `nama_calon_pasangan` ("Nama Lengkap dan Alias")
- `bin_binti_calon_pasangan` ("Bin/Binti")
- `nik_calon_pasangan` ("NIK")
- ... dan 5 lagi

---

### ✅ `N1L` — Pengantar Nikah

- **Slug:** `marriage-introduction-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `marriage-introduction-letter.js`
- **Blade template:** `marriage-introduction-letter.blade.php`
- **Field frontend:** 1 | **Field backend:** 23

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `jenis_kelamin` ("Jenis Kelamin")
- `tempat_lahir` ("Tempat Lahir")
- `tanggal_lahir` ("Tanggal Lahir")
- `kewarganegaraan` ("Kewarganegaraan")
- `agama` ("Agama")
- `pekerjaan` ("Pekerjaan")
- `pendidikan_terakhir` ("Pendidikan Terakhir")
- `status_perkawinan` ("Status")
- `nama_ayah` ("Nama Lengkap dan Alias")
- `nik_ayah` ("NIK")
- ... dan 12 lagi

---

### ✅ `N4L` — Persetujuan Calon Pengantin

- **Slug:** `bride-groom-consent-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `bride-groom-consent-letter.js`
- **Blade template:** `bride-groom-consent-letter.blade.php`
- **Field frontend:** 0 | **Field backend:** 16

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nama_calon_suami` ("Nama Lengkap dan Alias")
- `bin_calon_suami` ("Bin")
- `nik_calon_suami` ("NIK")
- `ttl_calon_suami` ("Tempat dan Tanggal Lahir")
- `kewarganegaraan_calon_suami` ("Kewarganegaraan")
- `agama_calon_suami` ("Agama")
- `pekerjaan_calon_suami` ("Pekerjaan")
- `alamat_calon_suami` ("Alamat")
- `nama_calon_istri` ("Nama Lengkap dan Alias")
- `binti_calon_istri` ("Binti")
- ... dan 6 lagi

---

### ✅ `SKKL` — Surat Keterangan Kelahiran

- **Slug:** `birth-attestation-letter`
- **Sumber data:** TEMPLATE_BY_CODE
- **File frontend:** `birth-attestation-letter.js`
- **Blade template:** `birth-attestation-letter.blade.php`
- **Field frontend:** 0 | **Field backend:** 47

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_anak` ("NIK")
- `nama_anak` ("Nama Lengkap")
- `jenis_kelamin_anak` ("Jenis Kelamin")
- `tempat_dilahirkan` ("Tempat Dilahirkan")
- `tempat_kelahiran_kota` ("Tempat Kelahiran (Kota)")
- `hari_tanggal_lahir` ("Hari/Tanggal Lahir")
- `jam_kelahiran` ("Waktu/Jam Kelahiran")
- `jenis_kelahiran` ("Jenis Kelahiran")
- ... dan 37 lagi

---

### ✅ `LPKL` — Laporan Pendaftaran Peristiwa Kelahiran

- **Slug:** `birth-registration-report`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `birth-registration-report.js`
- **Blade template:** `birth-registration-report.blade.php`
- **Field frontend:** 0 | **Field backend:** 10

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `kk_number` ("Nomor Kartu Keluarga")
- `nama_ayah` ("Nama Ayah")
- `nama_ibu` ("Nama Ibu")
- `tanggal_lapor` ("Tanggal Lapor")
- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `gender` ("Jenis Kelamin")
- `religion` ("Agama")
- `occupation` ("Pekerjaan")
- `address` ("Alamat")

---

### ✅ `DKF` — Formulir Pelaporan Kematian

- **Slug:** `death-report-form`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `death-report-form.js`
- **Blade template:** `death-report-form.blade.php`
- **Field frontend:** 0 | **Field backend:** 34

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_jenazah` ("NIK Jenazah")
- `nama_jenazah` ("Nama Jenazah")
- `jenis_kelamin_jenazah` ("Jenis Kelamin Jenazah")
- `tempat_kelahiran_jenazah` ("Tempat Kelahiran Jenazah")
- `tanggal_lahir_jenazah` ("Tanggal Lahir Jenazah")
- `ttl_jenazah` ("Tempat/Tanggal Lahir Jenazah")
- `umur_jenazah` ("Umur Jenazah")
- `agama_jenazah` ("Agama Jenazah")
- ... dan 24 lagi

---

### ✅ `DKA` — Permohonan Akta Kematian

- **Slug:** `death-certificate-application`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `death-certificate-application.js`
- **Blade template:** `death-certificate-application.blade.php`
- **Field frontend:** 0 | **Field backend:** 34

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_jenazah` ("NIK Jenazah")
- `nama_jenazah` ("Nama Jenazah")
- `jenis_kelamin_jenazah` ("Jenis Kelamin Jenazah")
- `tempat_kelahiran_jenazah` ("Tempat Kelahiran Jenazah")
- `tanggal_lahir_jenazah` ("Tanggal Lahir Jenazah")
- `ttl_jenazah` ("Tempat/Tanggal Lahir Jenazah")
- `umur_jenazah` ("Umur Jenazah")
- `agama_jenazah` ("Agama Jenazah")
- ... dan 24 lagi

---

### ✅ `DKR` — Laporan Kematian

- **Slug:** `death-report`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `death-report.js`
- **Blade template:** `death-report.blade.php`
- **Field frontend:** 0 | **Field backend:** 34

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_jenazah` ("NIK Jenazah")
- `nama_jenazah` ("Nama Jenazah")
- `jenis_kelamin_jenazah` ("Jenis Kelamin Jenazah")
- `tempat_kelahiran_jenazah` ("Tempat Kelahiran Jenazah")
- `tanggal_lahir_jenazah` ("Tanggal Lahir Jenazah")
- `ttl_jenazah` ("Tempat/Tanggal Lahir Jenazah")
- `umur_jenazah` ("Umur Jenazah")
- `agama_jenazah` ("Agama Jenazah")
- ... dan 24 lagi

---

### ✅ `DKS` — SPTJM Kebenaran Data Kematian

- **Slug:** `death-data-statement`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `death-data-statement.js`
- **Blade template:** `death-data-statement.blade.php`
- **Field frontend:** 0 | **Field backend:** 34

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_jenazah` ("NIK Jenazah")
- `nama_jenazah` ("Nama Jenazah")
- `jenis_kelamin_jenazah` ("Jenis Kelamin Jenazah")
- `tempat_kelahiran_jenazah` ("Tempat Kelahiran Jenazah")
- `tanggal_lahir_jenazah` ("Tanggal Lahir Jenazah")
- `ttl_jenazah` ("Tempat/Tanggal Lahir Jenazah")
- `umur_jenazah` ("Umur Jenazah")
- `agama_jenazah` ("Agama Jenazah")
- ... dan 24 lagi

---

### ✅ `DKP` — Surat Kuasa Permohonan Akta Kematian

- **Slug:** `death-power-of-attorney`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `death-power-of-attorney.js`
- **Blade template:** `death-power-of-attorney.blade.php`
- **Field frontend:** 4 | **Field backend:** 34

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_jenazah` ("NIK Jenazah")
- `jenis_kelamin_jenazah` ("Jenis Kelamin Jenazah")
- `tempat_kelahiran_jenazah` ("Tempat Kelahiran Jenazah")
- `tanggal_lahir_jenazah` ("Tanggal Lahir Jenazah")
- `ttl_jenazah` ("Tempat/Tanggal Lahir Jenazah")
- `umur_jenazah` ("Umur Jenazah")
- `agama_jenazah` ("Agama Jenazah")
- `pekerjaan_jenazah` ("Pekerjaan Jenazah")
- ... dan 20 lagi

---

### ✅ `DKC` — Pelaporan Pencatatan Kematian

- **Slug:** `death-registration-report`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `death-registration-report.js`
- **Blade template:** `death-registration-report.blade.php`
- **Field frontend:** 0 | **Field backend:** 34

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_jenazah` ("NIK Jenazah")
- `nama_jenazah` ("Nama Jenazah")
- `jenis_kelamin_jenazah` ("Jenis Kelamin Jenazah")
- `tempat_kelahiran_jenazah` ("Tempat Kelahiran Jenazah")
- `tanggal_lahir_jenazah` ("Tanggal Lahir Jenazah")
- `ttl_jenazah` ("Tempat/Tanggal Lahir Jenazah")
- `umur_jenazah` ("Umur Jenazah")
- `agama_jenazah` ("Agama Jenazah")
- ... dan 24 lagi

---

### ✅ `DKH` — Perhitungan Selamatan Kematian

- **Slug:** `death-commemoration-calculation`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `death-commemoration-calculation.js`
- **Blade template:** `death-commemoration-calculation.blade.php`
- **Field frontend:** 0 | **Field backend:** 34

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_jenazah` ("NIK Jenazah")
- `nama_jenazah` ("Nama Jenazah")
- `jenis_kelamin_jenazah` ("Jenis Kelamin Jenazah")
- `tempat_kelahiran_jenazah` ("Tempat Kelahiran Jenazah")
- `tanggal_lahir_jenazah` ("Tanggal Lahir Jenazah")
- `ttl_jenazah` ("Tempat/Tanggal Lahir Jenazah")
- `umur_jenazah` ("Umur Jenazah")
- `agama_jenazah` ("Agama Jenazah")
- ... dan 24 lagi

---

### ✅ `SKBK2` — Surat Keterangan Belum Pernah Menikah

- **Slug:** `unmarried-certificate`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `unmarried-certificate.js`
- **Blade template:** `unmarried-certificate.blade.php`
- **Field frontend:** 0 | **Field backend:** 6

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `birth_place` ("Tempat Lahir")
- `birth_date` ("Tanggal Lahir")
- `gender` ("Jenis Kelamin")
- `religion` ("Agama")
- `occupation` ("Pekerjaan")
- `address` ("Alamat")

---

### ✅ `SPCCP` — Surat Persetujuan Calon Pengantin

- **Slug:** `bride-groom-consent-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `bride-groom-consent-letter.js`
- **Blade template:** `bride-groom-consent-letter.blade.php`
- **Field frontend:** 0 | **Field backend:** 135

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `education` ("Pendidikan Terakhir")
- `nationality` ("Kewarganegaraan")
- `name_alias` ("Nama Alias")
- `marital_status` ("Status Perkawinan")
- `groom_name` ("Nama Calon Suami")
- `groom_bin` ("Bin Calon Suami")
- `groom_nik` ("NIK Calon Suami")
- `groom_birth_place` ("Tempat Lahir Calon Suami")
- `groom_birth_date` ("Tanggal Lahir Calon Suami")
- `groom_citizenship` ("Kewarganegaraan Calon Suami")
- ... dan 125 lagi

---

### ✅ `SKKMP` — Surat Keterangan Kematian untuk Keperluan Nikah

- **Slug:** `death-certificate-for-marriage-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `death-certificate-for-marriage-letter.js`
- **Blade template:** `death-certificate-for-marriage-letter.blade.php`
- **Field frontend:** 13 | **Field backend:** 135

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `education` ("Pendidikan Terakhir")
- `name_alias` ("Nama Alias")
- `marital_status` ("Status Perkawinan")
- `groom_name` ("Nama Calon Suami")
- `groom_bin` ("Bin Calon Suami")
- `groom_nik` ("NIK Calon Suami")
- `groom_birth_place` ("Tempat Lahir Calon Suami")
- `groom_birth_date` ("Tanggal Lahir Calon Suami")
- `groom_citizenship` ("Kewarganegaraan Calon Suami")
- `groom_religion` ("Agama Calon Suami")
- ... dan 112 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `deathDate` ("Tanggal Meninggal") → backend: `deathDate`
- `deceasedBirthDate` ("Tanggal Lahir") → backend: `deceasedBirthDate`

---

### ✅ `SKUMN` — Surat Keterangan Umum Pernikahan

- **Slug:** `general-certificate-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `general-certificate-letter.js`
- **Blade template:** `general-certificate-letter.blade.php`
- **Field frontend:** 5 | **Field backend:** 135

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `name_alias` ("Nama Alias")
- `marital_status` ("Status Perkawinan")
- `groom_name` ("Nama Calon Suami")
- `groom_bin` ("Bin Calon Suami")
- `groom_nik` ("NIK Calon Suami")
- `groom_birth_place` ("Tempat Lahir Calon Suami")
- `groom_birth_date` ("Tanggal Lahir Calon Suami")
- `groom_citizenship` ("Kewarganegaraan Calon Suami")
- `groom_religion` ("Agama Calon Suami")
- `groom_occupation` ("Pekerjaan Calon Suami")
- ... dan 120 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `validUntil` ("Surat pengantar berlaku sampai tanggal") → backend: `validUntil`

---

### ✅ `SPNIK` — Surat Permohonan Pernikahan

- **Slug:** `marriage-application-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `marriage-application-letter.js`
- **Blade template:** `marriage-application-letter.blade.php`
- **Field frontend:** 0 | **Field backend:** 135

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `education` ("Pendidikan Terakhir")
- `nationality` ("Kewarganegaraan")
- `name_alias` ("Nama Alias")
- `marital_status` ("Status Perkawinan")
- `groom_name` ("Nama Calon Suami")
- `groom_bin` ("Bin Calon Suami")
- `groom_nik` ("NIK Calon Suami")
- `groom_birth_place` ("Tempat Lahir Calon Suami")
- `groom_birth_date` ("Tanggal Lahir Calon Suami")
- `groom_citizenship` ("Kewarganegaraan Calon Suami")
- ... dan 125 lagi

---

### ✅ `SPNIP` — Surat Pengantar Pernikahan

- **Slug:** `marriage-introduction-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `marriage-introduction-letter.js`
- **Blade template:** `marriage-introduction-letter.blade.php`
- **Field frontend:** 1 | **Field backend:** 135

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `education` ("Pendidikan Terakhir")
- `nationality` ("Kewarganegaraan")
- `name_alias` ("Nama Alias")
- `marital_status` ("Status Perkawinan")
- `groom_name` ("Nama Calon Suami")
- `groom_bin` ("Bin Calon Suami")
- `groom_nik` ("NIK Calon Suami")
- `groom_birth_place` ("Tempat Lahir Calon Suami")
- `groom_birth_date` ("Tanggal Lahir Calon Suami")
- `groom_citizenship` ("Kewarganegaraan Calon Suami")
- ... dan 124 lagi

---

### ✅ `SKTNB` — Surat Keterangan Menumpang Nikah

- **Slug:** `marriage-lodging-certificate-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `marriage-lodging-certificate-letter.js`
- **Blade template:** `marriage-lodging-certificate-letter.blade.php`
- **Field frontend:** 7 | **Field backend:** 135

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `education` ("Pendidikan Terakhir")
- `nationality` ("Kewarganegaraan")
- `name_alias` ("Nama Alias")
- `marital_status` ("Status Perkawinan")
- `groom_name` ("Nama Calon Suami")
- `groom_bin` ("Bin Calon Suami")
- `groom_nik` ("NIK Calon Suami")
- `groom_birth_place` ("Tempat Lahir Calon Suami")
- `groom_birth_date` ("Tanggal Lahir Calon Suami")
- `groom_citizenship` ("Kewarganegaraan Calon Suami")
- ... dan 118 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `spouseBirthDate` ("Tanggal Lahir") → backend: `spouseBirthDate`

---

### ✅ `SPBML` — Surat Pernyataan Tidak Menikah Lagi

- **Slug:** `not-remarried-statement-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `not-remarried-statement-letter.js`
- **Blade template:** `not-remarried-statement-letter.blade.php`
- **Field frontend:** 2 | **Field backend:** 135

#### ✅ Semua field frontend berhasil dipetakan ke backend

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `education` ("Pendidikan Terakhir")
- `nationality` ("Kewarganegaraan")
- `name_alias` ("Nama Alias")
- `marital_status` ("Status Perkawinan")
- `groom_name` ("Nama Calon Suami")
- `groom_bin` ("Bin Calon Suami")
- `groom_nik` ("NIK Calon Suami")
- `groom_birth_place` ("Tempat Lahir Calon Suami")
- `groom_birth_date` ("Tanggal Lahir Calon Suami")
- `groom_citizenship` ("Kewarganegaraan Calon Suami")
- ... dan 123 lagi

#### 📅 Field Date/Time (Rentan Bug A — Multipart)

> Jika ada lampiran → surat dikirim multipart → `appendNested()` tidak dipanggil untuk File,
> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread
> menjadi properti kosong dan nilainya hilang.

- `registrationDate` ("Tanggal pendaftaran") → backend: `registrationDate`

---

### ✅ `SPOT` — Surat Persetujuan Orang Tua

- **Slug:** `parental-consent-letter`
- **Sumber data:** AdditionalCatalog
- **File frontend:** `parental-consent-letter.js`
- **Blade template:** `parental-consent-letter.blade.php`
- **Field frontend:** 0 | **Field backend:** 135

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `education` ("Pendidikan Terakhir")
- `nationality` ("Kewarganegaraan")
- `name_alias` ("Nama Alias")
- `marital_status` ("Status Perkawinan")
- `groom_name` ("Nama Calon Suami")
- `groom_bin` ("Bin Calon Suami")
- `groom_nik` ("NIK Calon Suami")
- `groom_birth_place` ("Tempat Lahir Calon Suami")
- `groom_birth_date` ("Tanggal Lahir Calon Suami")
- `groom_citizenship` ("Kewarganegaraan Calon Suami")
- ... dan 125 lagi

---

### ✅ `SKKM` — (tidak ada)

- **Slug:** `(tidak ada)`
- **Sumber data:** SeederOnly
- **File frontend:** `(tidak ditemukan)`
- **Blade template:** `(tidak ditemukan)`
- **Field frontend:** 0 | **Field backend:** 48

#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing

#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)

- `nomor_kk` ("Nomor Kartu Keluarga")
- `nama_kepala_keluarga` ("Nama Kepala Keluarga")
- `nik_jenazah` ("NIK")
- `nama_jenazah` ("Nama Lengkap")
- `jenis_kelamin_jenazah` ("Jenis Kelamin")
- `tempat_kelahiran_jenazah` ("Tempat Kelahiran")
- `tanggal_lahir_jenazah` ("Tanggal Lahir")
- `umur_jenazah` ("Umur")
- `agama_jenazah` ("Agama")
- `pekerjaan_jenazah` ("Pekerjaan")
- ... dan 38 lagi

---

## 🔬 ANALISIS AKAR MASALAH

### Bug A — Date Hilang saat Multipart (appendNested + Date Object)

**Status:** ⚠️ TERKONFIRMASI PARSIAL

**Mekanisme:** `appendNested(fd, key, value)` hanya cek `instanceof File`. Jika value adalah objek Date (bukan string ISO), `Object.entries(dateObj)` menghasilkan array kosong → tanggal hilang diam-diam.

**Kapan terjadi:** Surat dengan lampiran (multipart) + datepicker Vue yang menghasilkan Date object alih-alih string ISO.

**Surat berisiko (25 surat):**
- `SPPWNI` (relocation-cover-letter): birthDate
- `SPPWF` (relocation-certificate-form): birthDate
- `SPSKG` (lease-offer-letter): requestDate, rentStartDate
- `SBP` (research-response-letter): requestDate, researchStartDate, researchEndDate
- `PNL` (marriage-registration-data-sheet): groomPrevDeathDate, bridePrevDeathDate, groomPrevBirthDate
- `FPDN` (marriage-registration-data-sheet): groomPrevDeathDate, bridePrevDeathDate, groomPrevBirthDate
- `SMLPI` (permit-followup-letter): requestDate, followUpDate
- `SKWKA` (heir-power-of-attorney-letter): birthDate, attorneyBirthDate
- `SKDT` (resident-arrival-form): arrivalDate
- `SKK` (event-permit-letter): eventDate, eventTime
- `N6P` (n6): exHusbandDiedAt, exHusbandBirthDate
- `SKWNP` (guardian-statement): guardianBirthDate
- `SKBK` (unmarried-status-letter): applicantBirthDate
- `SPPD` (duty-travel-order-letter): departureDate, returnDueDate
- `SPTMDK` (population-document-statement-letter): birthDate
- `SPU` (general-cover-letter): birthDate
- `SPKIA` (kia-application-form): birthDate
- `SKDPAK` (population-service-authorization-letter): birthDate, attorneyBirthDate
- `SKTA` (land-origin-certificate-letter): measurementLetterDate
- `SKTMS` (sktm-school): studentBirthDate
- `SPTJP` (unregistered-marriage-responsibility-letter): marriageDate, husbandBirthDate, wifeBirthDate
- `SKKMP` (death-certificate-for-marriage-letter): deathDate, deceasedBirthDate
- `SKUMN` (general-certificate-letter): validUntil
- `SKTNB` (marriage-lodging-certificate-letter): spouseBirthDate
- `SPBML` (not-remarried-statement-letter): registrationDate

### Bug B — Label Generik Ambigu (mapFieldsToBackend step "label persis")

**Status:** 🔴 TERKONFIRMASI

**Mekanisme:** Label umum seperti "Tempat Lahir", "Agama", "Pekerjaan", "Tanggal Lahir" muncul di banyak blok.
Saat `mapFieldsToBackend()` mencari via "label persis", ia menemukan field `tempat_lahir` (pemohon) dahulu
dan mengklaim slot itu — sehingga `groomBirthPlace` (label "Tempat Lahir") tidak bisa dipetakan ke `groom_birth_place` lagi.

**Efek:** Blok calon suami/istri, ayah, ibu, saksi kehilangan data tempat lahir, agama, pekerjaan.

**Surat paling terdampak:** Semua surat nikah (N1, N2, N4, N5, SPNIP, SPOT, dll.) dan surat kematian dengan data ibu/ayah.

**Label ambigu yang muncul berkali-kali dalam satu surat:**
- "Nama Lengkap" → dalam surat: PNL, FPDN, SKWKA, SKDPAK, SPTJP
- "NIK" → dalam surat: PNL, FPDN, SKWKA, SKC, SKDPAK, SPTJP
- "Tempat Lahir" → dalam surat: SKWKA, SKDPAK, SPTJP
- "Tanggal Lahir" → dalam surat: SKWKA, SKDPAK, SPTJP
- "Jenis Kelamin" → dalam surat: SKWKA, SKC
- "Alamat" → dalam surat: SKWKA, SKC, SKDPAK, SPTJP
- "Nama" → dalam surat: SKC
- "Pekerjaan" → dalam surat: SKDPAK, SPTJP

### Bug C — Field Backend Kurang dari Kebutuhan Blade

**Status:** 🔴 TERKONFIRMASI

**Mekanisme:** Seeder/katalog mendefinisikan field backend yang tidak mencukupi semua yang dibutuhkan blade.
Contoh: `groom_father_birth_date`, `groom_father_religion`, `groom_father_occupation` tidak ada di `marriedPersonFields()`.

**Surat dengan key blade yang tidak punya backing backend (7 surat):**
- `SPPWNI` (relocation-cover-letter): origin, kk_number, kkNumber, origin_kk_number, originKkNumber, head_of_family, headOfFamily, origin_head_of_family, originHeadOfFamily, nik, name, full_name, birth_place, birthPlace, birth_date, birthDate, relocation_type_label, registration_officer
- `SPPWF` (relocation-certificate-form): origin, kk_number, kkNumber, originKkNumber, head_of_family, headOfFamily, originHeadOfFamily, nik, name, full_name, birthPlace, birthDate, relocation_type_label
- `SRBBM` (fuel-recommendation-letter): consumer, fuel_items, total, volume_allocation, pickup_location, distributor_number, distributor_location
- `SKDT` (resident-arrival-form): origin, destination, registration_officer
- `SGC` (divorce-lawsuit-letter): court_name, court_city
- `SKCK` (skck-referral-letter): occupation_note
- `FBWNI` (family-biodata-form): members

### Bug D — Key Backend ≠ Key yang Dibaca Blade

**Status:** 🔴 TERKONFIRMASI

**Mekanisme:** `normalizeRootTemplateForm()` dan `normalizeComplexTemplateForm()` menambahkan alias,
tapi tidak semua konversi tercakup. Field backend `groom_citizenship` vs blade membaca `groom_nationality`.

**Contoh spesifik yang ditemukan:**
- Katalog menyimpan `groom_citizenship` tapi blade membaca `groom_citizenship` → OK, tapi normalizer alias di service mengubah `groomNationality` → `groom_citizenship`
- Surat kematian: frontend memakai `deathDate` (tanggal meninggal pemohon), backend punya `meninggal_hari_tanggal` → perlu override eksplisit yang sudah ada
- Surat pernikahan lama (N1-N6): blade membaca `$marriage['...']['...'])` (nested) tapi form_data menyimpan flat

### Bug E — Nilai Checkbox/Select Disimpan sebagai Label Teks

**Status:** 🟡 PERLU VERIFIKASI

**Mekanisme:** Form frontend menyimpan value select sebagai teks label (mis. "1. Numpang KK"), sedangkan blade atau backend mengharapkan kode numerik atau value bersih.
`matchBackendOption()` sudah mencoba menangani ini, tapi hanya jika backend mendefinisikan `options`.

**Surat berisiko:** `relocation-cover-letter` (originKkStatus, destinationKkStatus), `ktp-application-form` (applicationType)

### Bug F — Definisi Field di Seeder Tertinggal dari Katalog

**Status:** 🔴 TERKONFIRMASI

**Mekanisme:** `AdditionalLetterTemplateCatalog.php` mendefinisikan field terbaru, tapi `LetterTypeFieldSeeder.php` mungkin belum diperbarui (atau sebaliknya). Bila seeder pernah dijalankan dengan data lama, DB punya field lama; bila field baru hanya di katalog PHP tapi belum di-seed ulang, `getFields()` mengembalikan list lama.

**Ada di katalog tapi TIDAK di seeder:** SPAKL, LPKL, DKF, DKA, DKR, DKU, DKS, DKP, DKC, DKH, FBWNI, SKUMG, SKWKA, SPKIAF, RL, SKTA, SKTH, SPKLC, SKC, SKBK2, SPCCP, SKKMP, SKUMN, SPNIK, SPNIP, SKTNB, FPDN, SKBKN, SPBML, SPOT, SPPWF, SKDT, SKTMS, SPDP, SKTS, SPTTS, SKJG, SKJV, SPTJP

**Ada di seeder tapi TIDAK di katalog/template:** SKKM

### Bug G — BARU: Field Frontend Tidak Punya File JS (Tidak Diaudit)

**Status:** 🔴 DITEMUKAN

**Surat dengan backend terdefinisi tapi tidak ada file frontend JS:**
- `FBWNI` (`family-biodata-form`) — Formulir Biodata Penduduk WNI
- `SKU` (`business-permit-letter`) — business-permit-letter
- `SKUMG` (`general-statement-letter-gov`) — general-statement-letter-gov
- `SKJ` (`travel-permit-letter`) — travel-permit-letter
- `SKJV` (`travel-permit-letter-vill`) — travel-permit-letter-vill
- `SPKIAF` (`population-occurrence-registration-form`) — population-occurrence-registration-form
- `SKNNP` (`numpang-nikah`) — numpang-nikah
- `SKKM` (`(tidak ada)`) — -

Surat ini tidak dapat diaudit field frontendnya karena file JS tidak ditemukan di `data/letters/`.
Kemungkinan: (1) field didefinisikan di letterFields.js langsung tanpa file per-surat, atau (2) slug tidak cocok.

### Bug H — BARU: Duplikasi Key `registration_number` di `marriedPersonFields()`

**Status:** 🔴 DITEMUKAN

**Mekanisme:** `AdditionalLetterTemplateCatalog::marriedPersonFields()` mendefinisikan `registration_number` DUA KALI (baris 331 dan 405). Hal ini menyebabkan ambiguitas dan salah satu definisi tidak terpakai.

### Bug I — BARU: slug di letterIndex tidak cocok dengan slug di TEMPLATE_BY_CODE

**Status:** 🔴 DITEMUKAN

**Contoh:**
- letterIndex menggunakan `bussiness-permit-letter` (typo: bussiness), TEMPLATE_BY_CODE menggunakan `business-permit-letter`
- letterIndex menggunakan `population-occurrence-registration`, TEMPLATE_BY_CODE menggunakan `population-occurrence-registration-form`
- letterIndex menggunakan `family-biodata`, katalog menggunakan `family-biodata-form`

Akibatnya: `resolveLetterType()` gagal menemukan tipe surat di backend → error "Tipe surat ... belum terdaftar di backend"

### Bug J — BARU: normalizeRootTemplateForm tidak menangani semua kode

**Status:** 🔴 DITEMUKAN

**Mekanisme:** `normalizeComplexTemplateForm()` hanya memproses kode tertentu dalam array `$marriageCodes`. Kode katalog baru seperti `SPCCP`, `SKKMP`, `SKUMN`, dll. sudah masuk array itu, TAPI kode `SKBK2`, `DKU`, `DKS`, `DKP`, `DKH`, `SPKLC`, `SKC`, dll. dari katalog tidak punya normalizer khusus DAN tidak masuk `TEMPLATE_BY_CODE`, sehingga `viewData()` tidak tahu template mana yang dipakai.

