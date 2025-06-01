<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Cliente;
use MVC\Router;

class ClientesController extends ActiveRecord
{
    public function renderizarPagina(Router $router)
    {
        $router->render('clientes/index', []);
    }

    public static function buscarClientesAPI()
    {
        getHeadersApi();
        try {
            $sql = "SELECT * FROM clientesCC WHERE situacion = 1 ORDER BY nombre, apellido";
            $clientes = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes encontrados',
                'data' => $clientes
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los clientes',
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
                'mensaje' => 'El nombre del cliente es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['apellido'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El apellido del cliente es obligatorio'
            ]);
            return;
        }

        try {
            $_POST['nombre'] = htmlspecialchars($_POST['nombre']);
            $_POST['apellido'] = htmlspecialchars($_POST['apellido']);
            $_POST['email'] = htmlspecialchars($_POST['email'] ?? '');
            $_POST['telefono'] = htmlspecialchars($_POST['telefono'] ?? '');
            $_POST['direccion'] = htmlspecialchars($_POST['direccion'] ?? '');
            $_POST['nit'] = htmlspecialchars($_POST['nit'] ?? '');

            $cliente = new Cliente([
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'],
                'direccion' => $_POST['direccion'],
                'nit' => $_POST['nit'],
                'fecha_registro' => date('Y-m-d H:i:s'),
                'situacion' => 1
            ]);

            $resultado = $cliente->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Cliente guardado exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        $id = $_POST['id'];

        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Cliente no encontrado'
                ]);
                return;
            }

            $_POST['nombre'] = htmlspecialchars($_POST['nombre']);
            $_POST['apellido'] = htmlspecialchars($_POST['apellido']);
            $_POST['email'] = htmlspecialchars($_POST['email'] ?? '');
            $_POST['telefono'] = htmlspecialchars($_POST['telefono'] ?? '');
            $_POST['direccion'] = htmlspecialchars($_POST['direccion'] ?? '');
            $_POST['nit'] = htmlspecialchars($_POST['nit'] ?? '');

            $cliente->sincronizar([
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'],
                'direccion' => $_POST['direccion'],
                'nit' => $_POST['nit']
            ]);

            $cliente->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Cliente modificado exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        getHeadersApi();
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $sql = "SELECT COUNT(*) as facturas FROM facturasCC WHERE id_cliente = $id AND situacion = 1";
            $resultado = self::fetchFirst($sql);

            if ($resultado['facturas'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el cliente porque tiene facturas asociadas'
                ]);
                return;
            }

            $sqlUpdate = "UPDATE clientesCC SET situacion = 0 WHERE id = $id";
            self::SQL($sqlUpdate);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Cliente eliminado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
?>