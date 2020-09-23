<?php $title = 'Cargar pedidos';
require_once('includes/header.php');
$scripts = ['cargar_pedidos']; ?>
<div class="fixed-action-btn">
  <a class="btn-floating modal-trigger btn-large pulse red" href="#order_make">
    <i class="material-icons" onclick="make_order()">add</i>
  </a>
  <a class="btn-floating modal-trigger btn-large pulse black" href="#order_make_fast">
    <i class="material-icons" onclick="make_order_fast()">add</i>
  </a>
</div>

<div class="row">
  <div class="col s5">
    <!-- Inner right -->
    <div class="row">

      <h5 class="col s12 grey-text center-align">En cola</h5>
    </div>
    <div id="queued_requests_cards_container" class="row">
      <!-- Card container -->
      <h6 id="queue_empty" class="center-align grey-text"></h6>
    </div>
  </div>

  <div class="col s2 inner"></div> <!-- Separator -->

  <div class="col s5">
    <!-- Inner right -->
    <div class="row">
      <h5 class="col s12 grey-text center-align">Finalizado</h5>
    </div>
    <div id="done_requests_cards_container" class="row">
      <!-- Card container -->
      <h6 id="done_empty" class="center-align grey-text"></h6>
    </div>
  </div>

  <div id="order_remove" class="modal modal-fixed-footer">
    <div class="modal-content">
      <h4>¿Por qué querés cancelar este pedido?</h4>
      <input type="hidden" id="order_id_cancelar" name="order_id_cancelar" required>
      <input type="text" id="cancelar_motivo" class="cancelar_motivo verify" name="cancelar_motivo" value="" pattern="[a-zA-Z]" required>
      <label for="cancelar_motivo"><i class="small material-icons">announcement</i>Ingrese el motivo:</label>
      <br>
      <div class="row">
        <input type="checkbox" id="sacar_caja" />
        <label for="sacar_caja">¿Se devolvió el dinero?</label>
      </div>
    </div>
    <div class="modal-footer">
      <a href="#!" class="btn-flat" onclick="cancelar_pedido(true)">Sí</a>
      <a href="#!" class="modal-close btn-flat">No</a>
    </div>
  </div>

  <div id="no_vino" class="modal modal-fixed-footer">
    <div class="modal-content">
      <h4>Confirmas que nadie vino a retirar el pedido</h4>
      <input type="hidden" id="order_id_no_vino" name="order_id_no_vino" value="">
    </div>
    <div class="modal-footer">
      <a href="#!" class="btn-flat" onclick="no_vino(true)">Sí</a>
      <a href="#!" class="modal-close btn-flat">No</a>
    </div>
  </div>

  <div id="order_make" class="modal modal-fixed-footer">
    <!-- Default with no input (automatically generated)  -->


    <!-- Customizable input  -->
    <div class="row">
      <br>
      <div class="row">
        <div class="right-align" id="op">
          <a href="#!" class="modal-close btn red"><i class="material-icons">clear</i></a>
        </div>
        <div class="col s12" id="total">
        </div>
        <div class="col s12">
          <div class="row">
            <div class="col s4">
              <H5>Comida</H5>
              <div id="order_food"></div>
            </div>
            <div class="col s4">
              <h5>Bebida</h5>
              <div id="order_drink"></div>
            </div>
            <div class="col s4">
              <h5>Promo</h5>
              <div id="order_promo"></div>
            </div>
          </div>

        </div>
      </div>
      <div class="row">
        <div class="col s4">
          <a href="#!" class="btn green" id="open_food"><i class="material-icons">fastfood</i></a>
        </div>
        <div class="col s4">
          <a href="#!" class="btn green" id="open_drink"><i class="material-icons">format_bold</i></a>
        </div>
        <div class="col s4">
          <a href="#!" class="btn green" id="open_promo"><i class="material-icons">local_parking</i></a>
        </div>
      </div>
      <div class="col s12" id="open">

      </div>
    </div>
  </div>

  <div id="order_make_fast" class="modal modal-fixed-footer">
    <div class="row">
      <br>
      <div class="row">
        <div class="right-align" id="op_fast">
          <a href="#!" class="modal-close btn red"><i class="material-icons">clear</i></a>
        </div>
        <br>
        <div id="order_fast"></div>
        <br>
        <div class="col s12" id="total_fast">
        </div>
        <div class="col s12">
          <div class="row">
            <div class="col s6">
              <h5 class="btn" id="Bebidas">Bebidas</h5>
            </div>
            <div class="col s6">
              <h5 class="btn" id="Salsas">Salsas</h5>
            </div>
          </div>
          <div class="col s12">
            <div id="order_op_fast"></div>
          </div>


        </div>
      </div>
    </div>
  </div>


  <div id="food_fast" class="modal modal-fixed-footer">
    <div class="row">
      <br>
      <div class="row">
        <div class="right-align" id="op_food_fast">
          <a href="#!" class="modal-close btn red"><i class="material-icons">clear</i></a>
        </div>
        <div class="col s12">
          <div class="row">
            <div class="col s12">
              <h5>Comidas</h5>
              <div id="order_food_fast"></div>
            </div>
          </div>

        </div>
      </div>
    </div>
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


  <div id="ver_mas" class="modal modal-fixed-footer">
    <div class="modal-content">
      <h4 id="pedido_titulo"></h4>
      <div id="pedido_info">
      </div>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close btn-flat">Volver</a>
    </div>
  </div>

  <!-- AGREGAR UNA SALSA -->
  <div id="add_soats" class="modal modal-fixed-footer">
    <div class="row">
      <div class="right-align" id="op_soat">
        <a href="#!" class="modal-close btn green" onclick="final_soat(true)"><i class="material-icons">check</i></a>
        <a href="#!" class="modal-close btn red" onclick="reiniciar_soat()"><i class="material-icons">clear</i></a>
      </div>
      <div class="col s12" id="totalFOOD">
      </div>
      <div class="row">
        <div class="col s12">
          SALSAS:
          <div id="order_soat"></div>
          <div class="row" id="salsas">
            <div class="col s12">
              <ul class='collapsible'>
                <li>
                  <div class="collapsible-header active orange lighten-3"></div>
                  <div class="collapsible-body" id="option_soats"></div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div id="Seguro" class="modal modal-fixed-footer">
    <div class="modal-content">
      <div class="col s12">
        <div class="row">
          Orden:
        </div>
        <div id="Detalles"></div>
        <div class="row">
          <label>
            <input class="with-gap" checked="true" name="radio" id="Pedido" type="radio" />
            <label for=Pedido>Pedido</label>
          </label>
          <label>
            <input class="with-gap" name="radio" id="Regalo" type="radio" />
            <label for=Regalo>Regalo</label>
          </label>
          <label>
            <input class="with-gap" name="radio" id="PedidosYa" type="radio" />
            <label for=PedidosYa>Pedidos Ya!</label>
          </label>
        </div>
        <div class="row">
          <input type="number" name="num" pattern="[0-9]" id="num" class="verify num" required>
          <label for=num>Ingresá el número de pedido:</label>
        </div>
        <div class="row" id="regalo">

        </div>
        <div class="row" id="productos_pedidos">

        </div>
      </div>
    </div>

    <div class="modal-footer">
      <a href="#!" class="btn-flat" onclick="finish_make_order()">Sí</a>
      <a href="#!" class="modal-close btn-flat">No</a>
    </div>
  </div>
  <?php require_once('includes/footer.php'); ?>