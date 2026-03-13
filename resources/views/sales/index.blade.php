@extends('layouts.app') {{-- Ajusta a tu layout --}}

@section('content')
<div class="container mt-5">
    <h1>Historial de Ventas</h1>
    @if(strlen(($sales)))
        {{-- Mostrar errores de validación --}}
        @if($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="my-3">
            <form action="{{ route('sales.find') }}" method="GET" class="mb-4">
                <div><strong>Buscar factura</strong></div>
                <div class="input-group">                
                    <input type="text" name="invoice_number" class="form-control" placeholder="Número de factura" value="{{ request('invoice_number') }}">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>

        <table class="table">
            <thead>
                <tr><th>ID</th><th>Vendedor</th><th>Factura</th><th>Fecha</th><th>Cliente</th><th>Total</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @php
                $totalFinal = 0;
                @endphp
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
                        <td>$ {{ number_format($sale->total_price, 2) }}</td>
                        <td><a href="{{ route('sales.receipt', $sale) }}" class="btn btn-sm btn-info">Ver Recibo</a></td>
                    </tr>                
                @endforeach
            </tbody>
        </table>
        {{ $sales->links() }}
    @else
       <div>No tiene ventas registradas en el momento.</div> 
    @endif
</div>
@endsection
