<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Producto;
use MVC\Router;

class ProductosController extends ActiveRecord
{
    public function renderizarPagina(Router $router)
    {
        $router->render('productos/index', []);
    }

    public static function buscarProductosAPI()
    {
        getHeadersApi();
        try {
            $sql = "SELECT * FROM productosCC WHERE situacion = 1 ORDER BY nombre";
            $productos = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Productos encontrados',
                'data' => $productos
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los productos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        if (empty($_POST['nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del producto es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['precio']) || $_POST['precio'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a 0'
            ]);
            return;
        }

        if (!isset($_POST['stock_disponible']) || $_POST['stock_disponible'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El stock no puede ser negativo'
            ]);
            return;
        }

        try {
            $_POST['nombre'] = htmlspecialchars($_POST['nombre']);
            $_POST['descripcion'] = htmlspecialchars($_POST['descripcion'] ?? '');
            $_POST['precio'] = filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT);
            $_POST['stock_disponible'] = filter_var($_POST['stock_disponible'], FILTER_VALIDATE_INT);
            $_POST['stock_minimo'] = filter_var($_POST['stock_minimo'], FILTER_VALIDATE_INT) ?: 5;

            $producto = new Producto([
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'stock_disponible' => $_POST['stock_disponible'],
                'stock_minimo' => $_POST['stock_minimo'],
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'fecha_actualizacion' => date('Y-m-d H:i:s'),
                'situacion' => 1
            ]);

            $resultado = $producto->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Producto guardado exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        $id = $_POST['id'];

        try {
            $producto = Producto::find($id);

            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            $_POST['nombre'] = htmlspecialchars($_POST['nombre']);
            $_POST['descripcion'] = htmlspecialchars($_POST['descripcion'] ?? '');
            $_POST['precio'] = filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT);
            $_POST['stock_disponible'] = filter_var($_POST['stock_disponible'], FILTER_VALIDATE_INT);
            $_POST['stock_minimo'] = filter_var($_POST['stock_minimo'], FILTER_VALIDATE_INT) ?: 5;

            $producto->sincronizar([
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'stock_disponible' => $_POST['stock_disponible'],
                'stock_minimo' => $_POST['stock_minimo'],
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

            $producto->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Producto modificado exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        getHeadersApi();
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $sql = "SELECT stock_disponible FROM productosCC WHERE id = $id AND situacion = 1";
            $resultado = self::fetchFirst($sql);

            if (!$resultado) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            if ($resultado['stock_disponible'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el producto porque tiene stock disponible'
                ]);
                return;
            }

            $sqlUpdate = "UPDATE productosCC SET situacion = 0 WHERE id = $id";
            self::SQL($sqlUpdate);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Producto eliminado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
