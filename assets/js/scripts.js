/**
 * scripts.js — SynkronyAI
 * Ubicación: /assets/js/scripts.js
 * Gestión unificada de interfaz: Menú, Animaciones, Modal de Video y Alertas.
 */

document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    // --- 1. CONFIGURACIÓN Y VARIABLES GLOBALES ---
    const hasGSAP = typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined";
    const hasSwal = typeof Swal !== "undefined";

    // Helpers para seleccionar elementos más rápido
    const qs = (sel) => document.querySelector(sel);
    const qsa = (sel) => Array.from(document.querySelectorAll(sel));

    /* --------------------------
       2. MENÚ DE NAVEGACIÓN (Móvil) - LEGADO
    --------------------------- */
    const menuToggle = qs("#menu-toggle");
    const mainNav = qs("#main-nav");

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', () => {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            menuToggle.classList.toggle('open');
            mainNav.classList.toggle('open');
        });

        // Cerrar menú al hacer clic en enlaces para mejorar la experiencia en móvil
        mainNav.querySelectorAll('.nav-link, .button-header-cta').forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.classList.remove('open');
                mainNav.classList.remove('open');
            });
        });
    }

    /* --------------------------
       3. ANIMACIONES (GSAP)
    --------------------------- */
    if (hasGSAP) {
        gsap.registerPlugin(ScrollTrigger);

        // Animación de entrada del Hero (Texto y Botones)
        gsap.from(".hero h1", { y: 32, opacity: 0, duration: 0.9, ease: "power2.out" });
        gsap.from(".hero p, .hero .cta-row", { 
            y: 20, opacity: 0, duration: 0.9, delay: 0.15, stagger: 0.12, ease: "power2.out" 
        });

        // Animación de entrada del Hero de soluciones
        gsap.from(".hero-title", { y: 32, opacity: 0, duration: 0.9, ease: "power2.out" });
        gsap.from(".hero-subtitle", { y: 20, opacity: 0, duration: 0.9, delay: 0.15, ease: "power2.out" });

        // Animación de aparición de las tarjetas de soluciones
        gsap.from(".solution-card", {
            y: 36, opacity: 0, duration: 0.85, delay: i * 0.1,
            scrollTrigger: {
                trigger: ".solution-card",
                start: "top 92%", // Empieza cuando la tarjeta entra por abajo
                toggleActions: "play none none reverse"
            }
        });

        // Animación de aparición de las tarjetas al hacer scroll
        gsap.from(".card", {
            y: 36, opacity: 0, duration: 0.85, delay: i * 0.1,
            scrollTrigger: {
                trigger: ".card",
                start: "top 92%", // Empieza cuando la tarjeta entra por abajo
                toggleActions: "play none none reverse"
            }
        });

        // Efecto Parallax suave en el placeholder del video del Hero
        gsap.to(".hero-card", {
            yPercent: -8, ease: "none",
            scrollTrigger: { trigger: ".hero", scrub: 1 }
        });
    }

    /* --------------------------
       3.5 FILTROS DE CATEGORÍAS
    --------------------------- */
    const categoryBtns = qsa(".category-btn");
    const solutionCards = qsa(".solution-card");

    if (categoryBtns.length > 0 && solutionCards.length > 0) {
        categoryBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const category = btn.getAttribute('data-category');
                
                // Actualizar botón activo
                categoryBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Filtrar tarjetas
                filterSolutions(category);
            });
        });
    }

    const filterSolutions = (category) => {
        solutionCards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            
            if (category === 'all' || cardCategory === category) {
                card.style.display = 'block';
                // Animar entrada
                gsap.fromTo(card, { 
                    opacity: 0, y: 20, 
                    duration: 0.3, 
                    ease: "power2.out" 
                }, {
                    opacity: 1, y: 0,
                    delay: Math.random() * 0.2
                });
            } else {
                card.style.display = 'none';
            }
        });
    };

    /* --------------------------
       5. GESTIÓN DE ALERTAS (URL Parameters)
    --------------------------- */
    if (hasSwal) {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('status')) {
            const status = urlParams.get('status');
            const msg = urlParams.get('msg');
            const toastConfig = { 
                confirmButtonColor: '#9F40FF', 
                background: '#14141d', 
                color: '#fff' 
            };

            if (status === 'demo_success') {
                Swal.fire({ ...toastConfig, title: '¡Recibido!', text: 'Solicitud enviada correctamente.', icon: 'success' });
            } 
            else if (status === 'error' && msg === 'already_requested') {
                Swal.fire({ ...toastConfig, title: 'Atención', text: 'Ya has solicitado una demo con este correo.', icon: 'warning' });
            }
            else if (status === 'registered') {
                Swal.fire({ ...toastConfig, title: '¡Éxito!', text: 'Registro completado. Ya puedes iniciar sesión.', icon: 'success' });
            }

            // Limpiar la URL para que la alerta no salga al refrescar (F5)
            if (window.history.replaceState) {
                const url = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({path:url}, '', url);
            }
        }
    }
});