# Smart CRM (Laravel 12)

CRM sederhana untuk leads → customers → deals (dengan approval) + products.
Stack: **Laravel 12**, **Breeze** (Blade + Tailwind), **PostgreSQL**, **Vite**, Maatwebsite Excel (export report).

> Catatan proyek:
> - Semua tabel domain menggunakan **soft deletes**.
> - HPP = `products.cost_price`.

## Prasyarat
- PHP 8.3+
- Composer 2.x
- Node.js 20/22 + NPM
- PostgreSQL 11+ (atau MySQL jika diubah)

## Setup Cepat (Local)

```bash
git clone https://github.com/<dmjrmh>/<dame_crm>.git
cd <dame_crm>
```

# 1) Dependensi
composer install
npm install

# 2) Env
cp .env.example .env
php artisan key:generate

# 3) Edit .env sesuai database PostgreSQL:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smart_crm
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

# 4) Lalu migrate + seed:
```
php artisan migrate --seed
php artisan storage:link
```

# 5) Run:
```
npm run dev
php artisan serve
```

Akses: http://127.0.0.1:8000

# 6) Akun Demo (default seeder) 

Manager: manager / passwordManager
Sales: elaen / AtminSalesOne || lorenza / AtminSalesTwo || sulley / AtminSalesThree

## 🗄️ Database Entities

- **users**: id, name, email, role
- **leads**: id, user_id, name, contact, status
- **customers**: id, lead_id, user_id, name, contact
- **products**: id, name, sku, cost_price, sell_price
- **deals**: id, user_id, lead_id, customer_id, approval_status, closed_at
- **deal_items**: id, deal_id, product_id, quantity, unit_price
- **pipeline_stages**: id, key, name, is_won, is_closed

## 🔄 Business Flow

- **Leads**
  - Sales bisa input calon customer (lead).
  - Leads bisa diedit/dihapus sebelum jadi deal.
  - Jika sudah terjadi transaksi → lead otomatis berubah status:
    - `Won` → ketika deal sukses (approved).
    - `Lost` → ketika deal ditolak / rejected.

- **Deals (Projects)**
  - 1 Deal bisa terdiri dari banyak item produk.
  - **Approval Rule**:
    - Jika `deal_items.unit_price >= product.sell_price` → auto approved (mempercepat input data sales).
    - Jika `deal_items.unit_price < product.sell_price` → butuh approval manager.
  - Manager bisa approve / reject deal dari halaman approvals.

- **Customers**
  - Lead yang berstatus `Won` otomatis terkonversi jadi customer baru.
  - Customer bisa punya banyak deal / layanan aktif.

- **Reporting**
  - Manager & Sales bisa export laporan ke Excel (lead → customer, revenue, HPP, profit).
