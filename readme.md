# Plateforme de recrutement intelligente pour les recruteurs et les candidats

## Présentation

Ce projet est une application web développée principalement en PHP, CSS et JavaScript. Il s'agit d'une plateforme de gestion de profils, d'offres et d'interactions entre candidats et recruteurs, avec des fonctionnalités avancées telles que l'intégration d'une IA, la gestion des utilisateurs, et plus encore.

## Fonctionnalités principales

- Authentification pour candidats et recruteurs
- Tableau de bord personnalisé pour chaque type d'utilisateur (candidat, recruteur, admin)
- Gestion des offres d'emploi et candidatures
- Intégration d'une IA pour le matching et l'assistance
- Gestion des notifications
- Connexion via Google

## Structure du projet

- `accueil.php` : Page d'accueil du site
- `admin/` : Scripts de gestion des utilisateurs et des offres pour l'administrateur
- `avatars/` : Images de profil des utilisateurs
- `chatbot-CSS/`, `js/`, `css/` : Feuilles de style et scripts JavaScript
- `dashboard-*-CSS/` : Styles pour les différents tableaux de bord
- `dashboard-*.php` : Tableaux de bord pour chaque type d'utilisateur
- `ia/` : Scripts et modèles liés à l'intelligence artificielle
- `firebase-credentials/` : Clés de service pour l'intégration Firebase * dans un .gitignore*
- `offres_france_travail.php` : Intégration avec France Travail
- `hackathon.sql` : Fichier de base de données SQL

## Prérequis

- Serveur web local (ex : MAMP, XAMPP) -- si vous utilisez MAMP n'oubliez pas d'ajouter le mdp root
- PHP 7.x ou supérieur
- Base de données MySQL
- Navigateur web moderne

## Installation

1. Clonez ou téléchargez ce dépôt sur votre machine.
2. Placez le dossier `frontend2` dans le répertoire de votre serveur local (ex : `htdocs` pour MAMP/XAMPP).
3. Importez le fichier `hackathon.sql` dans votre base de données MySQL.
4. Configurez les accès à la base de données dans les fichiers PHP concernés si nécessaire.
5. Lancez le serveur local et accédez à l'application via `http://localhost/frontend2/`.

## Utilisation

- Inscrivez-vous en tant que candidat ou recruteur.
- Connectez-vous pour accéder à votre tableau de bord.
- Gérez votre profil, postulez à des offres, ou publiez des offres selon votre rôle.
- Utilisez la messagerie et explorez les fonctionnalités IA.



