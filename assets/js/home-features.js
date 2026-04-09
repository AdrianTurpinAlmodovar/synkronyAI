// assets/js/home-features.js

document.addEventListener('DOMContentLoaded', function() {

    // 1. Inicialización del Carrusel Continuo (Swiper)
    if(typeof Swiper !== 'undefined') {
        var swiper = new Swiper(".servicesSwiper", {
            slidesPerView: 'auto',
            spaceBetween: 30,
            loop: true,
            freeMode: true,
            grabCursor: true,
            speed: 7000,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            }
        });
    }

    // 2. Efecto Spotlight (Mouse Tracker) para el Bento Grid
    const bentoCards = document.querySelectorAll('.bento-card');
    if (window.matchMedia("(pointer: fine)").matches) {
        document.getElementById('bento-grid').addEventListener('mousemove', function(e) {
            bentoCards.forEach(card => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });
    }

    // 3. Calculadora de ROI (Lógica Matemática)
    const hoursSlider = document.getElementById('hours-slider');
    const rateSlider = document.getElementById('rate-slider');

    const hoursVal = document.getElementById('hours-val');
    const rateVal = document.getElementById('rate-val');
    const costVal = document.getElementById('cost-val');
    const savingsVal = document.getElementById('savings-val');

    function calculateROI() {
        if(!hoursSlider) return;

        const hours = parseInt(hoursSlider.value);
        const rate = parseInt(rateSlider.value);

        hoursVal.textContent = hours;
        rateVal.textContent = rate;

        const annualCost = hours * 52 * rate;
        const potentialSavings = annualCost * 0.80;

        costVal.textContent = new Intl.NumberFormat('es-ES').format(annualCost);
        savingsVal.textContent = new Intl.NumberFormat('es-ES').format(potentialSavings);
    }

    if(hoursSlider && rateSlider) {
        hoursSlider.addEventListener('input', calculateROI);
        rateSlider.addEventListener('input', calculateROI);
        calculateROI();
    }

    // 4. Lógica del Acordeón para Preguntas Frecuentes (FAQ)
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const questionBtn = item.querySelector('.faq-question');
        const answerDiv = item.querySelector('.faq-answer');

        questionBtn.addEventListener('click', () => {
            const isActive = item.classList.contains('active');

            faqItems.forEach(otherItem => {
                otherItem.classList.remove('active');
                otherItem.querySelector('.faq-answer').style.maxHeight = null;
            });

            if (!isActive) {
                item.classList.add('active');
                answerDiv.style.maxHeight = answerDiv.scrollHeight + "px";
            }
        });
    });

    // 5. Lógica del Botón Flotante (Scroll Listener)
    const floatingCta = document.getElementById('floating-cta');
    const agendaSection = document.getElementById('agenda');

    if(floatingCta) {
        window.addEventListener('scroll', () => {
            // Aparecer a partir de 400px de scroll (Pasado el Hero)
            if (window.scrollY > 400) {
                floatingCta.classList.add('visible');
            } else {
                floatingCta.classList.remove('visible');
            }

            // Desaparecer si llega a la sección del formulario final
            if(agendaSection) {
                const agendaRect = agendaSection.getBoundingClientRect();
                // Si la sección agenda entra en la pantalla, ocultamos el botón flotante
                if(agendaRect.top < window.innerHeight - 100) {
                    floatingCta.classList.remove('visible');
                }
            }
        });
    }

});