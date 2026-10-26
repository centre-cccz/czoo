# CCCZ — Modèle de gouvernance numérique
## Deuxième passe d’analyse fonctionnelle, organisationnelle et technique

**Date :** 11 septembre 2026  
**Base :** `AUDIT_FONCTIONNEL_CCCZ_SITE_GESTION_INTERNE.md` et état réel du dépôt  
**Statut :** proposition de gouvernance soumise à validation humaine  
**Important :** aucun code applicatif n’est modifié par ce document.

## Statuts de vérité

- **[EXISTANT]** : observé dans le code, la documentation ou la base locale.
- **[PARTIEL]** : une partie existe, sans couvrir le processus complet.
- **[PROPOSÉ]** : organisation recommandée, non adoptée.
- **[À VALIDER]** : décision nécessaire de la Direction.
- **[PROJET FUTUR]** : fonctionnalité à concevoir et développer.

---

# 1. Objet et question directrice

Ce document transforme l’audit technique en proposition d’organisation numérique pour le Centre Culturel Congolais « Le Zoo ».

Question directrice :

> **Comment le numérique peut-il aider le CCCZ à mieux travailler, mieux communiquer, mieux coordonner ses services et mieux servir les artistes, partenaires et visiteurs ?**

La réponse proposée repose sur cinq principes :

1. une information saisie une seule fois puis réutilisée avec contrôle ;
2. une responsabilité métier clairement attribuée avant toute validation ;
3. une séparation entre administration technique et décision institutionnelle ;
4. une publication publique uniquement après approbation ;
5. une traçabilité des décisions, changements, consultations sensibles et archivages.

Ce document ne décide pas à la place du CCCZ. Il fournit une base de discussion pour la Direction Générale, les directeurs, les responsables de services et les futurs administrateurs.

---

# 2. État de départ confirmé

## 2.1 Écosystème actuel

[EXISTANT]

- Le site public est principalement HTML/CSS/JavaScript.
- Les pages publiques couvrent l’accueil, les programmes, les actualités, les événements, la galerie, les partenaires, l’à-propos et le contact.
- `contact-submit.php` enregistre les demandes de contact dans `contact_requests`.
- Gestion_Interne-CZOO possède une authentification PHP, des sessions, un RBAC, un tableau de bord et des modules internes.
- Les modules internes sont majoritairement génériques et s’appuient sur `internal_items`.
- Le registre RH possède des tables et scripts spécifiques `agents` / `agent_imports`.
- Les documents possèdent une table et une route de téléchargement protégée.
- Un endpoint interne finances existe en lecture.
- Les tables `users`, `directions`, `roles`, `permissions`, `user_roles`, `role_permissions` et `audit_logs` existent dans le modèle local.

## 2.2 Limites confirmées

[PARTIEL]

- Les événements et actualités publics ne sont pas alimentés par un workflow interne de publication.
- Les statuts existent dans le catalogue PHP mais ne constituent pas encore un moteur de transitions contrôlées.
- Les validations métier, hiérarchiques et éditoriales ne sont pas implémentées comme circuits complets.
- Les statistiques métier fiables ne sont pas encore disponibles.
- Les responsabilités exactes des directions et services ne sont pas toutes documentées.
- Les directions sont présentes dans le schéma local, mais leur organigramme officiel, leurs délégations et leurs suppléances restent à confirmer.
- La relation entre `direction_id` et le filtrage effectif des données doit être vérifiée avant une mise en production.

---

# 3. Points critiques à décider avant développement

| Problème | Risque | Décision à prendre | Proposition | Responsable de la décision |
|---|---|---|---|---|
| Organigramme incomplet | Mauvaise attribution des tâches et droits | Valider directions, services et rattachements | Publier un organigramme de référence versionné | Direction Générale |
| Responsabilités non nominatives | Contenus bloqués ou validés par la mauvaise personne | Désigner propriétaire et suppléant par processus | Une fiche RACI par processus | Direction Générale |
| Délégations absentes | Impossible de savoir qui valide en l’absence du directeur | Définir délégation temporaire et durée | Délégation datée, limitée et auditée | Direction Générale |
| Rôles confondus avec fonctions | Permissions trop larges ou incohérentes | Séparer fonction institutionnelle et rôle système | Un utilisateur peut avoir une fonction et un rôle technique distincts | Direction + Administration |
| Validation non définie | Publication de données incomplètes ou non autorisées | Définir niveaux de validation | Validation métier puis hiérarchique puis éditoriale | Direction Générale |
| Publication non attribuée | Retards ou publication sans contrôle | Désigner un responsable éditorial | L’éditeur publie uniquement un contenu approuvé | Direction Générale |
| Archivage non défini | Perte d’historique ou suppression illégitime | Définir durée et responsable d’archivage | Archivage logique, versionné et non destructif | Administration |
| Données sensibles non classifiées | Exposition de données RH, financières ou contractuelles | Adopter une classification | Public, interne, confidentiel, strictement confidentiel | Direction + Administration |
| Statistiques sans source officielle | Indicateurs contradictoires | Définir source et formule de chaque indicateur | Dictionnaire des indicateurs approuvé | Direction Générale |
| Documents non classés | Recherche impossible et fuite de pièces | Définir catégories, versions et droits | Référentiel documentaire commun | Administration |
| Notifications non gouvernées | Saturation, oubli ou divulgation | Choisir événements déclencheurs et destinataires | Notifications internes prioritaires avant e-mail | Direction + Administration |
| Super Administrateur trop puissant | Confusion entre technique et métier | Limiter ses pouvoirs métier | Administration technique, jamais validation automatique | Direction Générale |
| Migrations et bootstrap concurrents | Base différente selon installation | Choisir la source de vérité du schéma | Migrations versionnées, bootstrap limité | Responsable technique |
| Site public et interne séparés | Double saisie et contenus divergents | Décider le mode de publication | Publication contrôlée d’un contenu validé | Direction + responsable éditorial |
| Données personnelles sans politique | Risque juridique et institutionnel | Définir conservation et accès | Registre des traitements et règles de rétention | Direction + Administration |

---

# 4. Niveaux d’autorité

## 4.1 Niveau 1 — Agent / opérateur

Responsabilités proposées :

- recueillir l’information ;
- saisir une proposition ou un dossier ;
- joindre les pièces nécessaires ;
- corriger une fiche avant soumission ;
- suivre les tâches qui lui sont assignées ;
- produire les éléments de rapport demandés.

Limites :

- ne valide pas son propre contenu ;
- ne publie pas par défaut ;
- ne consulte que les données nécessaires à sa mission ;
- ne modifie pas une décision déjà validée sans nouvelle version.

## 4.2 Niveau 2 — Responsable / directeur

Responsabilités proposées :

- contrôler la qualité métier ;
- arbitrer au niveau du service ou de la direction ;
- valider les informations relevant de son périmètre ;
- demander une correction ;
- suivre les échéances et rapports ;
- proposer la publication d’un contenu approuvé métier.

Limites :

- ne modifie pas les permissions techniques hors délégation ;
- ne publie pas nécessairement directement ;
- ne valide pas les domaines d’une autre direction sans mandat.

## 4.3 Niveau 3 — Direction Générale

Responsabilités proposées :

- fixer les orientations ;
- arbitrer les sujets transversaux ou sensibles ;
- valider les publications institutionnelles ;
- approuver les conventions et engagements selon délégation ;
- consulter les indicateurs consolidés ;
- décider des exceptions et délégations.

La Direction Générale ne doit pas être sollicitée pour chaque opération courante. Les seuils d’escalade doivent être définis.

## 4.4 Responsabilité métier versus administration technique

| Domaine | Responsabilité métier | Administration technique |
|---|---|---|
| Événement | Service culturel / direction porteuse | Configuration du module et comptes |
| Finance | Direction Financière | Comptes, permissions, maintenance |
| Personnel | RH / Administration | Sécurité des accès et sauvegardes |
| Publication | Responsable éditorial + autorité validante | Infrastructure et paramètres |
| Rôles | Direction définit le besoin | Administrateur applique la configuration autorisée |
| Audit | Direction consulte et contrôle | Administrateur protège le journal sans le réécrire |

---

# 5. Matrice institutionnelle « Qui fait quoi ? »

Les termes « responsable », « validation » et « publication » ci-dessous sont des propositions. Toute ligne non explicitement confirmée porte la mention **[À VALIDER PAR LA DIRECTION]**.

| Processus | Proposition | Saisie | Vérification | Validation métier | Validation hiérarchique | Approbation publication | Publication | Suivi | Rapport | Archivage |
|---|---|---|---|---|---|---|---|---|---|---|
| Événement | Service culturel / porteur [À VALIDER] | Agent culturel | Responsable Culture & Arts | Direction culturelle [À VALIDER] | Directeur concerné | Responsable éditorial + autorité | Éditeur | Culture, espaces, finances | Porteur | Administration / producteur |
| Formation | Service culturel ou pédagogique [À VALIDER] | Responsable formation | Vérificateur pédagogique | Responsable de programme | Directeur concerné | Éditeur | Éditeur | Inscriptions et présences | Responsable formation | Administration |
| Activité | Service porteur | Agent habilité | Responsable de service | Direction porteuse | Directeur | Éditeur après accord | Éditeur | Service porteur | Service porteur | Producteur |
| Visite | École, partenaire ou accueil | Accueil / animation | Disponibilité des espaces | Responsable visites [À VALIDER] | Direction concernée | Non par défaut | Non par défaut | Accueil | Accueil | Administration |
| Actualité | Service porteur | Rédacteur / éditeur | Responsable du sujet | Direction concernée | Selon sensibilité | Responsable éditorial | Éditeur | Communication | Éditorial | Édition |
| Spectacle | Culture & Arts | Agent culturel | Technique, espace, budget | Responsable culturel | Directeur | Éditeur + autorité | Éditeur | Culture / finances | Culture | Culture |
| Exposition | Culture & Arts / artiste | Curateur ou agent culturel | Œuvres, droits, espace | Responsable culturel | Directeur | Éditeur + autorité | Éditeur | Fréquentation et conservation | Curateur | Patrimoine |
| Conférence | Service porteur / partenaire | Agent désigné | Intervenants, dates, convention | Responsable porteur | Directeur | Éditeur | Éditeur | Partenariat | Porteur | Documents |
| Atelier | Médiation / Culture | Médiateur | Public, sécurité, moyens | Responsable médiation | Directeur | Éditeur | Éditeur | Présences et résultats | Médiation | Culture |
| Artiste | Culture & Arts | Agent culturel | Identité, biographie, droits | Responsable culturel | Directeur si publication sensible | Éditeur + artiste | Éditeur | Participations | Culture | Culture / documents |
| Partenaire | Direction porteuse | Agent relations | Coordonnées, convention | Responsable partenariat | Autorité signataire | Éditeur si communication | Éditeur | Échéances et livrables | Partenariat | Administration |
| Projet | Direction porteuse | Chef de projet | Jalons, budget, risques | Directeur porteur | Direction Générale selon seuil | À décider par projet | Éditeur si public | Chef de projet | Chef de projet | Administration |
| Document | Service producteur | Agent habilité | Classement, confidentialité | Responsable producteur | Direction selon sensibilité | Non par défaut | Selon type | Producteur | Producteur | Archives |
| Galerie | Culture / édition | Éditeur | Légendes, droits, qualité | Responsable éditorial | Direction si exposition sensible | Autorité de publication | Éditeur | Édition | Édition | Médiathèque |
| Patrimoine | Préservation culturelle | Agent désigné | Provenance, état, localisation | Responsable patrimoine | Direction concernée | Selon contenu | Éditeur si public | Patrimoine | Patrimoine | Archives |
| Rapport | Service ou direction | Rapporteur | Cohérence des données | Responsable de service | Directeur | Non par défaut | Interne | Direction | Auteur | Administration |
| Publication institutionnelle | Direction porteuse | Communication / éditeur | Faits, position officielle | Direction concernée | Direction Générale | Direction Générale | Éditeur | Communication | Archives |

---

# 6. Circuits de validation par contenu

## 6.1 Actualité

```text
Information reçue
 -> rédaction
 -> contrôle des faits, dates, noms et droits d’image
 -> validation du service responsable
 -> validation hiérarchique si sujet sensible
 -> approbation éditoriale
 -> publication
 -> suivi des corrections et retrait
 -> archivage
```

- Propose : service porteur.
- Saisit : rédacteur ou éditeur.
- Contrôle : responsable du sujet.
- Valide : direction concernée.
- Autorise : responsable éditorial, avec escalade à la Direction Générale si institutionnel.
- Publie : éditeur habilité.
- Archive : équipe éditoriale avec version publiée.

**[À VALIDER] :** responsable éditorial officiel et seuil d’escalade.

## 6.2 Événement

```text
Idée
 -> fiche événement
 -> contrôle dates / lieu / public / moyens
 -> validation culturelle
 -> validation hiérarchique
 -> autorisation de communication
 -> publication
 -> suivi fréquentation / coûts / partenaires
 -> rapport
 -> archivage
```

Le système actuel ne réalise pas cette chaîne complète [PARTIEL].

## 6.3 Spectacle

Contrôles supplémentaires proposés : disponibilité de la salle, besoins techniques, contrats, droits, sécurité, budget, communication et bilan de fréquentation.

**Validation métier proposée :** Culture & Arts.  
**Validation technique :** service responsable de l’espace et de l’intendance [À VALIDER].  
**Validation financière :** Direction Financière selon seuil [À VALIDER].

## 6.4 Exposition

Contrôles : œuvres, provenance, assurance éventuelle, droits d’image, scénographie, espace, conservation et médiation.

**Validation métier proposée :** Culture & Arts / patrimoine.  
**Autorisation publique :** direction compétente et responsable éditorial.  
**Archivage :** dossier exposition, œuvres, légendes, photos et bilan.

## 6.5 Conférence

Contrôles : intervenant, sujet, partenariat, calendrier, public, droits de captation et convention.

**Proposition :** la direction porteuse valide le contenu ; la Direction Générale intervient si l’engagement institutionnel le justifie.

## 6.6 Atelier

Contrôles : objectifs pédagogiques, public, capacité, intervenant, sécurité, matériel et présence.

**Validation :** responsable de médiation puis direction concernée [À VALIDER].

## 6.7 Formation

Contrôles : programme, formateur, calendrier, capacité, inscription, présence, évaluation et certificat éventuel.

Le certificat, son modèle et sa valeur institutionnelle sont **[À VALIDER]**.

## 6.8 Visite

```text
Demande
 -> enregistrement du groupe
 -> contrôle capacité / date / espace
 -> confirmation
 -> accueil
 -> présence réelle
 -> statistiques
 -> rapport périodique
 -> archivage
```

Le module de visites n’existe pas actuellement [PROJET FUTUR].

## 6.9 Artiste

Contrôles : identité professionnelle, biographie, discipline, accord de publication, médias, rémunération éventuelle et lien avec activité.

L’artiste ne doit pas être rendu public sans contrôle des droits et accord approprié [À VALIDER].

## 6.10 Partenaire

```text
Prospection ou demande
 -> fiche partenaire
 -> vérification institutionnelle
 -> projet de convention
 -> validation administrative / juridique éventuelle
 -> signature par l’autorité habilitée
 -> activité liée
 -> suivi des obligations
 -> bilan
 -> archivage
```

L’autorité signataire et le rôle de l’Administration doivent être confirmés.

## 6.11 Projet

Contrôles : objectif, responsable, jalons, budget, risques, partenaires, livrables, indicateurs et clôture.

La validation hiérarchique dépend du seuil financier, institutionnel ou partenarial [À VALIDER].

## 6.12 Document

```text
Création
 -> dépôt
 -> classement
 -> contrôle de confidentialité
 -> validation du producteur
 -> consultation autorisée
 -> versionnement
 -> archivage
```

La publication publique d’un document doit être une décision distincte de sa simple validation interne.

## 6.13 Galerie

Contrôles : droits, légendes, crédit photo, consentement, qualité et lien avec l’événement ou le patrimoine.

## 6.14 Patrimoine

Contrôles : provenance, état, localisation, valeur documentaire, droits et conditions de consultation.

La conservation patrimoniale nécessite une politique spécifique [À VALIDER].

## 6.15 Rapport

Un rapport est d’abord un produit de suivi interne. Sa publication publique doit être exceptionnelle et explicitement approuvée.

## 6.16 Publication institutionnelle

Toute prise de position institutionnelle, annonce officielle ou publication engageant le Centre doit suivre :

```text
Proposition de la direction porteuse
 -> rédaction
 -> vérification factuelle
 -> validation de la direction
 -> approbation Direction Générale
 -> publication par éditeur
 -> archivage de la version publiée
```

---

# 7. Futur Super Administrateur

## 7.1 Ce qu’il devrait gérer

[PROPOSÉ / PROJET FUTUR]

- comptes utilisateurs et désactivation ;
- directions et services, après validation institutionnelle ;
- rôles et permissions ;
- catégories et tags contrôlés ;
- statuts et transitions techniques ;
- types d’activités et d’événements ;
- espaces et paramètres de disponibilité ;
- modèles de formulaires et documents ;
- règles de notifications ;
- paramètres d’intégration ;
- journaux, consultation et rétention ;
- sauvegardes et restauration avec procédure approuvée ;
- configuration technique non métier ;
- contrôle de santé du système.

## 7.2 Ce qu’il ne doit pas faire automatiquement

- valider un contenu métier sans délégation explicite ;
- publier un contenu sans autorisation ;
- modifier ou effacer l’historique d’audit ;
- supprimer arbitrairement des données ;
- consulter des données RH ou financières sans justification et traçabilité ;
- modifier une convention au nom d’une direction ;
- remplacer le directeur responsable ;
- attribuer une responsabilité institutionnelle non décidée ;
- contourner une séparation de validation ;
- utiliser l’accès technique comme preuve d’autorité métier.

## 7.3 Garde-fous proposés

- double validation pour les changements sensibles ;
- journal obligatoire des changements de rôle et permission ;
- impossibilité de supprimer physiquement certains journaux ;
- accès aux données sensibles justifié et audité ;
- comptes personnels, jamais de compte partagé ;
- procédure de révocation et de remplacement ;
- revue périodique des privilèges.

---

# 8. Registre des projets numériques futurs

| Projet | Objectif | Utilisateurs | Direction | Priorité | Bénéfice | Dépendances | Complexité | Décision nécessaire |
|---|---|---|---|---|---|---|---|---|
| Gouvernance des workflows | Formaliser états, transitions et validations | Agents, responsables, DG | Toutes | Critique | Responsabilités claires | Organigramme, délégations | Élevée | Valider le circuit institutionnel |
| Calendrier institutionnel | Centraliser activités et échéances | Tous | Culture / Administration | Haute | Coordination et visibilité | Événements structurés | Moyenne | Choisir le calendrier de référence |
| Réservation des espaces | Éviter conflits et améliorer l’usage | Agents, partenaires | Intendance / Culture | Haute | Meilleure planification | Espaces, calendrier | Élevée | Définir priorités de réservation |
| Gestion des visiteurs | Suivre demandes, groupes et fréquentation | Accueil, écoles | À valider | Moyenne | Meilleur accueil | Formulaire, espaces | Moyenne | Désigner service responsable |
| Gestion des formations | Gérer inscriptions, présences, évaluations | Formateurs, agents | Culture | Moyenne | Suivi pédagogique | Modèle de formation | Élevée | Décider valeur des certificats |
| Gestion des artistes | Fiches et participations | Artistes, culture, éditeurs | Culture & Arts | Haute | Mémoire et valorisation | Droits et consentements | Moyenne | Valider données publiables |
| Gestion des partenaires | Suivre conventions et livrables | Directions, partenaires | À valider | Haute | Continuité relationnelle | Modèle convention | Moyenne | Désigner responsable partenariat |
| Médiathèque | Organiser photos, vidéos et crédits | Culture, édition | Culture / édition | Moyenne | Réutilisation maîtrisée | Droits médias | Moyenne | Politique de droits |
| Archives numériques | Classer et conserver les documents | Tous services | Administration | Haute | Mémoire institutionnelle | Politique rétention | Élevée | Durées et niveaux de confidentialité |
| Patrimoine numérique | Inventorier objets et ressources | Patrimoine, bibliothèque | Préservation culturelle | Haute | Protection de la mémoire | Provenance, inventaire | Élevée | Définir périmètre patrimonial |
| Équipements et maintenance | Suivre matériel, affectation et pannes | Intendance | Intendance | Moyenne | Réduction des pertes | Inventaire physique | Moyenne | Nommer gestionnaire équipement |
| Demandes internes | Centraliser demandes entre services | Agents, responsables | Administration | Haute | Réduction des échanges perdus | Workflow, notifications | Moyenne | Définir types de demandes |
| Billetterie | Gérer réservations, ventes et fréquentation | Public, finances | À valider | À étudier | Données de fréquentation | Modèle financier, paiement | Élevée | Décider modèle économique |
| Newsletter | Diffuser contenus validés | Public, édition | Communication | À étudier | Fidélisation du public | Consentement, outil d’envoi | Moyenne | Valider politique de contact |
| Notifications | Alerter sur tâches et validations | Tous | Toutes | Haute | Moins de retards | Workflow, préférences | Moyenne | Choisir canaux et priorités |
| Statistiques | Consolider indicateurs fiables | Directeurs, DG | Toutes | Haute | Pilotage fondé sur données | Dictionnaire indicateurs | Élevée | Définir formules officielles |
| Tableau DG | Vue consolidée des activités et risques | Direction Générale | Toutes | Haute | Décision rapide | Données validées | Élevée | Valider indicateurs autorisés |
| Recherche | Retrouver contenus, documents et partenaires | Agents, publics | Toutes | Moyenne | Gain de temps | Métadonnées | Moyenne | Définir périmètre public/interne |
| API interne | Échanger entre modules de manière contrôlée | Applications internes | Technique | Moyenne | Évolutivité | Contrats, auth | Élevée | Choisir architecture |
| Intégration réseaux sociaux | Relayer les publications approuvées | Communication, public | Communication | À étudier | Portée des campagnes | Workflow publication | Moyenne | Décider comptes et règles |
| Intégration YouTube | Relier vidéos et contenus | Public, édition | Culture / communication | À étudier | Valorisation audiovisuelle | Métadonnées et droits | Faible à moyenne | Valider gouvernance chaîne |
| Sauvegarde/restauration | Restaurer données et documents | Administrateur, DG | Technique | Critique | Résilience | Stockage, procédures | Moyenne | RPO/RTO et fréquence |
| Registre des décisions | Tracer arbitrages et validations | Directeurs, DG | Administration | Critique | Mémoire de gouvernance | Workflow, audit | Moyenne | Définir portée juridique |
| Automatisation | Réduire saisies répétitives | Agents, admin | Toutes | Moyenne | Productivité | Données structurées | Moyenne | Identifier tâches automatisables |
| IA assistée | Aider classement, synthèse et brouillon | Agents, éditeurs | Toutes | À étudier | Gain de temps contrôlé | Politique IA, confidentialité | Élevée | Autoriser ou non les usages |
| Registre des droits médias | Suivre autorisations photos/vidéos | Culture, édition | Culture / communication | Haute | Réduction du risque juridique | Médiathèque | Moyenne | Définir preuve de consentement |
| Registre des traitements | Documenter données personnelles | Administration | Direction / Administration | Haute | Conformité et confiance | Inventaire données | Moyenne | Désigner responsable |
| Continuité d’activité | Prévoir panne, perte ou indisponibilité | Directions | DG / technique | Haute | Résilience opérationnelle | Sauvegardes, procédures | Moyenne | Valider scénarios de crise |

---

# 9. Cycle de vie général d’une information

```text
IDÉE / DEMANDE
      ↓
PROPOSITION
      ↓
SAISIE
      ↓
VÉRIFICATION
      ↓
VALIDATION MÉTIER
      ↓
VALIDATION HIÉRARCHIQUE
      ↓
APPROBATION
      ↓
PUBLICATION éventuelle
      ↓
SUIVI
      ↓
RAPPORT
      ↓
ARCHIVAGE
```

## 9.1 Règles communes proposées

- Une information possède un propriétaire métier.
- Une information possède un statut explicite.
- Toute correction après validation crée une nouvelle version ou relance la validation.
- Une information sensible ne devient jamais publique par simple changement d’état technique.
- La publication et l’archivage sont des actions distinctes.
- Le rapport clôture l’activité mais ne remplace pas les pièces justificatives.
- L’audit conserve qui a fait quoi et quand.

## 9.2 Variantes

- **Actualité :** le suivi porte surtout sur visibilité, corrections et retrait.
- **Événement :** le suivi porte sur fréquentation, moyens, budget, partenaires et bilan.
- **Formation :** le suivi porte sur inscriptions, présences, résultats et certificats.
- **Visite :** le suivi porte sur groupe accueilli, espaces visités et satisfaction.
- **Partenaire :** le suivi porte sur convention, obligations, échéances et livrables.
- **Document :** le suivi porte sur version, confidentialité, consultation et rétention.
- **Patrimoine :** le suivi porte sur état, localisation, provenance et conservation.
- **Rapport :** le suivi porte sur approbation, diffusion et décisions prises.

---

# 10. Nomenclature officielle provisoire

Cette nomenclature est **[PROPOSÉE]** et doit être approuvée avant son implémentation.

## 10.1 Catégories

- Actualité ;
- Événement ;
- Activité ;
- Projet ;
- Partenaire ;
- Artiste ;
- Document ;
- Rapport ;
- Patrimoine ;
- Bibliothèque ;
- Publication institutionnelle.

## 10.2 Tags

Tags contrôlés possibles :

- culture ;
- art ;
- patrimoine ;
- jeunesse ;
- école ;
- médiation ;
- résidence ;
- création ;
- transmission ;
- Kinshasa ;
- partenaire ;
- institution ;
- appel à projets ;
- accès libre ;
- inscription ;
- audiovisuel.

## 10.3 Statuts

Cycle transversal proposé :

- brouillon ;
- soumis ;
- à corriger ;
- vérifié ;
- validé métier ;
- approuvé ;
- publié ;
- suspendu ;
- archivé ;
- rejeté.

## 10.4 Types d’activités

- spectacle ;
- exposition ;
- conférence ;
- atelier ;
- formation ;
- visite ;
- rencontre ;
- projection ;
- résidence ;
- manifestation institutionnelle.

## 10.5 Types d’événements

- programmation culturelle ;
- événement partenaire ;
- événement institutionnel ;
- événement éducatif ;
- événement patrimonial ;
- événement de communication.

## 10.6 Publics

- grand public ;
- enfants ;
- jeunes ;
- écoles ;
- étudiants ;
- artistes ;
- professionnels ;
- partenaires ;
- médias ;
- agents du CCCZ.

## 10.7 Espaces

- salle de spectacle ;
- bibliothèque ;
- galerie ;
- village des artistes ;
- jardin / espace extérieur ;
- espace culturel ;
- espace patrimoine / documentation.

## 10.8 Disciplines

- théâtre ;
- danse ;
- musique ;
- arts visuels ;
- cinéma ;
- littérature ;
- patrimoine ;
- médiation culturelle ;
- pratiques transdisciplinaires.

## 10.9 Valeurs contrôlées par le Super Administrateur

- catégories ;
- tags ;
- statuts ;
- types ;
- publics ;
- espaces ;
- disciplines ;
- directions responsables.

Le Super Administrateur ne doit pas créer seul une catégorie ayant une conséquence institutionnelle sans validation de la Direction.

---

# 11. Niveaux de confidentialité

Cette classification est **[PROPOSÉE]**.

| Niveau | Exemples | Accès proposé |
|---|---|---|
| Public | Contenus publiés, événements approuvés, informations institutionnelles | Site public |
| Interne | Brouillons, rapports internes, planning non publié | Agents habilités |
| Confidentiel | Conventions en négociation, données partenaires, budgets prévisionnels | Service et direction concernés |
| Strictement confidentiel | Données RH personnelles, mots de passe, journaux sensibles, pièces financières nominatives | Autorités désignées et accès tracé |

Règles proposées :

- une donnée ne descend pas vers « public » automatiquement ;
- la consultation des niveaux confidentiels est auditée ;
- les exports sont soumis au même niveau de confidentialité ;
- les documents associés héritent d’un niveau par défaut mais peuvent être renforcés ;
- les durées de conservation sont décidées par catégorie.

---

# 12. Tableau de bord de la Direction Générale

## 12.1 Indicateurs disponibles

[EXISTANT / LIMITÉ]

- demandes de contact comptées ;
- éléments récents de certains modules génériques ;
- permissions de la session ;
- activité de connexion et d’audit selon les actions enregistrées.

## 12.2 Indicateurs à structurer

[PROJET FUTUR]

- nombre d’activités validées ;
- nombre d’événements publiés ;
- projets par statut ;
- conventions actives et échéances ;
- documents en attente ;
- formations programmées ;
- visites confirmées ;
- espaces réservés ;
- rapports reçus et en retard.

## 12.3 Indicateurs à développer

- fréquentation réelle ;
- visiteurs par public et période ;
- participants aux formations ;
- artistes accueillis ;
- partenaires actifs ;
- recettes et dépenses validées ;
- coût par activité ;
- évolution mensuelle et annuelle ;
- risques et décisions ouvertes ;
- délais moyens de validation et publication ;
- taux de dossiers incomplets ;
- état du patrimoine et des équipements.

La Direction Générale ne doit voir que des indicateurs définis, sourcés, datés et accompagnés d’un niveau de confiance.

---

# 13. Expérience des publics

| Public | Besoin | Service numérique | Information | Action possible |
|---|---|---|---|---|
| Agents | Savoir quoi faire et où saisir | Gestion interne | Tâches, formulaires, échéances | Saisir, corriger, soumettre |
| Cadres | Contrôler l’activité du service | Tableau de direction | Dossiers, alertes, indicateurs | Vérifier, demander correction |
| Directeurs | Arbitrer et valider | Espace de direction | Dossiers validés métier, risques | Valider, refuser, déléguer |
| Direction Générale | Piloter et décider | Tableau DG | Indicateurs consolidés, décisions | Arbitrer, approuver, orienter |
| Artistes | Comprendre les opportunités | Site public / futur espace | Programmes, appels, contacts | Proposer, participer, consentir |
| Partenaires | Collaborer avec le CCCZ | Site public / futur suivi partenaire | Vision, conventions, activités | Contacter, proposer, suivre |
| Visiteurs | Préparer une venue | Site public | Dates, lieux, accès, programmes | Consulter, contacter, réserver si décidé |
| Écoles | Organiser une visite ou activité | Site public / futur module visites | Ateliers, visites, conditions | Demander, inscrire, confirmer |
| Médias | Obtenir une information fiable | Site public / espace presse futur | Actualités, visuels, contacts | Consulter, demander, relayer |
| Public culturel | Découvrir et participer | Site public | Programmation, artistes, galerie | Explorer, venir, partager |

---

# 14. Les 10 décisions que la Direction devra prendre en premier

1. Valider l’organigramme numérique officiel : directions, services et rattachements.
2. Désigner les responsables et suppléants de chaque processus.
3. Valider les huit rôles, leurs périmètres et la séparation technique/métier.
4. Décider qui peut valider, approuver et publier chaque type de contenu.
5. Définir les seuils d’intervention de la Direction Générale.
6. Adopter le cycle de vie commun des informations.
7. Adopter les catégories, statuts, types et tags contrôlés.
8. Classer les données par niveau de confidentialité et fixer les règles de conservation.
9. Définir les indicateurs officiels et leurs sources.
10. Décider si la Gestion interne doit devenir un système de production relié au site public.

---

# 15. Les 10 projets numériques à prioriser

1. Gouvernance des workflows et délégations.
2. Modèle métier des événements et activités.
3. Calendrier institutionnel.
4. Gestion et réservation des espaces.
5. Gestion des partenaires et conventions.
6. Gestion documentaire et archives.
7. Tableau de bord de la Direction Générale.
8. Notifications internes et échéances.
9. Registre des décisions et audit renforcé.
10. Sauvegarde, restauration et continuité d’activité.

Ces priorités doivent être réordonnées après les décisions institutionnelles.

---

# 16. Les 10 bénéfices concrets

1. Les agents savent où saisir une information et à qui la transmettre.
2. Les responsables voient les dossiers incomplets et les échéances.
3. Les directeurs valident dans leur périmètre sans confusion de responsabilités.
4. La Direction Générale dispose d’une vue consolidée et documentée.
5. Les artistes trouvent plus facilement les programmes, appels et contacts.
6. Les partenaires suivent mieux conventions, activités et obligations.
7. Les visiteurs accèdent à une information publique cohérente et actualisée.
8. Les écoles peuvent préparer visites et activités plus simplement.
9. Les documents, décisions et rapports sont retrouvables dans le temps.
10. Le CCCZ réduit les doubles saisies, les pertes d’information et les publications non autorisées.

---

# 17. Recommandation finale de gouvernance

Le numérique doit être conçu comme une mémoire organisée et un outil de coordination du CCCZ, pas comme un simple ensemble de formulaires.

La priorité est de créer une chaîne de responsabilité lisible :

```text
Une idée est proposée
 -> un service la porte
 -> un agent la saisit
 -> un responsable la vérifie
 -> une autorité la valide
 -> une autorité habilitée autorise la publication
 -> un éditeur publie
 -> le service suit les résultats
 -> un rapport est produit
 -> les pièces sont archivées
```

Le Super Administrateur garantit que cette chaîne fonctionne techniquement. Il ne doit pas décider à la place des directions.

La Direction Générale conserve l’arbitrage institutionnel. Les directeurs conservent la responsabilité métier. Les agents disposent d’outils clairs pour exécuter et documenter leur travail. Les artistes, partenaires, visiteurs, écoles et médias bénéficient d’une information plus fiable et de parcours mieux coordonnés.

**Conclusion :** avant tout développement métier important, le CCCZ devrait valider ce modèle, le compléter avec son organigramme officiel et adopter une première matrice RACI. Cette validation humaine sera la condition de réussite de la future Gestion_Interne-CZOO et de sa relation avec le site public.
