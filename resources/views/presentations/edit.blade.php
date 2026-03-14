<!-- resources/views/inventory/presentations/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Presentación</h1>
        @if($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <a href="{{ route('presentations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Información de la Presentación</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('presentations.update', $presentation->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="form-group mb-3">
                                <div>Producto asociado</div>
                                <strong for="presentation">{{ $presentation->product->name }}</strong>                                
                            </div>

                            <!-- Nombre de Presentación -->
                            <div class="form-group mb-3">
                                <label for="presentation">Nombre de Presentación</label>
                                <input type="text" name="presentation" id="presentation" class="form-control" placeholder="Ej. Unidad, Paquete de 10, Caja de 100" value="{{ old('presentation', $presentation->presentation) }}" required>
                                @error('presentation')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Unidad de Medida -->
                            <div class="form-group mb-3">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
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

                                    <!-- Estado -->
                                    <div class="col-md-6 mb-3">
                                        <label for="active" class="form-label">Estado</label>
                                        <select name="active" id="active" class="form-select">
                                            <option value="1" {{ old('active', $presentation->active) ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ !old('active', $presentation->active) ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                    </div>
                                </div>                                
                            </div>

                            <!-- Precios -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="purchase_price">Precio de Compra</label>
                                        <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control" value="{{ old('purchase_price', $presentation->purchase_price) }}" required>
                                        @error('purchase_price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sales_price">Precio de Venta</label>
                                        <input type="number" step="0.01" name="sales_price" id="sales_price" class="form-control" value="{{ old('sales_price', $presentation->sales_price) }}" required>
                                        @error('sales_price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>                            

                            <!-- Inventario Inicial -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="current_quantity">Stock Inicial</label>
                                        <input type="number" name="current_quantity" id="current_quantity" class="form-control" value="{{ old('current_quantity', $presentation->inventory->current_quantity) }}" min="0">
                                        @error('current_quantity')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="minimum_quantity">Stock Mínimo (Alerta)</label>
                                        <input type="number" name="minimum_quantity" id="minimum_quantity" class="form-control" value="{{ old('minimum_quantity', $presentation->inventory->minimum_quantity) }}" min="0">
                                        @error('minimum_quantity')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location">Ubicación</label>
                                        <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $presentation->inventory->location) }}">
                                        @error('location')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('presentations.index', $presentation->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Información Rápida -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Información Rápida</h5>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> {{ $presentation->id }}</p>
                    <p><strong>Nombre:</strong> {{ $presentation->presentation }}</p>
                    <p><strong>Stock:</strong> {{ $presentation->stock }}</p>
                    <p><strong>Estado:</strong> 
                        <span class="badge bg-{{ $presentation->active ? 'success' : 'secondary' }}">
                            {{ $presentation->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </p>
                    <p><strong>Fecha Creación:</strong> {{ $presentation->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5>Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('presentations.destroy', $presentation->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Eliminar presentación?')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection