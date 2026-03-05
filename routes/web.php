<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {        
        return view('dashboard');
    })->name('dashboard');
});

Route::resource('users', UserController::class);

Route::get('/administration', [DashboardController::class, 'index'])->name('administration.index');

Route::resource('companies', CompanyController::class);
Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');

Route::resource('products', ProductController::class);
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/productos/{product}/presentaciones/create', [PresentationController::class, 'create'])->name('presentaciones.create');
Route::post('/productos/{product}/presentaciones', [PresentationController::class, 'store'])->name('presentaciones.store');

Route::resource('presentations', PresentationController::class);

Route::middleware(['auth'])->group(function () {
    Route::get('/products/{product}/presentations', [ProductController::class, 'getPresentations'])->name('products.presentations');
});

Route::resource('categories', CategoryController::class);
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');

Route::get('/cart/add', [CartController::class, 'addForm'])->name('cart.add.form');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/get', [CartController::class, 'getCart'])->name('cart.get');
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/delete/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
Route::get('/sales/{sale}/receipt', [SalesController::class, 'receipt'])->name('sales.receipt');
Route::get('/sales', [SalesController::class, 'index'])->name('sales.index'); // Opcional
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('brands', BrandController::class);
