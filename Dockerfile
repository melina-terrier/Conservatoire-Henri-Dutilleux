# syntax=docker/dockerfile:1.6
#
# Dockerfile production — Thème Conservatoire Henri Dutilleux
#
# Build une image WordPress autonome avec le thème activé et tous les assets
# pré-buildés. Convient à un hébergement containerisé (Cloud Run, ECS, Fly.io,
# AWS App Runner). Pour OVH mutualisé, préférer le déploiement FTP via le
# workflow GitHub Actions (cf. .github/workflows/ci.yml).
#
# Build :   docker build -t conservatoire:latest .
# Run :     docker run -p 8080:80 conservatoire:latest

# ─── Stage 1 : build des assets (JS minifié + CSS compilé) ────────────────────
FROM node:20-alpine AS assets

WORKDIR /build

# Installe d'abord les deps (couche cache séparée des sources).
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# Copie les sources et build.
COPY src/ ./src/
COPY Conservatoire/ ./Conservatoire/
RUN npm run build

# Sanity check : sans le `Theme Name:` dans style.css, WordPress refuse d'activer
# le thème. Ce check fait échouer le build plutôt qu'expédier un thème cassé.
RUN grep -q "Theme Name: Conservatoire" Conservatoire/style.css \
    || (echo "ERROR: style.css missing Theme Name header" && exit 1)


# ─── Stage 2 : image WordPress finale ─────────────────────────────────────────
FROM wordpress:6.9-php8.3-apache AS runtime

# Active mod_headers + mod_expires + mod_deflate (utilisés par .htaccess).
RUN a2enmod headers expires deflate rewrite

# Copie uniquement le thème (assets buildés inclus).
COPY --from=assets --chown=www-data:www-data /build/Conservatoire/ \
    /var/www/html/wp-content/themes/Conservatoire/

# Sécurité : retire les fichiers de dev qui auraient pu se glisser dans l'image.
RUN find /var/www/html/wp-content/themes/Conservatoire \
    \( -name "*.scss" -o -name "*.map" -o -name "node_modules" -type d \) \
    -prune -exec rm -rf {} + 2>/dev/null || true

# Variables d'env attendues au runtime :
#   GOOGLE_MAPS_API_KEY (consommée par functions.php via getenv)
#   WORDPRESS_DB_HOST / WORDPRESS_DB_USER / WORDPRESS_DB_PASSWORD / WORDPRESS_DB_NAME
# L'image wordpress:6.9 gère wp-config.php depuis ces variables.

# Healthcheck simple : WordPress sert un 200 sur /wp-admin/admin-ajax.php?action=heartbeat
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://localhost/wp-login.php > /dev/null || exit 1

EXPOSE 80
