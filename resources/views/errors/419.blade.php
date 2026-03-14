@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="error-container">
        <div class="error-card">
            <!-- Icono animado -->
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>

            <!-- Código de error -->
            <div class="error-code">419</div>

            <!-- Título -->
            <h1 class="error-title">Página Expirada</h1>

            <!-- Subtítulo -->
            <p class="error-subtitle">
                La página ha expirado debido a inactividad o falta de verificación de seguridad.
            </p>

            <!-- Detalles técnicos -->
            <div class="error-details">
                <strong><i class="fas fa-info-circle me-2"></i>¿Qué significa esto?</strong><br>
                <small>
                    Laravel requiere un token CSRF para validar las peticiones POST, PUT, PATCH o DELETE.<br>
                    Este error ocurre cuando:
                    <ul class="mt-2 mb-0">
                        <li>• El formulario expiró por inactividad</li>
                        <li>• Falta el token @csrf en formularios POST</li>
                        <li>• Problemas con cookies de sesión</li>
                        <li>• Cache de vistas desactualizado</li>
                    </ul>
                </small>
            </div>

            <!-- Botones de acción -->
            <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
                <a href="{{ url()->previous() }}" class="btn btn-custom btn-primary-custom">
                    <i class="fas fa-arrow-left"></i>
                    Volver atrás
                </a>

                <a href="{{ route('scanner') }}" class="btn btn-custom btn-primary-custom">
                    <i class="fas fa-barcode"></i>
                    Ir al escáner
                </a>

                <a href="{{ url('/') }}" class="btn btn-custom btn-secondary-custom">
                    <i class="fas fa-home"></i>
                    Inicio
                </a>
            </div>

            <!-- Botón de recarga forzada -->
            <div class="text-center">
                <button onclick="location.reload()" class="btn btn-outline-danger btn-lg px-4">
                    <i class="fas fa-sync-alt me-2"></i>
                    Recargar página
                </button>
            </div>
        </div>

        <!-- Información de debug -->
        <div class="debug-info">
            <strong>Debug Info:</strong><br>
            URL: {{ request()->fullUrl() }}<br>
            Method: {{ request()->method() }}<br>
            Session: {{ session()->token() ? 'OK' : 'NO' }}<br>
            User Agent: {{ request()->userAgent() }}
        </div>
    </div>
</div>
@endsection    
