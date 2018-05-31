<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$config['debug'] = true;
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = "127.0.0.1";
$config['db']['user']   = "root";
$config['db']['pass']   = "";
$config['db']['dbname'] = "neurofac_rojas";

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


function getNow() {
    return date_create('now', timezone_open('America/Lima'))->format('Y-m-d H:i:s');
}


$app->get('/categorias', function (Request $request, Response $response, array $args) {

    $select = $this->db->select()->from('Gen_ProductoCategoria');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});


$app->get('/marcas', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_ProductoMarca');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});


$app->get('/mediciones', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_ProductoMedicion');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});


$app->get('/modelos', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_ProductoModelo');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});


$app->get('/tallas', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_ProductoTalla');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});


$app->get('/productos', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_Producto');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});


$app->get('/proveedores', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Lo_Proveedor');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

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

    $insert = $this->db->insert(array('IdProductoMarca', 'IdProductoFormaFarmaceutica', 'IdProductoMedicion', 'IdProductoCategoria', 'IdProductoModelo', 'IdProductoTalla', 'Producto', 'FechaReg', 'Hash', 'ControlaStock', 'PorcentajeUtilidad', 'Genero', 'Color', 'Botapie'))
                       ->into('Gen_Producto')
                       ->values(array($idProductoMarca, $idProductoFormaFarmaceutica, $idProductoMedicion, $idProductoCategoria, $idProductoModelo, $idProductoTalla, $producto, $fechaReg, $hash, $controlaStock, $porcentajeUtilidad, $genero, $color, $botapie));
    $insertId = $insert->execute();
    
    // Generando codigo de barras
    $categoria = $request->getParam('categoria')['ProductoCategoria'];
    $codigoBarra = substr($categoria, 0, 2) . $insertId . substr($producto, 0, 2);
    
    $update = $this->db->update(array("CodigoBarra" => $codigoBarra))
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

   /* echo "<style>@page {
        size: 11cm 4cm;
        margin: 10%;
    }</style>";
    $barcodes = '';
    foreach($data as $key => $producto) {
        for($i = 0; $i < $producto['Cantidad']; $i++) {
            echo $generator->getBarcode($producto['CodigoBarra'], $generator::TYPE_CODE_128) . '---';
        }
    }*/
    //var_dump($barcodes);exit();
    return $response->withJson($data);
});





$app->run();