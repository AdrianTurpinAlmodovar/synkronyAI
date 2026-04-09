# Documentación - SynkronyAI

# 1. Visión General del Proyecto

El presente proyecto detalla el diseño, desarrollo e implementación de **SynkronyAI**, una plataforma tecnológica orientada a la auditoría y aplicación de automatizaciones empresariales. El sistema está concebido para actuar como un puente entre herramientas de software de uso diario y flujos de trabajo autónomos, permitiendo a los negocios delegar tareas repetitivas.

### 1.1. Propósito y Objetivos

El origen de **SynkronyAI** surge tras la identificación de una brecha tecnológica significativa en el mercado actual. A pesar del auge de las plataformas de integración (como Make) y la Inteligencia Artificial, gran parte del tejido empresarial desconoce estas herramientas o carece de los conocimientos técnicos para implementarlas de forma nativa.

El propósito fundamental del proyecto es democratizar el acceso a estas tecnologías, ofreciendo soluciones a medida que se adaptan a la lógica de negocio de cada empresa.

Los objetivos principales que persigue este proyecto son:

- **Optimización de recursos:** Reducir la carga de trabajo manual y repetitivo, permitiendo a los empleados centrarse en tareas de mayor valor estratégico.
- **Reducción de errores operativos:** Minimizar el margen de error humano en la transferencia de datos y en tareas puramente administrativas.
- **Modernización y transformación digital:** Proveer a empresas tradicionales de herramientas de vanguardia, mejorando su competitividad tecnológica en el mercado.

### 1.2. Público Objetivo y Enfoque Arquitectónico

La plataforma y los servicios de SynkronyAI han sido diseñados teniendo en mente a un perfil de cliente muy definido: las **Pequeñas y Medianas Empresas (PYMES)**.

Estas organizaciones suelen contar con plantillas reducidas y no disponen de departamentos de IT (tecnología) dedicados. Por ello, la arquitectura del proyecto se ha enfocado en abstraer la complejidad técnica. Se ha construido una plataforma web intuitiva y desarrollada a medida que actúa como escaparate e interfaz de contacto, mientras que el ecosistema de automatización opera en un segundo plano ("en la sombra"), integrándose con las herramientas que la PYME ya utiliza sin alterar drásticamente su forma de trabajar.

### 1.3. Stack Tecnológico Principal

Para el desarrollo de la plataforma web se ha optado por una programación nativa desde cero. Esta decisión descarta el uso de gestores de contenido genéricos (como WordPress) con el objetivo de garantizar el máximo control, un rendimiento óptimo y una seguridad robusta a medida.

A nivel general, el ecosistema tecnológico se compone de:

- **Backend y Seguridad:** PHP para el enrutamiento y la lógica principal del servidor, implementando sentencias preparadas (*Prepared Statements*) como medida de seguridad activa contra inyecciones SQL.
- **Base de Datos:** MySQL para el almacenamiento estructurado y relacional de la información del catálogo.
- **Frontend (Estructura y Estilos):** HTML5 y CSS3 para el diseño visual y la maquetación responsiva.
- **Interactividad y Dinamismo:** JavaScript.
- **Experiencia de Usuario (UI/UX):** Integración de librerías especializadas como GSAP para las animaciones y efectos visuales avanzados, y Swiper.js para la gestión de galerías y carruseles.

*(Nota: La justificación detallada y el caso de uso específico de cada una de estas tecnologías se abordará en profundidad en los capítulos posteriores de esta memoria).*

# 2. Estructura de Archivos

![image.png](image.png)

![image.png](image%201.png)

![image.png](image%202.png)

### 2.1. Directorio `/admin/` (Panel de Administración)

El directorio `/admin/` constituye el núcleo de gestión privada (Back-Office) de SynkronyAI. Esta sección ha sido diseñada bajo el principio de Separación de Responsabilidades, dividiendo claramente las interfaces visuales (vistas) de los archivos que ejecutan las operaciones en la base de datos (controladores).

A continuación, se detalla en profundidad la función y composición de cada script:

### 2.1.1. Interfaces de Gestión (Vistas Principales)

- **`admin_dashboard.php` (Panel Principal)**
Actúa como el centro de control y vista general del sistema. Su objetivo es proporcionar métricas clave del negocio nada más iniciar sesión. A nivel de código, el archivo extrae información de la base de datos almacenándola en variables críticas (como `$total_servicios`, `$total_usuarios` y `$solicitudes_pendientes`).
La interfaz renderiza componentes visuales tipo tarjeta (`stat-card`) para mostrar estos valores numéricos de forma clara e incluye enlaces de acceso rápido para redirigir al administrador hacia las herramientas de gestión operativas (`admin_services.php` y `admin_dashboard_requests.php`).
- **`admin_dashboard_requests.php` (Gestión de Leads)**
Interfaz dedicada a la visualización y seguimiento de las solicitudes de clientes captadas por la web. Renderiza una tabla de datos interactiva conectada a la base de datos que expone las siguientes columnas: ID de registro, Nombre del cliente, Correo electrónico, Estado de la cita, Fecha de la cita y Fecha de solicitud. Desde esta vista, el administrador puede realizar acciones de seguimiento (Aprobar, Rechazar, Contactar) mediante botones que invocan a los scripts de edición y borrado correspondientes.
- **`admin_services.php` (Constructor de Soluciones)**
Es el módulo más avanzado del panel, ya que permite la creación automatizada de páginas web. Visualmente se compone de una cabecera, un título descriptivo, un botón de "Añadir Nuevo Servicio" y una tabla de gestión (con columnas para Icono, Título, Categoría, Tipo, Tiempo y Acciones).
Su valor técnico reside en que, al utilizar el formulario de creación o el botón de editar, el administrador rellena los campos y el sistema procesa esa información para crear dinámicamente la página pública del servicio, eliminando por completo la necesidad de programar código manual para cada nueva solución añadida al catálogo.
- **`admin_users.php` (Gestión de Accesos)**
Módulo de control de privilegios. Presenta una estructura tabular similar a las anteriores para listar a los administradores del sistema. Muestra los datos de ID, Nombre, Rol y Fecha de creación. Incluye un botón de acción principal (distinguido visualmente en color morado) para dar de alta nuevas credenciales y botones individuales por fila para editar o revocar el acceso de usuarios existentes.

### 2.1.2. Arquitectura Asíncrona (API Interna)

- **`api_stats.php`**
Para evitar sobrecargar el tiempo de respuesta al cargar el panel principal, las métricas complejas se procesan mediante este archivo, que actúa como un endpoint de API. El flujo de ejecución es el siguiente:
    1. El administrador accede a `admin_dashboard.php`.
    2. Un script de JavaScript en la vista realiza una petición asíncrona a `api_stats.php`.
    3. El servidor ejecuta las consultas y devuelve los datos estructurados en formato JSON.
    4. El Dashboard recibe el JSON y actualiza los indicadores (KPIs) en tiempo real. Los datos servidos por este archivo incluyen: 3 KPIs numéricos (Usuarios Totales, Histórico de Citas Totales, Próximas Citas) y la información para renderizar dos gráficos (un gráfico de líneas con los nuevos usuarios de los últimos 7 días, y un gráfico circular mostrando el porcentaje de citas confirmadas, canceladas y pendientes).

### 2.1.3. Controladores CRUD (Persistencia de Datos)

Para aislar la lógica, los botones de acción de las vistas anteriores envían los datos a los siguientes scripts externos, los cuales se encargan exclusivamente de ejecutar la sentencia SQL (Crear, Editar, Eliminar) y redirigir al usuario, sin mostrar interfaz gráfica:

- **Controladores de Solicitudes:**
    - `request_edit.php`: Este archivo nos permite modificar las citas que ahi ahora mismo activas.
    - `request_delete.php`: Elimina definitivamente el registro de la cita de la base de datos.
- **Controladores de Servicios, importante porque con esto podemos controlar toda la parte se servicios que esta automatizada:**
    - `service_add.php`: Inserta los parámetros de un nuevo servicio en el catálogo.
    - `service_edit.php`: Actualiza la información de un servicio existente.
    - `service_delete.php`: Retira el servicio y sus datos asociados.
- **Controladores de Usuarios:**
    - `user_add.php`: Registra un nuevo administrador con su contraseña cifrada.
    - `user_edit.php`: Modifica el rol o la información de acceso de un administrador.
    - `user_delete.php`: Elimina la cuenta y bloquea su acceso al sistema.

### 2.2. Directorio `/assets/` (Recursos Visuales e Interactivos)

Este directorio almacena todos los elementos que el navegador del visitante necesita para dibujar y dar vida a la página web.

Para garantizar un rendimiento óptimo y tiempos de carga rápidos, se ha aplicado una **estrategia modular**. En lugar de crear archivos gigantes que se cargan en todas partes, los estilos y funciones se han dividido en archivos más pequeños. De este modo, cada página web solicita únicamente los recursos que realmente necesita en ese momento.

A continuación, se desglosan las tres subcarpetas principales:

### 2.2.1. Hojas de Estilo (`/css/`)

Contiene 11 archivos encargados de la apariencia visual de la plataforma, organizados según su propósito:

- **Base y Diseño Global (`styles.css`):** Es el archivo principal. Define la "identidad visual" de SynkronyAI a través de variables de colores (fondos oscuros con acentos en tonos azul y morado) y establece las tipografías oficiales (fuente *Inter* para textos de lectura y *Poppins* para los títulos).
- **Vistas del Catálogo (`solutions.css` y `solution-detail.css`):** Controlan el diseño de la sección de servicios. Organizan las soluciones en una cuadrícula responsiva (que se adapta a móviles y ordenadores) y dan forma a la página individual de cada servicio, estructurando la galería de imágenes, las características y el formulario de contacto integrado.
- **Efectos de la Portada (`home-features.css` y `hero-impact.css`):** Archivos dedicados exclusivamente a la página de inicio. Maquetan elementos visualmente complejos como simulaciones de chats de WhatsApp, líneas de tiempo visuales y efectos de entrada al cargar la página inicial.
- **Zona Privada (`admin-styles.css`):** Contiene reglas de diseño que solo se aplican en el Panel de Administración (tablas de datos, botones de estado, alertas en color rojo para borrados, etc.), aislando estos estilos de la web pública.
- **Componentes Emergentes y Ventanas Modales:** Un grupo de archivos pequeños (`video-modal.css`, `image-modal.css`, `demo-modal.css` y `user-widget.css`) que dan estilo a los elementos que "flotan" sobre la pantalla, como reproductores de vídeo en pantalla completa, galerías de imágenes con zoom o el menú desplegable del perfil del usuario.
- **Sistema de Citas (`agenda.css`):** Da formato a los calendarios interactivos y a los formularios donde los clientes solicitan reuniones, marcando visualmente en distintos colores si el usuario ha introducido bien o mal sus datos.

### 2.2.2. Interactividad y Dinamismo (`/js/`)

Esta subcarpeta contiene la lógica de programación (JavaScript) que hace que la web reaccione a las acciones del usuario sin tener que recargar la página:

- **`scripts.js` (Comportamiento Global):** Archivo central que controla funciones comunes, como el despliegue del menú en dispositivos móviles, el desplazamiento suave al pulsar enlaces y la visualización de notificaciones y alertas estéticas en pantalla.
- **`soluciones.js` (Buscador Inteligente):** Dota de "inteligencia" al catálogo de soluciones. Permite al usuario usar filtros de categorías o buscar servicios por palabras clave de forma instantánea. Además, incluye un sistema de carga progresiva (*lazy loading*) que hace aparecer las tarjetas de forma animada a medida que el visitante baja por la pantalla.
- **`home-features.js` (Efectos Avanzados):** Activa las animaciones complejas de la página de inicio, como el efecto de "escribiendo..." en los mensajes simulados o el seguimiento de clics en los botones principales.

### 2.2.3. Recursos Gráficos (`/img/` y `/uploads/`)

Separa estrictamente las imágenes de diseño de las imágenes de contenido:

- **Directorio Estático (`/img/`):** Almacena imágenes que forman parte del diseño permanente de la web, como las variaciones del logotipo corporativo (con y sin fondo) y los iconos de redes sociales (Telegram, TikTok, Instagram).
- **Directorio Dinámico (`/uploads/services/`):** Es una carpeta fundamental que se comunica directamente con el Panel de Administración. Aquí se guardan de forma automática todas las fotografías que el administrador sube al crear un nuevo servicio, manteniendo el contenido de la base de datos perfectamente ordenado y separado del código del sistema.

### 2.3. Autenticación y Formularios (`/auth/` y Raíz)

Esta sección del proyecto gestiona el acceso al sistema (inicios de sesión), el registro de nuevos usuarios y la recolección de solicitudes de contacto de los clientes.

De forma equivalente al panel de administración, se aplica el principio de separación de tareas: los archivos con interfaz gráfica (formularios) se ubican en el directorio principal, mientras que los archivos que procesan la información de forma segura operan desde el directorio `/auth/`.

### 2.3.1. Interfaces Visibles (Formularios)

Estos son los archivos HTML que interactúan directamente con el usuario visitante. Mantienen un diseño centrado y coherente con la identidad visual corporativa.

- **`login.html` (Pantalla de Acceso):** Formulario para la introducción de credenciales (correo y contraseña). Incorpora validaciones nativas de HTML5 para garantizar que los campos requeridos se completen antes del envío al servidor.
- **`register.html` (Pantalla de Registro):** Formulario de alta para nuevos usuarios. Solicita correo, contraseña y un campo adicional de confirmación de contraseña para prevenir errores tipográficos durante el registro.

### 2.3.2. Archivos de Procesamiento Interno (Carpeta `/auth/`)

Al enviar los formularios, los datos se dirigen a los siguientes scripts encargados de su validación y procesamiento en la base de datos:

- **`login_process.php` (Motor de Inicio de Sesión):**
Recibe las credenciales y verifica su existencia en la base de datos. Si la validación es correcta, inicializa la sesión del usuario. Implementa una redirección condicional: los usuarios con rol `admin` son enviados al panel de control privado, mientras que el rol `user` es dirigido al panel estándar.
- **`register_process.php` (Motor de Registro):**
Realiza comprobaciones de seguridad previas a la creación del usuario. Valida el formato del correo, la longitud mínima de la contraseña y verifica que el correo no se encuentre ya registrado para evitar cuentas duplicadas. Tras superar estas validaciones, cifra la contraseña e inserta el registro.
- **`demo_request_create.php` (Captación de Clientes):**
Procesa los formularios de solicitud de demostración. Incluye una validación que comprueba si el cliente ya ha enviado una solicitud previa con ese mismo correo, previniendo registros duplicados. Tras la operación, redirige al usuario al formulario original mostrando un mensaje de estado (éxito o error).

### 2.3.3. Medidas de Seguridad Implementadas

Debido a la manipulación de datos sensibles, esta sección incorpora medidas de seguridad específicas para prevenir vulnerabilidades comunes:

1. **Cifrado de Contraseñas:** Las contraseñas se almacenan mediante el algoritmo de cifrado `BCRYPT` (utilizando la función nativa `password_hash`). Esto asegura que las credenciales permanezcan ilegibles en la base de datos.
2. **Protección contra Inyecciones SQL (SQLi):** Todas las consultas a la base de datos se ejecutan mediante "Sentencias Preparadas". Este método garantiza que los datos introducidos por el usuario no puedan alterar la estructura de las consultas SQL.
3. **Sanitización de Datos:** Antes de procesar entradas de texto, el sistema elimina espacios en blanco y neutraliza caracteres especiales para mitigar ataques de tipo XSS (Cross-Site Scripting).
4. **Gestión Centralizada de Errores:** Los errores de validación se manejan mediante una función unificada (`display_error()`), la cual devuelve al usuario al formulario original con un mensaje descriptivo, evitando exponer errores técnicos del servidor.

### 2.4. Panel de Usuario y Sistema de Citas (`/dashboard/`)

Esta sección abarca los archivos responsables del área privada de los clientes (usuarios estándar) y la lógica necesaria para que puedan gestionar su calendario de reuniones con la empresa de forma autónoma.

### 2.4.1. `dashboard_normal.php` (Interfaz Principal)

Es la pantalla que ve el cliente al iniciar sesión. Su función es actuar como el centro de control personal del usuario. Dentro de este archivo se ejecutan las siguientes acciones:

- **Visualización Inteligente:** Muestra un listado responsivo con todas las citas del usuario. El código asigna etiquetas visuales de colores según el estado de la reunión (Confirmada, Cancelada o Completada).
- **Mantenimiento Automático:** Antes de cargar la página, el script hace una limpieza automática en la base de datos: actualiza a "Completada" las citas cuya fecha ya ha pasado y oculta las citas canceladas muy antiguas.
- **Formulario de Reserva:** Renderiza el calendario y el selector de horas para pedir nuevas reuniones. El archivo comprueba en tiempo real la disponibilidad y bloquea el formulario si el usuario ya ha alcanzado el límite permitido (4 citas activas).
- **Control de Accesos:** Incluye un filtro de seguridad que redirige inmediatamente a los administradores hacia el panel correspondiente si intentan acceder a esta vista de cliente.

### 2.4.2. `appointment_handler.php` (Creador de Citas)

Este es un archivo de procesamiento "invisible". Recibe los datos del formulario de reserva cuando el cliente pulsa en "Agendar Cita" y ejecuta el siguiente flujo de trabajo:

- **Validaciones de Reglas de Negocio:** Verifica que la sesión del usuario es válida, que no se ha superado el límite de 4 citas y que el horario elegido no choca con el de otro cliente.
- **Guardado Seguro:** Utiliza sentencias preparadas para insertar los datos en la base de datos sin riesgo de inyecciones SQL.
- **Integraciones Externas:** Una vez guardada la cita, este archivo ejecuta dos acciones automáticas:
    1. Utiliza la librería **PHPMailer** para enviar un correo electrónico de confirmación con los datos de la cita al cliente.
    2. Dispara un *webhook* hacia [**Make.com**](http://make.com/), avisando al sistema externo de automatización de que hay un nuevo evento en el calendario.
- Finalmente, devuelve al usuario al dashboard con un mensaje visual de éxito o error.

### 2.4.3. `appointment_cancel.php` (Gestor de Cancelaciones)

Archivo encargado de procesar las anulaciones. Su código está diseñado para proteger el historial de la empresa y la privacidad de los clientes:

- **Validación de Propiedad (Ownership):** Es su medida de seguridad más importante. El script comprueba que el ID del usuario que intenta cancelar coincide exactamente con el dueño de la cita, impidiendo que un usuario malintencionado pueda borrar las reuniones de otros.
- **Preservación de Historial:** A diferencia de otros borrados del sistema, este archivo **no elimina** el registro de la base de datos. Simplemente cambia su estado a 'Cancelada' y guarda el motivo indicado por el usuario, manteniendo intacto el historial comercial.
- **Avisos Externos:** Al igual que el creador de citas, este archivo envía un *webhook* a [**Make.com**](http://make.com/) para que las automatizaciones externas liberen el hueco en el calendario y notifiquen al equipo de la anulación.

### 2.5. Directorio `/includes/` (Componentes Reutilizables)

La carpeta `/includes/` actúa como el "cerebro central" de la plataforma web. Siguiendo el principio de diseño *DRY (Don't Repeat Yourself - No te repitas)*, este directorio almacena todos los fragmentos de código, funciones de seguridad y secciones visuales que necesitan cargarse en múltiples páginas. Así, si hay que actualizar un elemento (como el pie de página o la conexión a la base de datos), se modifica un solo archivo y el cambio se refleja en todo el proyecto.

Para facilitar su comprensión, los archivos de este directorio se dividen en cuatro categorías:

### 2.5.1. Núcleo y Configuración del Sistema

Son los archivos invisibles que garantizan que el sistema funcione y sea seguro.

- **`db_config.php` (Conexión Central):** Es el puente entre el código PHP y la base de datos MySQL. Define las credenciales de acceso de forma centralizada y crea un objeto de conexión global (`$conn`). Técnicamente, está optimizado para usar el conjunto de caracteres `utf8mb4` (garantizando compatibilidad total con caracteres especiales y emojis) y cuenta con un sistema de captura de errores (*try-catch*) para evitar que la web se caiga si falla el servidor de datos.
- **`funciones.php` (Librería Global):** Es un "botiquín" de herramientas de programación usadas por todo el sistema. Incluye funciones de seguridad críticas como `check_login_access()` y `check_admin_access()` para bloquear páginas privadas, funciones de limpieza para evitar inyecciones de código malicioso (XSS), y un sistema unificado llamado `display_error()` que muestra ventanas de alerta estéticas al usuario en lugar de errores técnicos del servidor.

### 2.5.2. Componentes Estructurales Globales

Son las piezas de la interfaz que el usuario ve de forma constante mientras navega por diferentes páginas.

- **`footer.php` (Pie de Página):** Cierra todas las páginas del sitio. Muestra la información de contacto, los enlaces legales y las redes sociales. Técnicamente, incluye una función para actualizar el año del *Copyright* dinámicamente y está optimizado para buscadores (SEO) mediante datos estructurados.
- **Sistema de Widgets (`user_widget.php` y `user_widget_soluciones.php`):** Son los menús de usuario situados en la barra de navegación superior. Tienen "inteligencia contextual": detectan si el usuario está conectado para mostrar su avatar y botones de salir/entrar. La versión `_soluciones` está diseñada específicamente para calcular bien las rutas de los enlaces cuando el usuario navega dentro de subcarpetas, garantizando que los enlaces nunca se rompan independientemente de dónde se encuentre el visitante.
- **`modal_image_video.php` (Ventanas Multimedia):** Contiene la estructura HTML para reproducir vídeos de YouTube/Vimeo o ampliar imágenes sin salir de la página. Incluye opciones de accesibilidad (para navegar con el teclado), cierre al hacer clic fuera del recuadro y soporte para gestos táctiles en móviles.

### 2.5.3. Módulos de la Página Principal (Homepage)

En lugar de tener una página de inicio gigante e inmanejable, su código se ha dividido en "bloques de construcción" que se ensamblan entre sí.

- **`hero_home.php`:** Es la primera impresión del sitio. Renderiza el título principal, el subtítulo persuasivo y los botones de llamada a la acción (CTAs).
- **`home_sections.php` (Motor Principal):** Alberga el grueso visual de la portada. Utiliza consultas SQL para mostrar tres servicios aleatorios del catálogo (para que la portada cambie dinámicamente), dibuja la cuadrícula interactiva de servicios (*Bento grid*) e integra el formulario de reserva de auditorías.
- **`seccion_valoraciones.php`:** Módulo de "prueba social" que muestra las opiniones de los clientes. Renderiza un carrusel o cuadrícula con las estrellas de valoración, fotografías y cargos de los clientes para aumentar la credibilidad y la tasa de conversión.

### 2.5.4. Librerías de Terceros

- **Carpeta `/PHPMailer/`:** Para evitar que los correos electrónicos del sistema acaben en la bandeja de *Spam*, el proyecto prescinde de la función básica de correo de PHP e integra la librería profesional PHPMailer. Esta herramienta permite conectar el sistema con servidores SMTP (como Gmail), enviar correos en formato HTML con diseños atractivos e incluir archivos adjuntos. Se utiliza principalmente para confirmar citas agendadas desde el panel de control y para dar la bienvenida a nuevos registros.

### 2.6. Sistema Automatizado de Noticias (`/noticias/`)

El módulo de noticias incorpora una arquitectura innovadora que difiere del resto de la plataforma. En lugar de consumir información de la base de datos MySQL local, implementa un enfoque de tipo *Headless CMS* (Gestor de Contenido Desacoplado), utilizando los servicios en la nube de Google para alimentar la página web en tiempo real.

### 2.6.1. Arquitectura "Sin Base de Datos" Local

La mayor ventaja técnica de este módulo es la separación total entre la gestión del contenido y el código del servidor. Los artículos se almacenan y actualizan desde una hoja de cálculo de Google Sheets. El sistema lee este documento remoto publicado en formato CSV (*Comma-Separated Values*) mediante conexiones seguras.

Esto permite que cualquier persona del equipo pueda añadir, editar o borrar noticias directamente desde la interfaz familiar de Google, reflejándose los cambios automáticamente en la página web sin necesidad de acceder al panel de administración del sistema ni sobrecargar la base de datos principal.

### 2.6.2. `index.php` (Motor del *Feed* de Noticias)

Este archivo es el responsable de conectar con la nube, procesar la información y dibujar la interfaz visual. Sus funciones principales se dividen en tres áreas:

- **Conexión, Streaming y Resiliencia:** El archivo realiza la petición HTTP a Google Sheets cada vez que un visitante accede a la página. Para evitar mostrar información obsoleta guardada en la memoria del navegador, incorpora un sistema automático de *Cache Breaker* (rompedor de caché). Además, cuenta con "fallbacks inteligentes": si la conexión con Google falla temporalmente, el código está preparado para capturar el error y mostrar un mensaje amigable o un diseño alternativo, evitando que la página colapse.
- **Lógica de Presentación Dinámica:** Una de sus características más destacadas es el cálculo de fechas en formato relativo. En lugar de mostrar al usuario una fecha estática (ej. "12/05/2026"), el script cuenta con un algoritmo que calcula la diferencia de tiempo entre la publicación y la visita actual para generar formatos amigables como "hace 5 minutos" o "hace 2 horas". Esto genera una mayor sensación de inmediatez y dinamismo en la plataforma.
- **Estructura Visual e Integración Global:** Tras procesar y limpiar los textos recibidos (escapando caracteres para mantener la seguridad), el código renderiza el contenido utilizando una cuadrícula responsiva (*Grid layout*). Cada noticia se muestra en un componente tipo tarjeta (*Card*) que encapsula la imagen, la categoría, el título, un breve resumen y la etiqueta de tiempo. Finalmente, el archivo inyecta los componentes reutilizables del directorio `/includes/` (como el *header*, el menú de usuario contextual y el *footer*) para que el diseño sea idéntico al resto de la aplicación.

### 2.7. Catálogo Dinámico de Servicios (`/soluciones/`)

Este directorio contiene el núcleo comercial de SynkronyAI: el escaparate donde los clientes pueden explorar, filtrar y solicitar las automatizaciones que ofrece la empresa.

A nivel arquitectónico, este módulo destaca por su alta escalabilidad. En lugar de crear un archivo HTML estático por cada servicio ofrecido (lo cual sería insostenible a largo plazo), se ha desarrollado un sistema de plantillas dinámicas que se comunican constantemente con la base de datos para renderizar la información en tiempo real.

El módulo se compone de dos archivos principales:

### 2.7.1. `index.php` (Explorador del Catálogo)

Es la página principal de la sección de soluciones. Su objetivo es mostrar a los visitantes todas las automatizaciones disponibles de forma ordenada y accesible. Sus características técnicas son:

- **Renderizado de Cuadrícula (Grid):** Se conecta a la base de datos para extraer el listado completo de servicios activos y los dibuja en pantalla utilizando componentes visuales tipo tarjeta (*Cards*), mostrando la información básica (icono, título y resumen) de cada uno.
- **Filtrado Dinámico y Búsqueda:** Incorpora un sistema interactivo que permite al usuario filtrar los servicios por categorías o por su tipo de implementación. Además, incluye un buscador integrado para encontrar soluciones específicas rápidamente sin recargar la página.
- **Enrutamiento SEO-Friendly:** Para cada servicio listado, el archivo genera automáticamente URLs "amigables" y fáciles de leer (por ejemplo: `dominio.com/soluciones/bot-whatsapp` en lugar de `dominio.com/soluciones?id=4`). Esto es fundamental para el posicionamiento en buscadores (SEO).

### 2.7.2. `detail.php` (Plantilla de Vista Detallada)

Este archivo es una plantilla "maestra". Cuando el visitante hace clic en un servicio específico del catálogo, este archivo captura la URL, busca ese servicio exacto en la base de datos y construye una página web completa y personalizada en milisegundos. Sus funcionalidades clave incluyen:

- **Procesamiento de Datos Estructurados:** Es capaz de leer e interpretar información compleja de la base de datos (como características guardadas en formato de texto o JSON) para dibujarlas en pantalla con su estilo e iconos correspondientes.
- **Gestión Multimedia:** Renderiza las galerías de imágenes y vídeos explicativos específicos de esa solución.
- **Experiencia de Usuario y Conversión:** Estructura la información de forma persuasiva. Dibuja una línea de tiempo (*Timeline* visual) que explica al cliente paso a paso cómo se implementaría esa IA en su negocio, despliega una sección de Preguntas Frecuentes (FAQ) y coloca botones estratégicos (CTAs) para redirigir al usuario hacia el formulario de contacto o agenda.

### 2.8. Archivos del Directorio Raíz (Puntos de Entrada)

El directorio principal (la raíz del servidor) se ha mantenido lo más limpio posible para evitar el caos organizativo. En lugar de acumular decenas de scripts, la raíz aloja exclusivamente los puntos de entrada públicos y los archivos de navegación más básicos.

- **`index.php` (Página de Inicio y Ensamblador)**
Es el punto de entrada principal del sitio web (Homepage). A nivel de código, este archivo funciona como un "ensamblador" o "esqueleto". En lugar de contener miles de líneas de código HTML, se encarga de llamar e incrustar los diferentes módulos dinámicos alojados en la carpeta `/includes/` (como el *Hero*, las secciones de servicios, las valoraciones y los formularios de agenda). Además, inicializa elementos globales interactivos como el widget de usuario y las ventanas modales multimedia, garantizando que la portada cargue rápido y sea fácil de editar en el futuro.
- **`logout.php` (Gestor de Cierre de Sesión)**
Es un archivo crítico para la seguridad del sistema. Su única función es destruir la sesión activa del usuario. Cuando un administrador o cliente decide salir, este script borra de forma segura todas las variables de entorno (`$_SESSION`) y limpia las cookies de autenticación, garantizando que nadie más pueda acceder al panel si el ordenador se queda desatendido. Una vez limpia la memoria, redirige automáticamente al usuario a la pantalla de inicio de sesión.
- **Interfaces Públicas Estáticas (`login.html` y `register.html`)**
Como se detalló en el apartado de Autenticación (2.3), estos son los formularios visuales de acceso y alta de usuarios. Se ubican estratégicamente en la raíz para que sus URLs sean cortas, directas y fáciles de compartir (ej. `dominio.com/login.html`), mientras que envían los datos sensibles a la carpeta protegida `/auth/` para su procesamiento.

# 3. Almacenamiento de Datos

El sistema de SynkronyAI utiliza una arquitectura de persistencia de datos híbrida. Este modelo está diseñado para equilibrar la seguridad y estructuración de la información crítica con la agilidad necesaria para actualizar contenidos dinámicos.

Para lograrlo, la plataforma divide su almacenamiento en dos entornos: una base de datos relacional tradicional (MySQL) para la lógica de negocio, y un entorno basado en la nube (Google Sheets) que actúa como un Gestor de Contenidos (CMS) externo.

### 3.1. Base de Datos Relacional (MySQL)

El núcleo del sistema utiliza MySQL (MariaDB) para garantizar la integridad referencial de los datos y la seguridad de los usuarios. La base de datos, denominada `synkrony_db`, está configurada con el conjunto de caracteres `utf8mb4` para asegurar una compatibilidad absoluta con todo tipo de caracteres, incluyendo emojis.

A continuación, se detalla la estructura lógica y el propósito de cada tabla (Diccionario de Datos):

### 3.1.1. Tabla `users` (Gestión de Identidad)

Es la tabla maestra para el control de accesos. Centraliza las credenciales tanto del equipo de administración como de los clientes registrados.

- **id** (INT, Primary Key, Auto-incremento): Identificador único.
- **name** (VARCHAR): Nombre completo del usuario.
- **email** (VARCHAR, Unique): Correo electrónico. Su restricción de unicidad previene registros duplicados.
- **password_hash** (VARCHAR): Contraseña protegida mediante el algoritmo de cifrado unidireccional BCRYPT.
- **role** (VARCHAR): Nivel de privilegios dentro del sistema (`admin` o `user`).
- **created_at** (DATETIME): Fecha y hora exacta de registro.

### 3.1.2. Tabla `appointments` (Motor de Reservas)

Registra y gestiona las citas solicitadas por los clientes desde el panel de usuario. Mantiene una relación directa con la tabla de usuarios.

- **id** (INT, Primary Key, Auto-incremento): Identificador único de la cita.
- **user_id** (INT, Foreign Key): Referencia directa al `id` del usuario en la tabla `users`.
- **date** (DATE) y **time** (TIME): Fecha y hora de la reunión.
- **status** (VARCHAR): Estado de la cita (valores predeterminados: `confirmed`, `completed` o `cancelled`).
- **cancellation_reason** (TEXT): Campo de texto que registra el motivo de anulación para mantener el histórico de interacciones.
- **Integridad Relacional:** Se ha implementado una restricción `ON DELETE CASCADE` sobre la clave foránea `user_id`. Si un usuario elimina su cuenta, todas sus citas asociadas se borran automáticamente, evitando inconsistencias en la base de datos.

### 3.1.3. Tabla `services` (Catálogo de Soluciones)

Almacena todos los parámetros necesarios para renderizar el catálogo web. Su diseño permite la generación automática de páginas dinámicas sin modificar el código fuente.

- **id** (INT, Primary Key, Auto-incremento): Identificador del servicio.
- **icon**, **image_url**, **video_url**: Enlaces y referencias a los recursos multimedia (imágenes, iconos y vídeos de YouTube).
- **title**, **categoria**, **tipo**: Clasificadores principales de la solución (ej. "Inteligencia Artificial", "Asistente Conversacional").
- **tiempo_implementacion** (VARCHAR): Estimación temporal proyectada al cliente.
- **resumen**, **description**, **description_extended**: Diferentes niveles de profundidad de texto (para tarjetas resumen o páginas detalladas).
- **caracteristicas_principales** (TEXT): Almacena matrices de características técnicas en **formato JSON** (ej. `["Reconocimiento óptico", "Integración CRM"]`). Esto optimiza la base de datos al permitir guardar múltiples puntos clave en una sola celda.

### 3.2. Base de Datos Externa en la Nube (Google Sheets)

Para dotar al sistema de mayor agilidad operativa sin sobrecargar el servidor principal, SynkronyAI implementa una arquitectura externa basada en Google Sheets. Estas hojas de cálculo actúan como bases de datos ligeras, alimentadas automáticamente por flujos de trabajo en [**Make.com**](http://make.com/).

Este entorno se divide en tres documentos principales:

### 3.2.1. Hoja: `Noticias_IA` (Gestor de Contenidos - Headless CMS)

Actúa como el motor de datos para la página de actualidad (`/noticias/index.php`). Esta hoja es actualizada periódicamente a través de un **flujo automatizado de noticias en Make**, permitiendo que la web muestre nuevos artículos sin necesidad de utilizar el servidor MySQL.

- **Columnas de la tabla:** * `Título`: Titular del artículo.
    - `Categoría`: Clasificación temática (ej. Inteligencia Artificial).
    - `Imagen_URL`: Enlace directo a la miniatura visual.
    - `Resumen`: Texto corto para la tarjeta de previsualización.
    - `Contenido_Extendido`: Cuerpo completo de la noticia.
    - `Fecha`: Fecha de publicación para calcular el tiempo relativo en la web.

### 3.2.2. Hoja: `Leads Synkrony` (CRM Comercial Ligero)

Funciona como un repositorio estructurado para clientes potenciales. Esta tabla es alimentada por el **flujo de Make de categorización de correos**, el cual detecta mensajes con intención comercial y los registra aquí automáticamente.

- **Columnas de la tabla:**
    - `Fecha`: Momento exacto de la entrada del lead.
    - `Nombre`, `Email`, `Teléfono`: Datos de contacto del prospecto.
    - `Empresa`: Entidad a la que representa.
    - `Mensaje/Interés`: Extracto procesado de la necesidad del cliente.
    - `Estado`: Fase en el embudo de ventas (ej. "Nuevo").
    - `Prioridad`: Nivel de urgencia o importancia asignado (ej. "Alta").

### 3.2.3. Hoja: `Búfer Otros` (Gestión Administrativa)

Actúa como una bandeja de entrada estructurada para aquellos correos que el **flujo de Make** clasifica como "no comerciales" (consultas generales, proveedores, notificaciones, etc.), manteniendo limpia la hoja de Leads.

- **Columnas de la tabla:**
    - `Fecha`: Momento de recepción.
    - `Remitente`: Dirección de correo o entidad que envía el mensaje.
    - `Asunto`: Título original del correo.
    - `Resumen_Contenido`: Síntesis del mensaje generada automáticamente.
    - `Clasificación`: Etiqueta del tipo de correo (ej. "Administrativo").

# 4. Automatizaciones e Integraciones ([Make.com](http://make.com/))

El núcleo operativo de SynkronyAI no reside únicamente en su código fuente, sino en una arquitectura de automatización basada en la plataforma iPaaS (Integration Platform as a Service) [**Make.com**](http://make.com/).

Se han desarrollado tres flujos de trabajo (*scenarios*) complejos que actúan como "middleware", conectando el servidor web, el correo electrónico corporativo y modelos de Inteligencia Artificial (LLMs) de gran escala.

### 4.1. Flujo 1: Clasificador Inteligente de Correos (`Integration Gmail`)

![image.png](image%203.png)

Este escenario revoluciona la gestión de atención al cliente de la plataforma, actuando como un filtro inteligente para la bandeja de entrada corporativa.

1. **Monitorización (*Trigger*):** El módulo `google-email:triggerWatchNewEmails` escanea continuamente la bandeja de entrada en busca de mensajes no leídos.
2. **Procesamiento de Lenguaje Natural (LLM):** Cuando entra un correo, se envía el asunto y el cuerpo del mensaje a la API de **Groq**. Se utiliza el modelo **`llama-3.3-70b-versatile`** con una instrucción (*System Prompt*) estricta que obliga a la IA a devolver un objeto JSON puro. La IA analiza el contexto y determina la categoría (Ventas, Soporte, Agenda, Administración, Spam u Otros), la urgencia y genera un resumen de máximo 15 palabras.
3. **Enrutamiento Condicional (*Router*):** Un módulo enrutador lee el JSON parseado y divide el flujo según la etiqueta de la IA:
    - **Ruta "Es Ventas" (Leads):** Inyecta los datos en la hoja `Leads Synkrony` y dispara una notificación push inmediata al equipo comercial mediante el Bot de Telegram (`telegram:SendReplyMessage`).
    - **Ruta "Es Agenda":** Archiva el correo asignándole la etiqueta interna de "Agenda" en Gmail para mantener el orden.
    - **Ruta "Es Spam":** El módulo `google-email:moveAnEmail` intercepta el correo basura y lo mueve directamente a la carpeta SPAM del servidor, manteniendo limpia la bandeja de entrada.
    - **Ruta de Seguridad (*Fallback* y Sistema de Búfer):** Esta es la ruta por defecto (`else`). Si el correo se clasifica como "Administración", "Otros" o no cumple las condiciones de las ramas principales, el sistema actúa para no perder la información pero evitando interrupciones constantes al equipo:
        - Lo registra en la hoja externa `Búfer Otros` y le añade la etiqueta correspondiente en Gmail.
        - **Lógica de Lotes (*Batching*):** En lugar de avisar por cada correo irrelevante, un módulo cuenta las filas del Excel. Si detecta que hay 5 o más correos acumulados, un Agregador de Texto (`util:TextAggregator`) consolida los resúmenes y envía un único informe por Telegram ("*Tienes 5 nuevos correos en Otros...*").
        - Finalmente, el módulo `google-sheets:clearValuesFromRange` vacía automáticamente las celdas del búfer en Drive para reiniciar el contador.

### 4.2. Flujo 2: Motor del CMS Autónomo (`Integration RSS, Groq`)

![image.png](image%204.png)

Este escenario es el responsable de mantener viva la sección de actualidad del sitio web sin intervención humana, alimentando la base de datos externa de Google Sheets.

1. **Captura de Datos:** Un lector RSS (`rss:TriggerNewArticle`) extrae las últimas publicaciones tecnológicas (ej. feed de [*eldiario.es*](http://eldiario.es/)).
2. **Síntesis y Etiquetado:** Se invoca nuevamente a la API de **Groq**, esta vez utilizando el modelo **`llama-3.1-8b-instant`** (optimizado para velocidad). La IA lee los primeros 2000 caracteres de la noticia y devuelve un JSON con un resumen ultracorto (10-15 palabras) y una categorización predefinida (IA, Software, Hardware o Gadgets).
3. **Persistencia Dinámica:** El módulo `google-sheets:addRow` mapea la respuesta de la IA junto con la imagen original, la URL y la fecha, insertando una nueva fila en el documento `Noticias_IA`. Al instante, la página `/noticias/index.php` refleja este nuevo contenido.
4. Este flujo tiene un pequeño detalle externo, usando Scripts de Google asociandolo con Google Sheet que tenemos de noticias existe un script el cual se ejecuta todos los dias y elimina las noticias que se hayan creado hace mas de 10 dias:

### 4.3. Flujo 3: Sincronización Bidireccional de Citas (`Integration Webhooks`)

![image.png](image%205.png)

Garantiza que las acciones realizadas por los usuarios en el panel web (Frontend) se reflejen en tiempo real en la agenda corporativa del equipo.

1. **Receptor (*Custom Webhook*):** Actúa como un *endpoint* que escucha peticiones POST enviadas desde el motor PHP de la página web. Recibe un *payload* con el `id_cita`, `nombre`, `email`, `fecha`, `hora` y la `accion` a realizar (crear o cancelar).
2. **Enrutamiento por Acción:**
    - **Alta de Citas:** Si la acción es `crear`, el módulo `google-calendar:createAnEvent` bloquea el espacio en la agenda, genera una sala virtual de Google Meet e invita al cliente. Paralelamente, se envía la alerta "🚀 ¡Nueva reserva recibida!" por Telegram. La descripción del evento de Google incluye un código trazador oculto (`ID_SISTEMA: id_cita`).
    - **Baja de Citas:** Si el usuario cancela desde su panel, la variable `cancelar` activa otra ruta. El flujo utiliza `google-calendar:searchEvents` para buscar en el calendario el `ID_SISTEMA` exacto de esa reunión y ejecuta una orden de borrado (`google-calendar:deleteAnEvent`), liberando el hueco en la agenda y alertando al equipo vía Telegram ("⚠️ Cita Cancelada").
    

# 5. Motor Dinámico y Navegación

Uno de los mayores retos técnicos del proyecto ha sido evitar la creación manual de páginas estáticas para cada servicio. Para lograr una plataforma escalable, se ha desarrollado un motor dinámico basado en PHP que genera el contenido bajo demanda.

### 5.1. Enrutamiento Nativo y Slugs Dinámicos

Para garantizar la compatibilidad del proyecto con cualquier entorno de alojamiento (*hosting*) y no depender de reglas de reescritura de Apache (archivo `.htaccess`), el sistema de URLs amigables se ha programado íntegramente en PHP.

Se utiliza un sistema de enrutamiento "semi-amigable" mediante el paso de parámetros limpios (ej. `detail.php?slug=chatbot-whatsapp`). Para lograrlo, el sistema cuenta con una función propia `generateSlug()` que captura el título real del servicio desde la base de datos, lo convierte a minúsculas, elimina tildes y reemplaza los espacios por guiones en tiempo real.

Esto permite generar enlaces legibles que mejoran el SEO y la experiencia del usuario de forma dinámica.

### 5.2. Explorador del Catálogo (`/soluciones/index.php`)

La página principal del módulo actúa como un índice. Al cargar, el script ejecuta una consulta para obtener todos los servicios activos en la base de datos. Mediante un bucle, renderiza la cuadrícula de tarjetas (*Cards*), y aplica la función de *slugs* en el botón de "Ver detalles", construyendo las rutas de navegación automáticamente para cada solución.

### 5.3. Generación de la Vista y Tolerancia a Fallos (`detail.php`)

El archivo `detail.php` funciona como una plantilla maestra que se ensambla en milisegundos. Su mayor fortaleza arquitectónica es su **alta tolerancia a fallos**, implementada a través del siguiente flujo de ejecución:

1. **Captura y Saneamiento:** El script intercepta la variable `$_GET['slug']` y la limpia rigurosamente (`trim`, `preg_replace`) para evitar ataques de inyección.
2. **Motor de Búsqueda Dual:** * *Búsqueda Principal:* Realiza una consulta a la base de datos manipulando el campo título mediante SQL (`REPLACE(LOWER(title), ' ', '-') = ?`) para emparejarlo con el slug de la URL.
    - *Fallback Numérico:* Si la búsqueda anterior no arroja resultados y el parámetro recibido es numérico, el sistema tiene un mecanismo de rescate que busca directamente por el identificador único (`id = ?`). Si ambas fallan, redirige de forma segura al catálogo.
3. **Optimización SEO Dinámica:** Una vez obtenidos los datos, el script no solo rellena el HTML visible, sino que inyecta etiquetas meta automáticas (`<title>` y URLs canónicas) basándose en el servicio actual para optimizar la indexación en buscadores.
4. **Parseo Resiliente de Datos Estructurados:** El campo `caracteristicas_principales` se almacena en la base de datos en formato JSON. El script intenta deserializarlo (`json_decode`) para iterar sobre él. Si por algún motivo el formato JSON se corrompe en la base de datos, el código cuenta con un *fallback* automático que detecta el error y procesa la cadena como texto plano separado por comas (`explode`), garantizando que la página nunca colapse y siempre muestre la información al usuario.
5. **Renderizado Visual:** Finalmente, inyecta las animaciones GSAP correspondientes y los módulos reutilizables (Header, Timeline, FAQ y Footer).

# 6. Panel de Administración (Back-Office)

Para garantizar la escalabilidad operativa de SynkronyAI, se ha desarrollado un panel de control privado (*Back-Office*) desde el cual los administradores pueden gestionar todos los aspectos de la plataforma sin necesidad de interactuar directamente con la base de datos ni modificar el código fuente.

### 6.1. Seguridad y Control de Acceso Basado en Roles (RBAC)

El acceso al directorio `/admin/` está estrictamente restringido. Para evitar la duplicación de código y posibles vulnerabilidades por despistes humanos, se ha implementado un *middleware* de seguridad centralizado en el archivo principal `funciones.php`.

Este script verifica dos factores críticos antes de renderizar ninguna página: que exista una sesión activa (`loggedin`) y que el privilegio del usuario sea específicamente el de administrador (`user_role === 'admin'`). Si alguna de estas dos condiciones falla, se aborta la ejecución y se expulsa al usuario inmediatamente:

```jsx
/**
 * Middleware: Verifica si el usuario activo tiene privilegios de administrador.
 * Ubicación: /includes/funciones.php
 */
function check_admin_access() {
    // 1. Inicia o reanuda la sesión de forma segura
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    // 2. Verificación estricta de Autenticación y Autorización (RBAC)
    if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
        // Expulsión de seguridad en caso de acceso no autorizado
        header("Location: ../login.html");
        exit;
    }
}
```

Con tan solo requerir esta función en la cabecera de cada archivo privado (por ejemplo, añadiendo `require_once '../includes/funciones.php'; check_admin_access();`), todo el entorno de administración queda completamente blindado contra accesos no deseados.

### 6.2. Arquitectura de Gestión (Operaciones CRUD)

El panel se divide en módulos funcionales independientes que permiten realizar operaciones CRUD (*Create, Read, Update, Delete*) sobre las tablas principales del sistema. Esta separación de responsabilidades facilita el mantenimiento del código:

- **Gestión de Servicios (`admin_services.php`):** Permite dar de alta nuevas soluciones de IA. Los datos introducidos aquí (títulos, descripciones e imágenes subidas al directorio `/uploads/`) alimentan automáticamente el catálogo dinámico de la interfaz pública.
- **Gestión de Citas (`admin_dashboard_requests.php`):** Actúa como un CRM interno y bidireccional para visualizar, editar el estado o cancelar las reservas realizadas por los clientes.
- **Gestión de Usuarios (`admin_users.php`):** Interfaz para la administración de credenciales, permitiendo crear nuevas cuentas de acceso con contraseñas cifradas en formato *hash* o revocar accesos a antiguos empleados.

### 6.3. Motor de Métricas Asíncrono (API REST Interna)

El *Dashboard* principal no ejecuta consultas SQL pesadas durante la carga inicial de la página HTML, ya que esto penalizaría drásticamente el tiempo de respuesta del servidor (TTFB).

En su lugar, se ha diseñado el archivo `api_stats.php`, el cual funciona como una API REST interna (*Backend* puro). La interfaz carga de forma instantánea y, mediante peticiones asíncronas de JavaScript (Fetch/AJAX), solicita los datos a este archivo.

El script comprueba la seguridad, compila las métricas de la base de datos (como el volumen total de usuarios, el sumatorio de citas o la agrupación por estados) y devuelve un objeto JSON estructurado:

```php
<?php
// Ubicación: admin/api_stats.php (Fragmento del compilador de métricas)
header('Content-Type: application/json');
require_once '../includes/db_config.php';
require_once '../includes/funciones.php';

// 1. Capa de Seguridad de la API interna
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

$response = [];

try {
    // 2. Ejecución de consultas de agrupación (Ejemplo: Citas por Estado)
    $sql = "SELECT status, COUNT(*) as count FROM appointments GROUP BY status";
    $result = $conn->query($sql);
    
    $appt_labels = []; 
    $appt_data = []; 
    $appt_colors = [];
    
    // Mapeo dinámico de colores corporativos según estado
    $color_map = [
        'confirmed' => '#00ff99', 
        'cancelled' => '#FF6B6B', 
        'completed' => '#0077FF', 
        'pending'   => '#FFD700'
    ];

    while ($row = $result->fetch_assoc()) {
        $st = $row['status'];
        // Traducción al vuelo para la interfaz gráfica
        $label = ($st == 'confirmed') ? 'Confirmadas' : (($st == 'cancelled') ? 'Canceladas' : (($st == 'completed') ? 'Finalizadas' : $st));
        
        $appt_labels[] = $label;
        $appt_data[] = $row['count'];
        $appt_colors[] = $color_map[$st] ?? '#9F40FF';
    }

    // 3. Ensamblaje del nodo de datos para la librería gráfica
    $response['chart_appointments'] = [
        'labels' => $appt_labels,
        'data' => $appt_data,
        'colors' => $appt_colors
    ];
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// 4. Salida estándar de la API
echo json_encode($response);
?>
```

Este enfoque arquitectónico delega el renderizado al navegador del cliente (*Client-Side Rendering*), consumiendo el siguiente paquete de datos estructurado y logrando que el panel de administración sea extremadamente rápido, reactivo y profesional:

```json
{
    "total_users": 150,
    "total_appointments": 89,
    "upcoming_appointments": 12,
    "chart_appointments": {
        "labels": ["Confirmadas", "Canceladas", "Finalizadas", "Pendientes"],
        "data": [45, 12, 28, 4],
        "colors": ["#00ff99", "#FF6B6B", "#0077FF", "#FFD700"]
    }
}
```

# 7. Tareas de Mantenimiento Automatizado (Cron Jobs)

Para garantizar que la plataforma sea autosuficiente y mantenga un rendimiento óptimo a largo plazo, se han diseñado rutinas de mantenimiento en segundo plano. El objetivo de estas tareas es gestionar el ciclo de vida de los datos (*Data Lifecycle Management*), evitando la saturación del almacenamiento y la ralentización de las consultas.

### 7.1. Limpieza de Bases de Datos Externas (Google Apps Script)

Dado que los flujos de automatización inyectan datos continuamente en los repositorios de Google Sheets (como el catálogo dinámico de noticias), el documento crecería indefinidamente, lo que acabaría penalizando los tiempos de carga de la web al procesar archivos CSV demasiado pesados.

Para solucionarlo, se ha desarrollado un *script* nativo en **Google Apps Script** que actúa como un recolector de basura automatizado.

**Configuración del Disparador (*Time-driven Trigger / Cron Job*):**
Para no interferir con el tráfico de usuarios en la web ni generar bloqueos de lectura/escritura, el script está programado para ejecutarse diariamente en horario "valle", concretamente entre las **2:00 AM y las 3:00 AM**.

**Lógica de Ejecución:**
El código se conecta a la hoja de cálculo, evalúa las marcas de tiempo (*timestamps*) de cada fila y elimina cualquier registro que tenga una antigüedad superior a 10 días, manteniendo la base de datos siempre ligera y con información reciente.

JavaScript

```jsx
function limpiarNoticiasAntiguas() {
  // 1. Pega aquí la URL completa de tu hoja de Google Sheets (la que copias del navegador)
  const urlDocumento = "https://docs.google.com/spreadsheets/d/1Eg5J1rY82pgmzl_hLT_NfBHQGh5qypmqxWCuTe1iKpE/edit?gid=0#gid=0";
  
  // 2. Conectamos con el documento y la hoja específica
  const documento = SpreadsheetApp.openByUrl(urlDocumento);
  const hoja = documento.getSheetByName("Hoja 1");
  
  const datos = hoja.getDataRange().getValues();
  
  // 3. Calculamos la fecha límite (hace 10 días)
  const fechaLimite = new Date();
  fechaLimite.setDate(fechaLimite.getDate() - 10);
  
  // 4. Recorremos las filas desde abajo hacia arriba
  for (let i = datos.length - 1; i >= 1; i--) { 
    // Javascript lee el formato "2026-03-23T12:46:58.853Z" automáticamente
    let fechaFila = new Date(datos[i][4]); 
    
    // Si la fecha es válida y es anterior a la fecha límite, borra la fila
    if (!isNaN(fechaFila) && fechaFila < fechaLimite) {
      hoja.deleteRow(i + 1);
    }
  }
}
```

**Justificación Técnica:**
Un detalle arquitectónico crucial de este *snippet* es la estructura de su bucle (`for (let i = datos.length - 1; i >= 1; i--)`). Al eliminar elementos de una matriz o tabla, si el borrado se realiza de arriba hacia abajo, los índices se desplazan, provocando que el script se salte filas. Al programar la iteración en orden inverso (de abajo hacia arriba), se garantiza el borrado perfecto y seguro de los datos.

# 8. Integración de Asistente Virtual (Botpress)

Como parte de la propuesta de valor de **SynkronyAI**, la plataforma integra un agente conversacional inteligente diseñado para mejorar la retención de usuarios y automatizar la resolución de dudas frecuentes en tiempo real.

### 8.1. Arquitectura del Chatbot

El asistente ha sido desarrollado sobre la plataforma **Botpress**, aprovechando su motor de procesamiento de lenguaje natural (NLP) y su capacidad para gestionar flujos de diálogo complejos. La integración se realiza de forma asíncrona para no penalizar la velocidad de carga inicial del sitio web.

**Componentes de la integración:**

- **Motor de Inyección:** Se utiliza el SDK de Botpress (`inject.js`) para renderizar la interfaz del chat sobre el DOM de la página.
- **Script de Configuración:** El archivo externo referenciado contiene la lógica de personalidad, los disparadores (*triggers*) de bienvenida y la conexión con el modelo de lenguaje específico de la instancia.

### 8.2. Implementación técnica en el Frontend

Para asegurar que el widget esté disponible en toda la plataforma (Home, Catálogo y Detalles) sin replicar código, el script se ha insertado en el componente global de pie de página o en la cabecera, utilizando los atributos `defer` para optimizar el renderizado:

```html
<script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>

<script src="https://files.bpcontent.cloud/2026/03/18/14/20260318144925-98QS164S.js" defer></script>
```

### 8.3. Funcionalidades del Asistente

El bot no solo actúa como una interfaz de texto, sino que está configurado para:

1. **Cualificación de Leads:** Interactuar con el usuario para entender sus necesidades antes de redirigirlos al sistema de citas.
2. **Navegación Asistida:** Ayudar a los usuarios a encontrar servicios específicos dentro del catálogo dinámico.
3. **Disponibilidad 24/7:** Actuar como primer nivel de soporte, recogiendo datos de contacto incluso cuando el equipo técnico no está operativo, cerrando así el ciclo de automatización junto con los flujos de Make.com.

# 9. Despliegue y Arquitectura de Servidor

Para que **SynkronyAI** sea accesible desde cualquier parte del mundo, se ha realizado el despliegue de la aplicación en un entorno de producción real, configurando la infraestructura necesaria para soportar la arquitectura híbrida de la plataforma.

### 9.1. Entorno de Alojamiento (Hosting)

El proyecto utiliza un entorno de servidor basado en **LAMP** (Linux, Apache, MySQL, PHP). Se ha seleccionado el proveedor **InfinityFree** para el despliegue inicial, cuyas características técnicas garantizan el cumplimiento de los requisitos del sistema:

- **Servidor Web:** Apache 2.4.
- **Intérprete de Backend:** PHP 7.2 / 8.x.
- **Motor de Base de Datos:** MariaDB 11.4 (compatible con MySQL).
- **Gestión de Archivos:** Protocolo FTP seguro para la transferencia del código fuente y recursos multimedia.

### 9.2. Configuración del Servidor de Base de Datos

La base de datos relacional se aloja en el servidor `sql101.infinityfree.com`. Para la conexión desde el código PHP, se ha configurado un archivo de credenciales protegido (`db_config.php`) que utiliza variables de entorno para evitar la exposición de datos sensibles.

**Parámetros de conexión implementados:**

```php
<?php
// includes/db_config.php
$servername = "sql101.infinityfree.com";
$username = "if0_40917535";
$password = "**********"; // Protegido
$dbname = "if0_40917535_skynkrony";

// Conexión segura con soporte para caracteres especiales (utf8mb4)
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
```

### 9.3. Gestión de Dominio y SSL

La plataforma es accesible bajo el dominio **`synkrony-ai.xo.je`**.

1. **Enrutamiento DNS:** Se han configurado los registros tipo A y CNAME para apuntar el tráfico del dominio hacia las direcciones IP de la infraestructura de hosting.
2. **Seguridad de Capa de Transporte (SSL):** Para proteger el envío de datos de los usuarios en los formularios de registro y reserva de citas, se ha implementado un certificado SSL. Esto garantiza que toda la comunicación entre el navegador del cliente y el servidor viaje cifrada bajo el protocolo **HTTPS**.

### 9.4. Integración Multicanal (Ecosistema Final)

A diferencia de una página web tradicional, el despliegue de SynkronyAI constituye un ecosistema conectado:

- **Frontend & Backend:** Servidor Apache (InfinityFree).
- **Automatización:** Cloud de Make.com.
- **Almacenamiento Auxiliar:** Google Drive (API).
- **Comunicación:** Servidores de Telegram y SMTP de Gmail.
- **Inteligencia Artificial:** API de Groq (Llama 3) y Cloud de Botpress.
