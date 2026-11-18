#!/usr/bin/env bash
# =============================================================================
# Build paket deploy untuk InfinityFree
# -----------------------------------------------------------------------------
# Jalankan dari Git Bash di root project:
#
#   APP_URL=https://namasitus.infinityfreeapp.com bash deploy/infinityfree/build.sh
#
# Opsional (bisa juga diisi manual nanti di .env hasil build):
#   DB_HOST=sqlXXX.infinityfree.com DB_DATABASE=if0_XXX_pooja DB_USERNAME=if0_XXX DB_PASSWORD=...
#   ADMIN_EMAIL=admin@contoh.com   (default: admin@poojacoolprima.com)
#   MYSQL_BIN=/c/laragon/bin/mysql/mysql-8.4.3-winx64/bin   (lokasi mysql & mysqldump)
#
# Hasil di deploy/build/ (TIDAK ikut git):
#   upload/htdocs/        → upload seluruh isinya ke folder htdocs di InfinityFree
#   database.sql          → impor lewat phpMyAdmin
#   KREDENSIAL-ADMIN.txt  → email & password admin awal (ganti setelah login pertama)
# =============================================================================
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
SRC="$ROOT/deploy/infinityfree"
OUT="$ROOT/deploy/build"
HT="$OUT/upload/htdocs"
APP="$HT/laravel"

APP_URL="${APP_URL:-https://NAMA-SITUS-ANDA.infinityfreeapp.com}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@poojacoolprima.com}"
MYSQL_BIN="${MYSQL_BIN:-/c/laragon/bin/mysql/mysql-8.4.3-winx64/bin}"
LOCAL_DB_USER="${LOCAL_DB_USER:-root}"
LOCAL_DB_PASS="${LOCAL_DB_PASS:-}"
TMP_DB="pooja_deploy_tmp_$$"

step() { echo; echo "==> $*"; }
rand() { php -r '$c="ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";$s="";for($i=0;$i<'"$1"';$i++)$s.=$c[random_int(0,strlen($c)-1)];echo $s;'; }
mysql_local() { "$MYSQL_BIN/mysql" -h127.0.0.1 -u"$LOCAL_DB_USER" ${LOCAL_DB_PASS:+-p"$LOCAL_DB_PASS"} "$@"; }

[ -x "$MYSQL_BIN/mysqldump" ] || [ -x "$MYSQL_BIN/mysqldump.exe" ] || { echo "mysqldump tidak ditemukan di $MYSQL_BIN (set MYSQL_BIN)"; exit 1; }

step "1/7 Tes otomatis (paket tidak dibuat jika ada tes gagal)"
(cd "$ROOT" && php artisan test >/dev/null) || { echo "Ada tes yang gagal. Jalankan: php artisan test"; exit 1; }

step "2/7 Build aset frontend"
(cd "$ROOT" && npm run build >/dev/null)

step "3/7 Menyalin aplikasi"
rm -rf "$OUT"
mkdir -p "$APP"
for d in app bootstrap config database routes; do cp -r "$ROOT/$d" "$APP/"; done
mkdir -p "$APP/resources" && cp -r "$ROOT/resources/views" "$APP/resources/"
[ -d "$ROOT/lang" ] && cp -r "$ROOT/lang" "$APP/"
cp "$ROOT/artisan" "$ROOT/composer.json" "$ROOT/composer.lock" "$APP/"
rm -f "$APP"/bootstrap/cache/*.php "$APP"/database/*.sqlite

# storage kosong (tanpa log, sesi, cache, atau bukti bayar dari laptop)
(cd "$ROOT" && find storage -name .gitignore) | while read -r f; do
  mkdir -p "$APP/$(dirname "$f")" && cp "$ROOT/$f" "$APP/$f"
done
mkdir -p "$APP/storage/framework/cache/data"

# File publik → htdocs
cp -r "$ROOT/public/." "$HT/"
rm -rf "$HT/index.php" "$HT/hot" "$HT/storage" "$HT/.htaccess"
cp "$SRC/index.php" "$HT/index.php"
cp "$SRC/htaccess-root" "$HT/.htaccess"
cp "$SRC/htaccess-laravel" "$APP/.htaccess"

step "4/7 composer install --no-dev"
(cd "$APP" && composer install --no-dev --optimize-autoloader --no-interaction --no-progress --quiet)

step "5/7 Membuat .env produksi"
APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
sed -e "s|__APP_KEY__|$APP_KEY|" \
    -e "s|__APP_URL__|$APP_URL|" \
    -e "s|__DB_HOST__|${DB_HOST:-ISI_DB_HOST}|" \
    -e "s|__DB_DATABASE__|${DB_DATABASE:-ISI_DB_DATABASE}|" \
    -e "s|__DB_USERNAME__|${DB_USERNAME:-ISI_DB_USERNAME}|" \
    -e "s|__DB_PASSWORD__|${DB_PASSWORD:-ISI_DB_PASSWORD}|" \
    "$SRC/env.production.template" > "$APP/.env"

step "6/7 Membuat database.sql (migrasi + data layanan + admin) dari paket ini"
ADMIN_PASSWORD="$(rand 16)"
mysql_local -e "CREATE DATABASE \`$TMP_DB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
trap 'mysql_local -e "DROP DATABASE IF EXISTS \`$TMP_DB\`" >/dev/null 2>&1 || true' EXIT
(
  cd "$APP"
  APP_ENV=production DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE="$TMP_DB" \
  DB_USERNAME="$LOCAL_DB_USER" DB_PASSWORD="$LOCAL_DB_PASS" CACHE_STORE=array \
  ADMIN_EMAIL="$ADMIN_EMAIL" ADMIN_PASSWORD="$ADMIN_PASSWORD" \
    php artisan migrate --force --seed --no-interaction >/dev/null
)
"$MYSQL_BIN/mysqldump" -h127.0.0.1 -u"$LOCAL_DB_USER" ${LOCAL_DB_PASS:+-p"$LOCAL_DB_PASS"} \
  --no-tablespaces --skip-comments --skip-add-locks --skip-lock-tables --set-gtid-purged=OFF \
  --default-character-set=utf8mb4 --skip-triggers "$TMP_DB" \
  | sed -e 's/utf8mb4_0900_ai_ci/utf8mb4_unicode_ci/g' > "$OUT/database.sql"

# Bersihkan jejak migrasi lokal dari paket
find "$APP/storage" -type f ! -name .gitignore -delete
rm -f "$APP"/bootstrap/cache/config.php "$APP"/bootstrap/cache/routes-*.php

cat > "$OUT/KREDENSIAL-ADMIN.txt" <<EOF
Login admin awal (ganti password lewat menu "Ganti Password" setelah login pertama):
  URL      : $APP_URL/admin/login
  Email    : $ADMIN_EMAIL
  Password : $ADMIN_PASSWORD
Simpan file ini di tempat aman, lalu HAPUS setelah password diganti.
EOF

step "7/7 Ringkasan"
FILES=$(find "$OUT/upload" -type f | wc -l)
SIZE=$(du -sh "$OUT/upload" | cut -f1)
echo "Paket   : $OUT/upload/htdocs  ($FILES file, $SIZE)"
echo "Database: $OUT/database.sql ($(grep -c '^CREATE TABLE' "$OUT/database.sql") tabel)"
echo "Admin   : $OUT/KREDENSIAL-ADMIN.txt"
grep -q "ISI_DB_" "$APP/.env" && echo "PERHATIAN: data database di htdocs/laravel/.env masih ISI_DB_... (isi sesuai panel InfinityFree)."
grep -q "NAMA-SITUS-ANDA" "$APP/.env" && echo "PERHATIAN: APP_URL masih contoh. Ubah di htdocs/laravel/.env."
echo "Selesai."
