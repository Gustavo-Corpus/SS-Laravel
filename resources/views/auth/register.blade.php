<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2>Crear Cuenta</h2>
                            <p class="text-muted">o usa tu correo para registrarte</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <input type="text" class="form-control" name="username" placeholder="Nombre de usuario" required>
                            </div>

                            <div class="mb-3">
                                <input type="text" class="form-control" name="nombre" placeholder="Nombre" required>
                            </div>

                            <div class="mb-3">
                                <input type="text" class="form-control" name="apellido" placeholder="Apellido" required>
                            </div>

                            <div class="mb-3">
                                <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                            </div>

                            <div class="mb-4">
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Confirmar Contraseña" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">REGISTRARSE</button>
                        </form>

                        <div class="text-center mt-4">
                            <div class="d-flex justify-content-center gap-2 mb-3">
                                <a href="#" class="btn btn-outline-secondary"><i class="bi bi-google"></i></a>
                                <a href="#" class="btn btn-outline-secondary"><i class="bi bi-facebook"></i></a>
                                <a href="#" class="btn btn-outline-secondary"><i class="bi bi-github"></i></a>
                                <a href="#" class="btn btn-outline-secondary"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
