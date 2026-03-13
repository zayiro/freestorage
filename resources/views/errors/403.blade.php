@extends('layouts.app')

@section('content')  
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center error-container">
                
                <!-- Icono SVG de Candado -->
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-shield-lock-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 1a2 2 0 0 1 2 2v4.657a1 1 0 0 1-.293.707L6.343 11.4a1 1 0 0 1-1.414 0L2.293 8.364A1 1 0 0 1 2 7.657V3a2 2 0 0 1 2-2h4zm-1 4h2V3a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v4.657l3.293 3.293a.5.5 0 0 0 .707 0L7 7.657V5zm1 1.5a.5.5 0 0 0-1 0v1.5H6a.5.5 0 0 0 0 1h1.5v1.5a.5.5 0 0 0 1 0V9H10a.5.5 0 0 0 0-1H8.5V6.5H10a.5.5 0 0 0 0-1H8.5V5z"/>
                    </svg>
                </div>

                <h1 class="error-code">403</h1>
                <h2 class="h3 mb-3 fw-bold">Acceso Denegado</h2>
                <p class="lead text-muted mb-4">
                    Lo sentimos, no tienes los permisos necesarios para ver esta página.
                </p>

                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg px-4 gap-3">
                        <i class="bi bi-house-door-fill me-2"></i>
                        Ir al Inicio
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-arrow-left me-2"></i>
                        Atrás
                    </a>
                </div>
                
                <div class="mt-4 text-muted small">
                    <p>¿Necesitas acceso? <a href="mailto:ocampotecnologo@gmail.com">Contacta al soporte</a></p>
                </div>

            </div>
        </div>
    </div>
@endsection