<?php 
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AppController;
use Controllers\ProductosController;
use Controllers\ClientesController;
use Controllers\FacturaController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

// Ruta principal
$router->get('/', [AppController::class,'index']);

//productos
$router->get('/productos', [ProductosController::class, 'renderizarPagina']);
$router->get('/productos/buscarAPI', [ProductosController::class, 'buscarProductosAPI']);
$router->post('/productos/guardarAPI', [ProductosController::class, 'guardarAPI']);
$router->post('/productos/modificarAPI', [ProductosController::class, 'modificarAPI']);
$router->get('/productos/eliminarAPI', [ProductosController::class, 'eliminarAPI']);

//clientes
$router->get('/clientes', [ClientesController::class, 'renderizarPagina']);
$router->get('/clientes/buscarAPI', [ClientesController::class, 'buscarClientesAPI']);
$router->post('/clientes/guardarAPI', [ClientesController::class, 'guardarAPI']);
$router->post('/clientes/modificarAPI', [ClientesController::class, 'modificarAPI']);
$router->get('/clientes/eliminarAPI', [ClientesController::class, 'eliminarAPI']);

// Rutas del carrito
$router->get('/carrito', [FacturaController::class, 'renderizarPagina']);
$router->get('/carrito/modificar', [FacturaController::class, 'renderizarPaginaModificar']);
$router->get('/facturas', [FacturaController::class, 'renderizarPaginaFacturas']);
$router->get('/carrito/buscarFacturaPorIdAPI', [FacturaController::class, 'buscarFacturaPorIdAPI']);
$router->post('/carrito/modificarFacturaAPI', [FacturaController::class, 'modificarFacturaAPI']);
$router->get('/carrito/buscarClientesAPI', [FacturaController::class, 'buscarClientesAPI']);
$router->get('/carrito/buscarProductosDisponiblesAPI', [FacturaController::class, 'buscarProductosDisponiblesAPI']);
$router->post('/carrito/guardarFacturaAPI', [FacturaController::class, 'guardarFacturaAPI']);
$router->get('/carrito/buscarFacturasAPI', [FacturaController::class, 'buscarFacturasAPI']);




// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();