<?php
    // Internal information
    $info = array(
        'home'       => 'index.php'
    );

    // Database
    $GLOBALS['server'] = array(
        'hostname'   => 'localhost',
        'username'   => 'root',
        'password'   => '',
        'port'       => 3306,
        'database'   => 'controlpanel'
    );

    $GLOBALS['tables'] = array(
        'admin'      => 'administrador',
        'drink_conteiner' => 'bebidas_envases',
        'drink_taste' => 'bebidas_sabores',
        'drink_final' => 'bebida_final',
        'drink_brands' => 'bebida_marca',
        'drink_size' => 'bebida_tamaño',
        'caja' => 'caja',
        'category' => 'categorias_productos',
        'food' => 'comida',
        'food_final' => 'comida_final',
        'food_size' => 'comida_tamaño',
        'food_use' => 'comida_usa',
        'employees'      => 'empleados',
        'expenses' => 'gastos',
        'expenses_products' => 'gastos_productos',
        'orders' => 'pedidos',
        'order_Products' => 'pedidos_productos',
        'soats_price' => 'precio_salsas',
        'promos_price' => 'promos_precios',
        'promos_products' => 'promos_productos',
        'soats' => 'salsa',
        'soats_use' => 'salsa_usa',
        'type_products' => 'tipo_productos'
    );


    // Open a connection to the database.
    try {
        $GLOBALS['server'] =  new PDO('mysql:host=' . $server['hostname'] . ':' . $server['port'] . ';dbname=' . $server['database'] .';charset=utf8', $server['username'], $server['password']);
    } catch (PDOException $e) {
        $secure = false; $title = 'Fatal error';
        echo '
        <html>
          <head>
            <meta charset="utf-8"/>
            <title>Control - ' . $title . '</title>
            <!--Import Google Icon Font-->
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
            <!--Import materialize.css-->
            <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"  media="screen,projection"/>

            <!--Import custom styles-->
            <link type="text/css" rel="stylesheet" href="styles/custom.css">

            <!--Let browser know website is optimized for mobile-->
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
          </head>
          <body>
            <header>
              <nav class="black">
                <div class="nav-wrapper">
                  <a href="#" class="brand-logo">Control</a>
                </div>
              </nav>
            </header>
            <main>
              <div id="vcentered_message" class="col s12">
                  <h5 class="grey-text center-align">
                      <i class="large material-icons">link_off</i> <br>
                      No fue posible conectarse con la base de datos. ¿Querés <a href="">probar otra vez</a>?
                  </h5>
              </div>';
        require_once('includes/footer.php');
        $server = null;
        error_log($e->getMessage());
        exit();
    }
