<?php 
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AppController;
use Controllers\ProductosController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

// Ruta principal
$router->get('/', [AppController::class,'index']);

// Rutas de productos
$router->get('/productos', [ProductosController::class, 'renderizarPagina']);
$router->get('/productos/buscarAPI', [ProductosController::class, 'buscarProductosAPI']);
$router->post('/productos/guardarAPI', [ProductosController::class, 'guardarAPI']);
$router->post('/productos/modificarAPI', [ProductosController::class, 'modificarAPI']);
$router->get('/productos/eliminarAPI', [ProductosController::class, 'eliminarAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();