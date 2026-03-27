#!/usr/bin/env bash
# ┌─────────────────────────────────────────────────────────────┐
# │  KurDom — Production Deploy Script                          │
# │  Usage: bash deploy.sh                                      │
# │  Run from project root on the production server             │
# └─────────────────────────────────────────────────────────────┘

set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$APP_DIR"

echo "🚀 Deploying KurDom..."

# ── 1. Maintenance mode ────────────────────
echo "→ Enabling maintenance mode..."
php artisan down --secret="kurdom-deploy-bypass"

# ── 2. Pull latest code ───────────────────
echo "→ Pulling latest code..."
git pull origin main

# ── 3. Install PHP dependencies ────────────
echo "→ Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# ── 4. Install & build frontend ────────────
echo "→ Installing NPM packages..."
npm ci --production=false

echo "→ Building frontend assets..."
npm run build

# ── 5. Run migrations ─────────────────────
echo "→ Running database migrations..."
php artisan migrate --force

# ── 6. Seed initial data (safe — uses updateOrCreate) ──
echo "→ Seeding initial data..."
php artisan db:seed --force

# ── 7. Optimize for production ─────────────
echo "→ Optimizing application..."
php artisan optimize
php artisan filament:optimize
php artisan icons:cache
php artisan event:cache

# ── 8. Restart services ───────────────────
echo "→ Restarting queue workers..."
php artisan queue:restart

echo "→ Restarting Reverb..."
supervisorctl restart kurdom-reverb

# ── 9. Disable maintenance mode ────────────
echo "→ Going live..."
php artisan up

echo "✅ Deployment complete!"
