
    document.addEventListener('DOMContentLoaded', (event) => {
        const carrito = document.querySelector('.carrito-flotante');
        let isDragging = false;
        let offsetX, offsetY;

        carrito.addEventListener('mousedown', function (e) {
            isDragging = true;
            offsetX = e.clientX - carrito.getBoundingClientRect().left;
            offsetY = e.clientY - carrito.getBoundingClientRect().top;
            carrito.classList.add('moving');
        });

        document.addEventListener('mousemove', function (e) {
            if (isDragging) {
                carrito.style.left = (e.clientX - offsetX) + 'px';
                carrito.style.top = (e.clientY - offsetY) + 'px';
            }
        });

        document.addEventListener('mouseup', function () {
            isDragging = false;
            carrito.classList.remove('moving');
        });
    });
    
