<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Cliente;
use Model\Producto;
use Model\Factura;
use Model\DetalleFactura;
use MVC\Router;

class FacturaController extends ActiveRecord
{
    public function renderizarPagina(Router $router)
    {
        $router->render('carrito/index', []);
    }

    public static function buscarClientesAPI()
    {
        getHeadersApi();
        try {
            $sql = "SELECT id, nombre, apellido, email, telefono, direccion, nit FROM clientesCC where situacion = 1 ORDER BY nombre, apellido";
            $clientes = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes Encontrados',
                'data' => $clientes
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar clientes',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarProductosDisponiblesAPI()
    {
        getHeadersApi();

        try {
            $sql = "SELECT id, nombre, descripcion, precio, stock_disponible FROM productosCC WHERE situacion = 1 AND stock_disponible > 0 ORDER BY nombre";
            $productos = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Se encontraron los productos disponibles',
                'data' => $productos
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'error al encontrar los productos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarFacturaAPI()
    {
        getHeadersApi();
        if (empty($_POST['id_cliente'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Seleccione un cliente']);
            return;
        }

        if (empty($_POST['productos'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Seleccione productos']);
            return;
        }

        try {
            $numeroFactura = 'FACT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $subtotal = 0;
            $productos = $_POST['productos'];

            foreach ($productos as $producto) {
                $cantidad = $producto['cantidad'];
                $precio = $producto['precio'];
                $subtotal = $subtotal + ($cantidad * $precio);
            }

            $impuestos = $subtotal * 0.12;

            if (isset($_POST['descuento'])) {
                $descuento = $_POST['descuento'];
            } else {
                $descuento = 0;
            }

            $total = $subtotal + $impuestos - $descuento;

            $factura = new Factura([
                'numero_factura' => $numeroFactura,
                'id_cliente' => intval($_POST['id_cliente']),
                'fecha_factura' => date('Y-m-d H:i:s'),
                'subtotal' => $subtotal,
                'impuestos' => $impuestos,
                'descuento' => $descuento,
                'total' => $total,
                'estado' => 'EMITIDA',
                'observaciones' => htmlspecialchars($_POST['observaciones'] ?? ''),
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'situacion' => 1
            ]);

            $resultadoFactura = $factura->crear();

            if ($resultadoFactura['resultado']) {
                $idFactura = $resultadoFactura['id'];
                foreach ($productos as $producto) {
                    $cantidad = intval($producto['cantidad']);
                    $precio = floatval($producto['precio']);
                    $subtotalLinea = $cantidad * $precio;
                    $detalle = new DetalleFactura([
                        'id_factura' => $idFactura,
                        'id_producto' => intval($producto['id']),
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $subtotalLinea,
                        'descuento_linea' => 0,
                        'total_linea' => $subtotalLinea,
                        'situacion' => 1
                    ]);

                    $detalle->crear();
                    $sqlUpdateStock = "UPDATE productosCC 
                                  SET stock_disponible = stock_disponible - $cantidad 
                                  WHERE id = " . intval($producto['id']);
                    self::SQL($sqlUpdateStock);
                }

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Factura creada exitosamente',
                    'numero_factura' => $numeroFactura,
                    'id_factura' => $idFactura
                ]);
            } else {
                throw new Exception('Error al crear la factura');
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al procesar la factura',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarFacturasAPI()
    {
        getHeadersApi();
        try {
            $sql = "SELECT f.id, f.numero_factura, f.fecha_factura, f.total, f.estado, 
                           c.nombre, c.apellido, c.nit
                    FROM facturasCC f 
                    INNER JOIN clientesCC c ON f.id_cliente = c.id 
                    WHERE f.situacion = 1 
                    ORDER BY f.fecha_factura DESC";

            $facturas = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Facturas encontradas',
                'data' => $facturas
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las facturas',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
