<?php
$title   = 'Productos';
require_once('includes/header.php');
$scripts = ['products'];
?>
<div id="" class="row">
  <!-- Card container -->
  <center>
    <div class="row center-align">
      <div class="col">
        <a href="#!" class="btn green" id="show_food">Comidas</a>
      </div>
      <div class="col">
        <a href="#!" class="btn green" id="show_drink">Bebidas</a>
      </div>
      <div class="col">
        <a href="#!" class="btn green" id="show_soats">Salsas</a>
      </div>
      <div class="col">
        <a href="#!" class="btn green" id="show_promo">Promos</a>
      </div>
    </div>
  </center>
  <div id="only_foods">
    <div class="col s12">
      <!-- Inner left -->
      <div class="row">
        <h5 class="col s12 grey-text center-align">Comidas
          <div class="row">
            <div class="input-feld col s12" id="foods_filter">
            </div>
          </div>
        </h5>
      </div>
      <div id="food_scards_container" class=" row">
        <!-- Card container -->
        <h6 id="foods_empty" class="center-align grey-text">No hay comidas.</h6>
      </div>
    </div>
  </div>


  <div id="only_drinks">
    <div class="col s12">
      <!-- Inner right -->
      <div class="row">
        <h5 class="col s12 grey-text center-align">Bebidas
          <div class="row">
            <div class="input-feld col s12" id="drinks_filter">
            </div>
          </div>
        </h5>
      </div>
      <div id="drinks_cards_container" class="row">
        <!-- Card container -->
        <h6 id="drinks_empty" class="center-align grey-text">No hay bebidas.</h6>
      </div>
    </div>
  </div>


  <div id="only_soats">
    <div class="col s12">
      <!-- Inner right -->
      <div class="row">
        <h5 class="col s12 grey-text center-align">Salsas
          <div class="row">
            <div class="input-feld col s12" id="soats_filter">
            </div>
          </div>
        </h5>
      </div>
      <div id="soats_cards_container" class="row">
        <!-- Card container -->
        <h6 id="soats_empty" class="center-align grey-text">No hay salsas.</h6>
      </div>
    </div>
  </div>

  <div id="only_promos">
    <div class="col s12">
      <!-- Inner right -->
      <div class="row">
        <h5 class="col s12 grey-text center-align">Promos
          <div class="row">
            <div class="input-feld col s12" id="promos_filter">
            </div>
          </div>
        </h5>
      </div>
      <div id="promos_cards_container" class="row">
        <!-- Card container -->
        <h6 id="promos_empty" class="center-align grey-text">No hay promos.</h6>
      </div>
    </div>
  </div>


</div>
<div id="view_product" class="modal modal-fixed-footer">
  <div class="modal-content">
    <h4 id="Title"></h4>
    <div id="Descripcion">
    </div>
  </div>
  <!-- BOTONES PARA AGREGAR O CANCELAR UN PEDIDO-->
  <div class="modal-footer" id="Opciones">

  </div>
</div>
<?php require_once('includes/footer.php'); ?>