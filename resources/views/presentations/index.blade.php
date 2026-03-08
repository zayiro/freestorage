@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Presentaciones</h1>
    
    <!-- Filtros -->
    <form method="GET" action="{{ route('presentations.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <select name="product_id" class="form-control">
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
                <th>Stock</th>
                <th>Precio Venta</th>
                <th>Inventario</th>
                <th>Mín. Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presentations as $presentation)
                <tr class="{{ $presentation->inventory && $presentation->inventory->current_quantity <= $presentation->inventory->minimun_quantity ? 'table-warning' : '' }}">
                    <td class="align-baseline">{{ $presentation->product->id }}</td>
                    <td class="align-baseline">{{ $presentation->product->name }}</td>
                    <td class="align-baseline">{{ $presentation->presentation }}</td>
                    <td class="align-baseline">{{ $presentation->unit }}</td>
                    <td class="align-baseline">{{ $presentation->stock }}</td>
                    <td class="align-baseline">$ {{ number_format($presentation->sales_price, 2) }}</td>
                    <td class="align-baseline">
                        <span class="badge {{ $presentation->inventory && $presentation->inventory->current_quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $presentation->inventory->current_quantity ?? 0 }}
                        </span>
                    </td>
                    <td class="align-baseline">{{ $presentation->inventory->minimum_quantity ?? 0 }}</td>
                    <td class="align-baseline">
                        <div class="d-flex gap-2">
                            <a href="{{ route('presentations.edit', $presentation->id) }}" class="btn btn-warning btn-sm shadow"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="{{ route('products.show', $presentation->product->id) }}" class="btn btn-info btn-sm shadow" target="_blank"><i class="fa-brands fa-product-hunt"></i></a>
                        </div>
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