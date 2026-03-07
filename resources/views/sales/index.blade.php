@extends('layouts.app') {{-- Ajusta a tu layout --}}

@section('content')
<div class="container mt-5">
    <h1>Historial de Ventas</h1>
    <table class="table">
        <thead>
            <tr><th>ID</th><th>Vendedor</th><th>Invoice number</th><th>Fecha</th><th>Cliente</th><th>Total</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>
                        @if($sale->user)
                            {{ $sale->user->name }}
                        @else
                            <span class="badge bg-danger">Usuario eliminado</span>
                        @endif
                    </td>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->company ? $sale->customer_name : 'Individual' }}</td>
                    <td>${{ number_format($sale->total_price, 2) }}</td>
                    <td><a href="{{ route('sales.receipt', $sale) }}" class="btn btn-sm btn-info">Ver Recibo</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $sales->links() }}
</div>
@endsection
