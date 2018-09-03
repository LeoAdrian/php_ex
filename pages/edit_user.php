<?php
  /*
    Pagina de vizualizare a unui utilizator.

    Fluxul de lucru:
      # PHP-Main:
        - Se verifica daca userul este logat:
          - Daca userul nu este logat, se redirecteaza catre index.php;
        - Se verifica parametrii trimisi:
            - Aici vor exista doua seturi de parametrii:
              - Parametrii "GET" ce va defini in mod unic utilizatorul;
              - Parametrii de sesiune in caz ca au aparut erori la modificarea utilizatorului;
            - In orice moment in care apare vreo probleme, utilizatorul va fi redirectat catre landing page, fara alte explicatii;
        - Se verifica daca exista erori in sesiune:
          - Daca nu exista erori:
            - se extrag informatiile din baza de date pentru a putea fi afisate in continut;
            - se trece mai departe sau se completeaza erorile cu null / '';
          - Daca exista erori:
            - se completeaza input-urile cu valorile introduse de catre utilizator;
            - se completeaza erorile salvate in sesiune cu scopul de a fi afisate;
        - Se afiseaza continutul paginii;
      # HTML:
        - Se creaza continutul paginii. Elemetele necesare sunt:
          - O zona de listare a utilizatorilor din baza de date. Listarea si infrumusetarea sunt la indemana fiecaruia;
            - Listarea se va face in cadrul unui form:
                - Formul va avea o actiune de tip "POST";
            - Fiecare element va fi listat in cadrul unui input specific;
            - Elementele input-urilor vor fi auto-completate in felul urmator:
              - Din baza de date, daca este prima data cand se intra pe pagina sau in cazul in care nu avem eroare;
              - Din sesiune daca au aparut erori;
            - Se vor afisa cat mai multe informatii;
          - O zona de erori in care se vor detalia erorile salvate in sesiune;
  */
  include_once '../utils/util.php';
  include_once '../../config/app_config.php';
  session_start();
  // set_error_handler (
  //   function($errno, $errstr, $errfile, $errline) {
  //       throw new ErrorException($errstr, $errno, 0, $errfile, $errline);     
  //   } 
  // ); 
  try {
    Utils::checkLogin();
    // var_dump($_SESSION);
    // echo gettype($_GET["user_id"]);
    // echo '<br>';
    // echo empty($_GET);
    $userID = $_GET["user_id"] ?: $_SESSION['pass_id_on_update_error'];
    // Message to be desplayed if something goes wrong
    $update_err_msg = $_SESSION['update_err'] ?: '';
    if($_SESSION){
      unset($_SESSION['update_err']);
    // If user gets error and its id stored in the session is assigned, remove it from the collection 
    }
    $mysqli = new mysqli($gc_mysql_ip, $gc_mysql_user, $gc_mysql_password, $gc_mysql_database);
    $edit_form_content = '';
    $edit_user_query = $mysqli->query("
                                      SELECT id, 
                                      first_name,
                                      last_name, 
                                      email, 
                                      password_hash, 
                                      phone_number, 
                                      job_title 
                                      FROM users 
                                      WHERE id = " . $userID);
    foreach($edit_user_query as $obj) {
      foreach($obj as $key=>$value){
        // echo $key . '<br>';
        // echo $key == 'phone_number' ?: 'None' . '<br>';
        // echo '<br>';
        if($key == 'id') {
          $edit_form_content .= '<input type="hidden" name="edit-values-id" value="'. $value .'">';
          continue;
        }
        if($key == 'password_hash') {
          $edit_form_content .= 
          '<div class = "edit-form-unit">' 
            .
            '<label for=' . $key . '>'. Utils::formatStr($key) .'</label>'
            .
            '<input required type="password" placeholder="Insert new password" . name = ' . $key . ' >'
            .
          '</div>';
          continue;
        }
        if($key == 'phone_number') {
          $formated_tel = preg_replace('/(\d{4})(\d{3})(\d{3})/', '${1}-${2}-${3}', $value);
          $edit_form_content .= 
          '<div class = "edit-form-unit">' 
            .
            '<label for=' . $key . '>'. Utils::formatStr($key) .'</label>'
            .
            '<input required type="tel" pattern="[0-9]{4}-[0-9]{3}-[0-9]{3}" ' . 'value= ' . $formated_tel . ' name = ' . $key . ' >'
            .
          '</div>';
          continue;
        }
        $edit_form_content .= 
          '<div class = "edit-form-unit">'
            .
            '<label for=' . $key . '>'. Utils::formatStr($key) .'</label>'
            .
            '<input type="text" value = ' . '"' . $value . '"' . ' name = ' . $key . '>'
            .
          '</div>';

      } 
    }
  } 
  catch(Exception $e){
    echo "Something went wrong  : " . $e->getMessage();
  }

?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit User</title>
  <link rel="stylesheet" href="../assets/css/landing.css">
</head>
<body>
    <div class="login-err update-msg">
      <?php
        echo $update_err_msg;
      ?>
    </div>
    <!-- <h1>Edit User</h1> -->
    <div class="edit-space">
      <img src="https://www.zipformplus.com/css/images/UserProfileGray.fw.jpg" alt="user image">
      <form method="POST" action="../controllers/main_controller.php" class="edit-form">
        <?php 
          echo $edit_form_content;
        ?>
        <div class="edit-form-btns">
          <input type="submit" value="Save Changes">
          <input type="reset" value="Reset Changes">
        </div>
      </form>
    </div>
  <script>
    const update_err_block = document.querySelector('.update-msg');
    console.log(update_err_block);
    if(!update_err_block.innerText){
      update_err_block.style.display = 'none';
    }
    // Hide error message after 2 seconds
    setTimeout(() => update_err_block.style.display = 'none', 2000);
  </script>
</body>
</html>