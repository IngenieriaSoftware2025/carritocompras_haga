import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from './funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./lenguaje";

const FormProductos = document.getElementById('FormProductos');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');

const GuardarProducto = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    // Validación personalizada sin required
    const nombre = document.getElementById('producto_nombre').value.trim();
    const precio = document.getElementById('producto_precio').value.trim();
    const stock = document.getElementById('producto_stock').value.trim();

    // Validar campos obligatorios
    if (!nombre) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Campo obligatorio",
            text: "El nombre del producto es obligatorio",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!precio || parseFloat(precio) <= 0) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Precio inválido",
            text: "El precio debe ser mayor a 0",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!stock || parseInt(stock) < 0) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Stock inválido",
            text: "El stock no puede ser negativo",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormProductos);
    const url = '/carritocompras/productos/guardarAPI';
    const config = {
        method: 'POST',
        body
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        console.log(datos);
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarProductos();
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error);
    }
    BtnGuardar.disabled = false;
};

const BuscarProductos = async () => {
    const url = '/carritocompras/productos/buscarAPI';
    const config = {
        method: 'GET'
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            datatable.clear().draw();
            if (data && data.length > 0) {
                datatable.rows.add(data).draw();
            }
        } else {
            console.log('Error al buscar productos:', mensaje);
        }
    } catch (error) {
        console.log(error);
    }
};

const datatable = new DataTable('#TablaProductos', {
    language: lenguaje,
    responsive: false,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'id',
            width: '%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { title: 'Producto', data: 'nombre' },
        { title: 'Descripción', data: 'descripcion' },
        { 
            title: 'Precio', 
            data: 'precio',
            render: (data) => `Q${parseFloat(data || 0).toFixed(2)}`
        },
        { title: 'Stock', data: 'stock_disponible' },
        {
            title: 'Estado',
            data: 'stock_disponible',
            render: (data, type, row) => {
                const stock = parseInt(row.stock_disponible || 0);
                if (stock > 0) {
                    return "<span class='badge bg-success'>Disponible</span>";
                } else {
                    return "<span class='badge bg-danger'>Sin Stock</span>";
                }
            }
        },
        {
            title: 'Acciones',
            data: 'id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-nombre="${row.nombre}"  
                         data-descripcion="${row.descripcion || ''}"  
                         data-precio="${row.precio}"  
                         data-stock="${row.stock_disponible}"  
                         data-stock-minimo="${row.stock_minimo}">
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger eliminar mx-1' 
                         data-id="${data}"
                         data-stock="${row.stock_disponible}">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('producto_id').value = datos.id;
    document.getElementById('producto_nombre').value = datos.nombre;
    document.getElementById('producto_descripcion').value = datos.descripcion;
    document.getElementById('producto_precio').value = datos.precio;
    document.getElementById('producto_stock').value = datos.stock;
    document.getElementById('producto_stock_minimo').value = datos.stockMinimo;

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    });
};

const limpiarTodo = () => {
    FormProductos.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
};

const ModificarProducto = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    // Validación personalizada
    const nombre = document.getElementById('producto_nombre').value.trim();
    const precio = document.getElementById('producto_precio').value.trim();
    const stock = document.getElementById('producto_stock').value.trim();

    if (!nombre) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Campo obligatorio",
            text: "El nombre del producto es obligatorio",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    if (!precio || parseFloat(precio) <= 0) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Precio inválido",
            text: "El precio debe ser mayor a 0",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    if (!stock || parseInt(stock) < 0) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Stock inválido",
            text: "El stock no puede ser negativo",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormProductos);
    const url = '/carritocompras/productos/modificarAPI';
    const config = {
        method: 'POST',
        body
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarProductos();
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error);
    }
    BtnModificar.disabled = false;
};

const EliminarProducto = async (e) => {
    const idProducto = e.currentTarget.dataset.id;
    const stock = e.currentTarget.dataset.stock;

    if (parseInt(stock) > 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "No se puede eliminar",
            text: "El producto tiene stock disponible. No se puede eliminar.",
            showConfirmButton: true,
        });
        return;
    }

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: 'Esta completamente seguro que desea eliminar este registro',
        showConfirmButton: true,
        confirmButtonText: 'Si, Eliminar',
        confirmButtonColor: 'red',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/carritocompras/productos/eliminarAPI?id=${idProducto}`;
        const config = {
            method: 'GET'
        };

        try {
            const consulta = await fetch(url, config);
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Exito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarProductos();
            } else {
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error",
                    text: mensaje,
                    showConfirmButton: true,
                });
            }
        } catch (error) {
            console.log(error);
        }
    }
};

// Event listeners
BuscarProductos();
datatable.on('click', '.eliminar', EliminarProducto);
datatable.on('click', '.modificar', llenarFormulario);
FormProductos.addEventListener('submit', GuardarProducto);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarProducto);