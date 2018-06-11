<?php
// quitar esto cuando este mejorada la validacion
// include_once('../views/validateUser.php');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$config['debug'] = true;
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = "127.0.0.1";
$config['db']['user']   = "root";
// $config['db']['user']   = "neurosys_rojas";
$config['db']['pass']   = "";
// $config['db']['pass']   = ")-9OkYjdiU1k";
$config['db']['dbname'] = "neurofac_rojas";
// $config['db']['dbname'] = "neurosys_rojas";

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    
    $dsn = 'mysql:host=' .  $db['host'] . ';dbname=' . $db['dbname'] . ';charset=utf8';
    $usr = $db['user'];
    $pwd = $db['pass'];

    $pdo = new \Slim\PDO\Database($dsn, $usr, $pwd);

    return $pdo;
};



$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});



function getNow() {
    return date_create('now', timezone_open('America/Lima'))->format('Y-m-d H:i:s');
}


$app->get('/categorias', function (Request $request, Response $response, array $args) {
    
    $select = $this->db->select()->from('Gen_ProductoCategoria')
                ->whereLike('ProductoCategoria','%' . $request->getParam('q') . '%');

    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});

$app->post('/categorias', function (Request $request, Response $response, array $args) {
    
    $productoCategoria = $request->getParam('ProductoCategoria');
    $anulado = 0;

    $insert = $this->db->insert(array('ProductoCategoria', 'Anulado', 'FechaReg'))
                       ->into('Gen_ProductoCategoria')
                       ->values(array($productoCategoria, $anulado, getNow()));
    $insertId = $insert->execute();
    
    return $response->withJson(array("insertId" => $insertId, "ProductoCategoria" => $productoCategoria));
});


$app->get('/marcas', function (Request $request, Response $response, array $args) {
    $q = $request->getParam('q');

    $select = "SELECT * FROM Gen_ProductoMarca";
    $select .= " WHERE ProductoMarca LIKE '%" . $q . "%' ";

    if ($request->getParam('limit')) {
        $limit = $request->getParam('limit');
        $offset = 0;
        if ($request->getParam('page')) {
            $page = $request->getParam('page');
            $offset = (--$page) * $limit;
        }
        $select .= " LIMIT " . $limit;
        $select .= " OFFSET " . $offset;
    }

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();  

    return $response->withJson($data);
});

$app->post('/marcas', function (Request $request, Response $response, array $args) {
    
    $productoMarca = $request->getParam('ProductoMarca');
    $anulado = 0;

    $insert = $this->db->insert(array('ProductoMarca', 'Anulado', 'FechaReg'))
                       ->into('Gen_ProductoMarca')
                       ->values(array($productoMarca, $anulado, getNow()));
    $insertId = $insert->execute();
    
    return $response->withJson(array("insertId" => $insertId, "ProductoMarca" => $productoMarca));
});


$app->get('/mediciones', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_ProductoMedicion');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});


$app->get('/modelos', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_ProductoModelo')
                ->whereLike('ProductoModelo','%' . $request->getParam('q') . '%');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});

$app->post('/modelos', function (Request $request, Response $response, array $args) {
    
    $productoModelo = $request->getParam('ProductoModelo');
    $anulado = 0;

    $insert = $this->db->insert(array('ProductoModelo', 'Anulado', 'FechaReg'))
                       ->into('Gen_ProductoModelo')
                       ->values(array($productoModelo, $anulado, getNow()));
    $insertId = $insert->execute();
    
    return $response->withJson(array("insertId" => $insertId, "ProductoModelo" => $productoModelo));
});



$app->get('/tallas', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_ProductoTalla')
                ->whereLike('ProductoTalla','%' . $request->getParam('q') . '%');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});


$app->get('/productos', function (Request $request, Response $response, array $args) {
    $select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca, Gen_ProductoModelo.ProductoModelo,
        Gen_ProductoTalla.ProductoTalla, Gen_ProductoMedicion.ProductoMedicion
        FROM Gen_Producto 
        INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
        INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
        INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
        LEFT JOIN Gen_ProductoModelo ON Gen_Producto.IdProductoModelo = Gen_ProductoModelo.IdProductoModelo
        LEFT JOIN Gen_ProductoTalla ON Gen_Producto.IdProductoTalla = Gen_ProductoTalla.IdProductoTalla ";
    
    if ($request->getParam('filter')) {
        $filter = $request->getParam('filter');
        $select .= " WHERE Gen_Producto.Producto LIKE '%" . $filter . 
                   "%' OR Gen_Producto.CodigoBarra LIKE '%" . $filter . 
                   "%' OR Gen_Producto.Color LIKE '%" . $filter . 
                   "%' OR Gen_ProductoMarca.ProductoMarca LIKE '%" . $filter . 
                   "%' OR Gen_ProductoCategoria.ProductoCategoria LIKE '%" . $filter . 
                   "%' ";        
    } else {
        $select .= " WHERE Gen_Producto.Producto LIKE '%" . $request->getParam('q') . "%' ";
    }

    if ($request->getParam('sortBy')) {
        $sortBy = $request->getParam('sortBy');
        $sortDesc = $request->getParam('sortDesc');
        $orientation = $sortDesc ? 'DESC' : 'ASC';
        $select .= " ORDER BY " . $sortBy . " " . $orientation;
    }

    if ($request->getParam('limit')) {
        $limit = $request->getParam('limit');
        $offset = 0;
        if ($request->getParam('page')) {
            $page = $request->getParam('page');
            $offset = (--$page) * $limit;
        }
        $select .= " LIMIT " . $limit;
        $select .= " OFFSET " . $offset;
    }

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});


$app->get('/productos/count', function (Request $request, Response $response, array $args) {
    $select = "SELECT COUNT(*) as total FROM Gen_Producto";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});



$app->get('/proveedores', function (Request $request, Response $response, array $args) {
    $q = $request->getParam('q');

    $select = "SELECT *, IFNULL(CONCAT(Ruc, ' - ', Proveedor), '-') AS ProveedorRuc FROM Lo_Proveedor";
    $select .= " WHERE Proveedor LIKE '%" . $q . "%' OR Ruc LIKE '%" . $q . "%' ";

    if ($request->getParam('limit')) {
        $limit = $request->getParam('limit');
        $offset = 0;
        if ($request->getParam('page')) {
            $page = $request->getParam('page');
            $offset = (--$page) * $limit;
        }
        $select .= " LIMIT " . $limit;
        $select .= " OFFSET " . $offset;
    }

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();  

    return $response->withJson($data);
});

$app->get('/proveedores/count', function (Request $request, Response $response, array $args) {
    $select = "SELECT COUNT(*) as total FROM Lo_Proveedor";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});


$app->post('/proveedores', function (Request $request, Response $response) {
    $proveedor = $request->getParam('Proveedor');
    $ruc = $request->getParam('Ruc');
    $direccion = $request->getParam('Direccion');
    $observacion = $request->getParam('Observacion');

    $insert = $this->db->insert(array('Proveedor', 'Ruc', 'Direccion', 'Observacion', 'FechaReg'))
                       ->into('Lo_Proveedor')
                       ->values(array($proveedor, $ruc, $direccion, $observacion, getNow()));
    
    $insertId = $insert->execute();

    $data = array(
        "insertId" => $insertId
    );

    return $response->withJson($data);
});


$app->post('/productos', function (Request $request, Response $response) {
    $idProductoMarca = $request->getParam('marca')['IdProductoMarca'];
    $idProductoMedicion = $request->getParam('medicion')['IdProductoMedicion'];
    $idProductoCategoria = $request->getParam('categoria')['IdProductoCategoria'];
    $idProductoModelo = $request->getParam('modelo')['IdProductoModelo'];
    $idProductoTalla = $request->getParam('talla')['IdProductoTalla'];
    $idProductoFormaFarmaceutica = 1;
    $producto = $request->getParam('Producto');
    $fechaReg = getNow();
    $hash = time();
    $controlaStock = 1;
    $porcentajeUtilidad = $request->getParam('PorcentajeUtilidad');
    $genero = $request->getParam('Genero');
    $color = $request->getParam('Color');
    $botapie = $request->getParam('Botapie');
    $anulado = $request->getParam('Anulado');
    $categoria = $request->getParam('categoria')['ProductoCategoria'];

    // Actualizamos el producto si le pasamos el ID
    if ($request->getParam('IdProducto')) {
        // aqui se actualiza el producto si existe
        $idProducto = $request->getParam('IdProducto');
        $codigoBarra = $request->getParam('CodigoBarra');
        if (!$codigoBarra) {
            $codigoBarra = substr($categoria, 0, 2) . $idProducto . substr($producto, 0, 2);
            $producto = $producto . '-' . $codigoBarra;
        }
        $update = $this->db->update(array(
                            "CodigoBarra" => $codigoBarra, 
                            "Producto" => $producto,
                            "IdProductoMarca" => $idProductoMarca,
                            "IdProductoFormaFarmaceutica" => $idProductoFormaFarmaceutica,
                            "IdProductoMedicion" => $idProductoMedicion,
                            "IdProductoCategoria" => $idProductoCategoria,
                            "IdProductoModelo" => $idProductoModelo,
                            "IdProductoTalla" => $idProductoTalla,
                            "ControlaStock" => $controlaStock,
                            "PorcentajeUtilidad" => $porcentajeUtilidad,
                            "Genero" => $genero, "Color" => $color, "Botapie" => $botapie, "Anulado" => $anulado
                        ))
                       ->table('Gen_Producto')
                       ->where('IdProducto', '=', $idProducto);
        $affectedRows = $update->execute();
        return $response->withJson(array("affectedRows" => $affectedRows));
    }
    // fin actualizacion producto

    $insert = $this->db->insert(array('IdProductoMarca', 'IdProductoFormaFarmaceutica', 'IdProductoMedicion', 'IdProductoCategoria', 'IdProductoModelo', 'IdProductoTalla', 'Producto', 'FechaReg', 'Hash', 'ControlaStock', 'PorcentajeUtilidad', 'Genero', 'Color', 'Botapie', 'Anulado'))
                       ->into('Gen_Producto')
                       ->values(array($idProductoMarca, $idProductoFormaFarmaceutica, $idProductoMedicion, $idProductoCategoria, $idProductoModelo, $idProductoTalla, $producto, $fechaReg, $hash, $controlaStock, $porcentajeUtilidad, $genero, $color, $botapie, $anulado));
    $insertId = $insert->execute();
    
    // Generando codigo de barras  // actualizar el nombre para que sea unico
    $codigoBarra = substr($categoria, 0, 2) . $insertId . substr($producto, 0, 2);
    
    $update = $this->db->update(array("CodigoBarra" => $codigoBarra, "Producto" => $producto . '-' . $codigoBarra))
                       ->table('Gen_Producto')
                       ->where('IdProducto', '=', $insertId);
    $affectedRows = $update->execute();

    return $response->withJson(array("insertId" => $insertId));

});



$app->get('/movimiento/productos', function (Request $request, Response $response, array $args) {
    $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
    $hashMovimiento = $request->getParam('hash');
    
    $select = "SELECT Lo_MovimientoDetalle.IdProducto, Gen_Producto.Producto, Gen_ProductoMarca.ProductoMarca, Gen_ProductoModelo.ProductoModelo, 
        Gen_Producto.Color, Gen_Producto.CodigoBarra, Gen_Producto.PrecioContado, Gen_ProductoTalla.ProductoTalla, Lo_MovimientoDetalle.Cantidad 
        FROM Lo_MovimientoDetalle 
        INNER JOIN Gen_Producto ON Lo_MovimientoDetalle.IdProducto = Gen_Producto.IdProducto
        INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
        LEFT JOIN Gen_ProductoModelo ON Gen_Producto.IdProductoModelo = Gen_ProductoModelo.IdProductoModelo
        LEFT JOIN Gen_ProductoTalla ON Gen_Producto.IdProductoTalla = Gen_ProductoTalla.IdProductoTalla
        WHERE hashMovimiento = '$hashMovimiento'";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});




$app->get('/movimientos/tipos', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Lo_MovimientoTipo')->whereLike('TipoMovimiento', '%' . $request->getParam('q') . '%');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});



$app->post('/movimientos', function (Request $request, Response $response) { 
    // start verificar Movimiento
    $select = "SELECT * FROM Lo_Movimiento
	WHERE IdMovimientoTipo = '" . $request->getParam('movimiento')['movimientoTipo']['IdMovimientoTipo']
	. "' AND IdProveedor = '" . $request->getParam('movimiento')['proveedor']['IdProveedor']
	. "' AND Serie = '" . $request->getParam('movimiento')['Serie']
    . "' AND Numero = '" . $request->getParam('movimiento')['Numero'] . "'";

    $stmt = $this->db->query($select);
    $stmt->execute();
    
    if (count($stmt->fetchAll())) {
        $data = array(
            "error" => 'Error: Movimiento duplicado'
        );
        return $response->withJson($data);
    }
    // end verificar Movimiento

    // start Insertar Movimiento
    $idMovimientoTipo = $request->getParam('movimiento')['movimientoTipo']['IdMovimientoTipo'];
    $idProveedor = $request->getParam('movimiento')['proveedor']['IdProveedor'];
    $serie = $request->getParam('movimiento')['Serie'];
    $numero = $request->getParam('movimiento')['Numero'];
    $movimientoFecha = $request->getParam('movimiento')['MovimientoFecha'];
    $almacenOrigen = $request->getParam('movimiento')['almacenOrigen']['IdAlmacen'];
    $almacenDestino = $request->getParam('movimiento')['almacenDestino']['IdAlmacen'];
    $anulado = 0;
    $fechaReg = getNow();
    $usuarioReg = 'jeam';
    $hash = time();
    $fechaStock = $request->getParam('movimiento')['FechaStock'];
    $percepcion = 0;
    $esCredito = $request->getParam('movimiento')['EsCredito'];
    $fechaVenCredito = $request->getParam('movimiento')['FechaVenCredito'];
    $fechaPeriodoTributario = $request->getParam('movimiento')['FechaPeriodoTributario'];
    $tipoCambio = $request->getParam('movimiento')['TipoCambio'];
    $moneda = $request->getParam('movimiento')['Moneda'];
   
    $insert = $this->db->insert(array('IdMovimientoTipo', 'IdProveedor', 'Serie', 'Numero', 'MovimientoFecha', 'IdAlmacenOrigen',
                        'IdAlmacenDestino', 'Anulado', 'FechaReg', 'UsuarioReg', 'Hash', 'FechaStock', 'Percepcion', 'EsCredito',
                        'FechaVenCredito', 'FechaPeriodoTributario', 'TipoCambio', 'Moneda'))
                       ->into('Lo_Movimiento')
                       ->values(array($idMovimientoTipo, $idProveedor, $serie, $numero, $movimientoFecha, $almacenOrigen, 
                       $almacenDestino, $anulado, $fechaReg, $usuarioReg, $hash, $fechaStock, $percepcion, $esCredito, $fechaVenCredito,
                       $fechaPeriodoTributario, $tipoCambio, $moneda));
    
    $insertId = $insert->execute();

    $data = array(
        "hash" => $hash
    );
    // end insertar Movimiento
    
    // start Movimiento Detalle
    $productos = $request->getParam('productos');
    foreach($productos as $producto) {
        if ($producto['total'] > 0) {
            $idProducto = $producto['IdProducto'];
            $cantidad = $producto['cantidad'];
            $tieneIgv = $producto['TieneIgv'];
            $precio = $producto['precio'];
            $nuevoPrecioContado = $producto['nuevoPrecioContado'];
            $idLote = $producto['IdLote'];
            
            $insert = $this->db->insert(array('hashMovimiento', 'IdProducto', 'Cantidad', 'TieneIgv', 'Precio', 'IdLote'))
            ->into('Lo_MovimientoDetalle')
            ->values(array($hash, $idProducto, $cantidad, $tieneIgv, $precio, $idLote));
            $insert->execute();
            
            // start actualizar precioventa producto
            $update = $this->db->update(array("PrecioCosto" => $precio, "PrecioContado" => $nuevoPrecioContado))
                               ->table('Gen_Producto')
                               ->where('IdProducto', '=', $idProducto);
            $update->execute();
            // end actualizar precioventa producto
        }
    }
    // end Movimiento Detalle

    
    return $response->withJson($data);
});



$app->get('/monedas', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_Moneda')->whereLike('Moneda', '%' . $request->getParam('q') . '%');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});


$app->get('/almacenes', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Lo_Almacen');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});


$app->get('/consultarRUC', function (Request $request, Response $response, array $args) {
    $headers = array(
        "Content-Type: application/json; charset=UTF-8",
        "Cache-Control: no-cache",
        "Pragma: no-cache"
    );
    //var_dump("https://www.facturacionelectronica.us/facturacion/controller/ws_consulta_rucdni_v2.php?usuario=20573027125&password=5i573m45&documento=" . $_GET['type'] . "&nro_documento=" . $_GET['numero']);
    $ch = curl_init("https://www.facturacionelectronica.us/facturacion/controller/ws_consulta_rucdni_v2.php?usuario=20573027125&password=5i573m45&documento=" . $_GET['type'] . "&nro_documento=" . $_GET['numero']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    //quitar en produccion
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //curl_setopt($ch, CURLOPT_USERPWD, "PRUEBA:LOG");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    // Se cierra el recurso CURL y se liberan los recursos del sistema
    curl_close($ch);
    if (!$response) {
        return false;
    } else {
      header('Content-Type: application/json');
      echo $response;
    }
});













$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();