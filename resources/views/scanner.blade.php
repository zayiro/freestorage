@extends('layouts.app')

@section('content')    
    <style>        
        

        .scanner-header {
            text-align: center;
            
            margin-bottom: 40px;
        }

        .scanner-header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .scanner-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .scanner-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.4s ease;
        }

        .barcode-input-container {
            position: relative;
            margin-bottom: 30px;
        }

        #barcode-input {
            width: 100%;
            padding: 20px 70px 20px 20px;
            font-size: 1.4rem;
            border: 3px solid #e2e8f0;
            border-radius: 16px;
            outline: none;
            transition: all 0.3s ease;
            background: white;
            font-family: monospace;
            letter-spacing: 2px;
        }

        #barcode-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: scale(1.02);
        }

        .input-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            color: var(--primary);
        }

        .status-bar {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 50px;
            font-weight: 600;
            color: white;
            backdrop-filter: blur(10px);
            z-index: 1;
            min-width: 200px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .status-ready { background: rgba(40, 167, 69, 0.95); }
        .status-scanning { background: rgba(102, 126, 234, 0.95); }
        .status-success { 
            background: rgba(40, 167, 69, 0.95); 
            animation: pulse 1.5s infinite;
        }
        .status-error { background: rgba(220, 53, 69, 0.95); }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .scan-line {
            position: absolute;
            top: -100%;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            animation: scan 3s infinite linear;
            z-index: 10;
        }

        @keyframes scan {
            0% { top: -100%; }
            100% { top: 100%; }
        }

        .product-display {
            margin-top: 30px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s ease;
        }

        .product-display.show {
            opacity: 1;
            transform: translateY(0);
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        }

        .product-info h3 {
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .price-tag {
            background: linear-gradient(45deg, var(--success), #20c997);
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            font-size: 1.5rem;
            font-weight: 700;
            display: inline-block;
        }

        .stock-badge {
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .stock-ok { background: #d4edda; color: #155724; }
        .stock-low { background: #fff3cd; color: #856404; }
        .stock-out { background: #f8d7da; color: #721c24; }       

        .no-product {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .scan-animation {
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(102, 126, 234, 0.3); }
            to { box-shadow: 0 0 40px rgba(102, 126, 234, 0.6); }
        }

        @media (max-width: 768px) {
            .scanner-header h1 { font-size: 2rem; }
            .scanner-card { padding: 20px; margin: 10px; }
            #barcode-input { font-size: 1.2rem; padding: 15px; }
        }
    </style>
    <div class="container mt-5">
        <div class="scanner-container">
            <div class="scanner-header">
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