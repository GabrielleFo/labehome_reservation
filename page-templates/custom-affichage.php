<?php

/*

Template Name: affichage des salles 

*/
get_header();
?>
<?
//connexion avec PDO


try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


//déclaration des variables

$debut = $_GET['start_datetime'];
$depart = $_GET['end_datetime'];

$dated = date('Y-m-d H:i:s', strtotime($debut));
$datef = date('Y-m-d H:i:s', strtotime($depart));

//********************verifier si les dates sont bonnes en comparaison avec la date d'aujourdhui et le formulaire du client  AVEC UNE CONDITION ,
//************************** */ rediriger et affichage message d'erreur 
// quand au formulaire à bloqué sur 30 min , voir dans JQUERY

$datedebut =  date('d-m-Y H:i:s', strtotime($dated));
$datefin =  date('d-m-Y H:i:s', strtotime($datef));

//pour récuperer l'id du client connectée 
$id_client = get_current_user_id();
// var_dump($id_client);

//récupération de toutes les champs de  l'user 
$details_id =  wp_get_current_user();

//selectionner que les champs désirées
$user_mail = $details_id->user_email;
// var_dump($user_mail);

$user_first_name = $details_id->first_name;
$user_last_name = $details_id->last_name;

// var_dump($user_first_name, $user_last_name);



// if (!empty($_POST)) {
//     //
//     // Debug
//     //
//     echo '<pre>';
//     print_r($_POST);
//     echo '</pre><br />';
// }

?>

<?php  //GET THEME HEADER CONTENT

woffice_title(get_the_title()); ?>

<div class="col-sm-4 m-3 ">
    <div class="card p-3">




        <form action="" method="GET" id="mx-auto">



            <legend class="">Jour et heure de réservation</legend>
            <div class="">
                <label for="start_datetime" class="">Début</label>

                <!-- ce min ne fonctionne pas min="<?php echo date('Y-m-d\TH:i:s'); ?> -->
                <!-- Controle en back avant d'essayer de faire le front  -->

                <input type="datetime-local" class="" name="start_datetime" id="start_datetime">
            </div>
            <div class="">
                <label for="end_datetime" class="">Fin</label>
                <input type="datetime-local" class="" name="end_datetime" id="end_datetime">
            </div>
            <div class="" style=display:flex>
                <input class="" type="submit" value="rechercher" name="submit">
            </div>
        </form>

    </div>
</div>

<?

if ($_GET['submit']) {


    //heure de reservation
    if (isset($_GET['start_datetime']) && isset($_GET['end_datetime'])) {   // si on a reçu une valeur du formulaire 
?>

        <div class="col-sm-4 col-md-12 ">
            <div class="row">

                <?php

                // var_dump($dated);

                // $heure = $conn->query("SELECT DISTINCT *
                //  FROM salles 
                //  LEFT JOIN locations ON salles.IdSalle = locations.IdSalle
                //   WHERE DateDebut < '$dated'
                //    AND DateFin < '$datef' OR DateDebut > '$dated' 
                //    AND DateFin > '$datef'
                //     AND DateDebut NOT BETWEEN '$dated' AND '$datef' 
                //     AND DateFin NOT BETWEEN '$dated' AND '$datef' OR DateDebut IS NULL AND  DateFin IS NULL");


                // $heure = $conn->query("SELECT  s.*
                // FROM salles s
                // LEFT JOIN locations l ON s.Idsalle = l.Idsalle

                // WHERE (l.DateDebut IS NULL OR l.DateFin < '$dated' OR l.DateDebut > '$datef')
                // ");

                $heure = $conn->query("SELECT * FROM salles
                WHERE Idsalle NOT IN (
                SELECT Idsalle FROM locations
                WHERE DateDebut <= '$datef' AND DateFin >= '$dated'
                );");


                // echo '<pre>';
                // print_r($heure);
                // echo '</pre>';


                while ($ligne = $heure->fetch(PDO::FETCH_ASSOC)) {
                    // echo '<pre>';
                    // print_r($ligne);
                    // echo '</pre>';

                ?>
                    <div class="col-sm-4 mb-3">
                        <div class="card">

                            <div class="card-body">
                                <h5 class="card-title"><?php echo $ligne['nom'] ?></h5>

                                <p>lieu : <?= $ligne['lieu'] ?></p>
                                <p>Date/heure demandée : du <?= $datedebut ?> au <?= $datefin ?></p>



                                <!--bouton reserver-->
                                <!-- <form action="/wp-content/themes/woffice-child-theme/page-templates/reserver-salle.php" method="POST"> -->
                                <form action="" method="POST">


                                    <input type="submit" name="reserveButton" value="Réserver">
                                    <input type="hidden" name="Id" value="<?php echo $id_client; ?>">
                                    <input type="hidden" name="productId" value="<?php echo $ligne['IdSalle']; ?>">
                                    <input type="hidden" name="start_hour" value="<?php echo $dated; ?>">
                                    <input type="hidden" name="end_hour" value="<?php echo $datef ?>">
                                    <input type="hidden" name="name_room" value="<?php echo $ligne['nom'] ?>">
                                    <input type="hidden" name="city_room" value="<?php echo $ligne['lieu'] ?>">

                                </form>

                            </div>
                        </div>
                    </div>
                    <!-- faire une autre requete pour afficher les salles prises avec le nom en comparant le nom de la salle sois avec la date de debut et la date de fin  -->
        <?
                }
            }
        }
        $valider = $_POST['reserveButton'];


        if (isset($valider)) {

            $id_client = $_POST['Id'];

            $id_product = $_POST['productId'];

            $start_hour = $_POST['start_hour'];


            $end_hour = $_POST['end_hour'];

            $name_room = $_POST['name_room'];

            $city_room = $_POST['city_room'];


            //******************* */ SELECT pour verifier si la salle demandée est encore disponible avec une condition ensuite 
            //***************************rediriger si salle deja prise ou message d'erreur  sinon direction le insert 




            $sth = $conn->prepare("INSERT INTO locations(IdClient,DateDebut,DateFin,IdSalle)
VALUES (:IdClient,:DateDebut,:DateFin,:IdSalle)");


            $sth->bindValue(':IdClient', $id_client);
            $sth->bindValue(':DateDebut', $start_hour, PDO::PARAM_STR);
            $sth->bindValue(':DateFin', $end_hour, PDO::PARAM_STR);
            $sth->bindValue(':IdSalle', $id_product, PDO::PARAM_STR);
            $sth->execute();
            echo "Votre réservation éffectuée, vous recevez un mail de confirmation ";


            /*************************VOIR ICI LA FONCTION DE MAIL WORDPRESS wp-mail */
            $to = $user_mail;
            var_dump($to);
            $subject = 'Confirmation de réservation';
            $message = "Bonjour  $user_first_name $user_last_name ,<br><br> Nous avons bien reçu votre demande réservation pour la salle $name_room à $city_room pour la periode $datedebut au  $datefin .<br><br>Cordialement,<br><br><br>L'équipe réservation de votre entreprise";
            $headers = 'From: fouix.gabrielle@gmail.com';
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";

            mail($to, $subject, $message, $headers);
            //redirection sur la page de mes reservations
        }


        // echo '<pre>';
        // print_r($_GET);
        // echo '</pre>';
        // echo '<pre>';
        // print_r($_POST,);
        // echo '</pre>';

        ?>





        <?php
        get_footer();
