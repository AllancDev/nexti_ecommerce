<?php
    use \nexti\Page;
    use \nexti\Model\Product;
    use \nexti\Model\Category;
    use \nexti\Model\Cart;
    use \nexti\Model\Address;
    use \nexti\Model\User;


    $app -> get("/", function(){
        $products = Product::listAll();
        $page = new Page();
        $page->setTpl("index", [
            'products'=>Product::checkList($products)
        ]);
        //echo $_SERVER["DOCUMENT_ROOT"] . "/_courses/hcode_php_7/projeto_final/views/";
    });

    $app -> get("/categories/:idcategory", function($idcategory) {
        $page = (isset($_GET['page'])) ? (int)$_GET["page"] : 1;
        $category = new Category();
        $category -> get((int)$idcategory);
        $pagination = $category -> getProductsPage($page);
        $pages = [];

        for($i = 1; $i <= $pagination["pages"]; $i++) {
            array_push($pages, [
                'link'=>'/pj_final_hcode/categories/'.$category->getidcategory().'?page='.$i,
                "page" => $i
            ]);
        }

        $page = new Page();

        $page -> setTpl("category", [
            "category" => $category -> getValues(),
            "products" => $pagination["data"],
            "pages" => $pages
        ]); 
    });

    $app -> get("/products/:desurl", function($desurl) {
        $product = new Product();
        $product -> getFromURL($desurl);
        $page = new Page();
        $page -> setTpl("product-detail", [
            "product" => $product -> getValues(),
            "categories" => $product -> getCategories()
        ]);
    });

    $app -> get("/cart", function() {
        $cart = Cart::getFromSession();
        $page = new Page();

        $page -> setTpl("cart", [
            "cart" => $cart -> getValues(),
            "products" => $cart -> getProducts(),
            "error" => Cart::getMsgError()
        ]);
    });

    $app -> get("/cart/:idproduct/add", function($idproduct) {
        $product = new Product();
        $product -> get((int)$idproduct);
        $cart = Cart::getFromSession();

        $qtd = (isset($_GET["qtd"])) ? (int)$_GET["qtd"] : 1;

        for($i = 0; $i < $qtd; $i++) {
            $cart -> addProduct($product);
        }

        header("Location: /pj_final_hcode/cart");
        exit;
    });

    $app -> get("/cart/:idproduct/minus", function($idproduct){ 
        $product = new Product();
        $product -> get((int)$idproduct);
        $cart = Cart::getFromSession();
        $cart -> removeProduct($product);
        header("Location: /pj_final_hcode/cart");
        exit;
    });

    $app -> get("/cart/:idproduct/remove", function($idproduct) {
        $product = new Product();
        $product -> get((int)$idproduct);
        $cart = Cart::getFromSession();
        $cart -> removeProduct($product, true);
        header("Location: /pj_final_hcode/cart");
        exit;
    });

    $app -> post("/cart/freight", function() {
        $cart = Cart::getFromSession();
        $cart -> setFreight($_POST["zipcode"]);

        header("Location: /pj_final_hcode/cart");
        exit;
    });

    $app -> get("/checkout", function() {
        User::verifyLogin(false);
        $cart = Cart::getFromSession();
        $page = new Page();

        $page -> setTpl("checkout", [
            'cart' => $cart -> getValues(),
            'address' => []
        ]);
    });

    $app -> get("/login", function() {
        $page = new Page();
        $page -> setTpl("login", [
            "error" => User::getError(),
            "errorRegister" => User::getErrorRegister(),
            "registerValues" => (isset($_SESSION["registerValues"])) ? $_SESSION["registerValues"] : ["name" => "", "email" => "", "phone" => ""]
        ]);
    });

    $app -> post("/login", function() {
        try{
            User::login($_POST["login"], $_POST["password"]);
        } catch(Exception $e) {
            User::setError($e -> getMessage());
        }

        header("Location: /pj_final_hcode/checkout");
        exit;
    });

    $app -> get("/logout", function() {
        User::logout();
        header("Location: /pj_final_hcode/login");
        exit;
    });


    $app -> post("/register", function() {

        $_SESSION["registerValues"] = $_POST;
        $_SESSION['registerValues']['password'] = "";

        if(!isset($_POST["name"]) ||
            $_POST["name"] == ""    
        ) {
            User::setErrorRegister("Preencha o seu nome!");
            header("Location: /pj_final_hcode/login");
            exit;
        }

        if(!isset($_POST["email"]) ||
            $_POST["email"] == ""
        ) {
            User::setErrorRegister("Preencha o seu email!");
            header("Location: /pj_final_hcode/login");
            exit;

        }

        if(!isset($_POST["password"]) ||
            $_POST["password"] == ""
        ) {
            User::setErrorRegister("Preencha sua senha!");
            header("Location: /pj_final_hcode/login");
            exit;
        }

        if(User::checkLoginExist($_POST["email"]) === true) {
            User::setErrorRegister("Este endereço de email já está sendo utilizado por outro usuário.");
            header("Location: /pj_final_hcode/login");
            exit;
        }


        $user = new User();
        $user -> setData([
            "inadmin" => 0,
            "deslogin" => $_POST["email"],
            'desperson' => $_POST["name"],
            "desemail" => $_POST["email"],
            "despassword" => User::getPasswordHash($_POST["password"]),
            "nrphone" => $_POST["phone"],
            "inadmin" => 0
        ]);


        $user -> save();

        try {
            User::login($_POST["email"], $_POST["password"]);
        } catch(Exception $e) {
            User::setError($e -> getMessage());
            header("Location: /pj_final_hcode/login");
            exit;
        }


        $_SESSION["registerValues"] = ["name" => '', "email" => "", "phone" => ""];

        header("Location: /pj_final_hcode/checkout");
        exit;
    });


?>