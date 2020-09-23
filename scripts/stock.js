$('document').ready(()=> {
  $.ajax({
    url: 'api.php',
    type: 'POST',
    data: {
      request: 'ProductosVendidos',
      content:[0]
    }
  })
  .done(data => {
    if (true) {
      data = JSON.parse(data);
      $('#Comidas').html(data["comida"]);
      $('#Bebidas').html(data["bebida"]);
      $('#Salsas').html(data["salsa"]);
      $('#Promos').html(data["promo"]);
    };
  })
  .fail(data => {
    ERRORFATAL()
  });
});


function ver_promo(id){
  $.ajax({
    url: 'api.php',
    type: 'POST',
    data: {
      request: 'getOnePromo',
      content:[id]
    }
  })

  .done(data => {
    if (permitido(data)) {
      $('#view_promo').modal('open');
      $('#promo_info').html(data);
    };
  })
  .fail(data => {
      ERRORFATAL()
  });
}
