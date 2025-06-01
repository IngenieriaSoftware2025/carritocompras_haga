<div class="container mt-4">

    <!-- Información de la Factura -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #dc3545;">
                <div class="card-header bg-danger text-white">
                    <h4 class="text-center mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Modificar Factura
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información de Factura</h5>
                            <p><strong>Número:</strong> <span id="numero_factura_display">-</span></p>
                            <p><strong>Fecha:</strong> <span id="fecha_factura_display">-</span></p>
                            <p><strong>Estado:</strong> <span id="estado_factura_display" class="badge bg-info">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Cliente</h5>
                            <p><strong>Nombre:</strong> <span id="cliente_nombre_display">-</span></p>
                            <p><strong>NIT:</strong> <span id="cliente_nit_display">-</span></p>
                            <p><strong>Email:</strong> <span id="cliente_email_display">-</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Modificación -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #ffc107;">
                <div class="card-header bg-warning text-dark">
                    <h4 class="text-center mb-0">
                        <i class="bi bi-cart-check me-2"></i>Productos de la Factura
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form id="FormModificarFactura">
                        <input type="hidden" id="modificar_id_factura" name="id_factura">
                        <input type="hidden" id="modificar_descuento" name="descuento" value="0">
                        <input type="hidden" id="modificar_observaciones" name="observaciones" value="">
                        
                        <!-- Productos Actuales -->
                        <div class="mb-4">
                            <h5 class="mb-3">Productos Actuales</h5>
                            <div class="table-responsive">
                                <table class="table table-striped" id="TablaProductosActuales">
                                    <thead class="table-warning">
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="seleccionar_todos_actuales" class="form-check-input">
                                            </th>
                                            <th>Producto</th>
                                            <th>Precio Unitario</th>
                                            <th>Stock Disponible</th>
                                            <th width="15%">Cantidad</th>
                                            <th>Subtotal</th>
                                            <th width="10%">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="CuerpoTablaProductosActuales">
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="spinner-border text-warning" role="status">
                                                    <span class="visually-hidden">Cargando</span>
                                                </div>
                                                <p class="mt-2">Cargando productos de la factura</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Agregar Nuevos Productos -->
                        <div class="mb-4">
                            <h5 class="mb-3">Agregar Productos Adicionales</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nuevo_producto_select" class="form-label">Seleccionar Producto</label>
                                    <select class="form-control" id="nuevo_producto_select">
                                        <option value="">Seleccione un producto para agregar</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="nuevo_producto_cantidad" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="nuevo_producto_cantidad" min="1" value="1">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-success w-100" id="BtnAgregarProducto">
                                        <i class="bi bi-plus-circle me-1"></i>Agregar
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Productos Agregados -->
                            <div id="productos_agregados_container" style="display: none;">
                                <h6>Productos Agregados:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-success">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla_productos_agregados">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Modificación -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #198754;">
                <div class="card-header bg-success text-white">
                    <h4 class="text-center mb-0">
                        <i class="bi bi-calculator me-2"></i>Resumen de Modificación
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Productos Finales: <span id="total_productos_modificar" class="badge bg-primary">0</span></h5>
                            <div id="lista_productos_finales">
                                <p class="text-muted">Cargando resumen de productos</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Subtotal:</strong></td>
                                            <td class="text-end">Q<span id="subtotal_modificar_display">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>IVA (12%):</strong></td>
                                            <td class="text-end">Q<span id="impuestos_modificar_display">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Descuento:</strong></td>
                                            <td class="text-end">Q<span id="descuento_modificar_display">0.00</span></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><h5><strong>TOTAL:</strong></h5></td>
                                            <td class="text-end"><h5><strong>Q<span id="total_modificar_display">0.00</span></strong></h5></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row justify-content-center mt-4">
                        <div class="col-auto">
                            <button class="btn btn-success btn-lg" type="button" id="BtnGuardarModificacion" disabled>
                                <i class="bi bi-save me-2"></i>Guardar Modificación
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-secondary btn-lg" type="button" id="BtnCancelarModificacion">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="/carritocompras/carrito" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Volver al Carrito
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="<?= asset('build/js/modificar_factura.js') ?>"></script>