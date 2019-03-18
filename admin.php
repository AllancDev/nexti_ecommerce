<?php

use nexti\PageAdmin;
use nexti\Model\User;

    $app -> get("/admin", function(){
        User::verifyLogin();
        $page = new PageAdmin();
        $page -> setTpl("index");
        //echo $_SERVER["DOCUMENT_ROOT"] . "/_courses/hcode_php_7/projeto_final/views/";
    });

    
    $app -> get("/admin/forgot", function() {
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);

        $page -> setTpl("forgot");
    });

    $app -> post("/admin/forgot", function() {
        $foUser = User::getForgot($_POST["email"]);
        header("Location: /pj_final_hcode/admin/forgot/sent");
        exit;
    });

    $app -> get("/admin/forgot/sent", function() {
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);

        $page -> setTpl("forgot-sent");
    });

    $app -> get("/admin/forgot/reset", function() {
        $user = User::validForgotDecrypt($_GET["code"]);

        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);

        $page -> setTpl("forgot-reset", array(
            "name" => $user["desperson"],
            "code" => $_GET["code"]
        ));

    });

    $app -> post("/admin/forgot/reset", function() {
        $forgot = $user = User::validForgotDecrypt($_POST["code"]);
        User::setForgotUsed($forgot["idrecovery"]);
        $user = new User();

        $user -> get((int)$forgot["iduser"]);
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
            "cost" => 10
        ]);

        $user -> setPassword($password);
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);

        $page -> setTpl("forgot-reset-success");

    });

    $app -> get("/admin/login", function() {
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page -> setTpl("login");
    });

    $app -> post("/admin/login", function() {
        User::login($_POST["login"], $_POST["password"]);
        header("Location: /pj_final_hcode/admin");
        exit;
    });

    $app -> get("/admin/logout", function() {
        User::logout();
        header("Location: /pj_final_hcode/admin/login");
        exit;
    });


?>