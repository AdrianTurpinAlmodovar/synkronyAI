<?php
// includes/home_sections.php - Sección de soluciones preview
$sql_random = "SELECT * FROM services ORDER BY RAND() LIMIT 3";
$result_random = $conn->query($sql_random);

// Función generateSlug (si no está definida)
if (!function_exists('generateSlug')) {
    function generateSlug($title) {
        $slug = strtolower($title);
        $slug = str_replace(
            ['á', 'à', 'ä', 'â', 'ã', 'å', 'é', 'è', 'ë', 'ê', 'í', 'ì', 'ï', 'î', 
             'ó', 'ò', 'ö', 'ô', 'õ', 'ø', 'ú', 'ù', 'ü', 'û', 'ñ', 'ç', 'ý', 'ÿ'],
            ['a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 
             'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'n', 'c', 'y', 'y'],
            $slug
        );
        $slug = preg_replace('/[^a-zA-Z0-9]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
}
?>

<!-- PREVIEW DE SOLUCIONES - 3 TARJETAS ALEATORIAS -->
<section class="solutions-preview-section">
    <div class="section-header container">
        <h2>Nuestras Soluciones Base</h2>
        <p>Descubre nuestras herramientas más populares para optimizar tu negocio</p>
    </div>
    
    <div class="solutions-container container">
        <div class="solutions-grid">
            <?php if ($result_random && $result_random->num_rows > 0): ?>
                <?php while($service = $result_random->fetch_assoc()): ?>
                    <div class="solution-card" data-category="<?php echo htmlspecialchars($service['categoria'] ?? 'all'); ?>">
                        <div class="solution-visual">
                            <?php if (!empty($service['image_url'])): ?>
                                <?php 
                                // Corregir ruta para que funcione desde el home
                                $image_url = $service['image_url'];
                                if (!str_starts_with($image_url, 'http') && !str_starts_with($image_url, 'assets/')) {
                                    $image_url = 'assets/' . $image_url;
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($image_url); ?>" 
                                     alt="<?php echo htmlspecialchars($service['title']); ?>" 
                                     class="solution-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="solution-placeholder" style="display:none; width:100%; height:100%; background:linear-gradient(135deg, #1a1a2e, #2d2d44); display:flex; align-items:center; justify-content:center; color:#9F40FF; font-size:3rem;">
                                    🖼️
                                </div>
                            <?php else: ?>
                                <div class="solution-placeholder" style="width:100%; height:100%; background:linear-gradient(135deg, #1a1a2e, #2d2d44); display:flex; align-items:center; justify-content:center; color:#9F40FF; font-size:3rem;">
                                    🖼️
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="solution-content">
                            <div class="solution-category"><?php echo htmlspecialchars($service['categoria'] ?? 'Solución'); ?></div>
                            <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                            <p class="solution-description"><?php echo htmlspecialchars($service['description']); ?></p>
                            
                            <div class="solution-features">
                                <span class="feature-tag">Automatización</span>
                                <span class="feature-tag">IA</span>
                            </div>
                            
                            <button class="solution-cta" 
                                    onclick="window.location.href='/soluciones/<?php echo generateSlug($service['title']); ?>'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                Ver detalles completos
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Fallback si no hay servicios -->
                <div class="no-solutions-message" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <p>Próximamente disponibles nuevas soluciones</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- CTA para ver todas las soluciones -->
    <div class="preview-cta-container text-center container" style="margin-top: 40px;">
        <a href="soluciones/" class="cta-primary">
            Ver más soluciones
        </a>
    </div>
</section>
<hr>
<!-- CASOS DE USO (BENTO GRID) -->
<section class="rich-section padding-y-lg">
    <div class="section-header">
        <h2>Reducción de tareas repetitivas</h2>
        <p>Implementación de sistemas para la gestión de datos, comunicación interna y atención al cliente.</p>
    </div>

    <div id="bento-grid" class="bento-grid">

        <!-- Tarjeta 1: WhatsApp (Grande) -->
        <div class="bento-card bento-large">
            <div class="bento-border-glow"></div>
            <div class="bento-inner">
                <div class="bento-content">
                    <span class="panel-tag bg-green-light text-green">Comunicación</span>
                    <h3 class="panel-title">Asistente de WhatsApp</h3>
                    <p class="panel-desc">Sistema de respuesta automática. Identifica consultas frecuentes y registra citas en el calendario corporativo sin intervención manual.</p>
                    <ul class="bento-features">
                        <li><span class="text-green">✓</span> Disponibilidad continua 24/7</li>
                        <li><span class="text-green">✓</span> Sincronización con calendarios</li>
                    </ul>
                </div>
                <div class="bento-visual">
                    <div class="chat-sim">
                        <div class="chat-msg left anim-msg-1">¿Tenéis disponibilidad hoy?</div>
                        <div class="chat-reply-wrapper">
                            <div class="chat-msg right typing anim-typing"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
                            <div class="chat-msg right resolved anim-msg-2">Sí, te reservo a las 17:00. 📅</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Publicación Multicanal -->
        <div class="bento-card">
            <div class="bento-border-glow"></div>
            <div class="bento-inner">
                <div class="bento-content">
                    <span class="panel-tag bg-blue-light text-blue">Distribución</span>
                    <h3 class="panel-title">Publicación Multicanal</h3>
                    <p class="panel-desc">Despliegue simultáneo de contenido corporativo. Adaptación de formatos y programación de publicaciones en redes desde una única base de datos.</p>
                </div>
                <div class="bento-visual centered">
                    <div class="social-sim">
                        <div class="s-source">📄</div>
                        <div class="s-routes">
                            <div class="s-route">
                                <div class="s-line"><div class="s-packet"></div></div>
                                <div class="s-platform"><img src="assets/img/telegram.png" alt="LinkedIn" onerror="this.style.display='none'"></div>
                            </div>
                            <div class="s-route">
                                <div class="s-line"><div class="s-packet"></div></div>
                                <div class="s-platform"><img src="assets/img/tiktok.png" alt="X Twitter" onerror="this.style.display='none'"></div>
                            </div>
                            <div class="s-route">
                                <div class="s-line"><div class="s-packet"></div></div>
                                <div class="s-platform"><img src="assets/img/insta.png" alt="Instagram" onerror="this.style.display='none'"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Clasificación de Correos -->
        <div class="bento-card">
            <div class="bento-border-glow"></div>
            <div class="bento-inner">
                <div class="bento-content">
                    <span class="panel-tag bg-purple-light text-purple">Soporte Técnico</span>
                    <h3 class="panel-title">Clasificación de Correos</h3>
                    <p class="panel-desc">Lectura de bandejas de entrada. Asignación de etiquetas según el contenido y derivación de mensajes al departamento correspondiente.</p>
                </div>
                <div class="bento-visual centered">
                    <div class="mail-sim">
                        <div class="icon-box mail-envelope">✉️</div>
                        <div class="mail-tags">
                            <div class="m-tag m-red"></div>
                            <div class="m-tag m-yellow"></div>
                            <div class="m-tag m-green"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- NUEVO CTA 1: TRAS VER EL PRODUCTO -->
    <div style="text-align: center; margin-top: 50px; border-color: #9F40FF;">
        <a href="#agenda" class="cta-secondary">Solicitar viabilidad para mi empresa</a>
    </div>

</section>
<hr>
<!-- METODOLOGÍA -->
<section class="rich-section padding-y-lg">
    <div class="section-header">
        <h2 style="font-size: 2.5rem;">Metodología de Implementación</h2>
        <p>Proceso estructurado para auditar e integrar soluciones tecnológicas.</p>
    </div>
    <div class="timeline-container">

        <div class="timeline-step">
            <div class="step-number" style="border-color: #0056b3; background: #004494; color: white;">1</div>
            <span class="step-badge bg-blue-light text-blue">Fase 1</span>
            <h3 class="step-title">Evaluación de Necesidades</h3>
            <p class="step-desc">Analizamos tus procesos actuales sin costo e identificamos las áreas donde la automatización puede tener más impacto.</p>
            <ul class="step-list">
                <li>Análisis de procesos actuales</li>
                <li>Identificación de áreas problemáticas</li>
                <li>Evaluación del retorno sobre la inversión</li>
            </ul>
        </div>

        <div class="timeline-step">
            <div class="step-number" style="border-color: #0056b3; background: #004494; color: white;">2</div>
            <span class="step-badge bg-blue-light text-blue">Fase 2</span>
            <h3 class="step-title">Mapeo Completo</h3>
            <p class="step-desc">Documentamos todos los flujos de trabajo e identificamos oportunidades de optimización y automatización.</p>
            <ul class="step-list">
                <li>Mapeo de todos los procesos empresariales</li>
                <li>Identificación de cuellos de botella</li>
                <li>Priorización de oportunidades</li>
            </ul>
        </div>

        <div class="timeline-step">
            <div class="step-number" style="border-color: #0056b3; background: #004494; color: white;">3</div>
            <span class="step-badge bg-blue-light text-blue">Fase 3</span>
            <h3 class="step-title">Selección de Herramientas</h3>
            <p class="step-desc">Te entregamos un roadmap detallado: qué automatizar, en qué orden y por qué, con las herramientas más adecuadas.</p>
            <ul class="step-list">
                <li>Selección de herramientas adecuadas</li>
                <li>Evaluación de compatibilidad</li>
                <li>Plan de implementación priorizado</li>
            </ul>
        </div>

        <div class="timeline-step">
            <div class="step-number" style="border-color: #0056b3; background: #004494; color: white;">4</div>
            <span class="step-badge bg-blue-light text-blue">Fase 4</span>
            <h3 class="step-title">Implementación Gradual</h3>
            <p class="step-desc">Automatizamos por fases, comenzando con proyectos piloto y midiendo resultados en cada etapa.</p>
            <ul class="step-list">
                <li>Implementación por fases</li>
                <li>Proyectos piloto</li>
                <li>Monitoreo continuo de resultados</li>
            </ul>
        </div>

        <div class="timeline-step">
            <div class="step-number" style="border-color: #0056b3; background: #004494; color: white;">5</div>
            <span class="step-badge bg-blue-light text-blue">Fase 5</span>
            <h3 class="step-title">Optimización Continua</h3>
            <p class="step-desc">Monitoreamos y ajustamos constantemente para asegurar un ROI sostenido y mejora continua.</p>
            <ul class="step-list">
                <li>Monitoreo de métricas clave</li>
                <li>Ajustes basados en resultados</li>
                <li>Capacitación continua del equipo</li>
            </ul>
        </div>

    </div>
</section>
<hr>
<!-- ======================================================= -->
<!-- SECCIÓN AGENDA (REDISEÑADA PARA ALTA CONVERSIÓN)    -->
<!-- ======================================================= -->
<section id="agenda" class="agenda-section">
    <div class="container">
        <div class="agenda-card">

            <!-- Columna Izquierda: Información y Pasos -->
            <div class="agenda-info">
                <span class="agenda-tag">Fase Inicial</span>
                <h3>Auditoría Técnica de Procesos</h3>
                <p class="agenda-desc">
                    Solicite una sesión consultiva. Analizaremos su flujo de trabajo actual para identificar operaciones susceptibles de ser automatizadas e integradas mediante Inteligencia Artificial.
                </p>

                <div class="agenda-steps">
                    <div class="agenda-step">
                        <div class="step-indicator">1</div>
                        <div class="step-text">
                            <strong>Análisis Operativo (30 min)</strong>
                            <span>Revisión de sus herramientas de software y localización de cuellos de botella operativos.</span>
                        </div>
                    </div>
                    <div class="agenda-step">
                        <div class="step-indicator">2</div>
                        <div class="step-text">
                            <strong>Mapeo de Soluciones</strong>
                            <span>Diseño preliminar de la arquitectura de datos y viabilidad de conectores API.</span>
                        </div>
                    </div>
                    <div class="agenda-step">
                        <div class="step-indicator">3</div>
                        <div class="step-text">
                            <strong>Plan de Despliegue</strong>
                            <span>Entrega de cronograma de integración y proyección de retorno de inversión (ROI).</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Panel de Acción -->
            <div class="agenda-action">
                <div class="action-panel">
                    <div class="panel-header">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <h4>Acceso al Calendario</h4>
                    </div>
                    <p>Seleccione el horario óptimo para su equipo técnico o directivo. Asignaremos a un especialista en infraestructura a su caso.</p>

                    <a href="<?php echo $cta_link; ?>" class="cta-primary agenda-btn">
                        <?php echo $is_logged_in ? 'Seleccionar fecha y hora' : 'Crear cuenta para acceder'; ?>
                    </a>

                    <div class="panel-footer">
                        <span class="disclaimer">✓ Sesión sin compromiso de contratación.</span>
                        <?php if (!$is_logged_in): ?>
                            <a href="register.html" class="register-link">¿No dispone de credenciales? <strong>Alta de empresa</strong></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<hr>
<!-- PREGUNTAS FRECUENTES (FAQ) -->
<section class="rich-section padding-y-lg">
    <div class="section-header">
        <h2>Preguntas Frecuentes</h2>
        <p>Resolvemos las consultas técnicas y operativas más habituales sobre la integración de nuestros sistemas.</p>
    </div>

    <div class="faq-container">

        <div class="faq-item">
            <button class="faq-question">
                <span>¿Es necesario tener conocimientos técnicos para utilizar los sistemas?</span>
                <div class="faq-icon"></div>
            </button>
            <div class="faq-answer">
                <p>No. Nos encargamos íntegramente del desarrollo, la integración de APIs y el despliegue en sus servidores. Entregamos el sistema configurado y proporcionamos la formación necesaria para su administración a nivel de usuario.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                <span>¿Cuánto tiempo requiere la implementación de una automatización?</span>
                <div class="faq-icon"></div>
            </button>
            <div class="faq-answer">
                <p>El cronograma depende de la complejidad estructural de la empresa. Las implementaciones estándar (como asistentes de comunicación o volcado de documentos) suelen estar en fase de producción en un plazo de 2 a 4 semanas tras la auditoría inicial.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                <span>¿Qué ocurre con la privacidad y seguridad de los datos de la empresa?</span>
                <div class="faq-icon"></div>
            </button>
            <div class="faq-answer">
                <p>Operamos bajo un estricto cumplimiento normativo (RGPD). Utilizamos integraciones oficiales mediante API y aseguramos que la información corporativa no se emplea para el entrenamiento de modelos de Inteligencia Artificial públicos de terceros.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                <span>¿Pueden los sistemas integrarse con el software que ya utilizamos?</span>
                <div class="faq-icon"></div>
            </button>
            <div class="faq-answer">
                <p>Sí. Desarrollamos conectores compatibles con la mayoría de ERPs, CRMs y herramientas de gestión del mercado (tales como Holded, HubSpot, SAP, Google Workspace o Microsoft 365), siempre y cuando estas plataformas dispongan de accesos API o webhooks habilitados.</p>
            </div>
        </div>

    </div>
</section>
<section class="integrations-band">
    <div class="integrations-header">
        <h3>Compatibilidad de sistemas</h3>
        <p>Desarrollo de conectores vía API para sincronizar datos con las plataformas estándar del mercado.</p>
    </div>
    <div class="marquee-container">
        <div class="marquee-track">
            <!-- Bloque 1 -->
            <div class="marquee-badge">Holded</div>
            <div class="marquee-badge">HubSpot</div>
            <div class="marquee-badge">WhatsApp API</div>
            <div class="marquee-badge">Salesforce</div>
            <div class="marquee-badge">Google Workspace</div>
            <div class="marquee-badge">Slack</div>
            <div class="marquee-badge">Microsoft 365</div>
            <div class="marquee-badge">Stripe</div>
            <div class="marquee-badge">Shopify</div>
            <div class="marquee-badge">Notion</div>
            <!-- Bloque 2 -->
            <div class="marquee-badge">Holded</div>
            <div class="marquee-badge">HubSpot</div>
            <div class="marquee-badge">WhatsApp API</div>
            <div class="marquee-badge">Salesforce</div>
            <div class="marquee-badge">Google Workspace</div>
            <div class="marquee-badge">Slack</div>
            <div class="marquee-badge">Microsoft 365</div>
            <div class="marquee-badge">Stripe</div>
            <div class="marquee-badge">Shopify</div>
            <div class="marquee-badge">Notion</div>
        </div>
    </div>
</section>
<!-- ======================================================= -->
<!-- NUEVO CTA 3: BOTÓN FLOTANTE PERSISTENTE                 -->
<!-- ======================================================= -->
<a href="#agenda" id="floating-cta" class="floating-cta">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="16" y1="2" x2="16" y2="6"></line>
        <line x1="8" y1="2" x2="8" y2="6"></line>
        <line x1="3" y1="10" x2="21" y2="10"></line>
    </svg>
    <span>Agendar evaluación</span>
</a>