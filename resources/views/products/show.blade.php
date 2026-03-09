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
        <p><strong>Barcode:</strong><br> {{ $product->barcode ?? 'Sin barcode' }}</p>
        
        <a href="{{ route('products.index') }}" class="btn btn-primary">Lista de productos</a>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Editar</a>
                               
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