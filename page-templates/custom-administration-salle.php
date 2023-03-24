<?php

/*

Template Name: administration-salle

*/

/*********************VOIR ICI LES FONCTIONS AVEC LES ROLES DE WORDPRESS POUR ACCEDER UNIQUEMENT A UN CERTAIN ROLE   */
// VOIR POUR UN AFFICHAGE PAR SALLES ET NON GLOBAL 
get_header();


try {

    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


woffice_title(get_the_title());

// les réservations doivent etre triées par date mais a partir du jour j
$recup_administration_salles = $conn->query("SELECT locations.*, salles.*, wor80_users.* 
FROM locations 
JOIN salles ON locations.Idsalle = salles.Idsalle 
JOIN wor80_users ON locations.IdClient = wor80_users.ID  AND locations.DateDebut >= NOW()
ORDER BY locations.DateDebut asc;");



echo '<div class="col-md-8"style="margin-top:100px">';

echo '<button type="button" class="btn btn-primary">
Nombre de commande: <span class="badge badge-light">' . $recup_administration_salles->rowCount() . '</span>
</button><hr>';



//condition pour supprimer une reservation
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM locations WHERE IdLocation = ?");
    $stmt->execute([$id]);
    echo "la réservation à été supprimée avec succès";
}
//affichage d'un tableau pour le récapitulatif des reservations de salles
echo '<table class="table table-bordered">';

echo '<tr>';

echo '<th>Nom de la réservation</th> ';
echo '<th>Nom/Prénom</th> ';
echo '<th>Adresse Mail</th> ';
echo '<th>date/heure Réservation</th> ';
echo '<th>Date d\'enregistrement</th>';
//ICI,PAS DE LIEN POUR LA SUPPRESSION MAIS UN FORMULAIRE EN POST 
echo '<th>Suppr</th>';
echo '</tr>';


while ($commande = $recup_administration_salles->fetch(PDO::FETCH_ASSOC)) {


    echo '<tr>';
    echo '<td>' . $commande['nom']  . ' à ' . $commande['lieu'] . '</td>';
    echo '<td>' . $commande['user_login'] . '</td>';
    echo '<td>' . $commande['user_email'] . '</td>';
    echo '<td>' . ' Du ' . date('d-m-Y H:i:s', strtotime($commande['DateDebut'])) . ' au ' . date('d-m-Y H:i:s', strtotime($commande['DateFin'])) . '</td>';
    //ICI,PAS DE LIEN POUR LA SUPPRESSION MAIS UN FORMULAIRE EN POST 
    echo '<td>' . date('d-m-Y H:i:s', strtotime($commande['dateReservation'])) . '</td>';
    //essai avec formulaire 
    echo '<td>' . '<form action="" method="POST" onSubmit="return confirm(\'Etes-vous sûr de vouloir supprimer cette réservation ?\');">
<input type="hidden" name="id" value="' . $commande['IdLocation'] . '">
<input type="submit" value="supprimer">
</form>' . '</td>';


    //*****************************pour modifier par la suite les réservations************

    //ICI PAS UN LIEN MAIS UN FORMULAIRE EN POST
    // echo '<td><a href="?action=suppression&IdLocation=' . $commande['IdLocation'] . '" class="btn btn-danger text-white" onclick="return(confirm(\'Etes-vous sûr ?\'));" ><i class="fas fa-trash-alt"></i></a></td>';

    echo '</tr>';
}
/*********suppresion d'une reservation */
//CONDITION A MODIFIER EN POST
// if ($_GET['action'] == 'suppression') {
//     $id = $_GET['IdLocation'];
//     $stmt = $conn->prepare("DELETE FROM locations WHERE IdLocation = ?");
//     $stmt->execute([$id]);
// }


// envoyer un mail  a la personne concernée pour confirmée son annulation faite par un chef
echo '</table>';
echo '</div>';
