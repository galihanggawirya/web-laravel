<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <div class="container mt-3">
        <h2>Transactions</h2>
        <a href="{{ url('transactions/create') }}"><button type="button" class="btn btn-success btn-sm">Tambah</button></a>
        <table class="table table-striped">
        <thead>
            <tr>
                <th>Aksi</th>
                <th>ID</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="TransactionTableBody">
            @foreach($transactions as $transaction)
            <tr>
                <td>
                    <a href="{{ url('transactions/show', $transaction->id) }}" class="btn btn-warning btnsm">View</a>
                </td>
                <td>{{$transaction->id}}</td>
                <td>{{$transaction->invoice_number}}</td>
                <td>{{$transaction->transaction_date}}</td>
                <td>Rp {{$transaction->total_amount}}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
</body>
</html>