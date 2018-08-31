<?php
  /*
    Pagina de index. Prima pagina ce este apelata la accesarea aplicatiei noastre.

    Fluxul de lucru:
      - Se verifica daca userul este logat:
        - Daca userul este logat, se redirecteaza catre landing-page.
        - Daca userul nu este logat, se redirecteaza catre pagina de log-in.
  */
  //Start session
  session_start();
  // Create a variable that will store the page to which the user
  // will be redirected
  $redirect = '';
  // Check to see if the loggedIn prop exists and if it is true
  // If both conditions are met, redirect user to landing page
  // If not, redirect user to login page
  if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == TRUE) {
    $redirect = './pages/landing_page.php';
  } else {
    $redirect = './pages/login.php';  
  };
  // Function that changes the page location based on the path we give it
  header('Location: ' . $redirect);
?>