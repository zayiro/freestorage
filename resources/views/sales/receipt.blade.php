<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Recibo de Venta #{{ $sale->id }}</h1>
        <p><strong>Fecha:</strong> {{ $sale->sale_date->format('d/m/Y H:i') }}</p>
        <!-- Después de la fecha -->
        @if($sale->company)
            <p><strong>Compañía:</strong> {{ $sale->company->name }} ({{ $sale->company->contact_person }})</p>
        @endif
        <table class="table">
            <thead>
                <tr><th>Producto</th><th>Presentación</th><th>Cantidad</th><th>Precio Unitario</th><th>Total</th></tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['presentation'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>${{ number_format($item['price'], 2) }}</td>
                        <td>${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <h3>Total: ${{ number_format($sale->total_amount, 2) }}</h3>
        <a href="{{ route('products.index') }}" class="btn btn-primary">Volver a Productos</a>
    </div>
</body>
</html>