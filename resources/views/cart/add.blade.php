@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Agregar al Carrito</h1>
    
    <!-- Mensajes de respuesta -->
    <div id="message" class="alert" style="display: none;"></div>

    <form id="addToCartForm">
        @csrf
        
        <!-- Seleccionar Presentación -->
        <div class="form-group">
            <label for="presentation_id">Producto / Presentación</label>
            <select name="presentation_id" id="presentation_id" class="form-control" required>
                <option value="">Selecciona un producto</option>
                @foreach($presentations as $presentation)
                    <option value="{{ $presentation->id }}" 
                            data-precio="{{ $presentation->sales_price }}" 
                            data-stock="{{ $presentation->stock ?? 0 }}">
                        {{ $presentation->product->name }} - {{ $presentation->presentation }} 
                        ({{ $presentation->unit }}) - Stock: {{ $presentation->stock ?? 0 }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Cantidad -->
        <div class="form-group">
            <label for="quantity">Cantidad</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" required>
            <small class="text-muted">Stock disponible: <span id="stock-display">-</span></small>
        </div>

        <!-- Precio unitario -->
        <div class="form-group">
            <label for="precio">Precio Unitario</label>
            <input type="text" id="precio" class="form-control" readonly>
        </div>

        <!-- Subtotal -->
        <div class="form-group">
            <label for="subtotal">Subtotal</label>
            <input type="text" id="subtotal" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn">Agregar al Carrito</button>
        <a href="{{ route('cart.show') }}" class="btn btn-secondary">Ver Carrito (<span id="cart-count">0</span>)</a>
    </form>

    <!-- Carrito flotante o sección -->
    <div class="mt-4">
        <h3>Carrito Actual</h3>
        <div id="cart-preview">
            <p class="text-muted">No hay productos en el carrito.</p>
        </div>
    </div>
</div>

<script>
    // Actualizar precio y stock al cambiar selección
    document.getElementById('presentation_id').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const precio = option.dataset.precio || 0;
        const stock = option.dataset.stock || 0;
        
        document.getElementById('precio').value = '$' + parseFloat(precio).toFixed(2);
        document.getElementById('stock-display').textContent = stock;
        
        const cantidad = document.getElementById('quantity').value;
        document.getElementById('subtotal').value = '$' + (precio * cantidad).toFixed(2);
    });

    // Actualizar subtotal al cambiar cantidad
    document.getElementById('quantity').addEventListener('input', function() {
        const option = document.getElementById('presentation_id').options[document.getElementById('presentation_id').selectedIndex];
        const precio = option.dataset.precio || 0;
        document.getElementById('subtotal').value = '$' + (precio * this.value).toFixed(2);
    });

    // Enviar formulario via AJAX
    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
       
        const submitBtn = document.getElementById('submitBtn');
        const messageDiv = document.getElementById('message');
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Agregando...';
        messageDiv.style.display = 'none';

        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            messageDiv.style.display = 'block';
            
            if (data.success) {
                messageDiv.className = 'alert alert-success';
                messageDiv.textContent = data.message;
                
                // Actualizar contador del carrito
                document.getElementById('cart-count').textContent = data.cart_count;
                
                // Actualizar previsualización del carrito
                updateCartPreview();
                
                // Resetear formulario
                document.getElementById('quantity').value = 1;
                document.getElementById('precio').value = '';
                document.getElementById('subtotal').value = '';
                document.getElementById('stock-display').textContent = '-';
                document.getElementById('presentation_id').value = '';
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = data.message;
            }
        })
        .catch(error => {
            messageDiv.style.display = 'block';
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error al agregar al carrito.';
            console.error('Error:', error);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Agregar al Carrito';
        });
    });

    // Actualizar previsualización del carrito
    function updateCartPreview() {
        fetch('{{ route('cart.get') }}')
        .then(response => response.json())
        .then(data => {
            const cartPreview = document.getElementById('cart-preview');
            
            if (data.cart_count > 0) {
                let html = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Cant.</th><th>Subtotal</th></tr></thead><tbody>';
                
                for (const [id, item] of Object.entries(data.cart)) {
                    html += `<tr>
                        <td>${item.presentation} - ${item.presentation}</td>
                        <td>${item.quantity}</td>
                        <td>$${(item.purchase_price * item.quantity).toFixed(2)}</td>
                    </tr>`;
                }
                
                html += `<tr><th colspan="2">Total:</th><th>$${data.total.toFixed(2)}</th></tr></tbody></table>`;
                cartPreview.innerHTML = html;
            } else {
                cartPreview.innerHTML = '<p class="text-muted">No hay productos en el carrito.</p>';
            }
        });
    }

    // Cargar carrito al inicio
    document.addEventListener('DOMContentLoaded', function() {
        updateCartPreview();
    });
</script>
@endsection