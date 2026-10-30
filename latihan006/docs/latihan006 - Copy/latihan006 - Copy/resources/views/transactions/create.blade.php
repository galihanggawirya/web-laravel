<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Transaksi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>


<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-itemscenter">
                <h5 class="mb-0">Form Transaksi</h5>
            </div>
            <div class="card-body">
                <!-- Alert Notifikasi -->
                <div id="alertContainer"></div>
                <!-- Input Tanggal & Keterangan -->
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal:</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan:</label>
                    <input type="text" class="form-control" id="keterangan" placeholder="Masukkan Keterangan" name="keterangan">
                </div>
                <!-- Input Pilih Item Sementara -->
                <div class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="id_item" data-bs-toggle="modal" data-bs-target="#itemModal" placeholder="Klik ID Item" readonly style="cursor: pointer;">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="name_item" placeholder="Nama Item" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" id="price_item" placeholder="Harga" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end" id="qty_item" placeholder="Qty" value="1" min="1">
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end" id="subtotal_item" placeholder="Subtotal" value="0" readonly>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-success w-100" id="btnAddCart">+</button>
                        </div>
                    </div>
                </div>
                <!-- Tabel Keranjang Transaksi -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle" id="cartTable">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 80px;" class="text-center">Aksi</th>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Item</th>
                                <th style="width: 150px;" class="text-end">Harga (Rp)</th>
                                <th style="width: 120px;" class="text-center">Qty</th>
                                <th style="width: 180px;" class="text-end">Subtotal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr id="emptyCartRow">
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada item dipilih. Klik ID Item di atas.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end fw-bold">Grand Total (Rp):</th>
                                <th class="fw-bold text-primary fs-5 text-end" id="grandTotalText">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- Tombol Simpan -->
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-primary btn-lg" id="btnSubmitTransaction" disabled>Simpan Transaksi</button>
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
                                <th class="text-center">Aksi</th>
                                <th>ID</th>
                                <th>Nama Barang</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Stok</th>
                            </tr>
                        </thead>
                        <tbody id="itemListBody">
                        @foreach($items as $item)
                        <tr>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal"
                                    onclick="pilihan('{{ $item->id }}', '{{ addslashes($item->name) }}', '{{ $item->price }}')">Pilih</button>
                            </td>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-end">{{ $item->stock }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi memasukkan item yang dipilih dari modal ke form
function pilihan(id, name, price) {
    $("#id_item").val(id);
    $("#name_item").val(name);
    $("#price_item").val(price);
    $("#qty_item").val(1);
    $("#subtotal_item").val(price);
}


let cart = [];

$(document).ready(function() {

// Hitung otomatis subtotal saat qty diubah
    $("#qty_item").on("input change", function() {
        let qty = parseInt($(this).val()) || 0;
        let price = parseFloat($("#price_item").val()) || 0;
        $("#subtotal_item").val(qty * price);
    });


// Pencarian Barang di Modal
    $("#searchItemInput").on("keyup", function() {
        let value = $(this).val().toLowerCase();
        $("#itemListBody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });


// Tambahkan Barang ke Keranjang (+)
    $("#btnAddCart").click(function() {
        let id = $("#id_item").val();
        let name = $("#name_item").val();
        let price = parseFloat($("#price_item").val());
        let qty = parseInt($("#qty_item").val());
        if (!id) {
            alert("Silakan pilih barang terlebih dahulu!");
            return;
        }

        if (qty <= 0 || isNaN(qty)) {
            alert("Jumlah (qty) minimal 1!");
            return;
        }

        // Cek jika barang sudah ada di keranjang
        let existingItem = cart.find(item => item.id == id);
        if (existingItem) {
            existingItem.qty += qty;
            existingItem.subtotal = existingItem.qty * existingItem.price;
        } else {
            cart.push({
                id: id,
                name: name,
                price: price,
                qty: qty,
                subtotal: qty * price
            });
        }

        // Reset input item
        $("#id_item").val('');
        $("#name_item").val('');
        $("#price_item").val('');
        $("#qty_item").val(1);
        $("#subtotal_item").val(0);
        renderCart();
    });


    // Fungsi Render Tabel Keranjang & Hitung Grand Total
    function renderCart() {
        let cartBody = $("#cartBody");
        cartBody.empty();
        if (cart.length === 0) {
            cartBody.html(`
            <tr id="emptyCartRow">
            <td colspan="6" class="text-center text-muted py-4">
            Belum ada item dipilih. Klik ID Item di atas.
            </td>
            </tr>
            `);
            $("#btnSubmitTransaction").prop("disabled", true);
            $("#grandTotalText").text("0");
            return;
        }
        let grandTotal = 0;
        cart.forEach((item, index) => {
            grandTotal += item.subtotal;
            cartBody.append(`
            <tr>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">Hapus</button>
                </td>
                <td class="text-center">${index + 1}</td>
                <td>${item.name}</td>
                <td class="text-end">Rp ${item.price.toLocaleString('id-ID')}</td>
                <td class="text-center">${item.qty}</td>
                <td class="text-end">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
            </tr>
            `);
        });
        $("#grandTotalText").text("Rp " + grandTotal.toLocaleString('id-ID'));
        $("#btnSubmitTransaction").prop("disabled", false);
    }
    // Fungsi Hapus Barang dari Keranjang
    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        renderCart();
    };
    // Simpan Transaksi via AJAX
    $("#btnSubmitTransaction").click(function() {
    let tanggal = $("#tanggal").val();
    let keterangan = $("#keterangan").val();
    if (cart.length === 0) {
        alert("Keranjang belanja kosong!");
        return;
    }


    // 1. Inisialisasi FormData
    let formData = new FormData();
    formData.append('tanggal', tanggal);
    formData.append('keterangan', keterangan);
    // 2. Loop array cart untuk dimasukkan sebagai array input Laravel
    cart.forEach((item, index) => {
        formData.append(`items[${index}][id]`, item.id);
        formData.append(`items[${index}][name]`, item.name);
        formData.append(`items[${index}][price]`, item.price);
        formData.append(`items[${index}][qty]`, item.qty);
        formData.append(`items[${index}][subtotal]`, item.subtotal);
    });

    $.ajax({
        url: "{{ url('transactions') }}",
        type: "POST",
        data: formData,
        processData: false, // Wajib: mencegah jQuery mengubah FormData menjadi string
        contentType: false, // Wajib: agar browser menentukan boundary multipart/form-data
        success: function(response) {
            $("#alertContainer").html(`
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            Transaksi berhasil disimpan!
            <button type="button" class="btn-close" data-bs-dismiss="alert" arialabel="Close"></button>
            </div>
            `);
            cart = [];
            renderCart();
            $("#keterangan").val('');
            window.location.href = "{{ url('transactions') }}";
        },
        error: function(xhr) {
            let err = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan server";
            $("#alertContainer").html(`
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Gagal menyimpan transaksi: ${err}
            <button type="button" class="btn-close" data-bs-dismiss="alert" arialabel="Close"></button>
            </div>
            `);
        }
    });
    });
});
</script>
</body>
</html>