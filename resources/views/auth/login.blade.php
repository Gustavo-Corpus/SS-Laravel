<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form method="POST" action="{{ route('register') }}" id="registroForm">
                @csrf
                <h1>Crear Cuenta</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>o usa tu correo para registrarte</span>
                <input type="text" name="username" placeholder="Nombre de usuario" required>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellido" placeholder="Apellido" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>
                <button type="submit">Registrarse</button>
            </form>
        </div>

        <div class="form-container sign-in">
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                @if($errors->any())
                    <div class="alert alert-danger" style="color: red; margin-bottom: 10px;">
                        {{ $errors->first() }}
                    </div>
                @endif
                <h1>Iniciar Sesión</h1>
                <div class="social-icons">
                    <a href="#" class="icon"
                    ><i class="fa-brands fa-google-plus-g"></i
                    ></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"
                    ><i class="fa-brands fa-linkedin-in"></i
                    ></a>
                </div>
                <span>o usa tu correo y contraseña</span>
                <input type="text" name="username" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <div class="mb-3">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#olvidoPasswordModal">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
                <button type="submit">Ingresar</button>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>¡Bienvenido de nuevo!</h1>
                    <p>Para mantenerte conectado, por favor inicia sesión con tu información personal</p>
                    <button class="hidden" id="login">Iniciar Sesión</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>¡Hola, Amigo!</h1>
                    <p>Ingresa tus datos personales y empieza tu aventura con nosotros</p>
                    <button class="hidden" id="register">Registrarse</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="olvidoPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="cambiarPasswordForm">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username_reset" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="cambiarPassword()">Cambiar Contraseña</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/login.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function cambiarPassword() {
            const formData = new FormData(document.getElementById('cambiarPasswordForm'));

            $.ajax({
                url: '{{ url("/cambiar-password") }}',  // Usa el helper url() de Laravel
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#olvidoPasswordModal').modal('hide');
                    alert('Contraseña actualizada correctamente');
                    $('#cambiarPasswordForm')[0].reset();
                },
                error: function(xhr) {
                    alert('Error al actualizar la contraseña: ' + (xhr.responseJSON?.message || 'Error desconocido'));
                }
            });
        }
    </script>
    </body>
</html>
