<?php

namespace Database\Seeders;

use App\Models\ChatFaq;
use Illuminate\Database\Seeder;

/**
 * ============================================================================
 * Seeder pour les FAQ du Chatbot OBD
 * ============================================================================
 * Ce seeder crée des questions-réponses préenregistrées pour le chatbot.
 * Les FAQ sont organisées par catégories :
 * - Informations générales (horaires, contact, inscription, tarifs)
 * - Disciplines spécifiques (taekwondo, basketball, volleyball, gymnastique)
 * - Sport & Santé (nutrition, hydratation, récupération)
 * - Pédagogie pour parents (accompagnement, motivation, communication)
 * - Prévention & Bien-être (blessures, échauffement, mental)
 */
class ChatFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // ================================================================
            // INFORMATIONS GÉNÉRALES
            // ================================================================
            
            // Horaires
            [
                'category' => 'horaires',
                'keywords' => ['horaire', 'heure', 'ouvert', 'ferme', 'quand', 'matin', 'soir', 'weekend', 'samedi', 'dimanche'],
                'question' => 'Quels sont les horaires du centre ?',
                'answer' => "🕐 Nos horaires d'ouverture :\n\n• Lundi - Vendredi : 8h00 - 20h00\n• Samedi : 8h00 - 18h00\n• Dimanche : 9h00 - 14h00\n\nLes entraînements sont organisés selon les disciplines et catégories d'âge.",
                'priority' => 10,
            ],
            // Disciplines
            [
                'category' => 'disciplines',
                'keywords' => ['discipline', 'sport', 'proposez', 'activité', 'liste', 'offre'],
                'question' => 'Quelles disciplines proposez-vous ?',
                'answer' => "🏅 Nos disciplines sportives :\n\n• 🥋 Taekwondo\n• 🏀 Basketball\n• 🏐 Volleyball\n• 🤸 Gymnastique\n\nChaque discipline est encadrée par des coachs qualifiés et diplômés.",
                'priority' => 10,
            ],
            // Inscription
            [
                'category' => 'inscription',
                'keywords' => ['inscrire', 'inscription', 'rejoindre', 'adhérer', 'membre', 'comment', 'enfant', 'fils', 'fille'],
                'question' => 'Comment inscrire mon enfant ?',
                'answer' => "📝 Pour inscrire votre enfant :\n\n1. Rendez-vous au secrétariat du centre\n2. Apportez les documents suivants :\n   • Certificat médical d'aptitude au sport\n   • 2 photos d'identité\n   • Copie de l'acte de naissance\n   • Frais d'inscription\n\nVous pouvez aussi nous contacter pour plus d'informations.",
                'priority' => 9,
            ],
            // Tarifs
            [
                'category' => 'tarifs',
                'keywords' => ['tarif', 'prix', 'coût', 'combien', 'payer', 'cotisation', 'mensuel', 'annuel', 'frais'],
                'question' => 'Quels sont les tarifs ?',
                'answer' => "💰 Nos tarifs :\n\n• Inscription annuelle : 15 000 FCFA\n• Cotisation mensuelle : 5 000 - 10 000 FCFA (selon discipline)\n• Équipement sportif : variable selon discipline\n\nDes facilités de paiement sont disponibles. Contactez le secrétariat pour plus de détails.",
                'priority' => 9,
            ],
            // Contact
            [
                'category' => 'contact',
                'keywords' => ['contact', 'téléphone', 'appeler', 'joindre', 'adresse', 'où', 'localisation', 'email', 'mail'],
                'question' => 'Comment vous contacter ?',
                'answer' => "📞 Nos coordonnées :\n\n• Téléphone : +223 XX XX XX XX\n• Email : contact@obd-sport.ml\n• Adresse : Baco-Djicoroni, Bamako, Mali\n\nNous sommes disponibles du lundi au samedi.",
                'priority' => 8,
            ],
            // Certificat médical
            [
                'category' => 'documents',
                'keywords' => ['certificat', 'médical', 'docteur', 'médecin', 'santé', 'visite', 'aptitude'],
                'question' => 'Le certificat médical est-il obligatoire ?',
                'answer' => "🏥 Oui, le certificat médical est obligatoire.\n\nIl doit :\n• Être délivré par un médecin agréé\n• Attester l'aptitude à la pratique sportive\n• Être renouvelé chaque année\n\nSans certificat valide, l'athlète ne peut pas participer aux entraînements.",
                'priority' => 7,
            ],
            // Âge
            [
                'category' => 'inscription',
                'keywords' => ['âge', 'ans', 'minimum', 'maximum', 'enfant', 'adulte', 'jeune', 'catégorie'],
                'question' => 'À partir de quel âge peut-on s\'inscrire ?',
                'answer' => "👶 Âges d'inscription :\n\n• Éveil sportif : 4-6 ans\n• Poussins : 7-8 ans\n• Benjamins : 9-10 ans\n• Minimes : 11-12 ans\n• Cadets : 13-14 ans\n• Juniors : 15-17 ans\n• Seniors : 18 ans et plus\n\nChaque catégorie a des entraînements adaptés.",
                'priority' => 7,
            ],
            // Équipement
            [
                'category' => 'equipement',
                'keywords' => ['équipement', 'tenue', 'maillot', 'chaussure', 'matériel', 'acheter', 'fournir'],
                'question' => 'Quel équipement faut-il ?',
                'answer' => "👕 Équipement nécessaire :\n\n• Tenue de sport adaptée à la discipline\n• Chaussures de sport appropriées\n• Bouteille d'eau\n• Serviette\n\nLe centre fournit le maillot officiel après inscription. Certains équipements spécifiques peuvent être achetés sur place.",
                'priority' => 6,
            ],
            // Compétitions
            [
                'category' => 'competitions',
                'keywords' => ['compétition', 'tournoi', 'match', 'championnat', 'participer', 'jouer'],
                'question' => 'Y a-t-il des compétitions ?',
                'answer' => "🏆 Oui, nous participons à :\n\n• Championnats régionaux\n• Tournois inter-clubs\n• Compétitions nationales\n• Événements sportifs locaux\n\nLes athlètes sont sélectionnés selon leur niveau et leur assiduité aux entraînements.",
                'priority' => 6,
            ],
            // Paiement
            [
                'category' => 'paiement',
                'keywords' => ['paiement', 'payer', 'orange money', 'mobile money', 'espèce', 'virement', 'retard'],
                'question' => 'Quels sont les modes de paiement ?',
                'answer' => "💳 Modes de paiement acceptés :\n\n• Espèces au secrétariat\n• Orange Money\n• Mobile Money\n• Virement bancaire\n\nLe paiement mensuel doit être effectué avant le 10 de chaque mois.",
                'priority' => 5,
            ],

            // ================================================================
            // TAEKWONDO
            // ================================================================
            [
                'category' => 'taekwondo',
                'keywords' => ['taekwondo', 'tkd', 'ceinture', 'grade', 'dan', 'poomsae', 'combat', 'art martial'],
                'question' => 'Comment fonctionne le taekwondo au centre ?',
                'answer' => "🥋 Le Taekwondo au centre OBD :\n\n• Cours 3x par semaine (Lundi, Mercredi, Vendredi)\n• Progression par ceintures (blanche → noire)\n• Apprentissage des poomsae (formes) et du combat\n• Préparation aux compétitions nationales\n\nLe taekwondo développe la discipline, le respect et la confiance en soi.",
                'priority' => 8,
            ],
            [
                'category' => 'taekwondo',
                'keywords' => ['ceinture', 'grade', 'passage', 'examen', 'niveau', 'progression'],
                'question' => 'Comment passer les ceintures en taekwondo ?',
                'answer' => "🎖️ Progression des ceintures :\n\n• Ceinture blanche → Jaune → Verte → Bleue → Rouge → Noire\n• Examen de passage tous les 3-4 mois\n• Critères : technique, poomsae, combat, assiduité\n• La ceinture noire (1er Dan) nécessite minimum 3 ans de pratique\n\nVotre coach vous indiquera quand vous êtes prêt pour le passage.",
                'priority' => 7,
            ],
            [
                'category' => 'taekwondo',
                'keywords' => ['dobok', 'kimono', 'tenue', 'équipement', 'protection', 'casque', 'plastron'],
                'question' => 'Quel équipement pour le taekwondo ?',
                'answer' => "🥋 Équipement taekwondo :\n\n**Obligatoire :**\n• Dobok (tenue blanche)\n• Ceinture de votre grade\n\n**Pour le combat :**\n• Casque de protection\n• Plastron (hogu)\n• Protège-tibias et avant-bras\n• Coquille\n• Protège-dents\n\nL'équipement de combat peut être prêté pour les débutants.",
                'priority' => 6,
            ],
            [
                'category' => 'taekwondo',
                'keywords' => ['bienfait', 'avantage', 'bénéfice', 'pourquoi', 'enfant', 'développement'],
                'question' => 'Quels sont les bienfaits du taekwondo pour les enfants ?',
                'answer' => "✨ Bienfaits du taekwondo :\n\n**Physiques :**\n• Souplesse et coordination\n• Force et endurance\n• Équilibre et agilité\n\n**Mentaux :**\n• Discipline et respect\n• Confiance en soi\n• Concentration\n• Gestion du stress\n\n**Sociaux :**\n• Esprit d'équipe\n• Respect des règles\n• Persévérance",
                'priority' => 7,
            ],

            // ================================================================
            // BASKETBALL
            // ================================================================
            [
                'category' => 'basketball',
                'keywords' => ['basket', 'basketball', 'ballon', 'panier', 'dribble', 'shoot', 'équipe'],
                'question' => 'Comment fonctionne le basketball au centre ?',
                'answer' => "🏀 Le Basketball au centre OBD :\n\n• Entraînements 3x par semaine\n• Catégories : Mini-basket (6-10 ans), Cadets, Juniors, Seniors\n• Apprentissage technique et tactique\n• Matchs amicaux et championnats\n\nLe basketball développe l'esprit d'équipe et la coordination.",
                'priority' => 8,
            ],
            [
                'category' => 'basketball',
                'keywords' => ['technique', 'dribble', 'passe', 'shoot', 'tir', 'défense', 'apprendre'],
                'question' => 'Quelles techniques apprend-on en basketball ?',
                'answer' => "🏀 Techniques enseignées :\n\n**Fondamentaux :**\n• Dribble (main droite/gauche)\n• Passes (poitrine, baseball, à terre)\n• Tir (lay-up, jump shot)\n• Défense (position, déplacement)\n\n**Avancé :**\n• Écrans et pick & roll\n• Contre-attaque\n• Jeu en équipe\n• Lecture du jeu",
                'priority' => 6,
            ],
            [
                'category' => 'basketball',
                'keywords' => ['taille', 'grand', 'petit', 'hauteur', 'jouer', 'position'],
                'question' => 'Faut-il être grand pour jouer au basketball ?',
                'answer' => "📏 Non, la taille n'est pas tout !\n\n• Les meneurs sont souvent de taille moyenne\n• La vitesse et l'agilité sont très importantes\n• Chaque poste a ses avantages\n\n**Positions selon le profil :**\n• Meneur (1) : vision du jeu, rapidité\n• Arrière (2) : tir, pénétration\n• Ailier (3) : polyvalence\n• Ailier fort (4) : puissance\n• Pivot (5) : taille, rebonds\n\nTous les profils ont leur place !",
                'priority' => 5,
            ],

            // ================================================================
            // VOLLEYBALL
            // ================================================================
            [
                'category' => 'volleyball',
                'keywords' => ['volley', 'volleyball', 'filet', 'service', 'smash', 'manchette', 'passe'],
                'question' => 'Comment fonctionne le volleyball au centre ?',
                'answer' => "🏐 Le Volleyball au centre OBD :\n\n• Entraînements 2-3x par semaine\n• Catégories mixtes et séparées\n• Apprentissage progressif des gestes\n• Tournois inter-clubs\n\nLe volleyball développe la coordination, les réflexes et l'esprit d'équipe.",
                'priority' => 8,
            ],
            [
                'category' => 'volleyball',
                'keywords' => ['technique', 'manchette', 'passe haute', 'service', 'smash', 'block', 'réception'],
                'question' => 'Quelles techniques apprend-on en volleyball ?',
                'answer' => "🏐 Techniques de volleyball :\n\n**Fondamentaux :**\n• Manchette (réception basse)\n• Passe haute (touche)\n• Service (cuillère, tennis, smashé)\n\n**Attaque :**\n• Smash (attaque)\n• Feinte\n\n**Défense :**\n• Block (contre)\n• Défense basse\n• Couverture\n\nChaque joueur apprend toutes les positions en rotation.",
                'priority' => 6,
            ],
            [
                'category' => 'volleyball',
                'keywords' => ['position', 'poste', 'rôle', 'passeur', 'attaquant', 'libéro'],
                'question' => 'Quels sont les postes au volleyball ?',
                'answer' => "🏐 Postes au volleyball :\n\n• **Passeur** : Organise le jeu, fait les passes décisives\n• **Attaquant (Pointu)** : Finit les actions, puissance\n• **Réceptionneur-Attaquant** : Polyvalent, réception et attaque\n• **Central** : Block et attaque rapide\n• **Libéro** : Spécialiste défense, ne peut pas attaquer\n\nLes débutants apprennent tous les postes avant de se spécialiser.",
                'priority' => 5,
            ],

            // ================================================================
            // GYMNASTIQUE
            // ================================================================
            [
                'category' => 'gymnastique',
                'keywords' => ['gym', 'gymnastique', 'acrobatie', 'souplesse', 'agrès', 'sol', 'poutre'],
                'question' => 'Comment fonctionne la gymnastique au centre ?',
                'answer' => "🤸 La Gymnastique au centre OBD :\n\n• Cours adaptés par niveau et âge\n• Gymnastique artistique et rythmique\n• Travail au sol et sur agrès\n• Développement de la souplesse et de la force\n\nLa gymnastique est la base de nombreux sports et développe toutes les qualités physiques.",
                'priority' => 8,
            ],
            [
                'category' => 'gymnastique',
                'keywords' => ['souplesse', 'étirement', 'flexible', 'raide', 'écart', 'pont'],
                'question' => 'Comment améliorer sa souplesse en gymnastique ?',
                'answer' => "🧘 Améliorer sa souplesse :\n\n**Conseils :**\n• S'étirer après l'échauffement (muscles chauds)\n• Tenir chaque étirement 30 secondes minimum\n• Respirer profondément pendant l'étirement\n• Pratiquer régulièrement (idéalement tous les jours)\n\n**Exercices clés :**\n• Écarts (facial et latéral)\n• Pont\n• Étirements des jambes et du dos\n\nLa souplesse s'acquiert progressivement, soyez patient !",
                'priority' => 6,
            ],
            [
                'category' => 'gymnastique',
                'keywords' => ['figure', 'mouvement', 'roulade', 'roue', 'salto', 'flip', 'acrobatie'],
                'question' => 'Quelles figures apprend-on en gymnastique ?',
                'answer' => "🤸 Progression des figures :\n\n**Niveau 1 (Débutant) :**\n• Roulade avant et arrière\n• Équilibre (chandelle)\n• Roue\n\n**Niveau 2 (Intermédiaire) :**\n• Rondade\n• Flip avant\n• Équilibre sur les mains\n\n**Niveau 3 (Avancé) :**\n• Salto avant et arrière\n• Flic-flac\n• Enchaînements\n\nChaque figure est apprise de manière sécurisée avec le coach.",
                'priority' => 6,
            ],

            // ================================================================
            // SPORT & SANTÉ
            // ================================================================
            [
                'category' => 'sante',
                'keywords' => ['manger', 'alimentation', 'nutrition', 'repas', 'avant', 'après', 'entraînement', 'nourriture'],
                'question' => 'Que manger avant et après l\'entraînement ?',
                'answer' => "🍎 Nutrition sportive :\n\n**Avant l'entraînement (2-3h avant) :**\n• Glucides complexes (riz, pâtes, pain)\n• Protéines légères (poulet, poisson)\n• Fruits\n• Éviter les graisses lourdes\n\n**Après l'entraînement (dans les 30 min) :**\n• Protéines (œufs, viande, légumineuses)\n• Glucides pour récupérer\n• Beaucoup d'eau\n\n**Collation légère (1h avant) :**\n• Banane, barre de céréales, yaourt",
                'priority' => 8,
            ],
            [
                'category' => 'sante',
                'keywords' => ['eau', 'boire', 'hydratation', 'soif', 'déshydratation', 'boisson'],
                'question' => 'Combien d\'eau faut-il boire pendant le sport ?',
                'answer' => "💧 Hydratation sportive :\n\n**Avant l'effort :**\n• 500 ml dans les 2h précédentes\n\n**Pendant l'effort :**\n• 150-200 ml toutes les 15-20 minutes\n• Ne pas attendre d'avoir soif !\n\n**Après l'effort :**\n• Boire jusqu'à ce que l'urine soit claire\n• Environ 1,5L par kg perdu\n\n**Signes de déshydratation :**\n• Fatigue, maux de tête\n• Crampes musculaires\n• Urine foncée",
                'priority' => 8,
            ],
            [
                'category' => 'sante',
                'keywords' => ['sommeil', 'dormir', 'repos', 'fatigue', 'récupération', 'nuit', 'heure'],
                'question' => 'Combien d\'heures de sommeil pour un jeune sportif ?',
                'answer' => "😴 Sommeil et sport :\n\n**Heures recommandées :**\n• 6-12 ans : 9-12 heures\n• 13-18 ans : 8-10 heures\n• Adultes : 7-9 heures\n\n**Importance du sommeil :**\n• Récupération musculaire\n• Consolidation des apprentissages\n• Croissance (hormone de croissance)\n• Système immunitaire\n\n**Conseils :**\n• Horaires réguliers\n• Éviter les écrans 1h avant\n• Chambre fraîche et sombre",
                'priority' => 7,
            ],
            [
                'category' => 'sante',
                'keywords' => ['croissance', 'grandir', 'taille', 'développement', 'enfant', 'adolescent'],
                'question' => 'Le sport aide-t-il à grandir ?',
                'answer' => "📈 Sport et croissance :\n\n**Oui, le sport favorise la croissance !**\n\n• Stimule la sécrétion d'hormone de croissance\n• Renforce les os et les muscles\n• Améliore la posture\n• Favorise un sommeil de qualité\n\n**Sports recommandés :**\n• Natation, basketball, volleyball (étirement)\n• Gymnastique (souplesse)\n• Tous les sports pratiqués avec modération\n\n**Attention :**\n• Éviter le surentraînement\n• Alimentation équilibrée essentielle\n• Repos suffisant",
                'priority' => 6,
            ],

            // ================================================================
            // PÉDAGOGIE POUR PARENTS
            // ================================================================
            [
                'category' => 'parents',
                'keywords' => ['parent', 'accompagner', 'soutenir', 'encourager', 'aider', 'rôle', 'famille'],
                'question' => 'Comment accompagner mon enfant dans le sport ?',
                'answer' => "👨‍👩‍👧 Rôle des parents :\n\n**À faire :**\n• Encourager les efforts, pas seulement les résultats\n• Être présent aux matchs/compétitions\n• Respecter les décisions du coach\n• Valoriser le plaisir de jouer\n• Assurer une bonne hygiène de vie\n\n**À éviter :**\n• Mettre trop de pression\n• Critiquer pendant les matchs\n• Comparer avec d'autres enfants\n• Imposer vos ambitions\n\nVotre soutien bienveillant est essentiel !",
                'priority' => 9,
            ],
            [
                'category' => 'parents',
                'keywords' => ['motivation', 'motiver', 'envie', 'abandonner', 'arrêter', 'décourager', 'lassé'],
                'question' => 'Mon enfant veut arrêter le sport, que faire ?',
                'answer' => "💪 Gérer la démotivation :\n\n**Comprendre les raisons :**\n• Fatigue passagère ?\n• Problème avec un camarade ?\n• Difficulté technique ?\n• Trop de pression ?\n\n**Solutions :**\n• Dialoguer sans juger\n• Rencontrer le coach\n• Proposer une pause courte\n• Rappeler les bons moments\n• Fixer des objectifs atteignables\n\n**Important :**\n• Ne pas forcer mais ne pas céder trop vite\n• La persévérance s'apprend\n• Parfois changer de discipline est la solution",
                'priority' => 8,
            ],
            [
                'category' => 'parents',
                'keywords' => ['école', 'scolaire', 'études', 'devoirs', 'notes', 'équilibre', 'temps'],
                'question' => 'Comment équilibrer sport et études ?',
                'answer' => "📚 Équilibre sport-études :\n\n**Organisation :**\n• Planning hebdomadaire clair\n• Devoirs avant l'entraînement si possible\n• Temps de repos préservé\n• Week-end pour rattraper si besoin\n\n**Bienfaits du sport sur les études :**\n• Meilleure concentration\n• Gestion du stress\n• Discipline et organisation\n• Confiance en soi\n\n**Signaux d'alerte :**\n• Baisse des notes\n• Fatigue excessive\n• Stress permanent\n\nLe sport doit rester un plaisir, pas une contrainte !",
                'priority' => 8,
            ],
            [
                'category' => 'parents',
                'keywords' => ['coach', 'entraîneur', 'communiquer', 'parler', 'relation', 'problème'],
                'question' => 'Comment communiquer avec le coach de mon enfant ?',
                'answer' => "🗣️ Communication avec le coach :\n\n**Bonnes pratiques :**\n• Prendre rendez-vous (pas pendant l'entraînement)\n• Écouter avant de parler\n• Poser des questions ouvertes\n• Partager les informations importantes (santé, école)\n\n**Sujets à aborder :**\n• Progression de l'enfant\n• Points à améliorer\n• Comportement en groupe\n• Objectifs à venir\n\n**À éviter :**\n• Remettre en cause les choix techniques\n• Demander plus de temps de jeu\n• Critiquer devant l'enfant",
                'priority' => 7,
            ],
            [
                'category' => 'parents',
                'keywords' => ['compétition', 'stress', 'anxiété', 'peur', 'match', 'pression', 'nerveux'],
                'question' => 'Mon enfant stresse avant les compétitions, que faire ?',
                'answer' => "😰 Gérer le stress de compétition :\n\n**Avant la compétition :**\n• Routine rassurante (repas, sommeil)\n• Visualisation positive\n• Respiration profonde\n• Rappeler que c'est un jeu\n\n**Le jour J :**\n• Arriver à l'avance\n• Échauffement complet\n• Mots d'encouragement simples\n• Éviter les consignes de dernière minute\n\n**Après :**\n• Féliciter les efforts\n• Analyser calmement (plus tard)\n• Célébrer la participation\n\nUn peu de stress est normal et peut être positif !",
                'priority' => 7,
            ],

            // ================================================================
            // PRÉVENTION & BIEN-ÊTRE
            // ================================================================
            [
                'category' => 'prevention',
                'keywords' => ['blessure', 'mal', 'douleur', 'prévenir', 'éviter', 'risque', 'accident'],
                'question' => 'Comment éviter les blessures sportives ?',
                'answer' => "🩹 Prévention des blessures :\n\n**Avant l'effort :**\n• Échauffement complet (10-15 min)\n• Étirements dynamiques\n• Vérifier son équipement\n\n**Pendant l'effort :**\n• Respecter les consignes du coach\n• Écouter son corps\n• S'hydrater régulièrement\n\n**Après l'effort :**\n• Étirements statiques\n• Récupération active\n• Repos suffisant\n\n**Facteurs de risque :**\n• Fatigue excessive\n• Mauvaise technique\n• Équipement inadapté\n• Croissance rapide (ados)",
                'priority' => 9,
            ],
            [
                'category' => 'prevention',
                'keywords' => ['échauffement', 'chauffer', 'préparer', 'avant', 'commencer', 'muscles'],
                'question' => 'Pourquoi l\'échauffement est-il important ?',
                'answer' => "🔥 Importance de l'échauffement :\n\n**Effets sur le corps :**\n• Augmente la température musculaire\n• Améliore la circulation sanguine\n• Prépare les articulations\n• Active le système nerveux\n\n**Échauffement type (10-15 min) :**\n1. Course légère (3-5 min)\n2. Mobilisation articulaire\n3. Étirements dynamiques\n4. Exercices spécifiques au sport\n\n**Sans échauffement :**\n• Risque de blessure x3\n• Performance réduite\n• Récupération plus longue",
                'priority' => 8,
            ],
            [
                'category' => 'prevention',
                'keywords' => ['récupération', 'repos', 'après', 'effort', 'courbature', 'muscle', 'fatigue'],
                'question' => 'Comment bien récupérer après l\'entraînement ?',
                'answer' => "🧊 Récupération optimale :\n\n**Juste après l'effort :**\n• Retour au calme progressif\n• Étirements légers (5-10 min)\n• Réhydratation immédiate\n\n**Dans les heures suivantes :**\n• Collation protéinée\n• Douche (pas trop chaude)\n• Repos actif (marche légère)\n\n**Les jours suivants :**\n• Sommeil de qualité\n• Alimentation équilibrée\n• Alterner les groupes musculaires\n\n**En cas de courbatures :**\n• Bain chaud\n• Massage léger\n• Mouvement doux",
                'priority' => 7,
            ],
            [
                'category' => 'prevention',
                'keywords' => ['mental', 'tête', 'psychologie', 'confiance', 'concentration', 'focus'],
                'question' => 'Comment développer le mental sportif ?',
                'answer' => "🧠 Préparation mentale :\n\n**Techniques :**\n• Visualisation (imaginer la réussite)\n• Respiration contrôlée\n• Discours intérieur positif\n• Fixation d'objectifs SMART\n\n**Qualités à développer :**\n• Concentration\n• Gestion du stress\n• Résilience (rebondir après l'échec)\n• Confiance en soi\n\n**Exercices quotidiens :**\n• 5 min de visualisation\n• Journal de progression\n• Célébrer les petites victoires\n\nLe mental se travaille comme le physique !",
                'priority' => 7,
            ],
            [
                'category' => 'prevention',
                'keywords' => ['chaleur', 'chaud', 'soleil', 'été', 'canicule', 'coup de chaleur', 'température'],
                'question' => 'Comment s\'entraîner par forte chaleur ?',
                'answer' => "☀️ Sport et chaleur :\n\n**Précautions :**\n• S'entraîner tôt le matin ou en soirée\n• Porter des vêtements clairs et légers\n• Casquette et lunettes de soleil\n• Crème solaire\n\n**Hydratation renforcée :**\n• Boire avant d'avoir soif\n• Eau fraîche (pas glacée)\n• Boissons isotoniques si effort > 1h\n\n**Signes d'alerte (coup de chaleur) :**\n• Maux de tête, vertiges\n• Nausées\n• Peau chaude et sèche\n• Confusion\n\n➡️ Arrêter immédiatement et se mettre à l'ombre !",
                'priority' => 6,
            ],
            [
                'category' => 'prevention',
                'keywords' => ['fair-play', 'respect', 'règle', 'adversaire', 'arbitre', 'comportement', 'esprit sportif'],
                'question' => 'Qu\'est-ce que le fair-play ?',
                'answer' => "🤝 Le Fair-Play :\n\n**Définition :**\nRespecter les règles, les adversaires, les arbitres et soi-même.\n\n**En pratique :**\n• Saluer l'adversaire avant et après\n• Accepter les décisions de l'arbitre\n• Aider un adversaire blessé\n• Reconnaître ses fautes\n• Féliciter le vainqueur\n\n**Valeurs transmises :**\n• Respect\n• Honnêteté\n• Humilité\n• Solidarité\n\nLe fair-play est plus important que la victoire !",
                'priority' => 6,
            ],

            // ================================================================
            // QUESTIONS GÉNÉRALES CHATBOT
            // ================================================================
            [
                'category' => 'chatbot',
                'keywords' => ['bonjour', 'salut', 'hello', 'coucou', 'bonsoir', 'hey'],
                'question' => 'Salutations',
                'answer' => "👋 Bonjour et bienvenue au Centre Sportif OBD !\n\nJe suis votre assistant virtuel. Je peux vous aider avec :\n• 📅 Horaires et inscriptions\n• 🏅 Informations sur nos disciplines\n• 💰 Tarifs et paiements\n• 🏥 Conseils santé et prévention\n\nComment puis-je vous aider aujourd'hui ?",
                'priority' => 10,
            ],
            [
                'category' => 'chatbot',
                'keywords' => ['merci', 'remercie', 'super', 'génial', 'parfait', 'excellent'],
                'question' => 'Remerciements',
                'answer' => "😊 Je vous en prie ! C'est un plaisir de vous aider.\n\nN'hésitez pas si vous avez d'autres questions. Je suis là pour vous accompagner dans votre parcours sportif au centre OBD.\n\nBonne journée et bon entraînement ! 💪",
                'priority' => 5,
            ],
            [
                'category' => 'chatbot',
                'keywords' => ['aide', 'aider', 'question', 'information', 'renseignement', 'savoir'],
                'question' => 'Demande d\'aide',
                'answer' => "🤖 Je peux vous aider sur :\n\n**Informations générales :**\n• Horaires, tarifs, inscription\n• Contact et localisation\n\n**Disciplines :**\n• Taekwondo, Basketball, Volleyball, Gymnastique\n\n**Santé & Bien-être :**\n• Nutrition, hydratation, sommeil\n• Prévention des blessures\n\n**Pour les parents :**\n• Accompagnement, motivation\n• Équilibre sport-études\n\nPosez-moi votre question !",
                'priority' => 9,
            ],
        ];

        foreach ($faqs as $faq) {
            ChatFaq::updateOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['actif' => true])
            );
        }

        $this->command->info('✓ ' . count($faqs) . ' FAQ créées pour le chatbot');
    }
}
