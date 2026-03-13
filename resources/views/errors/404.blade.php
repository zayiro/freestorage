@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center error-container">
                
                <!-- Icono SVG de Error -->
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-exclamation-triangle-fill text-danger" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                </div>

                <h1 class="error-code">404</h1>
                <h2 class="h3 mb-3 fw-bold">¡Ups! Página no encontrada</h2>
                <p class="lead text-muted mb-4">
                    Lo sentimos, la página que estás buscando no existe o ha sido movida.
                </p>

                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg px-4 gap-3">
                        <i class="bi bi-house-door-fill me-2"></i>
                        Volver al Inicio
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-arrow-left me-2"></i>
                        Atrás
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection