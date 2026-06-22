# Conservatoire de Belfort — Thème WordPress

Thème WordPress sur-mesure pour le Conservatoire Henri Dutilleux de Belfort (projet pédagogique Master IW).

## Stack

- WordPress 6.9 · PHP 8.1+ · MariaDB 10.6
- SCSS compilé via **Sass**, JS minifié via **Terser**
- Plugins requis : **ACF Pro** (contenus éditables), **Contact Form 7** (formulaires), **Yoast SEO** (meta-titres, descriptions, Open Graph, sitemap)

## Développement local

Prérequis : [Docker Desktop](https://www.docker.com/products/docker-desktop) + [Node.js](https://nodejs.org/) 18+.

```bash
# 1. Lancer l'environnement (WordPress :8080 · Adminer :8081)
docker compose up -d

# 2. Installer les dépendances de build (une seule fois)
npm install

# 3. Compiler le SCSS en continu pendant le dev
npm run watch
```

Puis sur http://localhost:8080 : terminer l'installation WordPress et activer le thème **Conservatoire** (Apparence → Thèmes).

> Le thème lit une clé Google Maps : crée un fichier `.env` à la racine avec `GOOGLE_MAPS_API_KEY=ta_clé`.

## Build de production

```bash
npm run build      # style.css (compressé) + dist/js/*.min.js (minifié)
```

Déploiement : **FTP vers OVH** (les fichiers générés `style.css` et `dist/` doivent être présents sur le serveur).

## Structure

```
Conservatoire/                  → le thème (bind-mounté dans Docker)
├── inc/                        → logique PHP : actions, filters, CPT agenda, ACF, helpers
├── classes/                    → walker de menu custom
├── template-parts/             → composants (hero, card, sections, content-*…)
├── src/                        → sources : scss/ · js/ · fonts/ · img/
├── dist/js/                    → JS minifié (généré)
├── *.php                       → templates (front-page, archive, single-agenda, page-*)
├── style.css                   → CSS compilé (généré — NE PAS éditer à la main)
└── functions.php               → point d'entrée (inclut inc/)

docker-compose.yml              → environnement de dev (WordPress + MariaDB + Adminer)
package.json                    → dépendances et scripts de build (sass, terser)
```

> `style.css` et `dist/` sont **générés** et gitignorés. Après un `git clone` : `npm install && npm run build` avant que le thème soit reconnu par WordPress.

## Scripts npm

| Commande | Effet |
|---|---|
| `npm run watch` | Compile le SCSS en continu |
| `npm run build` | Build de production (CSS + JS) |

## Conventions

- **PHP** : préfixe `crdtheme_`, hooks séparés par type (`actions.php` / `filters.php` / `template-functions.php`), échappement systématique des sorties (`esc_html` / `esc_attr` / `esc_url`).
- **SCSS** : nommage BEM, variables CSS pour les couleurs, partials organisés par responsabilité.

## Dépannage — événements passés affichés

Un cron quotidien décale les `event_date` passés vers le futur (rotation +1 an). Pour forcer le rattrapage : supprimer l'option `crdtheme_events_last_shift` via Adminer, puis recharger une page front.
