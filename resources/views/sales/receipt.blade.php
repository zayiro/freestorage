@extends('layouts.app')

@section('content')
<style>
    /* Estilos para impresión */
    @media print {
        /* Ocultar elementos que no queremos imprimir */
        body * {
            visibility: hidden;
        }
        
        /* Mostrar solo el div específico */
        #area-imprimir, #area-imprimir * {
            visibility: visible;
        }
        
        /* Posicionar el div en la parte superior */
        #area-imprimir {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        
        /* Ocultar el botón de imprimir */
        button {
            display: none;
        }
    }
</style>

<div id="area-imprimir" class="container my-3">
    <!-- Encabezado -->
    <div class="receipt-header">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row mb-3">
            <div class="col-md-5">
                <h4 class="mb-0">{{ $company->name }}</h4>
                <p class="mb-0">Dirección: {{ $company->address }}</p>
                <p class="mb-0">Teléfono: {{ $company->phone }}</p>
                <p class="mb-0">NIT: {{ $company->dni }}</p>
            </div>
            <div class="col-md-7">
                @if($company->image)
                    <!-- img-thumbnail es una clase de Bootstrap que crea un borde redondeado -->
                    <img src="{{ asset('storage/' . $company->image) }}" style="max-width: 50px; max-height: 50px;" alt="Logo {{ $company->name }}">
                @else
                    <span class="text-muted">Sin imagen</span>
                @endif
            </div>
        </div>        
    </div>

    <!-- Información de la Venta -->
    <div class="mb-3">
        <div class="table-responsive">
            <table class="table">
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
                    <th>Razón Social:</th>
                    <td>{{ $sale->customer_name ?? 'Público General' }}</td>
                </tr>
                <tr>
                    <th>NIT:</th>
                    <td>{{ $sale->customer_id ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Método de Pago:</th>
                    <td>{{ ucfirst($sale->payment_method) }}</td>
                </tr>
                <tr>
                    <th>Artículos:</th>
                    <td>{{ $total_items }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Productos -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>                    
                    <th>ARTÍCULO</th>
                    <th>CANTIDAD</th>
                    <th>VALOR UNITARIO</th>
                    <th>VALOR TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                @endphp

                @foreach($items as $item)
                    @php
                    $subtotal += $item['sales_price'] * $item['quantity'];
                    @endphp
                    <tr>
                        <td>{{ $item['product_id'] }}</td>                        
                        <td>{{ $item['product_name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>$ {{ number_format($item['sales_price'], 2) }}</td>
                        <td>$ {{ number_format($item['sales_price'] * $item['quantity'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totales -->    
    <div class="table-responsive">
        <table class="table">
            <tr>
                <td>Venta grabada:</td>
                <td>$ {{ number_format($subtotal - $sale->tax, 2) }}</td>
            </tr>
            @if($sale->discount > 0)
                <tr>
                    <td>Descuento ({{ $sale->discount_percentage }}%):</td>
                    <td>$ {{ number_format($sale->discount, 2) }}</td>
                </tr>
            @endif
            @if($sale->tax > 0)
                <tr>
                    <td>Impuestos:</td>
                    <td>$ {{ number_format($sale->tax, 2) }}</td>
                </tr>
            @endif
            @if($sale->delivery_fee > 0)
            <tr>
                <td>Domicilio:</td>
                <td>$ {{ number_format($sale->delivery_fee, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>TOTAL:</strong></td>
                <td><strong>$ {{ number_format($sale->total_price, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Notas -->
    @if($sale->notes)
        <div class="mt-3 mb-3">
            <strong>Notas:</strong>
            <p class="mb-0">{{ $sale->notes }}</p>
        </div>
    @endif    
</div>

<div class="container mb-3">
    <!-- Pie de página -->
    <div class="no-print receipt-footer">
        <p class="mb-0"><strong>¡Gracias por su compra!</strong></p>
        <p class="mb-0">Conservar este recibo para garantías</p>
        <p class="mb-0">{{ $company->email }}</p>
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
