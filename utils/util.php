<?php
class Utils {
    public static function checkLogin($page=''){
        // $redirectLocation = '';
        // switch($page){
        //     case 'login':
        //         $redirectLocation = './pages/landing_page.php';
        //         break;
        //     case 'landing-page':
        //         $redirectLocation = '../index.php';
        //     break;

        // }
        if(!isset($_SESSION['loggedIn']) || isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == FALSE || !$_SESSION['loggedIn']){
            header('Location: ' . '../index.php');
          }
    }
    public static function formatStr($str){
        // Split string into array and then unite the words by using 1 space char between them
        return ucwords(implode(" ", explode('_',$str)));
    }
}
?>