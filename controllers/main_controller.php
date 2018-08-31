<?php
  /*
    Controller-ul principal.

    In scopul acestui tutorial, vom defini un singur controller ce va procesa toate request-urile venite, in functie de cazul curent.

    Fluxul de lucru:
      # Main:
        - in zona "main" se verifica tipul requestului (POST sau GET) si se face switch pe baza actiunii primite;
        - Se verifica parametrii primiti:
          - Daca se incearca accesarea acestei "pagini", fara parametrii, se redirecteaza apelul catre "index.php";
        - Se va crea un switch in functie de actiunea / cazul curent.
          - Se determina cazul curent;
          - Switch-ul va apela o metoda specifica acelui caz;
          - Metoda va prelucra datele primite si va redirecta utilizatorul catre pagina destinatie.
      # Login:
          - Se verifica parametrii POST;
          - Se valideaza daca acestia sunt corecti;
            - Daca sunt incorecti, se salveaza o eroare in sesiune si se redirecteaza userul catre pagina de log-in, unde i se va afisa eroarea;
          - Se obtine un hash a parolei trimise de utilizator folosind algoritmul one-way, "MD5".
            - La parola utilizatorului se adauga un parametru de tip pepper: "_pepper" (asta trebuia sa fie optionala, dar configurile de baze de date le-am facut pe baza aplicatiei mele :P);
          - Se verifica daca username-ul si parola sunt corecte;
            - Daca datele sunt corecte, se seteaza o bifa de login si se redirectioneaza apelul catre landing page;
            - Daca nu sunt corecte, se redirectioneaza apelul catre pagina de log-in unde se va afisa o eroare generica (a nu se mentiona care este campul vinovat).
      # Edit user:
          - Se verifica parametrii POST;
          - Se valideaza daca acestia sunt corecti;
            - Daca sunt incorecti:
              - se salveaza datele trimise de catre utilizator in sesiune;
              - se salveaza erorile intampinate in sesiune;
              - se redirecteaza userul catre pagina de editare unde se vor completa datele si lista erorile;
            - Daca sunt corecte:
              - se actualizeaza baza de date cu noile date;
              - se redirecteaza utilizatorul catre pagina de editare, unde, optional, i se va afisa un mesaj de succes.
      # Logout
          - Se sterge sesiunea utilizatorului;
          - Se redirecteaza utilizatorul catre "index.php"

    Mod de lucru:
        - se definesc metode specifice fiecarui caz la inceputul acestui fisier;
        - aceste metode sunt apelate din cadrul constructiei de tip switch din zona main, in functie de cazul curent;
        - (optional) se poate folosi o clasa statica de tip helper daca se doreste acest lucru.
*/
  include_once '../../config/app_config.php';
  session_start();
// MySQL connection
  $mysqli = new mysqli($gc_mysql_ip, $gc_mysql_user, $gc_mysql_password, $gc_mysql_database);
  // foreach($password_query as $pass_hash){
  //   var_dump($pass_hash["password_hash"]);
  // }
  var_dump($mysqli);
  $conn_err = mysqli_error() ?: $mysqli->connect_error;
  if(is_null($mysqli->error) || !empty($mysqli->error) ) {
    die('Could not connect: ' . $conn_err);
  } else {
    echo 'Connected successfully<br>';
  }

  class Init {
    const USERNAME = "Leo";
    const PASSWORD = "admin";
    public static function main($request) {
      switch($request) {
        // If request is of type GET, go here
        case 'GET':
          echo 'GET request:';
          print_r($_GET);
          if(empty($_GET)){
            header('Location: ../index.php');
            exit;
          };
          // Checking if get request comes from the landing page
          if(isset($_GET['change-to-view-user'])){
            echo 'Request coming from the landing page.Go to view user!';
            //Get id param sent from the landing page
            //Sent it when redirecting to view user
            $id = $_GET['change-to-view-user']['id'];
            // header('Location: ' . '../pages/view_user.php' . '?user_id=' . $id);
            self::redirect('../pages/view_user.php?user_id=', $id);
            // echo($id);
            // $viewUserQuery = $mysqli->query("SELECT * FROM users where id =" . $id);
            // foreach($viewUserQuery as $user){
              
            // }
          } elseif(isset($_GET['change-to-edit-user'])){
            echo 'Request coming from the landing page.Go to edit user!';
            $id = $_GET['change-to-edit-user']['id'];
            self::redirect('../pages/edit_user.php?user_id=', $id);
          }
          break;
          // If request is POST go here
        case 'POST':
          echo 'POST request:';
          print_r($_POST);
          if(isset($_POST['login-request'])) {
            // Destroy "login-request"
            echo '<br>login branch<br>';
            self::login();

          }
          if(isset($_POST['logout-request'])){
            self::logout();
          }
          if(!empty($_POST['edit-values-id'])){
            self::updateUser($_POST);
          }
          break;
        default:
          echo 'handle error';
          //die(msg)
      }
    }
    private static function login() {
      echo 'Login function:' . '<br>';
      // print_r($_POST);
      global $mysqli;
      $user = self::USERNAME;
      $pass = self::PASSWORD;
      $sentUser = $_POST['username'];
      $sentPass = $_POST['password'];
      $client_hash = md5($sentPass . '_pepper');
      // Retrieve the password hash for the entered email 
      $password_query = $mysqli->query("SELECT first_name, password_hash FROM users WHERE email = " . '"' . $sentUser . '"');
      $db_hash = '';
      $db_first_name = '';

      foreach($password_query as $field){
        $db_hash = $field["password_hash"];
        $db_first_name = $field["first_name"];
      };

      echo 'Password is: ';
      echo ' ' . $db_hash;
      echo '<br>' . $client_hash;
      // Variable that will contain a specific error if wrong information is sent
      $login_error = '';
      // Branching to check each possible case for validation
      if($user == $sentUser && $pass == $sentPass || $db_hash == $client_hash) {
      // if($db_hash == $client_hash) {
        // If both values are correct then the user is redirected to landing page
        $_SESSION['loggedIn'] = TRUE;
        $_SESSION['first-conn'] = TRUE;
        //Also store the name of the user in order to greet him the first time he logs in
        $_SESSION['first-name'] = $db_first_name;
        self::redirect('../pages/landing_page.php');
        //If something goes wrong store an error msg in the session and show it to the client
      } else {
        $_SESSION['login_error'] = 'Username or password is incorrect';
        // Store the incorrect username and password in the session in order to send them back to 
        // their inputs
        $_SESSION['stored_user'] = $sentUser;
        // $_SESSION['stored_pass'] = $sentPass;
        self::redirect('../pages/login.php');
        exit;
      }
    }
    private static function logout(){
      //Destroy current session
      $_SESSION = array();
      self::redirect('../index.php');
    }
    private static function updateUser($new_info) {
      // Construct the string that will contain what comes after set in the mysql query
      set_error_handler (
        function($errno, $errstr, $errfile, $errline) {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);     
        } 
      );
      // try {
        $string_col_val = '';
        foreach($new_info as $key=>$prop){
        if($key == 'edit-values-id'){
          $key = 'id';
        }
        // Hash a new password based on the input
        if($key == 'password_hash') {
          // echo "Hash pass";
          // echo "Old email: " . $prop;
          $prop = md5($prop . '_pepper');
          // echo "New password: " . $prop;
        }
        $string_col_val .= $key . '=' . '"'. $prop . '"' . ', ';
        if (end($new_info) == $key) {
          $string_col_val .= $key . '=' . '"'. $prop . '"' . ' ';
        }
      };

      // $update_query = $mysqli->query("UPDATE user" . " SET " . "first_name = '"'ROBIN'"' " . " WHERE email = robin_jackman@mysite.com");
      // $update_query = $mysqli->query("UPDATE user SET first_name = 'sss'  WHERE email = 'robin_jackman@mysite.com'");
      // echo $update_query;
      $sql = "UPDATE users SET first_name='Doe' WHERE email='robin_jackman@mysite.com'";
      if ($mysqli->query($sql) == true) {
        echo "Record updated successfully";
     } else {
        echo "Error updating record: " . mysqli_error($conn);
     }
      echo "<br>Update function";
      // echo $string_col_val;
      // }
      // catch(Exception $e){
      //   $_SESSION['update_err'] = "Something went wrong.Please try to update again!";
      //   $_SESSION['pass_id_on_update_error'] = $new_info['edit-values-id'];
      //   echo "Something went wrong <br> " . $new_info['edit-values-id'];
      //   // self::redirect('../pages/edit_user.php');
      //   // header('Location: ../index.php');
      //   // header('Location: ../pages/edit_user.php');
      // }
    }
    private static function redirect($loc, $param=''){
      // Redirect to the desired page
      $redirectLocation = '';
      if($params == ''){
        $redirectLocation = 'Location: ' . $loc . $param;
      } else {
        $redirectLocation = 'Location: ' . $loc;
      }
      header($redirectLocation);
    }
  }
  echo 'Controller Page' . '<br>';
  Init::main($_SERVER['REQUEST_METHOD']);
  $mysqli->close();
?>