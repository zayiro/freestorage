@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Productos</h1>
        {{-- Botón para crear --}}
        <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Crear Producto</a>
        @if(!empty(($products)))
            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
                        
            {{-- Tabla de productos --}}
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Marca</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="align-middle">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" style="max-width: 50px; max-height: 50px;" alt="Logo {{ $product->name }}">
                                @endif
                            </td>
                            <td class="align-middle">{{ $product->name }}</td>
                            <td class="align-middle">{{ $product->brand->name ?? 'Sin marca' }}</td>
                            <td class="align-middle">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">Ver</a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Editar</a>
                                <a href="{{ route('presentations.create', ['product_id' => $product->id]) }}" class="btn btn-success btn-sm" title="Agregar Presentación">
                                    + Presentación
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este producto?')">Eliminar</button>
                                </form>                            
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @else            
            <div>No tiene productos registrados en el momento.</div>             
        @endif
    </div>
@endsection