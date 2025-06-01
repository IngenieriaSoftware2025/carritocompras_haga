import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "./lenguaje";

// Variables globales
const BtnBuscarFacturas = document.getElementById('BtnBuscarFacturas');
const FiltroEstado = document.getElementById('filtro_estado');
const FiltroFechaInicio = document.getElementById('filtro_fecha_inicio');
const FiltroFechaFin = document.getElementById('filtro_fecha_fin');
const CuerpoTablaFacturas = document.getElementById('CuerpoTablaFacturas');

let facturasData = [];

document.addEventListener('DOMContentLoaded', function() {
    configurarEventos();
    cargarFacturas();
    configurarDataTable();
});

const configurarEventos = () => {
    BtnBuscarFacturas.addEventListener('click', aplicarFiltros);
    FiltroEstado.addEventListener('change', aplicarFiltros);
};

const cargarFacturas = async () => {
    const url = '/carritocompras/carrito/buscarFacturasAPI';
    const config = { method: 'GET' };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            facturasData = datos.data;
            actualizarDataTable();
            actualizarEstadisticas();
        } else {
            console.error('Error al cargar facturas:', datos.mensaje);
            mostrarErrorCarga();
        }
    } catch (error) {
        console.error('Error al cargar facturas:', error);
        mostrarErrorCarga();
    }
};

const mostrarErrorCarga = () => {
    CuerpoTablaFacturas.innerHTML = `
        <tr>
            <td colspan="8" class="text-center">
                <p class="text-danger">Error al cargar las facturas</p>
                <button class="btn btn-outline-primary" onclick="cargarFacturas()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
                </button>
            </td>
        </tr>
    `;
};

let datatable;
const configurarDataTable = () => {
    datatable = new DataTable('#TablaFacturas', {
        language: lenguaje,
        responsive: true,
        data: [],
        columns: [
            {
                title: 'No.',
                data: null,
                width: '5%',
                render: (data, type, row, meta) => meta.row + 1
            },
            { 
                title: 'Número Factura', 
                data: 'numero_factura',
                width: '15%'
            },
            { 
                title: 'Cliente', 
                data: 'nombre',
                render: (data, type, row) => `${row.nombre} ${row.apellido}`
            },
            { 
                title: 'NIT', 
                data: 'nit',
                width: '10%'
            },
            { 
                title: 'Fecha', 
                data: 'fecha_factura',
                width: '12%',
                render: (data) => new Date(data).toLocaleDateString()
            },
            { 
                title: 'Total', 
                data: 'total',
                width: '10%',
                render: (data) => `Q${parseFloat(data).toFixed(2)}`
            },
            {
                title: 'Estado',
                data: 'estado',
                width: '8%',
                render: (data) => {
                    let badgeClass = 'bg-secondary';
                    switch (data) {
                        case 'EMITIDA':
                            badgeClass = 'bg-primary';
                            break;
                        case 'PAGADA':
                            badgeClass = 'bg-success';
                            break;
                        case 'ANULADA':
                            badgeClass = 'bg-danger';
                            break;
                    }
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            {
                title: 'Acciones',
                data: 'id',
                width: '15%',
                searchable: false,
                orderable: false,
                render: (data, type, row) => {
                    return `
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-sm btn-warning modificar-factura" 
                                    data-id="${data}" 
                                    data-numero="${row.numero_factura}"
                                    title="Modificar factura">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-info ver-detalles" 
                                    data-id="${data}" 
                                    data-numero="${row.numero_factura}"
                                    title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger generar-pdf" 
                                    data-id="${data}" 
                                    data-numero="${row.numero_factura}"
                                    title="Generar PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[4, 'desc']], // Ordenar por fecha descendente
        pageLength: 25,
        scrollX: true
    });

    // Event listeners para botones de acciones
    datatable.on('click', '.modificar-factura', function() {
        const id = this.dataset.id;
        const numero = this.dataset.numero;
        modificarFactura(id, numero);
    });

    datatable.on('click', '.ver-detalles', function() {
        const id = this.dataset.id;
        verDetallesFactura(id);
    });

    datatable.on('click', '.generar-pdf', function() {
        const id = this.dataset.id;
        const numero = this.dataset.numero;
        generarPDF(id, numero);
    });
};

const actualizarDataTable = () => {
    if (datatable) {
        datatable.clear();
        if (facturasData && facturasData.length > 0) {
            datatable.rows.add(facturasData);
        }
        datatable.draw();
    }
};

const aplicarFiltros = () => {
    let facturasFiltradas = [...facturasData];

    // Filtro por estado
    const estado = FiltroEstado.value;
    if (estado) {
        facturasFiltradas = facturasFiltradas.filter(factura => factura.estado === estado);
    }

    // Filtro por fecha inicio
    const fechaInicio = FiltroFechaInicio.value;
    if (fechaInicio) {
        facturasFiltradas = facturasFiltradas.filter(factura => {
            const fechaFactura = new Date(factura.fecha_factura).toISOString().split('T')[0];
            return fechaFactura >= fechaInicio;
        });
    }

    // Filtro por fecha fin
    const fechaFin = FiltroFechaFin.value;
    if (fechaFin) {
        facturasFiltradas = facturasFiltradas.filter(factura => {
            const fechaFactura = new Date(factura.fecha_factura).toISOString().split('T')[0];
            return fechaFactura <= fechaFin;
        });
    }

    // Actualizar tabla con filtros
    if (datatable) {
        datatable.clear();
        if (facturasFiltradas.length > 0) {
            datatable.rows.add(facturasFiltradas);
        }
        datatable.draw();
    }

    // Actualizar estadísticas con datos filtrados
    actualizarEstadisticas(facturasFiltradas);
};

const actualizarEstadisticas = (datos = facturasData) => {
    if (!datos || datos.length === 0) {
        document.getElementById('total_facturas').textContent = '0';
        document.getElementById('facturas_emitidas').textContent = '0';
        document.getElementById('facturas_pagadas').textContent = '0';
        document.getElementById('total_ventas').textContent = 'Q0.00';
        return;
    }

    const totalFacturas = datos.length;
    const facturasEmitidas = datos.filter(f => f.estado === 'EMITIDA').length;
    const facturasPagadas = datos.filter(f => f.estado === 'PAGADA').length;
    const totalVentas = datos.reduce((sum, f) => sum + parseFloat(f.total), 0);

    document.getElementById('total_facturas').textContent = totalFacturas.toString();
    document.getElementById('facturas_emitidas').textContent = facturasEmitidas.toString();
    document.getElementById('facturas_pagadas').textContent = facturasPagadas.toString();
    document.getElementById('total_ventas').textContent = `Q${totalVentas.toFixed(2)}`;
};

const modificarFactura = (id, numero) => {
    // Redirigir a página de modificación
    window.location.href = `/carritocompras/carrito/modificar?id=${id}`;
};

const verDetallesFactura = async (id) => {
    try {
        const url = `/carritocompras/carrito/buscarFacturaPorIdAPI?id=${id}`;
        const respuesta = await fetch(url);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            const facturaData = datos.data;
            mostrarModalDetalles(facturaData);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los detalles de la factura'
            });
        }
    } catch (error) {
        console.error('Error al cargar detalles:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar los detalles de la factura'
        });
    }
};

const mostrarModalDetalles = (facturaData) => {
    const factura = facturaData.factura;
    const cliente = facturaData.cliente;
    const detalles = facturaData.detalles;

    let productosHTML = '';
    if (detalles && detalles.length > 0) {
        productosHTML = detalles.map(detalle => `
            <tr>
                <td>${detalle.producto_nombre}</td>
                <td class="text-center">${detalle.cantidad}</td>
                <td class="text-end">Q${parseFloat(detalle.precio_unitario).toFixed(2)}</td>
                <td class="text-end">Q${parseFloat(detalle.total_linea).toFixed(2)}</td>
            </tr>
        `).join('');
    }

    const modalContent = `
        <div class="row">
            <div class="col-md-6">
                <h6>Información de Factura</h6>
                <p><strong>Número:</strong> ${factura.numero_factura}</p>
                <p><strong>Fecha:</strong> ${new Date(factura.fecha_factura).toLocaleDateString()}</p>
                <p><strong>Estado:</strong> <span class="badge bg-primary">${factura.estado}</span></p>
            </div>
            <div class="col-md-6">
                <h6>Cliente</h6>
                <p><strong>Nombre:</strong> ${cliente.nombre} ${cliente.apellido}</p>
                <p><strong>NIT:</strong> ${cliente.nit || '-'}</p>
                <p><strong>Email:</strong> ${cliente.email || '-'}</p>
            </div>
        </div>
        <hr>
        <h6>Productos</h6>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                ${productosHTML}
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td class="text-end">Q${parseFloat(factura.subtotal).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td><strong>IVA:</strong></td>
                        <td class="text-end">Q${parseFloat(factura.impuestos).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td><strong>Descuento:</strong></td>
                        <td class="text-end">Q${parseFloat(factura.descuento || 0).toFixed(2)}</td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>TOTAL:</strong></td>
                        <td class="text-end"><strong>Q${parseFloat(factura.total).toFixed(2)}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    `;

    Swal.fire({
        title: `Detalles de Factura ${factura.numero_factura}`,
        html: modalContent,
        width: '800px',
        showConfirmButton: true,
        confirmButtonText: 'Cerrar',
        showCancelButton: true,
        cancelButtonText: 'Modificar',
        cancelButtonColor: '#ffc107'
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            modificarFactura(factura.id, factura.numero_factura);
        }
    });
};

const generarPDF = (id, numero) => {
    Swal.fire({
        icon: 'info',
        title: 'Funcionalidad en desarrollo',
        text: `La generación de PDF para la factura ${numero} estará disponible próximamente.`,
        showConfirmButton: true
    });
};

window.cargarFacturas = cargarFacturas;