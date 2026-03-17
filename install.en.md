# 📡 YSF Reflector & LuxLink Fusion Installation Guide

🇺🇸 English | 🇪🇸 [Español](install.md)

---

## 🖥️ Requirements

⚠️ **Important:**
You must have **DVReflector (NOSTAR)** installed and running.

👉 https://github.com/nostar/DVReflectors

If you already have it working, you can skip directly to the **Dashboard Installation** section.

If you are starting from scratch, follow the full guide below.

---

## ⚠️ Warning

If you already have a working web dashboard:

* Make a **backup first**
* Or install this dashboard in parallel (recommended)

Example:

```bash
/var/www/html/ysf/
```

This way you avoid breaking your current system.

---

## ℹ️ Important Information

If you notice differences between this guide and the installation videos:

👉 It’s due to improvements over time
👉 These changes do NOT affect functionality

The system is designed to remain stable and backward compatible.

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

* Debian 12+
* Raspberry Pi OS
* Ubuntu Server
* Armbian (Bookworm)

---

## 📦 Required Software

* Apache2
* PHP 8.2+
* Git
* cURL
* nmcli

### Tools (Recommended)

* IP Scanner (to find device IP)
* PuTTY (SSH access)

👉 For Raspberry Pi: use **Raspberry Pi Imager**

---

# 📡 YSF Reflector Installation (DVReflector)

## 👤 Create user

```bash
sudo adduser ysfreflector
sudo usermod -aG sudo ysfreflector
```

---

## 🔄 Update system

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install git build-essential cmake -y
sudo apt install jq -y
```

---

## ⬇️ Download DVReflector

```bash
cd /opt
sudo git clone https://github.com/nostar/DVReflectors.git
sudo chmod -R 755 DVReflectors
cd DVReflectors/YSFReflector
```

---

## ⚙️ Compile

```bash
cd /opt/DVReflectors/YSFReflector
sudo make clean
sudo make -j4
```

---

## 📁 Copy configuration

```bash
sudo cp /opt/DVReflectors/YSFReflector/YSFReflector.ini /etc/YSFReflector.ini
```

---

## 📂 Create logs directory

```bash
sudo mkdir -p /var/log/YSFReflector
sudo chmod 777 /var/log/YSFReflector
```

---

## ⚙️ Configure YSFReflector.ini

```bash
sudo nano /etc/YSFReflector.ini
```

```ini
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

## 🔧 Create systemd service

```bash
sudo nano /etc/systemd/system/ysfreflector.service
```

```ini
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

## 🔐 Sudo permissions

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

## ▶️ Start service

```bash
sudo systemctl daemon-reload
sudo systemctl enable ysfreflector
sudo systemctl start ysfreflector
sudo systemctl status ysfreflector
```

---

# 📦 Dashboard Installation (LuxLink Fusion)

## 🧰 Install dependencies

```bash
sudo apt update
sudo apt install apache2 -y
sudo apt install php libapache2-mod-php -y
sudo apt install php-curl unzip -y
sudo apt install network-manager -y
sudo apt install git -y
```

---

## 📁 Install dashboard

```bash
cd /var/www/
sudo rm -rf /var/www/html
sudo git clone https://github.com/telecov/LUXLINK-FUSION.git html
```

---

## 🔐 Permissions

```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chown www-data:www-data /var/www/html/monitor_log.php
sudo chmod 775 /var/www/html/monitor_log.php
sudo chown root:root /var/www/html/includes/update.sh
sudo chmod 750 /var/www/html/includes/update.sh
```

---

## ⚙️ Allow updates via sudo

```bash
sudo visudo
```

Add:

```bash
www-data ALL=(root) NOPASSWD: /var/www/html/includes/update.sh
```

---

## 🤖 Telegram Real-Time Service

```bash
sudo nano /etc/systemd/system/luxlink-monitor.service
```

```ini
[Unit]
Description=LuxLink Fusion - YSF Monitor
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

```bash
sudo systemctl daemon-reload
sudo systemctl enable luxlink-monitor.service
sudo systemctl start luxlink-monitor.service
```

---

## 🌐 Web Access

Open in browser:

```
http://your-server/
```

Default password:

```bash
luxlink2024
```

---

## 💬 Telegram Setup

* Create a bot via @BotFather
* Get API token
* Add bot to a group/channel
* Get Chat ID:

```
https://api.telegram.org/bot<TOKEN>/getUpdates
```

---

## 🎨 Customization

From the dashboard you can configure:

* Reflector name
* IP address
* Port & description
* System status
* Visual customization
* Language (EN / ES)
* Temperature units

---

🚀 Done! Your LuxLink Fusion dashboard should now be fully operational.
