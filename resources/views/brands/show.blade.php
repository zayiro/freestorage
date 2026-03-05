@extends('layouts.app') {{-- Ajusta a tu layout --}}

@section('content')
    <div class="container">
        <h1>{{ $brand->name }}</h1>
        
        <div class="card">
            <div class="card-body">
                <p><strong>Descripción:</strong> {{ $brand->description ?: 'Sin descripción' }}</p>
                <p><strong>Compañía:</strong> {{ $brand->company->name ?? 'N/A' }}</p>
                <p><strong>Creada en:</strong> {{ $brand->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Actualizada en:</strong> {{ $brand->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        
        {{-- Enlaces a acciones --}}
        <div class="mt-3">
            <a href="{{ route('brands.index') }}" class="btn btn-secondary">Volver al Listado</a>
            <a href="{{ route('brands.edit', $brand) }}" class="btn btn-warning">Editar</a>
            <form action="{{ route('brands.destroy', $brand) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar esta marca?')">Eliminar</button>
            </form>
        </div>
    </div>
@endsection