@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Presentación</h1>
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <h5>Errores encontrados:</h5>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('presentations.store') }}" method="POST">
        @csrf
        
        <!-- Selección de Producto -->
        <div class="form-group mb-3">
            <label for="product_id">Producto</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">Selecciona un producto</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id', $productoSeleccionado->id ?? '') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Nombre de Presentación -->
        <div class="form-group mb-3">
            <label for="presentation">Nombre de Presentación</label>
            <input type="text" name="presentation" id="presentation" class="form-control" placeholder="Ej. Unidad, Paquete de 10, Caja de 100" value="{{ old('presentation') }}" required>
        </div>

        <!-- Unidad de Medida -->
        <div class="form-group mb-3">
            <label for="unit">Unidad de Medida</label>
            <select name="unit" id="unit" class="form-control" required>
                <option value="unidad" {{ old('unit') == 'unidad' ? 'selected' : '' }}>Unidad</option>
                <option value="pieza" {{ old('unit') == 'pieza' ? 'selected' : '' }}>Pieza</option>
                <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogramo</option>
                <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>Gramo</option>
                <option value="litro" {{ old('unit') == 'litro' ? 'selected' : '' }}>Litro</option>
                <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>Mililitro</option>
                <option value="metro" {{ old('unit') == 'metro' ? 'selected' : '' }}>Metro</option>
                <option value="cm" {{ old('unit') == 'cm' ? 'selected' : '' }}>Centímetro</option>
                <option value="caja" {{ old('unit') == 'caja' ? 'selected' : '' }}>Caja</option>
                <option value="paquete" {{ old('unit') == 'paquete' ? 'selected' : '' }}>Paquete</option>
                <option value="bolsa" {{ old('unit') == 'bolsa' ? 'selected' : '' }}>Bolsa</option>
                <option value="bulto" {{ old('unit') == 'bulto' ? 'selected' : '' }}>Bulto</option>
            </select>
        </div>

        <!-- Precios -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="purchase_price">Precio de Compra</label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control" value="{{ old('purchase_price') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sales_price">Precio de Venta</label>
                    <input type="number" step="0.01" name="sales_price" id="sales_price" class="form-control" value="{{ old('sales_price') }}" required>
                </div>
            </div>
        </div>

        <!-- Código de Barras -->
        <div class="form-group mb-3">
            <label for="barcode">Código de Barras (Opcional)</label>
            <input type="text" name="barcode" id="barcode" class="form-control" value="{{ old('barcode') }}">
        </div>

        <!-- Inventario Inicial -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="current_quantity">Stock Inicial</label>
                    <input type="number" name="current_quantity" id="current_quantity" class="form-control" value="{{ old('current_quantity', 0) }}" min="0">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="minimum_quantity">Stock Mínimo (Alerta)</label>
                    <input type="number" name="minimum_quantity" id="minimum_quantity" class="form-control" value="{{ old('minimum_quantity', 0) }}" min="0">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="location">Ubicación</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location', 'General') }}">
                </div>
            </div>
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-primary">Crear Presentación</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection