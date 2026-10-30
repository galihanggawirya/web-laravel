# QUERY BUILDER DI LARAVEL

Laravel Query Builder adalah antarmuka yang lancar (*fluent interface*) untuk membuat dan menjalankan kueri basis data (*database queries*). Query Builder menggunakan PDO parameter binding secara otomatis untuk melindungi aplikasi dari serangan SQL Injection.

Berbeda dengan Eloquent ORM yang bekerja menggunakan kelas Model dan objek, Query Builder dapat berinteraksi langsung dengan tabel database melalui facade DB tanpa memerlukan file Model tersendiri.

---

## 1. Persiapan & Import Facade

Untuk menggunakan Query Builder di Controller atau berkas PHP lainnya, panggil facade DB terlebih dahulu:

```php
use Illuminate\Support\Facades\DB;
```

---

## 2. Mengambil Data (Retrieving Data / READ)

### A. Mengambil Semua Data
Mengembalikan kumpulan (*Collection*) berisi objek `stdClass`.

```php
$users = DB::table('users')->get();

foreach ($users as $user) {
    echo $user->name;
}
```

### B. Mengambil Satu Baris Data
* `first()`: Mengembalikan objek tunggal dari baris pertama yang ditemukan.
* `find()`: Mengambil baris data berdasarkan nilai kolom `id` (*primary key*).

```php
// Mengambil baris pertama yang cocok
$user = DB::table('users')->where('name', 'Budi')->first();
echo $user->email;

// Mengambil data berdasarkan ID = 5
$user = DB::table('users')->find(5);
```

### C. Mengambil Kolom Spesifik saja
Gunakan `select()` agar kueri lebih efisien dan tidak mengembalikan seluruh kolom tabel.

```php
$users = DB::table('users')
    ->select('name', 'email as user_email')
    ->get();
```

### D. Mengambil Nilai Skalar / Kolom Tunggal
* `value()`: Mengambil nilai dari satu kolom saja pada baris pertama.
* `pluck()`: Mengambil semua nilai dari satu kolom dalam bentuk Collection sederhana.

```php
// Mengambil email dari user dengan id 1
$email = DB::table('users')->where('id', 1)->value('email');

// Mengambil seluruh nama user dalam bentuk Array/Collection
$names = DB::table('users')->pluck('name');

// Pluck dengan pasangan Key-Value (contoh: ['id' => 'title'])
$roles = DB::table('roles')->pluck('title', 'id');
```

### E. Metode Agregat
Query Builder menyediakan metode untuk kalkulasi nilai matematis langsung dari database:

```php
$count = DB::table('products')->count();
$maxPrice = DB::table('products')->max('price');
$minPrice = DB::table('products')->min('price');
$avgPrice = DB::table('products')->avg('price');
$totalStock = DB::table('products')->sum('stock');
```

---

## 3. Manipulasi Data (CREATE, UPDATE, DELETE)

### A. Insert (Menambah Data)

```php
// Insert satu baris
DB::table('users')->insert([
    'name' => 'Siti Nurhaliza',
    'email' => 'siti@example.com',
    'password' => bcrypt('password123'),
    'created_at' => now(),
    'updated_at' => now(),
]);

// Insert banyak baris sekaligus (Batch Insert)
DB::table('categories')->insert([
    ['name' => 'Elektronik', 'slug' => 'elektronik'],
    ['name' => 'Pakaian', 'slug' => 'pakaian'],
]);

// Insert dan mendapatkan ID yang baru dibuat
$id = DB::table('users')->insertGetId([
    'name' => 'Andi',
    'email' => 'andi@example.com',
]);
```

### B. Update (Mengubah Data)

```php
// Mengubah data dengan kondisi spesifik
DB::table('users')
    ->where('id', 1)
    ->update(['name' => 'Budi Santoso']);

// Increment & Decrement (Menambah/Mengurangi nilai angka)
DB::table('products')->where('id', 1)->increment('stock'); // +1
DB::table('products')->where('id', 1)->increment('stock', 5); // +5
DB::table('products')->where('id', 1)->decrement('stock', 2); // -2
```

### C. Delete (Menghapus Data)

```php
// Menghapus baris berdasarkan kondisi
DB::table('users')->where('id', 5)->delete();

// Menghapus seluruh isi tabel dan mereset auto-increment
DB::table('logs')->truncate();
```

---

## 4. Klausa Kondisional (Where Clauses)

Query Builder mendukung berbagai variasi klausa WHERE untuk memfilter data.

```php
// Standard WHERE (=, >, <, LIKE, dll)
$users = DB::table('users')->where('votes', '=', 100)->get();
$users = DB::table('users')->where('votes', '>=', 100)->get();
$users = DB::table('users')->where('name', 'like', '%Budi%')->get();

// Penggabungan banyak klausa WHERE (Secara default dianggap AND)
$users = DB::table('users')->where([
    ['status', '=', 'active'],
    ['subscribed', '=', true],
])->get();

// Klausa OR WHERE
$users = DB::table('users')
    ->where('votes', '>', 100)
    ->orWhere('name', 'Budi')
    ->get();

// Where In & Where Not In
$users = DB::table('users')->whereIn('id', [1, 2, 3])->get();

// Where Between
$users = DB::table('products')->whereBetween('price', [10000, 50000])->get();

// Where Null & Where Not Null
$users = DB::table('users')->whereNull('updated_at')->get();

// Where Date / Time
$users = DB::table('orders')->whereDate('created_at', '2026-10-04')->get();
$users = DB::table('orders')->whereMonth('created_at', '10')->get();
$users = DB::table('orders')->whereYear('created_at', '2026')->get();
```

---

## 5. Joining Tables (Relasi Tabel)

Query Builder dapat menghubungkan beberapa tabel menggunakan klausa SQL Join standar.

### A. Inner Join
```php
$orders = DB::table('orders')
    ->join('users', 'orders.user_id', '=', 'users.id')
    ->select('orders.id', 'orders.total_price', 'users.name as customer_name')
    ->get();
```

### B. Left Join / Right Join
```php
$users = DB::table('users')
    ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
    ->select('users.name', 'posts.title')
    ->get();
```

### C. Advanced Join (Multiple Conditions)
```php
DB::table('users')
    ->join('contacts', function ($join) {
        $join->on('users.id', '=', 'contacts.user_id')
             ->where('contacts.status', '=', 'active');
    })
    ->get();
```

---

## 6. Ordering, Grouping, & Pagination

```php
// Ordering (Urutan)
$users = DB::table('users')
    ->orderBy('name', 'asc')
    ->orderBy('created_at', 'desc')
    ->get();

// Group By & Having
$categories = DB::table('products')
    ->select('category_id', DB::raw('SUM(stock) as total_stock'))
    ->groupBy('category_id')
    ->having('total_stock', '>', 100)
    ->get();

// Pagination (Otomatis menangani query LIMIT, OFFSET, dan Link Halaman)
$users = DB::table('users')->paginate(15); // 15 item per halaman
```

---

## 7. Raw Expressions (Metode SQL Murni)

Kadang kala diperlukan penulisan perintah SQL murni untuk ekspresi yang kompleks. Gunakan `DB::raw()` atau helper raw method agar string SQL tidak di-escape sebagai nama kolom.

```php
// Menggunakan DB::raw dalam select
$users = DB::table('users')
    ->select(DB::raw('count(*) as user_count, status'))
    ->where('status', '<>', 1)
    ->groupBy('status')
    ->get();

// Menggunakan selectRaw()
$orders = DB::table('orders')
    ->selectRaw('price * ? as price_with_tax', [1.11])
    ->get();

// Menggunakan whereRaw()
$orders = DB::table('orders')
    ->whereRaw('price > IF(state = "TX", 200, 100)')
    ->get();
```

---
---

# PANDUAN IMPLEMENTASI CRUD ITEMS (AJAX + QUERY BUILDER)

Berikut adalah materi dan panduan implementasi CRUD (*Create, Read, Update, Delete*) untuk tabel `items` menggunakan Laravel Query Builder (`DB::table`) dan AJAX (jQuery) tanpa memuat ulang halaman (*page refresh*).

---

## 1. Persiapan Database & Migration

Buat berkas migration tabel items melalui terminal:

```bash
php artisan make:migration create_items_table
```

Buka berkas migration `database/migrations/YYYY_MM_DD_HHMMSS_create_items_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
```

Jalankan perintah migrasi:

```bash
php artisan migrate
```

---

## 2. Controller (`ItemController.php`)

Buat controller baru menggunakan artisan:

```bash
php artisan make:controller ItemController
```

Buka `app/Http/Controllers/ItemController.php`. Seluruh respon dari controller ini akan mengembalikan format JSON (kecuali method `index` yang merender halaman Blade).

> **Catatan:** Karena menggunakan Query Builder (`DB::table`), penambahan/perubahan timestamp `created_at` dan `updated_at` dilakukan secara manual menggunakan helper `now()`.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    // Menampilkan halaman utama
    public function index()
    {
        return view('items.index');
    }

    // READ: Fetch semua data dalam format JSON
    public function getData()
    {
        $items = DB::table('items')->orderBy('id', 'desc')->get();
        return response()->json(['data' => $items]);
    }

    // CREATE: Menyimpan data baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('items')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Item berhasil ditambahkan!'
        ]);
    }

    // READ: Ambil 1 data berdasarkan ID (untuk modal edit)
    public function show($id)
    {
        $item = DB::table('items')->where('id', $id)->first();

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $item
        ]);
    }

    // UPDATE: Memperbarui data berdasarkan ID
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('items')->where('id', $id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Item berhasil diperbarui!'
        ]);
    }

    // DELETE: Menghapus data berdasarkan ID
    public function destroy($id)
    {
        DB::table('items')->where('id', $id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item berhasil dihapus!'
        ]);
    }
}
```

---

## 3. Registrasi Route (`routes/web.php`)

Buka `routes/web.php` dan daftarkan endpoint pendukung AJAX:

```php
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/data', [ItemController::class, 'getData'])->name('items.data');
Route::post('/items', [ItemController::class, 'store'])->name('items.store');
Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show');
Route::put('/items/{id}', [ItemController::class, 'update'])->name('items.update');
Route::delete('/items/{id}', [ItemController::class, 'destroy'])->name('items.destroy');
```

---

## 4. Single-Page View (`resources/views/items/index.blade.php`)

Buat berkas `resources/views/items/index.blade.php`. Tampilan ini sudah dilengkapi dengan HTML, CSS Modal sederhana, serta script jQuery AJAX.

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CRUD Items - Laravel Query Builder & AJAX</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 6px 12px; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .btn-green { background: #28a745; }
        .btn-blue { background: #007bff; }
        .btn-red { background: #dc3545; }
        .btn-secondary { background: #6c757d; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; display: none; }
        .alert-success { background: #d4edda; color: #155724; }
        /* Modal Style */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: #fff; width: 400px; margin: 100px auto; padding: 20px; border-radius: 5px; position: relative; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        .text-error { color: red; font-size: 12px; margin-top: 3px; display: block; }
    </style>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Kelola Data Item (Query Builder + AJAX)</h2>
<div id="alertSuccess" class="alert alert-success"></div>
<button id="btnCreate" class="btn btn-green">+ Tambah Item Baru</button>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Item</th>
            <th>Deskripsi</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody id="itemTableBody">
        <!-- Data dari AJAX dimasukkan di sini -->
    </tbody>
</table>

<!-- Modal Form (Create / Edit) -->
<div id="itemModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Tambah Item</h3>
        <form id="itemForm">
            <input type="hidden" id="itemId" name="itemId">
            
            <div class="form-group">
                <label>Nama Item:</label>
                <input type="text" id="name" name="name">
                <span class="text-error" id="error-name"></span>
            </div>
            
            <div class="form-group">
                <label>Deskripsi:</label>
                <textarea id="description" name="description"></textarea>
                <span class="text-error" id="error-description"></span>
            </div>
            
            <div class="form-group">
                <label>Harga (Rp):</label>
                <input type="number" id="price" name="price">
                <span class="text-error" id="error-price"></span>
            </div>

            <div class="form-group">
                <label>Stok:</label>
                <input type="number" id="stock" name="stock">
                <span class="text-error" id="error-stock"></span>
            </div>

            <button type="submit" id="btnSave" class="btn btn-blue">Simpan</button>
            <button type="button" id="btnClose" class="btn btn-secondary">Batal</button>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
    // Setup CSRF Token untuk seluruh request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 1. Load Data Pertama Kali
    loadItems();

    function loadItems() {
        $.ajax({
            url: "{{ route('items.data') }}",
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                let rows = '';
                if (response.data.length > 0) {
                    $.each(response.data, function (index, item) {
                        rows += `
                        <tr>
                            <td>${item.id}</td>
                            <td>${item.name}</td>
                            <td>${item.description ? item.description : '-'}</td>
                            <td>Rp ${parseFloat(item.price).toLocaleString('id-ID')}</td>
                            <td>${item.stock}</td>
                            <td>
                                <button class="btn btn-blue btn-edit" data-id="${item.id}">Edit</button>
                                <button class="btn btn-red btn-delete" data-id="${item.id}">Hapus</button>
                            </td>
                        </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="6" style="text-align:center;">Tidak ada data item.</td></tr>';
                }
                $('#itemTableBody').html(rows);
            }
        });
    }

    // Reset Form Modal & Pesan Error
    function resetForm() {
        $('#itemForm')[0].reset();
        $('#itemId').val('');
        $('.text-error').text('');
    }

    // 2. Buka Modal Tambah
    $('#btnCreate').click(function () {
        resetForm();
        $('#modalTitle').text('Tambah Item Baru');
        $('#itemModal').show();
    });

    // Tutup Modal
    $('#btnClose').click(function () {
        $('#itemModal').hide();
    });

    // 3. Simpan Data (Create & Update)
    $('#itemForm').submit(function (e) {
        e.preventDefault();
        $('.text-error').text('');

        let id = $('#itemId').val();
        let url = id ? `/items/${id}` : "{{ route('items.store') }}";
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: {
                name: $('#name').val(),
                description: $('#description').val(),
                price: $('#price').val(),
                stock: $('#stock').val(),
            },
            success: function (response) {
                if (response.status) {
                    $('#itemModal').hide();
                    showAlert(response.message);
                    loadItems();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $(`#error-${key}`).text(value[0]);
                    });
                }
            }
        });
    });

    // 4. Buka Modal Edit & Set Data
    $(document).on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        resetForm();

        $.ajax({
            url: `/items/${id}`,
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                if (response.status) {
                    let item = response.data;
                    $('#modalTitle').text('Edit Item');
                    $('#itemId').val(item.id);
                    $('#name').val(item.name);
                    $('#description').val(item.description);
                    $('#price').val(item.price);
                    $('#stock').val(item.stock);
                    $('#itemModal').show();
                }
            }
        });
    });

    // 5. Hapus Data
    $(document).on('click', '.btn-delete', function () {
        let id = $(this).data('id');
        if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
            $.ajax({
                url: `/items/${id}`,
                type: "DELETE",
                dataType: "JSON",
                success: function (response) {
                    if (response.status) {
                        showAlert(response.message);
                        loadItems();
                    }
                }
            });
        }
    });

    // Helper Notifikasi Toast / Alert
    function showAlert(message) {
        $('#alertSuccess').text(message).fadeIn().delay(3000).fadeOut();
    }
});
</script>
</body>
</html>
```

---

## 5. Ringkasan Alur Kerja CRUD

| Aksi | Endpoint Route | HTTP Method | Logika Controller (Query Builder) | Respon AJAX |
| :--- | :--- | :--- | :--- | :--- |
| **Read (All)** | `/items/data` | `GET` | `DB::table('items')->get()` | Objek JSON berisi array data |
| **Create** | `/items` | `POST` | `DB::table('items')->insert(...)` | Status sukses / validasi error (422) |
| **Show (Single)** | `/items/{id}` | `GET` | `DB::table('items')->where('id', $id)->first()` | Objek JSON data item spesifik |
| **Update** | `/items/{id}` | `PUT` | `DB::table('items')->where('id', $id)->update(...)` | Status sukses / validasi error (422) |
| **Delete** | `/items/{id}` | `DELETE` | `DB::table('items')->where('id', $id)->delete()` | Status sukses |

---
---

# PANDUAN TRANSAKSI PENJUALAN (AJAX + QUERY BUILDER)

Berikut adalah panduan dan materi lengkap pembuatan Transaksi Penjualan Items menggunakan Laravel Query Builder (`DB::table`) dan AJAX (jQuery), dilengkapi fitur Modal Pilih Item.

---

## 1. Struktur Database & Migration

Aplikasi ini membutuhkan 3 tabel utama: 
1. `items` (master data barang)
2. `transactions` (header transaksi)
3. `transaction_details` (rincian item transaksi)

Jalankan perintah Artisan berikut di terminal:

```bash
php artisan make:migration create_sales_transaction_tables
```

Buka file migration yang baru dibuat, lalu sesuaikan kodenya:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Item
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->integer('stock');
            $table->timestamps();
        });

        // 2. Tabel Header Transaksi
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('transaction_date');
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();
        });

        // 3. Tabel Detail Transaksi
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('price', 12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('items');
    }
};
```

Jalankan migrasi ke database:

```bash
php artisan migrate
```

---

## 2. Controller (`TransactionController.php`)

Buat controller baru:

```bash
php artisan make:controller TransactionController
```

Buka file `app/Http/Controllers/TransactionController.php`. Pengolahan data dilakukan murni menggunakan Query Builder dan Database Transaction (`DB::transaction`) untuk memastikan konsistensi data saat menyimpan header, detail, serta memotong stok item.

```php
<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    // Menampilkan halaman form transaksi
    public function create()
    {
        return view('transactions.create');
    }

    // Endpoint AJAX: Mengambil daftar item yang stoknya > 0 untuk Modal
    public function getItems(Request $request)
    {
        $search = $request->get('search');
        $query = DB::table('items')->where('stock', '>', 0);

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $items = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'status' => true,
            'data' => $items
        ]);
    }

    // Endpoint AJAX: Menyimpan Transaksi Penjualan
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal, periksa item yang diinput.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Gunakan Database Transaction agar jika ada error, seluruh query di-rollback
        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $transactionItems = $request->items;

            // Validasi ketersediaan stok terlebih dahulu
            foreach ($transactionItems as $item) {
                $dbItem = DB::table('items')->where('id', $item['item_id'])->first();

                if (!$dbItem) {
                    throw new Exception("Item dengan ID {$item['item_id']} tidak ditemukan.");
                }

                if ($dbItem->stock < $item['quantity']) {
                    throw new Exception("Stok barang '{$dbItem->name}' tidak mencukupi. (Tersedia: {$dbItem->stock})");
                }

                $totalAmount += $item['price'] * $item['quantity'];
            }

            // 1. Insert Header Transaksi & Dapatkan ID-nya
            $invoiceNumber = 'TRX-' . date('Ymd') . '-' . rand(1000, 9999);
            $transactionId = DB::table('transactions')->insertGetId([
                'invoice_number' => $invoiceNumber,
                'transaction_date' => now()->toDateString(),
                'total_amount' => $totalAmount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Insert Detail Transaksi & Potong Stok Item
            foreach ($transactionItems as $item) {
                $subtotal = $item['price'] * $item['quantity'];

                DB::table('transaction_details')->insert([
                    'transaction_id' => $transactionId,
                    'item_id' => $item['item_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Pengurangan Stok menggunakan decrement
                DB::table('items')
                    ->where('id', $item['item_id'])
                    ->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'invoice' => $invoiceNumber
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
```

---

## 3. Route (`routes/web.php`)

Daftarkan route berikut pada berkas `routes/web.php`:

```php
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
Route::get('/transactions/get-items', [TransactionController::class, 'getItems'])->name('transactions.get-items');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
```

---

## 4. View & JavaScript AJAX (`resources/views/transactions/create.blade.php`)

Buat berkas `resources/views/transactions/create.blade.php`. Menggunakan Bootstrap 5 untuk modal & UI, serta jQuery untuk menangani interaksi AJAX.

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transaksi Penjualan - Laravel Query Builder AJAX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Form Transaksi Penjualan</h5>
            <span>Tanggal: {{ date('d-m-Y') }}</span>
        </div>
        <div class="card-body">
            <!-- Alert Notifikasi -->
            <div id="alertContainer"></div>

            <!-- Tombol Trigger Modal Item -->
            <div class="mb-3">
                <button type="button" class="btn btn-success" id="btnOpenModal">
                    + Pilih Item Barang
                </button>
            </div>

            <!-- Tabel Keranjang Transaksi -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="cartTable">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Item</th>
                            <th style="width: 150px;">Harga (Rp)</th>
                            <th style="width: 120px;">Qty</th>
                            <th style="width: 180px;">Subtotal (Rp)</th>
                            <th style="width: 80px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cartBody">
                        <tr id="emptyCartRow">
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada item dipilih. Klik tombol "+ Pilih Item Barang" di atas.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end fw-bold">Grand Total (Rp):</th>
                            <th colspan="2" class="fw-bold text-primary fs-5" id="grandTotalText">0</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Tombol Simpan -->
            <div class="text-end mt-3">
                <button type="button" class="btn btn-primary btn-lg" id="btnSubmitTransaction" disabled>
                    Simpan Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PILIH ITEM -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalLabel">Pilih Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Input Pencarian -->
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchItemInput" placeholder="Cari nama barang...">
                </div>
                <!-- Tabel Daftar Barang -->
                <div class="table-responsive" style="max-height: 350px;">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>ID</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemListBody">
                            <!-- Data item AJAX dimuat di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    // Setup Header CSRF Token untuk AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
    let cart = []; // Array penampung item di keranjang

    // 1. Buka Modal & Ambil Data Item
    $('#btnOpenModal').click(function () {
        fetchModalItems('');
        itemModal.show();
    });

    // Event pencarian barang secara realtime
    $('#searchItemInput').on('keyup', function () {
        let keyword = $(this).val();
        fetchModalItems(keyword);
    });

    // Function mengambil data item dari server via AJAX
    function fetchModalItems(search) {
        $.ajax({
            url: "{{ route('transactions.get-items') }}",
            type: "GET",
            data: { search: search },
            dataType: "JSON",
            success: function (response) {
                let html = '';
                if (response.data.length > 0) {
                    $.each(response.data, function (i, item) {
                        html += `
                        <tr>
                            <td>${item.id}</td>
                            <td>${item.name}</td>
                            <td>Rp ${parseFloat(item.price).toLocaleString('id-ID')}</td>
                            <td><span class="badge bg-info text-dark">${item.stock}</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-success btn-select-item"
                                    data-id="${item.id}"
                                    data-name="${item.name}"
                                    data-price="${item.price}"
                                    data-stock="${item.stock}">
                                    Pilih
                                </button>
                            </td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-muted">Barang tidak ditemukan / stok habis.</td></tr>';
                }
                $('#itemListBody').html(html);
            }
        });
    }

    // 2. Event Tombol "Pilih" Item dari Modal
    $(document).on('click', '.btn-select-item', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let price = parseFloat($(this).data('price'));
        let stock = parseInt($(this).data('stock'));

        // Cek apakah item sudah ada di keranjang
        let existingIndex = cart.findIndex(i => i.item_id === id);

        if (existingIndex !== -1) {
            if (cart[existingIndex].quantity + 1 > stock) {
                alert('Jumlah melebihi stok yang tersedia!');
                return;
            }
            cart[existingIndex].quantity += 1;
        } else {
            cart.push({
                item_id: id,
                name: name,
                price: price,
                stock: stock,
                quantity: 1
            });
        }

        renderCart();
        itemModal.hide();
    });

    // 3. Render Ulang Tabel Keranjang
    function renderCart() {
        if (cart.length === 0) {
            $('#cartBody').html(`
                <tr id="emptyCartRow">
                    <td colspan="6" class="text-center text-muted py-4">
                        Belum ada item dipilih. Klik tombol "+ Pilih Item Barang" di atas.
                    </td>
                </tr>
            `);
            $('#grandTotalText').text('0');
            $('#btnSubmitTransaction').prop('disabled', true);
            return;
        }

        let html = '';
        let grandTotal = 0;

        $.each(cart, function (index, item) {
            let subtotal = item.price * item.quantity;
            grandTotal += subtotal;

            html += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                <td>
                    <input type="number" class="form-control form-control-sm input-qty" 
                        data-index="${index}" value="${item.quantity}" min="1" max="${item.stock}">
                </td>
                <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger btn-remove-item" data-index="${index}">&times;</button>
                </td>
            </tr>`;
        });

        $('#cartBody').html(html);
        $('#grandTotalText').text('Rp ' + grandTotal.toLocaleString('id-ID'));
        $('#btnSubmitTransaction').prop('disabled', false);
    }

    // 4. Ubah Jumlah Quantity dari Tabel Keranjang
    $(document).on('change keyup', '.input-qty', function () {
        let index = $(this).data('index');
        let newQty = parseInt($(this).val()) || 1;
        let maxStock = cart[index].stock;

        if (newQty > maxStock) {
            alert(`Stok hanya tersedia ${maxStock}`);
            newQty = maxStock;
            $(this).val(maxStock);
        }

        if (newQty < 1) {
            newQty = 1;
            $(this).val(1);
        }

        cart[index].quantity = newQty;
        renderCart();
    });

    // 5. Hapus Item dari Keranjang
    $(document).on('click', '.btn-remove-item', function () {
        let index = $(this).data('index');
        cart.splice(index, 1);
        renderCart();
    });

    // 6. Simpan Transaksi via AJAX
    $('#btnSubmitTransaction').click(function () {
        if (cart.length === 0) return;

        let $btn = $(this);
        $btn.prop('disabled', true).text('Menyimpan...');

        let payload = {
            items: cart.map(item => ({
                item_id: item.item_id,
                quantity: item.quantity,
                price: item.price
            }))
        };

        $.ajax({
            url: "{{ route('transactions.store') }}",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            dataType: "JSON",
            success: function (response) {
                if (response.status) {
                    showAlert('success', `<strong>Sukses!</strong> ${response.message} (No. Invoice: ${response.invoice})`);
                    cart = [];
                    renderCart();
                }
            },
            error: function (xhr) {
                let msg = 'Terjadi kesalahan saat menyimpan transaksi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showAlert('danger', `<strong>Error!</strong> ${msg}`);
            },
            complete: function () {
                $btn.prop('disabled', false).text('Simpan Transaksi');
            }
        });
    });

    // Helper Alert
    function showAlert(type, message) {
        $('#alertContainer').html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
    }
});
</script>
</body>
</html>
```

---

## 5. Ringkasan Alur Sistem Transaksi

1. **Pengambilan Data Item (`GET /transactions/get-items`):** Modal AJAX mengambil list barang yang `stock > 0` menggunakan `DB::table('items')`.
2. **Kalkulasi Frontend:** Item yang dipilih ditampung di variabel array JavaScript `cart`. Setiap penambahan/perubahan quantity langsung dihitung subtotal dan grandTotal-nya di browser tanpa request ke server.
3. **Pengiriman Data AJAX (`POST /transactions`):** Array `cart` dikirimkan ke backend dalam format JSON Payload.
4. **Eksekusi Backend Transaksi (`DB::transaction`):**
   * Memeriksa ulang ketersediaan stok di database.
   * Membuat nomor invoice unik dan menyisipkan record header ke tabel `transactions` dengan `insertGetId()`.
   * Menyisipkan detail belanja ke `transaction_details`.
   * Mengurangi stok pada tabel `items` secara otomatis dengan `decrement('stock', $qty)`.
   * Jika ada kendala (misal stok tiba-tiba kurang), `DB::rollBack()` dijalankan otomatis.