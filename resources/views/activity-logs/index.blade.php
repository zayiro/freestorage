@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📋 Registro de Actividad</h5>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('activity-logs.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Usuario</label>
                                <select name="user_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipo de Acción</label>
                                <select name="log_name" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="created" {{ request('log_name') == 'created' ? 'selected' : '' }}>Creado</option>
                                    <option value="updated" {{ request('log_name') == 'updated' ? 'selected' : '' }}>Actualizado</option>
                                    <option value="deleted" {{ request('log_name') == 'deleted' ? 'selected' : '' }}>Eliminado</option>
                                    <option value="restored" {{ request('log_name') == 'restored' ? 'selected' : '' }}>Restaurado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Desde</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hasta</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                                <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de Logs -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Modelo</th>
                                    <th>Descripción</th>
                                    <th>Fecha</th>
                                    <th>IP</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->id }}</td>
                                        <td>
                                            @if($activity->user)
                                                <strong>{{ $activity->user->name }}</strong>
                                            @else
                                                <span class="text-muted">Sistema</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $activity->log_name == 'created' ? 'success' : ($activity->log_name == 'updated' ? 'primary' : ($activity->log_name == 'deleted' ? 'danger' : 'secondary')) }}">
                                                {{ ucfirst($activity->log_name) }}
                                            </span>
                                        </td>
                                        <td>{{ $activity->subject_type }}</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ Str::limit($activity->description, 50) }}
                                            </small>
                                        </td>
                                        <td>
                                            {{ $activity->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>{{ $activity->ip_address ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('activity-logs.show', $activity) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No hay registros de actividad
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($activities->hasPages())
                        <nav aria-label="Navegación de registros">
                            {{ $activities->links() }}
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection