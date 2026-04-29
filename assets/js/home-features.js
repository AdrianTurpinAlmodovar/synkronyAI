// assets/js/home-features.js

document.addEventListener('DOMContentLoaded', function() {

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