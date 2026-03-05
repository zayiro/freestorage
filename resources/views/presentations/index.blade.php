@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Presentaciones</h1>
    
    <!-- Filtros -->
    <form method="GET" action="{{ route('presentations.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <select name="producto_id" class="form-control">
                    <option value="">Todos los Productos</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-2">
                    <input type="checkbox" name="stock_bajo" id="stock_bajo" class="form-check-input" value="1" {{ request('stock_bajo') ? 'checked' : '' }}>
                    <label for="stock_bajo" class="form-check-label">Stock Bajo</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('presentations.create', ['product_id' => request('product_id')]) }}" class="btn btn-success">Nueva Presentación</a>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Presentación</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Precio Venta</th>
                <th>Stock</th>
                <th>Mín. Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presentations as $presentation)
                <tr class="{{ $presentation->inventario && $presentation->inventario->cantidad_actual <= $presentation->inventario->cantidad_minima ? 'table-warning' : '' }}">
                    <td>{{ $presentation->product->name }}</td>
                    <td>{{ $presentation->presentation }}</td>
                    <td>{{ $presentation->unit }}</td>
                    <td>{{ $presentation->stock }}</td>
                    <td>${{ number_format($presentation->sales_price, 2) }}</td>
                    <td>
                        <span class="badge {{ $presentation->inventario && $presentation->inventario->cantidad_actual > 0 ? 'badge-success' : 'badge-danger' }}">
                            {{ $presentation->inventario->cantidad_actual ?? 0 }}
                        </span>
                    </td>
                    <td>{{ $presentation->inventario->cantidad_minima ?? 0 }}</td>
                    <td>
                        <a href="{{ route('presentations.edit', $presentation->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <a href="{{ route('products.show', $presentation->product->id) }}" class="btn btn-info btn-sm">Ver Producto</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No hay presentaciones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginación -->
    {{ $presentations->links() }}
</div>
@endsection