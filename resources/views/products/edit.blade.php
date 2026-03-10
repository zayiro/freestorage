@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Producto</h1>
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group mb-3">
            <label for="nombre">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
        </div>
        <div class="form-group mb-3">
            <label for="image">Imagen</label><br>
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="Imagen actual" style="max-width: 100px; max-height: 100px;">
                <p>Deja vacío para mantener la imagen actual.</p>
            @endif
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            @error('image')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="descripcion">Descripción</label>
            <textarea name="description" id="description" class="form-control">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="form-group mb-3">
            <label for="categoria_id">Categoría</label>
            <select name="category_id" id="category_id" class="form-control">
                <option value="">Selecciona una categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="brand_id">Marca</label>
            <select name="brand_id" id="brand_id" class="form-control">
                <option value="">Sin Marca</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ (isset($product) && $product->brand_id == $brand->id) || old('brand_id') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
            @error('brand_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        @if($product->barcode)
            <div class="form-group mb-3">
                <label>Código de Barras Actual</label>
                <img src="{{ Storage::url($product->barcode) }}" alt="Código de Barras" style="max-width: 300px;">
            </div>
        @endif

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection