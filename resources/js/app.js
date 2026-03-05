import './bootstrap';


// Importar tus helpers
import { formatPrice, showLoading, hideLoading, showToast } from './helpers/general.js';

// Exponerlos globalmente
window.formatPrice = formatPrice;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.showToast = showToast;