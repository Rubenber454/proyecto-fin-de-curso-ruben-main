function validarFormulario(event) {
    event.preventDefault(); // Evita el envío tradicional del formulario

    // Limpiar mensajes de error
    document.getElementById('error-nombre').textContent = "";
    document.getElementById('error-email').textContent = "";
    document.getElementById('error-mensaje').textContent = "";

    // Obtener los valores de los campos
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const mensaje = document.getElementById('mensaje').value.trim();

    let isValid = true;

    // Validación del nombre (solo letras, no vacío)
    const nombreRegex = /^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]+$/;
    if (nombre === "") {
        document.getElementById('error-nombre').textContent = "El nombre es obligatorio.";
        isValid = false;
    } else if (!nombreRegex.test(nombre)) {
        document.getElementById('error-nombre').textContent = "El nombre solo puede contener letras y espacios.";
        isValid = false;
    }

    // Validación del email (formato válido)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === "") {
        document.getElementById('error-email').textContent = "El correo electrónico es obligatorio.";
        isValid = false;
    } else if (!emailRegex.test(email)) {
        document.getElementById('error-email').textContent = "Por favor, ingresa un correo electrónico válido.";
        isValid = false;
    }

    // Validación del mensaje (mínimo 10 caracteres y máximo 500 caracteres)
    if (mensaje === "") {
        document.getElementById('error-mensaje').textContent = "El mensaje es obligatorio.";
        isValid = false;
    } else if (mensaje.length < 10) {
        document.getElementById('error-mensaje').textContent = "El mensaje debe tener al menos 10 caracteres.";
        isValid = false;
    } else if (mensaje.length > 500) {
        document.getElementById('error-mensaje').textContent = "El mensaje no puede tener más de 500 caracteres.";
        isValid = false;
    }

    // Si la validación falla, no enviar el formulario
    if (!isValid) {
        return false;
    }

    // Si todas las validaciones pasan, enviamos el formulario usando fetch
    const form = document.getElementById('contact-form');
    const formData = new FormData(form);

    fetch('procesar_contacto.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const mensajeResultado = document.getElementById('mensaje-resultado');
        mensajeResultado.textContent = data.message;
        mensajeResultado.style.color = data.success ? 'green' : 'red';

        // Limpiar el formulario
        form.reset(); // Esto restablece todos los campos del formulario
        // También puedes limpiar el mensaje de resultado después de un tiempo, si deseas
        setTimeout(() => {
            mensajeResultado.textContent = ""; // Limpiar el mensaje después de 3 segundos
        }, 3000);
    })
    .catch(error => {
        console.error('Error:', error);
        const mensajeResultado = document.getElementById('mensaje-resultado');
        mensajeResultado.textContent = 'Error al enviar el formulario.';
        mensajeResultado.style.color = 'red';
    });

    return false; // Evitar el envío tradicional del formulario
}
