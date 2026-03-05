@extends('layouts.app') {{-- Ajusta a tu layout --}}

@section('content')
    <div class="container">
        <h1>Editar Marca</h1>
        
        {{-- Mostrar errores de validación --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach>
                </ul>
            </div>
        @endif
        
        <form action="{{ route('brands.update', $brand) }}" method="POST">
            @csrf
            @method('PUT') {{-- Simula una solicitud PUT --}}
            
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $brand->name) }}" required>
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $brand->description) }}</textarea>
                @error('description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">Actualizar Marca</button>
            <a href="{{ route('brands.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection