<?php
ini_set('max_execution_time', 300);
    use \nexti\Model\User;


    function formatPrice($vlprice) {
        if(!$vlprice > 0) $vlprice = 0;
        return number_format($vlprice, 2, ",", ".");
    }

    function checkLogin($inadmin = true) {
        return User::checkLogin($inadmin);
    }

    function getUserName() {
        $user = User::getFromSession();
        return $user -> getdesperson();
    }

?>