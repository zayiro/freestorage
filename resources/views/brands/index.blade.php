@extends('layouts.app') {{-- Ajusta a tu layout --}}

@section('content')
    <div class="container">
        <h1>Marcas</h1>
        
        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        {{-- Botón para crear --}}
        <a href="{{ route('brands.create') }}" class="btn btn-primary mb-3">Crear Marca</a>
        
        {{-- Tabla de marcas --}}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                    <tr>
                        <td class="align-middle">{{ $brand->name }}</td>
                        <td class="align-middle">{{ $brand->description ?: 'Sin descripción' }}</td>
                        <td class="align-middle">
                            <a href="{{ route('brands.show', $brand) }}" class="btn btn-info btn-sm">Ver</a>
                            <a href="{{ route('brands.edit', $brand) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('brands.destroy', $brand) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta marca?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No hay marcas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection