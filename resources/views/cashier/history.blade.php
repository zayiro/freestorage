<!-- resources/views/cashier/history.blade.php -->

@extends('layouts.app')

@section('title', 'Historial de Ventas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>📊 Historial de Ventas</h2>
                <div>
                    <a href="{{ route('cashier.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Venta
                    </a>
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen del Periodo -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Ventas Hoy</h5>
                    <h2 class="mb-0">{{ $todaySales['count'] ?? 0 }}</h2>
                    <small>${{ number_format($todaySales['total'] ?? 0, 2) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Periodo</h5>
                    <h2 class="mb-0">${{ number_format($totalSales['total'] ?? 0, 2) }}</h2>
                    <small>{{ $totalSales['count'] ?? 0 }} ventas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Promedio Venta</h5>
                    <h2 class="mb-0">${{ number_format($totalSales['average'] ?? 0, 2) }}</h2>
                    <small>por venta</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Métodos de Pago</h5>
                    <h5 class="mb-0">
                        <span class="badge bg-success">💵 Efectivo: {{ $paymentMethods['cash'] ?? 0 }}</span>
                        <span class="badge bg-primary">💳 Tarjeta: {{ $paymentMethods['card'] ?? 0 }}</span>
                        <span class="badge bg-info">🏦 Transferencia: {{ $paymentMethods['transfer'] ?? 0 }}</span>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">🔍 Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('cashier.history') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Número de venta...">
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Desde</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" 
                               value="{{ request('date_from', date('Y-m-01')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Hasta</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" 
                               value="{{ request('date_to', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Estado</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Aplicar Filtros
                        </button>
                        <a href="{{ route('cashier.history') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📋 Lista de Ventas</h5>
            <span class="badge bg-info">{{ $sales->total() }} registros</span>
        </div>
        <div class="card-body">
            @if($sales->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>N° Venta</th>
                                <th>Fecha</th>
                                <th>Cajero</th>
                                <th>Productos</th>
                                <th>Subtotal</th>
                                <th>Descuento</th>
                                <th>Impuestos</th>
                                <th>Total</th>
                                <th>Pago</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                                <tr>
                                    <td>{{ $sales->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $sale->invoice_number }}</strong>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        {{ $sale->cashier->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $sale->total_items }} ítems
                                    </td>
                                    <td>${{ number_format($sale->subtotal, 2) }}</td>
                                    <td class="text-danger">-${{ number_format($sale->discount, 2) }}</td>
                                    <td>${{ number_format($sale->tax, 2) }}</td>
                                    <td>
                                        <strong>${{ number_format($sale->total_price, 2) }}</strong>
                                    </td>
                                    <td>
                                        ${{ number_format($sale->paid_amount, 2) }}
                                        @if($sale->change > 0)
                                            <br><small class="text-success">Cambio: ${{ number_format($sale->change, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($sale->status == 'completed')
                                            <span class="badge bg-success">Completado</span>
                                        @elseif($sale->status == 'pending')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @else
                                            <span class="badge bg-danger">Cancelado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('cashier.receipt', $sale->id) }}" class="btn btn-info" title="Ver Recibo">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            <a href="{{ route('cashier.history') }}?search={{ $sale->sale_number }}" class="btn btn-secondary" title="Buscar">
                                                <i class="fas fa-search"></i>
                                            </a>
                                            @if($sale->status == 'completed')
                                                <button type="button" class="btn btn-danger" 
                                                        onclick="confirmCancelSale({{ $sale->id }})" title="Cancelar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $sales->links() }}
                </div>

            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No se encontraron ventas</h4>
                    <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                    <a href="{{ route('cashier.history') }}" class="btn btn-primary">
                        <i class="fas fa-redo"></i> Limpiar Filtros
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Cancelar Venta -->
<div class="modal fade" id="cancelSaleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas cancelar la venta <strong id="cancelSaleNumber"></strong>?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="cancelSaleForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sí, Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Función para confirmar cancelación de venta
    function confirmCancelSale(saleId) {
        const modal = new bootstrap.Modal(document.getElementById('cancelSaleModal'));
        const form = document.getElementById('cancelSaleForm');
        
        // Obtener el número de venta
        const row = document.querySelector(`tr td:nth-child(2)`);
        const saleNumber = row ? row.textContent.trim() : 'N/A';
        
        document.getElementById('cancelSaleNumber').textContent = saleNumber;
        form.action = `/cashier/sales/${saleId}`;
        
        modal.show();
    }

    // Auto-refresh cada 30 segundos
    setInterval(function() {
        location.reload();
    }, 30000);
</script>
@endsection