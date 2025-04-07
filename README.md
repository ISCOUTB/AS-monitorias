# AS-monitorias
# 

**Acerca de arc42**

arc42, La plantilla de documentación para arquitectura de sistemas y de
software.

Por Dr. Gernot Starke, Dr. Peter Hruschka y otros contribuyentes.

Revisión de la plantilla: 7.0 ES (basada en asciidoc), Enero 2017

© Reconocemos que este documento utiliza material de la plantilla de
arquitectura arc42, <https://www.arc42.org>. Creada por Dr. Peter
Hruschka y Dr. Gernot Starke.

# Introducción y Metas {#section-introduction-and-goals}
En la Universidad Tecnológica de Bolívar se busca optimizar el rendimiento académico de sus 
estudiantes mediante la integración de un módulo en la plataforma institucional SAVIO. Este 
módulo se encargará de identificar a los estudiantes con calificaciones reprobatorias y 
sugerirles sesiones de monitoría en horarios disponibles, gestionando la notificación tanto 
para el estudiante como para el monitor asignado.
Meta.
Desarrollar un módulo de notificación y asignación de monitorías en SAVIO que, mediante la 
integración con Banner, permita automatizar la identificación de estudiantes en riesgo 
académico y la gestión de sus sesiones de monitoría. 

## Vista de Requerimientos {#_vista_de_requerimientos}
- Descripción de los requisitos clave del Módulo de Monitorías.
- Requerimientos funcionales y no funcionales.
- Integraciones con otras plataformas educativas.

## Metas de Calidad {#_metas_de_calidad}
- Alta disponibilidad y escalabilidad.
- Facilidad de uso para monitores y estudiantes.
- Seguridad en el manejo de información de los usuarios.

## Partes interesadas (Stakeholders) {#_partes_interesadas_stakeholders}

+-------------+---------------------------+---------------------------+
| Rol/Nombre  | Contacto                  | Expectativas              |
+=============+===========================+===========================+
| *\<Role-1>* | *\<Contact-1>*            | *\<Expectation-1>*        |
+-------------+---------------------------+---------------------------+
| *\<Role-2>* | *\<Contact-2>*            | *\<Expectation-2>*        |
+-------------+---------------------------+---------------------------+

# Restricciones de la Arquitectura {#section-architecture-constraints}
- Uso de tecnologías compatibles con la infraestructura universitaria.
- Cumplimiento con normativas de privacidad y seguridad de datos.
- Interoperabilidad con LMS existentes.
# Alcance y Contexto del Sistema {#section-context-and-scope}

## Contexto de Negocio {#_contexto_de_negocio}
- Proporcionar un sistema eficiente para la gestión de monitoreos académicos.
- Facilitar la comunicación entre estudiantes y monitores.
- Permitir el registro y análisis de sesiones de monitoreo.

**\<Diagrama o Tabla>**

**\<optionally: Explanation of external domain interfaces>**

## Contexto Técnico {#_contexto_t_cnico}

**\<Diagrama o Tabla>**

**\<Opcional: Explicación de las interfases técnicas>**

**\<Mapeo de Entrada/Salida a canales>**

# Estrategia de solución {#section-solution-strategy}

# Vista de Bloques {#section-building-block-view}

## Sistema General de Caja Blanca {#_sistema_general_de_caja_blanca}

***\<Diagrama general>***

Motivación

:   *\<Explicación en texto>*

Bloques de construcción contenidos

:   *\<Desripción de los bloques de construcción contenidos (Cajas
    negras)>*

Interfases importantes

:   *\<Descripción de las interfases importantes>*

### \<Caja Negra 1> {#__caja_negra_1}

*\<Propósito/Responsabilidad>*

*\<Interfase(s)>*

*\<(Opcional) Características de Calidad/Performance>*

*\<(Opcional) Ubicación Archivo/Directorio>*

*\<(Opcional) Requerimientos Satisfechos>*

*\<(Opcional) Riesgos/Problemas/Incidentes Abiertos>*

### \<Caja Negra 2> {#__caja_negra_2}

*\<plantilla de caja negra>*

### \<Caja Negra N> {#__caja_negra_n}

*\<Plantilla de caja negra>*

### \<Interfase 1> {#__interfase_1}

...

### \<Interfase m> {#__interfase_m}

## Nivel 2 {#_nivel_2}

### Caja Blanca *\<bloque de construcción 1>* {#_caja_blanca_emphasis_bloque_de_construcci_n_1_emphasis}

*\<plantilla de caja blanca>*

### Caja Blanca *\<bloque de construcción 2>* {#_caja_blanca_emphasis_bloque_de_construcci_n_2_emphasis}

*\<plantilla de caja blanca>*

...

### Caja Blanca *\<bloque de construcción m>* {#_caja_blanca_emphasis_bloque_de_construcci_n_m_emphasis}

*\<plantilla de caja blanca>*

## Nivel 3 {#_nivel_3}

### Caja Blanca \<\_bloque de construcción x.1\_\> {#_caja_blanca_bloque_de_construcci_n_x_1}

*\<plantilla de caja blanca>*

### Caja Blanca \<\_bloque de construcción x.2\_\> {#_caja_blanca_bloque_de_construcci_n_x_2}

*\<plantilla de caja blanca>*

### Caja Blanca \<\_bloque de construcción y.1\_\> {#_caja_blanca_bloque_de_construcci_n_y_1}

*\<plantilla de caja blanca>*

## visibilidad en building blocks
#### Estructura propuesta
#### Nivel 1 Visión general (caja blanca del sistema completo)
#### Sistema: módulo de solicitud de monitorias

##### Bloque principal:
#####  Gestor de identificación académica
- Se conecta con banner y obtiene estudiantes con bajo rendimiento.
##### Gestor de sesiones de monitoria
- Administra la solicitud, programación y seguimiento de sesiones.
##### Notificación de usuario 
- Envía correo/aviso a estudiantes y monitores
##### Interfaz de usuario 
- Página en savio donde el estudiante y el monitor interactúan
##### Base de datos del modulo
- Guardar historial de sesiones, observaciones y estado
------------
##### Descomposición de bloques
Por ejemplo, gestor de sesiones de monitoria
- Validador de solicitud 
-  Planificador de horarios 
- Asignado de monitor 
- Generador reportes de sesión 
Componentes internos de un bloque 
##### Validador de solicitud
- Componente que verifica si el estudiante está habilitado 
- Componentes que verifica disponibilidad de monitores 
- Aplicar la lógica de regla académica, ejemplo, nota :<3.0.

- Un punto importante relaciones entre los bloques
El gestor de académico actuaria de forma periódica para detectar estudiantes con bajo rendimiento.
- El gestor de sesiones se comunica con el servidor de notificaciones y con la base de datos para almacenar y enviar información.

------------
## (Nivel 2) Gestor de sesiones de monitoria
El propósito de este gestor es administrar el ciclo de las sesiones de monitorias: hablamos desde las solicitudes hasta el registro de la sesión realizada.
#### Subcomponentes internos
##### 1.Validador de la solicitud
- Verifica si el estudiante está habilitado un ejemplo de esto sería si esta generado por un gestor académico.
- Se asegura que el estudiante no tenga sesiones en conflicto.
##### 2.Planifica horarios
- Consulta los horarios disponibles de los monitores.
- Presenta al estudiante opciones compartibles
##### Asignar monitor
- Selecciona un monitor disponible y que este apto para llevar acabo la asignatura.
- Puede notificar de forma automática la asignación
##### 4.Registro de sesión 
- Puede capturar las observaciones y asistencia por el monitor 
##### 5.Gestor de reporte 
- Puede realizar informe por estudiante, asignatura y monitor.

###### Caja blanca nivel 3 Planificador de horarios 
- Con este planificador nos va ayudar a encontrar y dar sugerencia de los mejores horarios posibles para la monitoria, también debe considerar la disponibilidad del monitor, los cursos y si el estudiante tiene alguna restricción.

##  Subcomponentes
##### Consultor de disponibilidad
- Extrae los horarios disponibles de los monitores desde la base de datos.
Compatibilidad
- Se encarga de filtrar la disponibilidad que cuenta el estudiante en los horarios del monitor.
Optimización
- Toma prioridad con los resultados, ejemplo cercanía de fecha, afinidad temática.

# Vista de Ejecución {#section-runtime-view}
## \<Escenario de ejecución n> {#__escenario_de_ejecuci_n_n}
## **Escenario de ejecución 1: Registro de monitoreo**
1. Un estudiante inicia sesión en la plataforma.
2. Solicita una sesión de monitoreo en una materia específica.
3. El sistema asigna un monitor y agenda la sesión.
4. Se notifica al estudiante y al monitor.

## **Escenario de ejecución 2: Realización de monitoreo**
1. El monitor inicia la sesión en el sistema.
2. Conduce la sesión con el estudiante.
3. Registra observaciones y asistencia.
4. Se almacena el informe de la sesión en la base de datos.

# Vista de Despliegue {#section-deployment-view}


## Nivel de infraestructura 1 {#_nivel_de_infraestructura_1}

***\<Diagrama General>***

Motivación

:   *\<Explicación en forma textual>*

Características de Calidad/Rendimiento

:   *\<Explicación en forma textual>*

    Mapeo de los Bloques de Construcción a Infraestructura

    :   *\<Descripción del mapeo>*

## Nivel de Infraestructura 2 {#_nivel_de_infraestructura_2}

### *\<Elemento de Infraestructura 1>* {#__emphasis_elemento_de_infraestructura_1_emphasis}

*\<diagrama + explicación>*

### *\<Elemento de Infraestructura 2>* {#__emphasis_elemento_de_infraestructura_2_emphasis}

*\<diagrama + explicación>*

...

### *\<Elemento de Infraestructura n>* {#__emphasis_elemento_de_infraestructura_n_emphasis}

*\<diagrama + explicación>*

# Conceptos Transversales (Cross-cutting) {#section-concepts}

## *\<Concepto 1>* {#__emphasis_concepto_1_emphasis}

*\<explicación>*

## *\<Concepto 2>* {#__emphasis_concepto_2_emphasis}

*\<explicación>*

...

## *\<Concepto n>* {#__emphasis_concepto_n_emphasis}

*\<explicación>*

## Conceptos Cross-Cutting 
#### Seguridad 
- Para dar mayor seguridad utilizar autenticación todo el acceso se realiza mediante una autenticación de la institución en savio, usar un single-On.
- Acceso diferenciado para los estudiantes, monitores, coordinadores. Se validan permisos en cada solicitud al backend.

####  Escalabilidad
- Los componentes asignación, planificación y notificación funcionan como servicio desacoplado lo que permite escalar de manera independiente. 
- Para las bases de datos y tener optimización un índice de particiones y consultas eficiente para sesiones y usuarios activos.

#### Integración con APIs
- API en banner, hace consultas del rendimiento académico 
- API interno en savio registra sesiones, puede gestionar disponibilidad, obtener información de los estudiantes.
- Documentación de APIs rest las interacciones que estén expuesta cuentan con documentación OpenAPI.
#### Manejo de errores y logging 
###### Gestión centralizada de errores
- Repuesta estandarizadas con código HTTP con códigos ejemplo 400 para errores de solicitud, 500 para fallos del sistema.
- Captura de errores de validación y errores inesperados.
#### Sistema de logs 
- Registro de eventos importantes, inicio de sesión, solicitud de monitorias, fallos.
- Uso de herramientas como ELK Stack, servidores en la nube. Azure monitor, AWS cloudwatch, esto es para centralizar y visualizar.
- Una alerta automática en caso de una posible falla enviar una alerta al equipo de soporte.

# Decisiones de Diseño {#section-design-decisions}

# Requerimientos de Calidad {#section-quality-scenarios}
- **Escalabilidad:** Soporte para múltiples sesiones concurrentes.
- **Disponibilidad:** Tolerancia a fallos mediante infraestructura en la nube.
- **Seguridad:** Cifrado de datos y autenticación robusta.
## Árbol de Calidad {#__rbol_de_calidad}

## Escenarios de calidad {#_escenarios_de_calidad}

# Riesgos y deuda técnica {#section-technical-risks}
- Dependencia de servicios externos.
- Posibles problemas de escalabilidad en picos de uso.
- Compatibilidad con dispositivos móviles más antiguos.

# Glosario {#section-glossary}

| Término          | Definición                                    |
|------------------|-----------------------------------------------|
| Monitor          | Estudiante encargado de guiar sesiones.       |
| Sesión           | Espacio de apoyo académico para estudiantes.  |
| Reporte          | Registro de una sesión de monitoreo.          |
