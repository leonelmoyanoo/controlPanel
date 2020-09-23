$('document').ready(()=>{

  $('select').material_select();
  $('ul.tabs').tabs('select_tab', 'tab_id');
  $('.collapsible').collapsible();  
  $('.carousel').carousel();
  $('.tooltipped').tooltip();
  $('.modal').modal();
  $('.button-collapse').sideNav();
  $('.button-collapse').sideNav({
      menuWidth: 300, // Default is 300
      edge: 'left', // Choose the horizontal origin
      closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
      draggable: true, // Choose whether you can drag to open on touch screens,
      onOpen: function(el) { /* Do Stuff */ }, // A function to be called when sideNav is opened
      onClose: function(el) { /* Do Stuff*/ }, // A function to be called when sideNav is closed
    }
  );

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
});

const noDisponible = () => {
  Materialize.toast("Procedimiento no disponible",2000);
}

const permitido = data => {
  data = parseInt(data);
  let bandera = false;
  switch (data) {
    case 1:
      Materialize.toast('ERROR.',2000 );
      break;
    case 2:
      Materialize.toast('No tenés permitido el acceso.',2000 );
      setTimeout(()=> {
        window.location = 'index.php';
      }, 1000);
      break;
    default:
      bandera = true;
      break;
  }
  return bandera;
}
const ERRORFATAL = () => {
  Materialize.toast('Hubo un problema al conectarse con el servidor, ¿estás conectado a la red?',2000 );
}