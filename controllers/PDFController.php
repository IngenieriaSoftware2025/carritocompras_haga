<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use MVC\Router;
use Mpdf\Mpdf;

class PDFController extends ActiveRecord
{
    public static function generarFacturaPDF()
    {
        // Limpiar cualquier salida previa
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');

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

            // Verificar que el ID sea válido
            if (!$idFactura || $idFactura <= 0) {
                throw new Exception('ID de factura inválido');
            }

            $datosFactura = self::obtenerDatosFactura($idFactura);

            if (!$datosFactura) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Factura no encontrada'
                ]);
                return;
            }

            $htmlFactura = self::generarHTMLFactura($datosFactura);
            $nombreArchivo = self::crearPDF($htmlFactura, $datosFactura['factura']['numero_factura']);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'PDF generado exitosamente',
                'archivo' => $nombreArchivo,
                'url_descarga' => "/carritocompras/public/storage/pdfs/facturas/{$nombreArchivo}"
            ]);
        } catch (Exception $e) {
            // Limpiar cualquier salida
            if (ob_get_level()) {
                ob_end_clean();
            }

            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al generar PDF: ' . $e->getMessage(),
                'detalle' => $e->getMessage()
            ]);
        }
        exit;
    }

    private static function obtenerDatosFactura($idFactura)
    {
        $sqlFactura = "SELECT f.id, f.numero_factura, f.id_cliente, f.fecha_factura, 
                              f.subtotal, f.impuestos, f.descuento, f.total, f.estado, 
                              f.observaciones, f.fecha_creacion,
                              c.nombre, c.apellido, c.email, c.telefono, c.nit, c.direccion
                       FROM facturasCC f 
                       INNER JOIN clientesCC c ON f.id_cliente = c.id 
                       WHERE f.id = $idFactura AND f.situacion = 1";

        $factura = self::fetchFirst($sqlFactura);

        if (!$factura) {
            return null;
        }

        $sqlDetalles = "SELECT df.id, df.id_producto, df.cantidad, df.precio_unitario, 
                               df.subtotal, df.descuento_linea, df.total_linea,
                               p.nombre as producto_nombre, p.descripcion
                        FROM detalle_facturasCC df 
                        INNER JOIN productosCC p ON df.id_producto = p.id 
                        WHERE df.id_factura = $idFactura AND df.situacion = 1
                        ORDER BY p.nombre";

        $detalles = self::fetchArray($sqlDetalles);

        return [
            'factura' => $factura,
            'detalles' => $detalles,
            'cliente' => [
                'nombre' => $factura['nombre'],
                'apellido' => $factura['apellido'],
                'email' => $factura['email'],
                'telefono' => $factura['telefono'],
                'nit' => $factura['nit'],
                'direccion' => $factura['direccion']
            ]
        ];
    }

    private static function generarHTMLFactura($datos)
    {
        $factura = $datos['factura'];
        $cliente = $datos['cliente'];
        $detalles = $datos['detalles'];

        // Encabezado de la empresa
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; }
                .empresa { font-size: 18px; font-weight: bold; }
                .info-factura { margin: 20px 0; }
                .cliente-info { background-color: #f5f5f5; padding: 10px; margin: 15px 0; }
                .tabla-productos { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .tabla-productos th, .tabla-productos td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .tabla-productos th { background-color: #28a745; color: white; }
                .totales { margin-top: 20px; }
                .totales-tabla { width: 300px; margin-left: auto; }
                .totales-tabla td { padding: 5px; }
                .total-final { font-weight: bold; font-size: 14px; }
                .footer { margin-top: 30px; text-align: center; font-size: 10px; }
            </style>
        </head>
        <body>';

        // Encabezado empresa
        $html .= '
        <div class="header">
            <div class="empresa">COMANDO DE INFORMÁTICA Y TECNOLOGÍA</div>
            <div>Sistema de Carrito de Compras</div>
            <div>Guatemala, Guatemala</div>
        </div>';

        // Información de factura
        $fechaFactura = date('d/m/Y H:i', strtotime($factura['fecha_factura']));
        $html .= '
        <div class="info-factura">
            <table style="width: 100%;">
                <tr>
                    <td><strong>Factura No:</strong> ' . $factura['numero_factura'] . '</td>
                    <td style="text-align: right;"><strong>Fecha:</strong> ' . $fechaFactura . '</td>
                </tr>
                <tr>
                    <td><strong>Estado:</strong> ' . $factura['estado'] . '</td>
                    <td style="text-align: right;"><strong>Emisión:</strong> ' . date('d/m/Y H:i', strtotime($factura['fecha_creacion'])) . '</td>
                </tr>
            </table>
        </div>';

        // Información del cliente
        $html .= '
        <div class="cliente-info">
            <h3>INFORMACIÓN DEL CLIENTE</h3>
            <table style="width: 100%;">
                <tr>
                    <td><strong>Nombre:</strong> ' . $cliente['nombre'] . ' ' . $cliente['apellido'] . '</td>
                    <td><strong>NIT:</strong> ' . ($cliente['nit'] ?: 'C/F') . '</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong> ' . ($cliente['email'] ?: '-') . '</td>
                    <td><strong>Teléfono:</strong> ' . ($cliente['telefono'] ?: '-') . '</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Dirección:</strong> ' . ($cliente['direccion'] ?: '-') . '</td>
                </tr>
            </table>
        </div>';

        // Tabla de productos
        $html .= '
        <table class="tabla-productos">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="40%">Producto</th>
                    <th width="15%">Precio Unit.</th>
                    <th width="10%">Cantidad</th>
                    <th width="15%">Subtotal</th>
                    <th width="15%">Total Línea</th>
                </tr>
            </thead>
            <tbody>';

        $contador = 1;
        foreach ($detalles as $detalle) {
            $html .= '
                <tr>
                    <td style="text-align: center;">' . $contador . '</td>
                    <td>' . $detalle['producto_nombre'] . '</td>
                    <td style="text-align: right;">Q' . number_format($detalle['precio_unitario'], 2) . '</td>
                    <td style="text-align: center;">' . $detalle['cantidad'] . '</td>
                    <td style="text-align: right;">Q' . number_format($detalle['subtotal'], 2) . '</td>
                    <td style="text-align: right;">Q' . number_format($detalle['total_linea'], 2) . '</td>
                </tr>';
            $contador++;
        }

        $html .= '
            </tbody>
        </table>';

        // Totales
        $html .= '
        <div class="totales">
            <table class="totales-tabla">
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td style="text-align: right;">Q' . number_format($factura['subtotal'], 2) . '</td>
                </tr>
                <tr>
                    <td><strong>IVA (12%):</strong></td>
                    <td style="text-align: right;">Q' . number_format($factura['impuestos'], 2) . '</td>
                </tr>
                <tr>
                    <td><strong>Descuento:</strong></td>
                    <td style="text-align: right;">Q' . number_format($factura['descuento'], 2) . '</td>
                </tr>
                <tr class="total-final">
                    <td><strong>TOTAL:</strong></td>
                    <td style="text-align: right; border-top: 2px solid #000;">Q' . number_format($factura['total'], 2) . '</td>
                </tr>
            </table>
        </div>';

        // Observaciones
        if ($factura['observaciones']) {
            $html .= '
            <div style="margin-top: 20px;">
                <strong>Observaciones:</strong><br>
                ' . $factura['observaciones'] . '
            </div>';
        }

        // Footer
        $html .= '
        <div class="footer">
            <p>Comando de Informática y Tecnología - ' . date('Y') . '</p>
            <p>Sistema de Carrito de Compras - Factura generada automáticamente</p>
        </div>
        
        </body>
        </html>';

        return $html;
    }

    private static function crearPDF($html, $numeroFactura)
    {
        try {
            // Verificar que mPDF esté disponible
            if (!class_exists('\Mpdf\Mpdf')) {
                throw new Exception('mPDF no está instalado. Ejecute: composer require mpdf/mpdf');
            }

            $mpdf = new \Mpdf\Mpdf([
                'format' => 'Letter',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_header' => 10,
                'margin_footer' => 10,
                'tempDir' => sys_get_temp_dir()
            ]);
        } catch (Exception $e) {
            throw new Exception('Error al inicializar mPDF: ' . $e->getMessage());
        }

        // Metadatos del PDF
        $mpdf->SetTitle('Factura ' . $numeroFactura);
        $mpdf->SetAuthor('Sistema Carrito de Compras');
        $mpdf->SetSubject('Factura de Venta');

        // Escribir HTML al PDF
        $mpdf->WriteHTML($html);

        // Generar nombre único del archivo
        $numeroFacturaLimpio = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $numeroFactura);
        $nombreArchivo = 'factura_' . $numeroFacturaLimpio . '_' . date('YmdHis') . '.pdf';

        // Crear directorio si no existe
        $rutaDirectorio = __DIR__ . '/../public/storage/pdfs/facturas/';
        if (!file_exists($rutaDirectorio)) {
            if (!mkdir($rutaDirectorio, 0755, true)) {
                throw new Exception('No se pudo crear el directorio: ' . $rutaDirectorio);
            }
        }

        // Verificar permisos de escritura
        if (!is_writable($rutaDirectorio)) {
            throw new Exception('No hay permisos de escritura en: ' . $rutaDirectorio);
        }

        // Guardar archivo
        $rutaCompleta = $rutaDirectorio . $nombreArchivo;
        $mpdf->Output($rutaCompleta, 'F');

        // Verificar que el archivo se creó correctamente
        if (!file_exists($rutaCompleta)) {
            throw new Exception('Error al guardar el archivo PDF');
        }

        return $nombreArchivo;
    }

    public static function descargarPDF()
    {
        if (empty($_GET['archivo'])) {
            header('HTTP/1.0 404 Not Found');
            echo 'Archivo no especificado';
            return;
        }

        $nombreArchivo = htmlspecialchars($_GET['archivo']);
        $rutaArchivo = __DIR__ . '/../public/storage/pdfs/facturas/' . $nombreArchivo;

        if (!file_exists($rutaArchivo)) {
            header('HTTP/1.0 404 Not Found');
            echo 'Archivo no encontrado';
            return;
        }

        // Headers para descarga
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Content-Length: ' . filesize($rutaArchivo));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        // Enviar archivo
        readfile($rutaArchivo);
        exit;
    }
}
