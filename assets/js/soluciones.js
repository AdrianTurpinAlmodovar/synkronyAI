/**
 * soluciones.js — SynkronyAI
 * Ubicación: /assets/js/soluciones.js
 * Script específico para la página de soluciones sin errores
 */

document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    // --- 1. CONFIGURACIÓN Y VARIABLES GLOBALES ---
    const hasGSAP = typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined";
    const hasSwal = typeof Swal !== "undefined";

    // Helpers para seleccionar elementos más rápido
    const qs = (sel) => document.querySelector(sel);
    const qsa = (sel) => Array.from(document.querySelectorAll(sel));

    // --- 2. ANIMACIONES (GSAP) ESPECÍFICAS PARA SOLUCIONES ---
    if (hasGSAP) {
        gsap.registerPlugin(ScrollTrigger);

        // Animación de entrada del Hero de soluciones
        gsap.from(".hero-title", { y: 32, opacity: 0, duration: 0.9, ease: "power2.out" });
        gsap.from(".hero-subtitle", { y: 20, opacity: 0, duration: 0.9, delay: 0.15, ease: "power2.out" });

        // Animación de aparición de las tarjetas de soluciones (CORREGIDO)
        const solutionCards = document.querySelectorAll(".solution-card");
        solutionCards.forEach((card, index) => {
            gsap.from(card, {
                y: 36, opacity: 0, duration: 0.85, delay: index * 0.1,
                scrollTrigger: {
                    trigger: card,
                    start: "top 92%",
                    toggleActions: "play none none reverse"
                }
            });
        });

        // Animación de estadísticas
        gsap.from(".stat-card", {
            y: 20, opacity: 0, duration: 0.8, delay: 0.3,
            scrollTrigger: {
                trigger: ".stats-section",
                start: "top 85%",
                toggleActions: "play none none reverse"
            }
        });
    }

// --- 3. FILTROS DE CATEGORÍAS ---
    const categoryBtns = qsa(".category-btn");
    const solutionCards = qsa(".solution-card");

    if (categoryBtns.length > 0 && solutionCards.length > 0) {
        categoryBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault(); // <-- CRÍTICO: Evita que la página salte o se recargue al hacer clic
                
                const category = btn.getAttribute('data-category');
                
                // Actualizar estado visual de los botones
                categoryBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Ejecutar filtro
                filterSolutions(category);
            });
        });
    }

    const filterSolutions = (category) => {
        solutionCards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            
            if (category === 'all' || cardCategory === category) {
                card.style.display = 'block';
                
                // Animación de entrada fluida
                if (hasGSAP) {
                    gsap.killTweensOf(card); // <-- Detiene animaciones previas para evitar bloqueos visuales
                    gsap.fromTo(card, 
                        { opacity: 0, y: 20 }, 
                        { opacity: 1, y: 0, duration: 0.4, ease: "power2.out" }
                    );
                }
            } else {
                card.style.display = 'none';
            }
        });
    };



    // --- 5. CORRECCIÓN DE RUTAS DE IMÁGENES ---
    if (openButtons.length > 0) {
        openButtons.forEach(button => {
            const imageUrl = button.getAttribute('data-image');
            if (imageUrl && !imageUrl.startsWith('http') && !imageUrl.startsWith('../')) {
                // Corregir ruta relativa para que funcione desde /soluciones/
                button.setAttribute('data-image', '../' + imageUrl);
            }
        });
    }

    // --- 6. EVENT LISTENERS ---
    // Asignar el evento 'click' a todos los botones de video
    if (openButtons.length > 0) {
        openButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const videoUrl = btn.getAttribute('data-video');
                openVideo(videoUrl, e);
            });
        });
    }

    console.log('Soluciones.js cargado correctamente');
});
