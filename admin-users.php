<?php
    use nexti\PageAdmin;
    use nexti\Model\User;

    $app -> get("/admin/users", function() {
        
        User::verifyLogin();

        $search = (isset($_GET['search'])) ? $_GET['search'] : "";
        $page = (isset($_GET["page"])) ? (int) $_GET["page"] : 1;
        if($search != "") {
            $pagination = User::getPageSearch($search, $page);
        } else {
            $pagination = User::getPage($page);
        }

        $pages = [];

        for($x = 0; $x < $pagination['pages']; $x++) {
            array_push($pages, [
                'href' => "/pj_final_hcode/admin/users?" . http_build_query([
                    "page" => $x + 1,
                    "search" => $search
                ]),
                "text" => $x + 1
            ]);
        }

    
        $page = new PageAdmin();
        $page -> setTpl("users", array(
            "users" => $pagination['data'],
            "search" => $search,
            "pages" => $pages
        ));
    });

    $app -> get("/admin/users/create", function() {
        User::verifyLogin();
        $page = new PageAdmin();
        $page -> setTpl("users-create");
    });

    $app -> get("/admin/users/:iduser/delete", function($iduser) {
        User::verifyLogin();

        $User = new User();
        $User -> get((int)$iduser);
        $User -> delete();
        header("Location: /pj_final_hcode/admin/users");
        exit;

    });

    $app -> get("/admin/users/:iduser", function($iduser) {
        User::verifyLogin();

        $user = new User();
        $user -> get((int)$iduser);

        $page = new PageAdmin();
        $page -> setTpl("users-update", array(
            "user" => $user -> getValues()
        ));
    });


    $app -> post("/admin/users/create", function() {
        User::verifyLogin();
        $user = new User();
        $_POST["inadmin"] = (isset($_POST["inadmin"]))? 1 : 0;
        $_POST["despassword"] = User::getPasswordHash($_POST["despassword"]);
        $user -> setData($_POST);
        $user -> save();
        // var_dump($user);
        header("Location: /pj_final_hcode/admin/users");
        exit;
    });

    $app -> post("/admin/users/:iduser", function($iduser) {
        User::verifyLogin();
        $user = new User();

        $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

        $user -> get((int)$iduser);
        $user -> setData($_POST);
        $user -> update();

        header("Location: /pj_final_hcode/admin/users");
        exit;
    });


?>