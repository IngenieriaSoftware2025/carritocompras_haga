<?php

namespace Model;

class Producto extends ActiveRecord
{
    public static $tabla = 'productosCC';
    
    public static $columnasDB = [
        'nombre',
        'descripcion',
        'precio',
        'stock_disponible',
        'stock_minimo',
        'fecha_creacion',
        'fecha_actualizacion',
        'situacion'
    ];

    public static $idTabla = 'id';
    
    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $stock_disponible;
    public $stock_minimo;
    public $fecha_creacion;
    public $fecha_actualizacion;
    public $situacion;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->precio = $args['precio'] ?? 0.00;
        $this->stock_disponible = $args['stock_disponible'] ?? 0;
        $this->stock_minimo = $args['stock_minimo'] ?? 5;
        $this->fecha_creacion = $args['fecha_creacion'] ?? '';
        $this->fecha_actualizacion = $args['fecha_actualizacion'] ?? '';
        $this->situacion = $args['situacion'] ?? 1;
    }

    
}
?>