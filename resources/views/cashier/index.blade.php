@extends('layouts.app')

@section('content')
    <div class="container">

        <form action="{{ route('cashier.process') }}" method="POST">
            @csrf
            <!-- Formulario de venta -->
        </form>
        <div class="list-group mt-3">
            <a class="list-group-item list-group-item-action" href="{{ route('cashier.history') }}">Historial de Ventas</a>
            <a class="list-group-item list-group-item-action" href="{{ route('cashier.dashboard') }}">Dashboard</a>
            <a class="list-group-item list-group-item-action" href="{{ route('cashier.close') }}">Cerrar Caja</a>
        </div>
    </div>
@endsection    