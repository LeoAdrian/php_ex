<?php
  /*
    Pagina de vizualizare a unui utilizator.

    Fluxul de lucru:
      # PHP:
        - Se verifica daca userul este logat:
          - Daca userul nu este logat, se redirecteaza catre index.php;
        - Se verifica parametrii trimisi pe metoda "GET;
        - Se extrag informatiile din baza de date pentru a putea fi afisate in continut;
        - In orice moment in care apare vreo probleme, utilizatorul va fi redirectat catre landing page, fara alte explicatii;
        - Se afiseaza continutul paginii;
      # HTML:
        - Se creaza continutul paginii. Elemetele necesare sunt:
          - O zona de listare a utilizatorilor din baza de date. Listarea si infrumusetarea sunt la indemana fiecaruia.
          - Se vor afisa cat mai multe informatii.
  */
  //Start session
  session_start();
  // Include utility class to use checkLogin in order to verify if the user is logged in
  include_once '../utils/util.php';
  //Include MYSQL config
  include_once '../../config/app_config.php';

  Utils::checkLogin();
  set_error_handler (
    function($errno, $errstr, $errfile, $errline) {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);     
    } 
  );
  $user_details_table = '';
  // $p_hash = '';
  try {
  $mysqli = new mysqli($gc_mysql_ip, $gc_mysql_user, $gc_mysql_password, $gc_mysql_database);
  // Get user id from the url query
  $userID = $_GET['user_id'];
  // Query the DB for user with the matching ID
  $user_details = $mysqli->query("SELECT * FROM users WHERE id = " . $userID);
  // var_dump($user_details);
  // print_r($user_details);
  // foreach($user_details as $detail){
  //   var_dump($detail);
  //   foreach($detail as $key=>$prop){
  //     echo '<strong>' . $key . '</strong>' . ':  ';
  //     echo $prop;
  //     echo '<br>';
  //   }

  // }

  foreach($user_details as $detail){
    foreach($detail as $key=>$prop){
      // echo '<strong>' . $key . '</strong>' . ':  ';
      // echo $prop;
      // echo '<br>';
      $user_details_table .= '<div class="user">'
      .
      '<span class="left-side-details">' . ucfirst($key) . '</span>'
      .
      '<span class="right-side-details">' . $prop . '</span>'
      .
      '</div>';
      // if($key == 'password_hash'){
      //   $p_hash = $prop;
      // }
    }
  }
  // echo $p_hash;
  // // $salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
  // echo '<br>';
  // // echo $salt;
  // echo '<br>';
  // echo md5('test_pepper');
  $mysqli->close();
}

catch(Exception $e) {
    echo 'Message: ' .$e->getMessage();
    // header('Location: ../index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>View User</title>
  <link rel="stylesheet" href="../assets/css/landing.css">
  <!-- <link rel="stylesheet" href="../assets/css/view.css"> -->
</head>
<body>
  <!-- <h1>View user</h1> -->
  <!-- <br>
  <br>
  <br> -->
  <div class="view-user-container">
    <div class="user-list-header user">User Details</div>
    <?php
      echo $user_details_table;
    ?>
    <div class="line"></div>
    </div>
</body>
</html>