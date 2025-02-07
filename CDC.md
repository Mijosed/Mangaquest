# Cahier des charges

**1. Introduction** MangaQuest est une application web permettant d'accéder aux scans de mangas via l'API de MangaDex et aux informations sur les animes via l'API d'AniList. L'application proposera un espace de discussion sous forme de forum. Ce projet sera réalisé avec le framework Symfony et la bibliothèque Tailwind CSS pour le design.

**2. Objectifs généraux**

- Fournir une expérience utilisateur intuitive et rapide pour rechercher, consulter et discuter des mangas et animes.
- Garantir la sécurité et la scalabilité du site web.
- Offrir un espace communautaire pour les utilisateurs.

**3. Fonctionnalités principales**

**3.1 Gestion des utilisateurs**

- Inscription et connexion sécurisées (authentification avec gestion de mots de passe chiffrés).
- Rôles utilisateurs :
    - Administrateur : gestion complète des contenus et utilisateurs.
    - Modérateur : gestion des discussions sur le forum.
    - Utilisateur : accès aux contenus et participation au forum.

**3.2 Accès aux ressources**

- Intégration avec l'API de MangaDex pour afficher les scans de mangas.
- Intégration avec l'API d'AniList pour afficher des informations sur les animes.

**3.3 Forum communautaire**

- Espaces de discussion par sujet.
- Possibilité de créer, éditer et supprimer ses propres messages.
- Modération par les administrateurs et modérateurs.

**3.4 Evenements**

- Possibilité de créer et de participer à des évenements 

**3.5 Gestion administrative**

- Espace d'administration pour gérer les utilisateurs, discussions, et contenus provenant des API externes.
- Système de permissions basé sur les rôles définis.

**3.6 Notifications**

- Envoi de mails pour confirmation d'inscription, réinitialisation de mot de passe, et notifications importantes.

**3.7 Contact**

- Possibilité de contacter par mail les administrateurs du site

**4. Modélisation des données**

- **Entités :** Minimum 10 entités, avec héritage
- **Relations :**
    - Au moins 2 relations ManyToMany
    - Au moins 8 relations OneToMany

**5. Critères techniques**

- **Authentification :**
    - Système d'authentification sécurisé avec Symfony Security.
    - Implémentation d'un voter personnalisé pour contrôler l'accès à certaines ressources.
- **Requêtes personnalisées :** Utilisation de query builder dans les repositories pour optimiser les requêtes et répondre à des besoins spécifiques (exemple : recherche avancée dans les mangas).
- **Tests :**
    - Minimum 1 test unitaire pour valider des fonctions critiques.
    - Minimum 1 test fonctionnel pour vérifier le bon fonctionnement des cas d'utilisation clés.
- **Formulaires dynamiques :** Exemple : formulaire de recherche adapté aux critères choisis, avec chargement dynamique des résultats.

**6. Design et ergonomie**

- Utilisation de Tailwind CSS pour créer une interface moderne et réactive.
- Responsive design pour une compatibilité avec les différents appareils (mobile, tablette, desktop).

**7. Livrables**

- Application fonctionnelle avec les critères de fonctionnalité et de sécurité mentionnés.
- Documentation ( README )

**8. Planning et suivi**

- Suivi du projet et gestion des taches via Notion
- Étapes principales :
    1. Analyse des besoins et modélisation des données.
    2. Développement des fonctionnalités de base (authentification, API).
    3. Intégration des API externes et implémentation des relations entre entités.
    4. Création de l'espace admin et des pages utilisateur.
    5. Implémentation des tests unitaires et fonctionnels.
    6. Tests et ajustements.
    7. Livraison et déploiement.

**9. Pages prévues**

1. Page d'accueil.
2. Page de connexion/inscription.
3. Page de profil utilisateur.
4. Page de gestion administrative.
5. Page de liste des manga
6. Page de détails d'un manga.
7. Page de liste des animes.
8. Page de détails d'un anime.
9. Communauté : liste des sujets.
10. Communauté : détails d'un sujet avec messages.
11. Mes favoris
12. Évènements