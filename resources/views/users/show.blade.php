@extends('layouts.app') {{-- Ajusta a tu layout --}}

@section('content')
    <div class="container">
        <h1>Detalles del Usuario: {{ $user->name }}</h1>
        
        <div class="card">
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Admin:</strong> {{ $user->is_admin ? 'Sí' : 'No' }}</p>
                <p><strong>Compañía:</strong> {{ $user->company->name ?? 'N/A' }}</p>
                <p><strong>Creado en:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Actualizado en:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        
        {{-- Enlaces a acciones --}}
        <div class="mt-3">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Volver al Listado</a>
            @if(auth()->user()->is_admin && $user->id !== auth()->id())
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">Editar</a>
                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</button>
                </form>
            @endif
        </div>
    </div>
@endsection