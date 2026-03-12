<!-- resources/views/sale-items/index.blade.php -->

@extends('layouts.app')

@section('title', 'Ítems de Venta')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>📋 Ítems de Venta</h2>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Ítems</h5>
                    <h2 class="mb-0">{{ $statistics['total_items'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Cantidad Vendida</h5>
                    <h2 class="mb-0">{{ $statistics['total_quantity'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Ventas Totales</h5>
                    <h2 class="mb-0">${{ number_format($statistics['total_sales'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Descuentos</h5>
                    <h2 class="mb-0">-${{ number_format($statistics['total_discount'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Ítems -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Lista de Ítems</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Venta</th>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Descuento</th>
                            <th>Impuesto</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td><a href="{{ route('sales.show', $item->sale_id) }}">{{ $item->sale->sale_number }}</a></td>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                    @if($item->product_sku)
                                        <br><small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>${{ number_format($item->subtotal, 2) }}</td>
                                <td class="text-danger">-${{ number_format($item->discount, 2) }}</td>
                                <td>${{ number_format($item->tax, 2) }}</td>
                                <td>
                                    <strong>${{ number_format($item->final_total, 2) }}</strong>
                                </td>
                                <td>
                                    @if($item->status == 'completed')
                                        <span class="badge bg-success">Completado</span>
                                    @elseif($item->status == 'pending')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @else
                                        <span class="badge bg-danger">Cancelado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('sale-items.update', $item->id) }}" class="btn btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger" onclick="deleteItem({{ $item->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">
                                    No hay ítems registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection