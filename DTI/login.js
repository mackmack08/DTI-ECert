function togglePassword() {
    const passwordInput = document.getElementById('floatingPassword');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const carouselInner = document.querySelector(".carousel-inner");
    const items = document.querySelectorAll(".carousel-item");
    
    let index = 0;
    
    function nextSlide() {
        index++;
        if (index >= items.length) {
            index = 0; // Reset to first slide
        }
        let newTransformValue = -index * 100 + "%";
        carouselInner.style.transform = "translateY(" + newTransformValue + ")";
    }

    // Auto-scroll every 3 seconds
    setInterval(nextSlide, 3000);
});

