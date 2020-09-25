<?php

  ini_set('display_errors', 1);
  require_once('config.php');
  /*
  // Public API.
  //
  // Possible statuses:
  // 0 -> Pass.
  // 1 -> General error/invalid request.
  // 2 -> Not allowed.
  // 3 -> Empety.
  // 4 -> Already registered or exist.
  // * -> Custom status(es).
  */
  define('PASS', 0);
  define('ERROR', 1);
  define('NOT_ALLOWED', 2);
  define('EMPETY', 3);
  define('ALREADY_REGISTERED',4);

  //Default values because it's a simple test
  //These constants should be change by $_SESSION (for example) 
  //The id refers to their respective id in the database, it's different in a real situation
  define('IDSUCURSAL',1);
  define('IDCAJA',1);
  define('IDENCARGADO',1);


#QUERY
  function INSERT($tabla,$columnas,$valores){
    $sql = "INSERT INTO ".$tabla."(".$columnas.") VALUES (".$valores.")";
    $lookup = $GLOBALS['server']->query($sql);
    return $lookup;
  }

  function UPDATE ($tabla,$valores,$donde){
    $sql = "UPDATE ".$tabla." SET ".$valores." WHERE ".$donde;
    $lookup = $GLOBALS['server']->query($sql);
    return $lookup;
  }

  function DELETE ($tabla,$donde){
    $sql = "DELETE FROM ".$tabla." WHERE ".$donde;
    $lookup = $GLOBALS['server']->query($sql);
    return $lookup;
  }
  function Consulta($sql){
    if($lookup = $GLOBALS['server']->query($sql)){
        print json_encode($lookup->fetchAll());
    }else{print ERROR;}
  }
#CHECK
  function Existe($sql){
    $valor = false;
    if ($lookup = $GLOBALS['server']->query($sql)) 
      $valor =  $lookup->rowCount()==1;
    return $valor;
  }
#FUNCTIONS
  function Pedidos($content,$donde2,$update){
    if (count($_POST['content'])==$content) {
      $idOrden = clean($_POST['content'][0]);
      $donde = 'ID_Sucursal = '.IDSUCURSAL.'
                      AND 
                      FH_Entregado IS NULL
                      AND
                      Cancelado IS NULL
                      AND
                      FH_Inicio IS NOT NULL
                      AND
                      '.$donde2.'
                      AND
                      ID_PedidoAI='.$idOrden;
      $sql = "SELECT PrecioFinal FROM  ".$GLOBALS['tables']['orders']."
              where ".$donde;
      $lookup = $GLOBALS['server']->query($sql);

      if ($lookup && $lookup->rowCount() == 1) {
        if (UPDATE($GLOBALS['tables']['orders'],$update,$donde)) {
          if (isset($_POST['content'][2])) {
            if ($_POST['content'][2]=='true') {
              $update = "Total=Total-". $lookup->fetch()['PrecioFinal'];
              if (UPDATE($GLOBALS['tables']['caja'],$update,dondeCaja())) {
                $sql = 'SELECT Total
                        FROM '.$GLOBALS['tables']["caja"].'
                        WHERE '.dondeCaja();
                if ($lookup=$GLOBALS['server']->query($sql)) {
                  $arreglo = array('Caja' => $lookup->fetch()["Total"]);
                  print json_encode([$arreglo]);
                }else{print ERROR;}
              }else{print ERROR;}
            }else{print PASS;}
          }else{print PASS;}
        }else{
          print ERROR;
        }
      }else{print ALREADY_REGISTERED;}
    }else{print NOT_ALLOWED;}
  }

  function Month($fecha){
    $meses = array('January' => 1,
                'February' => 2,
                'March' => 3,
                'April' => 4,
                'May' => 5,
                'June' => 6,
                'July' => 7,
                'August' => 8,
                'September' => 9,
                'October' => 10, 
                'November'  => 11,
                'December' => 12);
    $aux="'".$fecha."'";
    if (count(explode(",",$fecha))==2) {
      $aux = explode(",",$fecha)[0].explode(",",$fecha)[1];
      $aux = explode(" ",$aux);
      $aux="'".$aux[2]."-".$meses[$aux[1]]."-".$aux[0]."'";
    }
    return $aux;
  }

  function dia($dia){
    switch ($dia) {
      case 'monday':
        $dia = "Lunes";
      break;
      case 'tuesday':
        $dia = "Martes";
      break;
      case 'wednesday':
        $dia = "Miercoles";
      break;
      case 'thursday':
        $dia = "Jueves";
      break;
      case 'friday':
        $dia = "Viernes";
      break;
      case 'saturday':
        $dia = "Sabado";
      break;
      case 'sunday':
        $dia = "Domingo";
      break;
      return $dia;
    }
  }

  function Filtrar_Categoria($columna){
    $categoria = clean($_POST['content'][0]);
    $donde = '';
    if ($categoria>0)
      $donde = ' WHERE '.$columna.' = '.$categoria;
    return $donde;
  }

  function dondeCaja(){
    return 'ID_Sucursal = '.IDSUCURSAL.'
            AND
            FH_Cierre IS NULL
            AND
            ID_Caja = '.IDCAJA;
  }
  function clean($dato) {
    $clean;
    if (is_array($dato)) {
      foreach ($dato as $value) {
        $value = clean($value);
      }
    } else {
      $dato = $GLOBALS['server']->quote($dato);
    }
    $clean = $dato;
    return $clean;
  }

#CODIGO
  if (isset($_POST['request']))
  {
    if (isset($_POST['content']) && is_array($_POST['content']))
    {
      switch ($_POST['request']) {
#CONTENT
  #GASTOS
          case 'addExpenses':
            if (count($_POST['content'])==3) {
              $total = 0;
                for ($i=0; $i < count($_POST['content'][0]); $i++) { 
                  $product = clean($_POST['content'][0][$i]);
                  $sql = 'SELECT ID_Producto
                          FROM  '.$tables["expenses_products"].'
                          WHERE Producto = '.$product;
                  if($lookup = $GLOBALS['server']->query($sql)){
                    $id = $lookup->fetch()['ID_Producto'];
                    if (!$id) {
                      INSERT($tables["expenses_products"],'', 'NULL,'.$product);
                      $sql = 'SELECT ID_Producto
                              FROM  '.$tables["expenses_products"].'
                              WHERE Producto = '.$product;
                      $lookup = $GLOBALS['server']->query($sql);
                      $id = $lookup->fetch()['ID_Producto'];
                    }
                    $precio = $_POST['content'][1][$i];
                    $cantidad = $_POST['content'][2][$i];
                    $valores = IDSUCURSAL.',';
                    $valores .= IDENCARGADO.',';
                    $valores .= $id.',';
                    $valores .= $precio.',';
                    $valores .= $cantidad;
                    $columnas = "ID_Sucursal,
                              ID_Encargado,
                              ID_Producto,
                              Precio,
                              Cantidad";
                    if (!INSERT($tables["expenses"],$columnas,$valores)) {
                      print ERROR;
                      exit();
                    }else{ $total +=$precio*$cantidad;}
                  }else{print ERROR;}
                  if (UPDATE($tables["caja"],'Total = Total - '.$total, dondeCaja())) {
                    print PASS;
                  }else{print ERROR;}
                }
            }else{print NOT_ALLOWED;}
          break;
  #CAJA
          case 'getCajaSucursal':
            if (count($_POST['content'])==1) {
              $id = clean($_POST['content'][0]);
              $sql = 'SELECT
                    Total,ID_Sucursal,DATE_FORMAT(c.FH_Apertura,"%d-%m-%Y") AS "DIA" 
                    FROM ' .$tables['caja'] . ' c
                    WHERE ID_Caja = '.$id;
              if ($lookup=$GLOBALS['server']->query($sql)) {
                if ($lookup->rowCount()==1) {
                  $datos = $lookup->fetchall();
                  $fecha = $datos[0]["DIA"];
                  $total = $datos[0]["Total"];
                  $gastos = '';
                  $pedidos = '';
                  $caja = '';
                  $sql='SELECT 
                        CONCAT(e.Nombres," ",e.Apellidos) AS "Encargado",
                        g.Precio AS "Precio",
                        g.Cantidad AS "Cantidad",
                        g.Precio*g.Cantidad AS "Total",
                        gp.Producto AS "Producto",
                        CONCAT(g.FH_Gasto," (", 
                                  CASE 
                                    WHEN DAYNAME(g.FH_Gasto) = "Monday"
                                      THEN "Lunes"
                                    WHEN DAYNAME(g.FH_Gasto) = "Tuesday"
                                      THEN "Martes"
                                    WHEN DAYNAME(g.FH_Gasto) = "Wednesday"
                                      THEN "Miercoles"
                                    WHEN DAYNAME(g.FH_Gasto) = "Thursday"
                                      THEN "Jueves"
                                    WHEN DAYNAME(g.FH_Gasto) = "Friday"
                                      THEN "Viernes"
                                    WHEN DAYNAME(g.FH_Gasto) = "Saturday"
                                      THEN "Sabado"
                                    WHEN DAYNAME(g.FH_Gasto) = "Sunday"
                                      THEN "Domingo"
                                    END
                                  ,")") AS "FH"
                        FROM ' .$tables['expenses'] . ' g
                        INNER JOIN ' .$tables['expenses_products'] . ' gp
                        ON g.ID_Producto = gp.ID_Producto
                        INNER JOIN ' .$tables['employees'] . ' e
                        ON g.ID_Encargado = e.ID_Usuario
                        WHERE
                          g.ID_Sucursal = '.IDSUCURSAL.'
                        ORDER BY g.FH_Gasto DESC';
                  if ($lookup=$GLOBALS['server']->query($sql)) {
                    if ($lookup->rowCount()>=1) {
                      $gastos = $lookup->fetchall();
                    }
                  }else{print ERROR;}

                    $sql = 'SELECT CASE 
                                    WHEN TotalCierre IS NOT NULL
                                    THEN  
                                      CASE 
                                        WHEN TotalCierre = 0
                                        THEN CONCAT(0,"($",Total,")")
                                        ELSE CONCAT(TotalCierre,"($",TotalCierre+1000,")")
                                      END
                                    ELSE 2
                                  END AS "Cierre",
                                  CONCAT(e.Nombres," ",e.Apellidos) AS "Encargado",
                                  c.Total AS "Total",
                                  CONCAT(c.FH_Apertura," (", 
                                    CASE 
                                      WHEN DAYNAME(c.FH_Apertura) = "Monday"
                                        THEN "Lunes"
                                      WHEN DAYNAME(c.FH_Apertura) = "Tuesday"
                                        THEN "Martes"
                                      WHEN DAYNAME(c.FH_Apertura) = "Wednesday"
                                        THEN "Miercoles"
                                      WHEN DAYNAME(c.FH_Apertura) = "Thursday"
                                        THEN "Jueves"
                                      WHEN DAYNAME(c.FH_Apertura) = "Friday"
                                        THEN "Viernes"
                                      WHEN DAYNAME(c.FH_Apertura) = "Saturday"
                                        THEN "Sabado"
                                      WHEN DAYNAME(c.FH_Apertura) = "Sunday"
                                        THEN "Domingo"
                                      END
                                    ,")") AS FHA, 
                                  CASE WHEN c.FH_Cierre IS NOT NULL THEN
                                    CONCAT(c.FH_Cierre," (", 
                                    CASE 
                                      WHEN DAYNAME(c.FH_Cierre) = "Monday"
                                        THEN "Lunes"
                                      WHEN DAYNAME(c.FH_Cierre) = "Tuesday"
                                        THEN "Martes"
                                      WHEN DAYNAME(c.FH_Cierre) = "Wednesday"
                                        THEN "Miercoles"
                                      WHEN DAYNAME(c.FH_Cierre) = "Thursday"
                                        THEN "Jueves"
                                      WHEN DAYNAME(c.FH_Cierre) = "Friday"
                                        THEN "Viernes"
                                      WHEN DAYNAME(c.FH_Cierre) = "Saturday"
                                        THEN "Sabado"
                                      WHEN DAYNAME(c.FH_Cierre) = "Sunday"
                                        THEN "Domingo"
                                      END
                                    ,")") 
                                    ELSE
                                    "No se cerró"
                                  END AS FHC
                            FROM '.$tables["caja"].' c
                            INNER JOIN ' .$tables['employees'] . ' e
                            ON e.ID_Usuario =c.ID_Encargado
                            WHERE c.ID_Sucursal = '.IDSUCURSAL.' 
                            ORDER BY c.FH_Cierre DESC';

                      if ($lookup=$GLOBALS['server']->query($sql)) {
                        if ($lookup->rowCount()>=1) {
                          $caja = $lookup->fetchall();
                        }
                      }else{print ERROR;}
                    $sql='SELECT 
                          p.PrecioFinal AS "Total",
                          p.ID_PedidoAI AS "ID",
                          CONCAT(e.Nombres," ",e.Apellidos) AS "Encargado",
                          CONCAT(p.FH_Inicio," (", 
                                CASE 
                                  WHEN DAYNAME(p.FH_Inicio) = "Monday"
                                    THEN "Lunes"
                                  WHEN DAYNAME(p.FH_Inicio) = "Tuesday"
                                    THEN "Martes"
                                  WHEN DAYNAME(p.FH_Inicio) = "Wednesday"
                                    THEN "Miercoles"
                                  WHEN DAYNAME(p.FH_Inicio) = "Thursday"
                                    THEN "Jueves"
                                  WHEN DAYNAME(p.FH_Inicio) = "Friday"
                                    THEN "Viernes"
                                  WHEN DAYNAME(p.FH_Inicio) = "Saturday"
                                    THEN "Sabado"
                                  WHEN DAYNAME(p.FH_Inicio) = "Sunday"
                                    THEN "Domingo"
                                  END
                                ,")") AS "Inicio",
                          case 
                            WHEN p.FH_Entregado IS NOT NULL then
                              CONCAT(p.FH_Entregado," (", 
                                        CASE 
                                          WHEN DAYNAME(p.FH_Entregado) = "Monday"
                                            THEN "Lunes"
                                          WHEN DAYNAME(p.FH_Entregado) = "Tuesday"
                                            THEN "Martes"
                                          WHEN DAYNAME(p.FH_Entregado) = "Wednesday"
                                            THEN "Miercoles"
                                          WHEN DAYNAME(p.FH_Entregado) = "Thursday"
                                            THEN "Jueves"
                                          WHEN DAYNAME(p.FH_Entregado) = "Friday"
                                            THEN "Viernes"
                                          WHEN DAYNAME(p.FH_Entregado) = "Saturday"
                                            THEN "Sabado"
                                          WHEN DAYNAME(p.FH_Entregado) = "Sunday"
                                            THEN "Domingo"
                                          END
                                        ,")")
                            ELSE "No se entregó" 
                          END AS "FH",
                          CASE 
                            WHEN p.Regalo IS NOT NULL THEN
                              CONCAT("Regalado a: ",p.Regalo)
                            ELSE
                              "Pagado"
                          END "Regalo",
                          CASE 
                            WHEN p.Cancelado IS NOT NULL
                            THEN CONCAT("Se canceló porque: ",p.Cancelado)
                            ELSE ""
                          END AS "Cancelado"
                          FROM ' .$tables['orders'] . ' p
                          INNER JOIN ' .$tables['employees'] . ' e
                          ON p.ID_Encargado = e.ID_Usuario
                          WHERE
                            p.ID_Sucursal = '.IDSUCURSAL.'
                          ORDER BY p.FH_Inicio DESC';
                    if ($lookup=$GLOBALS['server']->query($sql)) {
                      if ($lookup->rowCount()>=1) {
                        $pedidos = $lookup->fetchall();
                      }
                    }else{print ERROR;}

                    $vector = [$gastos,$pedidos,$total,$caja];
                    print json_encode($vector);
                  }else{print ERROR;}
                }else{print ERROR;}
              }else{print NOT_ALLOWED;}
            break;   
  #BUSCAR PRODUCTO
            case 'searchSoatUse':
              if (count($_POST['content'])==1) {
                $id = clean($_POST['content'][0]);
                $sql = 'SELECT * FROM '.$tables["soats_use"].'
                        WHERE ID_Salsa = '.$id;
                Consulta($sql);
              }else{print NOT_ALLOWED;}
            break;
            case 'searchSoat':
              if (count($_POST['content'])==1) {
                $id = clean($_POST['content'][0]);
                $sql = 'SELECT 
                        s.Nombre as "Nombre",
                        s.ID_Salsa as "IDFinal",
                        case
                          WHEN p1.Precio IS NOT NULL 
                                AND
                              p2.Precio IS NOT NULL 
                                AND
                              p3.Precio IS NOT NULL 
                                AND
                              p4.Precio IS NOT NULL 
                              THEN CONCAT("$",p1.Precio,"-$",p2.Precio,"-$",p3.Precio,"-$",p4.Precio)
                          WHEN p1.Precio IS NOT NULL 
                                AND
                              p2.Precio IS NOT NULL 
                                AND
                              p3.Precio IS NOT NULL 
                              THEN CONCAT("$",p1.Precio,"-$",p2.Precio,"-$",p3.Precio)
                          WHEN p1.Precio IS NOT NULL 
                                AND
                              p2.Precio IS NOT NULL 
                              THEN CONCAT("$",p1.Precio,"-$",p2.Precio)
                          ELSE
                              "SIN CARGO" 
                          END as "Precios", 
                        c.Categoria as "Categoria"
                        FROM '.$tables["soats"].' s
                        INNER JOIN '.$tables["category"].' c ON
                        c.ID_Categoria = s.ID_Categoria

                        LEFT JOIN '.$tables["soats_price"].' p1 ON
                        p1.ID_SalsaCategoria = c.ID_Categoria

                        LEFT JOIN '.$tables["soats_price"].' p2 ON
                        p2.ID_SalsaCategoria = c.ID_Categoria
                        AND p2.Precio<>p1.Precio AND p2.Precio>p1.Precio

                        LEFT JOIN '.$tables["soats_price"].' p3 ON
                        p3.ID_SalsaCategoria = c.ID_Categoria
                        AND p3.Precio<>p1.Precio
                        AND p3.Precio<>p2.Precio AND p3.Precio>p2.Precio

                        LEFT JOIN '.$tables["soats_price"].' p4 ON
                        p4.ID_SalsaCategoria = c.ID_Categoria
                        AND p4.Precio<>p1.Precio
                        AND p4.Precio<>p2.Precio 
                        AND p4.Precio<>p3.Precio AND p4.Precio>p3.Precio
                        
                        

                        WHERE s.ID_Salsa = '.$id.'
                        LIMIT 1';
                Consulta($sql);
              }else{print NOT_ALLOWED;}
            break;
            case 'searchPromo':
              if (count($_POST['content'])==1) {
                $id = clean($_POST['content'][0]);
                $sql = 'SELECT Nombre,Precio,Nombre as "aux"
                        FROM '.$tables["promos_price"].' 
                        WHERE ID_Promo ='.$id;
                if ($lookup=$GLOBALS['server']->query($sql)) {
                  $datos = $lookup->fetchall();
                  for ($i=0; $i < count($datos); $i++) { 
                    $datos[$i]['aux']='00';
                  }
                  print json_encode($datos);
                }else{print ERROR;}
              }else{
                print NOT_ALLOWED;
              }
            break;
            case 'searchDrink':
              if (count($_POST['content'])==1) {
                $id = clean($_POST['content'][0]);
                $sql = 'SELECT 
                        f.ID_Bebida as "IDFinal",
                        f.ID_Bebida as "aux",
                        m.Marca as "Marca",
                        e.Envase as "Envase",
                        s.Sabor as "Sabor",
                        CASE
                          WHEN t.Mililitros/1000>=1 THEN CONCAT(t.Mililitros,"ml")
                        END AS "Size",
                        f.Precio as "Precio",
                        c.Categoria as "Categoria"
                        FROM '.$tables["drink_final"].' f
                        INNER JOIN '.$tables["drink_conteiner"].' e ON
                        e.ID_Envases = f.Envase
                        INNER JOIN '.$tables["drink_size"].' t ON
                        t.ID_Size = f.ID_Size
                        INNER JOIN '.$tables["drink_brands"].' m ON
                        m.ID_Marca = f.marca
                        INNER JOIN '.$tables["category"].' c ON
                        c.ID_Categoria = f.ID_Categoria
                        LEFT JOIN '.$tables["drink_taste"].' s ON
                        s.ID_Sabor = f.Sabor
                        WHERE f.ID_Bebida='.$id;
                if ($lookup=$GLOBALS['server']->query($sql)) {
                  $datos = $lookup->fetchall();
                  for ($i=0; $i < count($datos); $i++) { 
                    $datos[$i]['aux']='00';
                  }
                  print json_encode($datos);
                }else{print ERROR;}
              }else{print NOT_ALLOWED;}
            break;
            case 'searchFood':
              if (count($_POST['content'])==1) {
                $id = clean($_POST['content'][0]);
                $sql = 'SELECT 
                        f.ID_ComidaFinal as "IDFinal",
                        f.ID_ComidaFinal as "aux",
                        f.Precio as "Precio",
                        c.Categoria as "Categoria",
                        t.Nombre as "Nombre"
                        FROM '.$tables["food_final"].' f
                        INNER JOIN '.$tables["food"].' t ON
                        t.ID_Comida = f.ID_Comida
                        INNER JOIN '.$tables["category"].' c ON
                        c.ID_Categoria =f.ID_Categoria 
                        WHERE f.ID_ComidaFinal ='.$id;
                if ($lookup=$GLOBALS['server']->query($sql)) {
                  $datos = $lookup->fetchall();
                  for ($i=0; $i < count($datos); $i++) { 
                    $datos[$i]['aux']='00';
                  }
                  print json_encode($datos);
                }else{print ERROR;}
              }else{print NOT_ALLOWED;}
            break;
  #PRODUCTOS
          case 'getSomeSoats':
            if (count($_POST['content'])==3 ) {
              $soats = clean($_POST['content'][0]);
              $id_comida = clean($_POST['content'][1]);
              $pedidos = clean($_POST['content'][2]);
              $mostrar = '';
              $id = '';
              for ($i=0; $i < count($soats); $i++) { 
                $sql = 'SELECT
                        s.Nombre as "Salsa",
                        c.Categoria as "Categoria",
                        sp.Precio AS "Precio"

                        FROM ' . $tables['soats'].' s 
                        
                        INNER JOIN '.$tables['category'].' c  ON 
                        c.ID_Categoria=s.ID_Categoria
                        
                        INNER JOIN '.$tables['food_final'].' ff  ON 
                        ff.ID_ComidaFinal='.$id_comida.'

                        INNER JOIN ' . $tables['soats_price'].' sp ON
                        sp.ID_ComidaCategoria = ff.ID_Categoria AND
                        sp.ID_SalsaCategoria = s.ID_Categoria

                        WHERE s.ID_Salsa = '.$soats[$i];
                if($lookup = $GLOBALS['server']->query($sql)) {
                  $DatosSalsa = $lookup->fetchall();
                  $mostrar .= '<div class="row" id="soatfood_'.$soats[$i].'_'.$pedidos.'">
                      '.$DatosSalsa[0]["Salsa"].' $'.$DatosSalsa[0]["Precio"].' ('.$DatosSalsa[0]["Categoria"].')</div>';
                  $id .= $soats[$i].'('.$pedidos.')-';
                }
              }
              $array = array('Mostrar' => $mostrar, 'id' => $id);
              print json_encode($array);
            }else{print NOT_ALLOWED;}
          break;
          case 'getSoat':
            if (count($_POST['content'])==2) {
              $id_comida = clean($_POST['content'][0]);
              $id = clean($_POST['content'][1]);
              $sql = 'SELECT
                    s.ID_Salsa as "IDFinal",
                    s.Nombre as "Salsa",
                    c.Categoria as "Categoria",
                    s.ID_Categoria as "ID_Categoria",
                    sp.Precio AS "Precio"

                    FROM ' . $tables['soats'].' s 
                    
                    INNER JOIN '.$tables['category'].' c  ON 
                    c.ID_Categoria=s.ID_Categoria
                    
                    INNER JOIN '.$tables['food_final'].' ff  ON 
                    ff.ID_ComidaFinal='.$id_comida.'

                    INNER JOIN ' . $tables['soats_price'].' sp ON
                    sp.ID_ComidaCategoria = ff.ID_Categoria AND
                    sp.ID_SalsaCategoria = s.ID_Categoria AND
                    sp.ID_Comida='.$id.'

                    ORDER BY s.ID_Categoria DESC';
              Consulta($sql);
            }else{print NOT_ALLOWED;}
          break;
          case 'getDrink':
            if (count($_POST['content'])==1) {
              $id_comida = clean($_POST['content'][0]);
              $sql = "SELECT 
                      d.Precio AS 'Precio',
                      d.ID_Bebida AS 'IDfinal',
                      b.Marca as 'Marca',
                      ds.Mililitros AS 'Size',
                      c.Categoria AS 'Tipo',
                      dc.Envase AS 'Envase',
                      dt.Sabor AS 'Sabor'
                      FROM ".$tables['drink_final']." d

                      INNER JOIN ".$tables['drink_brands']." b
                      ON b.ID_Marca=d.Marca

                      INNER JOIN ".$tables['drink_size']." ds
                      ON ds.ID_Size=d.ID_Size

                      INNER JOIN ".$tables['category']." c
                      ON c.ID_Categoria=d.ID_Categoria

                      INNER JOIN ".$tables['drink_conteiner']." dc
                      ON dc.ID_Envases=d.Envase

                      LEFT JOIN ".$tables['drink_taste']." dt
                      ON dt.ID_Sabor=d.Sabor";
              Consulta($sql);
            }else{print NOT_ALLOWED;}
              
          break;
          case 'getFood':
            if (count($_POST['content'])==1) {
              $sql = "SELECT 
                      f.Nombre AS 'Comida',
                      ff.Precio AS 'Precio',
                      ff.ID_ComidaFinal AS 'IDfinal',
                      c.Categoria AS 'Size',
                      sp.Precio AS 'Precio_salsa',
                      s.Categoria AS 'Salsa'
                      FROM ".$tables['food_final']." ff

                      INNER JOIN ".$tables['category']." c
                      ON c.ID_Categoria=ff.ID_Categoria 

                      INNER JOIN ".$tables['food']." f
                      ON f.ID_Comida=ff.ID_Comida

                      INNER JOIN ".$tables['soats_price']." sp
                      ON sp.ID_ComidaCategoria=c.ID_Categoria

                      INNER JOIN ".$tables['category']." s
                      ON s.ID_Categoria=sp.ID_SalsaCategoria";
              Consulta($sql);
            }else{print NOT_ALLOWED;}
          break;
          case 'getPromos':
            if (count($_POST['content'])==1) {
              $sql = 'SELECT  * FROM ' . $tables['promos_price'];
              Consulta($sql);
            }else{print NOT_ALLOWED;}
          break;
          case 'getSoats':
            if (count($_POST['content'])==1) {
              $categoria = clean($_POST['content'][0]);
              $sql = 'SELECT
                    s.ID_Salsa as "IDFinal",
                    s.Nombre as "Salsa",
                    c.Categoria as "Categoria"
                    FROM ' . $tables['soats'].' s
                    INNER JOIN ' . $tables['category'].' c ON 
                    c.ID_Categoria = s.ID_Categoria
                      '.Filtrar_Categoria("s.ID_Categoria");
              Consulta($sql);
            }else{print NOT_ALLOWED;}
          break;
          case 'getFoods':
            if (count($_POST['content'])==1) {
              $categoria = clean($_POST['content'][0]);
              $sql = "SELECT 
                      f.Nombre AS 'Comida',
                      ff.Precio AS 'Precio',
                      ff.ID_ComidaFinal AS 'IDFinal',
                      c.Categoria AS 'Size',
                      ff.ID_Comida as 'ID'
                      FROM ".$tables['food_final']." ff

                      INNER JOIN ".$tables['category']." c
                      ON c.ID_Categoria=ff.ID_Categoria 

                      INNER JOIN ".$tables['food']." f
                      ON f.ID_Comida=ff.ID_Comida
                      ".Filtrar_Categoria('ff.ID_Categoria');
              Consulta($sql);
            }else{print NOT_ALLOWED;}
          break;
          case 'getDrinks':
            if (count($_POST['content'])==1) {
              $categoria = clean($_POST['content'][0]);
              $sql = "SELECT 
                      d.Precio AS 'Precio',
                      d.ID_Bebida AS 'IDFinal',
                      b.Marca as 'Marca',
                      CASE
                        WHEN ROUND((ds.Mililitros-500)/1000)>=1 THEN CONCAT(ROUND((ds.Mililitros-500)/1000),'L')
                        ELSE CONCAT(ds.Mililitros,'ml')
                      END AS 'Size',
                      c.Categoria AS 'Tipo',
                      dc.Envase AS 'Envase',
                      CASE 
                        WHEN dt.Sabor IS NULL THEN ''
                        ELSE dt.Sabor
                      END AS 'Sabor'
                      FROM ".$tables['drink_final']." d

                      INNER JOIN ".$tables['drink_brands']." b
                      ON b.ID_Marca=d.Marca

                      INNER JOIN ".$tables['drink_size']." ds
                      ON ds.ID_Size=d.ID_Size

                      INNER JOIN ".$tables['category']." c
                      ON c.ID_Categoria=d.ID_Categoria

                      INNER JOIN ".$tables['drink_conteiner']." dc
                      ON dc.ID_Envases=d.Envase

                      LEFT JOIN ".$tables['drink_taste']." dt
                      ON dt.ID_Sabor=d.Sabor
                      ".Filtrar_Categoria('d.ID_Categoria');
              Consulta($sql);
            }else{print NOT_ALLOWED;}
          break;
  #ORDENES/PEDIDOS
            case 'ProductosVendidos': 
              if (count($_POST['content'])==1) {
                $htmlF =  '';
                $htmlD =  '';
                $htmlP =  '';
                $htmlS = '';
                $dondeT = clean($_POST['content'][0])<>0?'':'';
                $dondeS=IDSUCURSAL>0?' AND op.ID_Sucursal='.IDSUCURSAL:'';
                $sql = 'SELECT 
                        COUNT(*) as "Total",
                        f.Nombre AS "Comida",
                        c.Categoria AS "Size" 
                        FROM '.$tables["orders"].' op
                        INNER JOIN ' .$tables["order_Products"] . ' o
                        ON o.ID_PedidoAI = op.ID_PedidoAI 
                        INNER JOIN ' .$tables["food_final"] . ' ff 
                        ON  o.ID_Producto = ff.ID_ComidaFinal
                        INNER JOIN ' .$tables["category"] . ' c 
                        ON c.ID_Categoria=ff.ID_Categoria
                        INNER JOIN '.$tables["food"].' f
                                      ON f.ID_Comida=ff.ID_Comida
                        WHERE 
                              o.Producto=2
                              AND 
                              op.FH_LISTO IS NOT NULL
                              '.$dondeS.'
                        GROUP BY f.Nombre,c.Categoria';
                if ($lookup=$GLOBALS['server']->query($sql)){
                  $comidas = $lookup->fetchall();
                  for ($i=0; $i < $lookup->rowCount(); $i++) { 
                    $htmlF.= '#'.$comidas[$i]["Comida"].' '.$comidas[$i]["Size"].' <br><div col s10 offset-s2">Vendido: '.$comidas[$i]["Total"].' </div><br>';
                  }
                }else{print ERROR;break;}
                $sql = 'SELECT 
                        COUNT(*) as "Total",
                        s.Nombre as "Salsa",
                        c.Categoria as "Categoria"
                        FROM '.$tables["orders"].' op
                        INNER JOIN ' .$tables["order_Products"] . ' o
                        ON o.ID_PedidoAI = op.ID_PedidoAI 
                        INNER JOIN ' .$tables["soats"] . ' s
                        ON  o.ID_Producto = s.ID_Salsa
                        INNER JOIN ' .$tables["category"] . ' c 
                        ON c.ID_Categoria=s.ID_Categoria
                        WHERE 
                              o.Producto=3
                              AND 
                              op.FH_LISTO IS NOT NULL
                              '.$dondeS.'
                        GROUP BY s.Nombre,c.Categoria';
                if ($lookup=$GLOBALS['server']->query($sql)){
                  $salsas = $lookup->fetchall();
                  for ($i=0; $i < $lookup->rowCount(); $i++) { 
                    $htmlS.= '#'.$salsas[$i]["Salsa"].' '.$salsas[$i]["Categoria"].' <br><div col s10 offset-s2">Vendido: '.$salsas[$i]["Total"].' </div><br>';
                  }
                }else{print ERROR;break;}
                $sql = 'SELECT 
                        COUNT(*) as "Total",
                        o.ID_Producto as "ID",
                        b.Marca AS "Marca",
                        CASE
                          WHEN ROUND((ds.Mililitros-500)/1000)>=1 THEN CONCAT(ROUND((ds.Mililitros-500)/1000),"L")
                          ELSE CONCAT(ds.Mililitros,"ml")
                        END AS "Size",
                        dc.Envase AS "Envase",
                        CASE 
                          WHEN dt.Sabor IS NULL THEN ""
                          ELSE dt.Sabor
                        END AS "Sabor" 
                        FROM '.$tables["orders"].' op
                        INNER JOIN ' .$tables["order_Products"] . ' o
                        ON o.ID_PedidoAI = op.ID_PedidoAI 
                        INNER JOIN ' .$tables["drink_final"] . ' d
                        ON  o.ID_Producto = d.ID_Bebida
                        INNER JOIN '.$tables["drink_brands"].' b
                        ON b.ID_Marca=d.Marca

                        INNER JOIN '.$tables["drink_size"].' ds
                        ON ds.ID_Size=d.ID_Size

                        LEFT JOIN '.$tables["drink_taste"].' dt
                        ON dt.ID_Sabor=d.Sabor
                        INNER JOIN '.$tables["drink_conteiner"].' dc
                                      ON dc.ID_Envases=d.Envase
                        INNER JOIN ' .$tables["category"] . ' c 
                        ON c.ID_Categoria=d.ID_Categoria
                        WHERE 
                              o.Producto=1
                              AND 
                              op.FH_LISTO IS NOT NULL
                              '.$dondeS.'
                        GROUP BY 
                          o.ID_Producto,dt.Sabor,b.Marca,ds.Mililitros,dc.Envase,c.Categoria';
                if ($lookup=$GLOBALS['server']->query($sql)){
                  $Total = $lookup->rowCount();
                  $bebidas = $lookup->fetchall();
                  for ($i=0; $i < $Total; $i++) { 
                    $sum = 0;
                    $sql = 'SELECT Cantidad
                        FROM '.$tables["orders"].' op
                        INNER JOIN ' .$tables["order_Products"] . ' o
                        ON o.ID_PedidoAI = op.ID_PedidoAI 
                        WHERE o.Producto=1 
                              AND 
                              op.FH_LISTO IS NOT NULL
                              AND
                              o.ID_Producto ='.$bebidas[$i]["ID"].'
                              '.$dondeS;
                    if ($lookup=$GLOBALS['server']->query($sql)) {
                      $aux = $lookup->fetchall();
                      for ($x=0; $x < $lookup->rowCount(); $x++) {
                        if ($aux[$x]['Cantidad']>1) {
                          $sum+=$aux[$x]['Cantidad']-1;
                        }
                      }
                      if ($sum>0) {
                        $bebidas[$i]['Total']+=$sum;
                      }
                    }
                    $htmlD.= '#'.$bebidas[$i]["Marca"].' '.$bebidas[$i]["Size"].' '.$bebidas[$i]["Envase"].' '.$bebidas[$i]["Sabor"].' <br><div col s10 offset-s2">Vendido: '.$bebidas[$i]["Total"].'</div></br>';
                  }
                }else{print ERROR;break;}

                $sql = 'SELECT 
                        COUNT(*) as "Total",
                        pp.Nombre AS "Promo",
                        o.ID_Producto as "ID"
                        FROM '.$tables["orders"].' op
                        INNER JOIN ' .$tables["order_Products"] . ' o
                        ON o.ID_PedidoAI = op.ID_PedidoAI 
                        INNER JOIN ' .$tables["promos_price"] . ' pp 
                        ON  o.ID_Producto = pp.ID_Promo
                        WHERE 
                              o.Producto=4
                              AND 
                              op.FH_LISTO IS NOT NULL
                              '.$dondeS.'
                        GROUP BY o.ID_Producto,pp.Nombre';
                if ($lookup=$GLOBALS['server']->query($sql)){
                  $Total = $lookup->rowCount();
                  $promos = $lookup->fetchall();
                  for ($i=0; $i < $Total; $i++) { 
                    $sum = 0;
                    $sql = 'SELECT Cantidad
                        FROM '.$tables["orders"].' op
                        INNER JOIN ' .$tables["order_Products"] . ' o
                        ON o.ID_PedidoAI = op.ID_PedidoAI 
                        WHERE o.Producto=3 
                              AND 
                              op.FH_LISTO IS NOT NULL
                              AND
                              o.ID_Producto ='.$promos[$i]["ID"].'
                              '.$dondeS;
                    if ($lookup=$GLOBALS['server']->query($sql)) {
                      $aux = $lookup->fetchall();
                      for ($x=0; $x < $lookup->rowCount(); $x++) {
                        if ($aux[$x]['Cantidad']>1) {
                          $sum+=$aux[$x]['Cantidad']-1;
                        }
                      }
                      if ($sum>0) {
                        $promos[$i]['Total']+=$sum;
                      }
                      $htmlP.= '#'.$promos[$i]["Promo"].' <a href="#!" class="waves-effect btn" onclick="ver_promo(' . $promos[$i]["ID"] .')"><i class="material-icons tiny">remove_red_eye</i></a> <br><div col s10 offset-s2">Vendido: '.$promos[$i]["Total"].'</div><br>';
                    }
                  }
                }else{print ERROR;break;}
                $vector = array('comida' => $htmlF,
                                'salsa' => $htmlS,
                                'bebida' => $htmlD,
                                'promo' => $htmlP,
                                );
                print json_encode($vector);
              }else{print NOT_ALLOWED;}
            break;
            case 'addFast':
              if (count($_POST['content'])==1) {
                $id = clean($_POST['content'][0]);
                $id= explode('-',$id);
                if (count($id)==2) {
                  $sql = 'SELECT ID_Tipo FROM '.$tables['type_products'].' WHERE ID_Tipo = '.$id[0];
                  if (Existe($sql)) {
                    $sql = '';
                    if ($id[0]=='1') {
                      $sql = 'SELECT Precio as "Total" FROM '.$tables['drink_final'].' 
                            WHERE ID_Bebida = '.$id[1];
                      $idFinal = $id[1];
                    }else{
                      $aux = explode('.',$id[1]);
                      if (count($aux)==4) {
                        $sql = 'SELECT sp.Precio as "Total" FROM '.$tables['soats_price'].' sp
                        INNER JOIN '.$tables['soats'].' s
                        ON ID_Salsa = '.$aux[0].'
                        INNER JOIN '.$tables['food_final'].' ff
                        ON ID_ComidaCategoria = ff.ID_Categoria
                            WHERE 
                            ff.ID_ComidaFinal = '.$aux[1].' AND
                            ff.ID_Comida = '.$aux[2].' AND 
                            ID_SalsaCategoria = '.$aux[3].' LIMIT 1';
                        $idFinal = $aux[0];
                      }
                    }
                    if ($sql!='') {
                      if ($lookup=$GLOBALS['server']->query($sql)) {
                        if ($lookup->rowCount()==1) {
                          $total = $lookup->fetch()['Total'];
                          $columnas = '`ID_Sucursal`, 
                                        `ID_Pedido`, 
                                        `PrecioFinal`, 
                                        `FH_Inicio`, 
                                        `FH_Listo`, 
                                        `ID_Encargado`';
                          $valores = IDSUCURSAL.',
                                      -100,
                                      '.$total.',
                                      NOW(),
                                      NOW(),
                          '.IDENCARGADO;
                          if (INSERT($tables['orders'], $columnas, $valores)) {
                            $sql = "SELECT ID_PedidoAI FROM ".$tables['orders']." 
                                    WHERE ID_Pedido = -100 
                                    AND FH_Entregado IS NULL
                                    AND ID_Sucursal = ".IDSUCURSAL."
                                    AND ID_Encargado = ".IDENCARGADO;
                            if ($lookup=$GLOBALS['server']->query($sql)) {
                              if ($lookup->rowCount()==1) {
                                $datos=$lookup->fetchall();
                                if (UPDATE($tables['orders'], "FH_Entregado=NOW()", "ID_PedidoAI=".$datos[0]['ID_PedidoAI'])) {
                                  $columnas = '`ID_PedidoAI`,
                                                `Producto`,
                                                `ID_Producto`,
                                                `Cantidad`';
                                  $valores = $datos[0]['ID_PedidoAI'].',
                                              '.$id[0].',
                                              '.$idFinal.',
                                              1';
                                  if (INSERT($tables['order_Products'], $columnas, $valores)) {
                                    if (UPDATE($tables['caja'],"Total=Total+".$total,dondeCaja())) {
                                      print PASS;
                                    }else{print ERROR.'2';}
                                  }else{print ERROR.'3';}
                                }else{print ERROR.'4';}
                              }else{print ERROR.'5';}
                            }else{print ERROR.'6';}
                          }else {print ERROR.'7';}
                        }else{print ERROR.$sql;}
                      }else{print ERROR.$sql;}
                    }else{print ERROR.'10';}
                  }else{print ERROR.'111';}
                }else{print ERROR.'1';}
              }else{print NOT_ALLOWED;}
            break;
            case 'Seguro':
              if (count($_POST['content'])==4) {
                $food = clean($_POST['content'][0]);
                $soat = clean($_POST['content'][1]);
                $drink = clean($_POST['content'][2]);
                $promo = clean($_POST['content'][3]);


                $htmlF =  '';
                $htmlD =  '';
                $htmlP =  '';

                $total = 0;


                if (count($food)>0) {
                  $title = '<h4>Comidas:</h4>';
                  for ($i=0; $i < count($food); $i++) { 
                    if (count(explode('(',$food[$i]))==2 && count(explode(')',$food[$i]))==2) {
                      $id = explode('(',$food[$i])[0];
                      $foodS = explode(')',explode('(',$food[$i])[1])[0];
                      $sql = "SELECT 
                                  f.Nombre AS 'Comida',
                                  ff.Precio AS 'Precio',
                                  c.Categoria AS 'Size',
                                  c.ID_Categoria AS 'ID_Categoria'
                                  FROM ".$tables['food_final']." ff

                                  INNER JOIN ".$tables['category']." c
                                  ON c.ID_Categoria=ff.ID_Categoria 

                                  INNER JOIN ".$tables['food']." f
                                  ON f.ID_Comida=ff.ID_Comida

                                  WHERE ID_ComidaFinal = ".$id;
                      if($lookup = $GLOBALS['server']->query($sql)) {
                        $aux = $lookup->fetchall();
                        if (count($aux)==1) {
                          $comidaCategoria = $aux[0]["ID_Categoria"];
                          $htmlF.= '#'.$aux[0]["Comida"].' '.$aux[0]["Size"].' $'.$aux[0]["Precio"].' 
                                  <br>';
                          $total += $aux[0]["Precio"];
                          for ($x=0; $x < count($soat); $x++) { 
                            
                            if (count(explode('(',$soat[$x]))==2 && count(explode(')',$soat[$x]))==2) {

                              $id = explode('(',$soat[$x])[0];

                              $salsaF = explode(')',explode('(',$soat[$x])[1])[0];
                              if ($salsaF == $foodS) {
                                $sql = 'SELECT
                                          s.Nombre as "Salsa",
                                          c.Categoria as "Categoria",
                                          sp.Precio as "Precio"
                                          FROM ' . $tables['soats'].' s 
                                          
                                          INNER JOIN '.$tables['category'].' c  ON 
                                          c.ID_Categoria=s.ID_Categoria

                                          INNER JOIN '.$tables['soats_price'].' sp  ON 
                                          c.ID_Categoria=sp.ID_SalsaCategoria
                                          AND
                                          sp.ID_ComidaCategoria = '.$comidaCategoria.'

                                          WHERE s.ID_Salsa = '.$id.' AND sp.ID_ComidaCategoria = '.$comidaCategoria;
                                if($lookup = $GLOBALS['server']->query($sql)) {
                                  $aux = $lookup->fetchall();
                                  if (count($aux)==1) {
                                    $htmlF.= '<div col s10 offset-s2">
                                                  ->'.$aux[0]["Salsa"].' '.$aux[0]["Categoria"].' $'.$aux[0]["Precio"].' 
                                                <br>
                                    </div>';
                                    $total += $aux[0]["Precio"];
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                  if ($htmlF<>"") {
                    $htmlF = $title.$htmlF;
                  }
                }
                if (count($drink)>0) {
                  $title = '<h4>Bebidas:</h4>';
                  for ($i=0; $i < count($drink); $i++) {
                    $sql = "SELECT b.Marca AS 'Marca',
                                    d.Precio AS 'Precio',
                                          CASE
                                            WHEN ROUND((ds.Mililitros-500)/1000)>=1 THEN CONCAT(ROUND((ds.Mililitros-500)/1000),'L')
                                            ELSE CONCAT(ds.Mililitros,'ml')
                                          END AS 'Size',
                                          dc.Envase AS 'Envase',
                                          CASE 
                                            WHEN dt.Sabor IS NULL THEN ''
                                            ELSE dt.Sabor
                                          END AS 'Sabor'
                                  FROM ".$tables['drink_final']." d

                                  INNER JOIN ".$tables['drink_brands']." b
                                  ON b.ID_Marca=d.Marca

                                  INNER JOIN ".$tables['drink_size']." ds
                                  ON ds.ID_Size=d.ID_Size

                                  INNER JOIN ".$tables['drink_conteiner']." dc
                                  ON dc.ID_Envases=d.Envase

                                  LEFT JOIN ".$tables['drink_taste']." dt
                                  ON dt.ID_Sabor=d.Sabor

                                  WHERE d.ID_Bebida = ".$drink[$i];
                    if($lookup = $GLOBALS['server']->query($sql)) {
                      $aux = $lookup->fetchall();
                      if (count($aux)==1) {
                        $htmlD.= '#'.$aux[0]["Marca"].' '.$aux[0]["Size"].' '.$aux[0]["Envase"].' '.$aux[0]["Sabor"].' $'.$aux[0]["Precio"].' 
                        <br>';
                        $total += $aux[0]["Precio"];
                      }
                    }
                  }
                  if ($htmlD<>"") {
                    $htmlD = $title.$htmlD;
                  }
                }
                if (count($promo)>0) {
                  $title = '<h4>Promos:</h4>';
                  for ($i=0; $i < count($promo); $i++) { 
                    $sql = 'SELECT *
                        FROM '.$tables["promos_price"].' pri WHERE ID_Promo = '.$promo[$i];
                    if($lookup = $GLOBALS['server']->query($sql)) {
                      $aux = $lookup->fetchall();
                      if (count($aux)==1) {
                        $htmlP.= '
                                    #'.$aux[0]["Nombre"].' $'.$aux[0]["Precio"].' 
                                  <br>';
                        $total += $aux[0]["Precio"];
                      }
                    }
                  }
                  if ($htmlP<>"") {
                    $htmlP = $title.$htmlP;
                  }
                }
                $concat = $htmlF.$htmlD.$htmlP;
                $arreglo = array('html' => $concat,
                                  'total' =>  $total);
                print json_encode([$arreglo]);
              }else{print NOT_ALLOWED;}
            break;
            case 'makeOrder':
              if (count($_POST['content'])==7 && is_array($_POST['content'][6]) && count($_POST['content'][6])==2) {

                $idOrden = $_POST['content'][4];
                $donde = 'ID_Sucursal = '.IDSUCURSAL.'
                                AND 
                                FH_Entregado IS NULL
                                AND
                                Cancelado IS NULL
                                AND
                                ID_Pedido="'.$idOrden.'"';
                $sql = "SELECT ID_Pedido FROM  ".$tables['orders']."
                        where ".$donde;
                $lookup = $GLOBALS['server']->query($sql);
                if ($lookup && $lookup->rowCount() == 0) {
                  $food = clean($_POST['content'][0]);
                  $soat = clean($_POST['content'][1]);
                  $drink = clean($_POST['content'][2]);
                  $promo = clean($_POST['content'][3]);

                  $regalo=  $_POST['content'][5] == ''?'NULL':clean($_POST['content'][5]);
                  $total = 0;

                  $valores = IDSUCURSAL.','.IDENCARGADO.',"'.$idOrden.'",'.$regalo;
                  if (INSERT($tables['orders'],'ID_Sucursal,ID_Encargado,ID_Pedido,Regalo',$valores)) {
                        $sql = "SELECT ID_PedidoAI FROM ".$tables['orders']."
                                WHERE 
                                ID_Sucursal = ".IDSUCURSAL." AND
                                FH_Listo IS NULL AND
                                ID_Pedido = '".$idOrden."' LIMIT 1";
                        $lookup=$GLOBALS['server']->query($sql);
                        $idOrden = $lookup->fetch()["ID_PedidoAI"];
                        if (count($food)>0) {
                          for ($i=0; $i < count($food); $i++) { 
                            if (count(explode('(',$food[$i]))==2 && count(explode(')',$food[$i]))==2) {
                              $id = explode('(',$food[$i])[0];
                              $foodS = explode(')',explode('(',$food[$i])[1])[0];
                              $sql = "SELECT 
                                          ff.Precio AS 'Precio',
                                          c.ID_Categoria AS 'ID_Categoria'
                                          FROM ".$tables['food_final']." ff

                                          INNER JOIN ".$tables['category']." c
                                          ON c.ID_Categoria=ff.ID_Categoria 

                                          WHERE ID_ComidaFinal = ".$id;
                              if($lookup = $GLOBALS['server']->query($sql)) {
                                $aux = $lookup->fetchall();
                                if (count($aux)==1) {
                                  $comidaCategoria = $aux[0]["ID_Categoria"];
                                  $total += $aux[0]["Precio"];
                                  $columnas = '`ID_PedidoAI`, 
                                                `Producto`, 
                                                `ID_Producto`,
                                                `ID_SubPedido`,
                                                `Cantidad`';
                                  $valores = $idOrden.','.'2'.','.$id.','.$foodS.','.'1';

                                  if (INSERT($tables["order_Products"],$columnas,$valores)) {
                                    for ($x=0; $x < count($soat); $x++) { 
                                    
                                      if (count(explode('(',$soat[$x]))==2 && count(explode(')',$soat[$x]))==2) {

                                        $id = explode('(',$soat[$x])[0];

                                        $salsaF = explode(')',explode('(',$soat[$x])[1])[0];
                                        if ($salsaF == $foodS) {
                                          $sql = 'SELECT
                                                    sp.Precio as "Precio"
                                                    FROM ' . $tables['soats'].' s 
                                                    
                                                    INNER JOIN '.$tables['category'].' c  ON 
                                                    c.ID_Categoria=s.ID_Categoria

                                                    INNER JOIN '.$tables['soats_price'].' sp  ON 
                                                    c.ID_Categoria=sp.ID_SalsaCategoria
                                                    AND
                                                    sp.ID_ComidaCategoria = '.$comidaCategoria.'

                                                    WHERE s.ID_Salsa = '.$id.' AND sp.ID_ComidaCategoria = '.$comidaCategoria;
                                          if($lookup = $GLOBALS['server']->query($sql)) {
                                            $aux = $lookup->fetchall();
                                            if (count($aux)==1) {
                                              $total += $aux[0]["Precio"];
                                              $columnas = '`ID_PedidoAI`, 
                                                            `Producto`, 
                                                            `ID_Producto`,
                                                            `ID_SubPedido`,
                                                            `Cantidad`';
                                              $valores = $idOrden.','.'3'.','.$id.','.$salsaF.','.'1';
                                              INSERT($tables["order_Products"],$columnas,$valores);
                                            }
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                        if (count($drink)>0) {
                          for ($i=0; $i < count($drink); $i++) {
                            $sql = 'SELECT COUNT(*) FROM '.$tables["order_Products"].'
                                    WHERE ID_PedidoAI='.$idOrden.' AND
                                          Producto=1 AND
                                          ID_Producto = '.$drink[$i];
                            if ($lookup = $GLOBALS['server']->query($sql)) {
                              if ($lookup->fetch()[0] == 0) {
                                $sql = "SELECT 
                                              d.Precio AS 'Precio'
                                            FROM ".$tables['drink_final']." d
                                            WHERE d.ID_Bebida = ".$drink[$i];
                                if($lookup = $GLOBALS['server']->query($sql)) {
                                  $aux = $lookup->fetchall();
                                  if (count($aux)==1) {
                                    $columnas = '`ID_PedidoAI`, 
                                                  `Producto`, 
                                                  `ID_Producto`,
                                                  `Cantidad`';
                                    $cont = 0;
                                    for ($x=0; $x < count($drink); $x++) { 
                                      if ($drink[$i]==$drink[$x]) {
                                        $cont++;
                                      }
                                    }
                                    $valores = $idOrden.','.'1'.','.$drink[$i].','.$cont;
                                    INSERT($tables["order_Products"],$columnas,$valores);
                                    $total += $aux[0]["Precio"]*$cont;
                                  }
                                }
                              }
                            }
                          }
                        }
                        if (count($promo)>0) {
                          for ($i=0; $i < count($promo); $i++) { 
                            $sql = 'SELECT COUNT(*) FROM '.$tables["order_Products"].'
                                    WHERE ID_PedidoAI='.$idOrden.' AND
                                          Producto=4 AND
                                          ID_Producto = '.$promo[$i];
                            if ($lookup = $GLOBALS['server']->query($sql)) {
                              if ($lookup->fetch()[0] == 0) {
                                $sql = 'SELECT *
                                    FROM '.$tables["promos_price"].' pri WHERE ID_Promo = '.$promo[$i];
                                if($lookup = $GLOBALS['server']->query($sql)) {
                                  $aux = $lookup->fetchall();
                                  if (count($aux)==1) {
                                    $columnas = '`ID_PedidoAI`, 
                                                  `Producto`, 
                                                  `ID_Producto`,
                                                  `Cantidad`';
                                    $cont = 0;
                                    for ($x=0; $x < count($promo); $x++) { 
                                      if ($promo[$i]==$promo[$x]) {
                                        $cont++;
                                      }
                                    }
                                    $total += $aux[0]["Precio"]*$cont;
                                    $valores = $idOrden.','.'4'.','.$promo[$i].','.$cont;
                                    INSERT($tables["order_Products"],$columnas,$valores);
                                  }
                                }
                              }
                            } 
                          }
                        }
                        $pedidos = clean($_POST['content'][6][0]);
                        if ($pedidos == '2') {
                          $total = clean($_POST['content'][6][1]);
                        }
                        $donde = 'ID_Sucursal = '.IDSUCURSAL.'
                                  AND 
                                  ID_Encargado = '.IDENCARGADO.' 
                                  AND 
                                  FH_Listo IS NULL
                                  AND 
                                  FH_Inicio IS NOT NULL
                                  AND
                                  ID_PedidoAI='.$idOrden;
                        if ($total > 0) {
                          if (UPDATE($tables['orders'],'PrecioFinal='.$total,$donde)) {
                            if ($_POST['content'][5] == '')
                              UPDATE($tables["caja"],"Total = Total+".$total,dondeCaja());
                            $sql = 'SELECT Total
                                      FROM '.$tables["caja"].'
                                      WHERE '.dondeCaja();
                            if ($lookup=$GLOBALS['server']->query($sql)) {
                              $arreglo = array('IDFinal' => clean($_POST['content'][4]),
                                              'ID_Pedido'=> $idOrden,
                                              'Total' =>  $total,
                                              'Caja' => $lookup->fetch()["Total"]);
                            print json_encode([$arreglo]);
                            }else{print ERROR;}
                          }else{print ERROR;}
                        }else{
                          DELETE($tables['orders'],$donde);
                          print ERROR;
                        }
                  } else {print ERROR;}
                }else{print ALREADY_REGISTERED;}
              }else{print NOT_ALLOWED;}
            break;
            case 'View_More':
              if (count($_POST['content'])==2) {
                if ($_POST['content'][1]==1) {
                  $sql = 'SELECT
                          o.ID_PedidoAI AS "ID",
                          o.ID_Pedido AS "ID_2",
                          CONCAT(e.Nombres," ",e.Apellidos) AS "Encargado",
                          CONCAT(
                          DATE_FORMAT(o.FH_Inicio,"%d-%m-%Y") ," (", 
                                        CASE 
                                          WHEN DAYNAME(o.FH_Inicio) = "Monday"
                                            THEN "Lunes"
                                          WHEN DAYNAME(o.FH_Inicio) = "Tuesday"
                                            THEN "Martes"
                                          WHEN DAYNAME(o.FH_Inicio) = "Wednesday"
                                            THEN "Miercoles"
                                          WHEN DAYNAME(o.FH_Inicio) = "Thursday"
                                            THEN "Jueves"
                                          WHEN DAYNAME(o.FH_Inicio) = "Friday"
                                            THEN "Viernes"
                                          WHEN DAYNAME(o.FH_Inicio) = "Saturday"
                                            THEN "Sabado"
                                          WHEN DAYNAME(o.FH_Inicio) = "Sunday"
                                            THEN "Domingo"
                                          END
                                        ,")"
                          ) 
                          AS "DIA",
                          CONCAT("Tomado a las: ",HOUR(o.FH_Inicio)+00,":",MINUTE(o.FH_Inicio)+00) AS "Tomado",
                          CASE
                            WHEN o.FH_Listo IS NOT NULL 
                            THEN CONCAT("Elaborado a las: ",HOUR(o.FH_Listo)+00,":",MINUTE(o.FH_Listo)+00) 
                            ELSE "No está listo."
                          END AS "Listo",
                          CASE 
                            WHEN FH_Entregado IS NOT NULL THEN
                              CONCAT("Se entregó a las:",HOUR(o.FH_Entregado)+00,":",MINUTE(o.FH_Entregado)+00)
                            ELSE
                              "No se entregó"
                          END AS "Entregado",
                          o.PrecioFinal AS "Total",
                          CASE
                            WHEN o.Regalo IS NOT NULL 
                            THEN CONCAT("Sin cobrar para: ",o.Regalo) 
                            ELSE ""
                          END AS "Regalo",
                          CASE 
                            WHEN Cancelado IS NOT NULL
                            THEN CONCAT("Se canceló porque: ",o.Cancelado)
                            ELSE ""
                          END AS "Cancelado"
                          FROM ' .$tables['orders'] . ' o
                          INNER JOIN ' .$tables['employees'] . ' e
                          ON e.ID_Usuario = o.ID_Encargado
                          WHERE
                          o.ID_PedidoAI = ' . clean($_POST['content'][0]);
                }else{
                  $sql = 'SELECT
                        o.ID_PedidoAI AS "ID",
                        o.ID_Pedido AS "ID_2",
                        CONCAT(e.Nombres," ",e.Apellidos) AS "Encargado",
                        CASE
                          WHEN o.FH_Listo IS NOT NULL 
                          THEN CONCAT("Elaborado a las: ",HOUR(o.FH_Listo)+00,":",MINUTE(o.FH_Listo)+00) 
                          ELSE "No está listo."
                        END AS "Listo",
                        CONCAT("Se tomó a las:",HOUR(o.FH_Inicio)+00,":",MINUTE(o.FH_Inicio)+00) AS "Inicio",
                        o.PrecioFinal AS "Total",
                        CASE
                          WHEN o.Regalo IS NOT NULL 
                          THEN CONCAT("Sin cobrar para: ",o.Regalo) 
                          ELSE ""
                        END AS "Regalo"
                        FROM ' .$tables['orders'] . ' o
                        INNER JOIN ' .$tables['employees'] . ' e
                        ON e.ID_Usuario = o.ID_Encargado
                        WHERE
                        o.ID_PedidoAI = ' . clean($_POST['content'][0]).' AND
                        o.FH_Entregado IS NULL AND
                        o.ID_Sucursal = '.IDSUCURSAL;
                }
                $htmlF =  '';
                $htmlD =  '';
                $htmlP =  '';
                if ($lookup = $GLOBALS['server']->query($sql)) {
                  if ($lookup->rowCount() == 1) {
                    $datos = $lookup->fetchall();
                    $sql = 'SELECT * 
                            FROM ' .$tables["order_Products"] . ' o
                            WHERE 
                                Producto = 2 AND
                                ID_PedidoAI = '.$datos[0]["ID"];
                    if ($lookup = $GLOBALS['server']->query($sql)) {
                      if ($lookup->rowCount()>0) {
                        $title = '<br>
                                      <h4>Comidas:</h4>';
                        $food = $lookup->fetchall();
                        for ($i=0; $i < count($food); $i++) { 
                          $sql = "SELECT 
                                      f.Nombre AS 'Comida',
                                      ff.Precio AS 'Precio',
                                      c.Categoria AS 'Size',
                                      c.ID_Categoria AS 'ID_Categoria'
                                      FROM ".$tables['food_final']." ff

                                      INNER JOIN ".$tables['category']." c
                                      ON c.ID_Categoria=ff.ID_Categoria 

                                      INNER JOIN ".$tables['food']." f
                                      ON f.ID_Comida=ff.ID_Comida

                                      WHERE ID_ComidaFinal = ".$food[$i]["ID_Producto"];
                          if ($lookup = $GLOBALS['server']->query($sql)) {
                            if ($lookup->rowCount()==1) {
                              $aux = $lookup->fetchall();
                              $comidaCategoria = $aux[0]["ID_Categoria"];
                              $htmlF.= '#'.$aux[0]["Comida"].' '.$aux[0]["Size"].' $'.$aux[0]["Precio"].' 
                                      <br>';
                              $sql = 'SELECT * 
                                      FROM ' .$tables["order_Products"] . ' o
                                      WHERE 
                                          Producto = 3 AND
                                          ID_SubPedido = '.$food[$i]["ID_SubPedido"].' AND
                                          ID_PedidoAI = '.$datos[0]["ID"];
                              if ($lookup = $GLOBALS['server']->query($sql)) {
                                if ($lookup->rowCount()>=1) {
                                  $soat = $lookup->fetchall();
                                  for ($x=0; $x < count($soat); $x++) { 
                                    $sql = 'SELECT
                                                s.Nombre as "Salsa",
                                                c.Categoria as "Categoria",
                                                sp.Precio as "Precio"
                                                FROM ' . $tables['soats'].' s 
                                                
                                                INNER JOIN '.$tables['category'].' c  ON 
                                                c.ID_Categoria=s.ID_Categoria

                                                INNER JOIN '.$tables['soats_price'].' sp  ON 
                                                c.ID_Categoria=sp.ID_SalsaCategoria
                                                AND
                                                sp.ID_ComidaCategoria = '.$comidaCategoria.'

                                                WHERE s.ID_Salsa = '.$soat[$x]["ID_Producto"].' AND sp.ID_ComidaCategoria = '.$comidaCategoria;
                                    if ($lookup = $GLOBALS['server']->query($sql)) {
                                      if ($lookup->rowCount()==1) {
                                        $aux = $lookup->fetchall();
                                        $htmlF.= '<div col s10 offset-s2">
                                                      ->'.$aux[0]["Salsa"].' '.$aux[0]["Categoria"].' $'.$aux[0]["Precio"].' 
                                                    <br>
                                        </div>';
                                      }
                                    }
                                  }
                                } 
                              }
                            }
                          }
                        }
                        if ($htmlF<>"")
                          $htmlF = $title.$htmlF;
                      }
                    }
                    //-------------------DRINK----------------------------
                    $sql = 'SELECT ID_Producto,ID_PedidoAI,Cantidad 
                            FROM ' .$tables["order_Products"] . ' o
                            WHERE 
                                Producto = 1 AND
                                ID_PedidoAI = '.$datos[0]["ID"];
                    if ($lookup = $GLOBALS['server']->query($sql)) {
                      if ($lookup->rowCount()>0) {
                        $title = '<br>
                                      <h4>Bebidas:</h4>';
                        $producto = $lookup->fetchall();
                        for ($i=0; $i < count($producto); $i++) { 
                          $sql = "SELECT b.Marca AS 'Marca',
                                        d.Precio AS 'Precio',
                                              CASE
                                                WHEN ROUND((ds.Mililitros-500)/1000)>=1 THEN CONCAT(ROUND((ds.Mililitros-500)/1000),'L')
                                                ELSE CONCAT(ds.Mililitros,'ml')
                                              END AS 'Size',
                                              dc.Envase AS 'Envase',
                                              CASE 
                                                WHEN dt.Sabor IS NULL THEN ''
                                                ELSE dt.Sabor
                                              END AS 'Sabor'
                                      FROM ".$tables['drink_final']." d

                                      INNER JOIN ".$tables['drink_brands']." b
                                      ON b.ID_Marca=d.Marca

                                      INNER JOIN ".$tables['drink_size']." ds
                                      ON ds.ID_Size=d.ID_Size

                                      INNER JOIN ".$tables['drink_conteiner']." dc
                                      ON dc.ID_Envases=d.Envase

                                      LEFT JOIN ".$tables['drink_taste']." dt
                                      ON dt.ID_Sabor=d.Sabor

                                      WHERE d.ID_Bebida = ".$producto[$i]['ID_Producto'];
                          if($lookup = $GLOBALS['server']->query($sql)) {  
                            $aux = $lookup->fetchall();
                            if (count($aux)==1) {
                              $htmlD.= '#'.$aux[0]["Marca"].' '.$aux[0]["Size"].' '.$aux[0]["Envase"].' '.$aux[0]["Sabor"].' $'.$aux[0]["Precio"].' ('.$producto[$i]["Cantidad"].')
                                      </br>';
                            }
                          }
                        }
                        if ($htmlD<>"")
                          $htmlD = $title.$htmlD;
                      }
                    }
                    //-------------------Promos----------------------------
                    $sql = 'SELECT ID_Producto,ID_PedidoAI,Cantidad 
                            FROM ' .$tables["order_Products"] . ' o
                            WHERE 
                                Producto = 4 AND
                                ID_PedidoAI = '.$datos[0]["ID"];
                    if ($lookup = $GLOBALS['server']->query($sql)) {
                      if ($lookup->rowCount()>0) {
                        $title = '<br>
                                      <h4>Promos:</h4>';
                        $producto = $lookup->fetchall();
                        for ($i=0; $i < count($producto); $i++) { 
                          $sql = 'SELECT *
                            FROM '.$tables["promos_price"].' pri WHERE ID_Promo = '.$producto[$i]['ID_Producto'];
                          if($lookup = $GLOBALS['server']->query($sql)) {
                            $aux = $lookup->fetchall();
                            if (count($aux)==1) {
                              $htmlP.= '#'.$aux[0]["Nombre"].' $'.$aux[0]["Precio"].' ('.$producto[$i]["Cantidad"].')
                                        <br>';
                            }
                          }
                        }
                        if ($htmlP<>"")
                          $htmlP = $title.$htmlP;
                      }
                    }

                    $concat = $htmlF.$htmlD.$htmlP;
                    $vector = array('datos' => $datos,
                                    'html' => $concat);
                    print json_encode($vector);
                  }else{print ERROR;}
                }else{print ERROR;}
              }else{print NOT_ALLOWED;}
            break;
            case 'readyOrder':
                $donde = 'FH_Listo IS NULL';
                Pedidos(1,$donde,"FH_Listo = NOW()");
            break;
            case 'finishOrder':
                $donde = 'FH_Listo IS NOT NULL';
                Pedidos(1,$donde,"FH_Entregado = NOW()"); 
            break;
            case 'cancelar':
              if (count($_POST['content'])==3) {
                if ($_POST['content'][2]=='true')
                  $motivo = '"'.clean($_POST['content'][1]).'(restado)"';
                else
                  $motivo = '"'.clean($_POST['content'][1]).'"';
                $donde = 'FH_Listo IS NULL';
                Pedidos(3,$donde,"Cancelado = ".$motivo);
              }else{print NOT_ALLOWED;}
            break;
            case 'no_vino':
                $donde = 'FH_Listo IS NOT NULL';
              Pedidos(1,$donde,"Cancelado = 'No vinieron a retirarlo'");               
            break;

  #PROMOS
            case 'getOnePromo':
              if (count($_POST['content']) == 1) {
                $sql = "SELECT * FROM ".$tables['promos_products']." WHERE ID_Promo=".clean($_POST['content'][0]);
                if($lookup = $GLOBALS['server']->query($sql)) {
                  $datos = $lookup->fetchall();
                  if (count($datos)>0){
                    $concat = '';
                    for ($i=0; $i < count($datos); $i++) { 
                      $id = $datos[$i]["ID_Producto"];
                      switch ($datos[$i]["ID_Tipo"]) {
                        case '1':
                          $sql = "SELECT b.Marca AS 'Marca',
                                          CASE
                                            WHEN ROUND((ds.Mililitros-500)/1000)>=1 THEN CONCAT(ROUND((ds.Mililitros-500)/1000),'L')
                                            ELSE CONCAT(ds.Mililitros,'ml')
                                          END AS 'Size',
                                          dc.Envase AS 'Envase',
                                          CASE 
                                            WHEN dt.Sabor IS NULL THEN ''
                                            ELSE dt.Sabor
                                          END AS 'Sabor'
                                  FROM ".$tables['drink_final']." d

                                  INNER JOIN ".$tables['drink_brands']." b
                                  ON b.ID_Marca=d.Marca

                                  INNER JOIN ".$tables['drink_size']." ds
                                  ON ds.ID_Size=d.ID_Size

                                  INNER JOIN ".$tables['drink_conteiner']." dc
                                  ON dc.ID_Envases=d.Envase

                                  LEFT JOIN ".$tables['drink_taste']." dt
                                  ON dt.ID_Sabor=d.Sabor

                                  WHERE d.ID_Bebida = ".$id;
                          if($lookup = $GLOBALS['server']->query($sql)) {
                            $aux = $lookup->fetchall();
                            $concat.= '<div class="row">
                                        #'.$aux[0]["Marca"].' '.$aux[0]["Size"].' '.$aux[0]["Envase"].' '.$aux[0]["Sabor"].' 
                                     </div>';
                          }
                        break;
                        case '2':
                          $sql = "SELECT 
                                  f.Nombre AS 'Comida',
                                  c.Categoria AS 'Size'
                                  FROM ".$tables['food_final']." ff

                                  INNER JOIN ".$tables['category']." c
                                  ON c.ID_Categoria=ff.ID_Categoria 

                                  INNER JOIN ".$tables['food']." f
                                  ON f.ID_Comida=ff.ID_Comida

                                  WHERE ID_ComidaFinal = ".$id;
                          if($lookup = $GLOBALS['server']->query($sql)) {
                            $aux = $lookup->fetchall();
                            $concat.= '<div class="row">
                                        #'.$aux[0]["Comida"].' '.$aux[0]["Size"].' 
                                     </div>';
                          }
                        break;
                        case '3':
                          $sql = 'SELECT
                                  s.Nombre as "Salsa",
                                  c.Categoria as "Categoria"

                                  FROM ' . $tables['soats'].' s 
                                  
                                  INNER JOIN '.$tables['category'].' c  ON 
                                  c.ID_Categoria=s.ID_Categoria

                                  WHERE ID_Salsa = '.$id;
                          if($lookup = $GLOBALS['server']->query($sql)) {
                            $aux = $lookup->fetchall();
                            $concat.= '<div class="row">
                                        '.$aux[0]["Salsa"].' '.$aux[0]["Categoria"].'
                                     </div>';
                          }
                        break;
                      }
                    }

                    print $concat;
                  }else{print ERROR;}
                }else{print ERROR;}
              }else{print NOT_ALLOWED;}
            break;
  #
  #CATEGORIAS/CATEGORY
            case 'getCategorias':
              if (count($_POST['content']) == 1) {
                $id = clean($_POST['content'][0]);
                $sql = 'SELECT c.* FROM '.$tables["category"].' c 
                        INNER JOIN '.$tables["type_products"].' t 
                        ON t.ID_Tipo = c.ID_Producto
                        WHERE Tipo = '.$id;
                Consulta($sql);
              }else{print NOT_ALLOWED;}
            break;
#DEFAULT
          default:
            print ERROR;
      }
    } elseif (isset($_POST['request'])) {
      switch ($_POST['request']) {
        case 'getMyOwnCaja':
          $gastos = '';
          $pedidos = '';
          $sql='SELECT 
                CONCAT(e.Nombres," ",e.Apellidos) AS "Encargado",
                g.Precio AS "Precio",
                g.Cantidad AS "Cantidad",
                g.Precio*g.Cantidad AS "Total",
                gp.Producto AS "Producto",
                CONCAT(g.FH_Gasto," (", 
                          CASE 
                            WHEN DAYNAME(g.FH_Gasto) = "Monday"
                              THEN "Lunes"
                            WHEN DAYNAME(g.FH_Gasto) = "Tuesday"
                              THEN "Martes"
                            WHEN DAYNAME(g.FH_Gasto) = "Wednesday"
                              THEN "Miercoles"
                            WHEN DAYNAME(g.FH_Gasto) = "Thursday"
                              THEN "Jueves"
                            WHEN DAYNAME(g.FH_Gasto) = "Friday"
                              THEN "Viernes"
                            WHEN DAYNAME(g.FH_Gasto) = "Saturday"
                              THEN "Sabado"
                            WHEN DAYNAME(g.FH_Gasto) = "Sunday"
                              THEN "Domingo"
                            END
                          ,")") AS "FH"
                FROM ' .$tables['expenses'] . ' g
                INNER JOIN ' .$tables['expenses_products'] . ' gp
                ON g.ID_Producto = gp.ID_Producto
                INNER JOIN ' .$tables['employees'] . ' e
                ON g.ID_Encargado = e.ID_Usuario
                WHERE
                  g.ID_Sucursal = '.IDSUCURSAL.'
                ORDER BY g.FH_Gasto DESC';
          if ($lookup=$GLOBALS['server']->query($sql)) {
            if ($lookup->rowCount()>=1) {
              $gastos = $lookup->fetchall();
            }
          }else{print ERROR;}

          $sql='SELECT 
                p.PrecioFinal AS "Total",
                p.ID_PedidoAI AS "ID",
                CONCAT(e.Nombres," ",e.Apellidos) AS "Encargado",
                CONCAT(p.FH_Inicio," (", 
                      CASE 
                        WHEN DAYNAME(p.FH_Inicio) = "Monday"
                          THEN "Lunes"
                        WHEN DAYNAME(p.FH_Inicio) = "Tuesday"
                          THEN "Martes"
                        WHEN DAYNAME(p.FH_Inicio) = "Wednesday"
                          THEN "Miercoles"
                        WHEN DAYNAME(p.FH_Inicio) = "Thursday"
                          THEN "Jueves"
                        WHEN DAYNAME(p.FH_Inicio) = "Friday"
                          THEN "Viernes"
                        WHEN DAYNAME(p.FH_Inicio) = "Saturday"
                          THEN "Sabado"
                        WHEN DAYNAME(p.FH_Inicio) = "Sunday"
                          THEN "Domingo"
                        END
                      ,")") AS "Inicio",
                case 
                  WHEN p.FH_Entregado IS NOT NULL then
                    CONCAT(p.FH_Entregado," (", 
                              CASE 
                                WHEN DAYNAME(p.FH_Entregado) = "Monday"
                                  THEN "Lunes"
                                WHEN DAYNAME(p.FH_Entregado) = "Tuesday"
                                  THEN "Martes"
                                WHEN DAYNAME(p.FH_Entregado) = "Wednesday"
                                  THEN "Miercoles"
                                WHEN DAYNAME(p.FH_Entregado) = "Thursday"
                                  THEN "Jueves"
                                WHEN DAYNAME(p.FH_Entregado) = "Friday"
                                  THEN "Viernes"
                                WHEN DAYNAME(p.FH_Entregado) = "Saturday"
                                  THEN "Sabado"
                                WHEN DAYNAME(p.FH_Entregado) = "Sunday"
                                  THEN "Domingo"
                                END
                              ,")")
                  ELSE "No se entregó" 
                END AS "FH",
                CASE 
                  WHEN p.Regalo IS NOT NULL THEN
                    CONCAT("Regalado a: ",p.Regalo)
                  ELSE
                    "Pagado"
                END "Regalo",
                CASE 
                  WHEN p.Cancelado IS NOT NULL
                  THEN CONCAT("Se canceló porque: ",p.Cancelado)
                  ELSE ""
                END AS "Cancelado"
                FROM ' .$tables['orders'] . ' p
                INNER JOIN ' .$tables['employees'] . ' e
                ON p.ID_Encargado = e.ID_Usuario
                WHERE
                  p.ID_Sucursal = '.IDSUCURSAL.'
                ORDER BY p.FH_Inicio DESC';
          if ($lookup=$GLOBALS['server']->query($sql)) {
            if ($lookup->rowCount()>=1) {
              $pedidos = $lookup->fetchall();
            }
          }else{print ERROR;}

          $vector = [$gastos,$pedidos];
          print json_encode($vector);
        break;
        case 'getHome':
          print json_encode($info['home']);
        break;
        case 'SoatPrices':
          $sql = "SELECT  
                    DISTINCT f.Nombre AS 'Comida',
                    f.ID_Comida as 'IDComida1',
                    c1.Categoria AS 'Size',
                    s.ID_ComidaCategoria AS 'IDComida2', 
                    c2.Categoria AS 'Salsa',
                    s.ID_SalsaCategoria AS 'IDSalsa',
                    s.Precio AS 'Precio' 
                  FROM " . $tables['soats_price']." s 

                  INNER JOIN ".$tables['category']." c1
                  ON c1.ID_Categoria=s.ID_ComidaCategoria 

                  INNER JOIN ".$tables['category']." c2
                  ON c2.ID_Categoria=s.ID_SalsaCategoria 

                  INNER JOIN ".$tables['food']." f
                  ON f.ID_Comida=s.ID_Comida";
          Consulta($sql);
        break;
        case 'getGastos-Ventas':
          $sql = 'SELECT 
                  g.FH_Gasto as "Fecha",
                  p.Producto as "Producto",
                  (g.Precio*g.Cantidad) as "Total"
                FROM '.$tables["expenses"].' as g
                INNER JOIN '.$tables["expenses_products"].' as p
                ON p.ID_Producto=g.ID_Producto
                WHERE ID_Sucursal = '.IDSUCURSAL.' ORDER BY FH_Gasto DESC';
          Consulta($sql);
        break;
        case 'getSoatAux':
          $sql = 'SELECT
                s.Nombre as "Producto"
                FROM ' . $tables['soats'].' s';
          Consulta($sql);
        break;
        case 'getDrinkAux':
          $sql = "SELECT 
                CONCAT(b.Marca,' ',CASE
                  WHEN ROUND((ds.Mililitros-500)/1000)>=1 THEN CONCAT(ROUND((ds.Mililitros-500)/1000),'L')
                      ELSE CONCAT(ds.Mililitros,'ml')
                END) AS 'Producto'
                FROM ".$tables['drink_final']." d

                INNER JOIN ".$tables['drink_brands']." b
                ON b.ID_Marca=d.Marca

                INNER JOIN ".$tables['drink_size']." ds
                ON ds.ID_Size=d.ID_Size";
          Consulta($sql);
        break;
        case 'getExpensesProducts':
          $sql = 'SELECT * FROM '.$tables["expenses_products"];
          Consulta($sql);
        break;
        case 'getPromos':
          $sql = 'SELECT *
                  FROM '.$tables["promos_price"].' pri';
          Consulta($sql);
        break;
        case 'getCaja':
            $donde = dondeCaja();
            $sql = 'SELECT Total,ID_Caja
                    FROM '.$tables["caja"].'
                    WHERE '.$donde;
            Consulta($sql);
        break;
        case 'viewQueve':
          $sql = 'SELECT
              o.ID_Pedido as "IDFinal",
              o.ID_PedidoAI as "ID_Pedido",
              o.PrecioFinal as "Total"
              FROM ' .$tables['orders'] . ' o
              WHERE
                  o.FH_Listo IS NULL AND 
                  o.Cancelado IS NULL AND
                  o.FH_Inicio IS NOT NULL AND
                  o.ID_Sucursal = '.IDSUCURSAL;
          Consulta($sql);
        break;
        case 'viewReady':
          $sql = 'SELECT
              o.ID_Pedido as "IDFinal",
              o.ID_PedidoAI as "ID_Pedido",
              o.PrecioFinal as "Total"
              FROM ' .$tables['orders'] . ' o
              WHERE
                  o.FH_Entregado IS NULL AND
                  o.FH_Listo IS NOT NULL AND 
                  o.Cancelado IS NULL AND
                  o.FH_Inicio IS NOT NULL AND
                  o.ID_Sucursal = '.IDSUCURSAL;
            Consulta($sql);
        break;
        case 'getAllCategorias':
          $sql = 'SELECT c.*,t.* FROM '.$tables["category"].' c 
                  INNER JOIN '.$tables["type_products"].' t 
                  ON t.ID_Tipo = c.ID_Producto';
          Consulta($sql);            
        break;

#SALSAS/SOATS

#COMIDAS/FOODS
        case 'getFoodType':
          $sql = 'SELECT * FROM '.$tables["food"];
          Consulta($sql);
        break;
        case 'getFoodUse':
          $sql = 'SELECT * FROM '.$tables["food_use"];
          Consulta($sql);
        break;
#BEBIDAS/DRINKS
        case 'getBrands':
          $sql = 'SELECT * FROM '.$tables["drink_brands"];
          Consulta($sql);
        break;
        case 'getConteiners':
          $sql = 'SELECT * FROM '.$tables["drink_conteiner"];
          Consulta($sql);
        break;
        case 'getSizes':
          $sql = 'SELECT * FROM '.$tables["drink_size"];
          Consulta($sql);
        break;
        case 'getTastes':
          $sql = 'SELECT * FROM '.$tables["drink_taste"];
          Consulta($sql);
        break;
        default:
          print ERROR;
      }
    }
  } else {
    $title = 'API';
    require_once('includes/header.php');
    print '
    <div id="vcentered_message" class="row valign-wrapper">
      <div class="col s12 center-align black-text">
        <h5>
          <i class="large material-icons">code</i> <br>
            ¡Hola! Esta es nuestra API.
          </h5>
      </div>
    </div>';
    require_once('includes/footer.php');
  }
  
  $title = 'API'; $no_menu = true;
