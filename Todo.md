
# Plan de creation (priorite)

## Repartition binome
- A: socle technique, auth, logique metier, espace employe
- B: RH + admin, vues, CRUD referentiels
- A/B: tests finaux et integration

## P0 - Socle qui fait tourner l app
- [x] Configuration base de donnees (.env)
	- Definir DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
	- Verifier que CI4 charge le bon environnement
- [x] Migrations et seeders
	- Verifier 5 tables: departements, types_conge, employes, soldes, conges
	- Corriger les contraintes manquantes (unique email, FK, etc.)
	- Creer/mettre a jour le seeder de demo avec comptes de test
- [] Routes (app/Config/Routes.php) [A]
	- Declarer routes publiques: /, /login (GET), /login (POST), /logout (GET)
	- Declarer routes protegees par role: /employe/*, /rh/*, /admin/*
	- Activer filtres d auth et de role

## P1 - Authentification et securite (prerequis pour toutes pages)
- [] Controller Auth [A]
	- Methode loginForm(): affiche la vue connexion
	- Methode login(): valide les champs, verifie mot de passe, ouvre la session
	- Methode logout(): detruit la session et redirige
- [] Filtre Auth [A]
	- Refuser l acces si non connecte
	- Redirection vers /login avec flash message
- [] Verif role dans chaque controller [A]
	- Employer role employe, rh, admin
	- Bloquer acces si role invalide
- [] Validation [A]
	- Email obligatoire et format valide
	- Password obligatoire
- [] Session [A]
	- Stocker id, role, nom, departement_id
	- Utiliser flashdata pour succes/erreur

## P2 - Base modele + services (logique metier)
- [] Model Employe [A]
	- Champs: nom, prenom, email, password, role, departement_id, actif
	- Methodes: findByEmail(), isActive()
- [] Model Departement [B]
	- CRUD simple
- [] Model TypeConge [B]
	- CRUD simple + deductible
- [] Model Solde [A]
	- getSolde(employe_id, type_conge_id, annee)
	- updatePrise(employe_id, type_conge_id, annee, nb_jours)
- [] Model Conge [A]
	- createDemande(), listByEmploye(), listByStatut(), updateStatut()
- [] Service CalculConge [A]
	- Calcul nb_jours (jours ouvrables si requis)
	- Verifier solde suffisant
	- Bloquer chevauchements de dates
	- Appliquer regle: solde deduit uniquement a l approbation

## P3 - Espace employe (pages fonctionnelles)
- [] Routes /employe/dashboard, /employe/demande, /employe/mes-demandes [A]
- [] Controller Employe [A]
	- dashboard(): stats + soldes + dernieres demandes
	- formulaire(): affiche la page nouvelle demande
	- submitDemande(): cree la demande, statut en_attente
	- listDemandes(): affiche toutes les demandes de l employe
	- cancelDemande(): annule si en_attente
- [] Views [A]
	- dashboard_employe: injecter vraies donnees
	- formulaire_demande: afficher types_conge + validation erreurs
	- demandes_employe: liste + actions
- [] Validation specifique [A]
	- date_debut <= date_fin
	- type_conge_id obligatoire
	- pas de chevauchement dates actives

## P4 - Espace RH (validation demandes)
- [] Routes /rh/demandes, /rh/demandes/{id}, /rh/valider, /rh/refuser [B]
- [] Controller RH [B]
	- index(): liste des demandes en attente + filtres
	- approuver(): verifie solde, maj statut, maj solde
	- refuser(): maj statut + commentaire
	- historique(): liste toutes demandes
- [] Views [B]
	- list_rh: integrer donnees reelles + filtre departement/statut
	- page de detail (si besoin)
- [] Regles metier cle [A]
	- Avant approbation: verifier solde (jours_pris + nb_jours <= jours_attribues)
	- A l approbation: incrementer jours_pris
	- Si annule/refuse apres approbation: decrementer jours_pris

## P5 - Espace admin (CRUD + supervision)
- [] Routes /admin/dashboard, /admin/employes, /admin/departements, /admin/types, /admin/soldes [B]
- [] Controller Admin [B]
	- dashboard(): stats globales
	- CRUD Employes: create/edit/disable
	- CRUD Departements
	- CRUD TypesConge
	- Gestion Soldes annuels
- [] Views [B]
	- dashboard_admin: stats reelles
	- gestion_employes: liste + formulaire
	- pages departements/types/soldes

## P6 - Qualite, messages, tests manuels
- [] Messages flash coherents pour chaque action [B]
- [] Validation server-side partout [A]
- [] Gestion erreurs (404, acces refuse) [A]
- [] Tests manuels: employe, rh, admin [A/B]

## P7 - Finition livrable
- [] README: installation, migrations, seed, comptes de test [B]
- [] Nettoyage UI (coherence des boutons, messages) [B]
- [] Donnees demo completes [A]
