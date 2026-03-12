<!-- resources/views/cashier/close.blade.php -->

@extends('layouts.app')

@section('title', 'Cierre de Caja')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>🔒 Cierre de Caja</h2>
                <a href="{{ route('cashier.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen del Día -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Ventas del Día</h5>
                    <h2 class="mb-0">{{ $todaySales['count'] }}</h2>
                    <small>Transacciones</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Esperado</h5>
                    <h2 class="mb-0">${{ number_format($todaySales['total'], 2) }}</h2>
                    <small>Según sistema</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Métodos de Pago</h5>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="badge bg-success">💵 Efectivo: ${{ number_format($todaySales['cash'] ?? 0, 2) }}</span>
                        <span class="badge bg-primary">💳 Tarjeta: ${{ number_format($todaySales['card'] ?? 0, 2) }}</span>
                        <span class="badge bg-info">🏦 Transferencia: ${{ number_format($todaySales['transfer'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Cierre -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">💰 Conteo de Efectivo</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cashier.close') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="final_amount" class="form-label">
                                <strong>Monto Total en Caja (Efectivo)</strong>
                                <span class="text-muted">(Solo efectivo físico)</span>
                            </label>
                            <input type="number" 
                                   name="final_amount" 
                                   id="final_amount" 
                                   class="form-control form-control-lg" 
                                   value="{{ old('final_amount') }}" 
                                   step="0.01" 
                                   min="0" 
                                   required
                                   placeholder="0.00">
                            <small class="text-muted">Ingresa el monto total de billetes y monedas en la caja</small>
                        </div>

                        <div class="mb-3">
                            <label for="expected_amount" class="form-label">
                                <strong>Total Esperado (Sistema)</strong>
                            </label>
                            <input type="text" 
                                   id="expected_amount" 
                                   class="form-control" 
                                   value="${{ number_format($todaySales['total'], 2) }}" 
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label for="difference" class="form-label">
                                <strong>Diferencia</strong>
                            </label>
                            <input type="text" 
                                   id="difference" 
                                   class="form-control" 
                                   value="$0.00" 
                                   readonly>
                            <small id="difference-hint" class="text-muted">
                                La diferencia se calculará automáticamente
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">
                                <strong>Notas del Cierre</strong>
                            </label>
                            <textarea name="notes" 
                                      id="notes" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Observaciones, incidencias, etc.">{{ old('notes') }}</textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Importante:</strong> El monto ingresado debe coincidir con el efectivo físico en la caja.
                            Las ventas con tarjeta y transferencia no se incluyen en este conteo.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check"></i> Confirmar Cierre de Caja
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir Reporte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Detalle de Ventas -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📊 Detalle de Ventas del Día</h5>
                    <span class="badge bg-info">{{ $todaySales['count'] }} ventas</span>
                </div>
                <div class="card-body">
                    @if($todaySales['sales']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>N° Venta</th>
                                        <th>Hora</th>
                                        <th>Método</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todaySales['sales'] as $sale)
                                        <tr>
                                            <td><strong>{{ $sale->sale_number }}</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('H:i') }}</td>
                                            <td>
                                                @if($sale->payment_method == 'cash')
                                                    <span class="badge bg-success">💵 Efectivo</span>
                                                @elseif($sale->payment_method == 'card')
                                                    <span class="badge bg-primary">💳 Tarjeta</span>
                                                @else
                                                    <span class="badge bg-info">🏦 Transferencia</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($sale->total_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between">
                                <strong>Total Efectivo:</strong>
                                <span class="text-success">${{ number_format($todaySales['cash'] ?? 0, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Total Tarjeta:</strong>
                                <span class="text-primary">${{ number_format($todaySales['card'] ?? 0, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Total Transferencia:</strong>
                                <span class="text-info">${{ number_format($todaySales['transfer'] ?? 0, 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total General:</strong>
                                <span class="text-success fw-bold">${{ number_format($todaySales['total'], 2) }}</span>
                            </div>
                        </div>

                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay ventas registradas hoy</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Movimientos del Día -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📋 Movimientos de Caja del Día</h5>
                </div>
                <div class="card-body">
                    @if($todayMovements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Referencia</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayMovements as $movement)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($movement->created_at)->format('d/m H:i') }}</td>
                                            <td>
                                                @if($movement->type == 'income')
                                                    <span class="badge bg-success">Ingreso</span>
                                                @else
                                                    <span class="badge bg-danger">Egreso</span>
                                                @endif
                                            </td>
                                            <td>{{ $movement->description }}</td>
                                            <td>{{ $movement->reference ?? '-' }}</td>
                                            <td class="{{ $movement->type == 'income' ? 'text-success' : 'text-danger' }}">
                                                {{ $movement->type == 'income' ? '+' : '-' }}${{ number_format($movement->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark">
                                        <td colspan="4" class="text-end"><strong>Total Ingresos:</strong></td>
                                        <td class="text-success"><strong>${{ number_format($todayMovements->where('type', 'income')->sum('amount'), 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-dark">
                                        <td colspan="4" class="text-end"><strong>Total Egresos:</strong></td>
                                        <td class="text-danger"><strong>${{ number_format($todayMovements->where('type', 'expense')->sum('amount'), 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-dark">
                                        <td colspan="4" class="text-end"><strong>Balance:</strong></td>
                                        <td class="text-primary"><strong>${{ number_format($todayMovements->where('type', 'income')->sum('amount') - $todayMovements->where('type', 'expense')->sum('amount'), 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No hay movimientos registrados hoy</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmCloseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Cierre de Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas cerrar la caja?</p>
                <div class="alert alert-info">
                    <strong>Resumen del Cierre:</strong><br>
                    Ventas del día: {{ $todaySales['count'] }}<br>
                    Total Esperado: ${{ number_format($todaySales['total'], 2) }}<br>
                    Monto en Caja: <span id="modalFinalAmount">$0.00</span><br>
                    Diferencia: <span id="modalDifference">$0.00</span>
                </div>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="confirmCloseForm" method="POST" action="{{ route('cashier.close') }}">
                    @csrf
                    <input type="hidden" name="final_amount" id="modalFinalAmountInput">
                    <input type="hidden" name="notes" id="modalNotesInput">
                    <button type="submit" class="btn btn-success">Sí, Cerrar Caja</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Calcular diferencia automáticamente
    document.getElementById('final_amount').addEventListener('input', function() {
        const finalAmount = parseFloat(this.value) || 0;
        const expectedAmount = parseFloat(document.getElementById('expected_amount').value.replace('$', '')) || 0;
        const difference = finalAmount - expectedAmount;
        
        const differenceElement = document.getElementById('difference');
        const differenceHint = document.getElementById('difference-hint');
        
        differenceElement.value = '$' + difference.toFixed(2);
        
        if (difference === 0) {
            differenceElement.classList.remove('text-danger', 'text-success');
            differenceElement.classList.add('text-success');
            differenceHint.textContent = '¡Perfecto! El monto coincide.';
        } else if (difference > 0) {
            differenceElement.classList.remove('text-danger', 'text-success');
            differenceElement.classList.add('text-success');
            differenceHint.textContent = 'Exceso de efectivo: $' + difference.toFixed(2);
        } else {
            differenceElement.classList.remove('text-danger', 'text-success');
            differenceElement.classList.add('text-danger');
            differenceHint.textContent = 'Falta de efectivo: $' + Math.abs(difference).toFixed(2);
        }
    });

    // Abrir modal de confirmación
    document.querySelector('form').addEventListener('submit', function(e) {
        const finalAmount = document.getElementById('final_amount').value;
        const notes = document.getElementById('notes').value;
        
        document.getElementById('modalFinalAmount').textContent = '$' + parseFloat(finalAmount || 0).toFixed(2);
        document.getElementById('modalFinalAmountInput').value = finalAmount;
        document.getElementById('modalNotesInput').value = notes;
        
        // Calcular diferencia en modal
        const expectedAmount = parseFloat(document.getElementById('expected_amount').value.replace('$', '')) || 0;
        const difference = parseFloat(finalAmount) - expectedAmount;
        document.getElementById('modalDifference').textContent = '$' + difference.toFixed(2);
        
        const modal = new bootstrap.Modal(document.getElementById('confirmCloseModal'));
        modal.show();
        
        e.preventDefault();
    });

    // Imprimir reporte
    function window.print() {
        window.print();
    }
</script>
@endsection