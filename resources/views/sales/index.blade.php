<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Historial de Ventas</h1>
        <table class="table">
            <thead>
                <tr><th>ID</th><th>Fecha</th><th>Compañía</th><th>Total</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                        <td>{{ $sale->company ? $sale->company->name : 'Individual' }}</td>
                        <td>${{ number_format($sale->total_amount, 2) }}</td>
                        <td><a href="{{ route('sales.receipt', $sale) }}" class="btn btn-sm btn-info">Ver Recibo</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $sales->links() }}
    </div>
</body>
</html>