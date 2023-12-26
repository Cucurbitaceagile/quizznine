<?php
session_start();

// Paramètres de connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$motDePasse = "";
$nomBaseDeDonnees = "quizzine_database";

// Connexion à la base de données
$mysqli = new mysqli($serveur, $utilisateur, $motDePasse, $nomBaseDeDonnees);

if ($mysqli->connect_error) {
    die("Erreur de connexion à la base de données : " . $mysqli->connect_error);
}

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['username'])) {
    echo "Bienvenue, " . $_SESSION['username'] . " ! <a href='logout.php'>Se déconnecter</a>";
} else {
    // Si la méthode HTTP est POST (le formulaire est soumis)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Vérifier les informations d'authentification dans la base de données
        $query = "SELECT password FROM users WHERE username = '$username'";
        $result = $mysqli->query($query);

        if ($result->num_rows === 0) {
            // Si l'utilisateur n'existe pas, enregistrer les informations dans la base de données
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";
            $mysqli->query($insertQuery);

            $_SESSION['username'] = $username;
            echo "Bienvenue, $username ! Première connexion réussie.";
        } else {
            // Si l'utilisateur existe, vérifier le mot de passe
            $row = $result->fetch_assoc();
            $hashedPasswordFromDB = $row['password'];

            if (password_verify($password, $hashedPasswordFromDB)) {
                $_SESSION['username'] = $username;
                echo "Bienvenue, $username !";
            } else {
                echo "Identifiants incorrects. Veuillez réessayer.";
            }
        }
    } else {
        // Afficher le formulaire de connexion
?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Page de Connexion</title>
        </head>
        <body>
            <h2>Connexion</h2>
            <form action="authentification.php" method="post">
                <label for="username">Nom d'utilisateur :</label><br>
                <input type="text" id="username" name="username" required><br>

                <label for="password">Mot de passe :</label><br>
                <input type="password" id="password" name="password" required><br><br>

                <input type="submit" value="Se connecter">
            </form>
        </body>
        </html>
<?php
    }
}
// Fermer la connexion à la base de données
$mysqli->close();
?>