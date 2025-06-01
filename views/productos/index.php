<div class="container mt-4">

<!--formulario de creacion -->
|   <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
                <div class="card-header">
                    <h4 class="text-center mb-0">
                    Gestión de Productos
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form id="FormProductos">
                        <input type="hidden" id="producto_id" name="id">
                        
                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="producto_nombre" class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" id="producto_nombre" name="nombre" 
                                       placeholder="Ingrese el nombre del producto">
                            </div>
                            <div class="col-lg-6">
                                <label for="producto_precio" class="form-label">Precio</label>
                                <input type="number" class="form-control" id="producto_precio" name="precio" placeholder="Q.">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="producto_stock" class="form-label">Stock Disponible</label>
                                <input type="number" class="form-control" id="producto_stock" name="stock_disponible" 
                                       placeholder="0">
                            </div>
                            <div class="col-lg-6">
                                <label for="producto_stock_minimo" class="form-label">Stock Mínimo</label>
                                <input type="number" class="form-control" id="producto_stock_minimo" name="stock_minimo" 
                                       placeholder="5">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="producto_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="producto_descripcion" name="descripcion" 
                                          rows="3" placeholder="Descripción del producto"></textarea>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Guardar
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil me-1"></i>Modificar
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--vista dde los productos -->
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">
                        Carrito de Compras
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive w-100">
                        <table class="table table-striped table-hover" id="TablaProductos">
                            <thead class="table-active">
                                <tr>
                                    <th>No.</th>
                                    <th>Producto</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="CuerpoTablaProductos">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando</span>
                                        </div>
                                        <p class="mt-2">Buscando productos</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="<?= asset('build/js/productos.js') ?>"></script>