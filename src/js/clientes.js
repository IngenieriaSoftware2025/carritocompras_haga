import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from './funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./lenguaje";

const FormClientes = document.getElementById('FormClientes');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const cliente_nit = document.getElementById('cliente_nit');
const cliente_email = document.getElementById('cliente_email');

function validarNIT() {
    const nit = cliente_nit.value.trim();
    
    let nd, add = 0;

    if (nd = /^(\d+)-?([\dkK])$/.exec(nit)) {
        nd[2] = (nd[2].toLowerCase() === 'k') ? 10 : parseInt(nd[2], 10);

        for (let i = 0; i < nd[1].length; i++) {
            add += ((((i - nd[1].length) * -1) + 1) * parseInt(nd[1][i], 10));
        }
        return ((11 - (add % 11)) % 11) === nd[2];
    } else {
        return false;
    }
}

function validarEmail() {
    const email = cliente_email.value.trim();
    
    if (email.length < 5 || email.length > 150) {
        return false;
    }  
    const regexEmail = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
    return regexEmail.test(email);
}

const EsValidoNit = () => {
    if (validarNIT()) {
        cliente_nit.classList.add('is-valid');
        cliente_nit.classList.remove('is-invalid');
    } else {
        cliente_nit.classList.remove('is-valid');
        cliente_nit.classList.add('is-invalid');
    }
    return validarNIT();
};

const EsValidoEmail = () => {
    if (validarEmail()) {
        cliente_email.classList.add('is-valid');
        cliente_email.classList.remove('is-invalid');
    } else {
        cliente_email.classList.remove('is-valid');
        cliente_email.classList.add('is-invalid');
    }
    return validarEmail();
};

const GuardarCliente = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    const nombre = document.getElementById('cliente_nombre').value.trim();
    const apellido = document.getElementById('cliente_apellido').value.trim();

    if (!nombre) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Campo obligatorio",
            text: "El nombre del cliente es obligatorio",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!apellido) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Campo obligatorio",
            text: "El apellido del cliente es obligatorio",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (cliente_nit.value.trim() && !validarNIT()) {
        Swal.fire({
            position: "center",
            icon: "error",
            title: "NIT INVALIDO",
            text: "El numero de nit ingresado es invalido",
            showConfirmButton: true,
        });
        cliente_nit.classList.remove('is-valid');
        cliente_nit.classList.add('is-invalid');
        BtnGuardar.disabled = false;
        return;
    }

    if (cliente_email.value.trim() && !validarEmail()) {
        Swal.fire({
            position: "center",
            icon: "error",
            title: "EMAIL INVALIDO",
            text: "El formato del email no es válido",
            showConfirmButton: true,
        });
        cliente_email.classList.remove('is-valid');
        cliente_email.classList.add('is-invalid');
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormClientes);
    const url = '/carritocompras/clientes/guardarAPI';
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
            BuscarClientes();
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

const BuscarClientes = async () => {
    const url = '/carritocompras/clientes/buscarAPI';
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
            console.log('Error al buscar clientes:', mensaje);
        }
    } catch (error) {
        console.log(error);
    }
};

const datatable = new DataTable('#TablaClientes', {
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
        { 
            title: 'Nombre Completo', 
            data: 'nombre',
            render: (data, type, row) => `${row.nombre} ${row.apellido}`
        },
        { title: 'Email', data: 'email' },
        { title: 'Teléfono', data: 'telefono' },
        { title: 'NIT', data: 'nit' },
        { title: 'Dirección', data: 'direccion' },
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
                         data-apellido="${row.apellido}"  
                         data-email="${row.email || ''}"  
                         data-telefono="${row.telefono || ''}"  
                         data-nit="${row.nit || ''}"
                         data-direccion="${row.direccion || ''}">
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger eliminar mx-1' 
                         data-id="${data}">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('cliente_id').value = datos.id;
    document.getElementById('cliente_nombre').value = datos.nombre;
    document.getElementById('cliente_apellido').value = datos.apellido;
    document.getElementById('cliente_email').value = datos.email;
    document.getElementById('cliente_telefono').value = datos.telefono;
    document.getElementById('cliente_nit').value = datos.nit;
    document.getElementById('cliente_direccion').value = datos.direccion;

    if (cliente_nit) cliente_nit.classList.remove('is-valid', 'is-invalid');
    if (cliente_email) cliente_email.classList.remove('is-valid', 'is-invalid');
    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    });
};

const limpiarTodo = () => {
    FormClientes.reset();
    if (cliente_nit) cliente_nit.classList.remove('is-valid', 'is-invalid');
    if (cliente_email) cliente_email.classList.remove('is-valid', 'is-invalid');
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
};

const ModificarCliente = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    const nombre = document.getElementById('cliente_nombre').value.trim();
    const apellido = document.getElementById('cliente_apellido').value.trim();

    if (!nombre) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Campo obligatorio",
            text: "El nombre del cliente es obligatorio",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    if (!apellido) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Campo obligatorio",
            text: "El apellido del cliente es obligatorio",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    if (cliente_nit.value.trim() && !validarNIT()) {
        Swal.fire({
            position: "center",
            icon: "error",
            title: "NIT INVALIDO",
            text: "El numero de nit ingresado es invalido",
            showConfirmButton: true,
        });
        cliente_nit.classList.remove('is-valid');
        cliente_nit.classList.add('is-invalid');
        BtnModificar.disabled = false;
        return;
    }

    if (cliente_email.value.trim() && !validarEmail()) {
        Swal.fire({
            position: "center",
            icon: "error",
            title: "EMAIL INVALIDO",
            text: "El formato del email no es válido",
            showConfirmButton: true,
        });
        cliente_email.classList.remove('is-valid');
        cliente_email.classList.add('is-invalid');
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormClientes);
    const url = '/carritocompras/clientes/modificarAPI';
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
            BuscarClientes();
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

const EliminarCliente = async (e) => {
    const idCliente = e.currentTarget.dataset.id;

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
        const url = `/carritocompras/clientes/eliminarAPI?id=${idCliente}`;
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

                BuscarClientes();
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

if (cliente_nit) {
    cliente_nit.addEventListener('blur', EsValidoNit);
}

if (cliente_email) {
    cliente_email.addEventListener('blur', EsValidoEmail);
}
// Event listeners
BuscarClientes();
datatable.on('click', '.eliminar', EliminarCliente);
datatable.on('click', '.modificar', llenarFormulario);
FormClientes.addEventListener('submit', GuardarCliente);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarCliente);