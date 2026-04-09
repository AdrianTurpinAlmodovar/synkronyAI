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

    // --- 4. MODAL DE VÍDEO E IMAGEN ---
    const videoModal = qs("#videoModal");
    const videoIframe = qs("#videoIframe");
    const closeBtn = qs("#closeVideo");
    const openButtons = qsa(".btn-open-video");

    // Variables para modal de imagen
    const imageModal = qs("#imageModal");
    const modalImage = qs("#modalImage");
    const modalDescription = qs("#modalDescription");
    const closeImageBtn = qs("#closeImage");

    // Función para ABRIR el modal con el video cargado
    const openVideo = (url, event = null) => {
        // Obtener datos adicionales de los atributos del botón
        const button = event?.currentTarget || event?.target;
        const imageUrl = button?.getAttribute('data-image') || '';
        const description = button?.getAttribute('data-description') || '';
        
        // Priorizar imagen si existe
        if (imageUrl && imageUrl.trim() !== '') {
            showImageModal(imageUrl, description);
            return;
        }
        
        // Mostrar video si existe
        if (url && url.trim() !== "") {
            showVideoModal(url);
            return;
        }
        
        // Mostrar mensaje "próximamente" si no hay nada
        showComingSoonMessage();
    };

    // Función para mostrar imagen y descripción en modal
    const showImageModal = (imageUrl, description) => {
        modalImage.src = imageUrl;
        modalDescription.textContent = description;
        imageModal.style.display = "flex";
        document.body.style.overflow = "hidden";
    };

    // Función para cerrar modal de imagen
    const closeImageModal = () => {
        imageModal.style.display = "none";
        document.body.style.overflow = "auto";
        modalImage.src = "";
        modalDescription.textContent = "";
    };

    // Función para mostrar mensaje "próximamente"
    const showComingSoonMessage = () => {
        if (hasSwal) {
            Swal.fire({
                title: 'Próximamente',
                text: 'Estamos terminando de grabar el vídeo de esta demo. ¡Vuelve pronto!',
                icon: 'info',
                background: '#14141d',
                color: '#fff',
                confirmButtonColor: '#9F40FF'
            });
        } else {
            alert("Demo no disponible por el momento.");
        }
    };

    // Función para mostrar video en modal
    const showVideoModal = (url) => {
        if (videoIframe) {
            videoIframe.style.display = "block";
            
            // Añadimos autoplay a la URL si es compatible (YouTube/Vimeo)
            const separator = url.includes('?') ? '&' : '?';
            videoIframe.src = url + separator + "autoplay=1";
        }
        
        if (videoModal) {
            videoModal.style.display = "flex";
            document.body.style.overflow = "hidden";
        }
    };

    // Función para CERRAR el modal y detener el video
    const closeVideo = () => {
        if (videoModal) {
            videoModal.style.display = "none";
            document.body.style.overflow = "auto";
        }
        if (videoIframe) {
            videoIframe.src = "";
            videoIframe.style.display = "block";
        }
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

    // Cerrar con el botón X del modal
    if (closeBtn) closeBtn.addEventListener('click', closeVideo);
    if (closeImageBtn) closeImageBtn.addEventListener('click', closeImageModal);
    
    // Cerrar si el usuario hace clic en el fondo oscuro (fuera del modal)
    window.addEventListener('click', (ev) => {
        if (ev.target === videoModal) closeVideo();
        if (ev.target === imageModal) closeImageModal();
    });

    // Cerrar con la tecla Escape
    document.addEventListener('keydown', (ev) => {
        if (ev.key === "Escape") {
            if (videoModal && videoModal.style.display === "flex") {
                closeVideo();
            }
            if (imageModal && imageModal.style.display === "flex") {
                closeImageModal();
            }
        }
    });

    console.log('Soluciones.js cargado correctamente');
});
