// Función para formatear precios (Reutilizable)
export function formatPrice(value, locales = 'es-MX', currency = 'MXN') {
    return new Intl.NumberFormat(locales, {
        style: 'currency',
        currency: currency,
        minimumFractionDigits: 2
    }).format(value);
}

// Función para mostrar loading
export function showLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.remove('d-none');
    }
}

// Función para ocultar loading
export function hideLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.add('d-none');
    }
}

// Mostrar toast de notificación
export function mostrarToast(tipo, titulo, mensaje) {
    const toastEl = document.getElementById('liveToast');
    const toastTitulo = document.getElementById('toastTitulo');
    const toastMensaje = document.getElementById('toastMensaje');
    
    // Configurar colores según el tipo
    if (tipo === 'success') {
        toastEl.classList.remove('bg-danger');
        toastEl.classList.add('bg-success', 'text-white');
    } else if (tipo === 'error') {
        toastEl.classList.remove('bg-success');
        toastEl.classList.add('bg-danger', 'text-white');
    }
    
    // Configurar contenido
    toastTitulo.textContent = titulo;
    toastMensaje.textContent = mensaje;
    
    // Mostrar toast
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
}

// Función para mostrar Toast (Bootstrap)
export function showToast(message, type = 'success') {
    // Asegurarse de que el DOM esté cargado
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => createToast(message, type));
        return;
    }
    
    createToast(message, type);
}

// Función para mostrar Toast (Bootstrap)
export function createToast(message, type = 'success') {
    // Crear el elemento del toast
    const toastContainer = document.getElementById('toast-container');
    
    if (!toastContainer) {
        // Si no existe el contenedor, crearlo dinámicamente
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '11';
        document.body.appendChild(container);
    }

    const toastId = `toast-${Date.now()}`;
    
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    if (toastContainer) {
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    } else {
        console.error("Error: El contenedor del toast no existe en el HTML.");
    }
    
    // Inicializar el toast con Bootstrap
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();

    // Eliminar el elemento después de que se cierre
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}
