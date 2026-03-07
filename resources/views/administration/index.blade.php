@extends('layouts.app') {{-- Ajusta a tu layout --}}

@section('content')
    <div class="container">
        <h1>Administración</h1>
        <div class="list-group mt-3">            
            <a class="list-group-item list-group-item-action" href="{{ route('brands.index') }}">Marcas</a>
            <a class="list-group-item list-group-item-action" href="{{ route('categories.index') }}">Categorias</a>
            <a class="list-group-item list-group-item-action" href="{{ route('products.index') }}">Productos</a>
            <a class="list-group-item list-group-item-action" href="{{ route('users.index') }}">Usuarios</a>
            <a class="list-group-item list-group-item-action" href="{{ route('activity-logs.index') }}">Actividad</a>
            <a class="list-group-item list-group-item-action" href="{{ route('companies.show', $companyId) }}">Compañia</a>
            <a class="list-group-item list-group-item-action" href="{{ route('sales.index') }}">Ventas</a>
        </div>
    </div>
@endsection