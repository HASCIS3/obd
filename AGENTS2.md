# AGENTS2.md - Prompt Flutter OBD Mobile

Ce document contient le prompt complet pour mettre à jour l'application mobile Flutter OBD en synchronisation avec le backend Laravel.

---

## 📱 Contexte du Projet

Tu travailles sur l'application mobile Flutter **OBD Mobile** (`D:\projects\flutter\obd_mobile\obd_mobile_app`) qui est le compagnon mobile de l'application web Laravel **OBD** (Olympiaque Bamako Dojo) - un système de gestion de centre sportif au Mali.

L'API backend est disponible à `http://127.0.0.1:8000/api` (dev) avec authentification Sanctum.

---

## 🏗️ Architecture Backend (Laravel)

### Modèles de données principaux

```
├── User (id, name, email, role: admin|coach|athlete|parent, athlete_id?)
├── Athlete (id, nom, prenom, date_naissance, sexe, telephone, email, adresse, photo, statut, date_inscription)
├── Coach (id, user_id, nom, prenom, telephone, email, specialite, photo, statut)
├── Discipline (id, nom, description, icone, couleur, tarif_mensuel, statut)
├── Presence (id, athlete_id, discipline_id, date, present, retard, remarque)
├── Paiement (id, athlete_id, mois, annee, montant, montant_paye, date_paiement, mode_paiement, statut, type_paiement)
├── Performance (id, athlete_id, discipline_id, date_evaluation, contexte, note_globale, resultat_match, medaille, adversaire, score_match)
├── Rencontre/Match (id, discipline_id, date_match, heure_match, type_match, adversaire, lieu, score_obd, score_adversaire, resultat, type_competition, saison, phase)
├── CombatTaekwondo (id, rencontre_id, athlete_rouge_id, athlete_bleu_id, nom_rouge, nom_bleu, rounds:JSON, score_rouge, score_bleu, statut, vainqueur, type_victoire, categorie_poids, categorie_age)
├── Activity (id, titre, description, type, debut, fin, lieu, image, statut)
├── Licence (id, athlete_id, numero, type, date_emission, date_expiration, statut)
├── CertificatMedical (id, athlete_id, type, date_emission, date_expiration, medecin, fichier)
├── SuiviScolaire (id, athlete_id, annee_scolaire, trimestre, etablissement, classe, moyenne_generale, comportement, remarques)
├── StageFormation (id, titre, description, date_debut, date_fin, lieu, tarif, places, discipline_id)
├── Facture (id, athlete_id, numero, montant_total, montant_paye, date_emission, date_echeance, statut)
├── Saison (id, nom, date_debut, date_fin, active)
└── ParentModel (id, user_id, nom, prenom, telephone, email, relation)
```

### API Endpoints existants

```
POST   /api/login                    - Connexion (email, password)
POST   /api/logout                   - Déconnexion
GET    /api/user                     - Utilisateur connecté
GET    /api/dashboard                - Stats dashboard

GET    /api/athletes                 - Liste athlètes
POST   /api/athletes                 - Créer athlète
GET    /api/athletes/{id}            - Détail athlète
PUT    /api/athletes/{id}            - Modifier athlète
DELETE /api/athletes/{id}            - Supprimer athlète
GET    /api/athletes/{id}/presences  - Présences d'un athlète
GET    /api/athletes/{id}/paiements  - Paiements d'un athlète
GET    /api/athletes/{id}/performances - Performances d'un athlète

GET    /api/disciplines              - Liste disciplines
GET    /api/disciplines/{id}         - Détail discipline

GET    /api/paiements                - Liste paiements
POST   /api/paiements                - Créer paiement
GET    /api/paiements/arrieres       - Arriérés
GET    /api/paiements/{id}/recu      - Reçu PDF

GET    /api/presences                - Liste présences
POST   /api/presences                - Créer présence
GET    /api/presences/rapport-mensuel - Rapport mensuel

GET    /api/performances             - Liste performances
POST   /api/performances             - Créer performance
GET    /api/performances/dashboard   - Stats performances
GET    /api/performances/evolution/{athlete} - Évolution athlète
```

### Nouveaux endpoints à ajouter (pour synchroniser avec le web)

```
# Rencontres/Matchs
GET    /api/rencontres               - Liste des matchs
POST   /api/rencontres               - Créer match
GET    /api/rencontres/{id}          - Détail match
PUT    /api/rencontres/{id}          - Modifier match
GET    /api/rencontres/a-venir       - Matchs à venir
GET    /api/rencontres/resultats     - Derniers résultats

# Combats Taekwondo
GET    /api/rencontres/{id}/combats  - Combats d'une rencontre
POST   /api/rencontres/{id}/combats  - Créer combat
GET    /api/combats/{id}             - Détail combat
PUT    /api/combats/{id}/scores      - Mettre à jour scores
PUT    /api/combats/{id}/terminer    - Terminer combat

# Activités
GET    /api/activities               - Liste activités
GET    /api/activities/a-venir       - Activités à venir
GET    /api/activities/{id}          - Détail activité

# Calendrier
GET    /api/calendrier/events        - Tous les événements (matchs + activités)
GET    /api/calendrier/a-venir       - Événements à venir

# Licences
GET    /api/athletes/{id}/licence    - Licence d'un athlète
GET    /api/licences/expirant        - Licences expirant bientôt

# Certificats médicaux
GET    /api/athletes/{id}/certificat - Certificat d'un athlète
GET    /api/certificats/expirant     - Certificats expirant

# Suivi scolaire
GET    /api/athletes/{id}/suivi-scolaire - Suivi scolaire
POST   /api/suivi-scolaire           - Ajouter suivi

# Stages
GET    /api/stages                   - Liste stages
GET    /api/stages/{id}              - Détail stage
POST   /api/stages/{id}/inscription  - Inscrire athlète
```

---

## 📱 Écrans Flutter à implémenter/mettre à jour

### 1. Dashboard (mise à jour)
- **Stats principales** : Athlètes actifs, Coachs, Disciplines, Arriérés
- **Rencontres sportives** : Stats (V/D/N), Prochains matchs, Derniers résultats
- **Activités** : Stats, Prochaines activités
- **Combats Taekwondo** : Widget si combats existent (Hong/Chung stats)
- **Performances** : Matchs, Médailles, Note moyenne
- **Accès rapides** : Boutons vers modules principaux

### 2. Module Rencontres/Matchs (NOUVEAU)
- **Liste des matchs** avec filtres (discipline, résultat, date)
- **Détail match** avec score, participants, stats
- **Calendrier des matchs** à venir
- **Formulaire création/édition** match

### 3. Module Combats Taekwondo (NOUVEAU)
- **Liste combats** par rencontre
- **Interface de scoring** (style feuille de match) :
  - Scoreboard : Rouge (Hong) vs Bleu (Chung)
  - Chronomètre 2 minutes par round
  - Sélection round (1, 2, 3, Golden)
  - Actions de scoring :
    - Poing tronc (1 pt) - Jirugi
    - Pied tronc (2 pts) - Momtong Chagi
    - Pied rotatif tronc (4 pts) - Dwi Chagi
    - Pied tête (3 pts) - Olgul Chagi
    - Pied rotatif tête (5 pts) - Dwi Huryeo Chagi
    - Kyong-go (avertissement, 2 = 1 pt adversaire)
    - Gam-jeom (pénalité, 1 = 1 pt adversaire)
  - Boutons +/- pour chaque action
  - Calcul automatique des scores
  - Alerte victoire si écart ≥ 20 points
  - Sauvegarde et validation du résultat

### 4. Module Activités (NOUVEAU)
- **Liste activités** avec types (stage, competition, evenement, reunion, autre)
- **Détail activité** avec images, lieu, dates
- **Calendrier activités**

### 5. Module Calendrier (mise à jour)
- **Vue calendrier** avec tous les événements :
  - Matchs (icône sport)
  - Activités (icône type)
  - Entraînements
- **Liste "À venir"** combinée

### 6. Module Licences (NOUVEAU)
- **Liste licences** avec statut (valide, expirant, expirée)
- **Alertes** licences expirant dans 30 jours
- **Détail licence** avec QR code

### 7. Module Certificats Médicaux (NOUVEAU)
- **Liste certificats** avec statut
- **Alertes** certificats expirant
- **Upload** nouveau certificat

### 8. Module Suivi Scolaire (NOUVEAU)
- **Liste suivis** par athlète
- **Formulaire** ajout/édition
- **Graphique** évolution moyennes

### 9. Module Stages Formation (NOUVEAU)
- **Liste stages** disponibles
- **Détail stage** avec places restantes
- **Inscription** athlète

### 10. Portail Athlète (mise à jour)
- Mon profil, mes présences, mes paiements
- Mes performances, mon calendrier
- Ma licence, mon certificat médical
- Mon suivi scolaire

### 11. Portail Parent (mise à jour)
- Voir enfants (athlètes liés)
- Suivre présences, paiements, performances
- Voir bulletins scolaires
- Notifications

---

## 🎨 Design System

### Palette Mali
```dart
const Color primaryGreen = Color(0xFF14532D);   // Vert Mali
const Color secondaryYellow = Color(0xFFFCD116); // Jaune Mali
const Color accentRed = Color(0xFFCE1126);       // Rouge Mali
const Color backgroundLight = Color(0xFFF9FAFB);
const Color textDark = Color(0xFF111827);
```

### Composants UI
- Cards avec ombres douces et coins arrondis (12-16px)
- Boutons avec icônes et états hover/pressed
- Badges colorés pour statuts
- Graphiques avec fl_chart
- Bottom navigation avec 5 onglets max
- Pull-to-refresh sur les listes
- Skeleton loading

---

## 🔧 Structure Flutter recommandée

```
lib/
├── main.dart
├── app/
│   ├── routes.dart
│   └── theme.dart
├── core/
│   ├── api/
│   │   ├── api_client.dart
│   │   └── endpoints.dart
│   ├── models/
│   │   ├── athlete.dart
│   │   ├── rencontre.dart
│   │   ├── combat_taekwondo.dart
│   │   ├── activity.dart
│   │   └── ...
│   └── services/
│       ├── auth_service.dart
│       ├── athlete_service.dart
│       └── ...
├── features/
│   ├── auth/
│   ├── dashboard/
│   ├── athletes/
│   ├── rencontres/
│   ├── combats_taekwondo/
│   ├── activities/
│   ├── calendrier/
│   ├── presences/
│   ├── paiements/
│   ├── performances/
│   ├── licences/
│   ├── certificats/
│   ├── suivi_scolaire/
│   ├── stages/
│   └── portails/
└── shared/
    ├── widgets/
    └── utils/
```

---

## 📋 Priorités d'implémentation

1. **Phase 1 - Core** : Dashboard mis à jour, Rencontres, Activités
2. **Phase 2 - Taekwondo** : Interface scoring combats (priorité haute)
3. **Phase 3 - Documents** : Licences, Certificats médicaux
4. **Phase 4 - Éducation** : Suivi scolaire, Stages
5. **Phase 5 - Portails** : Athlète et Parent améliorés

---

## ⚠️ Points importants

- Authentification via Laravel Sanctum (Bearer token)
- Gestion offline avec cache local (Hive/SQLite)
- Notifications push pour alertes (licences, paiements, matchs)
- Upload images avec compression
- Mode sombre optionnel
- Internationalisation (FR par défaut)
- Tests unitaires et widgets

---

## 🥋 Détails Combat Taekwondo (Kyorugi)

### Structure JSON des rounds
```json
{
  "1": {
    "rouge": {
      "poing_tronc": 0,
      "pied_tronc": 0,
      "pied_rotatif_tronc": 0,
      "pied_tete": 0,
      "pied_rotatif_tete": 0,
      "kyonggo": 0,
      "gamjeom": 0
    },
    "bleu": {
      "poing_tronc": 0,
      "pied_tronc": 0,
      "pied_rotatif_tronc": 0,
      "pied_tete": 0,
      "pied_rotatif_tete": 0,
      "kyonggo": 0,
      "gamjeom": 0
    }
  },
  "2": { ... },
  "3": { ... },
  "golden": { ... }
}
```

### Calcul des scores
```dart
int calculateScore(Map<String, dynamic> roundData, String combattant) {
  final data = roundData[combattant];
  final adversaire = combattant == 'rouge' ? 'bleu' : 'rouge';
  final advData = roundData[adversaire];
  
  int score = 0;
  score += data['poing_tronc'] * 1;
  score += data['pied_tronc'] * 2;
  score += data['pied_rotatif_tronc'] * 4;
  score += data['pied_tete'] * 3;
  score += data['pied_rotatif_tete'] * 5;
  
  // Pénalités adversaire = points pour nous
  score += (advData['kyonggo'] ~/ 2); // 2 kyonggo = 1 pt
  score += advData['gamjeom'];        // 1 gamjeom = 1 pt
  
  return score;
}
```

### Types de victoire
- `points` - Victoire aux points
- `ecart_20` - Écart de 20 points (victoire automatique)
- `disqualification` - Adversaire disqualifié (10 gam-jeom)
- `abandon` - Abandon de l'adversaire
- `ko` - KO technique
- `decision_arbitre` - Décision de l'arbitre

---

## 🔄 Synchronisation avec le Web

L'application mobile doit refléter exactement les mêmes fonctionnalités que l'application web Laravel :

| Module Web | Module Mobile | Statut |
|------------|---------------|--------|
| Dashboard | Dashboard | À mettre à jour |
| Athlètes | Athlètes | ✅ Existant |
| Coachs | Coachs | À ajouter |
| Disciplines | Disciplines | ✅ Existant |
| Présences | Présences | ✅ Existant |
| Paiements | Paiements | ✅ Existant |
| Performances | Performances | ✅ Existant |
| Rencontres | Rencontres | 🆕 À créer |
| Combats TKD | Combats TKD | 🆕 À créer |
| Activités | Activités | 🆕 À créer |
| Calendrier | Calendrier | À mettre à jour |
| Licences | Licences | 🆕 À créer |
| Certificats | Certificats | 🆕 À créer |
| Suivi Scolaire | Suivi Scolaire | 🆕 À créer |
| Stages | Stages | 🆕 À créer |
| Portail Athlète | Portail Athlète | À mettre à jour |
| Portail Parent | Portail Parent | À mettre à jour |

---

**Commence par analyser le projet Flutter existant et propose un plan d'implémentation pour synchroniser avec toutes ces nouvelles fonctionnalités du backend Laravel.**
