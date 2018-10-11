<?php
SESSION_start();

// Globale Variablen einbinden
include 'functions.php';

// Name des Skripts
$skript_name = "index.php";

// Login pruefen
pruefe_login($skript_name);

$Titel = "Tippspiel is kaputt!";

frame_header($skript_name);
?>
<center>
    <h2> Kurze(?) Unterbrechung </h2>
    <img src=<?php echo IMAGE_PATH ?>/stoerung.jpg>
         <h4> Wegen einiger Umbauarbeiten muss der Zugriff unterbrochen werden ... </h4>
</center>
</body>

<?php
echo" </form>\n";
frame_footer();


/*
 * DB-Verbindung sauber beenden
 */
mysqli_close($connection);
?>