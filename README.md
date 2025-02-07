![MangaQuest Logo](public/images/logoNavbar.svg)

Bienvenue sur **MangaQuest**, une plateforme permettant aux utilisateurs de découvrir et d'interagir avec des mangas et des animes. 

## ⚙️ Prérequis

Avant d'installer MangaQuest, assurez-vous d'avoir :
- PHP 8.3
- Composer
- Symfony CLI
- MySQL

## 📥 Installation

1. **Configurer l'environnement**
   ```bash
   cp .envCopy .env
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Démarrer le serveur Symfony**
   ```bash
   symfony serve
   ```

4. **Charger les données de test**
   ```bash
   php bin/console doctrine:fixtures:load
   ```
   Les identifiants de connexion se trouvent dans `fixtures/user.yaml` :
   - ✉️ `admin@gmail.com` | 🔑 `test`
   - ✉️ `user@gmail.com` | 🔑 `test`
   - ✉️ `banned@gmail.com` | 🔑 `test`

5. **Charger les animes**
   ```bash
   php bin/console app:fetch-anime
   ```

6. **Charger les mangas**
   ```bash
   php bin/console app:import-manga
   ```

## ✅ Tests

### 🔍 Tests unitaires
```bash
php bin/console phpunit test/Entity/UserTest
php bin/console phpunit test/Entity/MangaTest
```

### 🛠 Tests fonctionnels
```bash
php bin/console phpunit test/Controller/SecurityControllerTest
```

## 🗄 Schéma de la base de données
MangaQuest utilise une base de données MySQL contenant les entités suivantes :
![schéma de la base de données](./db.png)

## 🛠 Fonctionnalités

### 👥 Utilisateur non connecté
- Consultation des mangas et animes
- Accès aux discussions communautaires
- Visualisation des événements

### 🔓 Utilisateur connecté
- Donner un avis sur les mangas et animes
- Ajouter des mangas et animes en favoris
- Créer et répondre aux discussions
- Organiser et participer à des événements

### 🔧 Administrateur
- Gestion des mangas et animes
- Modération des reviews et messages
- Administration des utilisateurs

---



