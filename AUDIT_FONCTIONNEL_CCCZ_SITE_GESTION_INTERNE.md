# Audit fonctionnel, organisationnel et technique
## Centre Culturel Congolais « Le Zoo » (CCCZ)

**Date de l’audit :** 11 septembre 2026  
**Périmètre :** site public, Gestion_Interne-CZOO, documentation, PHP, HTML, CSS, JavaScript, migrations et schéma local observé.  
**Statut :** document d’analyse uniquement. Aucun fichier métier n’est modifié par cet audit.

## Légende de vérité

- **[EXISTANT]** : observé dans le code ou la base locale.
- **[PARTIELLEMENT IMPLÉMENTÉ]** : une fondation ou une partie existe, mais le besoin complet n’est pas couvert.
- **[PRÉVU]** : décrit dans la documentation ou la roadmap, sans être opérationnel.
- **[À DÉCIDER]** : nécessite une décision institutionnelle.
- **[PROPOSITION]** : recommandation d’organisation soumise à validation.
- **[PROJET FUTUR]** : fonctionnalité à concevoir et développer.

---

# 1. Résumé exécutif

Le CCCZ dispose actuellement de deux ensembles distincts :

1. **Un site public institutionnel** principalement composé de pages HTML, CSS et JavaScript. Il présente le Centre, sa programmation, ses actualités, sa galerie, ses partenaires et son contact.
2. **Une première application interne PHP**, Gestion_Interne-CZOO, avec connexion, session, RBAC, tableau de bord, modules internes génériques, gestion de comptes et quelques points d’API.

La séparation physique entre le site public et l’application interne est réelle. En revanche, la relation fonctionnelle entre les deux n’est pas encore automatisée : les contenus publics restent largement statiques et la Gestion interne ne publie pas actuellement d’événements ou d’actualités sur le site public.

Le constat central est le suivant :

- le socle technique de connexion, session, CSRF, permissions et audit existe [EXISTANT] ;
- les modules métier existent surtout sous forme de wrappers génériques autour de `internal_items` [PARTIELLEMENT IMPLÉMENTÉ] ;
- les workflows de proposition, vérification, validation, publication et archivage ne sont pas exécutés de bout en bout [PRÉVU / PROJET FUTUR] ;
- le modèle de responsabilités entre directions et services n’est pas suffisamment validé par le CCCZ [À DÉCIDER] ;
- les données publiques et les données internes ne sont pas encore reliées par un circuit de publication [PRÉVU] ;
- la matrice RBAC doit rester documentée et gouvernée avant toute extension fonctionnelle [À DÉCIDER].

La priorité institutionnelle n’est donc pas d’ajouter immédiatement tous les modules imaginables. Elle est de valider l’organigramme, les responsabilités, les circuits de décision et le vocabulaire métier, puis de construire les workflows autour de données métier réelles.

---

# 2. Présentation générale du projet

## 2.1 Mission institutionnelle

D’après la documentation de vision, le Centre Culturel Congolais « Le Zoo » a pour mission de :

- promouvoir la culture congolaise ;
- favoriser la création artistique ;
- construire des espaces de rencontre entre artistes, publics et institutions ;
- transmettre le patrimoine culturel aux générations futures ;
- valoriser les activités, artistes, projets et espaces du Centre.

Cette mission doit guider à la fois la communication publique et la future organisation interne.

## 2.2 Publics identifiés

Le site public vise notamment :

- les visiteurs et publics culturels ;
- les artistes et créateurs ;
- les partenaires institutionnels et privés ;
- les médiateurs culturels ;
- les jeunes, écoles et structures éducatives ;
- les médias ;
- la direction et l’administration du Centre.

## 2.3 Sources examinées

- `docs/README.md` et `docs/AI_CONTEXT.md` ;
- documentation de vision, architecture, sécurité, API, base de données, audit, roadmap et opérations ;
- pages publiques HTML et includes ;
- scripts JavaScript publics ;
- `db.php`, migrations SQL et scripts d’administration ;
- pages PHP de Gestion_Interne-CZOO ;
- modules, contrôleurs génériques, formulaires et API internes.

## 2.4 Limite de l’audit

L’environnement local XAMPP et la base locale sont observables. L’organigramme officiel complet, les délégations de signature, les circuits de validation institutionnels et les règles de production du CCCZ ne sont pas suffisamment documentés dans le dépôt. Toute responsabilité non démontrée est donc marquée : **RESPONSABILITÉ À VALIDER PAR LA DIRECTION.**

---

# 3. Architecture globale

## 3.1 Schéma actuel

```text
Visiteurs / artistes / partenaires / écoles / médias
                         |
                         v
              Site public CCCZ
       HTML + CSS + JavaScript + images
                         |
              contact-submit.php
                         |
                         v
                   MySQL local
               contact_requests

Utilisateurs internes
                         |
                         v
             Gestion_Interne-CZOO
       login + sessions + RBAC + modules PHP
                         |
                         v
                   MySQL local
 users / directions / roles / permissions / audit_logs
 internal_items / internal_documents / agents
```

## 3.2 Architecture cible à confirmer

```text
Proposition d'activité ou de contenu
              |
              v
       Saisie interne par service
              |
              v
       Vérification métier
              |
              v
       Validation hiérarchique
              |
              v
       Approbation de publication
              |
              v
       Site public CCCZ
              |
              v
       Suivi, statistiques et archivage
```

Cette chaîne n’est pas encore automatisée dans le code. Elle doit être validée par la Direction Générale avant développement.

## 3.3 Ce qui est automatisé

[EXISTANT]

- affichage des pages publiques statiques ;
- chargement dynamique du nav/footer publics par JavaScript ;
- traitement du formulaire de contact ;
- stockage MySQL des demandes de contact ;
- authentification interne par session PHP ;
- régénération de session après connexion ;
- expiration de session après une période d’inactivité ;
- vérification CSRF sur les formulaires internes ;
- vérification serveur des permissions ;
- audit de plusieurs actions internes ;
- téléchargement protégé de documents si le stockage est configuré ;
- création d’éléments génériques dans `internal_items`.

## 3.4 Ce qui reste manuel

[EXISTANT / MANUEL]

- création et mise à jour des pages publiques HTML ;
- préparation de nombreuses actualités et événements ;
- intégration des images, vidéos et contenus dans les fichiers ;
- définition des responsabilités entre directions ;
- validation éditoriale et institutionnelle ;
- publication publique depuis la Gestion interne ;
- production de rapports métier structurés ;
- gestion des sauvegardes et du déploiement, non documentée dans le dépôt.

---

# 4. Site public

## 4.1 Pages et fonctions observées

| Page / composant | Fonction | État |
|---|---|---|
| `accueil.html` | Porte d’entrée, univers culturels, événements, espaces, actualités, parcours publics | [EXISTANT] |
| `apropos.html` | Présentation institutionnelle | [EXISTANT] |
| `programmes.html` | Catalogue public et filtres de programmation | [EXISTANT] |
| `actualites.html` | Actualités et agenda présentés côté public | [EXISTANT] |
| `event.html` | Détail d’un événement selon identifiant | [EXISTANT] |
| `galerie.html` | Galerie et valorisation visuelle | [EXISTANT] |
| `partenaires.html` | Présentation des partenaires publics | [EXISTANT] |
| `contact.html` | Formulaire de contact | [EXISTANT] |
| `contact-submit.php` | Enregistrement et tentative d’envoi d’une demande | [EXISTANT] |
| `includes/nav.html` | Navigation publique partagée | [EXISTANT] |
| `includes/footer.html` | Pied de page partagé | [EXISTANT] |
| `js/include-nav.js` | Chargement des fragments publics | [EXISTANT] |
| `js/events-data.js` | Données événementielles côté JavaScript | [EXISTANT / À AUDITER RÉGULIÈREMENT] |

## 4.2 Limites publiques

[PARTIELLEMENT IMPLÉMENTÉ]

- contenu largement statique ;
- absence de CMS public ;
- absence de moteur de recherche structuré ;
- absence d’inscription ou de billetterie observée ;
- absence d’API publique opérationnelle pour événements et actualités ;
- absence de synchronisation avec les modules internes ;
- formulaire de contact sans workflow interne complet visible ;
- politique de publication et d’archivage éditorial non formalisée.

## 4.3 Rôle institutionnel du site

Le site public doit rester la vitrine et le service d’information du Centre. Il ne doit pas devenir l’outil de gestion des directions. La future intégration doit publier des contenus validés, jamais exposer directement les données internes sensibles.

---

# 5. Gestion_Interne-CZOO

## 5.1 Composants réels

| Composant | Fonction | État |
|---|---|---|
| `login.php` | Formulaire de connexion | [EXISTANT] |
| `authenticate.php` | Vérification du compte, session et permissions | [EXISTANT] |
| `logout.php` | Déconnexion et audit | [EXISTANT] |
| `dashboard.php` | Tableau de bord avec indicateurs simples et raccourcis | [PARTIELLEMENT IMPLÉMENTÉ] |
| `profil.php` | Lecture du profil courant | [PARTIELLEMENT IMPLÉMENTÉ] |
| `module.php` | Contrôleur générique de liste/création | [EXISTANT / GÉNÉRIQUE] |
| `includes/auth.php` | Session et contrôle des permissions | [EXISTANT] |
| `includes/functions.php` | CSRF, flash, URL et échappement | [EXISTANT] |
| `includes/permissions.php` | Labels et permission de lecture des modules | [EXISTANT] |
| `includes/header.php` | Shell, menu et navigation RBAC | [EXISTANT] |
| `includes/footer.php` | Footer et JavaScript commun | [EXISTANT] |
| `administration/users.php` | Création et liste des comptes | [PARTIELLEMENT IMPLÉMENTÉ] |
| `administration/user-edit.php` | Modification d’un profil utilisateur | [PARTIELLEMENT IMPLÉMENTÉ] |
| `personnel/agent.php` | Fiche agent et opérations RH | [PARTIELLEMENT IMPLÉMENTÉ] |
| `documents/download.php` | Téléchargement protégé | [PARTIELLEMENT IMPLÉMENTÉ] |
| `api/internal/finances.php` | Lecture JSON d’éléments financiers génériques | [PARTIELLEMENT IMPLÉMENTÉ] |

## 5.2 Modules internes réels

Les portes d’entrée suivantes existent :

- Administration ;
- Personnel ;
- Activités ;
- Projets ;
- Espaces ;
- Culture & Arts ;
- Bibliothèque ;
- Patrimoine ;
- Finances ;
- Intendance ;
- Documents ;
- Partenaires ;
- Rapports.

La majorité de ces portes d’entrée délègue encore à `module.php` et à la table `internal_items`. Elles ne constituent donc pas encore treize systèmes métier complets.

## 5.3 Données internes observées

[EXISTANT]

- `directions` ;
- `roles` ;
- `permissions` ;
- `users` ;
- `user_roles` ;
- `role_permissions` ;
- `audit_logs` ;
- `internal_items` ;
- `internal_documents` ;
- `agents` et `agent_imports` via migration RH ;
- `contact_requests` côté site public.

---

# 6. Directions et responsabilités

Les directions ci-dessous sont seedées dans `db.php` et apparaissent dans le modèle local :

1. Direction Générale ;
2. Direction Administrative ;
3. Direction Financière ;
4. Direction de l’Intendance ;
5. Direction de la Bibliothèque ;
6. Direction de l’Animation et de la Préservation culturelle.

La correspondance officielle entre ces directions, les services internes, les responsables et les délégations n’est pas suffisamment détaillée dans le dépôt.

| Direction / service observé | Responsabilités documentées ou déductibles | Modules associés | Création / modification / validation / publication | État |
|---|---|---|---|---|
| Direction Générale | Orientation, arbitrage et validation institutionnelle supposés | Tous selon mandat | Responsabilités exactes à formaliser | **À VALIDER PAR LA DIRECTION** |
| Direction Administrative | Administration générale, comptes et organisation supposés | Administration, personnel, documents, rapports | Création et validation selon délégation à préciser | [PARTIELLEMENT DÉFINI] |
| Direction Financière | Suivi financier | Finances, rapports | Saisie financière et validation financière à préciser | [PARTIELLEMENT DÉFINI] |
| Direction de l’Intendance | Logistique, stocks, maintenance et moyens | Intendance, espaces, patrimoine | Responsabilité de validation à préciser | [PARTIELLEMENT DÉFINI] |
| Direction de la Bibliothèque | Collections et activités de bibliothèque | Bibliothèque, documents, patrimoine | Responsabilité éditoriale à préciser | [PARTIELLEMENT DÉFINI] |
| Direction de l’Animation et de la Préservation culturelle | Activités culturelles, arts, patrimoine et transmission | Culture & Arts, Activités, Projets, Patrimoine, Espaces | Validation culturelle et publication à préciser | [PARTIELLEMENT DÉFINI] |
| Service RH / gestionnaire RH | Registre des agents, import/export et dossiers RH | Personnel | Données sensibles ; validation hiérarchique à préciser | [PARTIELLEMENT IMPLÉMENTÉ] |

Pour chaque ligne, les responsabilités de publication, de validation finale, de suppression et d’archivage doivent être confirmées par la Direction Générale.

---

# 7. Modules et état fonctionnel

| Module | Base actuelle | Ce qui existe | Ce qui manque pour un vrai module métier |
|---|---|---|---|
| Administration | `internal_items`, `users`, RBAC | Liste/création de comptes, éléments génériques | Directions, rôles, permissions, paramètres et délégations administrables |
| Personnel | `agents`, `agent_imports`, `internal_items` | Registre agent partiel, fiche, suppression logique, export | Workflow RH, historique complet, validation, confidentialité avancée |
| Finances | `internal_items`, API finances | CRUD générique et endpoint de lecture | Budget, recettes, dépenses, pièces, engagements, validation et rapports |
| Intendance | `internal_items` | Liste/création générique | Stocks, demandes, fournisseurs, maintenance, états des lieux |
| Bibliothèque | `internal_items` | Liste/création générique | Catalogue, prêts, collections, inventaire, consultation |
| Culture & Arts | `internal_items` | Liste/création générique | Artistes, œuvres, disciplines, événements, médiation |
| Activités | `internal_items` | Liste/création générique | Activités structurées, calendrier, publics, inscriptions, résultats |
| Projets | `internal_items` | Liste/création générique | Jalons, responsables, budget, livrables, risques |
| Espaces | `internal_items` | Liste/création générique | Disponibilité, réservations, validation, états des lieux |
| Patrimoine | `internal_items` | Liste/création générique | Inventaire, état, localisation, conservation, historique |
| Partenaires | `internal_items` | Liste/création générique | Fiches, conventions, échéances, activités liées |
| Documents | `internal_documents`, `internal_items` | Téléchargement protégé si chemin configuré | Dépôt, classement, versions, validation, archivage, rétention |
| Rapports | `internal_items` | Liste/création générique | Indicateurs fiables, périodes, validation et diffusion |

---

# 8. Workflow des contenus et événements

## 8.1 Workflow cible proposé

```text
BROUILLON
  -> SOUMIS
  -> VÉRIFICATION
  -> VALIDATION MÉTIER
  -> APPROBATION INSTITUTIONNELLE
  -> PUBLICATION
  -> ARCHIVAGE
  -> RAPPORT
```

## 8.2 Statuts actuellement observés

Le catalogue interne définit des statuts différents selon les modules, par exemple :

- `draft`, `in_review`, `approved`, `archived` pour l’administration ;
- `draft`, `scheduled`, `in_progress`, `completed`, `cancelled` pour les activités ;
- `draft`, `planning`, `in_progress`, `completed`, `on_hold` pour les projets ;
- `draft`, `planned`, `in_progress`, `completed`, `archived` pour Culture & Arts ;
- `draft`, `shared`, `validated`, `archived` pour les documents.

Ces statuts existent dans le catalogue PHP mais ne constituent pas encore un moteur de workflow : aucune transition générale, aucun validateur et aucune publication liée au site public ne sont implémentés.

## 8.3 Circuit d’un événement culturel

1. **Proposition :** service porteur ou direction concernée. **À valider.**
2. **Saisie :** agent habilité dans Activités, Culture & Arts ou Projets. [PRÉVU]
3. **Informations :** titre, description, dates, lieu, public, artistes, images et documents. [PRÉVU]
4. **Vérification :** service culturel et service administratif selon le contenu. **À valider.**
5. **Validation :** responsable hiérarchique ou Direction Générale selon seuil. **À décider.**
6. **Publication :** éditeur habilité après approbation. [PRÉVU]
7. **Suivi :** fréquentation, participants, dépenses et partenaires. [PROJET FUTUR]
8. **Clôture :** rapport d’activité et archivage. [PROJET FUTUR]

Le système actuel permet seulement de créer un élément générique, pas de gérer ce circuit complet.

## 8.4 Types d’activités concernés

Les types suivants doivent être supportés par un futur modèle `activities/events`, après validation :

- événement culturel ;
- formation ;
- visite ;
- spectacle ;
- exposition ;
- conférence ;
- atelier ;
- manifestation institutionnelle ;
- activité partenaire.

La liste est une **proposition de classification**, pas une fonctionnalité actuelle.

---

# 9. Actualités et publication

## 9.1 État actuel

Les actualités sont présentées sur des pages publiques HTML/JavaScript. Aucun workflow interne de rédaction, validation et publication n’a été identifié.

**Statut : [EXISTANT côté affichage public] / [PRÉVU côté gestion éditoriale].**

## 9.2 Processus cible proposé

```text
Information reçue
 -> rédaction
 -> vérification factuelle
 -> validation du service responsable
 -> validation institutionnelle si nécessaire
 -> illustration et métadonnées
 -> référencement / tags
 -> publication
 -> archivage
```

## 9.3 Responsabilités à confirmer

| Action | Responsable proposé | État |
|---|---|---|
| Créer une actualité | Éditeur ou service porteur | [PROPOSITION] |
| Modifier | Auteur ou éditeur avant validation | [PROPOSITION] |
| Vérifier | Service responsable du sujet | [PROPOSITION] |
| Valider | Direction concernée / Direction Générale selon sujet | [À DÉCIDER] |
| Publier | Éditeur habilité après approbation | [À DÉCIDER] |
| Supprimer | Administrateur technique avec trace, ou responsable éditorial | [À DÉCIDER] |
| Archiver | Éditeur / service responsable | [À DÉCIDER] |

---

# 10. Tags, catégories et métadonnées

## 10.1 État observé

Les pages publiques utilisent des catégories et paramètres de navigation côté HTML/JavaScript, mais aucun référentiel centralisé de tags et catégories n’a été identifié dans la base interne.

**Statut : [PARTIELLEMENT IMPLÉMENTÉ].**

## 10.2 Organisation proposée

### Catégories fixes

- Événement ;
- Formation ;
- Activité ;
- Visite ;
- Spectacle ;
- Exposition ;
- Atelier ;
- Conférence ;
- Actualité ;
- Partenaire ;
- Artiste ;
- Projet ;
- Bibliothèque ;
- Patrimoine ;
- Culture ;
- Art ;
- Documentation.

### Tags contrôlés

Les tags comme `jeunesse`, `médiation`, `résidence`, `patrimoine`, `Kinshasa`, `partenariat`, `éducation` devraient être administrés dans un référentiel contrôlé pour éviter les doublons.

### Règles proposées

- une catégorie principale obligatoire ;
- plusieurs tags contrôlés ;
- création d’un tag soumise à l’administration éditoriale ;
- fusion des doublons par un administrateur ;
- historique des changements ;
- filtres par catégorie, date, public, lieu et direction responsable.

Ces règles sont **[PROPOSITION]**.

---

# 11. Partenaires

## 11.1 État actuel

Le site public contient une page partenaires. Un module interne `partenaires` existe, mais repose sur des éléments génériques.

**Statut : [PARTIELLEMENT IMPLÉMENTÉ].**

## 11.2 Cycle proposé

```text
Nouveau partenaire
 -> fiche institutionnelle
 -> vérification des coordonnées
 -> convention ou document associé
 -> validation interne
 -> activités liées
 -> publication éventuelle
 -> suivi des échéances
 -> archivage
```

## 11.3 Responsabilités

- création : service relationnel ou direction porteuse [À VALIDER] ;
- vérification : service administratif et direction concernée [À VALIDER] ;
- conventions : Administration / Direction Générale selon délégation [À VALIDER] ;
- publication : éditeur après accord [À DÉCIDER] ;
- suivi : responsable du partenariat [À DÉCIDER].

---

# 12. Artistes et intervenants

Aucune base complète d’artistes n’est actuellement observée dans la Gestion interne. Le site public contient des visuels et références artistiques, mais pas de gestion structurée de profils.

**Statut : [PARTIELLEMENT IMPLÉMENTÉ côté présentation] / [PROJET FUTUR côté gestion].**

Cycle proposé :

```text
Création de fiche
 -> identité professionnelle
 -> discipline et biographie
 -> médias vérifiés
 -> participation à un événement
 -> validation éditoriale
 -> publication éventuelle
 -> historique des participations
```

Permissions futures à définir : `artists.read`, `artists.create`, `artists.update`, `artists.validate`, `artists.publish`, `artists.archive`. Ces permissions sont **[PROPOSITION]** et ne doivent pas être ajoutées sans validation du modèle métier.

---

# 13. Visites

Aucun module de visites structuré n’est observé.

**Statut : [PROJET FUTUR].**

Cycle recommandé :

```text
Demande de visite
 -> enregistrement du groupe
 -> date et nombre de visiteurs
 -> espace souhaité
 -> disponibilité
 -> validation
 -> accueil
 -> présence réelle
 -> statistiques
 -> rapport
```

Données possibles : établissement, responsable, contact, taille du groupe, public jeune/adulte, date, espaces visités, besoins spécifiques, agent d’accueil, résultat.

Services concernés : accueil, animation culturelle, espaces, administration. **Responsabilité à valider par la Direction.**

---

# 14. Formations

Aucune gestion structurée de formation, inscription, présence ou certificat n’est observée.

**Statut : [PROJET FUTUR].**

```text
Proposition
 -> validation
 -> programmation
 -> inscription
 -> participants
 -> présence
 -> évaluation
 -> certificat éventuel
 -> rapport
 -> archivage
```

La validation pédagogique, la délivrance d’un certificat et la conservation des listes de participants doivent être décidées institutionnellement.

---

# 15. Espaces du Centre

Les espaces sont représentés dans le site public par des images et descriptions, et dans Gestion_Interne-CZOO par le module générique `espaces`.

Espaces identifiés côté public :

- salle de spectacle ;
- bibliothèque ;
- galerie ;
- village des artistes ;
- espaces culturels ;
- patrimoine et documentation ;
- jardin / espaces extérieurs selon les contenus présentés.

**Statut : [PARTIELLEMENT IMPLÉMENTÉ].**

Workflow cible :

```text
Demande
 -> disponibilité
 -> réservation
 -> validation
 -> utilisation
 -> état des lieux
 -> rapport
```

La séparation entre réservation publique, réservation interne et autorisation institutionnelle doit être décidée avant développement.

---

# 16. Finances et intendance

## 16.1 Finances

[EXISTANT]

- module `finances` générique ;
- permissions de lecture/création/mise à jour ;
- endpoint interne GET protégé par session et `finances.read` ;
- table `internal_items` pouvant contenir des éléments génériques.

[NON OBSERVÉ]

- plan budgétaire métier ;
- recettes et dépenses structurées ;
- pièces justificatives ;
- engagements ;
- validation financière ;
- billetterie ;
- rapprochement ou clôture ;
- rapport financier institutionnel.

## 16.2 Intendance

[EXISTANT] module générique `intendance`.  
[NON OBSERVÉ] stock structuré, demandes d’achat, fournisseurs, maintenance, affectation de matériel, inventaire et états des lieux.

Toute évolution financière ou logistique doit être conçue avec validation de la Direction Financière et de l’Intendance. Il ne faut pas présenter le module générique actuel comme un système comptable.

---

# 17. Documents et archives

## 17.1 État actuel

- table `internal_documents` ;
- téléchargement protégé par `documents.download` ;
- contrôle du chemin de stockage via `INTERNAL_DOCUMENTS_PATH` ;
- audit du téléchargement ;
- aucun upload métier complet observé.

**Statut : [PARTIELLEMENT IMPLÉMENTÉ].**

## 17.2 Cycle proposé

```text
Création
 -> dépôt
 -> classement
 -> vérification
 -> validation
 -> consultation
 -> archivage
 -> conservation / destruction selon politique
```

## 17.3 Classement recommandé

- administratif ;
- rapports ;
- contrats et conventions ;
- projets ;
- culture et arts ;
- patrimoine ;
- finances ;
- ressources humaines ;
- photos ;
- vidéos ;
- publications.

La politique de conservation, les niveaux de confidentialité, les versions, les droits de téléchargement et la durée de rétention sont **[À DÉCIDER]**.

---

# 18. Rapports et indicateurs

## 18.1 Indicateurs actuellement disponibles

- nombre de demandes de contact publiques ;
- nombre de permissions en session ;
- état de session ;
- nombre d’éléments génériques dans certains modules ;
- liste d’éléments récents pour quelques modules ;
- export du registre des agents si permission dédiée.

**Statut : [EXISTANT MAIS LIMITÉ].**

## 18.2 Indicateurs à développer

| Public | Indicateurs possibles | État |
|---|---|---|
| Agents | tâches, activités saisies, formations suivies, accès | [PROJET FUTUR] |
| Directions | activités, projets, documents, budget, échéances | [PROJET FUTUR] |
| Direction Générale | événements, fréquentation, partenaires, finances, risques | [PROJET FUTUR] |
| Partenaires | activités associées, conventions, livrables, visibilité | [PROJET FUTUR] |

Indicateurs métier envisageables : événements, activités, visiteurs, formations, participants, artistes, partenaires, projets, espaces utilisés, recettes, dépenses, publications et évolution mensuelle/annuelle. Ils ne doivent être calculés qu’après définition de sources de données fiables.

---

# 19. Rôles et permissions

## 19.1 Modèle actuel

```text
Utilisateur
   -> Direction
   -> Rôle(s)
   -> Permissions
   -> Modules et actions autorisés
```

Tables observées : `users`, `directions`, `roles`, `permissions`, `user_roles`, `role_permissions`.

Les pages vérifient côté serveur les permissions. Le masquage du menu ne constitue pas une protection suffisante.

## 19.2 Rôles observés

- administrateur ;
- directeur ;
- editeur ;
- finance ;
- intendance ;
- bibliotheque ;
- gestionnaire_projets ;
- gestionnaire_rh.

## 19.3 Matrice synthétique actuelle

| Rôle | Lire | Créer / modifier | Valider | Publier | Archiver | Administrer |
|---|---|---|---|---|---|---|
| Administrateur | Tous les modules | Permissions techniques larges selon catalogue | Non défini métier | Non défini | Non défini | `admin.users`, `profile.update`, configuration technique partielle |
| Directeur | Lecture générale | Pas de droits métier larges documentés | Non implémenté | Non implémenté | Non implémenté | Non |
| Editeur | Culture, activités, projets, documents | Création/mise à jour de ces contenus selon matrice actuelle | Non implémenté | Non implémenté | Non implémenté | Non |
| Finance | Finances | Finances | Non implémenté | Non | Non | Non |
| Intendance | Intendance | Intendance | Non implémenté | Non | Non | Non |
| Bibliothèque | Bibliothèque | Bibliothèque | Non implémenté | Non | Non | Non |
| Gestionnaire projets | Projets | Projets | Non implémenté | Non | Non | Non |
| Gestionnaire RH | Personnel | Création, mise à jour, suppression logique, export/import RH | Non implémenté | Non | Non | Non |

Les colonnes validation, publication et archivage ne doivent pas être considérées comme fonctionnelles tant que les permissions et transitions correspondantes ne sont pas implémentées.

## 19.4 Incohérences et risques RBAC

- les anciennes migrations et le runtime ont historiquement divergé sur `profile.update` ; une migration corrective explicite est nécessaire et doit rester appliquée ;
- `administrateur` est un rôle technique, mais son accès à toutes les données métier doit être distingué de la responsabilité de validation métier ;
- la relation `direction_id` existe, mais son effet de filtrage métier n’est pas démontré partout ;
- certaines permissions existent sans page ou workflow correspondant ;
- les rôles multiples et les délégations temporaires ne sont pas documentés ;
- l’archivage, la publication et la validation n’ont pas encore de permissions métier dédiées.

---

# 20. Super Administrateur

## 20.1 Distinction essentielle

Le Super Administrateur doit administrer la configuration technique et fonctionnelle du système. Il ne doit pas devenir automatiquement le responsable métier des contenus, des finances, de la culture ou des validations institutionnelles.

## 20.2 Capacités proposées

[PROPOSITION / PROJET FUTUR]

- utilisateurs ;
- directions ;
- rôles ;
- permissions ;
- catégories ;
- tags contrôlés ;
- statuts ;
- paramètres ;
- espaces ;
- types d’activités et d’événements ;
- workflows ;
- modèles de documents ;
- journaux et rétention ;
- intégrations ;
- paramètres du site.

## 20.3 Limites à préserver

- ne pas valider un contenu à la place du responsable métier sans délégation ;
- ne pas publier automatiquement tout contenu ;
- ne pas consulter les données RH ou financières sans justification et journalisation ;
- ne pas modifier l’historique d’audit ;
- ne pas supprimer définitivement des données sans procédure approuvée.

---

# 21. Notifications

Aucun centre de notifications interne structuré n’est observé.

**Statut : [PROJET FUTUR].**

Notifications à envisager :

- contenu soumis à validation ;
- échéance de projet ;
- convention arrivant à expiration ;
- demande de visite ;
- document à vérifier ;
- échec de connexion répété ;
- changement de rôle ;
- tâche assignée ;
- publication approuvée ou refusée.

Canaux possibles : notification interne, e-mail institutionnel, éventuellement messagerie externe. Les destinataires, la confidentialité et l’archivage des notifications sont **[À DÉCIDER]**.

---

# 22. Scénarios d’utilisation

## Scénario 1 — Le CCCZ organise un spectacle

**Qui commence ?** Service culturel ou direction porteuse.  
**Qui saisit ?** Agent habilité dans Activités/Culture & Arts.  
**Qui vérifie ?** Responsable culturel et administratif.  
**Qui valide ?** Autorité désignée par la Direction Générale.  
**Qui publie ?** Éditeur après approbation.  
**Qui suit ?** Culture, espaces, intendance, finances.  
**Qui archive ?** Service porteur avec documents et rapport.  
**État actuel :** données et workflow complet non implémentés.

## Scénario 2 — Un partenaire propose une conférence

**Commence :** partenaire ou service relationnel.  
**Saisie :** fiche partenaire et activité.  
**Vérification :** service concerné.  
**Validation :** direction compétente.  
**Publication :** éditeur après accord.  
**Suivi :** partenaire, activité, convention et rapport.  
**Archivage :** documents et bilan.  
**Responsabilités :** à valider.

## Scénario 3 — Une école demande une visite

**Commence :** école via contact ou futur formulaire de visite.  
**Saisie :** accueil ou animation culturelle.  
**Vérification :** disponibilité et capacité des espaces.  
**Validation :** responsable des visites à désigner.  
**Publication :** aucune publication nécessaire, sauf communication autorisée.  
**Suivi :** présence et nombre de visiteurs.  
**Archivage :** demande et rapport.

## Scénario 4 — Une formation est organisée

**Commence :** service culturel ou pédagogique.  
**Saisie :** formation, formateur, dates, participants.  
**Vérification :** contenu, budget et capacité.  
**Validation :** direction concernée.  
**Publication :** éditeur.  
**Suivi :** inscriptions, présences et évaluation.  
**Rapport :** responsable de formation.

## Scénario 5 — Une actualité doit être publiée

**Commence :** service porteur.  
**Saisie :** éditeur ou rédacteur.  
**Vérification :** faits, dates, images et droits.  
**Validation :** responsable éditorial / direction si nécessaire.  
**Publication :** éditeur habilité.  
**Archivage :** date de retrait et conservation du contenu.

## Scénario 6 — Un artiste rejoint la programmation

**Commence :** service culturel.  
**Saisie :** fiche artiste et lien avec événement.  
**Vérification :** identité, biographie, consentement et médias.  
**Validation :** responsable culturel.  
**Publication :** après accord de l’artiste et validation éditoriale.  
**Suivi :** participations et contrats.

## Scénario 7 — Une convention partenaire est signée

**Commence :** direction ou service porteur.  
**Saisie :** partenaire, convention, dates et obligations.  
**Vérification :** Administration et Direction Générale selon délégation.  
**Validation :** autorité signataire.  
**Publication :** uniquement si autorisée.  
**Suivi :** échéances et livrables.  
**Archivage :** document signé et rapports.

## Scénario 8 — Une exposition est organisée dans la galerie

**Commence :** Culture & Arts ou artiste partenaire.  
**Saisie :** exposition, œuvres, calendrier, espace et besoins.  
**Vérification :** conservation, sécurité, communication et budget.  
**Validation :** direction culturelle et autorité désignée.  
**Publication :** programme public.  
**Suivi :** fréquentation, état des œuvres et bilan.

## Scénario 9 — Une direction produit son rapport mensuel

**Commence :** responsable de direction.  
**Saisie :** activités, résultats, difficultés, indicateurs et pièces.  
**Vérification :** Administration ou Direction Générale selon circuit.  
**Validation :** directeur de la direction.  
**Publication :** interne uniquement, sauf décision contraire.  
**Archivage :** rapport versionné.

## Scénario 10 — Un directeur consulte les statistiques globales

**Commence :** directeur ou Direction Générale.  
**Saisie :** aucune, consultation de données validées.  
**Vérification :** qualité et période des données.  
**Validation :** gouvernance des indicateurs.  
**Publication :** aucune par défaut.  
**Suivi :** décisions de pilotage.  
**État actuel :** seuls des indicateurs simples existent.

---

# 23. Matrice globale des services

| Activité | Service responsable | Services associés | Saisie | Vérification | Validation | Publication | Rapport |
|---|---|---|---|---|---|---|---|
| Événement | Culture / service porteur | Administration, espaces, finances | Agent habilité | Culture + administratif | À valider | Éditeur | Service porteur |
| Formation | Culture / pédagogie | Administration, partenaires | Responsable formation | Contenu et capacité | À valider | Éditeur | Responsable formation |
| Activité | Service porteur | Culture, intendance, espaces | Agent habilité | Responsable service | À valider | Éditeur | Service porteur |
| Visite | Accueil / animation | Espaces, écoles | Agent d’accueil | Disponibilité | À valider | Non par défaut | Accueil |
| Actualité | Édition / service porteur | Direction concernée | Rédacteur | Responsable sujet | À valider | Éditeur | Éditorial |
| Partenaire | Relations / direction porteuse | Administration, finances | Agent habilité | Coordonnées et convention | À valider | Éditeur | Responsable partenariat |
| Artiste | Culture & Arts | Édition, administration | Agent culturel | Identité et droits | À valider | Éditeur | Culture |
| Projet | Direction porteuse | Finances, partenaires | Chef de projet | Jalons et budget | À valider | Selon projet | Chef de projet |
| Espace | Intendance / espaces | Culture, accueil | Gestionnaire espace | Disponibilité et état | À valider | Non par défaut | Gestionnaire espace |
| Bibliothèque | Bibliothèque | Culture, patrimoine | Bibliothécaire | Inventaire | À valider | Éditeur si contenu public | Bibliothèque |
| Patrimoine | Culture / préservation | Bibliothèque, documentation | Agent désigné | État et provenance | À valider | Selon autorisation | Patrimoine |
| Finances | Direction Financière | Directions porteuses | Agent financier | Pièces et règles | Direction Financière | Interne | Finance |
| Intendance | Intendance | Espaces, finances | Agent intendance | Besoin et stock | Direction concernée | Non | Intendance |
| Personnel | Administration / RH | Directions | Gestionnaire RH | Données justificatives | Autorité RH | Non | RH |
| Document | Service producteur | Administration, archives | Agent habilité | Classement et confidentialité | Responsable service | Selon type | Producteur |
| Rapport | Direction / service | Administration, Direction Générale | Responsable | Cohérence données | Direction | Interne sauf décision | Auteur |
| Galerie | Édition / culture | Artistes, patrimoine | Éditeur | Droits et légendes | À valider | Éditeur | Éditorial |
| Spectacle | Culture & Arts | Espaces, intendance, finances | Service culturel | Fiche complète | À valider | Éditeur | Culture |
| Exposition | Culture & Arts | Patrimoine, espaces | Curateur/service | Œuvres et sécurité | À valider | Éditeur | Culture |
| Conférence | Culture / partenaire | Partenaire, communication | Service porteur | Intervenants et dates | À valider | Éditeur | Service porteur |
| Atelier | Culture / médiation | Écoles, espaces | Médiation | Public et moyens | À valider | Éditeur | Médiation |

Cette matrice est un support de travail. Les responsables nominatifs et délégations restent **À VALIDER PAR LA DIRECTION**.

---

# 24. Points nécessitant une décision de la Direction

## Urgent

- valider l’organigramme réel et les six directions initialement présentes ;
- confirmer les huit rôles et leurs périmètres ;
- désigner qui peut valider et publier un contenu ;
- décider si la Gestion interne est un outil local de préparation ou un système de production ;
- confirmer la politique de mots de passe, comptes et révocation ;
- confirmer le niveau de confidentialité des données RH, financières et partenaires.

## Important

- approuver le workflow institutionnel commun ;
- approuver le vocabulaire officiel des catégories, statuts et tags ;
- désigner le responsable éditorial ;
- définir les règles de conservation documentaire et d’audit ;
- définir la source officielle des statistiques ;
- décider les règles de publication des artistes, partenaires, images et conventions.

## À planifier

- modèles métier pour événements, formations, visites, partenaires et artistes ;
- circuit des rapports périodiques ;
- calendrier institutionnel ;
- réservation des espaces ;
- gestion des demandes internes ;
- connexion contrôlée entre Gestion interne et site public.

## À étudier

- billetterie ;
- newsletter ;
- intégration réseaux sociaux ;
- intégration YouTube ;
- notifications ;
- automatisation ou IA ;
- API publique et architecture backend durable ;
- hébergement de production, HTTPS, sauvegardes et supervision.

---

# 25. Fonctionnalités manquantes et projets à prévoir

| Projet / fonctionnalité | Objectif | Utilisateurs | Direction | Priorité | Difficulté | Dépendances |
|---|---|---|---|---|---|---|
| Workflow de validation | Faire passer un contenu de brouillon à publication | Services, directeurs, éditeurs | Toutes | Haute | Élevée | Organigramme, statuts |
| Calendrier institutionnel | Centraliser événements, activités et échéances | Tous | Culture / Administration | Haute | Moyenne | Événements structurés |
| Gestion des événements | Remplacer les éléments génériques par des fiches métier | Culture, éditeurs | Animation culturelle | Haute | Élevée | Modèle validé |
| Gestion des partenaires | Suivre fiches, conventions et échéances | Directions, administration | À valider | Haute | Moyenne | Politique partenariats |
| Gestion des artistes | Structurer profils, médias et participations | Culture, édition | Culture & Arts | Moyenne | Moyenne | Consentements et droits |
| Gestion des visites | Enregistrer demandes, accueil et statistiques | Accueil, écoles | À valider | Moyenne | Moyenne | Espaces, calendrier |
| Gestion des formations | Inscriptions, présences et évaluations | Formateurs, participants | Culture | Moyenne | Élevée | Politique pédagogique |
| Réservation des espaces | Éviter les conflits de planning | Espaces, culture | Intendance / Culture | Haute | Élevée | Calendrier et règles |
| Gestion documentaire | Dépôt, version, validation et archivage | Tous | Administration | Haute | Moyenne | Politique documentaire |
| Tableau de bord institutionnel | Fournir des indicateurs fiables | Directeurs, DG | Toutes | Haute | Élevée | Données structurées |
| Centre de notifications | Signaler validations et échéances | Tous | Toutes | Moyenne | Moyenne | Workflow |
| Médiathèque | Organiser photos, vidéos et droits | Édition, culture | Culture / édition | Moyenne | Moyenne | Politique médias |
| Gestion des équipements | Suivre matériel, affectation et maintenance | Intendance | Intendance | Moyenne | Moyenne | Inventaire |
| Billetterie | Suivre inscriptions, ventes et fréquentation | Public, finances | À décider | À étudier | Élevée | Modèle financier |
| Newsletter | Diffuser les actualités validées | Public, édition | Communication | À étudier | Moyenne | Consentement, outil d’envoi |
| Recherche interne et publique | Retrouver contenus et documents | Agents, publics | Toutes | Moyenne | Moyenne | Métadonnées |
| Registre des décisions | Tracer validations et arbitrages | Directeurs, DG | Administration | Haute | Moyenne | Audit et workflow |
| Plan de sauvegarde | Restaurer données et documents | Administrateur | Direction / IT | Haute | Moyenne | Infrastructure |

---

# 26. Feuille de route

## Phase 1 — Stabilisation

- valider organigramme et responsables ;
- stabiliser routes, sessions, RBAC et secrets ;
- documenter les migrations ;
- établir sauvegardes et procédure de restauration ;
- auditer les liens et formulaires publics ;
- clarifier les environnements local, staging et production.

## Phase 2 — Fonctionnalités métier

- définir les modèles événements, activités, partenaires, artistes, espaces et documents ;
- remplacer progressivement `internal_items` par des entités métier ;
- structurer le registre RH ;
- préciser finances et intendance sans simuler une comptabilité complète.

## Phase 3 — Workflow et validation

- statuts communs ;
- transitions contrôlées ;
- responsables et délégations ;
- validation métier ;
- approbation de publication ;
- journal des décisions ;
- archivage.

## Phase 4 — Tableaux de bord

- indicateurs par direction ;
- indicateurs Direction Générale ;
- rapports périodiques ;
- suivi des événements, visiteurs, partenaires et projets ;
- export contrôlé.

## Phase 5 — Intégrations

- publication contrôlée vers le site public ;
- calendrier ;
- médiathèque ;
- notifications ;
- newsletter et réseaux sociaux si validés ;
- API documentée.

## Phase 6 — Automatisation / IA

- aide à la classification ;
- détection de doublons ;
- génération assistée de brouillons ;
- synthèses de rapports ;
- alertes d’échéances ;
- jamais de validation ou publication automatique sans responsabilité humaine.

---

# 27. Risques et recommandations

## Risques fonctionnels

- confusion entre module générique et véritable module métier ;
- absence de workflow validé ;
- responsabilités non attribuées ;
- statistiques produites à partir de données incomplètes ;
- publication sans validation institutionnelle.

## Risques techniques

- bootstrap du schéma dans `db.php` en parallèle des migrations ;
- absence de suivi formel des migrations appliquées ;
- déploiement et production non documentés ;
- dépendance aux chemins locaux et absolus ;
- APIs et intégrations encore limitées ;
- tests runtime et tests d’intégration incomplets.

## Risques de sécurité et gouvernance

- credentials locaux et configuration à externaliser ;
- absence de mécanisme anti-brute-force documenté ;
- droits d’administrateur potentiellement très larges ;
- données RH et financières sensibles ;
- politique de conservation des logs non définie ;
- sauvegardes et restauration à formaliser.

## Recommandations

1. Ne pas annoncer comme opérationnel un workflow non implémenté.
2. Faire valider l’organigramme et la matrice des responsabilités avant d’ajouter des permissions.
3. Conserver une séparation stricte entre administration technique et validation métier.
4. Remplacer progressivement les écrans génériques par des modèles métier validés.
5. Établir un registre des décisions de la Direction.
6. Définir une politique documentaire, de confidentialité et de rétention.
7. Tester chaque évolution sur une base de staging avant production.
8. Mettre en place une stratégie de sauvegarde vérifiée par restauration réelle.
9. Documenter toutes les transitions de workflow et leurs responsables.
10. Prévoir des tests de sécurité, d’autorisation et de non-régression.

---

# 28. Conclusion

Le CCCZ dispose d’un site public riche et d’une première fondation de Gestion_Interne-CZOO. Le socle technique permet de poursuivre, mais l’application interne ne doit pas encore être décrite comme un système métier complet ni comme un workflow institutionnel automatisé.

La prochaine décision importante n’est pas uniquement technique. Elle concerne la gouvernance : qui propose, qui saisit, qui vérifie, qui valide, qui publie, qui archive et qui rend compte pour chaque activité du Centre.

Une fois ces responsabilités validées par la Direction Générale, le projet pourra évoluer de manière maîtrisée vers :

- des modules métier spécialisés ;
- un workflow de validation ;
- une publication publique contrôlée ;
- des indicateurs fiables ;
- une gestion documentaire et patrimoniale durable ;
- une séparation claire entre administration technique et responsabilité métier.

**Conclusion opérationnelle :** le projet est exploitable comme base locale et comme prototype structuré. Il nécessite encore des décisions institutionnelles, la formalisation des données métier et la construction progressive des workflows avant une mise en production complète.
