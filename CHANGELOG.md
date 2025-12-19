# Changelog
Todos los cambios relevantes de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/)
y este proyecto sigue versionado semántico.

---

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
