<?php
require_once('config.php');
if (isset($index) && $index)
  header("Location:cargar_pedidos.php");
?>

<html>

<head>
  <meta charset="utf-8" />
  <title>Control - <?= $title; ?></title>

  <head>
    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">

    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="styles/materialize.min.css" media="screen,projection" />

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <!--Import custom styles-->
  <link type="text/css" rel="stylesheet" href="styles/custom.css">
</head>

<body>
  <header>
    <nav class="nav-extended z-depth-3">
      <div class="nav-wrapper">
        <a href="#" data-activates="mobile-demo" class="button-collapse">
          <i class="material-icons">menu</i>
        </a>
        <div class="nav-mobile hide-on-med-and-down">
          <div>
            <a href="mensajes.php" class="brand-logo center-align" id="titulo">
              Control
            </a>
          </div>
          <ul class="right">
            <li>
              <a href="caja.php" target="_self">
                Caja: $
                <span class="totalCaja"></span>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="nav-content nav-mobile hide-on-med-and-down">
        <ul class="tabs tabs-transparent">
          <li class="tab"><a target="_self" href="cargar_pedidos.php">Pedidos</a></li>
          <li class="tab"><a target="_self" href="productos.php">Productos</a></li>
          <li class="tab"><a target="_self" href="stock.php">Stock</a></li>
          <li class="tab"><a target="_self" href="caja.php">Caja</a></li>
        </ul>
      </div>
    </nav>
    <!-- Menu Mobile -->
    <ul class="side-nav" id="mobile-demo">
      <li class="tab">
        <a target="_self" href="cargar_pedidos.php">
          <i class="material-icons">shopping_cart</i>Pedidos
        </a>
      </li>
      <li class="tab">
        <a target="_self" href="productos.php">
          <i class="material-icons">person</i>Productos
        </a>
      </li>
      <li class="tab">
        <a target="_self" href="stock.php">
          <i class="material-icons">person</i>Stock
        </a>
      </li>
      <li class="tab">
        <a target="_self" href="caja.php">
          <i class="material-icons">person</i>Caja
        </a>
      </li>
      <hr>
      <li>
        <a href="caja.php" target="_self">
          Caja: $
          <span class="totalCaja"></span>
        </a>
      </li>
    </ul>
  </header>