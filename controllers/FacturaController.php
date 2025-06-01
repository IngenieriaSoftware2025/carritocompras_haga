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

    public function renderizarPaginaModificar(Router $router)
    {
        $router->render('carrito/modificar', []);
    }

    public function renderizarPaginaFacturas(Router $router)
    {
        $router->render('carrito/facturas', []);
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

    public static function buscarFacturaPorIdAPI()
    {
        getHeadersApi();

        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de factura es requerido'
            ]);
            return;
        }

        try {
            $idFactura = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $sqlFactura = "SELECT f.id, f.numero_factura, f.id_cliente, f.fecha_factura, 
                                  f.subtotal, f.impuestos, f.descuento, f.total, f.estado, 
                                  f.observaciones, f.fecha_creacion,
                                  c.nombre, c.apellido, c.email, c.telefono, c.nit, c.direccion
                           FROM facturasCC f 
                           INNER JOIN clientesCC c ON f.id_cliente = c.id 
                           WHERE f.id = $idFactura AND f.situacion = 1";

            $factura = self::fetchFirst($sqlFactura);

            if (!$factura) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Factura no encontrada'
                ]);
                return;
            }

            $sqlDetalles = "SELECT df.id, df.id_producto, df.cantidad, df.precio_unitario, 
                                   df.subtotal, df.descuento_linea, df.total_linea,
                                   p.nombre as producto_nombre, p.descripcion, p.stock_disponible
                            FROM detalle_facturasCC df 
                            INNER JOIN productosCC p ON df.id_producto = p.id 
                            WHERE df.id_factura = $idFactura AND df.situacion = 1
                            ORDER BY p.nombre";

            $detalles = self::fetchArray($sqlDetalles);

            $facturaCompleta = [
                'factura' => $factura,
                'detalles' => $detalles,
                'cliente' => [
                    'id' => $factura['id_cliente'],
                    'nombre' => $factura['nombre'],
                    'apellido' => $factura['apellido'],
                    'email' => $factura['email'],
                    'telefono' => $factura['telefono'],
                    'nit' => $factura['nit'],
                    'direccion' => $factura['direccion']
                ]
            ];

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Factura encontrada',
                'data' => $facturaCompleta
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar la factura',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarFacturaAPI()
    {
        getHeadersApi();

        if (empty($_POST['id_factura'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de factura es requerido']);
            return;
        }

        if (empty($_POST['productos'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe seleccionar al menos un producto']);
            return;
        }

        try {
            $idFactura = intval($_POST['id_factura']);
            $sqlVerificar = "SELECT id, numero_factura FROM facturasCC WHERE id = $idFactura AND situacion = 1";
            $facturaExistente = self::fetchFirst($sqlVerificar);

            if (!$facturaExistente) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Factura no encontrada']);
                return;
            }

            $sqlDetallesOriginales = "SELECT id_producto, cantidad FROM detalle_facturasCC 
                                     WHERE id_factura = $idFactura AND situacion = 1";
            $detallesOriginales = self::fetchArray($sqlDetallesOriginales);

            foreach ($detallesOriginales as $detalle) {
                $sqlRevertirStock = "UPDATE productosCC 
                                    SET stock_disponible = stock_disponible + {$detalle['cantidad']} 
                                    WHERE id = {$detalle['id_producto']}";
                self::SQL($sqlRevertirStock);
            }

            $sqlDesactivarDetalles = "UPDATE detalle_facturasCC SET situacion = 0 WHERE id_factura = $idFactura";
            self::SQL($sqlDesactivarDetalles);

            $subtotal = 0;
            $productos = $_POST['productos'];

            foreach ($productos as $producto) {
                $cantidad = intval($producto['cantidad']);
                $precio = floatval($producto['precio']);
                $sqlStock = "SELECT stock_disponible FROM productosCC WHERE id = {$producto['id']}";
                $stockActual = self::fetchFirst($sqlStock);

                if ($cantidad > $stockActual['stock_disponible']) {
                    throw new Exception("Stock insuficiente para producto ID {$producto['id']}. Disponible: {$stockActual['stock_disponible']}, Solicitado: $cantidad");
                }

                $subtotal += $cantidad * $precio;
            }

            $impuestos = $subtotal * 0.12;
            $descuento = floatval($_POST['descuento'] ?? 0);
            $total = $subtotal + $impuestos - $descuento;

            $sqlActualizarFactura = "UPDATE facturasCC SET 
                                    subtotal = $subtotal,
                                    impuestos = $impuestos,
                                    descuento = $descuento,
                                    total = $total,
                                    observaciones = '" . htmlspecialchars($_POST['observaciones'] ?? '') . "'
                                    WHERE id = $idFactura";
            self::SQL($sqlActualizarFactura);
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

                $sqlDescontarStock = "UPDATE productosCC 
                                     SET stock_disponible = stock_disponible - $cantidad 
                                     WHERE id = " . intval($producto['id']);
                self::SQL($sqlDescontarStock);
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Factura modificada exitosamente',
                'numero_factura' => $facturaExistente['numero_factura']
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar la factura',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
