@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Categorías</h1>
        {{-- Botón para crear --}}
        <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">Crear Categoría</a>
        @if(!empty(($categories)))             
            @if(session('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td class="align-middle">{{ $category->name }}</td>
                            <td class="align-middle">{{ $category->description }}</td>
                            <td class="align-middle">
                                <a href="{{ route('categories.show', $category) }}" class="btn btn-info">Ver</a>
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">Editar</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else            
            <div>No tiene categorias registradas en el momento.</div>             
        @endif
    </div>
@endsection