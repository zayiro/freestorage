@extends('layouts.app') {{-- Adjust to your layout file --}}

@section('content')
    <div class="container mt-5">
        <p>
            @if($company->image)
                
                <img src="{{ asset('storage/' . $company->image) }}" class="img-thumbnail" alt="Logo {{ $company->name }}">
            @else
                <span class="text-muted">Sin imagen</span>
            @endif
        </p>
        <h1>{{ $company->name }}</h1>
        <p><strong>DNI:</strong> {{ $company->dni ?? 'N/A' }}</p>
        <p><strong>Correo electrónico:</strong> {{ $company->email }}</p>
        <p><strong>Dirección:</strong> {{ $company->address }}</p>
        <p><strong>Teléfono:</strong> {{ $company->phone }}</p>
        @if(auth()->check())
            <a href="{{ route('companies.edit', $company) }}" class="btn btn-warning btn-sm">Editar</a>
        @endif
    </div>
@endsection