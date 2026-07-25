# SIBIMO

Backend API untuk **SIBIMO (Sistem Informasi Kelurahan Bimomartani)** yang dibangun menggunakan **Laravel**.

Repository ini merupakan pusat layanan (REST API) yang digunakan oleh seluruh aplikasi pada ekosistem SIBIMO, meliputi website publik, layanan surat warga, dan dashboard administrasi.

---

## Tentang Proyek

SIBIMO merupakan sistem informasi digital yang dikembangkan untuk mendukung pelayanan administrasi dan penyebaran informasi di Kelurahan Bimomartani.

Melalui sistem ini, masyarakat dapat memperoleh informasi mengenai kelurahan sekaligus mengajukan layanan administrasi secara daring. Di sisi lain, perangkat kelurahan dapat mengelola konten website, memproses pengajuan surat, serta mengelola data master melalui dashboard admin.

Repository ini hanya berisi **Backend REST API**. Seluruh antarmuka pengguna (Frontend) dikembangkan pada repository terpisah.

---

## Arsitektur Sistem

```
                    +----------------------+
                    |     SIBIMO API       |
                    |  Laravel REST API    |
                    +----------+-----------+
                               |
             +-----------------+-----------------+
             |                                   |
    +--------v---------+               +---------v---------+
    |  sibimo-admin    |               |  sibimo-public    |
    |  Vue.js          |               |  Vue.js           |
    |                  |               |                   |
    | Dashboard Admin  |               | Website Publik    |
    | CMS              |               | Layanan Surat     |
    | Manajemen Surat  |               | Informasi Desa    |
    +------------------+               +-------------------+

                    MySQL Database
```

---

## Repository Ekosistem

| Repository | Deskripsi |
|------------|-----------|
| **sibimo** | Backend REST API menggunakan Laravel |
| **sibimo-admin** | Dashboard administrasi berbasis Vue.js |
| **sibimo-public** | Website publik dan layanan surat berbasis Vue.js |

---

## Tech Stack

### Backend

- Laravel 13
- MySQL
- REST API

### Development

- Composer
- Node.js
- Vite
- Git

---

## License

Copyright © Dejico ID.