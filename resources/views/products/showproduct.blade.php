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
        <p>
            @if ($product->barcode)
                <div><img src="{{ asset('storage/' . $product->barcode_image) }}" alt="{{ $product->barcode }}" class="img-fluid img-thumbnail" style="width: 300px;"></div>
                <div class="small">{{ $product->barcode }}</div>
            @else
                <div>Sin barcode</div>
            @endif
        </p>

        <div class="d-flex justify-content-between align-items-center gap-2"> 
            @if($product->presentations->isNotEmpty())                                           
                <button type="button" class="btn btn-success btn-lg w-100 shadow presentationsModal"
                    data-bs-toggle="modal" 
                    data-bs-target="#presentationsModal"
                    data-id="{{ $product->id }}"
                >
                    <i class="fa-solid fa-cart-shopping"></i> presentaciones
                </button>
            @else
                <button type="button" class="btn btn-secondary btn-lg w-100 shadow" disabled>
                    Sin presentación
                </button>
            @endif
            <div>
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg shadow">Tienda</a> 
            </div>
        </div>                                       
    </div>

    {{-- Modal único para todas las presentaciones --}}
    <div class="modal fade" id="presentationsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="presentationsProductName">Product name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addToCartForm">
                    <input type="hidden" name="product_id" id="presentationProductId">
                    <div class="modal-body">
                        {{-- Loader --}}
                        <div id="presentationsLoader" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando presentaciones...</p>
                        </div>
                        
                        {{-- Contenido --}}
                        <div id="presentationsContent" style="display: none;">
                            <h6>Presentaciones disponibles:</h6>
                            <ul class="list-group" id="presentationsList">
                                {{-- Se llena con AJAX --}}
                            </ul>
                        </div>
                        
                        {{-- Error --}}
                        <div id="presentationsError" class="alert alert-danger" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Agregar al Carrito</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toast de notificación -->
        <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="liveToast" class="toast hide" role="alert">
                <div class="toast-header">
                    <strong class="me-auto" id="toastTitulo">Notificación</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body" id="toastMensaje"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>    
    // Función para cargar presentaciones
    async function loadPresentations(productId) {
        // Resetear modal
        resetPresentationsModal();
        showPresentationsLoader();

        const producto = @json($product);
        
        if (producto) {
            fillPresentationsModal(producto);
        } else {
            showPresentationsError('Producto no encontrado');
        }
    }
    
    function resetPresentationsModal() {
        document.getElementById('presentationsProductName').textContent = '';
        document.getElementById('presentationsList').innerHTML = '';
        document.getElementById('presentationsError').style.display = 'none';
    }
    
    function showPresentationsLoader() {
        const loader = document.getElementById('presentationsLoader');
        const content = document.getElementById('presentationsContent');
        
        if (loader) loader.style.display = 'block';
        if (content) content.style.display = 'none';
    }
    
    function fillPresentationsModal(data) {
        const loader = document.getElementById('presentationsLoader');
        const content = document.getElementById('presentationsContent');
        
        if (loader) loader.style.display = 'none';
        if (content) content.style.display = 'block';
        
        // Datos del producto
        const nameEl = document.getElementById('presentationsProductName');
        const priceEl = document.getElementById('presentationsProductPrice');
        const imgEl = document.getElementById('presentationsProductImage');
        const listEl = document.getElementById('presentationsList');
        
        if (nameEl) nameEl.textContent = data.name;

        document.getElementById('presentationProductId').value = data.id;
                
        // Lista de presentaciones
        if (listEl) {
            listEl.innerHTML = '';
            
            if (data.presentations.length === 0) {
                listEl.innerHTML = '<li class="list-group-item text-muted">No hay presentaciones disponibles.</li>';
                return;
            }
            
            data.presentations.forEach(presentation => {
                const li = document.createElement('li');
                li.className = 'd-flex';
                
                if (presentation.stock > 0) {
                    li.innerHTML = `
                        <label class="list-group-item list-group-item-action cursor-pointer">
                            <div><strong class="ms-2">${presentation.presentation}</strong></div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    
                                    <input class="form-check-input presentation-radio" 
                                        type="radio" 
                                        name="presentation_id" 
                                        id="presentation_${presentation.id}" 
                                        value="${presentation.id}"
                                        data-max="${presentation.stock}"
                                        required>
                                    <span class="badge bg-success rounded-pill">${presentation.stock} disponibles</span>
                                </div>
                                <div>
                                    <label for="quantity_${presentation.id}"><strong class="ms-2">Cantidad</strong></label>
                                    <input type="number" 
                                            name="quantity" 
                                            id="quantity_${presentation.id}" 
                                            class="form-control quantity-input" 
                                            value="1" 
                                            min="1" 
                                            max="${presentation.stock}"
                                            data-presentation-id="${presentation.id}">
                                </div>
                                <div>
                                    <div><strong class="ms-2">Precio</strong></div>
                                    <span class="text-muted">${window.formatPrice(presentation.sales_price, 'es-CO', 'COP')}</span>
                                </div>
                            </div>                            
                        </label>
                    `;
                } else {
                    li.innerHTML = `
                        ${presentation.presentation_name}
                        <span class="badge bg-danger rounded-pill">Agotado</span>
                    `;
                }
                
                listEl.appendChild(li);
            });
        }
    }
    
    function showPresentationsError(message) {
        const loader = document.getElementById('presentationsLoader');
        const errorDiv = document.getElementById('presentationsError');
        
        if (loader) loader.style.display = 'none';
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }
    
    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {        
        // Usar event delegation para los botones
        document.addEventListener('click', function(e) {
            // Encontrar si el clic fue en un botón de modal
            const button = e.target.closest('[data-bs-toggle="modal"][data-bs-target="#presentationsModal"]');
            
            if (button) {
                const productId = button.getAttribute('data-id');
                
                if (productId) {
                    // Usar setTimeout para asegurar que el modal esté listo
                    setTimeout(() => {
                        loadPresentations(productId);
                    }, 100);
                }
            }
        });
        
        // También escuchar el evento shown.bs.modal como backup
        const modal = document.getElementById('presentationsModal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function() {
            });
            
            modal.addEventListener('hidden.bs.modal', function() {
                resetPresentationsModal();
            });
        }
    
        const form = document.getElementById('addToCartForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Obtener el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Crear FormData
            const formData = new FormData(form);
            
            // Enviar con fetch
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {                
                if (data.success) {
                    // Actualizar info icon menu carrito
                    document.getElementById('cart_count_menu').textContent = data.cart_count;
                    // Actualizar info icon menu total del carrito
                    document.getElementById('cart_menu').title = data.cart_total;

                    // Cerrar modal
                    document.getElementById('presentationsModal').style.display = 'none';
                    document.body.style.overflow = 'auto';
                    document.querySelector('.modal-backdrop')?.remove();    
                    
                    // Mostrar toast de éxito
                    window.showToast(data.message, 'success');
                }
            })
            .catch(error => {
                window.showToast('Error: ', error, 'error');
            });
        });
    });
</script>