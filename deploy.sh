#!/usr/bin/env bash
# Despliega el CRUD de ejemplo en Apache (Debian 12 con LAMP ya instalado).
# Ejecutar: sudo bash deploy.sh
#
# Requisitos previos: Apache + PHP + MariaDB funcionando
# (por ejemplo, instalados con el proyecto original).

set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WEB_DIR="/var/www/html/crud-example"
SQL_FILE="$PROJECT_DIR/sql/animales.sql"

if [[ "${EUID:-$(id -u)}" -ne 0 ]]; then
  echo "ERROR: Ejecuta con sudo: sudo bash deploy.sh"
  exit 1
fi

echo "==> Aplicando base de datos (crea la columna 'activo' si falta)..."
mariadb -u root < "$SQL_FILE"

echo "==> Copiando el proyecto a Apache..."
rm -rf "$WEB_DIR"
cp -a "$PROJECT_DIR" "$WEB_DIR"
chown -R www-data:www-data "$WEB_DIR"

echo "==> Probando respuesta HTTP..."
http_code=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost/crud-example/public/index.php?action=index")
echo "HTTP $http_code"

echo ""
echo "=============================================="
echo "  CRUD de ejemplo desplegado"
echo "=============================================="
echo ""
echo "  URL: http://localhost/crud-example/public/"
echo ""
echo "  Para quitarlo:"
echo "    sudo rm -rf $WEB_DIR"
echo ""
