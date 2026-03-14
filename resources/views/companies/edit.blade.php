@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Editar Compañía</h1>
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group mb-3">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $company->name) }}" required>
        </div>
        <div class="form-group mb-3">
            <label for="dni">DNI</label>
            <input type="text" name="dni" id="dni" class="form-control" value="{{ old('dni', $company->dni) }}">
        </div>
        <div class="form-group mb-3">
            <label for="address">Dirección</label>
            <textarea name="address" id="address" class="form-control">{{ old('address', $company->address) }}</textarea>
        </div>
        <div class="form-group mb-3">
            <label for="phone">Teléfono</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
        </div>
        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $company->email) }}" required>
        </div>
        <div class="form-group mb-3">
            <label for="image">Imagen</label><br>
            @if($company->image)
                <img src="{{ asset('storage/' . $company->image) }}" alt="Imagen actual" style="max-width: 100px; max-height: 100px;">
                <p>Deja vacío para mantener la imagen actual.</p>
            @endif
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            @error('image')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('companies.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection