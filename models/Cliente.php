<?php

namespace Model;

class Cliente extends ActiveRecord
{
    public static $tabla = 'clientesCC';
    
    public static $columnasDB = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'direccion',
        'nit',
        'fecha_registro',
        'situacion'
    ];

    public static $idTabla = 'id';
    
    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $telefono;
    public $direccion;
    public $nit;
    public $fecha_registro;
    public $situacion;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->nit = $args['nit'] ?? '';
        $this->fecha_registro = $args['fecha_registro'] ?? '';
        $this->situacion = $args['situacion'] ?? 1;
    }
}
?>