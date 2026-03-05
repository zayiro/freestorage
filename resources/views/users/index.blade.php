@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Usuarios de Mi Compañía</h1>
        
        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        {{-- Botón para crear --}}
        @if(auth()->user()->is_admin)
            <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Registrar Nuevo Usuario</a>
        @endif
        
        {{-- Tabla de usuarios --}}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Creado en</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="align-middle">{{ $user->name }}</td>
                        <td class="align-middle">{{ $user->email }}</td>
                        <td class="align-middle">{{ $user->is_admin ? 'Sí' : 'No' }}</td>
                        <td class="align-middle">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="align-middle">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm">Ver</a>
                            @if(auth()->user()->is_admin && $user->id !== auth()->id())
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay usuarios registrados en tu compañía.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection