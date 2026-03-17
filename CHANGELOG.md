# Changelog
Todos los cambios relevantes de este proyecto serán documentados en este archivo.
---
## [2.1.0] 2026-03-13

- Se agrega funcion para cambio de idioma INGLES - ESPAÑOL
- Se mejora la lectura del log, de ultimas estaciones escuchadas
- Se corrigen textos no traduccidos de en version anterior
- Se cambia boton de donacion PAYPAL a MIEMBROS YOUTUBE
- Se corrige sidebar en visualizacion movil
- Correcciones de seguridad
- Se corrigen permisos de update al actualizar

---
## [2.0.0] - 2025-12-23
- Disponible como release 2.0 se obtiene con la actualizacion via luxlink fusion
- Se agrega funcion para cambio de idioma INGLES - ESPAÑOL
- Se mejora la lectura del log, pudiendo mantener el historial la actividad y podio
- Mejoras visuales en formularios
  

## [1.2.1] - 2025-12-22
- Se agrega opcion para cambio de sistema metrico en temperatura y velocidad
  - Seleccion grados C, grados F, velocidad de vientos Km/h y mph, respectivamente (sugerencia de JOEY WP4MVR)
- Se agrega version.json para llevar el control de las versiones
- En pie de index.php se refleja version del sistema actual.
- Boton de actualizacion automatica sin necesidad de cargar el codigo nuevamente

  ## NOTA
si estan en una version previa o no tienes la funcion de actualizar
debes volver a cargar el codigo manual para tener las mejoras
  
## [1.2.0] - 2025-12-19

### 🔐 Seguridad
- Se eliminó la contraseña hardcodeada del código.
- Se implementó autenticación mediante hash seguro usando `config_seguridad.json`.
- Se agregó control de sesión (`$_SESSION['acceso_configuracion']`) en todas las páginas críticas.
- Se protegieron los endpoints sensibles contra accesos directos por URL.
- Se agregó confirmación de seguridad para reinicio del servidor.

### 🧩 Configuración
- Se agregó cierre de sesión (logout) desde la interfaz web.
- Se protegió la página de personalización con login.
- Se unificó el sistema de autenticación entre configuración y personalización.

### 🛠 Backend
- Se blindó `accion_servicio.php` contra ejecución externa.
- Se protegió `guardar_personalizacion.php` con validación de sesión y método POST.
- Se reforzó la validación de subida de archivos (banner).

### 🐞 Correcciones
- Se corrigieron accesos no autenticados a páginas de administración.
- Se evitó la pérdida de configuración previa al guardar personalización.

---

## [1.1.0] - 2025-11-30
### Added
- Panel web inicial para LuxLink Fusion.
