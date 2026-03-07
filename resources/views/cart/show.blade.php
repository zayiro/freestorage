@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Carrito de Compras</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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
    
    @if(count($cart) > 0)    
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-end">Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $id => $item)                    
                    <tr id="item_{{ $id }}">
                        <td class="align-baseline">
                            <div>{{ $item['product_name'] }}</div>
                            <div><strong>{{ $item['presentation'] }}</strong></div>
                        </td>
                        <td class="align-baseline">$ {{ number_format($item['sales_price'], 2) }}</td>
                        <td class="align-baseline text-center">
                            <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center">
                                @csrf
                                <input type="hidden" name="presentation_id" value="{{ $id }}">
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" class="form-control" style="width: 60px;margin-right: 3px;">
                                <button type="submit" class="btn btn-sm btn-success shadow"><i class="fas fa-edit"></i></button>
                            </form>
                        </td>
                        <td class="align-baseline text-end">$ {{ number_format($item['sales_price'] * $item['quantity'], 2) }}</td>
                        <td class="align-baseline">
                            <button type="submit" class="btn btn-danger btn-sm btn-eliminar shadow" data-id="{{ $id }}" data-name="{{ $item["product_name"] }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Subtotal:</th>
                    <th id="cart_subtotal" class="text-end" data-subtotal="{{ $total }}">$ {{ number_format($total, 2) }}</th>
                    <th id="items_discount" class="text-success"></th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Descuento (%):</th>
                    <th class="text-end">
                        <div class="d-flex justify-content-end">
                            <input type="text" id="discount" class="form-control text-end" value="0" maxlength="2" style="width: 80px;">
                        </div>
                    </th>
                    <th><button type="submit" class="btn btn-success btn-sm btn-discount shadow">Aplicar</button></th>
                </tr>
                @php
                    $taxPercent = 19; // Impuestos fijos del 19%                    
                    // Calcular IMPUESTOS (19%) sobre (Subtotal - Descuento)
                    $taxAmount = $total * ($taxPercent / 100);                             
                @endphp
                <tr>
                    <th colspan="3" class="text-right">Impuestos (19%):</th>
                    <th id="tax_amount" class="text-end">$ {{ number_format($taxAmount, 2) }}</th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Domicilio:</th>
                    <th class="text-end">
                        <div class="d-flex justify-content-end">
                            <input type="text" id="delivery_fee" class="form-control text-end" value="0" maxlength="6" style="width: 80px;">
                        </div>
                    </th>
                    <th><button type="submit" class="btn btn-success btn-sm btn-delivery shadow">Aplicar</button></th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <th id="cart_total" class="text-end text-success" data-total="{{ $total }}">$ {{ number_format($total, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>        

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Información de Pago</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sales.checkout') }}" method="POST">
                            @csrf                            
                            <div class="mb-3">
                                <label class="form-label">Método de Pago</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="cash">Efectivo</option>
                                    <option value="card">Tarjeta</option>
                                    <option value="transfer">Transferencia</option>
                                    <option value="nequi">NEQUI</option>
                                    <option value="other">Otro</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" name="customer_name" class="form-control" value="Cliente final">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cliente Id</label>
                                    <input type="text" name="customer_id" class="form-control" value="1111111111">
                                </div>                                
                                <div class="col-md-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="customer_phone" class="form-control" value="1111111111">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="customer_address" class="form-control" value="No aplica">
                                </div>
                            </div>                 
                            <div class="mt-3 mb-3">
                                <label class="form-label">Notas</label>
                                <textarea name="notes" class="form-control" rows="2" style="resize: none;"></textarea>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-success btn-lg shadow">
                                    <i class="fas fa-check-circle"></i> Realizar Venta
                                </button>
                            </div>
                            <input type="hidden" name="discount" id="hidden_discount" value="0">
                            <input type="hidden" name="delivery_fee" id="hidden_delivery_fee" value="0">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmación para Eliminar -->
        <div class="modal" id="confirmarEliminarModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Confirmar Eliminación
                    </h5>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de eliminar el item del carrito?</p>
                    
                    <div id="infoProducto" class="alert alert-info" style="display: none;">
                        <span id="nombreProducto"></span>
                    </div>

                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                        Eliminar
                    </button>
                </div>
                </div>
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
    @else
        <p class="alert alert-info">El carrito está vacío.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Agregar Productos</a>
    @endif
</div>
@endsection

@section('scripts')
<script>
    let itemAEliminar = null;

    function getDiscount() {
        let discountInput = document.getElementById('discount');
        let discountPercentage = parseFloat(discountInput.value) || 0;
        const subtotal = document.getElementById('cart_subtotal').getAttribute('data-subtotal');

        let discountAmount = subtotal * (discountPercentage / 100);//valor descuento en dinero
        let taxableAmount = parseFloat(subtotal) - discountAmount;

        return {
            discountAmount: discountAmount.toFixed(2),
            taxableAmount: taxableAmount.toFixed(2)
        };
    }

    document.addEventListener('click', function(e) {
        // Evento: Click en botón discount
        if (e.target.classList.contains('btn-discount')) {
            const discount = getDiscount();

            let deliveryFeeInput = document.getElementById('delivery_fee').value || 0;
            let deliveryFee = parseFloat(deliveryFeeInput) || 0;
                        
            if (discount.discountAmount > 0) {
                items_discount.textContent = `${window.formatPrice(discount.taxableAmount)}`;                
            } else {
                items_discount.textContent = "";
            }

            //tax 19% sobre el monto después del descuento
            let taxAmount = discount.taxableAmount * (19 / 100);
            document.getElementById('tax_amount').textContent = `${window.formatPrice(taxAmount)}`;

            let finalTotal = parseFloat(discount.taxableAmount) + parseFloat(deliveryFee);
            
            cart_total.setAttribute('data-total', finalTotal);
            cart_total.textContent = `${window.formatPrice(finalTotal)}`;

            document.getElementById('hidden_discount').value = document.getElementById('discount').value;
        }

        // Evento: Click en botón delivery
        if (e.target.classList.contains('btn-delivery')) {
            let deliveryFeeInput = document.getElementById('delivery_fee').value || 0;
            let deliveryFee = parseFloat(deliveryFeeInput) || 0;
                        
            const discount = getDiscount();

            let finalTotal = parseFloat(discount.taxableAmount) + parseFloat(deliveryFee);

            cart_total.setAttribute('data-total', finalTotal);
            cart_total.textContent = `${window.formatPrice(finalTotal)}`;

            document.getElementById('hidden_delivery_fee').value = deliveryFeeInput;
        }
        
        // Evento: Click en botón eliminar
        if (e.target.classList.contains('btn-eliminar')) {
            const boton = e.target;
            const fila = boton.closest('tr');
                        
            // Obtener datos del data attribute
            const presentationId = boton.getAttribute('data-id');
            const nombreProducto = boton.getAttribute('data-name');

            itemAEliminar = {
                presentationId: presentationId,
            };

            // Mostrar información del producto en el modal
            document.getElementById('nombreProducto').textContent = nombreProducto;
            document.getElementById('infoProducto').style.display = 'block';

            // Abrir el modal
            const modal = new bootstrap.Modal(document.getElementById('confirmarEliminarModal'));
            modal.show();
        }
    });

    document.getElementById('btnConfirmarEliminar').addEventListener('click', async function() {        
        if (!itemAEliminar) return;

        const btn = this;        
        
        try {
            // Deshabilitar botón y mostrar estado de carga
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const presentationId = itemAEliminar.presentationId;
            
            if (!presentationId) {
                throw new Error('ID de presentación no encontrado');
            }

            // Enviar petición AJAX
            const response = await fetch(`/cart/delete/${presentationId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            // Convertir respuesta a JSON
            const result = await response.json();

            // Cerrar modal de confirmación
            const modalConfirmacion = bootstrap.Modal.getInstance(document.getElementById('confirmarEliminarModal'));
            modalConfirmacion.hide();

            if (result.success) {
                // Eliminar fila de la tabla                               
                const fila = document.getElementById("item_" + presentationId);

                // Verificamos que existe para evitar errores
                if (fila) {
                    fila.remove();
                }

                // Actualizar info icon menu carrito
                document.getElementById('cart_count_menu').textContent = result.cart_count;
                // Actualizar info icon menu total del carrito
                document.getElementById('cart_menu').title = result.cart_total;
                                
                // Actualizar total del carrito
                document.getElementById('cart_subtotal').textContent = result.cart_total;
                document.getElementById('cart_total').textContent = result.cart_total;
                
                // Mostrar toast de éxito
                window.showToast(result.message, 'success');
            } else {
                // Mostrar toast de error
                window.showToast(result.message, 'error');
            }

        } catch (error) {
            window.showToast('Error de conexión', 'error');
        } finally {
            // Restaurar botón
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash"></i> Sí, Eliminar';
            itemAEliminar = null;
        }
    });
</script>
@endsection