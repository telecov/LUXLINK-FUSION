**Dashboard Web para Reflector YSF**  

---

🖥️ Requisitos
*** Tener instalado y corriendo DVREFLECTOR de NOSTAR ***
https://github.com/nostar/DVReflectors

si ya lo tienes instalado y funcionando, puedes saltar directamente a la instalacion del DASHBOARD, ahora si estas iniciando puedes seguir el paso a paso apoyando de este video

PRECAUCION, si ya tienes un dashboard web funcionando te recomiendo realizar backup, o instalar este dashboard paralelo para que lo pruebes antes, por ejemplo guardarlo en html/ysf/ para asi no perder lo que tienes en html, si es de tu gusto puede eliminar todo y seguir el procedimieto 

###

* Hardware recomendado:

* Requisitos mínimos:
CPU: Dual Core 1.2 GHz o superior (Intel Atom / Celeron)
RAM: 1 GB mínimo (2 GB recomendado)
Almacenamiento: 8 GB (SD o HDD)
Red: Ethernet 100 Mbps o Wi-Fi b/g/n
SO: Debian, Ubuntu Server, Raspbian, Bannanian 

* Raspberry PI 3 

YSF REFLECTOR ha sido probado y funciona de forma óptima en:

Distribución recomendada: Debian 12+ / Raspbian 12
Entornos compatibles: Raspberry Pi OS, Ubuntu Server, Armbian (bookwoorm)
Equipo recomendado: Computador o mini-servidor con Linux

Software necesario:

Apache2
PHP 8.2 o superior
Git
cURL
nmcli

** Software necesario para configurar **
IPSCANNER - para identificar ip de equipo
PUTTY - para administrar Linux por SSH

Para instalar en Raspberry OS se recomieda Raspberry pi Imager


📡 Instalación del Reflector YSF (DVReflector)

Creacion de usuario

```bash
sudo adduser ysfreflector
sudo usermod -aG sudo ysfreflector
```

Actualizar repositorios e instalar 

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install git build-essential cmake -y
```

Descargar DVReflector YSF

```bash
cd /opt
sudo git clone https://github.com/nostar/DVReflectors.git
sudo chmod -R 755 DVReflectors
cd DVReflectors/YSFReflector

```
Compilar e instalar

```bash
cd /opt/DVReflectors/YSFReflector
sudo make clean
sudo make -j4

```
Copiar archivo INI a /etc/

```bash
 sudo cp /opt/DVReflectors/YSFReflector/YSFReflector.ini /etc/YSFReflector.ini
```
Crear carpeta de logs

```bash
sudo mkdir -p /var/log/YSFReflector
sudo chmod 777 /var/log/YSFReflector
```

Configurar el archivo /etc/YSFReflector.ini

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

Crear servicio Systemd para autoinicio

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

Permisos sudoers para ejecutar cambios en el servidor 

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

```bash
sudo systemctl daemon-reload
sudo systemctl enable ysfreflector
sudo systemctl start ysfreflector
sudo systemctl status ysfreflector
```


## 📦 Instalación del Dashboard

🧰 Paso a paso

```bash
sudo apt update
sudo apt install apache2 -y
sudo apt install php libapache2-mod-php -y
sudo apt install php-curl unzip -y
sudo apt install network-manager -y
sudo apt install git -y
```

1. Copia la carpeta completa **LUXLINK FUSION** a tu servidor web:  
```bash
cd /var/www/
sudo rm -rf /var/www/html
sudo git clone https://github.com/telecov/LUXLINK-FUSION.git html
```

2. Permisos
```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chown www-data:www-data /var/www/html/monitor_log.php
sudo chmod 775 /var/www/html/monitor_log.php
```

2.1 Crear servicio Telegram Tiempo Real
```bash
sudo nano /etc/systemd/system/luxlink-monitor.service
```
escribe, guarda este servicio
```bash
[Unit]
Description=LuxLink Fusion - Monitor de Conexiones YSFReflector
After=network.target

[Service]
#apuntar al monitor del php
ExecStart=/usr/bin/php /var/www/html/monitor_log.php 
Restart=always
User=www-data
Group=www-data
StandardOutput=append:/var/log/luxlink_monitor.log
StandardError=append:/var/log/luxlink_monitor_error.log

[Install]
WantedBy=multi-user.target

```
Activa el servicio

```bash
sudo systemctl daemon-reload
sudo systemctl enable luxlink-monitor.service
sudo systemctl start luxlink-monitor.service
```

3. Acceso WEB
Accede desde tu navegador:

http://tu-servidor/


Contraseña por defecto

```bash
luxlink2024
```

### Desde el LUXLINK podras configurar
Nombre del sistema o reflector
Dirección IP del reflector
Puerto y descripción
Estado de enlace y estadísticas

## 💬 Telegram

* Activar o desactivar notificaciones

Configura Telegram (opcional)
Crea un bot en @BotFather
Obten el token http api
crea un canal o agraga tu bot como admin al grupo Telegram
buscar el ID del canal o grupo a utilizar https://api.telegram.org/bot/getUpdates
Asociar grupo o canal

* Controla mensajes automáticos de actividad o errores

## 🎨 Apariencia y encabezado

* Cambiar logos, íconos y textos principales
* Editar colores o imagen de fondo
