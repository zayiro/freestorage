<!-- resources/views/cashier/dashboard.blade.php -->

@extends('layouts.app')

@section('title', 'Dashboard de Caja')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">📊 Dashboard de Caja</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar-day"></i> 
                        {{ \Carbon\Carbon::today()->format('l, d \d\e F \d\e Y') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('cashier.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-cash-register"></i> Nueva Venta
                    </a>
                    <a href="{{ route('cashier.close') }}" class="btn btn-danger btn-lg">
                        <i class="fas fa-lock"></i> Cerrar Caja
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Ventas del Día
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($todaySales['total'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Transacciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $todaySales['count'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Promedio Venta
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($todaySales['average'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Productos Vendidos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $todaySales['total_items'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métodos de Pago -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">💳 Métodos de Pago</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-success bg-opacity-10 rounded">
                                <div class="bg-success text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-success">${{ number_format($todaySales['cash'] ?? 0, 2) }}</h5>
                                    <small class="text-muted">{{ $todaySales['cash_percentage'] ?? 0 }}% del total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-primary bg-opacity-10 rounded">
                                <div class="bg-primary text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-credit-card fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-primary">${{ number_format($todaySales['card'] ?? 0, 2) }}</h5>
                                    <small class="text-muted">{{ $todaySales['card_percentage'] ?? 0 }}% del total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-info bg-opacity-10 rounded">
                                <div class="bg-info text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-university fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-info">${{ number_format($todaySales['transfer'] ?? 0, 2) }}</h5>
                                    <small class="text-muted">{{ $todaySales['transfer_percentage'] ?? 0 }}% del total</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Estadísticas -->
    <div class="row mb-4">
        <!-- Ventas por Hora -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📈 Ventas por Hora</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesByHourChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Productos -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🏆 Top Productos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cant.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::limit($product->product_name, 20) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $product->total_quantity }}</span>
                                        </td>
                                        <td>
                                            ${{ number_format($product->total_quantity * $product->price, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            No hay datos disponibles
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas Recientes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">📋 Ventas Recientes</h6>
                    <a href="{{ route('cashier.history') }}" class="btn btn-sm btn-outline-primary">
                        Ver Todas <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>N° Venta</th>
                                    <th>Hora</th>
                                    <th>Cajero</th>
                                    <th>Productos</th>
                                    <th>Método</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todaySales['sales'] ?? [] as $sale)
                                    <tr>
                                        <td>
                                            <strong>{{ $sale->sale_number }}</strong>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($sale->created_at)->format('H:i') }}
                                        </td>
                                        <td>
                                            {{ $sale->cashier->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $sale->items->sum('quantity') }} ítems
                                        </td>
                                        <td>
                                            @if($sale->payment_method == 'cash')
                                                <span class="badge bg-success">💵 Efectivo</span>
                                            @elseif($sale->payment_method == 'card')
                                                <span class="badge bg-primary">💳 Tarjeta</span>
                                            @else
                                                <span class="badge bg-info">🏦 Transferencia</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>${{ number_format($sale->total_price, 2) }}</strong>
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
                                            <a href="{{ route('cashier.receipt', $sale->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No hay ventas registradas hoy</p>
                                            <a href="{{ route('cashier.index') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Crear Nueva Venta
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Movimientos de Caja -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">💰 Movimientos de Caja</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                    <i class="fas fa-arrow-up fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-success">Ingresos</h5>
                                    <small class="text-muted">${{ number_format($todayMovements['income'] ?? 0, 2) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger bg-opacity-10 rounded p-3 me-3">
                                    <i class="fas fa-arrow-down fa-2x text-danger"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-danger">Egresos</h5>
                                    <small class="text-muted">${{ number_format($todayMovements['expense'] ?? 0, 2) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                    <i class="fas fa-wallet fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-primary">Balance</h5>
                                    <small class="text-muted">${{ number_format($todayMovements['balance'] ?? 0, 2) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($todayMovements['movements'] && $todayMovements['movements']->count() > 0)
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Referencia</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayMovements['movements'] as $movement)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($movement->created_at)->format('H:i') }}</td>
                                            <td>
                                                @if($movement->type == 'income')
                                                    <span class="badge bg-success">Ingreso</span>
                                                @else
                                                    <span class="badge bg-danger">Egreso</span>
                                                @endif
                                            </td>
                                            <td>{{ $movement->description }}</td>
                                            <td>{{ $movement->reference ?? '-' }}</td>
                                            <td class="{{ $movement->type == 'income' ? 'text-success' : 'text-danger' }}">
                                                {{ $movement->type == 'income' ? '+' : '-' }}${{ number_format($movement->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos para el gráfico de ventas por hora
    const salesByHourData = @json($salesByHourData ?? []);
    
    // Datos para el gráfico de métodos de pago
    const paymentMethodData = @json($paymentMethodData ?? []);

    // Gráfico de Ventas por Hora
    const ctxSalesByHour = document.getElementById('salesByHourChart').getContext('2d');
    const salesByHourChart = new Chart(ctxSalesByHour, {
        type: 'bar',
        data: {
            labels: salesByHourData.map(item => item.hour),
            datasets: [{
                label: 'Ventas por Hora',
                data: salesByHourData.map(item => item.total),
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                title: {
                    display: true,
                    text: '📈 Ventas por Hora del Día',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Total: $' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    },
                    title: {
                        display: true,
                        text: 'Monto Total ($)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Hora del Día'
                    }
                }
            }
        }
    });

    // Gráfico de Métodos de Pago (Opcional)
    const ctxPaymentMethod = document.getElementById('paymentMethodChart');
    if (ctxPaymentMethod) {
        const paymentMethodChart = new Chart(ctxPaymentMethod, {
            type: 'doughnut',
            data: {
                labels: paymentMethodData.map(item => item.method),
                datasets: [{
                    data: paymentMethodData.map(item => item.total),
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',  // Efectivo
                        'rgba(0, 123, 255, 0.8)',  // Tarjeta
                        'rgba(23, 162, 184, 0.8)', // Transferencia
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(0, 123, 255, 1)',
                        'rgba(23, 162, 184, 1)',
                    ],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: '💳 Métodos de Pago',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return context.label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Actualizar gráfico cada 5 minutos
    setInterval(function() {
        // Recargar datos del servidor
        fetch('{{ route("cashier.dashboard") }}')
            .then(response => response.text())
            .then(html => {
                // Actualizar solo el gráfico sin recargar toda la página
                const newChart = document.getElementById('salesByHourChart');
                if (newChart) {
                    // Opcional: actualizar datos
                }
            });
    }, 300000); // 5 minutos

    // Auto-refresh de la página cada 10 minutos
    setInterval(function() {
        location.reload();
    }, 600000); // 10 minutos
</script>
@endsection