document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("container");
    const registerBtn = document.getElementById("register");
    const loginBtn = document.getElementById("login");

    // Manejar el cambio entre Login y Registro
    if (registerBtn && loginBtn) {
        registerBtn.addEventListener("click", () => {
            container.classList.add("active");
        });

        loginBtn.addEventListener("click", () => {
            container.classList.remove("active");
        });
    }

    // Validación del Formulario de Iniciar Sesión
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(loginForm);

            fetch(loginForm.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            })
                .then((response) => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.text();
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Error al procesar la solicitud");
                });
        });
    }

    // Validación del Formulario de Registro
    document
        .getElementById("registroForm")
        .addEventListener("submit", function (e) {
            e.preventDefault();

            // Validar que las contraseñas coincidan
            const password = document.querySelector(
                'input[name="password"]'
            ).value;
            const confirmPassword = document.querySelector(
                'input[name="password_confirmation"]'
            ).value;

            if (password !== confirmPassword) {
                alert("Las contraseñas no coinciden");
                return;
            }

            // Enviar el formulario
            this.submit();
        });

    // Código para alternar entre formularios en diseño móvil
    const loginFormContainer = container.querySelector(".sign-in");
    const registerFormContainer = container.querySelector(".sign-up");

    // Crear botones para alternar en vista móvil
    const mobileButtons = document.createElement("div");
    mobileButtons.classList.add("mobile-buttons");

    const loginToggleButton = document.createElement("button");
    loginToggleButton.textContent = "Iniciar Sesión";
    loginToggleButton.classList.add("active");

    const registerToggleButton = document.createElement("button");
    registerToggleButton.textContent = "Crear Cuenta";

    mobileButtons.appendChild(loginToggleButton);
    mobileButtons.appendChild(registerToggleButton);
    container.insertBefore(mobileButtons, loginFormContainer);

    console.log("Mobile buttons added to the DOM."); // Mensaje para verificar en consola

    // Funciones para mostrar el formulario correspondiente
    function showLogin() {
        loginFormContainer.classList.add("active");
        registerFormContainer.classList.remove("active");
        loginToggleButton.classList.add("active");
        registerToggleButton.classList.remove("active");
        mobileButtons.style.display = "flex"; // Asegura que los botones estén visibles
    }

    function showRegister() {
        registerFormContainer.classList.add("active");
        loginFormContainer.classList.remove("active");
        registerToggleButton.classList.add("active");
        loginToggleButton.classList.remove("active");
        mobileButtons.style.display = "flex"; // Asegura que los botones estén visibles
    }

    // Eventos de clic para alternar entre formularios
    loginToggleButton.addEventListener("click", showLogin);
    registerToggleButton.addEventListener("click", showRegister);

    // Inicializar mostrando el formulario de inicio de sesión
    showLogin();
});
