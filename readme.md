# VipTravel Application

## Installation

Vous pouvez installer le projet de deux façons :

### ✅ 1) Téléchargement direct depuis IONOS
En le téléchargeant directement depuis IONOS :
-> Vip_Travel_Project_ionos.zip  
Dans ce cas, toutes les bibliothèques sont déjà installées et configurées,  
il suffit d’extraire le projet sur votre machine et de lancer les commandes pour démarrer l’application.

### ✅ 2) Installation depuis GitHub
1. Cloner le projet :
   git clone https://github.com/aimaad/Vip_Travel_Project.git
   cd VIP_TRAVEL_PROJECT

2. Installer les dépendances PHP :
   composer install

3. Installer les dépendances Node :
   npm install

4. Générer la clé :
   php artisan key:generate


## Configuration du fichier .env

Ouvrir le fichier .env et configurer :

- Connexion à la base de données :
  DB_DATABASE=xxxx
  DB_USERNAME=xxxx
  DB_PASSWORD=xxxx

- Clés Amadeus :
  AMADEUS_KEY=xxxx
  AMADEUS_SECRET=xxxx

- Clé Duffel :
  DUFFEL_API_KEY=xxxx

- Clé Scraper API :
  SCRAPER_API_KEY=xxxx


## Lancement du projet

Ouvrir 3 terminaux et exécuter :

1) Lancer le serveur Laravel :
   php artisan serve --port=8081

2) Lancer le build front :
   npm run dev

3) Lancer le système de queue (jobs asynchrones) :
   php artisan queue:work

L’application sera accessible sur :
http://127.0.0.1:8081/


## Information sur le code

- Controller principal des offres : OfferController
- Controller principal des hôtels : HotelController


## Mode Asynchrone

Le projet utilise le système de Jobs pour exécuter certaines tâches en arrière-plan.
Tous les jobs se trouvent dans :
app/Jobs/

