# Plantilla arc42 – Proyecto Monitorías UTB

**Fecha:** Enero 2023  
**Autores:** Dr. Gernot Starke, Dr. Peter Hruschka y otros contribuyentes  
**Versión de la plantilla:** 7.0 ES (asciidoc)

---

## Introducción y Metas

En la Universidad Tecnológica de Bolívar se busca optimizar el rendimiento académico de sus estudiantes mediante la integración de un módulo en la plataforma SAVIO.  
Este módulo identificará estudiantes con calificaciones reprobatorias y les sugerirá sesiones de monitoría en horarios disponibles, gestionando la notificación tanto para el estudiante como para el monitor.

**Meta:**  
Desarrollar un módulo de notificación y asignación de monitorías en SAVIO que, mediante integración con Banner, automatice la identificación de estudiantes en riesgo académico y la gestión de sus sesiones de monitoría.

---

## Vista de Requerimientos

- Requisitos clave del módulo de monitorías.
- Requerimientos funcionales y no funcionales.
- Integraciones con otras plataformas educativas.

---

## Metas de Calidad

- Alta disponibilidad y escalabilidad.
- Facilidad de uso para monitores y estudiantes.
- Seguridad en el manejo de información de los usuarios.

---

## Partes Interesadas

| Rol/Nombre              | Contacto                   | Expectativas                                                        |
|-------------------------|----------------------------|----------------------------------------------------------------------|
| Yuraniz Henriquez Nuñez | yhenriquez@utb.edu.co      | Desarrollo de módulo de solicitud de monitorías según desempeño académico |

---

## Restricciones de la Arquitectura

- Uso de tecnologías compatibles con infraestructura universitaria.
- Cumplimiento con normativas de privacidad y seguridad de datos.
- Interoperabilidad con LMS existentes.

---

## Alcance y Contexto del Sistema

### Contexto de Negocio

- Gestión eficiente de monitoreos académicos.
- Comunicación fluida entre estudiantes y monitores.
- Registro y análisis de sesiones de monitoría.

### Interfaces de Dominio Externo

- **Estudiante:** Interfaz web, recordatorios por correo.
- **Profesor/Tutor:** Scheduler para definir y gestionar citas.
- **Administrador:** Docker y Azure Portal.
- **Sistema Académico:** API REST/CSV para sincronización de notas.

### Contexto Técnico

#### Mapeo de Entrada/Salida

| Componente        | Dirección | Canal     | Protocolo/Puerto | Propósito                             |
|------------------|-----------|-----------|------------------|---------------------------------------|
| Navegador Web    | Entrada   | HTTP/HTTPS| TCP 80/443       | Acceso a Moodle                       |
| Contenedor PHP   | Salida    | SMTP      | TCP 587          | Envío de recordatorios                |
| Contenedor PHP   | E/S       | MySQL     | TCP 3306         | Operaciones base de datos            |
| Azure Backup     | Salida    | HTTPS     | 443              | Backup diario de BD                  |
| Plugin Scheduler | Interno   | Eventos   | -                | Reserva de slots                     |
| Bloque Monitorías| Interno   | API       | -                | Consulta notas/agendamiento          |

#### Explicación de Interfaces Técnicas

- Seguridad vía TLS
- Balanceo de carga
- mysqli_connect() y variables de entorno
- OAuth2 para SMTP
- Backups automáticos (Azure CLI)

---

## Vista de Bloques

### Nivel 1: Sistema General (Caja Blanca)

#### Motivación

- Dockerización de Moodle, MySQL y dependencias.
- Extensión vía bloque personalizado + plugin scheduler.
- Automatización de citas y recordatorios.

#### Bloques de construcción

- **Caja Negra 1: Contenedor PHP (Moodle)**  
  Ejecuta lógica del bloque, interfaz web, plugin. Usa OPcache, escalado horizontal.

- **Caja Negra 2: Contenedor MySQL**  
  Almacena datos de Moodle, usa volúmenes Docker, backups diarios.

- **Caja Negra 3: Servidor de Correo Externo**  
  Envía recordatorios. Baja latencia.

#### Interfaces importantes

- PHP ↔ MySQL (consultas SQL)
- PHP ↔ SMTP (phpmailer)
- Bloque ↔ Plugin scheduler (API)

---

### Nivel 2: Caja Blanca del Bloque de Monitorías

- **Lógica de negocio**: verifica notas, agenda citas.
- **API del scheduler**: disponibilidad y reserva.
- **Envío de recordatorios**: cron.php, eventos Moodle.

---

## Visibilidad de Building Blocks

### Visión General

- **Gestor académico:** obtiene estudiantes con bajo rendimiento.
- **Gestor de sesiones:** administra toda la agenda.
- **Notificaciones:** correo a estudiantes y monitores.
- **Interfaz de usuario:** página SAVIO.
- **BD del módulo:** historial de sesiones.

### Descomposición de bloques

#### Subcomponentes

1. Validador de solicitud  
2. Planificador de horarios  
3. Asignador de monitor  
4. Registro de sesión  
5. Generador de reportes

---

### Nivel 3: Caja Blanca del Planificador de Horarios

- **Consultor de disponibilidad:** horarios de monitores.  
- **Compatibilidad:** filtra restricciones del estudiante.  
- **Optimización:** afinidad temática, fechas cercanas.

---

## Vista de Ejecución

**Escenario 1:** Registro de monitoreo  
Estudiante agenda, sistema asigna, se notifica.

**Escenario 2:** Realización de monitoreo  
Monitor registra asistencia y observaciones.

---

## Vista de Despliegue

### Nivel 1

- Contenedores separados (PHP/MySQL)
- Alta disponibilidad y rendimiento
- Mapas de bloques a contenedores

### Nivel 2

- Contenedor PHP: imagen `php:7.4-apache`
- Contenedor MySQL: con variables de entorno

---

## Conceptos Transversales

### Seguridad

- Autenticación institucional (SSO)
- Accesos diferenciados
- Validación de permisos

### Escalabilidad

- Servicios desacoplados escalables
- Optimización DB

### Integración con APIs

- Banner (rendimiento académico)
- Savio (sesiones, disponibilidad)
- Documentación OpenAPI

### Manejo de errores y logs

- Códigos estándar HTTP
- Centralización con ELK, Azure Monitor, etc.
- Alertas automáticas

---

## Decisiones de Diseño

- **Escalabilidad:** múltiples sesiones concurrentes  
- **Disponibilidad:** tolerancia a fallos  
- **Seguridad:** cifrado y autenticación robusta  

### Árbol de Calidad

| Atributo     | Estímulo                            | Respuesta esperada         | Métrica                      |
|--------------|-------------------------------------|-----------------------------|------------------------------|
| Funcionalidad| Agendamiento exitoso                | Cita en DB                  | 100% confiable               |
| Usabilidad   | Recordatorio automático             | Correo enviado              | < 1 min latencia             |
| Rendimiento  | Alta concurrencia (100+ solicitudes)| < 2 seg respuesta           | 95% de las peticiones        |

---

## Evaluación SAAM (Modificabilidad)

- Escenarios directos: cambiar umbral, añadir campos → impacto bajo/medio.
- Escenarios indirectos: cambiar SMTP → impacto alto.
- **Conclusión:** Alta modificabilidad en lógica, cuidado con integraciones externas.

---

## Riesgos y Deuda Técnica

- Dependencia de servicios externos
- Problemas en picos de uso
- Compatibilidad con móviles antiguos

---

## Glosario

| Término              | Definición                                                                 |
|----------------------|---------------------------------------------------------------------------|
| Monitor              | Estudiante que guía sesiones académicas                                   |
| Sesión               | Espacio programado entre monitor y estudiante                             |
| Reporte              | Registro formal de la sesión                                               |
| SAVIO                | Plataforma educativa basada en Moodle                                     |
| Bloque de Monitorías | Módulo personalizado de Moodle para agendar sesiones                      |
| Plugin Scheduler     | Complemento de Moodle que gestiona disponibilidad horaria                 |
| API Banner           | Servicio que consulta información académica de estudiantes                |
| SMTP                 | Protocolo de envío de correos                                              |
| Docker               | Plataforma para contenedores (PHP, MySQL)                                 |
| Azure                | Plataforma en la nube donde se aloja el sistema                           |

---

