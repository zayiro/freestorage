@extends('layouts.app') {{-- Ajusta si tu layout es diferente --}}

@section('content')
    <div class="container mt-5">
        <h1>Editar Categoría</h1>
        
        {{-- Mostrar errores de validación --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT') {{-- Simula una solicitud PUT --}}
            
            <div class="form-group mb-3">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $category->name) }}" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="description">Descripción</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $category->description) }}</textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Actualizar Categoría</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection