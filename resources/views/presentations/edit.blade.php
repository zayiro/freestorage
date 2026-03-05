@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Editar Presentación</h1>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <form action="{{ route('presentations.update', $presentation) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $presentation->name) }}" required>
            </div>
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $presentation->description) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
@endsection