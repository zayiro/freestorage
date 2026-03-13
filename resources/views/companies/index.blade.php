@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Compañías</h1>
    <a href="{{ route('companies.create') }}" class="btn btn-primary mb-3">Crear Nueva Compañía</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(auth()->check() && auth()->user()->is_admin && $user->id !== auth()->id())
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Logo</th>
                <th>Nombre</th>
                <th>DNI</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($companies as $company)
                <tr>                    
                    <td class="align-middle">{{ $company->id }}</td>
                    <td class="align-middle">
                        @if($company->image)
                            <!-- img-thumbnail es una clase de Bootstrap que crea un borde redondeado -->
                            <img src="{{ asset('storage/' . $company->image) }}" style="max-width: 50px; max-height: 50px;" alt="Logo {{ $company->name }}">
                        @else
                            <span class="text-muted">Sin imagen</span>
                        @endif
                    </td>
                    <td class="align-middle">{{ $company->name }}</td>
                    <td class="align-middle">{{ $company->dni ?? 'N/A' }}</td>
                    <td class="align-middle">{{ $company->email }}</td>
                    <td class="align-middle">{{ $company->phone }}</td>
                    <td class="align-middle">
                        <a href="{{ route('companies.show', $company) }}" class="btn btn-info btn-sm">Ver</a>
                        @if(auth()->check())
                            <a href="{{ route('companies.edit', $company) }}" class="btn btn-warning btn-sm">Editar</a>
                        @endif
                        @if(auth()->check() && auth()->user()->email === 'ocampo@gmail.com')
                        <form action="{{ route('companies.destroy', $company) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Realmente desea eliminar?')">Eliminar</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay compañías registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif
</div>
@endsection