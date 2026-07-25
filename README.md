# SIBIMO Backend

Backend API untuk **SIBIMO (Sistem Informasi Kelurahan Bimomartani)** yang dibangun menggunakan **Laravel**. Repository ini menjadi pusat layanan (API) yang digunakan oleh seluruh aplikasi frontend dalam ekosistem SIBIMO.

## Tentang Proyek

SIBIMO merupakan sistem informasi digital yang dikembangkan untuk mendukung pelayanan administrasi dan penyebaran informasi di Kelurahan Bimomartani.

Proyek ini terdiri dari beberapa bagian utama:

* **Sistem Layanan Surat** untuk membantu warga mengajukan dan memantau permohonan surat secara online.
* **Website Profil Kelurahan** sebagai media informasi resmi yang menampilkan profil, berita, pengumuman, agenda, dan informasi publik lainnya.
* **Content Management System (CMS)** yang digunakan oleh perangkat kelurahan untuk mengelola konten website dan data pelayanan.

Repository ini hanya berisi **backend API**, sedangkan aplikasi frontend dikembangkan pada repository terpisah.

---

# Arsitektur

```text
                 +----------------------+
                 |   Laravel Backend    |
                 |      (REST API)      |
                 +----------+-----------+
                            |
          +-----------------+-----------------+
          |                                   |
          |                                   |
+---------v---------+             +-----------v-----------+
| Layanan Surat     |             | Website & CMS         |
| (Vue.js)          |             | (Vue.js)              |
+-------------------+             +-----------------------+

                Database MySQL
```

---

# Tech Stack

## Backend

* Laravel
* MySQL
* REST API

## Development Tools

* Composer
* Node.js
* Git
* Vite

---

# License

Proyek ini dikembangkan untuk Kelurahan Bimomartani dan digunakan sebagai sistem pelayanan administrasi serta media informasi resmi.
