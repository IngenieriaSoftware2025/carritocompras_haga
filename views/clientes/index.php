<div class="container mt-4">

    <!--formulario de creacion -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
                <div class="card-header">
                    <h4 class="text-center mb-0">
                        Gestión de Clientes
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form id="FormClientes">
                        <input type="hidden" id="cliente_id" name="id">
                        
                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="cliente_nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="cliente_nombre" name="nombre" 
                                       placeholder="Ingrese el nombre del cliente">
                            </div>
                            <div class="col-lg-6">
                                <label for="cliente_apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="cliente_apellido" name="apellido" 
                                       placeholder="Ingrese el apellido del cliente">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="cliente_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="cliente_email" name="email" 
                                       placeholder="Ingrese el correo del cliente">
                            </div>
                            <div class="col-lg-6">
                                <label for="cliente_telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="cliente_telefono" name="telefono" 
                                       placeholder="Ingrese el numero de celular">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="cliente_nit" class="form-label">NIT</label>
                                <input type="text" class="form-control" id="cliente_nit" name="nit" 
                                       placeholder="Numero de nit">
                            </div>
                            <div class="col-lg-6">
                                <label for="cliente_direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="cliente_direccion" name="direccion" 
                                       placeholder="Ciudad">
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

    <!--vista de los clientes -->
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">
                        Lista de Clientes
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive w-100">
                        <table class="table table-striped table-hover" id="TablaClientes">
                            <thead class="table-active w-100">
                                <tr>
                                    <th>No.</th>
                                    <th>Nombre Completo</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>NIT</th>
                                    <th>Dirección</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="CuerpoTablaClientes">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando</span>
                                        </div>
                                        <p class="mt-2">Buscando clientes</p>
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

<script src="<?= asset('build/js/clientes.js') ?>"></script>