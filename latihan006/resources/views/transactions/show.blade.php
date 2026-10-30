<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Transaksi - {{ $transaction->invoice_number }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
@media print {
    .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-itemscenter">
                <h5 class="mb-0">Detail Transaksi</h5>
                <div class="no-print">
                    <button onclick="window.print()" class="btn btn-light btn-sm me-1">Cetak</button>
                    <a href="{{ url('transactions') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <!-- Informasi Header Transaksi -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="fw-bold" style="width: 140px;">No. Invoice</td>
                                <td>: <span class="badge bg-info text-dark fs-6">{{ $transaction->invoice_number }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tanggal</td>
                                <td>: {{ date('d-m-Y', strtotime($transaction->transaction_date)) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="fw-bold" style="width: 140px;">Keterangan</td>
                                <td>: {{ $transaction->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Dibuat Pada</td>
                                <td>: {{ date('d-m-Y H:i', strtotime($transaction->created_at)) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- Tabel Detail Item Transaksi -->
                <h6 class="fw-bold border-bottom pb-2">Daftar Item</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Item</th>
                                <th style="width: 150px;" class="text-end">Harga Satuan (Rp)</th>
                                <th style="width: 100px;" class="text-center">Qty</th>
                                <th style="width: 180px;" class="text-end">Subtotal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details as $index => $detail)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $detail->item_name }}</td>
                                <td class="text-end">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $detail->quantity }}</td>
                                <td class="text-end">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Tidak ada item dalam
                                    transaksi ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end fw-bold fs-5">Grand Total:</th>
                                <th class="text-end fw-bold text-primary fs-5">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>