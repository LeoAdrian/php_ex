<?php
  /*
    Pagina de log-in.

    Fluxul de lucru:
      # PHP:
        - Se verifica daca userul este logat:
          - Daca userul este logat, se redirecteaza catre landing-page.
          - Daca userul nu este logat:
            - Se verifica daca exista erori in sesiune:
              - Daca nu exista se trece mai departe sau se completeaza erorile cu null / '';
              - Daca exista erori, se completeaza username-ul si erorile respective;
            - Se afiseaza continutul paginii de log-in.
      # HTML:
        - Se creaza continutul unei pagini de tip log-in. Elemetele necesare sunt:
          - Un form care sa faca un apel de tip "POST" catre controller-ul principal;
          - Trei input-uri:
              - unul de tip email;
              - unul de tip password;
              - un buton cu actiunea "login";
          - (optional) stilizare CSS si / sau JS;
          - (optional) validari client-side de tip HTML5 sau JS.
  */
  session_start();
  // include_once '../utils/util.php';
  // Utils::checkLogin();

  if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == TRUE) {
    $redirect = './pages/landing_page.php';
  } 
  
  $login_err = '';
  $wrongUser = $_SESSION['stored_user'];
  if(!empty($_SESSION['login_error'])){
    $login_err = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
  }
  // else {
  //   // if(isset($_SESSION['error']) && $_SESSION['error']){

  //   // }
  //   // header('Location: ../controllers/main_controller.php');
  // };
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Login Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="login-err">
      <?php
        echo $login_err;
      ?>
      <button class='error-btn'>Ok</button>
  </div>
  <div class="login-content">
    <h1>Login Page</h1>
    <form class="login-form" method = "POST" action="../controllers/main_controller.php">
      <div class="input-el">
        <label for="username">Username</label>
        <input type="text" name="username" value=<?php echo $wrongUser?>>
      </div>
      <div class="input-el">
        <label for="username">Password</label>
        <input type="password" name="password">
        <input type="hidden" name="login-request">
      </div>
      <input type="submit">
    </form>
  </div>
  <script>
    const errorMsg = document.querySelector('.login-err');
    const errorMsgBtn = document.querySelector('.login-err button');
    if(errorMsg.innerText === 'Ok'){
      errorMsg.style.display = 'none';
    }else {
      errorMsg.style.display = 'flex';
    }

    if (performance.navigation.type == 1) {
      errorMsg.style.display = 'none';
    }

    setTimeout(() => {
      errorMsg.style.display = 'none';
    },4000)

    // Add event to button inside error msg, that will hide the error
    errorMsgBtn.addEventListener('click', function(){
      // this.parentNode.style.opacity = '0';
        errorMsg.style.display = 'none';
    })

  </script>
</body>
</html>