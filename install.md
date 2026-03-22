# **Dashboard Web para Reflector YSF**

🇪🇸 Español | 🇺🇸 [English](install.en.md)

---

## 🖥️ Requisitos

***Debe tener DVREFLECTOR de NOSTAR instalado y en funcionamiento***
https://github.com/nostar/DVReflectors

Si ya lo tienes instalado y funcionando, puedes ir directamente a la **instalación del DASHBOARD**.
Si estás comenzando desde cero, puedes seguir el paso a paso apoyándote en el video.

⚠️ **PRECAUCIÓN:**
Si ya tienes un dashboard web en funcionamiento, se recomienda realizar un **backup**, o instalar este dashboard en paralelo para probarlo antes.

Por ejemplo, puedes instalarlo en:

```
/var/www/html/ysf/
```

De esta forma no perderás tu instalación actual. Si es de tu gusto, luego puedes reemplazar completamente el sistema anterior.

---

## ℹ️ Información Importante

Si notas diferencias entre los comandos mostrados en este documento y los videos de instalación:

👉 Se debe a mejoras realizadas con el tiempo
👉 Estas mejoras no afectan el funcionamiento del sistema

El sistema está diseñado para operar de forma estable y sin problemas. aplicando estos comandos instalaras la version 1, una vez instalada puedes actualizar desde la web luxlink fusion

---

## 💻 Hardware recomendado

### Requisitos mínimos

* CPU: Dual Core 1.2 GHz o superior (Intel Atom / Celeron)
* RAM: 1 GB mínimo (2 GB recomendado)
* Almacenamiento: 8 GB (SD o HDD)
* Red: Ethernet 100 Mbps o Wi-Fi b/g/n
* Sistema Operativo: Debian, Ubuntu Server, Raspbian, Armbian

✔ Recomendado: Raspberry Pi 3 o superior

---

## 🧠 Sistemas compatibles

YSF REFLECTOR ha sido probado y funciona de forma óptima en:

* Debian 12+
* Raspbian 12
* Raspberry Pi OS
* Ubuntu Server
* Armbian (Bookworm)

Equipo recomendado: computador o mini-servidor con Linux

---

## 📦 Software necesario

* Apache2
* PHP 8.2 o superior
* Git
* cURL
* nmcli

### 🔧 Herramientas para configuración

* IP Scanner → para identificar la IP del equipo
* PuTTY → para administrar Linux vía SSH

👉 Para Raspberry Pi se recomienda usar **Raspberry Pi Imager**

---

# 📡 Instalación del Reflector YSF (DVReflector)

## 👤 Creación de usuario

```bash
sudo adduser ysfreflector
sudo usermod -aG sudo ysfreflector
```

---

## 🔄 Actualizar sistema e instalar dependencias

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install git build-essential cmake -y
sudo apt install jq -y
```

---

## ⬇️ Descargar DVReflector

```bash
cd /opt
sudo git clone https://github.com/nostar/DVReflectors.git
sudo chmod -R 755 DVReflectors
cd DVReflectors/YSFReflector
```

---

## ⚙️ Compilar

```bash
cd /opt/DVReflectors/YSFReflector
sudo make clean
sudo make -j4
```

---

## 📁 Copiar archivo de configuración

```bash
sudo cp /opt/DVReflectors/YSFReflector/YSFReflector.ini /etc/YSFReflector.ini
```

---

## 📂 Crear carpeta de logs

```bash
sudo mkdir -p /var/log/YSFReflector
sudo chmod 777 /var/log/YSFReflector
```

---

## ⚙️ Configurar /etc/YSFReflector.ini

```bash
sudo nano /etc/YSFReflector.ini
```

```bash
[General]
Daemon=0

[Log]
DisplayLevel=1
FileLevel=1
FilePath=/var/log/YSFReflector
FileRoot=YSFReflector
FileRotate=1

[Network]
Port=42000
Debug=0
```

---

## 🔧 Crear servicio systemd (inicio automático)

```bash
sudo nano /etc/systemd/system/ysfreflector.service
```

```bash
[Unit]
Description=YSF Reflector
After=network.target

[Service]
User=ysfreflector
ExecStart=/opt/DVReflectors/YSFReflector/YSFReflector /etc/YSFReflector.ini
Restart=always

[Install]
WantedBy=multi-user.target
```

---

## 🔐 Configurar permisos sudo

```bash
sudo nano /etc/sudoers.d/99-www-data-ysf
```

```bash
www-data ALL=NOPASSWD: /bin/systemctl restart ysfreflector.service
www-data ALL=NOPASSWD: /bin/systemctl start ysfreflector.service
www-data ALL=NOPASSWD: /bin/systemctl stop ysfreflector.service
www-data ALL=NOPASSWD: /sbin/reboot
www-data ALL=(ALL) NOPASSWD: /usr/bin/nmcli, /usr/sbin/ip, /bin/systemctl
www-data ALL=(ALL) NOPASSWD: /sbin/iwlist
```

---

## ▶️ Iniciar y habilitar servicio

```bash
sudo systemctl daemon-reload
sudo systemctl enable ysfreflector
sudo systemctl start ysfreflector
sudo systemctl status ysfreflector
```

---

# 📦 Instalación del Dashboard (LuxLink Fusion)

## 🧰 Instalar dependencias

```bash
sudo apt update
sudo apt install apache2 -y
sudo apt install php libapache2-mod-php -y
sudo apt install php-curl unzip -y
sudo apt install network-manager -y
sudo apt install git -y
```

---

## 📁 Instalar dashboard

```bash
cd /var/www/
sudo rm -rf /var/www/html
sudo git clone https://github.com/telecov/LUXLINK-FUSION.git html
```

---

## 🔐 Permisos

```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chown www-data:www-data /var/www/html/monitor_log.php
sudo chmod 775 /var/www/html/monitor_log.php
sudo chown root:root /var/www/html/includes/update.sh
sudo chmod 750 /var/www/html/includes/update.sh
```

---

## ⚙️ Permitir actualizaciones

```bash
sudo visudo
```

Agregar al final:

```bash
www-data ALL=(root) NOPASSWD: /var/www/html/includes/update.sh
```

---

## 🤖 Servicio Telegram en tiempo real

```bash
sudo nano /etc/systemd/system/luxlink-monitor.service
```

```bash
[Unit]
Description=LuxLink Fusion - Monitor de conexiones YSFReflector
After=network.target

[Service]
ExecStart=/usr/bin/php /var/www/html/monitor_log.php
Restart=always
User=www-data
Group=www-data
StandardOutput=append:/var/log/luxlink_monitor.log
StandardError=append:/var/log/luxlink_monitor_error.log

[Install]
WantedBy=multi-user.target
```

---

## ▶️ Activar servicio

```bash
sudo systemctl daemon-reload
sudo systemctl enable luxlink-monitor.service
sudo systemctl start luxlink-monitor.service
sudo systemctl status luxlink-monitor.service
```

---

## 🌐 Acceso Web

Accede desde tu navegador:

```
http://tu-servidor/
```

Contraseña por defecto:

```bash
luxlink2024
```

---

## ⚙️ Desde LuxLink podrás configurar

* Nombre del sistema o reflector
* Dirección IP del reflector
* Puerto y descripción
* Estado de enlace y estadísticas

---

## 💬 Telegram

* Activar o desactivar notificaciones

### Configuración (opcional):

* Crear un bot en @BotFather

* Obtener el token HTTP API

* Crear un canal o agregar el bot como administrador en un grupo

* Obtener el ID del grupo/canal:
  https://api.telegram.org/bot/getUpdates

* Asociar grupo o canal

✔ Controlar mensajes automáticos de actividad o errores

---

## 🎨 Apariencia y encabezado

* Cambiar logos, íconos y textos principales
* Personalizar colores o imagen de fondo

