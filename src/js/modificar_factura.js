import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";

// Variables globales
const FormModificarFactura = document.getElementById('FormModificarFactura');
const BtnGuardarModificacion = document.getElementById('BtnGuardarModificacion');
const BtnCancelarModificacion = document.getElementById('BtnCancelarModificacion');
const BtnAgregarProducto = document.getElementById('BtnAgregarProducto');
const NuevoProductoSelect = document.getElementById('nuevo_producto_select');
const NuevoProductoCantidad = document.getElementById('nuevo_producto_cantidad');
const CuerpoTablaProductosActuales = document.getElementById('CuerpoTablaProductosActuales');
const SeleccionarTodosActuales = document.getElementById('seleccionar_todos_actuales');

// Datos de la factura
let facturaActual = null;
let productosActuales = [];
let productosAgregados = [];
let productosDisponibles = [];

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    obtenerIdFacturaDeURL();
    configurarEventos();
});

// Obtener ID de factura desde URL
const obtenerIdFacturaDeURL = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const idFactura = urlParams.get('id');
    
    if (!idFactura) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'ID de factura no especificado'
        }).then(() => {
            window.location.href = '/carritocompras/carrito';
        });
        return;
    }
    
    document.getElementById('modificar_id_factura').value = idFactura;
    cargarDatosFactura(idFactura);
};

// Cargar datos completos de la factura
const cargarDatosFactura = async (idFactura) => {
    const url = `/carritocompras/carrito/buscarFacturaPorIdAPI?id=${idFactura}`;
    const config = { method: 'GET' };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            facturaActual = datos.data;
            mostrarInformacionFactura();
            cargarProductosActuales();
            cargarProductosDisponibles();
        } else {
            throw new Error(datos.mensaje);
        }
    } catch (error) {
        console.error('Error al cargar factura:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar la factura'
        }).then(() => {
            window.location.href = '/carritocompras/carrito';
        });
    }
};

// Mostrar información de la factura
const mostrarInformacionFactura = () => {
    const factura = facturaActual.factura;
    const cliente = facturaActual.cliente;
    
    document.getElementById('numero_factura_display').textContent = factura.numero_factura;
    document.getElementById('fecha_factura_display').textContent = new Date(factura.fecha_factura).toLocaleDateString();
    document.getElementById('estado_factura_display').textContent = factura.estado;
    
    document.getElementById('cliente_nombre_display').textContent = `${cliente.nombre} ${cliente.apellido}`;
    document.getElementById('cliente_nit_display').textContent = cliente.nit || '-';
    document.getElementById('cliente_email_display').textContent = cliente.email || '-';
};

// Cargar productos actuales de la factura
const cargarProductosActuales = () => {
    productosActuales = facturaActual.detalles.map(detalle => ({
        id: detalle.id_producto,
        nombre: detalle.producto_nombre,
        precio: parseFloat(detalle.precio_unitario),
        cantidad: parseInt(detalle.cantidad),
        stock_disponible: parseInt(detalle.stock_disponible),
        subtotal: parseFloat(detalle.total_linea),
        seleccionado: true
    }));
    
    renderizarTablaProductosActuales();
    actualizarCalculos();
};

// Renderizar tabla de productos actuales
const renderizarTablaProductosActuales = () => {
    if (productosActuales.length === 0) {
        CuerpoTablaProductosActuales.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <p class="text-muted">No hay productos en la factura</p>
                </td>
            </tr>
        `;
        return;
    }

    CuerpoTablaProductosActuales.innerHTML = '';
    productosActuales.forEach((producto, index) => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input producto-actual-checkbox" 
                       data-index="${index}" ${producto.seleccionado ? 'checked' : ''}>
            </td>
            <td>${producto.nombre}</td>
            <td>Q${producto.precio.toFixed(2)}</td>
            <td>${producto.stock_disponible + producto.cantidad}</td>
            <td>
                <input type="number" class="form-control cantidad-actual-input" 
                       data-index="${index}" min="1" 
                       max="${producto.stock_disponible + producto.cantidad}" 
                       value="${producto.cantidad}" 
                       ${!producto.seleccionado ? 'disabled' : ''}>
            </td>
            <td class="subtotal-actual-celda">Q${producto.subtotal.toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger eliminar-producto-actual" 
                        data-index="${index}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        CuerpoTablaProductosActuales.appendChild(fila);
    });
};

// Cargar productos disponibles para agregar
const cargarProductosDisponibles = async () => {
    const url = '/carritocompras/carrito/buscarProductosDisponiblesAPI';
    const config = { method: 'GET' };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            productosDisponibles = datos.data.filter(producto => 
                !productosActuales.some(actual => actual.id == producto.id)
            );
            
            renderizarSelectProductosDisponibles();
        }
    } catch (error) {
        console.error('Error al cargar productos disponibles:', error);
    }
};

// Renderizar select de productos disponibles
const renderizarSelectProductosDisponibles = () => {
    NuevoProductoSelect.innerHTML = '<option value="">Seleccione un producto para agregar</option>';
    
    productosDisponibles.forEach(producto => {
        const option = document.createElement('option');
        option.value = producto.id;
        option.textContent = `${producto.nombre} - Q${parseFloat(producto.precio).toFixed(2)} (Stock: ${producto.stock_disponible})`;
        option.dataset.nombre = producto.nombre;
        option.dataset.precio = producto.precio;
        option.dataset.stock = producto.stock_disponible;
        NuevoProductoSelect.appendChild(option);
    });
};

// Configurar eventos
const configurarEventos = () => {
    SeleccionarTodosActuales.addEventListener('change', seleccionarTodosProductosActuales);
    CuerpoTablaProductosActuales.addEventListener('change', manejarCambiosProductosActuales);
    CuerpoTablaProductosActuales.addEventListener('click', manejarClicksTablaActuales);
    BtnAgregarProducto.addEventListener('click', agregarNuevoProducto);
    BtnGuardarModificacion.addEventListener('click', guardarModificacion);
    BtnCancelarModificacion.addEventListener('click', cancelarModificacion);
};

// Seleccionar todos los productos actuales
const seleccionarTodosProductosActuales = (event) => {
    const checkboxes = document.querySelectorAll('.producto-actual-checkbox');
    const cantidadInputs = document.querySelectorAll('.cantidad-actual-input');
    
    checkboxes.forEach((checkbox, index) => {
        checkbox.checked = event.target.checked;
        cantidadInputs[index].disabled = !event.target.checked;
        productosActuales[index].seleccionado = event.target.checked;
    });
    
    actualizarCalculos();
};

// Manejar cambios en productos actuales
const manejarCambiosProductosActuales = (event) => {
    if (event.target.classList.contains('producto-actual-checkbox')) {
        const index = parseInt(event.target.dataset.index);
        const cantidadInput = document.querySelector(`.cantidad-actual-input[data-index="${index}"]`);
        
        productosActuales[index].seleccionado = event.target.checked;
        cantidadInput.disabled = !event.target.checked;
        
        if (!event.target.checked) {
            cantidadInput.value = productosActuales[index].cantidad;
        }
    }
    
    if (event.target.classList.contains('cantidad-actual-input')) {
        const index = parseInt(event.target.dataset.index);
        const nuevaCantidad = parseInt(event.target.value) || 1;
        
        productosActuales[index].cantidad = nuevaCantidad;
        productosActuales[index].subtotal = nuevaCantidad * productosActuales[index].precio;
        
        const subtotalCelda = document.querySelector(`.subtotal-actual-celda`);
        const filas = document.querySelectorAll('#CuerpoTablaProductosActuales tr');
        filas[index].querySelector('.subtotal-actual-celda').textContent = `Q${productosActuales[index].subtotal.toFixed(2)}`;
    }
    
    actualizarCalculos();
};

// Manejar clicks en tabla actuales
const manejarClicksTablaActuales = (event) => {
    if (event.target.classList.contains('eliminar-producto-actual') || 
        event.target.parentElement.classList.contains('eliminar-producto-actual')) {
        const button = event.target.classList.contains('eliminar-producto-actual') ? 
                      event.target : event.target.parentElement;
        const index = parseInt(button.dataset.index);
        
        productosActuales.splice(index, 1);
        renderizarTablaProductosActuales();
        actualizarCalculos();
    }
};

// Agregar nuevo producto
const agregarNuevoProducto = () => {
    const productoSeleccionado = NuevoProductoSelect.selectedOptions[0];
    const cantidad = parseInt(NuevoProductoCantidad.value) || 1;
    
    if (!productoSeleccionado || !productoSeleccionado.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Producto requerido',
            text: 'Debe seleccionar un producto'
        });
        return;
    }
    
    const stockDisponible = parseInt(productoSeleccionado.dataset.stock);
    if (cantidad > stockDisponible) {
        Swal.fire({
            icon: 'warning',
            title: 'Stock insuficiente',
            text: `Stock disponible: ${stockDisponible}`
        });
        return;
    }
    
    const nuevoProducto = {
        id: productoSeleccionado.value,
        nombre: productoSeleccionado.dataset.nombre,
        precio: parseFloat(productoSeleccionado.dataset.precio),
        cantidad: cantidad,
        stock_disponible: stockDisponible,
        subtotal: cantidad * parseFloat(productoSeleccionado.dataset.precio),
        esNuevo: true
    };
    
    productosAgregados.push(nuevoProducto);
    renderizarProductosAgregados();
    
    // Limpiar selección
    NuevoProductoSelect.value = '';
    NuevoProductoCantidad.value = 1;
    
    actualizarCalculos();
};

// Renderizar productos agregados
const renderizarProductosAgregados = () => {
    const container = document.getElementById('productos_agregados_container');
    const tbody = document.getElementById('tabla_productos_agregados');
    
    if (productosAgregados.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'block';
    tbody.innerHTML = '';
    
    productosAgregados.forEach((producto, index) => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${producto.nombre}</td>
            <td>Q${producto.precio.toFixed(2)}</td>
            <td>${producto.cantidad}</td>
            <td>Q${producto.subtotal.toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProductoAgregado(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(fila);
    });
};

// Eliminar producto agregado
window.eliminarProductoAgregado = (index) => {
    productosAgregados.splice(index, 1);
    renderizarProductosAgregados();
    actualizarCalculos();
};

// Actualizar cálculos
const actualizarCalculos = () => {
    let subtotal = 0;
    let totalProductos = 0;
    
    // Sumar productos actuales seleccionados
    productosActuales.forEach(producto => {
        if (producto.seleccionado) {
            subtotal += producto.subtotal;
            totalProductos++;
        }
    });
    
    // Sumar productos agregados
    productosAgregados.forEach(producto => {
        subtotal += producto.subtotal;
        totalProductos++;
    });
    
    const impuestos = subtotal * 0.12;
    const descuento = 0;
    const total = subtotal + impuestos - descuento;
    
    document.getElementById('total_productos_modificar').textContent = totalProductos;
    document.getElementById('subtotal_modificar_display').textContent = subtotal.toFixed(2);
    document.getElementById('impuestos_modificar_display').textContent = impuestos.toFixed(2);
    document.getElementById('descuento_modificar_display').textContent = descuento.toFixed(2);
    document.getElementById('total_modificar_display').textContent = total.toFixed(2);
    
    actualizarListaProductosFinales();
    validarFormulario();
};

// Actualizar lista de productos finales
const actualizarListaProductosFinales = () => {
    const lista = document.getElementById('lista_productos_finales');
    const productosFinales = [
        ...productosActuales.filter(p => p.seleccionado),
        ...productosAgregados
    ];
    
    if (productosFinales.length === 0) {
        lista.innerHTML = '<p class="text-muted">No hay productos seleccionados</p>';
        return;
    }
    
    let html = '';
    productosFinales.forEach(producto => {
        const etiqueta = producto.esNuevo ? 
            '<span class="badge bg-success ms-2">NUEVO</span>' : 
            '<span class="badge bg-primary ms-2">ACTUAL</span>';
        
        html += `
            <div class="mb-2">
                <span class="badge bg-secondary me-2">${producto.cantidad}</span>
                <strong>${producto.nombre}</strong> - 
                Q${producto.precio.toFixed(2)} c/u = 
                <span class="text-success">Q${producto.subtotal.toFixed(2)}</span>
                ${etiqueta}
            </div>
        `;
    });
    
    lista.innerHTML = html;
};

// Validar formulario
const validarFormulario = () => {
    const productosSeleccionadosActuales = productosActuales.filter(p => p.seleccionado).length;
    const productosAgregadosTotal = productosAgregados.length;
    const tieneProductos = productosSeleccionadosActuales > 0 || productosAgregadosTotal > 0;
    
    BtnGuardarModificacion.disabled = !tieneProductos;
};

// Guardar modificación
const guardarModificacion = async () => {
    const productosFinales = [
        ...productosActuales.filter(p => p.seleccionado),
        ...productosAgregados
    ];
    
    if (productosFinales.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Productos requeridos',
            text: 'Debe seleccionar al menos un producto'
        });
        return;
    }
    
    BtnGuardarModificacion.disabled = true;
    
    const formData = new FormData();
    formData.append('id_factura', document.getElementById('modificar_id_factura').value);
    formData.append('descuento', document.getElementById('modificar_descuento').value);
    formData.append('observaciones', document.getElementById('modificar_observaciones').value);
    
    productosFinales.forEach((producto, index) => {
        formData.append(`productos[${index}][id]`, producto.id);
        formData.append(`productos[${index}][cantidad]`, producto.cantidad);
        formData.append(`productos[${index}][precio]`, producto.precio);
    });
    
    const url = '/carritocompras/carrito/modificarFacturaAPI';
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
                title: 'Factura modificada',
                text: `Factura ${datos.numero_factura} modificada exitosamente`,
                showConfirmButton: true
            });
            
            window.location.href = '/carritocompras/carrito';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos.mensaje
            });
        }
    } catch (error) {
        console.error('Error al modificar factura:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al modificar la factura'
        });
    }
    
    BtnGuardarModificacion.disabled = false;
};

// Cancelar modificación
const cancelarModificacion = () => {
    Swal.fire({
        title: '¿Cancelar modificación?',
        text: 'Se perderán todos los cambios realizados',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, continuar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/carritocompras/carrito';
        }
    });
};