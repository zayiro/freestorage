@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $presentation->name }}</h1>
        <div class="card">
            <div class="card-body">
                <p><strong>Descripción:</strong> {{ $presentation->description ?: 'Sin descripción' }}</p>
                <p><strong>Compañía:</strong> {{ $presentation->company->name ?? 'N/A' }}</p>
                <p><strong>Creada en:</strong> {{ $presentation->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
        <a href="{{ route('presentations.index') }}" class="btn btn-secondary">Volver</a>
    </div>
@endsection