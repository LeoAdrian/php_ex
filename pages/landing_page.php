<?php
  /*
    Landing-page-ul. Pagina unde va ajuge utilizatorul dupa un log-in cu succes.

    Fluxul de lucru:
      # PHP:
        - Se verifica daca userul este logat:
          - Daca userul nu este logat, se redirecteaza catre index.php;
        - Se verifica sesiunea daca contine date specifice acestei pagini:
          - Daca nu avem erori, datele utilizatorului se extrag din baza de date;
          - Daca apar erori, datele utilizatorului se iau din sesiune. Tot aici se defineste eroarea ce a aparut la operatia de editare.
        - Se afiseaza continutul paginii.
      # HTML:
        - Se creaza continutul paginii. Elemetele necesare sunt:
          - Un form ce va face posibila operatia de log-out. Form-ul trebuie sa contina:
            - Un input de tip "submit";
            - Un input hidden cu actiunea "logout";
          - O zona de listare a utilizatorilor din baza de date.
            - Se va afisa doar numele sau username-ul;
            - Listarea si infrumusetarea sunt la indemana fiecaruia;
          - In dreptul fiecarui utilizator sunt necesare doua butoane (sau inlucuitori) ce vor avea actiuni specifice: view_user si edit_user;
            - Ambele actiuni vor fi de tip GET;
            - Cum se trimit datele catre controller este la indemana fiecaruia;
  */
  include_once '../../config/app_config.php';
  include_once '../utils/util.php';
  session_start();
  // if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == FALSE || !$_SESSION['loggedIn']){
  //   header('Location: ' . '../index.php');
  // }
  Utils::checkLogin();

  //Check to see if the user just logged in
  $messageToBeDisplayed = '';
  if($_SESSION['loggedIn'] == TRUE && $_SESSION['first-conn'] == TRUE && isset($_SESSION['first-name'])){
    $messageToBeDisplayed = 'Welcome Back, ' . $_SESSION['first-name'] . '!';
    // Change the boolean to false in order for the welcome message to dissapear
    $_SESSION['first-conn'] = FALSE;
    // Delete first name prop from the session object
    unset($_SESSION['first-name']);
  }
  // $_SESSION['count']++;
  // if($_SESSION['count'] == 2){
  //   $_SESSION['count'] = 2;
  // };
  $mysqli = new mysqli($gc_mysql_ip, $gc_mysql_user, $gc_mysql_password, $gc_mysql_database);
  $allUsersQuery = $mysqli->query("SELECT first_name,id FROM users");
  
  //Generate the user table by appending every user to the same string
  $allUsersTable = '';
  // Go through all the props of the user returned above
  foreach($allUsersQuery as $value){
    $allUsersTable .= '<div class="user">' . '<span>' . $value['first_name'] . '</span>' .
      '<div class="buttons-container">
        <form class="view-form" method = "GET" action="../controllers/main_controller.php">
            <input type="hidden" name="change-to-view-user" value=' . '"' .$value['id'] . '"' . '>
            <i class = "fa fa-user">
              <input type="submit" value="view">
            </i>
        </form>'
        .
        '<form class="edit-form" method = "GET" action="../controllers/main_controller.php">
            <input type="hidden" name="change-to-edit-user" value=' . '"' .$value['id'] . '"' . '>
          <i class = "fa fa-edit">
            <input type="submit" value="edit">
          </i>
        </form>
      </div>'
      .
    '</div>';
  };
  $mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Landing Page</title>
  <link rel="stylesheet" href="../assets/css/landing.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
  <div class="landing">
    <div class="login-err-msg" >
        <?php
          echo $messageToBeDisplayed;
        ?>
    </div>
    <!-- <h1 class="landing-title">Landing Page</h1> -->
    <form class="logout-form" method="POST" action="../controllers/main_controller.php">
      <input type="hidden" name="logout-request">
      <input type="submit" value="Logout">
    </form>
    <div class="user-list">
      <div class="user-list-header user">Users</div>
            <?php echo $allUsersTable ?>
    </div>
  </div>
  <script>
    const errorMsg = document.querySelector('.login-err-msg');
    const formBtns = document.querySelectorAll('.buttons-container form');

    errorMsg.classList.add('login-err');
    errorMsg.style.color = 'green';
    errorMsg.style.backgroundColor = 'white';
    setTimeout(function(){
      errorMsg.style.display = 'none';
      errorMsg.classList.remove('login-err');
    },2000);
    if(!errorMsg.innerText) {
      errorMsg.style.display = 'none';
    }
    
    // Add mouseover event for every form group
    formBtns.forEach(form => {
      form.addEventListener('mouseover', function(){
        hoverFormAction(this);
      })
      form.addEventListener('mouseout', function(){
          hoverFormDestruction(this)
      })

      //Add click listener to forms in order to trigger the sending of the data
      form.addEventListener('click', function(){
        console.log(this.children[1].children[0]);
        this.children[1].children[0].click();
      })
    })

    function hoverFormAction(form) {
      if(form.classList[0] === 'view-form'){
          form.style.backgroundColor = '#56da7e';
          form.style.cursor = 'pointer';
          for(let i = 0; i < form.children.length; i++){
            if(form.children[i].children){
              let arrChild = form.children[i].children;
              for(let j = 0; j < arrChild.length; j++){
                arrChild[j].style.color = 'white';
              }
            }
            form.children[i].style.color = 'white';
          }
      } 
      else {
          form.style.backgroundColor = 'orange';
          form.style.cursor = 'pointer';
          for(let i = 0; i < form.children.length; i++){
            if(form.children[i].children){
              let arrChild = form.children[i].children;
              for(let j = 0; j < arrChild.length; j++){
                arrChild[j].style.color = 'white';
              }
            }
            form.children[i].style.color = 'white';
          }
      }
    }

    function hoverFormDestruction(form) {
      if(form.classList[0] === 'view-form'){
          form.style.backgroundColor = 'transparent';
          for(let i = 0; i < form.children.length; i++){
            if(form.children[i].children){
              let arrChild = form.children[i].children;
              for(let j = 0; j < arrChild.length; j++){
                arrChild[j].style.color = '#56da7e';
              }
            }
            form.children[i].style.color = '#56da7e';
          }
      } 
      else {
          form.style.backgroundColor = 'transparent';
          for(let i = 0; i < form.children.length; i++){
            if(form.children[i].children){
              let arrChild = form.children[i].children;
              for(let j = 0; j < arrChild.length; j++){
                arrChild[j].style.color = 'orange';
              }
            }
            form.children[i].style.color = 'orange';
          }
      }
    }
    //Change color of view/edit buttons on mouseover
    // formBtns.addEventListener('mouseover', function(){
    //   // When user is over the btn, change its background color
    //   console.log(this);
    // })
  </script>
</body>
</html>