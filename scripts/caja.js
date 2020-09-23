var agregando = false;
var gasto;
var totalG = 0;
var totalP = 0;
var gastos = 'NINGÚN GASTO EFECTUADO';
var ventas = 'NINGUNA VENTA REALIZADA';
var vector = {};
$('document').ready(()=> {
  getCaja();
  $('.modal').modal();
});

function getCaja(){
  $.ajax({
    url: 'api.php',
    type: 'POST',
    data: {
      request: 'getCaja'
    }
  })
  .done(dataO => {
    if (permitido(dataO)) {
      data = JSON.parse(dataO);
      $('.totalCaja').html(data[0]["Total"]);
    };
  })
  .fail(data => {
      ERRORFATAL()
  });
}

function getAllCaja(){
  $.ajax({
    url: 'api.php',
    type: 'POST',
    data: {
      request: 'getCaja'
    }
  })
  .done(data => {
    if (parseInt(data)!=1 || parseInt(data)!=2) {
        data = JSON.parse(data);
        $('.totalCaja').html(data[0]["Total"]);
    };
  })
  .fail(data => {
      ERRORFATAL()
  });
}

function addGasto() {
  let bandera = true;
  let input = '';
  if (!agregando){
    $('#totalGasto').html(0);
    gasto = 0;
    agregando = true;
  }else{
    if ($('#producto_'+gasto).val().length == 0 || $('#cantidad_'+gasto).val().length == 0 || $('#precio_'+gasto).val().length == 0) {
      Materialize.toast('No completaste el gasto', 3000);
      bandera=false;
    }else{
      vector[gasto] = true;
      input = '<a class="btn red lighten-1" onclick="remove_gasto('+gasto+')">Borrar</a>';
      $('#agregarOtro_'+gasto).html(input);
      gasto++;
    }
  }
  if (bandera) {
    $.ajax({
      url: 'api.php',
      type: 'POST',
      data: {
        request: 'getExpensesProducts'
      }
    })
    .done(dataO => {
      if (permitido(dataO)) {
        let data = JSON.parse(dataO);
        input = '<div class="col s12" id="gasto_'+gasto+'">';
          input += '<input type="text" name="producto" id="producto_'+gasto+'" class="autocomplete input" autocomplete="off">';
          input += '<label for="producto">Producto comprado</label>';
          input += '<div class="row">';
            input += '<div class="col s6">';
              input += '<input type="number" name="precio" id="precio_'+gasto+'" pattern="[0-9]" onkeyup="changeTotal('+gasto+')" min-length="0.00">';
              input += '<label for="precio">Precio unitario</label>';
            input += '</div>';
            input += '<div class="col s6">';
              input += '<input type="number" name="canitdad" id="cantidad_'+gasto+'" pattern="[0-9]" onkeyup="changeTotal('+gasto+')"  min-length="0">';
              input += '<label for="cantidad">Cantidad</label>';
            input += '</div>';
          input += '</div>';
          input += '<div class="center-align">';
            input += '<div class="row">$<span id="total_'+gasto+'">0</span></div>';
          input += '</div>';
          input += '<div class="center-align" id="agregarOtro_'+gasto+'">';
            input += '<div class="row"><a class="btn" onclick="addGasto()">Agregar Otro</a></div>';
          input += '</div>';
        input += '</div>';
        $('#agregarProductoGasto').append(input);
        let autocomplete= {};
        data.forEach(item => {
          autocomplete[item["Producto"]] = null;
        });
        $('input.autocomplete').autocomplete({
          data: autocomplete,
          limit: 20, 
          minLength: 1, 
        });
      };
    })
    .fail(data => {
      ERRORFATAL()
    });
  }
}
function changeTotal(id){
  if ($('#precio_'+id).val().length>=1 || $('#cantidad_'+id).val().length>=1) {
    $('#totalGasto').html(parseInt($('#totalGasto').html())-parseInt($('#total_'+id).html()));
    let aux = $('#precio_'+id).val()*$('#cantidad_'+id).val();
    $('#total_'+id).html(aux);
    $('#totalGasto').html(parseInt($('#totalGasto').html())+parseInt(aux));
  }else{
    $('#totalGasto').html(parseInt($('#totalGasto').html())-parseInt($('#total_'+id).html()));
    $('#total_'+id).html(0);
  }
}
function remove_gasto(id){
  $("#gasto_"+id).fadeOut();
  $('#totalGasto').html(parseInt($('#totalGasto').html())-parseInt($('#total_'+id).html()));
  Materialize.toast('Gasto cancelado <button class="btn-flat toast-action" onclick="deshacer_gasto('+id+')">Deshacer</button>', 5000);
  vector[id] = false;
}

function deshacer_gasto(id){
  if (!vector[id]) {
    $("#gasto_"+id).fadeIn();
    $('#totalGasto').html(parseInt($('#totalGasto').html())+parseInt($('#total_'+id).html()));
    vector[id] = true;
  };
}

function cancelar_gastos(){
  totalGasto = 0;
  $('#agregarProductoGasto').html("");
  agregando = false;
}

function addProductExpenses(){
  let producto;
  let precio;
  let cantidad;
  let productosV = {};
  let preciosV = {};
  let cantidadesV = {};
  let bandera = true;
  for (let i=0;i<gasto; i++) {
    if (vector[i]) {
      producto = $('#producto_'+i).val();
      precio = $('#precio_'+i).val();
      cantidad = $('#cantidad_'+i).val();
      if (producto.length<1 || precio.length<1 || cantidad.length<1) {
        Materialize.toast('Hay gastos incompletos, cancelalos o completalos', 3000);
        bandera = false;
        break;
      }else{
        productosV[i] = producto;
        preciosV[i] = precio;
        cantidadesV[i] = cantidad;
      }
    }
  }
  if (bandera) {
    producto = $('#producto_'+gasto).val();
    precio = $('#precio_'+gasto).val();
    cantidad = $('#cantidad_'+gasto).val();
    if (producto.length>=1 || precio.length>=1 || cantidad.length>=1) {
      productosV[gasto] = producto;
      preciosV[gasto] = precio;
      cantidadesV[gasto] = cantidad;
    }
    $.ajax({
      url: 'api.php',
      type: 'POST',
      data: {
        request: 'addExpenses',
        content: [productosV,preciosV,cantidadesV]
      }
    })
    .done(data => {
      if (permitido(data)) {
        if (data==0) {
          $.ajax({
            url: 'api.php',
            type: 'POST',
            data: {
              request: 'sendAlertCaja',
              content:[3,$('#totalGasto').html()]
            }
          })
          .done(function (data1) {
            permitido(data1);
          });
        }else{Materialize.toast('ERROR.',2000 );}
      };
      getCaja();
      getAllCaja();
      $('#addGasto').modal('close');
    })
    .fail(data => {
        ERRORFATAL()
    });
  };
}

function Todo(){
  let donde;
  if ($('#Todos').prop('checked'))
      donde = 1;
  else if ($('#Ventas').prop('checked')) {
    $('#mostrar').html(ventas);
    $('#total').html("Ventas: "+totalP);
  }else if ($('#Gastos').prop('checked')) {
    $('#mostrar').html(gastos);
    $('#total').html("Gastos: "+totalG);
  };
}

function getCaja(){
  totalG = 0;
  totalP = 0;
  $.ajax({
    url: 'api.php',
    type: 'POST',
    data: {
      request: 'getMyOwnCaja'
    }
  })
  .done(data => {
    if (permitido(data)) {
        data = JSON.parse(data);
        if (data[0].length>0) {
          gastos = ` <thead>
                        <tr class='black'>
                          <th class='white-text'>Encargado</th>
                          <th class='white-text'>Producto</th>
                          <th class='white-text'>P/Unitario</th>
                          <th class='white-text'>Cantidad</th>
                          <th class='white-text'>Total</th>
                          <th class='white-text'>Fecha</th>
                        </tr>
                      </thead>
                      <tbody class='red lighten-4'>
                      `;
          data[0].forEach(item => {
            totalG++;
            gastos+=`
                    <tr>
                      <td>` + item['Encargado'] + `</td> 
                      <td>` + item['Producto'] + `</td> 
                      <td>` + item['Precio'] + `</td>
                      <td>` + item['Cantidad'] + `</td>
                      <td>-$`+ item['Total'] + ` </td>
                      <td>` + item['FH'] + `</td>
                    </tr>
                    `;
          });
          gastos+=`</tbody>`;
        }else
          gastos = 'NINGÚN GASTO EFECTUADO';
        if (data[1].length>0) {
          ventas = ` <thead>
                        <tr class='black'>
                          <th class='white-text'>Encargado</th>
                          <th class='white-text'>Total</th>
                          <th class='white-text'>Tomado</th>
                          <th class='white-text'>Entregado</th>
                          <th class='white-text'>Regalo</th>
                        </tr>
                      </thead>
                      <tbody class='green lighten-3'>
                      `;
          for (let i = 0; i <data[1].length; i++) {
            totalP++;
            let verificarCancelado = 0;
            if (data[1][i]['Cancelado']!="") {
              let cancelado = String(data[1][i]['Cancelado']).split("(");
              if (cancelado.length == 2) {
                cancelado[0] = String(cancelado[1]).split(")");
                verificarCancelado = 2;
              }else{verificarCancelado = 1;}
            }
            let aux = '';
            let regalo = data[1][i]['Regalo'];
            if (regalo=='Pagado' && data[1][i]['Cancelado']=="") {
              ventas+=`<tr>`;
              aux = '+';
            }else{
              switch(verificarCancelado){
                case 0:
                  ventas+=`<tr class='grey lighten-1'>`;
                break;
                case 1:
                  ventas+=`<tr>`;
                  aux = '+';
                break;
                case 2:
                  ventas+=`<tr class='grey lighten-1'>`;
                  regalo = 'Se devolvió el dinero';
                break;
                default:
                  ventas+=`<tr>`;
                  aux = '+';
                break;
              }  
            }
            ventas+=`<td>` + data[1][i]['Encargado'] + `</td> 
                      <td><a class="btn red" onclick="verOrden('` + data[1][i]['ID'] + `')" href="#">
                          `;
            ventas+= aux+`$` + data[1][i]['Total'] + `
                        </a></td>
                        <td>` + data[1][i]['Inicio'] + `</td>`;
            if (data[1][i]['Cancelado']=="") {
              ventas+=`<td>` + data[1][i]['FH'] + `</td>`;
            }else{
              ventas+=`<td>` + data[1][i]['Cancelado'] + `</td>`;
            }
            ventas+=` <td>` + regalo + `</td>
                    </tr>
                    `;
          };
          ventas+=`</tbody>`;
        }else {
          ventas = 'NINGUNA VENTA REALIZADA';
        }
        $('#mostrar').html(gastos);
        $('#total').html("Gastos: "+totalG);
        $('#Gastos').prop('checked',true);
        $('#Ventas').prop('checked',false);
    }
  })
  .fail(data => {
        ERRORFATAL()
  });
      
}

function verOrden(id){
  $.ajax({
    url: 'api.php',
    type: 'POST',
    data: {
      request: 'View_More',
      content: [id,1]
    }
  })
  .done(data => {
    if (permitido(data)) {
      $("#ver_pedido").modal("open");
      data = JSON.parse(data);
      let html = "";
      html +=`<p>Día `+data["datos"][0]["DIA"]+`</p>`;
      html +=`<p>Total:$`+data["datos"][0]["Total"]+`</p>`;
      html +=`<p>`+data["datos"][0]["Tomado"]+`</p>`;
      html +=`<p>`+data["datos"][0]["Listo"]+`</p>`;
      html +=`<p>`+data["datos"][0]["Entregado"]+`</p>`;
      html +=`<p>Encargado: `+data["datos"][0]["Encargado"]+`</p>`;
      html +=`<p>`+data["datos"][0]["Regalo"]+`</p>`;
      html +=`<p>`+data["datos"][0]["Cancelado"]+`</p>`;
      $('#ver_pedido').modal('open');
      $('#pedido_titulo').html('Pedido #'+data["datos"][0]["ID_2"]);
      $('#pedido_info').html(html+data['html']);
    };
  })
  .fail(data => {
        ERRORFATAL()
  });
}