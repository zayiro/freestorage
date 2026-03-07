@extends('layouts.app')

@section('content')    
<div class="container">
    <!-- Encabezado -->
    <div class="receipt-header">
        <h4 class="mb-0">FERRETERÍA EL HOMBRE</h4>
        <p class="mb-0">Av. Principal #123, Ciudad</p>
        <p class="mb-0">Tel: (555) 123-4567</p>
        <p class="mb-0">RUC: 1234567890001</p>
    </div>

    <!-- Información de la Venta -->
    <div class="mb-3">
        <table class="table table-sm">
            <tr>
                <th width="40%">Factura:</th>
                <td><strong>{{ $sale->invoice_number }}</strong></td>
            </tr>
            <tr>
                <th>Fecha:</th>
                <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Vendedor:</th>
                <td>{{ $sale->user ? $sale->user->name : 'Sistema' }}</td>
            </tr>
            <tr>
                <th>Cliente:</th>
                <td>{{ $sale->customer_name ?? 'Público General' }}</td>
            </tr>
            <tr>
                <th>Cédula:</th>
                <td>{{ $sale->customer_cedula ?? '-' }}</td>
            </tr>
            <tr>
                <th>Método de Pago:</th>
                <td>{{ ucfirst($sale->payment_method) }}</td>
            </tr>
        </table>
    </div>

    <!-- Productos -->
    <table class="receipt-table">
        <thead>
            <tr>
                <th>Cantidad</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $item['product_name'] }}</td>
                    <td>${{ number_format($item['sales_price'], 2) }}</td>
                    <td>${{ number_format($item['sales_price'] * $item['quantity'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales -->
    <div class="total-section">
        <table class="table table-sm">
            <tr>
                <td>Subtotal:</td>
                <td>${{ number_format($sale->subtotal, 2) }}</td>
            </tr>
            @if($sale->discount > 0)
                <tr class="text-danger">
                    <td>Descuento ({{ $sale->discount_percentage }}%):</td>
                    <td>-${{ number_format($sale->discount, 2) }}</td>
                </tr>
            @endif
            @if($sale->tax > 0)
                <tr class="text-success">
                    <td>Impuestos ({{ $sale->tax_percentage }}%):</td>
                    <td>+${{ number_format($sale->tax, 2) }}</td>
                </tr>
            @endif
            <tr class="final-total">
                <td><strong>TOTAL:</strong></td>
                <td><strong>${{ number_format($sale->total, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Notas -->
    @if($sale->notes)
        <div class="mt-3">
            <strong>Notas:</strong>
            <p class="mb-0">{{ $sale->notes }}</p>
        </div>
    @endif

    <!-- Pie de página -->
    <div class="receipt-footer">
        <p class="mb-0"><strong>¡Gracias por su compra!</strong></p>
        <p class="mb-0">Conservar este recibo para garantías</p>
        <p class="mb-0">www.ferreteria-ejemplo.com</p>
    </div>
</div>

<!-- Botones de Acción (No se imprimen) -->
<div class="no-print text-center mt-4">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> Imprimir Recibo
    </button>
    <a href="{{ route('sales.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>
@endsection

@section('scripts')
<script>
    // Imprimir automáticamente al cargar (opcional)
    // window.onload = function() { window.print(); }
</script>
@endsection
