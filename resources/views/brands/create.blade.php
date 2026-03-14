@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Crear Marca</h1>
        <form action="{{ route('brands.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="description">Descripción</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
        </form>
    </div>
@endsection