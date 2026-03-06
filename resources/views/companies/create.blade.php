@extends('layouts.app')

@section('content')
<div class="container">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h1>Crear Compañía</h1>
    <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-3">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="dni">DNI</label>
            <input type="text" name="dni" id="dni" class="form-control">
        </div>
        <div class="form-group mb-3">
            <label for="address">Dirección</label>
            <textarea name="address" id="address" class="form-control"></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="phone">Teléfono</label>
            <input type="text" name="phone" id="phone" class="form-control">
        </div>
        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <!-- Input para la imagen -->
        <div class="form-group mb-3">
            <label for="image" class="form-label">Logo / Imagen</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <!-- Opcional: Mostrar vista previa pequeña -->
            <img id="preview" src="#" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 100px;">
        </div>

        <h3>Datos del Usuario Administrador</h3>
        <div class="form-group mb-3">
            <label for="admin_name">Nombre del Admin</label>
            <input type="text" name="admin_name" id="admin_name" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="admin_email">Email del Admin</label>
            <input type="email" name="admin_email" id="admin_email" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="admin_password">Contraseña</label>
            <input type="password" name="admin_password" id="admin_password" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="admin_password_confirmation">Confirmar Contraseña</label>
            <input type="password" name="admin_password_confirmation" id="admin_password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Crear</button>
        <a href="{{ route('companies.index') }}" class="btn btn-secondary">Regresar a la lista</a>
    </form>
</div>
@endsection