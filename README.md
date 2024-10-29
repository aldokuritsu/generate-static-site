# Générateur de Site HTML Statique

## Description
Le **Générateur de Site HTML Statique** est un plugin WordPress qui permet de générer une version statique complète de votre site, incluant toutes les pages, les scripts CSS et JavaScript, et les images associées. Ce plugin est idéal pour réduire les besoins en ressources serveur et améliorer les temps de chargement en servant une version statique de votre site WordPress.

## Fonctionnalités
- Génération automatique d'une version statique complète du site, incluant toutes les pages, articles, images, fichiers CSS et JavaScript.
- Nettoyage automatique du dossier de destination avant chaque génération.
- Organisation des ressources par type (`css`, `js`, `img`) pour une meilleure gestion.
- Réécriture des URLs pour que toutes les ressources soient chargées depuis le dossier statique.

## Installation
1. Téléchargez le dossier du plugin et placez-le dans le répertoire `wp-content/plugins/` de votre site WordPress.
2. Activez le plugin depuis le menu **Extensions** dans le panneau d'administration de WordPress.

## Utilisation
1. Une fois le plugin activé, allez dans **Outils > Génération HTML** dans le tableau de bord WordPress.
2. Sur la page de génération, cliquez sur **Générer HTML** pour lancer la génération de la version statique.
3. Le plugin :
   - Nettoie le dossier de destination (`wp-content/uploads/pages-html/`) pour éviter les fichiers obsolètes.
   - Génère les pages HTML et télécharge toutes les ressources nécessaires dans les sous-dossiers appropriés (`css`, `js`, `img`).
4. À la fin du processus, une liste des pages générées est affichée.

## Structure des Fichiers Générés
Les fichiers statiques sont générés dans le dossier `wp-content/uploads/pages-html/` avec la structure suivante :

pages-html/ 

    ├── index.html // Page d'accueil 

    ├── page1.html // Exemple de page statique 

    ├── post1.html // Exemple d'article statique 

    ├── css/ // Fichiers CSS externes 

        └── styles.css 

    ├── js/ // Fichiers JavaScript externes

        └── script.js 

    └── img/ // Images utilisées sur le site
     
        └── image1.jpg

## Paramétrage et Options
- Le plugin enregistre les pages générées et les ressources associées dans le dossier de destination `pages-html`.
- Si vous souhaitez personnaliser le dossier de destination, vous pouvez modifier le chemin directement dans le code du plugin, dans la variable `$dossier` de `generator.php`.

## Notes de Sécurité
Le plugin assure la sécurité des données téléchargées en nettoyant les URLs et en utilisant les API sécurisées de WordPress pour récupérer les fichiers distants. Cependant, assurez-vous de vérifier les permissions de votre dossier `wp-content/uploads/` pour que WordPress puisse y écrire les fichiers.

## Contributeurs
- **Aldokuritsu** - Développeur du plugin

## Licence
Ce plugin est distribué sous la licence GPL v2 ou supérieure.
