<?php
$title   = 'Stock';
$secure = true;
require_once('includes/header.php');
$scripts = ['stock'];
?>
<div class="col s12">
  <h4>Comidas</h4>
  <div id="Comidas">
  </div>
  <h4>Bebidas</h4>
  <div id="Bebidas">
  </div>
  <h4>Salsas</h4>
  <div id="Salsas">
  </div>
  <h4>Promos</h4>
  <div id="Promos"></div>
</div>


<div id="view_promo" class="modal modal-fixed-footer">
  <div class="modal-content">
    <h4 id="promo_titulo">La promo incluye:</h4>
    <div id="promo_info">

    </div>
    <input type="hidden" name="promo_id" id="promo_id" class="promo_id" value="">
  </div>
  <div class="modal-footer">
    <a href="#!" class="modal-close btn-flat">Volver</a>
  </div>
</div>
<?php require_once('includes/footer.php'); ?>