<!-- resources/views/cashier/receipt.blade.php -->

@extends('layouts.app')

@section('title', 'Recibo de Venta')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <!-- Header del Recibo -->
                    <div class="text-center mb-4">
                        @if($sale->company)
                            <img src="{{ $sale->company->logo ?? asset('images/logo.png') }}" 
                                 alt="Logo" 
                                 class="mb-3" 
                                 style="max-height: 80px;">
                        @endif
                        <h3 class="mb-1">{{ $sale->company->name ?? 'Empresa' }}</h3>
                        <p class="text-muted mb-1">{{ $sale->company->address ?? 'Dirección de la empresa' }}</p>
                        <p class="text-muted mb-1">
                            <i class="fas fa-phone"></i> {{ $sale->company->phone ?? 'Teléfono' }}
                        </p>
                        <hr>
                        <h5 class="mb-1">RECIBO DE VENTA</h5>
                        <p class="text-muted mb-0">N° {{ $sale->sale_number }}</p>
                    </div>

                    <!-- Información de la Venta -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}<br>
                            <strong>Hora:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('H:i') }}
                        </div>
                        <div class="col-md-6 text-md-end">
                            <strong>Cajero:</strong> {{ $sale->cashier->name ?? 'N/A' }}<br>
                            <strong>Estado:</strong>
                            @if($sale->status == 'completed')
                                <span class="badge bg-success">Completado</span>
                            @elseif($sale->status == 'pending')
                                <span class="badge bg-warning">Pendiente</span>
                            @else
                                <span class="badge bg-danger">Cancelado</span>
                            @endif
                        </div>
                    </div>

                    <!-- Tabla de Productos -->
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cant.</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product_name }}</strong>
                                            @if($item->product_sku)
                                                <br><small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totales -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Subtotal:</strong>
                                <span>${{ number_format($sale->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Descuento:</strong>
                                <span class="text-danger">-${{ number_format($sale->discount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Impuestos:</strong>
                                <span>${{ number_format($sale->tax, 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="fs-5">Total:</strong>
                                <span class="fs-5 text-success fw-bold">${{ number_format($sale->total_price, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Pagado:</strong>
                                <span>${{ number_format($sale->paid_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Método:</strong>
                                <span>
                                    @if($sale->payment_method == 'cash')
                                        <span class="badge bg-success">💵 Efectivo</span>
                                    @elseif($sale->payment_method == 'card')
                                        <span class="badge bg-primary">💳 Tarjeta</span>
                                    @else
                                        <span class="badge bg-info">🏦 Transferencia</span>
                                    @endif
                                </span>
                            </div>
                            @if($sale->change > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Cambio:</strong>
                                    <span class="text-success fw-bold">${{ number_format($sale->change, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notas -->
                    @if($sale->notes)
                        <div class="alert alert-info mb-4">
                            <strong>Notas:</strong> {{ $sale->notes }}
                        </div>
                    @endif

                    <!-- Código de Barras del Recibo -->
                    <div class="text-center mb-4">
                        <small class="text-muted">N° de Recibo: {{ $sale->sale_number }}</small>
                        <div id="barcode" class="mt-2"></div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center mb-4">
                        <hr>
                        <p class="mb-1">
                            <i class="fas fa-check-circle text-success"></i> 
                            Gracias por su compra
                        </p>
                        <p class="text-muted mb-0">
                            <small>
                                <i class="fas fa-clock"></i> 
                                {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i') }}
                            </small>
                        </p>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button onclick="window.print()" class="btn btn-primary btn-lg">
                            <i class="fas fa-print"></i> Imprimir Recibo
                        </button>
                        <a href="{{ route('cashier.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-plus"></i> Nueva Venta
                        </a>
                        <a href="{{ route('cashier.history') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-history"></i> Historial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Imprimir -->
<div class="modal fade" id="printModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imprimir Recibo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea imprimir el recibo de la venta <strong>{{ $sale->sale_number }}</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">Imprimir</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    // Generar código de barras del recibo
    JsBarcode("#barcode", "{{ $sale->sale_number }}", {
        format: "CODE128",
        lineColor: "#000",
        width: 2,
        height: 50,
        displayValue: true,
        fontSize: 12,
        margin: 10
    });

    // Imprimir automáticamente después de 2 segundos
    setTimeout(function() {
        // Opcional: abrir modal de impresión
        // var modal = new bootstrap.Modal(document.getElementById('printModal'));
        // modal.show();
    }, 2000);

    // Atajo de teclado para imprimir (Ctrl+P)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
</script>
@endsection

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 20px;
            box-shadow: none;
        }
        .btn, .modal {
            display: none !important;
        }
        @page {
            size: auto;
            margin: 10mm;
        }
    }
</style>