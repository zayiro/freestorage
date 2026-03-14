@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Crear Producto</h1>
        {{-- Mostrar errores de validación --}}
        @if($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Información de Límite -->
        @if($limitCheck['success'])
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                <strong>Estado:</strong> Puedes crear {{ $limitCheck['remaining'] }} producto(s) más.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @else
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Limitación:</strong> {{ $limitCheck['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-3">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="image">Imagen</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                @error('image')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="description">Descripción</label>
                <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                @error('description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="category_id">Categoría</label>
                <select name="category_id" id="category_id" class="form-control">
                    <option value="">Sin Categoría</option>
                    @foreach(\App\Models\Category::where('company_id', auth()->user()->company_id)->get() as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                 @error('category_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="brand_id">Marca</label>
                <select name="brand_id" id="brand_id" class="form-control">
                    <option value="">Sin Marca</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
                @error('brand_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection