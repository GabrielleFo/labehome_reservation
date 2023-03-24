<?php

/*

Template Name: Mes réservations salles 

*/
get_header();


try {

    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
// récupération de l'id connecté 
// echo 'The current logged in user ID is: ' . get_current_user_id();

$id_client = get_current_user_id();
//selectionner que les champs désirées
$user_mail = $id_client->user_email;
// var_dump($user_mail);

$user_first_name = $id_client->first_name;
$user_last_name = $details_id->last_name;



// echo '<pre>';
// print_r($id_client);
// echo '</pre><br />';

?>
<?php  //récupère le titre de la page de WodPress

woffice_title(get_the_title()); ?>

<?
$recup_locations = $conn->query("SELECT locations.*, salles.* 
FROM locations 
INNER JOIN salles ON locations.IdSalle = salles.IdSalle 
WHERE locations.IdClient = $id_client  AND locations.DateDebut >= NOW()
ORDER BY locations.DateDebut ASC;");

// var_dump($recup_locations);

//LIEN ENTRE LES TABLES DIFFERENTES avec des id_identiques
echo '<div class="col-sm-8 col-md-8"style="margin-top:100px">';
echo '<h1> Récapitulatif de mes  réservations</h1>';
echo '<button type="button" class="btn btn-primary">
Nombre de réservations: <span class="badge badge-light">' . $recup_locations->rowCount() . '</span>
</button><hr>';


//affichage d'un tableau pour le récapitulatif des reservations de salles
echo '<table class="table table-bordered">';
//Attention, bien respecter l'ordre des colonnes de la base de données !!
echo '<tr>';
echo '<th>date/heure Réservation</th> ';
echo '<th>Nom de la réservation</th> ';
echo '<th>Reservation effectuée</th>';
echo '<th>Suppr</th>';
echo '</tr>';

while ($commande = $recup_locations->fetch(PDO::FETCH_ASSOC)) {


    echo '<tr>';
    echo '<td>' . ' Du ' . date('d-m-Y H:i:s', strtotime($commande['DateDebut'])) . ' au ' . date('d-m-Y H:i:s', strtotime($commande['DateFin'])) . '</td>';
    echo '<td>' . $commande['nom']  . ' à ' . $commande['lieu'] . '</td>';
    echo '<td>' . date('d-m-Y H:i:s', strtotime($commande['dateReservation'])) . '</td>';

    //*****************************pour modifier par la suite les réservations************


    echo '<td><a href="?action=suppression&IdLocation=' . $commande['IdLocation'] . '" class="btn btn-danger text-white" onclick="return(confirm(\'Etes-vous sûr ?\'));" ><i class="fas fa-trash-alt"></i></a></td>';

    echo '</tr>';
}
/*********suppresion d'une reservation */
if ($_GET['action'] == 'suppression') {
    $id = $_GET['IdLocation'];
    $stmt = $conn->prepare("DELETE FROM locations WHERE IdLocation = ?");
    $stmt->execute([$id]);

    //ensuite récupérer l'id de la personne connectée et lui envoyée un mail pour confirmer son annulation

}
echo '</table>';
echo '</div>';
