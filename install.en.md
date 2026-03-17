# **Web Dashboard for YSF Reflector**

🇺🇸 English | 🇪🇸 Español
---

## 🖥️ Requirements

***DVREFLECTOR (NOSTAR) must be installed and running***
https://github.com/nostar/DVReflectors

If you already have it installed and working, you can skip directly to the **DASHBOARD installation**.
If you are starting from scratch, you can follow the step-by-step process supported by this video.

⚠️ **WARNING:**
If you already have a web dashboard running, it is recommended to create a backup first, or install this dashboard in parallel so you can test it safely.

For example, you can install it in:

```
/var/www/html/ysf/
```

This way you won’t lose your current setup. If you like it, you can later replace your existing installation.

---

## ℹ️ Important Information

If you notice differences in the commands compared to the installation videos:

👉 This is due to improvements made over time.
👉 These improvements do not directly affect system functionality.

The system is designed to operate reliably and without issues.

---

## 💻 Recommended Hardware

### Minimum Requirements

* CPU: Dual Core 1.2 GHz or higher (Intel Atom / Celeron)
* RAM: 1 GB minimum (2 GB recommended)
* Storage: 8 GB (SD or HDD)
* Network: Ethernet 100 Mbps or Wi-Fi b/g/n
* OS: Debian, Ubuntu Server, Raspbian, Armbian

✔ Raspberry Pi 3 or higher recommended

---

## 🧠 Supported Systems

YSF REFLECTOR has been tested and works optimally on:

* Debian 12+
* Raspbian 12
* Raspberry Pi OS
* Ubuntu Server
* Armbian (Bookworm)

Recommended setup: Linux-based computer or mini-server

---

## 📦 Required Software

* Apache2
* PHP 8.2 or higher
* Git
* cURL
* nmcli

### 🔧 Tools for configuration

* IP Scanner → to identify the device IP
* PuTTY → to manage Linux via SSH

👉 For Raspberry Pi: use **Raspberry Pi Imager**

---

# 📡 YSF Reflector Installation (DVReflector)

## 👤 Create user

```bash id="z1a2b3"
sudo adduser ysfreflector
sudo usermod -aG sudo ysfreflector
```

---

## 🔄 Update system and install dependencies

```bash id="z1a2b4"
sudo apt update && sudo apt upgrade -y
sudo apt install git build-essential cmake -y
sudo apt install jq -y
```

---

## ⬇️ Download DVReflector

```bash id="z1a2b5"
cd /opt
sudo git clone https://github.com/nostar/DVReflectors.git
sudo chmod -R 755 DVReflectors
cd DVReflectors/YSFReflector
```

---

## ⚙️ Compile

```bash id="z1a2b6"
cd /opt/DVReflectors/YSFReflector
sudo make clean
sudo make -j4
```

---

## 📁 Copy configuration file

```bash id="z1a2b7"
sudo cp /opt/DVReflectors/YSFReflector/YSFReflector.ini /etc/YSFReflector.ini
```

---

## 📂 Create logs directory

```bash id="z1a2b8"
sudo mkdir -p /var/log/YSFReflector
sudo chmod 777 /var/log/YSFReflector
```

---

## ⚙️ Configure /etc/YSFReflector.ini

```bash id="z1a2b9"
sudo nano /etc/YSFReflector.ini
```

```bash id="z1a2c0"
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

## 🔧 Create systemd service (auto-start)

```bash id="z1a2c1"
sudo nano /etc/systemd/system/ysfreflector.service
```

```bash id="z1a2c2"
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

## 🔐 Configure sudo permissions

```bash id="z1a2c3"
sudo nano /etc/sudoers.d/99-www-data-ysf
```

```bash id="z1a2c4"
www-data ALL=NOPASSWD: /bin/systemctl restart ysfreflector.service
www-data ALL=NOPASSWD: /bin/systemctl start ysfreflector.service
www-data ALL=NOPASSWD: /bin/systemctl stop ysfreflector.service
www-data ALL=NOPASSWD: /sbin/reboot
www-data ALL=(ALL) NOPASSWD: /usr/bin/nmcli, /usr/sbin/ip, /bin/systemctl
www-data ALL=(ALL) NOPASSWD: /sbin/iwlist
```

---

## ▶️ Start and enable service

```bash id="z1a2c5"
sudo systemctl daemon-reload
sudo systemctl enable ysfreflector
sudo systemctl start ysfreflector
sudo systemctl status ysfreflector
```

---

# 📦 Dashboard Installation (LuxLink Fusion)

## 🧰 Install dependencies

```bash id="z1a2c6"
sudo apt update
sudo apt install apache2 -y
sudo apt install php libapache2-mod-php -y
sudo apt install php-curl unzip -y
sudo apt install network-manager -y
sudo apt install git -y
```

---

## 📁 Install dashboard

```bash id="z1a2c7"
cd /var/www/
sudo rm -rf /var/www/html
sudo git clone https://github.com/telecov/LUXLINK-FUSION.git html
```

---

## 🔐 Set permissions

```bash id="z1a2c8"
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chown www-data:www-data /var/www/html/monitor_log.php
sudo chmod 775 /var/www/html/monitor_log.php
sudo chown root:root /var/www/html/includes/update.sh
sudo chmod 750 /var/www/html/includes/update.sh
```

---

## ⚙️ Enable update permissions

```bash id="z1a2c9"
sudo visudo
```

Add the following line at the end:

```bash id="z1a2d0"
www-data ALL=(root) NOPASSWD: /var/www/html/includes/update.sh
```

---

## 🤖 Telegram Real-Time Service

```bash id="z1a2d1"
sudo nano /etc/systemd/system/luxlink-monitor.service
```

```bash id="z1a2d2"
[Unit]
Description=LuxLink Fusion - YSFReflector Connection Monitor
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

## ▶️ Enable service

```bash id="z1a2d3"
sudo systemctl daemon-reload
sudo systemctl enable luxlink-monitor.service
sudo systemctl start luxlink-monitor.service
sudo systemctl status luxlink-monitor.service
```

---

## 🌐 Web Access

Open your browser and go to:

```
http://your-server/
```

Default password:

```bash id="z1a2d4"
luxlink2024
```

---

## ⚙️ From LuxLink you can configure

* System or reflector name
* Reflector IP address
* Port and description
* Link status and statistics

---

## 💬 Telegram

* Enable or disable notifications

### Optional setup:

* Create a bot via @BotFather

* Get the HTTP API token

* Create a channel or add the bot as admin to a Telegram group

* Get the group/channel ID:
  https://api.telegram.org/bot/getUpdates

* Link the group or channel

✔ Control automatic activity or error messages

---

## 🎨 Appearance and Header

* Change logos, icons and main texts
* Customize colors or background image
