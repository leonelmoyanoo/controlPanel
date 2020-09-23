<?php
$title   = 'Caja';
require_once('includes/header.php');
?>
<div class="row center-align">
  <label>
    <input class="with-gap" checked="true" name="radio" id="Gastos" type="radio" onclick="Todo()" />
    <label for=Gastos>Gastos</label>
  </label>
  <label>
    <input class="with-gap" name="radio" id="Ventas" type="radio" onclick="Todo()" />
    <label for=Ventas>Ventas</label>
  </label>

</div>


<div class="fixed-action-btn">
  <a class="btn-floating modal-trigger btn-large pulse red" href="#addGasto">
    <i class="material-icons" onclick="addGasto()">add</i>
  </a>
</div>

<div class="col s12 flow-text">
  <!-- Inner left -->
  <span id="total"></span>
  <table id="mostrar" class='col s12 centered stripped bordered'>

  </table>

</div>

<div id="addGasto" class="modal modal-fixed-footer">
  <div class="modal-content">
    <h4>En qué se gastó?</h4>
    <div class="row">
      <div class="col s12">
        <p class="flow-text">Total: $<span class="totalGasto" id="totalGasto">0</span></p>
      </div>
    </div>
    <div id="agregarProductoGasto"></div>
  </div>
  <!-- BOTONES PARA AGREGAR O CANCELAR UN PEDIDO-->
  <div class="modal-footer">
    <button class="btn-flat" onclick="addProductExpenses()">Cargar</button>
    <button class="modal-close btn-flat" onclick="cancelar_gastos()">Cancelar</button>
  </div>
</div>


<div id="Gasto" class="modal modal-fixed-footer">
  <div class="modal-content">
    <div class="row">
      <div class="col s5 offset-s7">
        <p class="flow-text">Productos: $<span class="totalProductos"></span></p>
        <p class="flow-text">Total: $<span class="totalGastosProductos"></span></p>
      </div>
    </div>
    <div id="Gastos_Detallados">

    </div>
  </div>
  <!-- BOTONES PARA AGREGAR O CANCELAR UN PEDIDO-->
  <div class="modal-footer">
    <button class="modal-close btn-flat">Cerrar</button>
  </div>
</div>

<div id="ver_pedido" class="modal modal-fixed-footer">
  <div class="modal-content">
    <h4 id="pedido_titulo"></h4>
    <div id="pedido_info">
    </div>
  </div>
  <div class="modal-footer">
    <a href="#!" class="modal-close btn-flat">Volver</a>
  </div>
</div>
<?php require_once('includes/footer.php'); ?>