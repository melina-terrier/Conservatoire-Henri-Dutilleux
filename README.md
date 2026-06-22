# Conservatoire de Belfort — Thème WordPress

Thème WordPress custom développé pour le Conservatoire Henri Dutilleux de Belfort dans le cadre d'un projet pédagogique Master IW.

**Verdict audit final** : 9-10/10 sur SEO, accessibilité, performance, responsive, fonctionnalité, sécurité — validé par 3 audits externes successifs (mai 2026).

---

## 📖 Lectures recommandées

- **[`docs/case-study.md`](docs/case-study.md)** — récit narratif du projet : méthodologie d'audit en 4 passes, 2 bugs critiques trouvés en régression et transformés en garde-fous CI, patterns techniques défendables (piège CSS Grid, sécurité WordPress, cron robuste, etc.)
- **[`docs/admin.md`](docs/admin.md)** — guide pour les éditeurs / admins : options du thème, CPT agenda, classe magique `-btn` du walker, formulaires CF7, upload SVG
- **[`docs/htaccess-root.example`](docs/htaccess-root.example)** — règles `.htaccess` racine WordPress à appliquer en prod (xmlrpc, wp-login, readme.html, etc.)

---

## ⚡ Points forts techniques

- **ACF en PHP** versionné (`acf_add_local_field_group` dans `inc/plugins/acf-fields.php`) — config reproductible par `git pull`
- **Helpers mémoïsés** : `crdtheme_get_site_infos()` (5 lookups DB → 1), `crdtheme_get_upcoming_events()` factorisé entre home et single
- **Cron de rotation events** avec lock transient + batch 200 + `try/finally` + rattrapage async
- **Sécurité côté thème** : énumération users (`rest_endpoints`), `?author=N` (redirect 301), SVG sanitization, walker `noopener`, headers HTTP complets
- **Performance** : preload LCP, cache `.htaccess` 1 an `immutable`, versioning d'assets via `filemtime`, build minifié (`dist/js/*.min.js`), `update_meta_cache` warm avant boucles
- **Accessibilité RGAA** : focus trap + `Escape` symétriques sur menu + search, `prefers-reduced-motion` CSS+JS, skiplink, contrastes WCAG AA, table `<th scope>`, cibles tactiles ≥ 48 px
- **SEO** : JSON-LD schema.org Event sur fiches agenda, pagination "Page X sur Y" + `aria-label` FR, `rel=next/prev` injecté dans `<head>`
- **Fonction pure extraite** : `crdtheme_build_upcoming_events_args()` isolée de la logique WP (gère le piège `WP_Query posts_per_page = 0`), vérifiable indépendamment
- **Containerisation** : Dockerfile multi-stage (`node:20-alpine` build → `wordpress:6.9-php8.3-apache` runtime)
- **Sass moderne** : migration complète `@import` → `@use` (29 fichiers, 0 warning de dépréciation)

---

## Lancer l'environnement de développement

### Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop) installé et démarré
- [Node.js](https://nodejs.org/) installé (pour compiler le SCSS)

**Versions supportées :**

| Composant | Version |
|-----------|---------|
| WordPress | 6.9.x (image `wordpress:6.9.4` figée dans `docker-compose.yml`) |
| PHP       | 7.4 minimum requis par WordPress 6.9 — **8.1+ recommandé** (l'image Docker fournit PHP 8.3) |
| MariaDB   | 10.6 |
| Node.js   | 18+ (requis par sass ^1.97) |

### 1. Démarrer WordPress avec Docker

```bash
docker compose up -d
```

Cela lance trois conteneurs :

| Conteneur   | URL                      | Description                     |
|-------------|--------------------------|----------------------------------|
| WordPress   | http://localhost:8080    | Site WordPress                  |
| Adminer     | http://localhost:8081    | Interface de gestion de la base de données |
| MariaDB     | (interne)                | Base de données                 |

### 2. Compiler le SCSS

Installer les dépendances (une seule fois) :

```bash
npm install
```

Lancer la compilation en mode watch (recompile à chaque modification) :

```bash
npm run watch
```

Le fichier `Conservatoire/src/scss/style.scss` est compilé vers `Conservatoire/style.css`.

> **Important :** Ne jamais éditer `Conservatoire/style.css` directement. Toujours modifier les fichiers dans `Conservatoire/src/scss/`.

### 3. Première installation WordPress

1. Accéder à http://localhost:8080 et suivre l'assistant d'installation WordPress
2. Activer le thème **Conservatoire** dans *Apparence > Thèmes*

---

## Structure du projet

```
Conservatoire/                      → racine du dépôt
├── Conservatoire/                  → le thème WordPress (bind-mounté dans Docker)
│   ├── classes/                    → walker custom du menu de navigation
│   ├── inc/                        → logique séparée par responsabilité
│   │   ├── actions.php             → add_action() : hooks WordPress
│   │   ├── filters.php             → add_filter() : sécurité, perf, lazy load
│   │   ├── template-functions.php  → setup, scripts, AJAX, query vars
│   │   ├── custom-post-types.php   → CPT agenda + taxonomies (cat_agenda, location)
│   │   └── plugins/acf.php         → pages d'options et clé Google Maps
│   ├── template-parts/             → composants réutilisables (hero, card, content-*)
│   ├── src/                        → TOUTES les sources du thème (au même endroit)
│   │   ├── scss/                   → sources SCSS → compilées vers Conservatoire/style.css
│   │   │   ├── partials/           → variables, mixins, fonts, a11y
│   │   │   ├── layout/             → header, footer, menu, grid, main-column
│   │   │   ├── components/         → hero, section, card, crd, carousel, duotone, single
│   │   │   ├── pages/              → contact, enseignements, legal, sitemap, searchResult
│   │   │   ├── ui/                 → btn, form, pagination, search
│   │   │   ├── vendor/             → normalize, flickity
│   │   │   └── style.scss          → point d'entrée SCSS
│   │   ├── js/                     → sources JS → minifiées vers dist/js/*.min.js
│   │   ├── fonts/                  → fonts self-hostées (Mulish, Space Mono en woff2)
│   │   └── img/                    → assets statiques du thème
│   ├── dist/js/                    → JS minifié (généré par `npm run build:js`)
│   ├── front-page.php              → home (utilise ACF flexible content)
│   ├── archive.php                 → archive du CPT agenda
│   ├── single-agenda.php           → page d'un événement
│   ├── page-*.php                  → templates de pages spécifiques
│   ├── style.css                   → généré par sass, ne pas éditer à la main
│   └── functions.php               → point d'entrée, inclut les fichiers de inc/
│
├── docker-compose.yml              → orchestration WordPress + MariaDB + Adminer
├── package.json                    → dépendances de build (sass, terser)
└── .env                            → variables d'environnement (Google Maps API key, etc.)
```

---

## Plugins requis

| Plugin                     | Utilisation                                              |
|----------------------------|----------------------------------------------------------|
| Advanced Custom Fields Pro | Tous les contenus éditables (événements, pages, options) |
| Contact Form 7             | Page contact (shortcode intégré via `do_shortcode()`)    |
| Yoast SEO                  | Source de **toute** la couche SEO du site : méta-titres, méta-descriptions, canonical, Open Graph / Twitter cards, sitemap. Le thème ne génère pas ces balises lui-même (il ne produit que le JSON-LD `Event`/`ItemList` de l'agenda) — Yoast est donc requis pour les balises meta et les aperçus de partage social. |

---

## Commandes utiles

```bash
# Démarrer l'environnement
docker compose up -d

# Arrêter l'environnement
docker compose down

# Voir les logs
docker compose logs -f

# Compiler le SCSS en mode watch
npm run watch

# Build production (CSS compressé + JS minifié dans dist/)
npm run build

# Vérifier la syntaxe PHP de tout le thème (php -l)
composer install
composer test:lint
```

---

## Qualité

### Vérification de la syntaxe PHP

```bash
composer install
composer test:lint   # php -l sur tout le PHP du thème (hors dist/)
```

> La fonction `crdtheme_build_upcoming_events_args()` a été extraite de `crdtheme_get_upcoming_events()` comme **fonction pure** (sans dépendance WordPress) pour être vérifiable indépendamment — notamment le piège `WP_Query posts_per_page = 0` qui retourne les 10 posts par défaut au lieu de 0.

> ℹ️ Linters JS/CSS/PHP (ESLint, Stylelint, PHPCS) et tests PHPUnit ont été retirés du projet ; seul `composer test:lint` (php -l) subsiste pour la vérification de syntaxe.

### Dockerfile production

Le [Dockerfile](Dockerfile) à la racine build une image WordPress autonome avec le thème activé et tous les assets pré-buildés — utile pour un hébergement containerisé (Cloud Run, ECS, Fly.io). Build multi-stage :

1. **Stage `assets`** (`node:20-alpine`) : `npm ci` + `npm run build`, avec un check qui fait échouer le build si `Theme Name` est absent du `style.css` compilé.
2. **Stage `runtime`** (`wordpress:6.9-php8.3-apache`) : copie le thème depuis le stage 1, active `mod_headers`/`expires`/`deflate`/`rewrite`, expose le port 80.

```bash
docker build -t conservatoire:latest .
docker run -p 8080:80 -e WORDPRESS_DB_HOST=… conservatoire:latest
```

Pour OVH mutualisé : préférer le déploiement FTP via le workflow GitHub Actions.

---

## Troubleshooting

### Le port 8080 (ou 8081) est déjà occupé

Si vous obtenez `Bind for 0.0.0.0:8080 failed: port is already allocated`, un autre service utilise le port.

**Changer le mapping** dans `docker-compose.yml` :
```yaml
wordpress:
  ports:
    - "8090:80"    # remplace 8080 par 8090 (ou autre)
adminer:
  ports:
    - "8091:8080"  # remplace 8081
```
Puis mettre à jour `WP_HOME` et `WP_SITEURL` dans `WORDPRESS_CONFIG_EXTRA` pour qu'ils pointent vers le nouveau port, et relancer `docker compose up -d`.

### Le navigateur refuse de se connecter à `http://localhost:8080`

Vérifier d'abord que les conteneurs tournent : `docker compose ps`. Si tout est bien `Up`, le blocage vient probablement d'une extension navigateur (uBlock Origin, Privacy Badger, ou un VPN qui intercepte le trafic local). Désactiver les extensions pour `localhost` ou tester en navigation privée. Chrome bloque effectivement quelques ports (6000, 6665-6669, etc.) mais 8080 n'en fait pas partie.

### Erreur `Undefined constant "GOOGLE_MAPS_API_KEY"`

Si la variable est vide, créer/éditer `.env` à la racine avec `GOOGLE_MAPS_API_KEY=votreclé` puis `docker compose up -d` pour recharger.

### Le SCSS n'est pas pris en compte

Vérifier que `npm run watch` est bien lancé. Si oui, vider le cache navigateur (`Cmd+Shift+R` sur Mac). Si la modif vient de toucher une variable globale, redémarrer `npm run watch` pour forcer une recompilation propre.

### Événements passés visibles sur le site

Le thème inclut un cron quotidien qui décale automatiquement les `event_date` passés vers le futur (rotation +1 an). Si des événements passés s'affichent, déclencher manuellement la fonction depuis Adminer :
```sql
DELETE FROM wp_options WHERE option_name = 'crdtheme_events_last_shift';
```
Puis recharger n'importe quelle page front : le rattrapage se déclenche automatiquement.

---

## Contribuer

Le code suit ces conventions :

- **PHP** : préfixe `crdtheme_` pour toutes les fonctions custom, séparation `actions.php` / `filters.php` / `template-functions.php` par type de hook, docblocks PHPDoc avec précondition et invariant pour les fonctions critiques.
- **SCSS** : nommage BEM (`block__element--modifier`), variables CSS pour les couleurs (`var(--violet)`), `fluid()` pour les tailles fluides, partials organisés par responsabilité.
- **Sécurité** : `esc_html` / `esc_attr` / `esc_url` systématique sur les sorties dynamiques, `wp_kses_post` sur les champs WYSIWYG, nonces sur les endpoints AJAX, `noopener noreferrer` sur tous les `target="_blank"`.

Pour une pull request :

1. Forker le repo
2. Créer une branche descriptive : `git checkout -b feat/nouvelle-section` ou `fix/cron-rotation`
3. Tester localement (page d'accueil + page concernée + admin Gutenberg)
4. Vérifier la syntaxe PHP : `docker compose exec wordpress bash -c 'find /var/www/html/wp-content/themes/Conservatoire -name "*.php" | xargs -I{} php -l {}'`
5. Ouvrir la PR en décrivant le *pourquoi* du changement, pas juste le *quoi*

Pull requests bienvenues — n'hésitez pas à ouvrir une issue avant un gros changement pour discuter de l'approche.
