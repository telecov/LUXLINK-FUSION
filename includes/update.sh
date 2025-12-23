#!/bin/bash
set -e

echo "===> [LUXLINK FUSION] Iniciando actualización..."

APP_DIR="/var/www/html/"
BACKUP_DIR="/var/www/backup_luxlink_$(date +%Y%m%d_%H%M)"
PENDRIVE_DIR="/mnt/usb"
ZIP_NAME="luxlinkfusion_release.zip"
ZIP_LOCAL="$PENDRIVE_DIR/$ZIP_NAME"
ZIP_TMP="/tmp/$ZIP_NAME"
GITHUB_URL="https://github.com/telecov/LUXLINK-FUSION/releases/download/v2.0/$ZIP_NAME"

# Archivos / carpetas a preservar
PRESERVAR=(
  "includes/estilo.json"
  "includes/config.json"
  "includes/telegram_config.json"
  "data"
  "img/banner_luxlinkfusion.jpg"
)

# ===> Paso 0: Origen del ZIP
if [ -f "$ZIP_LOCAL" ]; then
  echo "📦 Usando ZIP desde pendrive: $ZIP_LOCAL"
  cp "$ZIP_LOCAL" "$ZIP_TMP"
else
  echo "🌐 Descargando ZIP desde GitHub Release"
  wget "$GITHUB_URL" -O "$ZIP_TMP"
  if [ ! -f "$ZIP_TMP" ]; then
    echo "❌ Error: no se pudo obtener el ZIP"
    exit 1
  fi
fi

# ===> Paso 1: Backup completo
echo "===> Paso 1: Backup en $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"
cp -r "$APP_DIR"/* "$BACKUP_DIR"

# ===> Paso 2: Dependencias básicas
echo "===> Paso 2: Instalando dependencias"
sudo apt update -y
sudo apt install -y unzip curl

# ===> Paso 3: Descomprimir
echo "===> Paso 3: Descomprimiendo nueva versión"
mkdir -p /tmp/luxlink_temp
unzip -o "$ZIP_TMP" -d /tmp/luxlink_temp

# ===> Paso 4: Instalar nueva versión (REPLACE)
echo "===> Paso 4: Instalando nueva versión"
rm -rf "$APP_DIR"
mkdir -p "$APP_DIR"
cp -r /tmp/luxlink_temp/* "$APP_DIR"

# ===> Paso 5: Restaurar personalizados
echo "===> Paso 5: Restaurando archivos personalizados"
for archivo in "${PRESERVAR[@]}"; do
  if [ -e "$BACKUP_DIR/$archivo" ]; then
    cp -r "$BACKUP_DIR/$archivo" "$APP_DIR/$archivo"
    echo "  - Restaurado: $archivo"
  fi
done

# ===> Paso 6: Permisos
echo "===> Paso 6: Corrigiendo permisos"
sudo chown -R www-data:www-data "$APP_DIR"
sudo find "$APP_DIR" -type d -exec chmod 755 {} \;
sudo find "$APP_DIR" -type f -exec chmod 644 {} \;

# ===> Paso 7: Limpieza
echo "===> Paso 7: Limpieza"
rm -rf /tmp/luxlink_temp "$ZIP_TMP"

# ===> Paso 8: Reinicio Apache
echo "===> Paso 8: Reiniciando Apache"
sudo systemctl restart apache2

# ===> Final
echo "✅ LuxLink Fusion actualizado correctamente a v2.0"
echo "    CA2RDP - Telecoviajero"
