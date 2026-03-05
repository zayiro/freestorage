{{-- resources/views/categories/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $category->name }}</h1>
        <p><strong>Descripción:</strong> {{ $category->description ?: 'Sin descripción' }}</p>
        <p><strong>Compañía:</strong> {{ $category->company->name }}</p>
        <p><strong>Creada el:</strong> {{ $category->created_at->format('d/m/Y H:i') }}</p>
        
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Volver a la Lista</a>
        <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">Editar</a>
        
        {{-- Opcional: Mostrar productos asociados si tienes la relación --}}
        @if($category->products ?? false)
            <h2>Productos en esta Categoría</h2>
            <ul>
                @foreach($category->products as $product)
                    <li>{{ $product->name }} - ${{ $product->price }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection