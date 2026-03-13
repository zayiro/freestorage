@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div>
            <h1><i class="fas fa-barcode"></i> Escáner Rápido</h1>
            <p>Escanea el código de barras del producto</p>
        </div>

        <div class="scanner-card scan-animation">
            <div class="scan-line"></div>
            
            <div class="barcode-input-container">
                <input 
                    type="text" 
                    id="barcode-input"
                    placeholder="📱 Escanea el código de barras aquí..."
                    autocomplete="off"
                    autofocus
                >
                <i class="fas fa-search input-icon"></i>
            </div>

            <div id="product-display" class="product-display"></div>
            <div id="no-product" class="no-product" style="display: none;">
                <i class="fas fa-search fa-3x mb-3" style="color: #dee2e6;"></i>
                <h4>Escanea un código de barras</h4>
                <p>La pistola enviará automáticamente el código al input</p>
            </div>
        </div>
    </div>

    <!-- Status Bar -->
    <div id="status-bar" class="status-bar status-ready">
        <i class="fas fa-circle mr-2"></i> Listo para escanear
    </div>
@endsection

@section('scripts')
<script>
    class BarcodeScanner {
        constructor() {
            this.input = document.getElementById('barcode-input');
            this.productDisplay = document.getElementById('product-display');
            this.noProduct = document.getElementById('no-product');
            this.statusBar = document.getElementById('status-bar');
            
            this.init();
        }

        init() {
            // Detectar escaneo de pistola (Enter automático)
            this.input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.scanBarcode();
                }
            });

            // Detectar escritura rápida (característica de pistolas)
            let lastInput = 0;
            this.input.addEventListener('input', (e) => {
                const now = Date.now();
                if (now - lastInput < 100) {
                    this.updateStatus('scanning');
                }
                lastInput = now;

                // Auto-submit si tiene más de 8 caracteres
                if (e.target.value.length >= 8) {
                    setTimeout(() => this.scanBarcode(), 300);
                }
            });

            // Focus automático
            this.input.focus();
        }

        async scanBarcode() {
            const barcode = this.input.value.trim();
            if (!barcode) return;

            this.updateStatus('scanning');
            this.showLoading();

            try {
                const response = await fetch(`/api/products/search-barcode?barcode=${encodeURIComponent(barcode)}`);
                const data = await response.json();

                if (data.success) {
                    this.showProduct(data.product);
                    this.updateStatus('success');
                } else {
                    this.showError(data.message);
                    this.updateStatus('error');
                }
            } catch (error) {
                this.showError('Error de conexión');
                this.updateStatus('error');
            }

            // Limpiar input
            setTimeout(() => {
                this.input.value = '';
                this.input.focus();
                this.updateStatus('ready');
            }, 2000);
        }

        showProduct(product) {
            const stockClass = product.stock > 10 ? 'stock-ok' : 
                                (product.stock > 0 ? 'stock-low' : 'stock-out');
            
            this.productDisplay.innerHTML = `
                <div class="product-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            ${product.image ? 
                                `<img src="/storage/${product.image}" class="product-image w-100" alt="${product.name}">` :
                                `<div class="product-image d-flex align-items-center justify-content-center">
                                    <i class="fas fa-box fa-3x text-muted"></i>
                                </div>`
                            }
                        </div>
                        <div class="col-md-8 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3>${product.name}</h3>
                                <span class="badge badge-primary px-3 py-2">${product.barcode}</span>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="price-tag">
                                        <i class="fas fa-tag mr-2"></i>
                                        S/ ${parseFloat(product.price).toFixed(2)}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <span class="stock-badge ${stockClass}">
                                        <i class="fas fa-warehouse mr-1"></i>
                                        ${product.stock > 10 ? 'Disponible' : 
                                            (product.stock > 0 ? 'Bajo stock' : 'Sin stock')} 
                                        (${product.stock})
                                    </span>
                                </div>
                            </div>
                            
                            ${product.description ? `<p class="text-muted">${product.description}</p>` : ''}
                            
                            <div class="d-flex gap-3">
                                <a href="#" class="btn btn-success">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Agregar
                                </a>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-eye mr-2"></i>
                                    Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            this.productDisplay.classList.add('show');
            this.noProduct.style.display = 'none';
        }

        showError(message) {
            this.productDisplay.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    ${message}
                </div>
            `;
            this.productDisplay.classList.add('show');
            this.noProduct.style.display = 'none';
        }

        showLoading() {
            this.productDisplay.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Buscando...</span>
                    </div>
                    <p class="mt-3">Buscando producto...</p>
                </div>
            `;
            this.productDisplay.classList.add('show');
            this.noProduct.style.display = 'none';
        }

        updateStatus(type) {
            const icons = {
                ready: 'fa-circle',
                scanning: 'fa-spinner fa-spin',
                success: 'fa-check-circle',
                error: 'fa-times-circle'
            };
            
            const texts = {
                ready: 'Listo para escanear',
                scanning: 'Escaneando...',
                success: '¡Producto encontrado!',
                error: 'Producto no encontrado'
            };

            this.statusBar.className = `status-bar status-${type}`;
            this.statusBar.innerHTML = `
                <i class="fas ${icons[type]} mr-2"></i>
                ${texts[type]}
            `;
        }
    }

    // Inicializar escáner
    document.addEventListener('DOMContentLoaded', () => {
        new BarcodeScanner();
    });
</script>
