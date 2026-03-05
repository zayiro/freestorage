@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid mb-4" style="max-height: 300px;">
        @endif
        <h1>{{ $product->name }}</h1>
        <p><strong>Descripción:</strong><br> {{ $product->description ?? 'N/A' }}</p>
        <p><strong>Categoría:</strong><br> {{ $product->category->name ?? 'Sin categoría' }}</p>
        <p><strong>Marca:</strong><br> {{ $product->brand->name ?? 'Sin marca' }}</p>
        <h3>Presentaciones Disponibles</h3>
        @if($product->presentations->isNotEmpty())
            <div class="row">
                @foreach($product->presentations as $presentation)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $presentation->presentation }}</h5>
                                <p class="card-text">
                                    <strong>Unidad:</strong><br> {{ $presentation->unit ?? 'N/A' }}<br>
                                    <strong>Precio:</strong><br> ${{ number_format($presentation->purchase_price, 2) }}<br>
                                    <strong>Stock:</strong><br> {{ $presentation->stock }} unidades
                                </p>
                                @if($presentation->stock > 0)
                                    <form class="add-to-cart-form" data-presentation-id="{{ $presentation->id }}">
                                        <input type="hidden" name="presentation_id" value="{{ $presentation->id }}">
                                        <input type="number" name="quantity" value="1" min="1" max="{{ $presentation->stock }}" class="form-control mb-2" required>
                                        <button type="submit" class="btn btn-primary">Agregar al Carrito</button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary" disabled>Agotado</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No hay presentaciones disponibles.</p>
        @endif

        <a href="{{ route('products.index') }}" class="btn btn-primary">Lista de productos</a>
                               
        <!-- Mensajes de Alerta -->
        <div id="alert-container" class="mt-3"></div>
    </div>

    <script>
        // Función para mostrar alertas
        function showAlert(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            document.getElementById('alert-container').appendChild(alertDiv);
            setTimeout(() => alertDiv.remove(), 5000); // Auto-remover después de 5s
        }

        // Manejar envío de formularios con AJAX
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
               
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content')); // CSRF
                
                fetch('/cart/add', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message);
                        // Actualizar contador y total
                        document.getElementById('cart-count').textContent = `(${data.cart_count} items)`;
                        document.getElementById('cart-total').textContent = data.total;
                    } else if (data.error) {
                        showAlert(data.error, 'danger');
                        return false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al agregar al carrito', 'danger');
                });
            });
        });
    </script>
@endsection