# AGENTS.md

Ce document définit la répartition des responsabilités entre plusieurs agents pour le développement de l'application de gestion d'un centre de sport.

---

## 🧑‍💻 Agent 1 — **Architecte & Backend Laravel**
**Responsabilités :**
- Concevoir l'architecture globale de l'application.
- Créer les modèles, migrations et relations Eloquent.
- Mettre en place les seeders (disciplines, rôles, tests initiaux).
- Développer les contrôleurs principaux (CRUD, logique business, validation).
- Implémenter l'authentification (Laravel Breeze).
- Gérer les middlewares (admin, coach).
- Définir les API internes si nécessaire.

---

## 🎨 Agent 2 — **Front-End & UI/UX (Blade + Tailwind)**
**Responsabilités :**
- Créer les vues Blade complètes et responsive.
- Développer le design avec Tailwind (palette Mali : vert, jaune, rouge).
- Structurer des composants Blade réutilisables.
- Intégrer Chart.js pour les graphiques.
- Créer les interfaces : dashboard, formulaires, listes, statistiques.
- Optimiser l’expérience utilisateur (navigation, lisibilité, mobile-first).

---

## 📊 Agent 3 — **Gestion des modules métier**
**Responsabilités :**
- Définir et développer les modules :
  - Gestion des athlètes
  - Gestion des coachs
  - Gestion des disciplines
  - Système de présences
  - Suivi scolaire
  - Paiements et arriérés
  - Performances sportives
- Implémenter la logique métier propre à chaque module.
- Assurer la cohérence entre tous les workflows.

---

## 🛠️ Agent 4 — **Qualité, Tests & Sécurité**
**Responsabilités :**
- Mettre en place les tests unitaires et fonctionnels (PHPUnit).
- Vérifier les contrôles de permissions (admin/coach).
- Tester tous les formulaires, validations et redirections.
- Assurer une bonne gestion des erreurs.
- Optimiser la sécurité générale (CSRF, validation, injections...).
- Proposer des améliorations de performance.

---

## 📦 Agent 5 — **Intégration & Documentation**
**Responsabilités :**
- Documenter toute l’architecture du projet.
- Rédiger le guide d’installation et déploiement.
- Centraliser le versioning Git et conventions de commit.
- Maintenir les fichiers README, AGENTS.md, ROADMAP.md.
- Organiser la structure globale du projet.

---

## 🔄 Agent 6 — **Support & Evolution du produit**
**Responsabilités :**
- Collecter les demandes du propriétaire.
- Proposer des évolutions fonctionnelles.
- Identifier les points faibles ou possibles améliorations.
- Prioriser les nouvelles fonctionnalités.

---

## 🧩 Coordination générale
Tous les agents doivent :
- Maintenir une communication constante.
- Documenter leurs décisions techniques.
- Respecter les dépendances entre modules.
- Assurer l'uniformité du code (PSR-12, standards Laravel).

---

**Fin du fichier AGENTS.md**

