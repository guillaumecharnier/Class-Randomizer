<?php
// Démarrer la session
session_start();

// Définir les classes et races par faction, avec les restrictions de combinaison
$classes_races_factions = [
    "Alliance" => [
        "Humain" => ["Guerrier", "Paladin", "Voleur", "Prêtre", "Mage", "Démoniste"],
        "Nain" => ["Guerrier", "Paladin", "Chasseur", "Voleur", "Prêtre"],
        "Elfe de la nuit" => ["Druide", "Guerrier", "Chasseur", "Voleur", "Prêtre"],
        "Gnome" => ["Mage", "Démoniste", "Voleur", "Guerrier"],
    ],
    "Horde" => [
        "Orc" => ["Démoniste", "Guerrier", "Chaman", "Chasseur", "Voleur"],
        "Mort-vivant" => ["Prêtre", "Mage", "Démoniste", "Voleur", "Guerrier"],
        "Tauren" => ["Druide", "Guerrier", "Chasseur", "Chaman"],
        "Troll" => ["Guerrier", "Chasseur", "Chaman", "Mage", "Prêtre", "Voleur"],
    ]
];

// Initialisation des variables de session pour les tirages et les comptages
if (!isset($_SESSION['tirages_count'])) {
    $_SESSION['tirages_count'] = 0;
}

if (!isset($_SESSION['combinaisons_tirages'])) {
    $_SESSION['combinaisons_tirages'] = [];
}

// Réinitialiser le compteur de tirages si le bouton "Réinitialiser le compteur" est cliqué
if (isset($_POST['reset_tirages'])) {
    $_SESSION['tirages_count'] = 0;
    $_SESSION['combinaisons_tirages'] = [];
}

// Effacer toute la session si le bouton "Tout effacer" est cliqué
if (isset($_POST['destroy'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

$class_result = "";
$faction_result = "";
$race_result = "";

// Vérifier si le formulaire a été soumis pour tirer une classe, race et faction
if (isset($_POST['submit'])) {
    // Tirer une faction aléatoire
    $factions_possibles = array_keys($classes_races_factions);
    $random_faction_index = rand(0, count($factions_possibles) - 1);
    $faction_result = $factions_possibles[$random_faction_index];

    // Tirer une race parmi celles disponibles pour cette faction
    $races_possibles = array_keys($classes_races_factions[$faction_result]);
    $random_race_index = rand(0, count($races_possibles) - 1);
    $race_result = $races_possibles[$random_race_index];

    // Tirer une classe parmi celles disponibles pour cette race et faction
    $classes_possibles = $classes_races_factions[$faction_result][$race_result];
    $class_result = $classes_possibles[rand(0, count($classes_possibles) - 1)];

    // Combinaison complète (Race + Classe + Faction)
    $combinaison = $race_result . " - " . $class_result . " - " . $faction_result;

    // Mettre à jour le compteur pour cette combinaison
    if (isset($_SESSION['combinaisons_tirages'][$combinaison])) {
        $_SESSION['combinaisons_tirages'][$combinaison]++;
    } else {
        $_SESSION['combinaisons_tirages'][$combinaison] = 1;
    }

    // Mettre à jour le compteur de tirages
    $_SESSION['tirages_count']++;
}

// Initialiser la variable pour la combinaison la plus tirée
$max_combinaison = '';
$max_combinaison_count = 0;

// Vérifier si le tableau de combinaisons n'est pas vide avant d'appliquer max()
if (!empty($_SESSION['combinaisons_tirages'])) {
    // Trouver la combinaison avec le plus grand nombre de tirages
    $max_combinaison_count = max($_SESSION['combinaisons_tirages']);
    foreach ($_SESSION['combinaisons_tirages'] as $combinaison => $count) {
        if ($count == $max_combinaison_count) {
            $max_combinaison = $combinaison;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tirage Aléatoire de Classe, Race et Faction</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <!-- Première section pour le tirage -->
        <div class="section">
            <h1>Tirage Aléatoire WoW Vanilla Hardcore</h1>

            <form method="post">
                <button type="submit" name="submit">Tirer une Classe, Race et Faction</button>
            </form>

            <div class="normal">
                <p>Classe tirée : <?php echo $class_result; ?></p>
                <p>Race tirée : <?php echo $race_result; ?></p>
                <p>Faction tirée : <?php echo $faction_result; ?></p>
            </div>

            <div class="highlight">
                <h3>Combinaison la plus tirée :</h3>
                <p><?php echo $max_combinaison; ?> (<?php echo $max_combinaison_count; ?> tirages)</p>
            </div>

            <h3>Compteur de tirages : <?php echo $_SESSION['tirages_count']; ?></h3>

            <form method="post">
                <button type="submit" name="reset_tirages">Réinitialiser le compteur de tirages</button>
            </form>

            <form method="post">
                <button type="submit" name="destroy">Tout effacer (détruire la session)</button>
            </form>
        </div>

        <!-- Deuxième section pour afficher les fréquences des combinaisons -->
        <div class="section">
            <h3>Fréquence des Combinaisons :</h3>
            <div class="combinaison-list-container">
                <?php
                    foreach ($_SESSION['combinaisons_tirages'] as $combinaison => $count) {
                        echo "<p class='combinaison'>$combinaison : $count tirages</p>";
                    }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
