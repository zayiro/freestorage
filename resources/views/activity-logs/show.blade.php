@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">📋 Detalle del Registro #{{ $activity->id }}</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información General</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $activity->id }}</td>
                                </tr>
                                <tr>
                                    <th>Usuario</th>
                                    <td>{{ $activity->user ? $activity->user->name : 'Sistema' }}</td>
                                </tr>
                                <tr>
                                    <th>Acción</th>
                                    <td>
                                        <span class="badge bg-{{ $activity->log_name == 'created' ? 'success' : ($activity->log_name == 'updated' ? 'primary' : ($activity->log_name == 'deleted' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($activity->log_name) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Modelo</th>
                                    <td>{{ $activity->subject_type }}</td>
                                </tr>
                                <tr>
                                    <th>Descripción</th>
                                    <td>{{ $activity->description }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha</th>
                                    <td>{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>IP</th>
                                    <td>{{ $activity->ip_address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>User Agent</th>
                                    <td>{{ $activity->user_agent ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Propiedades Cambiadas</h5>
                            @if($activity->properties)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Campo</th>
                                                <th>Antes</th>
                                                <th>Después</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activity->properties['changes'] ?? [] as $key => $value)
                                                <tr>
                                                    <td><strong>{{ $key }}</strong></td>
                                                    <td>{{ $value['old'] ?? '-' }}</td>
                                                    <td>{{ $value['new'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No hay cambios registrados</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                            ← Volver a la lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection