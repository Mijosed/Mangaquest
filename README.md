# Mangaquest

## Étapes d'installation
1. **Installer les dépendances :**

   ```bash
   cp .envCopy .env
   ```



2. **Installer les dépendances :**

   ```bash
   composer install
   ```

3. **Démarrer le serveur Symfony :**
   ```bash
   symfony serve
   ```
4. **Se connecter :**

    ```bash
    php bin/console doctrine:fixtures:load
    ```

- Les identifiants de connexion se trouvent dans `fixtures/user.yaml`.

    email: 'admin@gmail.com'
    password: 'test'
    email: 'user@gmail.com'
    password: 'test'
    email: 'banned@gmail.com'
    password: 'test'

5. **Charger les animes :**
   ```bash
    php bin/console app:fetch-anime
   ```
6. **Charger les mangas :**

   ```bash
    php bin/console app:import-manga
   ```

7. **Test unitaire**

    ```bash
    php bin/console phpunit test/Entity/MangaTest
    ```



8. **Test fonctionnel**

    ```bash
    php bin/console phpunit test/Controller/SecurityControllerTest
    ```

9. **Schéma de la base de données**
![schéma de la base de données](./db.png)
