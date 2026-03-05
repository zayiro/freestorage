@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Registrar Nuevo Usuario</h1>
        
        {{-- Mostrar errores --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach>
                </ul>
            </div>
        @endif
        
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="form-group mb-3">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                @error('password_confirmation')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection