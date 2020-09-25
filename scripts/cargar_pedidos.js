let atime = 100;
var total = 0;
var totalFOOD = 0;
var soat = 0;
var food = "";
var id;
var id_ComidaSoat;
var foods = "";
var soats = "";
var drinks = "";
var promos = "";
var soatsaux = '';
var pedidos = 0;
var precioFinal = 0;

const colapsibleC = `<ul class='collapsible'>
                        <li>
                            <div class="collapsible-header active orange lighten-3">Comidas</div>
                            <div class="collapsible-body" id="option_foods"></div>
                        </li>
                    </ul>`;
const colapsibleD = `<ul class='collapsible'>
                        <li>
                            <div class="collapsible-header active orange lighten-3">Bebidas</div>
                            <div class="collapsible-body" id="option_drinks"></div>
                        </li>
                    </ul>`;
const colapsibleP = `<ul class='collapsible'>
                        <li>
                            <div class="collapsible-header active orange lighten-3">Promos</div>
                            <div class="collapsible-body" id="option_promos"></div>
                        </li>
                    </ul>`;

var drinksHTML = '';
var foodsHTML = '';
var promoHTML = '';

const NO_ORDERS_QUEVE = 'No hay pedidos en cola.';
const NO_ORDERS_DONE = 'No hay pedidos finalizados.';
$('document').ready(()=> {
    $('.modal').modal();
    $('.tooltipped').tooltip();

    mostrarTotal();
    todo();
});


function make_order_fast() {
    $('#order_fast').html('');
    $('#total_fast').html('');
    $('#op_fast').html('');
    $('#order_op_fast').html('');
    $('#order_food_fast').html('');
    drinksFAST = '';
    salsasFAST = '';
    comidaFAST = '';
    let op = `<a href="#!" class="modal-close btn red"><i class="material-icons">clear</i></a>`;
    $('#op_fast').html(op);
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'getDrinks',
            content: [0]
        }
    })
    .done(data => {
        if (permitido(data)) {
            data = JSON.parse(data);
            if (data.length > 0) {
                data.forEach(item => {
                    drinksFAST += ` <a class="black-text option" onclick="add_drink_fast('` + item["IDFinal"] + `','` + item["Marca"] + `','` + item["Size"] + `','` + item["Sabor"] + `',` + item["Precio"] + `)">`;
                    drinksFAST += `     <div class="row  option">`;
                    drinksFAST += `
                                            <span class="red-text card-title ">` + item["Marca"] + `  ` + item["Size"] + `</span>
                                            <p>Sabor: ` + item["Sabor"] + `</p>
                                            <p>Precio: $` + item["Precio"] + `</p>
                                        </div>
                                    </a>
                                    <div class="divider"></div>`;
                });
            }
        };
    })
    .fail(data => {
        ERRORFATAL()
    });

    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'getFoods',
            content: [0]
        }
    })
    .done(data => {
        if (permitido(data)) {
            data = JSON.parse(data);
            if (data.length > 0) {
                data.forEach(item => {
                    comidaFAST += ` <a class="black-text modal-close option" onclick="Salsas_fast('` + item["IDFinal"] + `','` + item["ID"] + `')">`;
                    comidaFAST += `     <div class="row  option">`;
                    comidaFAST += `         <span class="red-text card-title ">` + item["Comida"] + `  ` + item["Size"] + `</span>
                                        </div>
                                    </a>
                                    <div class="divider"></div>`;
                });
            }
            $('#order_food_fast').html(comidaFAST);
        };
    })
    .fail(data => {
        ERRORFATAL()
    });

}

$("#Bebidas").click(()=>{
    $('#order_op_fast').html(drinksFAST);
});

$("#Salsas").click(()=>{
    $('#food_fast').modal('open');
});

function Salsas_fast(id_comida, id) {
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'getSoat',
            content: [id_comida, id]
        }
    })
    .done(data => {
            if (permitido(data)) {
                data = JSON.parse(data);
                let html = '';
                if (data.length > 0) {
                    for (let i = 0; i < data.length; i++) {
                        html += `
                                <div class="row">
                                    <a class="black-text" onclick="add_soat_fast('` + data[i]["IDFinal"] + `',` + data[i]["Precio"] + `,'` + data[i]["Categoria"] + `','` + data[i]["Salsa"] + `',` + data[i]["ID_Categoria"] + `,` + id_comida + `,` + id + `)">
                                    <div class="col s5  option">
                                        <span class="card-title red-text">` + data[i]["Salsa"] + `</span>
                                        <p>` + data[i]["Categoria"] + `</p>
                                        <p>Precio: $` + data[i]["Precio"] + `</p>
                                    </div>
                                    </a>`;
                        i++;
                        if (i < data.length) {
                            html += `<a class="black-text" onclick="add_soat_fast('` + data[i]["IDFinal"] + `',` + data[i]["Precio"] + `,'` + data[i]["Categoria"] + `','` + data[i]["Salsa"] + `',` + data[i]["ID_Categoria"] + `,` + id_comida + `,` + id + `)">
                                        <div class="col s5  option">
                                        <span class="card-title red-text">` + data[i]["Salsa"] + `</span>
                                        <p>` + data[i]["Categoria"] + `</p>
                                        <p>Precio: $` + data[i]["Precio"] + `</p>
                                        </div>
                                    </a>`;
                        };

                        html += `   </div>
                                    <div class="divider"></div>`;
                    }
                }
                $('#order_op_fast').html(html);
            };
        })
        .fail(data => {
            ERRORFATAL()
        });
}

function remove_fast() {
    let op = `<a href="#!" class="modal-close btn red"><i class="material-icons">clear</i></a>`;
    $('#op_fast').html(op);
    $('#order_fast').html('');
    $('#total_fast').html('');
    $('#order_op_fast').html('');
}

function add_drink_fast(id, marca, size, sabor, precio) {
    let bebida = `<div class="row" id="bebida_` + id + `">
                    <div class="col s12">
                    ` + marca + ` ` + size + ` ` + sabor + ` $` + precio + `
                    <a class="btn-floating right red" onclick="remove_fast()"><i class="material-icons">close</i></a>
                    </div>
                    </div>`;
    $('#order_fast').html(bebida);
    $('#total_fast').html('Total $' + precio);
    let op = `  <a href="#!" class="btn green" onclick="AgregarFast('1-` + id + `')"><i class="material-icons">check</i></a>
                <a href="#!" class="modal-close btn red"><i class="material-icons">clear</i></a>`;
    $('#op_fast').html(op);
}

function add_soat_fast(id, precio, categoria, salsa, idCategoria, idcomida, id) {
    let soat = `<div class="row m2" id="soat_` + id + `">
                    <div class="col s12">
                    ` + salsa + ` $` + precio + ` (` + categoria + `)
                    <a class="btn-floating right red" onclick="remove_fast()"><i class="material-icons">close</i></a>
                    </div>
                </div>`;
    $('#order_fast').html(soat);
    $('#total_fast').html('Total $' + precio);
    let op = `  <a href="#!" class=" modal-close btn green" onclick="AgregarFast('3-` + id + `.` + idcomida + `.` + id + `.` + idCategoria + `')"><i class="material-icons">check</i></a>
                <a href="#!" class="modal-close btn red"><i class="material-icons">clear</i></a>`;
    $('#op_fast').html(op);
}

function AgregarFast(id) {
    $('#order_make_fast').modal('close');
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'addFast',
            content: [id]
        }
    })
    .done(data => {
        if (permitido(data)) {
            if (parseInt(data) == 0) {
                Materialize.toast('Venta realizada con éxito.', 2000);
                getAllCaja();
            } else 
                Materialize.toast('ERROR.', 2000);
        }
    })
    .fail(data => {
        ERRORFATAL()
    });
}

function make_order() {
    foods = "";
    soats = "";
    drinks = "";
    promos = "";
    total = 0;
    totalFOOD = 0;
    soat = 0;
    food = "";
    mostrarTotal();
    $('#order_drink').html('');
    $('#order_food').html('');
    $('#order_soat').html('');
    $('#order_promo').html('');
    getDrinks();
    getFoods();
    getPromos()
}

function Seguro() {
    $('#num').val('')
    $('#Seguro').modal('open');
    $('#PedidosYa').prop('checked', false);
    $('#Regalo').prop('checked', false);
    $('#Pedido').prop('checked', true);
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'Seguro',
            content: [foods.split("-"), soats.split("-"), drinks.split("-"), promos.split("-")]
        }
    })

    .done(data => {
        if (permitido(data)) {
            data = JSON.parse(data);
            $('#productos_pedidos').html(data[0]['html']);
            precioFinal = data[0]['total'];
            $('#Detalles').html('Total: $<input type="number" readonly="readonly" disabled="disabled" name="TotalModificable" id="TotalModificable" value="' + data[0]['total'] + '">');
        };
    })
    .fail(data => {
        ERRORFATAL()
    });
}


function finish_make_order() {
    let id = $('#num').val();
    let regalo = $('#Regalo').prop('checked');
    let PedidosYa = $('#PedidosYa').prop('checked');
    let opcion = 0;
    let nombre_regalo = '';
    let total = 0;
    if (regalo) {
        opcion = 1;
        nombre_regalo = $('#nombre_regalo').val();
    } else if (PedidosYa) {
        opcion = 2;
        total = $('#TotalModificable').val();
    }
    if (id.length < 1) 
        Materialize.toast('No podés dejar ningún campo vacío.', 2000);
    else {
        if (regalo && nombre_regalo.length < 1) 
            Materialize.toast('No podés dejar ningún campo vacío.', 2000);
        else {
            $.ajax({
                url: 'api.php',
                type: 'POST',
                data: {
                    request: 'makeOrder',
                    content: [foods.split("-"), soats.split("-"), drinks.split("-"), promos.split("-"), id, nombre_regalo, [opcion, total]]
                }
            })

            .done(dataO => {
                let data = parseInt(dataO);
                if (permitido(dataO)) {
                    switch (data) {
                        case 4:
                            Materialize.toast('Ya existe un pedido con este número.', 2000);
                            break;
                        default:
                            data = JSON.parse(dataO);
                            $('.totalCaja').html(data[0]["Caja"]);
                            let html = `
                                        <div id="Pedido` + data[0]["ID_Pedido"] + `" class="producto">
                                            <div class="card z-depth-5" >
                                                <div class="card-content white left-align">
                                                <span class="card-title black-text">
                                                    Pedido: #` + id + `
                                                </span>
                                                <p>
                                                    Precio: $` + data[0]["Total"] + `
                                                </p>
                                                </div>
                                                <div class="card-action center-align amber lighten-2" id="opciones` + data[0]["ID_Pedido"] + `">
                                                    <a class="black-text modal-trigger" href="#" onclick="listo_pedido(` + data[0]["ID_Pedido"] + `)">Listo</a>
                                                    <a class="black-text modal-trigger" href="#order_remove" onclick="rechazar_pedido_request(` + data[0]["ID_Pedido"] + `)">Cancelar</a>
                                                    <br><a class="black-text modal-trigger" href="#view_more" onclick="Ver_Mas(` + data[0]["ID_Pedido"] + `)">Ver más</a>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                        `;
                            getAllCaja();
                            if ($('#queue_empty').length > 0) {
                                setTimeout(()=> {
                                    $('#queue_empty').fadeOut();
                                }, atime);
                                setTimeout(()=> {
                                    $('#queued_requests_cards_container').append(html);
                                }, atime * 2);
                            } else 
                                $('#queued_requests_cards_container').append(html);
                            $('#Seguro').modal('close');
                            $('#order_make').modal('close');
                            $('#regalo').prop('checked', false);
                            $('#num').prop('checked', true);
                            break;
                    }
                };
            })
            .fail(data => {
                ERRORFATAL()
            });
        }
    }
}

//COLAPSIBLES

    $("#open_drink").click(()=>{
        $('#open').html(colapsibleD);
        $('.collapsible').collapsible();
        $('#option_drinks').html(drinksHTML);
    });

    $("#open_food").click(()=>{
        $('#open').html(colapsibleC);
        $('.collapsible').collapsible();
        $('#option_foods').html(foodsHTML);
    });

    $("#open_promo").click(()=>{
        $('#open').html(colapsibleP);
        $('.collapsible').collapsible();
        $('#option_promos').html(promoHTML);
    });
function Ver_Mas(id) {
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'View_More',
            content: [id, 0]
        }
    })

    .done(data => {
        if (permitido(data)) {
            data = JSON.parse(data);
            let html = "";
            html += `<p>Total:$` + data["datos"][0]["Total"] + `</p>`;
            html += `<p>` + data["datos"][0]["Inicio"] + `</p>`;
            html += `<p>` + data["datos"][0]["Listo"] + `</p>`;
            html += `<p>Encargado: ` + data["datos"][0]["Encargado"] + `</p>`;
            html += `<p>` + data["datos"][0]["Regalo"] + `</p>`;
            $('#ver_mas').modal('open');
            $('#pedido_titulo').html('Pedido #' + data["datos"][0]["ID_2"]);
            $('#pedido_info').html(html + data['html']);
        };
    })
    .fail(data => {
        ERRORFATAL()
    });
}

function add_promo(id, nombre, precio) {
    let promo = `   <div class="row" id="promo_` + id + `">
                        <div class="col s12">
                            ` + nombre + ` $` + precio + `
                            <a class="btn-floating right red" onclick="remove('promo_` + id + `',` + precio + `)"><i class="material-icons">close</i></a>
                        </div>
                    </div>`;
    $('#order_promo').append(promo);
    total = total + precio;
    promos += id + "-";
    mostrarTotal();
}

$("#Pedido").click(()=>{
    $('#regalo').html('');
    $('#TotalModificable').attr('readonly', 'readonly');
    $('#TotalModificable').attr('disabled', 'disabled');
    $('#TotalModificable').val(precioFinal);
    $('#num').attr('type', 'number');
});


$("#Regalo").click(()=>{
    let html = `<div>
                    <input type="text" name="nombre_regalo" pattern="[a-zA-Z]" class="nombre_regalo verify" id="nombre_regalo" required>
                    <label for=nombre_regalo>Ingresá el nombre:</label>
                </div>`;
    $('#regalo').html(html);
    $('#TotalModificable').attr('readonly', 'readonly');
    $('#TotalModificable').attr('disabled', 'disabled');
    $('#TotalModificable').val(precioFinal);
    $('#num').attr('type', 'number');
});


$("#PedidosYa").click(()=>{
    $('#regalo').html('');
    $('#TotalModificable').attr('readonly', false);
    $('#TotalModificable').attr('disabled', false);
    $('#num').attr('type', 'text');
});

function getPromos() {
    promoHTML = '';
    $('#add_promo').modal('open');
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'getPromos'
        }
    })

    .done(data => {
            if (permitido(data)) {
                data = JSON.parse(data);
                if (data.length > 0) {
                    for (let i = 0; i < data.length; i++) {
                        promoHTML += `
                                        <div class="row">
                                            <a class="black-text" onclick="add_promo('` + data[i]["ID_Promo"] + `','` + data[i]["Nombre"] + `',` + data[i]["Precio"] + `)">
                                            <div class="col s5  option">
                                                <span class="card-title red-text">` + data[i]["Nombre"] + `</span>
                                                <p>Precio: $` + data[i]["Precio"] + `</p>
                                                <a href="#!" class="btn" onclick="ver_promo('` + data[i]["ID_Promo"] + `')"><i class="material-icons">remove_red_eye</i></a>
                                            </div>
                                            </a>`;
                        i++;
                        if (i < data.length) {
                            promoHTML += `  <a class="black-text" onclick="add_promo('` + data[i]["ID_Promo"] + `','` + data[i]["Nombre"] + `',` + data[i]["Precio"] + `)">
                                            <div class="col s5  option">
                                                <span class="card-title red-text">` + data[i]["Nombre"] + `</span>
                                                <p>Precio: $` + data[i]["Precio"] + `</p>
                                                <a href="#!" class="btn" onclick="ver_promo('` + data[i]["ID_Promo"] + `')"><i class="material-icons">remove_red_eye</i></a>
                                                </div>
                                            </a>`;
                        };

                        promoHTML += `  </div>
                                        <div class="divider"></div>`;
                    }
                }
            };
        })
        .fail(data => {
            ERRORFATAL()
        });
}

function ver_promo(id) {
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
            $('#view_promo').modal('open');
            $('#promo_info').html(data);
        };
    })
    .fail(data => {
        ERRORFATAL()
    });
}

function mostrarTotal() {
    if (total > 0) {
        $('#total').html('<h5>Total: $' + total + '</h5>');
        let op = `  <a href="#!" class="btn green" onclick="Seguro()"><i class="material-icons">check</i></a>
                    <a href="#!" class="modal-close btn red"><i class="material-icons">clear</i></a>`;
        $('#op').html(op);
    } else {
        $('#total').html('');
        let op = `<a href="#!" class="modal-close btn red"><i class="material-icons">clear</i></a>`;
        $('#op').html(op);
    }
}

function final_soat() {
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'getSomeSoats',
            content: [soatsaux.split('-'), id_ComidaSoat, pedidos]
        }
    })

    .done(data => {
            if (permitido(data)) {
                data = JSON.parse(data);
                food += data["Mostrar"];
                soats += data["id"];
                food += `<a class="btn-floating right red" onclick="remove('food_` + id_ComidaSoat + `_` + pedidos + `',` + totalFOOD + `)"><i class="material-icons">close</i></a>
                </div></div>
                </div>`;
                $('#order_soat').html("");
                $('#order_food').append(food);
                total = totalFOOD + total;
                foods += id_ComidaSoat + "(" + pedidos + ")-";
                mostrarTotal();
                reiniciar_soat();
            };
        })
        .fail(data => {
            ERRORFATAL()
        });

}

function reiniciar_soat() {
    totalFOOD = 0;
    soat = 0;
    food = '';
}

function mostrarTOTALFOOD() {
    if (totalFOOD > 0)
        $('#totalFOOD').html('<h5>Total de la comida con salsa: $' + totalFOOD + ' <span class="green-text">(+' + soat + ')</span></h5>');
    else
        $('#totalFOOD').html('');
}

function add_drink(id, marca, size, sabor, precio) {
    let bebida = `  <div class="row" id="bebida_` + id + `">
                        <div class="col s12">
                            ` + marca + ` ` + size + ` ` + sabor + ` $` + precio + `
                            <a class="btn-floating right red" onclick="remove('bebida_` + id + `',` + precio + `)"><i class="material-icons">close</i></a>
                        </div>
                    </div>`;
    $('#order_drink').append(bebida);
    total = total + precio;
    drinks += id + "-";
    mostrarTotal();
}

function add_soat(id, precio, categoria, salsa) {

    let op = `  <a href="#!" class="modal-close btn green" onclick="final_soat()"><i class="material-icons">check</i></a>
                <a href="#!" class="modal-close btn red"><i class="material-icons">clear</i></a>`;
    $('#op_soat').html(op);

    let soatA = `   <div class="row m2" id="soat_` + id + `">
                        <div class="col s12">
                            <span id="datos_` + id + `">` + salsa + ` $` + precio + ` (` + categoria + `)</span>
                            <a class="btn-floating right red" onclick="remove_soat('soat_` + id + `','soatfood_` + id + `',` + precio + `)"><i class="material-icons">close</i></a>
                        </div>
                    </div>`;
    $('#order_soat').append(soatA);
    totalFOOD = totalFOOD + precio;
    soat = soat + precio;
    mostrarTOTALFOOD();
    soatsaux += id + "-";
}

function add_soats(id_comida, precioA, nombre, size, id) {
    soatsaux = '';
    totalFOOD = 0;
    soat = 0;
    food = '';
    id_ComidaSoat = id_comida;
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'getSoat',
            content: [id_comida, id]
        }
    })

    .done(data => {
        if (permitido(data)) {
            data = JSON.parse(data);
            let html = '';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    html += `
                            <div class="row">
                                <a class="black-text" onclick="add_soat('` + data[i]["IDFinal"] + `',` + data[i]["Precio"] + `,'` + data[i]["Categoria"] + `','` + data[i]["Salsa"] + `')">
                                <div class="col s5  option">
                                    <span class="card-title red-text">` + data[i]["Salsa"] + `</span>
                                    <p>` + data[i]["Categoria"] + `</p>
                                    <p>Precio: $` + data[i]["Precio"] + `</p>
                                </div>
                                </a>`;
                        i++;
                        if (i < data.length) {
                            html += `<a class="black-text" onclick="add_soat('` + data[i]["IDFinal"] + `',` + data[i]["Precio"] + `,'` + data[i]["Categoria"] + `','` + data[i]["Salsa"] + `')">
                                    <div class="col s5  option">
                                        <span class="card-title red-text">` + data[i]["Salsa"] + `</span>
                                        <p>` + data[i]["Categoria"] + `</p>
                                        <p>Precio: $` + data[i]["Precio"] + `</p>
                                    </div>
                                    </a>`;
                        };

                        html += `   </div>
                                    <div class="divider"></div>`;
                    }
                }
                $('#option_soats').html(html);
                totalFOOD = totalFOOD + precioA;
                $('#order_soat').html("");
                mostrarTOTALFOOD();
                pedidos++;
                food = `<div class="row m2" id="food_` + id_ComidaSoat + `_` + pedidos + `">
                            <div class="col s12">
                                <div class="row">
                                    ` + nombre + ` ` + size + ` $` + precioA;
                this.id = pedidos;
            };
        })
        .fail(data => {
            ERRORFATAL()
        });
}

function remove(id, precio) {
    $('#' + id).remove();
    total = total - precio;
    let productos = id.split("_");
    let aux = '';
    let aux2 = productos[2];
    switch (productos[0]) {
        case "bebida":
            aux = drinks.split("-");
            drinks = '';
            break;
        case "promo":
            aux = promos.split("-");
            promos = '';
        case "food":
            aux = foods.split("-");
            foods = '';
            for (let i = 0; i < aux.length; i++) {
                if (aux[i] != '') {
                    auxFood = aux[i].split("(")[1];
                    auxFood = auxFood.split(")")[0];
                    if (auxFood != aux2) {
                        foods += aux[i] + "-";
                    }
                };
            };

            auxSoat1 = soats.split("-");
            soats = '';
            for (let i = 0; i < auxSoat1.length; i++) {
                if (auxSoat1[i] != '') {
                    auxSoat2 = auxSoat1[i].split("(")[1];
                    auxSoat2 = auxSoat2.split(")")[0];
                    if (auxSoat2 != aux2) {
                        soats += auxSoat1[i] + "-";
                    }
                };
            };
            break;
        case "soat":
            aux = soats.split("-");
            soats = '';
            for (let i = 0; i < aux.length; i++) {
                if (aux[i] != '') {
                    auxSoat2 = aux[i].split("(")[1];
                    auxSoat2 = auxSoat2.split(")")[0];
                    if (auxSoat2 != aux2) 
                        soats += aux[i] + "-";
                };
            };
            break;
    }
    for (i = 0; i < aux.length; i++) {
        if (aux[i] == productos[1]) {
            for (let x = 0; x < aux.length; x++) {
                if (x != i) {
                    switch (productos[0]) {
                        case "bebida":
                            drinks += aux[x] + "-";
                            break;
                        case "food":
                            foods += aux[x] + "-";
                            break;
                        case "soat":
                            soats += aux[x] + "-";
                            break;
                    }
                };
            }

            break;
        };
    };
    mostrarTotal();
}

function remove_soat(id, idfood, precio) {
    $('#' + id).remove();
    $('#' + idfood).remove();
    totalFOOD = totalFOOD - precio;
    soat = soat - precio;
    let productos = id.split("_");
    aux = soatsaux.split("-");
    soatsaux = '';
    for (i = 0; i < aux.length; i++) {
        if (aux[i] == productos[1]) {
            for (let x = 0; x < aux.length; x++) {
                if (x != i) 
                    soatsaux += aux[x] + "-";
            }
            break;
        };
    };
    mostrarTOTALFOOD();
}



function rechazar_pedido_request(id_pedido) {
    $('#order_id_cancelar').val(id_pedido);
}

function cancelar_pedido() {
    let id_pedido = $('#order_id_cancelar').val();
    let motivo = $('#cancelar_motivo').val();
    if (id_pedido.lengt < 1 || motivo.length < 1) 
        Materialize.toast('No podes dejar ningún campo vacio.', 2000);
    else {
        $.ajax({
            url: 'api.php',
            type: 'POST',
            data: {
                request: 'cancelar',
                content: [id_pedido, motivo, $('#sacar_caja').prop('checked')]
            }
        })
        .done(function(dataO) {
            let data = parseInt(dataO);
            if (permitido(data)) {
                switch (data) {
                    case 4:
                        Materialize.toast('Ya se canceló este pedido.', 2000);
                        break;
                    case 0:
                        getAllCaja();
                        $('#Pedido' + id_pedido).remove();
                        $('#order_remove').modal('close');
                        $('#sacar_caja').prop('checked', false);
                        $('#cancelar_motivo').val('');
                        break;
                    default:
                        data = JSON.parse(dataO);
                        if (data.length == 1) {
                            $('#sacar_caja').prop('checked', false);
                            $('#order_remove').modal('close');
                            $('.totalCaja').html(data[0]["Caja"]);
                            $('#Pedido' + id_pedido).remove();
                            $('#cancelar_motivo').val('');
                        } else 
                            Materialize.toast('ERROR.', 2000);
                        break;
                }
            };
        })
        .fail(data => {
            ERRORFATAL()
        });
    }
}

function completar_pedido(id_pedido) {
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'finishOrder',
            content: [id_pedido]
        }
    })
    .done(function(dataO) {
        if (permitido(dataO)) {
            let data = parseInt(dataO);
            switch (data) {
                case 4:
                    Materialize.toast('Ya se entregó este pedido.', 2000);
                    break;
                case 0:
                    $('#Pedido' + id_pedido).remove();
                    break;
                default:
                    Materialize.toast('ERROR.', 2000);
                    break;
            }
        };
    })
    .fail(data => {
        ERRORFATAL()
    });
}

function listo_pedido(id_pedido) {
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'readyOrder',
            content: [id_pedido]
        }
    })
    .done(function(dataO) {
        let data = parseInt(dataO);
        if (permitido(data)) {
            switch (data) {
                case 4:
                    Materialize.toast('Ya se finalizó este pedido.', 2000);
                    break;
                case 0:
                    let html = `<div id="Pedido` + id_pedido + `" class="producto">` + $('#Pedido' + id_pedido).html() + `</div>`;
                    $('#Pedido' + id_pedido).remove();
                    if ($('#done_empty').length > 0) {
                        $('#done_empty').fadeOut();
                        $('#done_requests_cards_container').append(html);
                    } else {
                        $('#done_requests_cards_container').append(html);
                    }
                    html = `<a class="black-text modal-trigger" href="#" onclick="completar_pedido(` + id_pedido + `)">Entregado</a>
                            <a class="black-text modal-trigger" href="#no_vino" onclick="no_vino_request(` + id_pedido + `)">No vino</a>
                            <br><a class="black-text modal-trigger" href="#" onclick="Ver_Mas(` + id_pedido + `)">Ver más</a>`;
                    $('#opciones' + id_pedido).html(html);
                    break;
                default:
                    Materialize.toast('ERROR.', 2000);
                    break;
            }
        };
    })
    .fail(data => {
        ERRORFATAL()
    });
}

function verificar_existencia() {
    if ($('#requests_cards_container').find('div').length < 1) 
        $('#del_empty').fadeIn();

    if ($('#queued_requests_cards_container').find('div').length < 1) 
        $('#queue_empty').fadeIn();
}

function no_vino_request(id_pedido) {
    $('#order_id_no_vino').val(id_pedido);
}

function no_vino() {
    let id_pedido = $('#order_id_no_vino').val();
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'no_vino',
            content: [id_pedido]
        }
    })
    .done(function(dataO) {
        let data = parseInt(dataO);
        if (permitido(data)) {
            switch (data) {
                case 4:
                    Materialize.toast('Ya se informó que no se retiró.', 2000);
                    break;
                case 0:
                    $('#no_vino').modal('close');
                    $('#Pedido' + id_pedido).remove();
                    break;
                default:
                    Materialize.toast('ERROR.', 2000);
                    break;
            }
        };
    })
    .fail(data => {
        ERRORFATAL()
    });
}




function todo() {

    //--------COLA
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'viewQueve'
        }
    })
    .done(data => {
        if (permitido(data)) {
            data = JSON.parse(data);
            let html = '';
            if (data.length > 0) {
                data.forEach(item => {
                    html += `   <div id="Pedido` + item["ID_Pedido"] + `" class="producto">
                                    <div class="card z-depth-5" >
                                        <div class="card-content white left-align">
                                        <span class="card-title black-text">
                                            Pedido: #` + item["IDFinal"] + `
                                        </span>
                                        <p>
                                            Precio: $` + item["Total"] + `
                                        </p>
                                        </div>
                                        <div class="card-action center-align amber lighten-2" id="opciones` + item["ID_Pedido"] + `">
                                            <a class="black-text modal-trigger" href="#" onclick="listo_pedido(` + item["ID_Pedido"] + `)">Listo</a>
                                            <a class="black-text modal-trigger" href="#order_remove" onclick="rechazar_pedido_request(` + item["ID_Pedido"] + `)">Cancelar</a>
                                            <br><a class="black-text modal-trigger" href="#" onclick="Ver_Mas(` + item["ID_Pedido"] + `)">Ver más</a>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                                `;
                });
                if ($('#queue_empty').length > 0) {
                    setTimeout(()=> {
                        $('#queue_empty').fadeOut();
                    }, atime);
                    setTimeout(()=> {
                        $('#queued_requests_cards_container').append(html);
                    }, atime * 2);
                } else 
                    $('#queued_requests_cards_container').append(html);
            } else 
                $('#queue_empty').html(NO_ORDERS_QUEVE);
        };
    })
    .fail(data => {
        ERRORFATAL()
    });

    //--------FINALIZADOS
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'viewReady'
        }
    })
    .done(data => {
        if (permitido(data)) {
            data = JSON.parse(data);
            let html = '';
            if (data.length > 0) {
                data.forEach(item => {
                    html += `   <div id="Pedido` + item["ID_Pedido"] + `" class="producto">
                                    <div class="card z-depth-5" >
                                        <div class="card-content white left-align">
                                        <span class="card-title black-text">
                                            Pedido: #` + item["IDFinal"] + `
                                        </span>
                                        <p>
                                            Precio: $` + item["Total"] + `
                                        </p>
                                        </div>
                                        <div class="card-action center-align amber lighten-2" id="opciones` + item["ID_Pedido"] + `">
                                            <a class="black-text modal-trigger" href="#" onclick="completar_pedido(` + item["ID_Pedido"] + `)">Entregado</a>
                                            <a class="black-text modal-trigger" href="#no_vino" onclick="no_vino_request(` + item["ID_Pedido"] + `)">No vino</a>
                                            <br><a class="black-text modal-trigger" href="#" onclick="Ver_Mas(` + item["ID_Pedido"] + `)">Ver más</a>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                                `;
                });
                if ($('#done_empty').length > 0) {
                    setTimeout(()=> {
                        $('#done_empty').fadeOut();
                    }, atime);
                    setTimeout(()=> {
                        $('#done_requests_cards_container').append(html);
                    }, atime * 2);
                } else 
                    $('#done_requests_cards_container').append(html);
            } else 
                $('#done_empty').html(NO_ORDERS_DONE);
        };
    })
    .fail(data => {
        ERRORFATAL()
    });
}


function getDrinks() {
    drinksHTML = '';
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'getDrinks',
            content: [0]
        }
    })
    .done(data => {
        if (permitido(data)) {
            data = JSON.parse(data);
            if (data.length > 0) {
                data.forEach(item => {
                    drinksHTML += `<a class="black-text option" onclick="add_drink('` + item["IDFinal"] + `','` + item["Marca"] + `','` + item["Size"] + `','` + item["Sabor"] + `',` + item["Precio"] + `)">`;
                    drinksHTML += `<div class="row  option">`;
                    drinksHTML += `
                                        <span class="red-text card-title ">` + item["Marca"] + `  ` + item["Size"] + `</span>
                                        <p>Sabor: ` + item["Sabor"] + `</p>
                                        <p>Precio: $` + item["Precio"] + `</p>
                                    </div>
                                    </a>
                                    <div class="divider"></div>`;
                });
            }
        };
    })
    .fail(data => {
        ERRORFATAL()
    });
}

function getFoods() {
    foodsHTML = '';
    $.ajax({
        url: 'api.php',
        type: 'POST',
        data: {
            request: 'getFoods',
            content: [0]
        }
    })
    .done(data => {
        if (permitido(data)) {
            data = JSON.parse(data);
            if (data.length > 0) {
                data.forEach(item => {
                    foodsHTML += `  <a class="black-text modal-trigger option" onclick="add_soats('` + item["IDFinal"] + `',` + item["Precio"] + `,'` + item["Comida"] + `','` + item["Size"] + `','` + item["ID"] + `')" href="#add_soats">`;
                    foodsHTML += `  <div class="row  option">`;
                    foodsHTML += `
                                        <span class="red-text card-title ">` + item["Comida"] + `  ` + item["Size"] + `</span>
                                        <p>Precio: $` + item["Precio"] + `</p>
                                    </div>
                                    </a>
                                    <div class="divider"></div>`;
                });
            }
        };
    })
    .fail(data => {
        ERRORFATAL()
    });
}