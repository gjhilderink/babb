#!/usr/bin/env bash
#
# BABB Portaal - deploy script
# Gebruik (op de server, in de projectmap):
#   ./deploy.sh            -> git pull + alles bijwerken
#   ./deploy.sh --no-pull  -> alleen bijwerken (als je zelf al gepulld hebt)
#
set -euo pipefail

# Alles in een functie: bash leest zo het hele script in voordat git pull het kan wijzigen
main() {

cd "$(dirname "$0")"

PHP="${PHP:-php}"
COMPOSER="${COMPOSER:-composer}"

step() { printf '\n\033[1;32m==> %s\033[0m\n' "$1"; }

if [ ! -f .env ]; then
    echo "Geen .env gevonden. Kopieer .env.example naar .env en vul deze eerst in." >&2
    exit 1
fi

step "Onderhoudsmodus aan"
$PHP artisan down --retry=15 || true
# Altijd weer online komen, ook als een stap mislukt
trap '$PHP artisan up >/dev/null 2>&1 || true' EXIT

if [ "${1:-}" != "--no-pull" ]; then
    step "Code ophalen (git pull)"
    git pull --ff-only
fi

step "Composer packages installeren"
$COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

if ! grep -q '^APP_KEY=.\+' .env; then
    step "APP_KEY genereren"
    $PHP artisan key:generate --force
fi

step "Database migreren"
$PHP artisan migrate --force

step "Database seeden"
$PHP artisan db:seed --force

step "Storage link"
[ -e public/storage ] || $PHP artisan storage:link

step "Caches legen"
$PHP artisan optimize:clear

step "Caches opbouwen (config, routes, views, events)"
$PHP artisan optimize

step "Onderhoudsmodus uit"
$PHP artisan up
trap - EXIT

printf '\n\033[1;32mDeploy klaar.\033[0m\n'
}

main "$@"
