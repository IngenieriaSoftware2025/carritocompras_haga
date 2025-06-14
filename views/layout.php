<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>Carrito de Compras</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark  bg-dark">

        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="/carritocompras/">
                <img src="<?= asset('./images/cit.png') ?>" width="35px'" alt="cit">
                Carrito de Compras
            </a>
            <div class="collapse navbar-collapse" id="navbarToggler">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="margin: 0;">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/carritocompras/"><i class="bi bi-house-fill me-2"></i>Inicio</a>
                    </li>

                    <!-- AGREGAR ESTE NUEVO ITEM -->
                    <li class="nav-item">
                        <a class="nav-link" href="/carritocompras/productos"><i class="bi bi-box me-2"></i>Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/carritocompras/clientes"><i class="bi bi-people me-2"></i>Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/carritocompras/carrito"><i class="bi bi-cart me-2"></i>Carrito</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/carritocompras/facturas"><i class="bi bi-receipt me-2"></i>Facturas</a>
                    </li>

                    <div class="nav-item dropdown ">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>Más opciones
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-dark " id="dropwdownRevision" style="margin: 0;">
                            <li>
                                <a class="dropdown-item nav-link text-white " href="/carritocompras/productos"><i class="ms-lg-0 ms-2 bi bi-box me-2"></i>Productos</a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-link text-white " href="/carritocompras/clientes"><i class="ms-lg-0 ms-2 bi bi-people me-2"></i>Clientes</a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/carritocompras/carrito"><i class="ms-lg-0 ms-2 bi bi-cart me-2"></i>Carrito de Compras</a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-link text-white " href="/carritocompras/facturas"><i class="ms-lg-0 ms-2 bi bi-receipt me-2"></i>Facturas</a>
                            </li>
                        </ul>
                    </div>

                </ul>
                <div class="col-lg-1 d-grid mb-lg-0 mb-2">
                    <!-- Ruta relativa desde el archivo donde se incluye menu.php -->
                    <a href="/menu/" class="btn btn-danger"><i class="bi bi-arrow-bar-left"></i>MENÚ</a>
                </div>


            </div>
        </div>

    </nav>
    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">

        <?php echo $contenido; ?>
    </div>
    <div class="container-fluid ">
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:xx-small; font-weight: bold;">
                    Comando de Informática y Tecnología, <?= date('Y') ?> &copy;
                </p>
            </div>
        </div>
    </div>
</body>

</html>