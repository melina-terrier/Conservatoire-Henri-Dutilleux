# Conservatoire de Belfort — Thème WordPress

Thème WordPress développé pour le Conservatoire de Belfort dans le cadre d'un exercice pédagogique.

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

Créer un fichier `.env` à la racine du projet avec la clé Google Maps (pour la carte ACF) :

```
GOOGLE_MAPS_API_KEY=votre_clé_ici
```

Le fichier `.env` est ignoré par Git pour ne pas exposer la clé.

Puis lancer les conteneurs :

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

Le fichier `src/scss/style.scss` est compilé vers `Conservatoire/style.css`.

> **Important :** Ne jamais éditer `Conservatoire/style.css` directement. Toujours modifier les fichiers dans `src/scss/`.

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
│   ├── src/
│   │   ├── fonts/                  → fonts self-hostées (Mulish, Space Mono en woff2 + ttf)
│   │   ├── img/                    → assets statiques du thème
│   │   └── js/                     → scripts compilés (vendors, script)
│   ├── front-page.php              → home (utilise ACF flexible content)
│   ├── archive.php                 → archive du CPT agenda
│   ├── single-agenda.php           → page d'un événement
│   ├── page-*.php                  → templates de pages spécifiques
│   ├── style.css                   → généré par sass, ne pas éditer à la main
│   └── functions.php               → point d'entrée, inclut les fichiers de inc/
│
├── src/scss/                       → sources SCSS, compilées vers Conservatoire/style.css
│   ├── partials/                   → variables, mixins, fonts, a11y
│   ├── layout/                     → header, footer, menu, grid, main-column
│   ├── components/                 → hero, section, card, crd, carousel, duotone, single
│   ├── pages/                      → contact, enseignements, legal, sitemap, searchResult
│   ├── ui/                         → btn, form, pagination, search
│   ├── vendor/                     → normalize, flickity
│   └── style.scss                  → point d'entrée
│
├── docker-compose.yml              → orchestration WordPress + MariaDB + Adminer
├── package.json                    → dépendances de build (sass)
└── .env                            → variables d'environnement (Google Maps API key, etc.)
```

---

## Plugins requis

| Plugin                     | Utilisation                                              |
|----------------------------|----------------------------------------------------------|
| Advanced Custom Fields Pro | Tous les contenus éditables (événements, pages, options) |
| Contact Form 7             | Page contact (shortcode intégré via `do_shortcode()`)    |
| Yoast SEO                  | Méta-titres, méta-descriptions, Open Graph, sitemap.xml. Le thème détecte sa présence via `WPSEO_VERSION` et désactive son propre fallback SEO pour éviter les doublons. |

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
```

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

### Erreur `Undefined constant "GOOGLE_MAPS_API_KEY"`

Le fichier `.env` n'est pas chargé ou la variable est vide. Vérifier :
```bash
docker compose exec wordpress env | grep GOOGLE_MAPS_API_KEY
```
Si la variable est vide, créer/éditer `.env` à la racine avec `GOOGLE_MAPS_API_KEY=votreclé` puis `docker compose up -d` pour recharger.

### Événements passés visibles sur le site

Le thème inclut un cron quotidien qui décale automatiquement les `event_date` passés vers le futur (rotation +1 an). Si des événements passés s'affichent, déclencher manuellement la fonction depuis Adminer :
```sql
DELETE FROM wp_options WHERE option_name = 'crdtheme_events_last_shift';
```
Puis recharger n'importe quelle page front : le rattrapage se déclenche automatiquement.
