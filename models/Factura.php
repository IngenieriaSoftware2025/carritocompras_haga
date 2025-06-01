<?php

namespace Model;

class Factura extends ActiveRecord
{
    public static $tabla = 'facturasCC';

    public static $columnasDB = [
        'numero_factura',
        'id_cliente',
        'fecha_factura',
        'subtotal',
        'impuestos',
        'descuento',
        'total',
        'estado',
        'observaciones',
        'fecha_creacion',
        'situacion'
    ];

    public static $idTabla = 'id';

    public $id;
    public $numero_factura;
    public $id_cliente;
    public $fecha_factura;
    public $subtotal;
    public $impuestos;
    public $descuento;
    public $total;
    public $estado;
    public $observaciones;
    public $fecha_creacion;
    public $situacion;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->numero_factura = $args['numero_factura'] ?? '';
        $this->id_cliente = $args['id_cliente'] ?? null;
        $this->fecha_factura = $args['fecha_factura'] ?? '';
        $this->subtotal = $args['subtotal'] ?? 0.00;
        $this->impuestos = $args['impuestos'] ?? 0.00;
        $this->descuento = $args['descuento'] ?? 0.00;
        $this->total = $args['total'] ?? 0.00;
        $this->estado = $args['estado'] ?? 'EMITIDA';
        $this->observaciones = $args['observaciones'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'] ?? '';
        $this->situacion = $args['situacion'] ?? 1;
    }

}

?>