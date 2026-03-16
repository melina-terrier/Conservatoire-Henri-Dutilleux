# Conservatoire de Belfort — Thème WordPress

Thème WordPress développé pour le Conservatoire de Belfort dans le cadre d'un exercice pédagogique.

---

## Lancer l'environnement de développement

### Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop) installé et démarré
- [Node.js](https://nodejs.org/) installé (pour compiler le SCSS)
- Un fichier `.env` à la racine avec la variable `GOOGLE_MAPS_API_KEY` (voir `.env.example`)

### 1. Démarrer WordPress avec Docker

```bash
docker compose up -d
```

Cela lance trois conteneurs :

| Conteneur   | URL                      | Description                     |
|-------------|--------------------------|----------------------------------|
| WordPress   | http://localhost         | Site WordPress                  |
| Adminer     | http://localhost:8080    | Interface de gestion de la base de données |
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

1. Accéder à http://localhost et suivre l'assistant d'installation WordPress
2. Activer le thème **Conservatoire** dans *Apparence > Thèmes*

---

## Plugins requis

| Plugin                     | Utilisation                                              |
|----------------------------|----------------------------------------------------------|
| Advanced Custom Fields Pro | Tous les contenus éditables (événements, pages, options) |
| Contact Form 7             | Page contact (shortcode intégré via `do_shortcode()`)    |

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
