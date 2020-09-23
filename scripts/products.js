let atime = 200;

const NO_FOOD = 'No hay comidas.';
const NO_DRINKS = 'No hay bebidas.';
var opDrink = "0";
var opFood = "0";
var opSoat = "0";
var opPromo = "0";
var totalS = 0;
var foods;
var soats;
var promos;
var drinks;
var body;

$('document').ready(()=> {
  $('.modal').modal();
  $('select').material_select();
  $('#only_foods').show();
  $('#only_soats').hide();
  $('#only_drinks').hide();
  $('#only_promos').hide();
  getDrinks();
  getFoods();
  getSoats();
  getPromos();
  filtros()
});

function Buscar(){
  if (opDrink != $('#drinks_filter1').val()) {
    opDrink = $('#drinks_filter1').val();
    getDrinks();
  }else if (opFood != $('#foods_filter1').val()) {
    opFood = $('#foods_filter1').val();
    getFoods();
  }else if (opSoat != $('#soats_filter1').val()) {
    opSoat = $('#soats_filter1').val();
    getSoats();
  }
}

function filtros(){
  $.ajax({
    url: 'api.php',
    type: 'POST',
    data: {
      request: 'getAllCategorias'
    }
  })
  .done(data => {
    if (permitido(data)) {
      data = JSON.parse(data);
      drinks = `<select name="drinks_filter" onchange="Buscar()" id="drinks_filter1">
                <option value="0">--------</option>`;
      foods = `<select name="food_filter" onchange="Buscar()" id="foods_filter1">
                <option value="0">--------</option>`;
      soats = `<select name="soats_filter" onchange="Buscar()" id="soats_filter1">
                <option value="0">--------</option>`;
      
      data.forEach(item => {
        if (item['Tipo'] == "Bebida") 
          drinks += `<option value="`+item['ID_Categoria']+`">`+item['Categoria']+`</option>`;
        else if (item['Tipo'] == "Comida") 
          foods += `<option value="`+item['ID_Categoria']+`">`+item['Categoria']+`</option>`;
        else if (item['Tipo'] == "Salsa") 
          soats += `<option value="`+item['ID_Categoria']+`">`+item['Categoria']+`</option>`;
      });
      drinks+= `</select>`;
      foods+= `</select>`;
      soats+= `</select>`;
      $('#foods_filter').html(foods);
      $('#soats_filter').html(soats);
      $('#drinks_filter').html(drinks);
      $('select').material_select();
    };
  })
  .fail(data => {
      ERRORFATAL()
  });
}

function getSoats(){
    $.ajax({
      url: 'api.php',
      type: 'POST',
      data: {
        request: 'getSoats',
      content: [opSoat]
      }
    })
    .done(data => {
      if (permitido(data)) {
        data = JSON.parse(data);
        if (data.length > 0) {
          soats='';
          
          data.forEach(item => {
            soats += `
                      <div id="Salsa` + item["IDFinal"] + `" class="producto">
                          <div class="card z-depth-5" >
                              <div class="card-content white left-align">
                                <span class="card-title black-text truncate tooltipped" data-position="top" data-tooltip="` + item["Salsa"] +`">
                                  ` + item["Salsa"] +`
                                </span>
                                <p class="truncate tooltipped" data-position="top" data-tooltip="Categoria: ` + item["Categoria"] +`">
                                  Categoria: ` + item["Categoria"] + `
                                </p>
                                <p>ID: ` + item["IDFinal"] + `</p>
                              </div>
                              <div class="card-action center-align amber lighten-2">
                                  <a class="black-text modal-trigger" href="#view_product" onclick="searchSoat(` + item["IDFinal"] + `)">Ver más</a>
                              </div>
                          </div>
                      </div>
                      <hr>`;
          });
        }
        $('#soats_empty').html(soats);
        $('.tooltipped').tooltip({delay: 0});
      };
    })
    .fail(data => {
        ERRORFATAL()
    });
}

function getDrinks(){
    $.ajax({
    url: 'api.php',
    type: 'POST',
    data: {
      request: 'getDrinks',
      content: [opDrink]
    }
    })
    .done(data => {
      if (permitido(data)) {
        data = JSON.parse(data);
        if (data.length > 0) {
          drinks = '';
          
          data.forEach(item => {
            drinks += `
              <div id="Bebida` + item["IDFinal"] + `" class="producto">
                  <div class="card z-depth-5" >
                      <div class="card-content white left-align">
                        <span class="card-title black-text truncate tooltipped"  data-position="top" data-tooltip="` + item["Marca"] +`  ` + item["Size"] +`">
                        ` + item["Marca"] +`  ` + item["Size"] +`
                        </span>
                        <p class="truncate tooltipped"   data-position="top" data-tooltip="Sabor: ` + item["Sabor"] + `">
                        Sabor: ` + item["Sabor"] + `
                        </p>
                        <p class="truncate tooltipped"   data-position="top" data-tooltip="Precio: $` + item["Precio"] + `">
                        Precio: $` + item["Precio"] + `
                        </p>
                        <p>ID: ` + item["IDFinal"] + `</p>
                      </div>
                      <div class="card-action center-align amber lighten-2">
                          <a class="black-text modal-trigger" href="#view_product" onclick="searchDrink(` + item["IDFinal"] + `)">Ver más</a>
                      </div>
                  </div>
              </div>
              <hr>
              `;
          });
        }
        $('#drinks_empty').html(drinks);
        $('.tooltipped').tooltip({delay: 0});
      };
    })
    .fail(data => {
        ERRORFATAL()
    });
}

function getFoods(){
  $.ajax({
    url: 'api.php',
    type: 'POST',
    data: {
      request: 'getFoods',
      content: [opFood]
    }
  })
  .done(data => {
    if (permitido(data)) {
      data = JSON.parse(data);
      foods = '';
      if (data.length > 0) {
        data.forEach(item => {
          foods += `
                    <div id="Comida` + item["IDFinal"] + `" class="producto">
                        <div class="card z-depth-5" >
                            <div class="card-content white left-align">
                              <span class="card-title black-text truncate tooltipped" data-position="top" data-tooltip="` + item["Comida"] +`  ` + item["Size"] +`">
                                ` + item["Comida"] +`  ` + item["Size"] +`
                              </span>
                              
                              <p class="truncate tooltipped"   data-position="top" data-tooltip="Precio: $` + item["Precio"] + `">
                              Precio: $` + item["Precio"] + `
                              </p>
                              <p>ID: ` + item["IDFinal"] + `</p>
                            </div>

                            <div class="card-action center-align amber lighten-2">
                                <a class="black-text modal-trigger" href="#view_product" onclick="searchFood(` + item["IDFinal"] + `)">Ver más</a>
                            </div>
                        </div>
                    </div>
                    <hr>
                    `;
        });
      }
      $('#foods_empty').html(foods);
      $('.tooltipped').tooltip({delay: 0});
    };
  })
  .fail(data => {
        ERRORFATAL()
    });
}

function getPromos(){
  $.ajax({
    url: 'api.php',
    type: 'POST',
    data: {
      request: 'getPromos',
      content: [1]
    }
  })
  .done(data => {
    if (permitido(data)) {
      data = JSON.parse(data);
      promos = '';
      if (data.length > 0) {
        data.forEach(item => {
          promos += `
                    <div id="Promo` + item["ID_Promo"] + `" class="producto">
                        <div class="card z-depth-5" >
                            <div class="card-content white left-align">
                              <span class="card-title black-text truncate tooltipped" data-position="top" data-tooltip="` + item["Nombre"]+`">` + item["Nombre"]+`</span>
                              
                              <p class="truncate tooltipped"   data-position="top" data-tooltip="Precio: $` + item["Precio"] + `">
                              Precio: $` + item["Precio"] + `
                              </p>
                              <p>ID: ` + item["ID_Promo"] + `</p>
                            </div>

                            <div class="card-action center-align amber lighten-2">
                                <a class="black-text modal-trigger" href="#view_product" onclick="searchPromo(` + item["ID_Promo"] + `)">Ver más</a>
                            </div>
                        </div>
                    </div>
                    <hr>
                    `;
        });
      }
      $('#promos_empty').html(promos);
      $('.tooltipped').tooltip({delay: 0});
    };
  })
  .fail(data => {
        ERRORFATAL()
    });
 }

//SEARCH
  function searchDrink(id){
    $('#Title').html('');
    $('#Descripcion').html('');
    $('#Opciones').html('');
    $.ajax({
      url: 'api.php',
      type: 'POST',
      data: {
        request: 'searchDrink',
        content: [id]
      }
    })
    .done(data => {
      if (permitido(data)) {
        data = JSON.parse(data);
        let title = data[0]['Marca']+` `+data[0]['Size']+` (`+data[0]['Categoria']+`)  (`+data[0]['IDFinal']+`)`;
        body = `    <div class="row">
                        <div class="col s5">
                          Precio: $`+data[0]["Precio"]+`
                        </div>
                        <div class="col s5">
                            Envase: `+data[0]["Envase"]+`
                        </div>
                        <div class="col s5">
                            Sabor: `+data[0]["Sabor"]+`
                        </div>
                    </div>`;
          let footer = ``;
              if (data[0]['IDFinal']==data[0]['aux']) 
                footer =`<button class="btn-flat" onclick="EditDrink(`+data[0]['IDFinal']+`,'`+data[0]['Marca']+`','`+data[0]['Size']+`','`+data[0]['Categoria']+`','`+data[0]['Precio']+`','`+data[0]['Envase']+`','`+data[0]['Sabor']+`')">Editar</button>`;
              footer += `<button class="modal-close btn-flat">Cancelar</button>`;
          Ordenar(title, body, footer);
      };
    })
    .fail(data => {
        ERRORFATAL()
    });
  }

  function searchSoat(id){
    $('#Title').html('');
    $('#Descripcion').html('');
    $('#Opciones').html('');
      $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
          request: 'searchSoat',
          content: [id]
        }
      })
      .done(data => {
        if (permitido(data)) {
          data = JSON.parse(data);
          let title = data[0]['Nombre']+` (`+data[0]['IDFinal']+`)`;
          body = `<div class="row">
                        <div class="col s12">
                          Precios: `+data[0]["Precios"]+`
                        </div>`;
          body += `<div class="col s12" id="soatUse">`;      
          body += `</div>`;       
          body += `</div>`;

          let footer = `<button class="modal-close btn-flat">Cancelar</button>`;
          Ordenar(title, body, footer);

          $.ajax({            
            url: 'api.php',
            type: 'POST',
            data: {
              request: 'searchSoatUse',
              content: [id]
            }
          })
          .done(data => {
            if (permitido(data)) {
              data = JSON.parse(data);
              if (data.length>1) {
                let usa = `Contiene: <ul class="collection">`;
                
                data.forEach(item => {
                  usa += `  <div class="row">
                              <li class="collection-item">`+item["Producto"]+`
                            </div>`;
                });
                usa += `</ul>`;
                $('#soatUse').html(usa);
              }
            };
          })
          .fail(data => {
              ERRORFATAL()
          });
        };
      })
      .fail(data => {
        ERRORFATAL()
    });
  }

  function searchFood(id){
    $('#Title').html('');
    $('#Descripcion').html('');
    $('#Opciones').html('');
    $.ajax({
      url: 'api.php',
      type: 'POST',
      data: {
        request: 'searchFood',
        content: [id]
      }
    })
    .done(data => {
    if (permitido(data)) {
        data = JSON.parse(data);
        let title = data[0]['Nombre']+` `+data[0]['Categoria'] +` (`+data[0]['IDFinal']+`)`;
        body = `  <div class="row">
                      <div class="col s5">
                          Precio: $`+data[0]["Precio"]+`
                      </div>
                  </div>`;
        let footer = ``;
            if (data[0]['IDFinal']==data[0]['aux']) 
              footer =`<button class="btn-flat" onclick="EditFood(`+data[0]['IDFinal']+`,'`+data[0]['Nombre']+`','`+data[0]['Categoria']+`',`+data[0]['Precio']+`)">Editar</button>`;
            footer += `<button class="modal-close btn-flat">Cancelar</button>`;
        Ordenar(title, body, footer);
    };
    })
    .fail(data => {
        ERRORFATAL()
    });
  }

  function searchPromo(id){
    $('#Title').html('');
    $('#Descripcion').html('');
    $('#Opciones').html('');
    $.ajax({
      url: 'api.php',
      type: 'POST',
      data: {
        request: 'getOnePromo',
        content: [id]
      }
    })
    .done(data => {
      if (permitido(data)) {
        $('#Title').html('');
        $('#Descripcion').html(data);
        
        $.ajax({
          url: 'api.php',
          type: 'POST',
          data: {
            request: 'searchPromo',
            content: [id]
          }
        })
        .done(data => {
          if (permitido(data)) {
            data = JSON.parse(data);
            $('#Title').html(data[0]['Nombre']+' $'+data[0]['Precio']);
            let footer = ``;
            if (data[0]['Nombre']==data[0]['aux']) 
              footer =`<button class="btn-flat" onclick="EditPromo(`+id+`,'`+data[0]['Nombre']+`',`+data[0]['Precio']+`)">Editar</button>`;
            footer += `<button class="modal-close btn-flat">Cancelar</button>`;
            $('#Opciones').html(footer);
          };
        })
        .fail(data => {
          ERRORFATAL()
      });
      };
    })
    .fail(data => {
        ERRORFATAL()
    });
  }

  
//MOSTRAR
  function Ordenar(titulo,body,footer){
    $('#Title').html(titulo);
    $('#Descripcion').html(body);
    $('#Opciones').html(footer);
  }


//MOSTRAR LOS PRODUCTOS

  $("#show_food").click(()=>{
    $('#only_foods').show();
    $('#only_soats').hide();
    $('#only_drinks').hide();
    $('#only_promos').hide();
  });

  $("#show_soats").click(()=>{
    $('#only_foods').hide();
    $('#only_soats').show();
    $('#only_drinks').hide();
    $('#only_promos').hide();
  });

  $("#show_drink").click(()=>{
    $('#only_foods').hide();
    $('#only_soats').hide();
    $('#only_drinks').show();
    $('#only_promos').hide();
  });

  $("#show_promo").click(()=>{
    $('#only_foods').hide();
    $('#only_soats').hide();
    $('#only_drinks').hide();
    $('#only_promos').show();
  });
