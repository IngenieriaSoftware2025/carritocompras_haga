<div class="container mt-4">

    <!-- Selección de Cliente -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #28a745;">
                <div class="card-header bg-success text-white">
                    <h4 class="text-center mb-0">
                        <i class="bi bi-person-check me-2"></i>Seleccionar Cliente
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form id="FormCarrito">
                        <input type="hidden" id="carrito_descuento" name="descuento" value="0">
                        <input type="hidden" id="carrito_observaciones" name="observaciones" value="">
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="carrito_cliente" class="form-label">Cliente</label>
                                <select class="form-control" id="carrito_cliente" name="id_cliente">
                                    <option value="">Seleccione un cliente</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row" id="info_cliente" style="display: none;">
                            <div class="col-md-4">
                                <p><strong>Email:</strong> <span id="cliente_email">-</span></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Teléfono:</strong> <span id="cliente_telefono">-</span></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>NIT:</strong> <span id="cliente_nit">-</span></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Productos Disponibles -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center mb-0">
                        <i class="bi bi-box-seam me-2"></i>Productos Disponibles
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="TablaProductosDisponibles">
                            <thead class="table-primary">
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="seleccionar_todos" class="form-check-input">
                                    </th>
                                    <th>Producto</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th width="15%">Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="CuerpoTablaProductos">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando</span>
                                        </div>
                                        <p class="mt-2">Cargando productos disponibles</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen del Carrito -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #ffc107;">
                <div class="card-header bg-warning text-dark">
                    <h4 class="text-center mb-0">
                        <i class="bi bi-calculator me-2"></i>Resumen de Compra
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Productos Seleccionados: <span id="total_productos" class="badge bg-primary">0</span></h5>
                            <div id="lista_productos_seleccionados">
                                <p class="text-muted">No hay productos seleccionados</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Subtotal:</strong></td>
                                            <td class="text-end">Q<span id="subtotal_display">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>IVA (12%):</strong></td>
                                            <td class="text-end">Q<span id="impuestos_display">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Descuento:</strong></td>
                                            <td class="text-end">Q<span id="descuento_display">0.00</span></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><h5><strong>TOTAL:</strong></h5></td>
                                            <td class="text-end"><h5><strong>Q<span id="total_display">0.00</span></strong></h5></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row justify-content-center mt-4">
                        <div class="col-auto">
                            <button class="btn btn-success btn-lg" type="button" id="BtnGuardarFactura" disabled>
                                <i class="bi bi-save me-2"></i>Procesar Factura
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-secondary btn-lg" type="button" id="BtnLimpiarCarrito">
                                <i class="bi bi-arrow-clockwise me-2"></i>Limpiar Carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="<?= asset('build/js/carrito.js') ?>"></script>