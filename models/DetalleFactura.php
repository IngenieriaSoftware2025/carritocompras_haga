<?php

namespace Model;

class DetalleFactura extends ActiveRecord
{
    public static $tabla = 'detalle_facturasCC';

    public static $columnasDB = [
        'id_factura',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'descuento_linea',
        'total_linea',
        'situacion'
    ];

    public static $idTabla = 'id';

    public $id;
    public $id_factura;
    public $id_producto;
    public $cantidad;
    public $precio_unitario;
    public $subtotal;
    public $descuento_linea;
    public $total_linea;
    public $situacion;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->id_factura = $args['id_factura'] ?? null;
        $this->id_producto = $args['id_producto'] ?? null;
        $this->cantidad = $args['cantidad'] ?? 0;
        $this->precio_unitario = $args['precio_unitario'] ?? 0.00;
        $this->subtotal = $args['subtotal'] ?? 0.00;
        $this->descuento_linea = $args['descuento_linea'] ?? 0.00;
        $this->total_linea = $args['total_linea'] ?? 0.00;
        $this->situacion = $args['situacion'] ?? 1;
    }
}

?>