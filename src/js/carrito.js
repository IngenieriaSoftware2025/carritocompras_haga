import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";

const FormCarrito = document.getElementById('FormCarrito');
const SelectCliente = document.getElementById('carrito_cliente');
const InfoCliente = document.getElementById('info_cliente');
const BtnGuardarFactura = document.getElementById('BtnGuardarFactura');
const BtnLimpiarCarrito = document.getElementById('BtnLimpiarCarrito');
const TablaProductos = document.getElementById('TablaProductosDisponibles');
const CuerpoTablaProductos = document.getElementById('CuerpoTablaProductos');
const SeleccionarTodos = document.getElementById('seleccionar_todos');

let productosDisponibles = [];
let productosSeleccionados = [];

document.addEventListener('DOMContentLoaded', function() {
    cargarClientes();
    cargarProductosDisponibles();
    configurarEventos();
});

const cargarClientes = async () => {
    const url = '/carritocompras/carrito/buscarClientesAPI';
    const config = { method: 'GET' };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            SelectCliente.innerHTML = '<option value="">Seleccione un cliente</option>';
            datos.data.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.id;
                option.textContent = `${cliente.nombre} ${cliente.apellido}`;
                option.dataset.email = cliente.email || '';
                option.dataset.telefono = cliente.telefono || '';
                option.dataset.nit = cliente.nit || '';
                SelectCliente.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar clientes:', error);
    }
};

const cargarProductosDisponibles = async () => {
    const url = '/carritocompras/carrito/buscarProductosDisponiblesAPI';
    const config = { method: 'GET' };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            productosDisponibles = datos.data;
            renderizarTablaProductos();
        }
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
};

const renderizarTablaProductos = () => {
    if (productosDisponibles.length === 0) {
        CuerpoTablaProductos.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <p class="text-muted">No hay productos disponibles</p>
                </td>
            </tr>
        `;
        return;
    }

    CuerpoTablaProductos.innerHTML = '';
    productosDisponibles.forEach(producto => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input producto-checkbox" 
                       data-id="${producto.id}" 
                       data-nombre="${producto.nombre}"
                       data-precio="${producto.precio}"
                       data-stock="${producto.stock_disponible}">
            </td>
            <td>${producto.nombre}</td>
            <td>${producto.descripcion || ''}</td>
            <td>Q${parseFloat(producto.precio).toFixed(2)}</td>
            <td>${producto.stock_disponible}</td>
            <td>
                <input type="number" class="form-control cantidad-input" 
                       data-id="${producto.id}"
                       min="1" max="${producto.stock_disponible}" 
                       value="1" disabled>
            </td>
            <td class="subtotal-celda" data-id="${producto.id}">Q0.00</td>
        `;
        CuerpoTablaProductos.appendChild(fila);
    });
};

const configurarEventos = () => {
    SelectCliente.addEventListener('change', mostrarInfoCliente);
    SeleccionarTodos.addEventListener('change', seleccionarTodosProductos);
    CuerpoTablaProductos.addEventListener('change', manejarCambiosProductos);
    BtnGuardarFactura.addEventListener('click', procesarFactura);
    BtnLimpiarCarrito.addEventListener('click', limpiarCarrito);
};

const mostrarInfoCliente = (event) => {
    const opcionSeleccionada = event.target.selectedOptions[0];
    
    if (opcionSeleccionada && opcionSeleccionada.value) {
        document.getElementById('cliente_email').textContent = opcionSeleccionada.dataset.email || '-';
        document.getElementById('cliente_telefono').textContent = opcionSeleccionada.dataset.telefono || '-';
        document.getElementById('cliente_nit').textContent = opcionSeleccionada.dataset.nit || '-';
        InfoCliente.style.display = 'block';
    } else {
        InfoCliente.style.display = 'none';
    }
    
    validarFormulario();
};

const seleccionarTodosProductos = (event) => {
    const checkboxes = document.querySelectorAll('.producto-checkbox');
    const cantidadInputs = document.querySelectorAll('.cantidad-input');
    
    checkboxes.forEach((checkbox, index) => {
        checkbox.checked = event.target.checked;
        cantidadInputs[index].disabled = !event.target.checked;
    });
    
    actualizarCalculos();
};

const manejarCambiosProductos = (event) => {
    if (event.target.classList.contains('producto-checkbox')) {
        const cantidadInput = document.querySelector(`.cantidad-input[data-id="${event.target.dataset.id}"]`);
        cantidadInput.disabled = !event.target.checked;
        
        if (!event.target.checked) {
            cantidadInput.value = 1;
        }
    }
    
    actualizarCalculos();
};

const actualizarCalculos = () => {
    productosSeleccionados = [];
    let subtotal = 0;
    let totalProductos = 0;
    
    const checkboxes = document.querySelectorAll('.producto-checkbox:checked');
    
    checkboxes.forEach(checkbox => {
        const id = checkbox.dataset.id;
        const cantidadInput = document.querySelector(`.cantidad-input[data-id="${id}"]`);
        const cantidad = parseInt(cantidadInput.value) || 1;
        const precio = parseFloat(checkbox.dataset.precio);
        const subtotalProducto = cantidad * precio;
        
        productosSeleccionados.push({
            id: id,
            nombre: checkbox.dataset.nombre,
            cantidad: cantidad,
            precio: precio,
            subtotal: subtotalProducto
        });
        
        subtotal += subtotalProducto;
        totalProductos++;
        
        // Actualizar subtotal
        const subtotalCelda = document.querySelector(`.subtotal-celda[data-id="${id}"]`);
        subtotalCelda.textContent = `Q${subtotalProducto.toFixed(2)}`;
    });
    
    // Limpiar subtotales de productos no seleccionados
    const todasLasCeldas = document.querySelectorAll('.subtotal-celda');
    todasLasCeldas.forEach(celda => {
        const id = celda.dataset.id;
        const checkbox = document.querySelector(`.producto-checkbox[data-id="${id}"]`);
        if (!checkbox.checked) {
            celda.textContent = 'Q0.00';
        }
    });
    
    // Calcular impuestos y total
    const impuestos = subtotal * 0.12;
    const descuento = 0;
    const total = subtotal + impuestos - descuento;

    document.getElementById('total_productos').textContent = totalProductos;
    document.getElementById('subtotal_display').textContent = subtotal.toFixed(2);
    document.getElementById('impuestos_display').textContent = impuestos.toFixed(2);
    document.getElementById('descuento_display').textContent = descuento.toFixed(2);
    document.getElementById('total_display').textContent = total.toFixed(2);

    actualizarListaProductosSeleccionados();
    validarFormulario();
};

const actualizarListaProductosSeleccionados = () => {
    const lista = document.getElementById('lista_productos_seleccionados');
    
    if (productosSeleccionados.length === 0) {
        lista.innerHTML = '<p class="text-muted">No hay productos seleccionados</p>';
        return;
    }
    
    let html = '';
    productosSeleccionados.forEach(producto => {
        html += `
            <div class="mb-2">
                <span class="badge bg-primary me-2">${producto.cantidad}</span>
                <strong>${producto.nombre}</strong> - 
                Q${producto.precio.toFixed(2)} c/u = 
                <span class="text-success">Q${producto.subtotal.toFixed(2)}</span>
            </div>
        `;
    });
    
    lista.innerHTML = html;
};

const validarFormulario = () => {
    const clienteSeleccionado = SelectCliente.value;
    const productosSeleccionadosValidos = productosSeleccionados.length > 0;
    
    BtnGuardarFactura.disabled = !(clienteSeleccionado && productosSeleccionadosValidos);
};

const procesarFactura = async () => {
    if (!SelectCliente.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Cliente requerido',
            text: 'Debe seleccionar un cliente'
        });
        return;
    }
    
    if (productosSeleccionados.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Productos requeridos',
            text: 'Debe seleccionar al menos un producto'
        });
        return;
    }
    
    BtnGuardarFactura.disabled = true;
    
    const formData = new FormData();
    formData.append('id_cliente', SelectCliente.value);
    formData.append('descuento', document.getElementById('carrito_descuento').value);
    formData.append('observaciones', document.getElementById('carrito_observaciones').value);
    
    // Agregar productos seleccionados
    productosSeleccionados.forEach((producto, index) => {
        formData.append(`productos[${index}][id]`, producto.id);
        formData.append(`productos[${index}][cantidad]`, producto.cantidad);
        formData.append(`productos[${index}][precio]`, producto.precio);
    });
    
    const url = '/carritocompras/carrito/guardarFacturaAPI';
    const config = {
        method: 'POST',
        body: formData
    };
    
    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            await Swal.fire({
                icon: 'success',
                title: 'Factura creada',
                text: `Factura ${datos.numero_factura} creada exitosamente`,
                showConfirmButton: true
            });
            
            limpiarCarrito();
            cargarProductosDisponibles();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos.mensaje
            });
        }
    } catch (error) {
        console.error('Error al procesar factura:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al procesar la factura'
        });
    }
    
    BtnGuardarFactura.disabled = false;
};

const limpiarCarrito = () => {
    SelectCliente.value = '';
    InfoCliente.style.display = 'none';
    SeleccionarTodos.checked = false;
    productosSeleccionados = [];
    
    const checkboxes = document.querySelectorAll('.producto-checkbox');
    const cantidadInputs = document.querySelectorAll('.cantidad-input');
    
    checkboxes.forEach((checkbox, index) => {
        checkbox.checked = false;
        cantidadInputs[index].disabled = true;
        cantidadInputs[index].value = 1;
    });
    
    actualizarCalculos();
};