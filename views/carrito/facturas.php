<div class="container mt-4">

    <!-- Encabezado Gestión de Facturas -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-12">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #198754;">
                <div class="card-header bg-success text-white">
                    <h4 class="text-center mb-0">
                        <i class="bi bi-receipt-cutoff me-2"></i>Gestión de Facturas
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-9">
                            <p class="mb-0">Administre las facturas del sistema. Puede modificar facturas existentes o crear nuevas desde el carrito de compras.</p>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="/carritocompras/carrito" class="btn btn-primary">
                                <i class="bi bi-cart-plus me-2"></i>Nueva Factura
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="filtro_estado" class="form-label">Estado</label>
                            <select class="form-control" id="filtro_estado">
                                <option value="">Todos los estados</option>
                                <option value="EMITIDA">Emitida</option>
                                <option value="PAGADA">Pagada</option>
                                <option value="ANULADA">Anulada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtro_fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="filtro_fecha_inicio">
                        </div>
                        <div class="col-md-3">
                            <label for="filtro_fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="filtro_fecha_fin">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary w-100" id="BtnBuscarFacturas">
                                <i class="bi bi-search me-1"></i>Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Facturas -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-light">
                    <h5 class="text-center mb-0">
                        <i class="bi bi-table me-2"></i>Lista de Facturas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="TablaFacturas">
                            <thead class="table-success">
                                <tr>
                                    <th>No.</th>
                                    <th>Número Factura</th>
                                    <th>Cliente</th>
                                    <th>NIT</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="CuerpoTablaFacturas">
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="spinner-border text-success" role="status">
                                            <span class="visually-hidden">Cargando</span>
                                        </div>
                                        <p class="mt-2">Cargando facturas del sistema</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen Estadísticas -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4 id="total_facturas">0</h4>
                            <p>Total Facturas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4 id="facturas_emitidas">0</h4>
                            <p>Emitidas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4 id="facturas_pagadas">0</h4>
                            <p>Pagadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4 id="total_ventas">Q0.00</h4>
                            <p>Total Ventas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="<?= asset('build/js/facturas.js') ?>"></script>