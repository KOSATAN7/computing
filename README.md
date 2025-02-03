# 📌 Manajemen Venue & Pertandingan API

![Laravel](https://img.shields.io/badge/Laravel-8.x-red?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-^8.0-blue?style=for-the-badge&logo=php)
![License](https://img.shields.io/badge/license-MIT-green)

## 📖 **Tentang API Ini**
API ini digunakan untuk mengelola **venue, pertandingan, pembayaran, dan pengguna** dalam sistem berbasis Laravel. API ini menggunakan **Sanctum untuk autentikasi**, serta middleware untuk membatasi akses berdasarkan peran pengguna.

---

## 🔑 **Autentikasi**
| Method | Endpoint | Deskripsi |
|--------|---------|-----------|
| `POST` | `/register-superadmin` | Registrasi Superadmin |
| `POST` | `/register-infobar` | Registrasi Infobar |
| `POST` | `/login` | Login |
| `GET` | `/check-login` | Cek status login |
| `POST` | `/logout` | Logout |

**📌 Contoh Login Request**
```json
{
    "email": "superadmin@example.com",
    "password": "password123"
}
```

---

## 🏟️ **Superadmin - Kelola Venue**
| Method | Endpoint | Deskripsi |
|--------|---------|-----------|
| `POST` | `/venue` | Buat Venue baru |
| `GET` | `/venue` | Ambil semua Venue |
| `PUT` | `/venue/{id}` | Ubah data Venue |
| `PATCH` | `/venue/status/{id}` | Ubah status Venue |
| `DELETE` | `/venue/{id}` | Hapus Venue |

---

## 🎟️ **Superadmin - Kelola Pertandingan**
| Method | Endpoint | Deskripsi |
|--------|---------|-----------|
| `POST` | `/pertandingan` | Buat pertandingan baru |
| `GET` | `/pertandingan` | Ambil semua pertandingan |
| `PUT` | `/pertandingan/{id}` | Ubah pertandingan |
| `PATCH` | `/pertandingan/status/{id}` | Ubah status pertandingan |
| `DELETE` | `/pertandingan/{id}` | Hapus pertandingan |

---

## 💳 **Superadmin - Kelola Metode Pembayaran**
| Method | Endpoint | Deskripsi |
|--------|---------|-----------|
| `GET` | `/metode-pembayaran` | Ambil semua metode pembayaran |
| `GET` | `/metode-pembayaran/{id}` | Detail metode pembayaran |
| `POST` | `/metode-pembayaran` | Tambah metode pembayaran |
| `PUT` | `/metode-pembayaran/{id}` | Ubah metode pembayaran |
| `PATCH` | `/metode-pembayaran/{id}/status` | Ubah status metode pembayaran |
| `DELETE` | `/metode-pembayaran/{id}` | Hapus metode pembayaran |

---

## ⚽ **Admin Venue - Kelola Pertandingan Venue**
| Method | Endpoint | Deskripsi |
|--------|---------|-----------|
| `POST` | `/konten/venue/{venueId}` | Tambah pertandingan ke venue |
| `GET` | `/konten/venue/{venueId}` | Ambil semua pertandingan di venue |
| `DELETE` | `/konten/venue/{venueId}` | Hapus pertandingan dari venue |

---

## 🍔 **Admin Venue - Kelola Menu**
| Method | Endpoint | Deskripsi |
|--------|---------|-----------|
| `POST` | `/menu/venue/{venueId}` | Tambah menu |
| `GET` | `/menu/venue/{venueId}` | Ambil semua menu di venue |
| `GET` | `/menu/venue/{venueId}/{menuId}` | Ambil detail menu |
| `PUT` | `/menu/venue/{venueId}/{menuId}` | Ubah menu |
| `PATCH` | `/menu/venue/{venueId}/status/{menuId}` | Ubah status menu |
| `DELETE` | `/menu/venue/{venueId}/{menuId}` | Hapus menu |

---

## 📌 **Umum - Pertandingan & Venue**
| Method | Endpoint | Deskripsi |
|--------|---------|-----------|
| `GET` | `/konten/aktif` | Ambil semua pertandingan aktif |
| `GET` | `/konten/{id}` | Ambil detail pertandingan |
| `GET` | `/venue/aktif` | Ambil semua venue aktif |
| `GET` | `/venue/{id}` | Detail venue |

---

## 🚀 **Cara Menjalankan**
1. **Clone Repository**
```bash
git clone https://github.com/username/repository-name.git
cd repository-name
```
2. **Jalankan Server**
```bash
php artisan serve
```
3. **Jalankan Seeder**
```bash
php artisan migrate:fresh --seed
```
4. **Testing API**
Gunakan **Postman**, **Insomnia**, atau **cURL** untuk mencoba endpoint API.

---

## 📜 **Lisensi**
Proyek ini dilisensikan di bawah [MIT License](LICENSE).

🚀 **Happy coding!**

