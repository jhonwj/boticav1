<?php
// quitar esto cuando este mejorada la validacion
// include_once('../views/validateUser.php');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

// EXCEL 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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



function getNow($format = "Y-m-d H:i:s") {
    return date_create('now', timezone_open('America/Lima'))->format($format);
}


$app->get('/categorias', function (Request $request, Response $response, array $args) {
    $q = $request->getParam('q');
    $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;
    
    $select = "SELECT * FROM Gen_ProductoCategoria";
    $select .= " WHERE ProductoCategoria LIKE '" . $q . "%' ";    

    if ($limit) {
        // $limit = $request->getParam('limit');
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
    $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;

    $select = "SELECT * FROM Gen_ProductoMarca";
    $select .= " WHERE ProductoMarca LIKE '" . $q . "%' ";

    if ($limit) {
        // $limit = $request->getParam('limit');
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


/* $app->get('/modelos', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_ProductoModelo')
                ->whereLike('ProductoModelo','%' . $request->getParam('q') . '%');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
}); */

/* $app->post('/modelos', function (Request $request, Response $response, array $args) {
    
    $productoModelo = $request->getParam('ProductoModelo');
    $anulado = 0;

    $insert = $this->db->insert(array('ProductoModelo', 'Anulado', 'FechaReg'))
                       ->into('Gen_ProductoModelo')
                       ->values(array($productoModelo, $anulado, getNow()));
    $insertId = $insert->execute();
    
    return $response->withJson(array("insertId" => $insertId, "ProductoModelo" => $productoModelo));
}); */



$app->get('/tallas', function (Request $request, Response $response, array $args) {
    $q = $request->getParam('q');
    $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;

    $select = "SELECT * FROM Gen_ProductoTalla";
    $select .= " WHERE ProductoTalla LIKE '" . $q . "%' ";

    if ($limit) {
        // $limit = $request->getParam('limit');
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

$app->post('/tallas', function (Request $request, Response $response, array $args) {
    
    $productoTalla = $request->getParam('ProductoTalla');
    $anulado = 0;

    $insert = $this->db->insert(array('ProductoTalla', 'Anulado', 'FechaReg'))
                       ->into('Gen_ProductoTalla')
                       ->values(array($productoTalla, $anulado, getNow()));
    $insertId = $insert->execute();
    
    return $response->withJson(array("insertId" => $insertId, "ProductoTalla" => $productoTalla));
});


$app->get('/productos/id/{id}', function (Request $request, Response $response, array $args) {
    $select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca,
    Gen_ProductoTalla.ProductoTalla, Gen_ProductoMedicion.ProductoMedicion
    FROM Gen_Producto 
    INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
    INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
    INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
    LEFT JOIN Gen_ProductoTalla ON Gen_Producto.IdProductoTalla = Gen_ProductoTalla.IdProductoTalla ";

    $select .= " WHERE Gen_Producto.IdProducto = '" . $args['id'] . "'";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();    

    return $response->withJson($data);
});

$app->get('/productos/colores', function (Request $request, Response $response, array $args) {
    $select = "SELECT DISTINCT Color FROM Gen_Producto";
    $select .= " WHERE Gen_Producto.Color LIKE '%" . $request->getParam('q') . "%' ";

    if ($request->getParam('sortBy')) {
        $sortBy = $request->getParam('sortBy');
        $sortDesc = $request->getParam('sortDesc');
        $orientation = $sortDesc ? 'DESC' : 'ASC';
        $select .= " ORDER BY " . $sortBy . " " . $orientation;
    }

    $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;
    if ($limit) {
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
$app->get('/productos/generos', function (Request $request, Response $response, array $args) {
    $select = "SELECT DISTINCT Genero FROM Gen_Producto";
    $select .= " WHERE Gen_Producto.Genero LIKE '%" . $request->getParam('q') . "%' ";

    if ($request->getParam('sortBy')) {
        $sortBy = $request->getParam('sortBy');
        $sortDesc = $request->getParam('sortDesc');
        $orientation = $sortDesc ? 'DESC' : 'ASC';
        $select .= " ORDER BY " . $sortBy . " " . $orientation;
    }

    $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;
    if ($limit) {
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
$app->get('/productos/botapies', function (Request $request, Response $response, array $args) {
    $select = "SELECT DISTINCT Botapie FROM Gen_Producto";
    $select .= " WHERE Gen_Producto.Botapie LIKE '%" . $request->getParam('q') . "%' ";

    if ($request->getParam('sortBy')) {
        $sortBy = $request->getParam('sortBy');
        $sortDesc = $request->getParam('sortDesc');
        $orientation = $sortDesc ? 'DESC' : 'ASC';
        $select .= " ORDER BY " . $sortBy . " " . $orientation;
    }

    $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;
    if ($limit) {
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
$app->get('/productos/modelos', function (Request $request, Response $response, array $args) {
    $select = "SELECT DISTINCT ProductoModelo FROM Gen_Producto";
    $select .= " WHERE Gen_Producto.ProductoModelo LIKE '%" . $request->getParam('q') . "%' ";

    if ($request->getParam('sortBy')) {
        $sortBy = $request->getParam('sortBy');
        $sortDesc = $request->getParam('sortDesc');
        $orientation = $sortDesc ? 'DESC' : 'ASC';
        $select .= " ORDER BY " . $sortBy . " " . $orientation;
    }

    $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;
    if ($limit) {
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


$app->get('/productos', function (Request $request, Response $response, array $args) {
    $select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca,
        Gen_ProductoTalla.ProductoTalla, Gen_ProductoMedicion.ProductoMedicion
        FROM Gen_Producto 
        INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
        INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
        INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
        LEFT JOIN Gen_ProductoTalla ON Gen_Producto.IdProductoTalla = Gen_ProductoTalla.IdProductoTalla ";
    
    if ($request->getParam('filter')) {
        $filter = $request->getParam('filter');

        if(is_array($filter)) {
            $select .= " WHERE Gen_Producto.Producto LIKE '%" . (isset($filter['producto']) ? addslashes($filter['producto']) : '') . 
                       "%' AND Gen_Producto.CodigoBarra LIKE '" . (isset($filter['codigo']) ? addslashes($filter['codigo']) : '') . 
                       "%' ANd Gen_Producto.Color LIKE '" . (isset($filter['color']) ? addslashes($filter['color']) : '') . 
                       "%' ANd Gen_ProductoMarca.ProductoMarca LIKE '" . (isset($filter['marca']) ? addslashes($filter['marca']) : '') . 
                       "%' ANd Gen_Producto.ProductoModelo LIKE '" . (isset($filter['modelo']) ? addslashes($filter['modelo']) : '') . 
                       "%' AND Gen_ProductoCategoria.ProductoCategoria LIKE '" . (isset($filter['categoria']) ? addslashes($filter['categoria']) : '') . 
                       "%' AND Gen_Producto.Genero LIKE '" . (isset($filter['genero']) ? addslashes($filter['genero']) : '') . 
                       "%' AND Gen_Producto.Botapie LIKE '" . (isset($filter['botapie']) ? addslashes($filter['botapie']) : '') . 
                       "%' AND Gen_ProductoTalla.ProductoTalla LIKE '" . (isset($filter['talla']) ? addslashes($filter['talla']) : '%') . 
                       "' "; 
            
        } else {
            $select .= " WHERE Gen_Producto.Producto LIKE '%" . $filter . 
                       "%' OR Gen_Producto.CodigoBarra LIKE '%" . $filter . 
                       "%' OR Gen_Producto.Color LIKE '%" . $filter .
                       "%' OR Gen_Producto.Genero LIKE '%" . $filter .  
                       "%' OR Gen_ProductoMarca.ProductoMarca LIKE '%" . $filter . 
                       "%' OR Gen_Producto.ProductoModelo LIKE '%" . $filter . 
                       "%' OR Gen_ProductoCategoria.ProductoCategoria LIKE '%" . $filter . 
                       "%' ";        
        }


       /* $select .= " WHERE Gen_Producto.Producto LIKE '%" . $filter . 
                   "%' OR Gen_Producto.CodigoBarra LIKE '%" . $filter . 
                   "%' OR Gen_Producto.Color LIKE '%" . $filter . 
                   "%' OR Gen_Producto.Genero LIKE '%" . $filter . 
                   "%' OR Gen_Producto.ProductoModelo LIKE '%" . $filter . 
                   "%' OR Gen_ProductoMarca.ProductoMarca LIKE '%" . $filter . 
                   "%' OR Gen_ProductoCategoria.ProductoCategoria LIKE '%" . $filter . 
                   "%' ";        */
    } else {
        $select .= " WHERE Gen_Producto.Producto LIKE '%" . $request->getParam('q') . "%' OR Gen_Producto.ProductoModelo LIKE '" . $request->getParam('q') . "%'";
    }

    if ($request->getParam('sortBy')) {
        $sortBy = $request->getParam('sortBy');
        $sortDesc = $request->getParam('sortDesc');
        $orientation = $sortDesc ? 'DESC' : 'ASC';
        $select .= " ORDER BY " . $sortBy . " " . $orientation;
    }

    $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;
    if ($limit) {
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

$app->get('/productos/kardex/{id}', function (Request $request, Response $response, array $args) {

    $idProducto = $args['id'];
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaHasta = $request->getParam('fechaHasta');

    $strIngresos = stringIngresoUndProducto($idProducto, $idAlmacen, $fechaHasta);
    $strSalidas = stringSalidaUndProducto($idProducto, $idAlmacen, $fechaHasta);
    $strVentas = stringSalidaVentaUndProducto($idProducto, $idAlmacen, $fechaHasta);

    $select = $strIngresos . ' UNION ALL ' . $strSalidas . ' UNION ALL ' . $strVentas; 
    $select .= ' ORDER BY Fecha ASC';
    
    $limit = $request->getParam('limit') ? $request->getParam('limit') :  0;
    if ($limit) {
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




$app->get('/proveedores', function (Request $request, Response $response, array $args) {
    $q = $request->getParam('q');

    $select = "SELECT *, IFNULL(CONCAT(Ruc, ' - ', Proveedor), '-') AS ProveedorRuc FROM Lo_Proveedor";
    $select .= " WHERE Proveedor LIKE '%" . $q . "%' OR Ruc LIKE '%" . $q . "%' ";

    $limit = $request->getParam('limit') ? $request->getParam('limit') : 10;
    if ($limit) {
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
    // $idProductoModelo = $request->getParam('modelo')['IdProductoModelo'];
    $idProductoTalla = $request->getParam('talla')['IdProductoTalla'];
    $idProductoFormaFarmaceutica = 1;
    $producto = $request->getParam('Producto');
    $productoModelo = $request->getParam('ProductoModelo') ? $request->getParam('ProductoModelo') : '';
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
        $precioContado = $request->getParam('PrecioContado');


        $update = $this->db->update(array(
                            "Producto" => $producto . '-' . $codigoBarra,
                            "IdProductoMarca" => $idProductoMarca,
                            "IdProductoFormaFarmaceutica" => $idProductoFormaFarmaceutica,
                            "IdProductoMedicion" => $idProductoMedicion,
                            "IdProductoCategoria" => $idProductoCategoria,
                            // "IdProductoModelo" => $idProductoModelo,
                            "IdProductoTalla" => $idProductoTalla,
                            "ControlaStock" => $controlaStock,
                            "PorcentajeUtilidad" => $porcentajeUtilidad,
                            "Genero" => $genero, "Color" => $color, "Botapie" => $botapie, "Anulado" => $anulado,
                            "ProductoModelo" => $productoModelo,
                            "PrecioContado" => $precioContado
                        ))
                       ->table('Gen_Producto')
                       ->where('IdProducto', '=', $idProducto);
        $affectedRows = $update->execute();
        return $response->withJson(array("affectedRows" => $affectedRows));
    }
    // Fin actualizacion producto

    // Inicio verificar Producto
    $select = "SELECT * FROM Gen_Producto
        WHERE IdProductoMarca = '" . $idProductoMarca
        . "' AND IdProductoMedicion = '" . $idProductoMedicion
        . "' AND IdProductoCategoria = '" . $idProductoCategoria
        . "' AND IdProductoTalla = '" . $idProductoTalla
        . "' AND ProductoModelo = '" . $productoModelo
        . "' AND Genero = '" . $genero
        . "' AND Botapie = '" . $botapie
        . "' AND Color = '" . $color
        . "' AND Producto like '" . $producto . "-%'";

    $stmt = $this->db->query($select);
    $prod = $stmt->fetch();
    $stmt->execute();

    //return $response->withJson(array('select' => empty($prod)));
    
    if (!empty($prod)) {
        $data = array(
            'error' => 'Producto ya existe',
            'codigoBarra' => $prod['CodigoBarra'],
            'insertId' => $prod['IdProducto']
        );
        return $response->withJson($data);
    }
    // Final verificar Movimiento

    $insert = $this->db->insert(array('IdProductoMarca', 'IdProductoFormaFarmaceutica', 'IdProductoMedicion', 'IdProductoCategoria', 'IdProductoTalla', 'Producto', 'FechaReg', 'Hash', 'ControlaStock', 'PorcentajeUtilidad', 'Genero', 'Color', 'Botapie', 'Anulado', 'ProductoModelo'))
                       ->into('Gen_Producto')
                       ->values(array($idProductoMarca, $idProductoFormaFarmaceutica, $idProductoMedicion, $idProductoCategoria, $idProductoTalla, $producto, $fechaReg, $hash, $controlaStock, $porcentajeUtilidad, $genero, $color, $botapie, $anulado, $productoModelo));
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
    
    $select = "SELECT Lo_MovimientoDetalle.IdProducto, Gen_Producto.Producto, Gen_ProductoMarca.ProductoMarca, Gen_Producto.ProductoModelo,
        Gen_Producto.Color, Gen_Producto.CodigoBarra, Gen_Producto.PrecioContado, Gen_ProductoTalla.ProductoTalla, Lo_MovimientoDetalle.Cantidad,
        Gen_Producto.Botapie 
        FROM Lo_MovimientoDetalle 
        INNER JOIN Gen_Producto ON Lo_MovimientoDetalle.IdProducto = Gen_Producto.IdProducto
        INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
        LEFT JOIN Gen_ProductoTalla ON Gen_Producto.IdProductoTalla = Gen_ProductoTalla.IdProductoTalla
        WHERE hashMovimiento = '$hashMovimiento'";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});




$app->get('/movimientos/tipos', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM Lo_MovimientoTipo WHERE Anulado=0";
    $select .= " AND TipoMovimiento LIKE '%" . $request->getParam('q') . "%' ";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});



$app->post('/movimientos', function (Request $request, Response $response) { 
    //return  $response->withJson($request->getParam('productos'));exit();
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
        //if ($producto['total'] > 0) {
            $idProducto = $producto['IdProducto'];
            $cantidad = $producto['cantidad'];
            $tieneIgv = isset($producto['TieneIgv']) ? $producto['TieneIgv'] : 0;
            $precio = $producto['precio'];
            $nuevoPrecioContado = $producto['nuevoPrecioContado'];
            $idLote = isset($producto['IdLote']) ? $producto['IdLote'] : 0;
            
            $insert = $this->db->insert(array('hashMovimiento', 'IdProducto', 'Cantidad', 'TieneIgv', 'Precio', 'IdLote'))
            ->into('Lo_MovimientoDetalle')
            ->values(array($hash, $idProducto, $cantidad, $tieneIgv, $precio, $idLote));
            $insert->execute();
            
            // start actualizar precioventa producto
            $alterarProductos = $request->getParam('movimiento')['alterarProductos'];
            if ($alterarProductos) {
                $update = $this->db->update(array("PrecioCosto" => $precio, "PrecioContado" => $nuevoPrecioContado))
                                   ->table('Gen_Producto')
                                   ->where('IdProducto', '=', $idProducto);
                $update->execute();
            }
            // end actualizar precioventa producto
        //}
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


function stringIngresoUndProducto($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT Lo_Movimiento.MovimientoFecha AS Fecha, Lo_MovimientoTipo.TipoMovimiento AS Detalle, Lo_Movimiento.Serie, Lo_Movimiento.Numero, Lo_Proveedor.Proveedor AS Nombres, 
    Lo_MovimientoDetalle.Cantidad AS IngresoCantidad, Lo_MovimientoDetalle.Precio AS IngresoPrecio, 
    '0' AS SalidaCantidad, '0' AS SalidaPrecio, 
    '0' AS Descuento FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        LEFT JOIN Lo_Proveedor ON Lo_Movimiento.IdProveedor = Lo_Proveedor.IdProveedor
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenDestino = $idAlmacen
            AND Lo_MovimientoDetalle.IdProducto=$idProducto AND Lo_Movimiento.Anulado=0 
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";

    return $select;
}
function stringSalidaUndProducto($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT Lo_Movimiento.MovimientoFecha AS Fecha, Lo_MovimientoTipo.TipoMovimiento AS Detalle, Lo_Movimiento.Serie, Lo_Movimiento.Numero, Lo_Proveedor.Proveedor AS Nombres, 
    '0' AS IngresoCantidad, '0' AS IngresoPrecio, 
    Lo_MovimientoDetalle.Cantidad as SalidaCantidad, Lo_MovimientoDetalle.Precio AS SalidaPrecio, 
    '0' AS Descuento FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        LEFT JOIN Lo_Proveedor ON Lo_Movimiento.IdProveedor = Lo_Proveedor.IdProveedor
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenOrigen = $idAlmacen
            AND Lo_MovimientoDetalle.IdProducto=$idProducto AND Lo_Movimiento.Anulado=0 
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";

    return $select;
}
function stringSalidaVentaUndProducto($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT Ve_DocVenta.FechaDoc AS Fecha, CONCAT('VENTA - ', Ve_DocVentaTipoDoc.TipoDoc) AS Detalle, Ve_DocVenta.Serie, Ve_DocVenta.Numero, 
    Ve_DocVentaCliente.Cliente AS Nombres, 
    '0' AS IngresoCantidad, '0' AS IngresoPrecio, 
    Ve_DocVentaDet.Cantidad as SalidaCantidad, Ve_DocVentaDet.Precio AS SalidaPrecio, 
    Ve_DocVentaDet.Descuento FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
        LEFT JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente= Ve_DocVentaCliente.IdCliente
        WHERE Ve_DocVenta.IdAlmacen = $idAlmacen
            AND Ve_DocVentaDet.IdProducto = $idProducto
            AND Ve_DocVenta.Anulado = 0
            AND Ve_DocVenta.FechaDoc < '$fechaHasta')";

    return $select;
}



function stringIngresoUnd($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT IFNULL(SUM(Lo_MovimientoDetalle.Cantidad), 0) AS cantidad FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenDestino = $idAlmacen
            AND Lo_MovimientoDetalle.IdProducto=$idProducto AND Lo_Movimiento.Anulado=0 
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";

    return $select;
}

function stringSalidaUnd($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT IFNULL(SUM(Lo_MovimientoDetalle.Cantidad), 0) AS cantidad FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenOrigen = $idAlmacen
            AND Lo_MovimientoDetalle.IdProducto=$idProducto AND Lo_Movimiento.Anulado=0 
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";

    return $select;
}

function stringSalidaVentaUnd($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT IFNULL(SUM(Ve_DocVentaDet.Cantidad), 0) AS cantidad FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
        WHERE Ve_DocVenta.IdAlmacen = $idAlmacen
            AND Ve_DocVentaDet.IdProducto = $idProducto
            AND Ve_DocVenta.Anulado = 0
            AND Ve_DocVentaTipoDoc.VaRegVenta = 1
            AND Ve_DocVenta.FechaDoc < '$fechaHasta')";

    return $select;
}

$app->get('/productos/stock/ingresos', function (Request $request, Response $response, array $args) {
    /*$idProducto = $request->getParam('idProducto');
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaHasta = $request->getParam('fechaHasta');

    $data = stockIngresoUnd($this->db, $idProducto, $idAlmacen, $fechaHasta);

    return $response->withJson($data);*/
});
$app->get('/productos/stock/salidas', function (Request $request, Response $response, array $args) {

    /*// SALIDAS POR MOVIMIENTO
    $idProducto = $request->getParam('idProducto');
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaHasta = $request->getParam('fechaHasta');

    $salidaUnd = stockSalidaUnd($this->db, $idProducto, $idAlmacen, $fechaHasta);
    $cantidadSalida = $salidaUnd['cantidad'];

    // SALIDAS POR VENTAS
    $salidaVentaUnd = stockSalidaVentaUnd($this->db, $idProducto, $idAlmacen, $fechaHasta);
    $salidaVentaUnd['cantidad'] += $cantidadSalida;

    return $response->withJson($salidaVentaUnd);*/
});



$app->get('/productos/stock', function (Request $request, Response $response, array $args) {

    $idAlmacen = $request->getParam('idAlmacen');
    $fechaHasta = $request->getParam('fechaHasta') ? $request->getParam('fechaHasta') : getNow();

    $strIngresoUnd = stringIngresoUnd('Gen_Producto.IdProducto', $idAlmacen, $fechaHasta);
    $strSalidaUnd = stringSalidaUnd('Gen_Producto.IdProducto', $idAlmacen, $fechaHasta);
    $strSalidaVentaUnd = stringSalidaVentaUnd('Gen_Producto.IdProducto', $idAlmacen, $fechaHasta);
    
    $select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca,
        Gen_ProductoTalla.ProductoTalla, Gen_ProductoMedicion.ProductoMedicion,

        $strIngresoUnd AS StockIngresoUnd, 
        $strSalidaUnd AS StockSalidaUnd,
        $strSalidaVentaUnd AS StockSalidaVentaUnd,
        ((SELECT StockIngresoUnd) - (SELECT StockSalidaUnd) - (SELECT StockSalidaVentaUnd)) AS stock 


        FROM Gen_Producto 
        INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
        INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
        INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
        LEFT JOIN Gen_ProductoTalla ON Gen_Producto.IdProductoTalla = Gen_ProductoTalla.IdProductoTalla ";
    
    if ($request->getParam('filter')) {
        $filter = $request->getParam('filter');
        if(is_array($filter)) {
            $select .= " WHERE Gen_Producto.Producto LIKE '%" . (isset($filter['producto']) ? addslashes($filter['producto']) : '') . 
                       "%' AND Gen_Producto.CodigoBarra LIKE '" . (isset($filter['codigo']) ? addslashes($filter['codigo']) : '') . 
                       "%' ANd Gen_Producto.Color LIKE '" . (isset($filter['color']) ? addslashes($filter['color']) : '') . 
                       "%' ANd Gen_ProductoMarca.ProductoMarca LIKE '" . (isset($filter['marca']) ? addslashes($filter['marca']) : '') . 
                       "%' ANd Gen_Producto.ProductoModelo LIKE '" . (isset($filter['modelo']) ? addslashes($filter['modelo']) : '') . 
                       "%' AND Gen_ProductoCategoria.ProductoCategoria LIKE '" . (isset($filter['categoria']) ? addslashes($filter['categoria']) : '') . 
                       "%' AND Gen_Producto.Genero LIKE '" . (isset($filter['genero']) ? addslashes($filter['genero']) : '') . 
                       "%' AND Gen_Producto.Botapie LIKE '" . (isset($filter['botapie']) ? addslashes($filter['botapie']) : '') . 
                       "%' AND Gen_ProductoTalla.ProductoTalla LIKE '" . (isset($filter['talla']) ? addslashes($filter['talla']) : '%') . 
                       "' "; 
            
        } else {
            $select .= " WHERE Gen_Producto.Producto LIKE '%" . $filter . 
                       "%' OR Gen_Producto.CodigoBarra LIKE '%" . $filter . 
                       "%' OR Gen_Producto.Color LIKE '%" . $filter . 
                       "%' OR Gen_ProductoMarca.ProductoMarca LIKE '%" . $filter . 
                       "%' OR Gen_Producto.ProductoModelo LIKE '%" . $filter . 
                       "%' OR Gen_ProductoCategoria.ProductoCategoria LIKE '%" . $filter . 
                       "%' ";        
        }
    } else {
        $select .= " WHERE Gen_Producto.Producto LIKE '%" . $request->getParam('q') . "%' ";
    }

    if ($request->getParam('idProductos')) {
        // mandar array de IDS 
        $idProductos = $request->getParam('idProductos');
        $select .= " AND Gen_Producto.IdProducto IN (" . implode(',', $idProductos) . ")";
    }

    if (isset($request->getParam('filter')['stock'])) {
        $filterStock = $request->getParam('filter')['stock'];
        if ($filterStock == 'mayor') {
            $select .= " HAVING stock > 0";
        }
        if ($filterStock == 'menor') {
            $select .= " HAVING stock <= 0";
        }
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
    // print_r($select);exit();
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();
    
    return $response->withJson($data);
});


$app->get('/movimientos/numero', function (Request $request, Response $response, array $args) {
    $serie = $request->getParam('serie');
    $idMovimientoTipo = $request->getParam('idMovimientoTipo');

    $select = "SELECT Lo_Movimiento.IdMovimientoTipo, Lo_MovimientoTipo.Tipo, Lo_Movimiento.Serie, Lo_Movimiento.Numero, (Lo_Movimiento.Numero+1) as NuevoNumero
        FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        WHERE Lo_Movimiento.Serie = '$serie' AND Lo_Movimiento.IdMovimientoTipo = '$idMovimientoTipo'
        ORDER BY Lo_Movimiento.Numero DESC
        LIMIT 1";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();    

    return $response->withJson($data);
});




// VENTAS

$app->get('/ventas/tipos', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Ve_DocVentaTipoDoc')->whereLike('TipoDoc', '%' . $request->getParam('q') . '%');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});

$app->get('/ventas/puntos', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Ve_DocVentaPuntoVenta')->whereLike('PuntoVenta', '%' . $request->getParam('q') . '%');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});

$app->get('/ventas/descuento/[{puntos}]', function (Request $request, Response $response, array $args) {
    $puntos = $request->getAttribute('puntos', 0);

    $select = "SELECT * FROM Ve_DocVentaDescuento WHERE IdDocVentaDescuento = 1";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();
    $data['descuento'] = $data['ValorPunto'] * $puntos;

    return $response->withJson($data);
});

$app->get('/ventas/descuento', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Ve_DocVentaDescuento')->where('IdDocVentaDescuento', '=', 1);
    $stmt = $select->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});

$app->post('/ventas/descuento', function (Request $request, Response $response) {
    $id = 1;
    $porCada = $request->getParam('porCada');
    $puntos = $request->getParam('puntos');
    $valorPunto = $request->getParam('valorPunto');
    
    $update = "UPDATE Ve_DocVentaDescuento SET PorCada=$porCada, Puntos=$puntos, ValorPunto=$valorPunto 
        WHERE IdDocVentaDescuento=$id";
    
    $stmt = $this->db->prepare($update);
    $updated = $stmt->execute();

    return $response->withJson(array(
        "IdDocVentaDescuento" => $id,
        "PorCada" => $porCada,
        "Puntos" => $puntos,
        "ValorPunto" => $valorPunto
    ));
});


$app->get('/ventas/numero', function (Request $request, Response $response, array $args) {
    $idPuntoVenta = $request->getParam('idPuntoVenta');
    $idTipoDoc = $request->getParam('idTipoDoc');

    // Obtener Serie
    $selectSerie = "SELECT Serie FROM Ve_DocVentaPuntoVentaDet WHERE IdDocVentaPuntoVenta=$idPuntoVenta AND IdDocVentaTipoDoc=$idTipoDoc";
    
    $stmt = $this->db->query($selectSerie);
    $stmt->execute();
    $selectSerie = $stmt->fetch();
    $serie = $selectSerie['Serie'];
    
    // Obtener siguiente Numero
    $selectNumero = "SELECT Numero + 1 AS NuevoNumero FROM Ve_DocVenta 
        WHERE IdDocVentaPuntoVenta=$idPuntoVenta AND IdTipoDoc=$idTipoDoc 
        ORDER BY Numero DESC LIMIT 1";
    $stmt = $this->db->query($selectNumero);
    $stmt->execute();
    $selectNumero = $stmt->fetch();
    $numero = $selectNumero['NuevoNumero'];
    return $response->withJson(array(
        "serie" => $serie,
        "numero" => $numero ? $numero : 1
    ));
});


$app->get('/ventas/total', function (Request $request, Response $response, array $args) {
    $fechaInicio = $request->getParam('fechaInicio');
    $fechaFin = $request->getParam('fechaFin');
    // $fechaInicio = $fechaInicio ? $fechaInicio : getNow('Y') . '-01-01';
    $fechaInicio = $fechaInicio ? $fechaInicio : getNow('Y-m-d');
    $fechaFin = $fechaFin ? $fechaFin : getNow('Y-m-d');

    $select = "SELECT ROUND(SUM((Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio) - Ve_DocVentaDet.Descuento), 2) AS total 
        FROM Ve_DocVentaDet
        INNER JOIN Ve_DocVenta ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta
        WHERE Ve_DocVenta.FechaDoc BETWEEN CAST('" . $fechaInicio . "' AS DATETIME) AND CONCAT('" . $fechaFin . "',' 23:59:59')";
    //print_r($select);exit();
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});


$app->get('/ventas/usuario', function (Request $request, Response $response) {
    $usuario = $request->getParam('usuario');
    $fechaInicio = $request->getParam('fechaInicio');
    $fechaFin = $request->getParam('fechaFin');
    $fechaInicio = $fechaInicio ? $fechaInicio : getNow('Y') . '-01-01';
    $fechaFin = $fechaFin ? $fechaFin : getNow('Y-m-d');

    $select = "SELECT ROUND(SUM((Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio) - Ve_DocVentaDet.Descuento), 2) AS total FROM Ve_DocVentaDet
    INNER JOIN Ve_DocVenta ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta
    INNER JOIN Seg_Usuario ON Ve_DocVenta.UsuarioReg = Seg_Usuario.Usuario
    WHERE Seg_Usuario.Usuario = '$usuario' 
    AND Ve_DocVenta.FechaDoc BETWEEN CAST('" . $fechaInicio . "' AS DATETIME) AND CONCAT('" . $fechaFin . "',' 23:59:59')";
    //print_r($select);exit();
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});


$app->get('/ventas', function (Request $request, Response $response) { 
    $filtros = '';

    $select = "SELECT Ve_DocVenta.idDocVenta, Ve_DocVenta.FechaDoc, Ve_DocVentaTipoDoc.TipoDoc, Ve_DocVentaTipoDoc.TieneIgv, 
        Ve_DocVenta.Anulado, Ve_DocVenta.Serie, Ve_DocVenta.Numero, Ve_DocVentaCliente.Cliente, Ve_DocVenta.UsuarioReg,
        IFNULL((SELECT SUM(ROUND((Ve_DocVentaDet.Precio * Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento, 2)) FROM Ve_DocVentaDet WHERE Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta), 0 ) AS Total
        FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
        LEFT JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente";
    
    $select .= " WHERE CONCAT(Ve_DocVenta.Serie, '-', Ve_DocVenta.Numero) LIKE '%" . $request->getParam('q') . "%' ";
    
    
    $idAlmacen = $request->getParam('idAlmacen');
    if ($idAlmacen) {
        $filtros .= " AND Ve_DocVenta.IdAlmacen =  $idAlmacen";
    }

    $filter = ($request->getParam('filter')) ? $request->getParam('filter') : [];
    if (!isset($filter['fechaInicio'])) {
        //$filter['fechaInicio'] = getNow('Y') . '-01-01';
        $filter['fechaInicio'] = getNow('Y-m-d');
    }

    if (!isset($filter['fechaFin'])) {
        $filter['fechaFin'] = getNow('Y-m-d');
    }
    
    if ($filter) {
        if(is_array($filter)) {

            if (isset($filter['vendedor']) && $filter['vendedor']) $filtros .= " AND Ve_DocVenta.UsuarioReg = '" . $filter['vendedor'] . "'";
            if (isset($filter['declarado'])) $filtros .= " AND Ve_DocVentaTipoDoc.VaRegVenta = " . $filter['declarado'];
            if (isset($filter['fechaInicio']) && isset($filter['fechaFin'])) $filtros .= " AND Ve_DocVenta.FechaDoc BETWEEN CAST('" . $filter['fechaInicio'] . "' AS DATETIME) AND CONCAT('" . $filter['fechaFin'] . "',' 23:59:59')";
            
        } else {
        }
    }

    $select .= $filtros;

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

    // print_r($select);exit();
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();
    
    return $response->withJson($data);

});


$app->get('/ventas/count', function (Request $request, Response $response) {
    $idAlmacen = $request->getParam('idAlmacen');
    $select = "SELECT COUNT(*) as total FROM Ve_DocVenta WHERE Ve_DocVenta.IdAlmacen = $idAlmacen";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});


$app->post('/ventas', function (Request $request, Response $response) { 
    $vendedor = 'xx';
    if(isset($_SESSION['user'])) {
        $vendedor = $_SESSION['user'];
    }

    // START INSERTAR NUEVA VENTA
    $idDocVentaPuntoVenta = $request->getParam('puntoVenta')['IdDocVentaPuntoVenta'];
    $idCliente = $request->getParam('cliente')['IdCliente'];
    $idTipoDoc = $request->getParam('tipoVenta')['IdTipoDoc']; 
    $idAlmacen = $request->getParam('almacen')['IdAlmacen']; 
    $serie = $request->getParam('Serie'); 
    $numero = $request->getParam('Numero');
    $anulado = 0;
    $usuarioReg = isset($request->getParam('vendedor')['Usuario']) ? $request->getParam('vendedor')['Usuario'] : $vendedor;
    
    $esCredito = $request->getParam('EsCredito');
    $fechaCredito = $request->getParam('FechaCredito');
    
    $insert = "INSERT INTO Ve_DocVenta (IdDocVentaPuntoVenta,IdCliente,IdTipoDoc,IdAlmacen,Serie,Numero,FechaDoc,Anulado,FechaReg,UsuarioReg,Hash, EsCredito, FechaCredito)
        VALUES ($idDocVentaPuntoVenta, $idCliente, $idTipoDoc, $idAlmacen, '$serie', '$numero', NOW(), $anulado, now(), '$usuarioReg', UNIX_TIMESTAMP(), $esCredito, '$fechaCredito')";
    
    $stmt = $this->db->prepare($insert);
    $inserted = $stmt->execute();
    $idDocVenta = $this->db->lastInsertId();
    // END INSERTAR NUEVA VENTA
    
    
    // START INSERTAR VENTA DETALLE
    $productos = $request->getParam('productos');
    $descuentoTotal = 0;
    foreach($productos as $producto) {
        if ($producto['total'] > 0) {
            $idProducto = $producto['IdProducto'];
            $cantidad = $producto['cantidad'];
            $precio = $producto['precio'];
            $descuento = $producto['descuento'];
            
            $insert = $this->db->insert(array('IdDocVenta', 'IdProducto', 'Cantidad', 'Precio', 'Descuento'))
                ->into('Ve_DocVentaDet')
                ->values(array($idDocVenta, $idProducto, $cantidad, $precio, $descuento));
            
            $insert->execute();
            $descuentoTotal += $descuento;
        }
    }
    // END VENTA DETALLE
    
    // START INSERTAR PAGO DETALLE
    $pagos = $request->getParam('pago');
    if (!$esCredito) {
        foreach($pagos as $pago) {
            if ($pago['monto'] > 0) {
                $idMetodoPago = $pago['IdMetodoPago'];
                $importe = $pago['monto'];
                $nroTarjeta = isset($pago['descripcion']) ? $pago['descripcion'] : '';
                $insert = $this->db->insert(array('IdDocVenta', 'IdMetodoPago', 'Importe', 'NroTarjeta'))
                    ->into('Ve_DocVentaMetodoPagoDet')
                    ->values(array($idDocVenta, $idMetodoPago, $importe, $nroTarjeta));
                $insert->execute();
            }
        }
    }
    // END PAGO DETALLE

    // START AÑADIR PUNTOS CLIENTE
    $puntosAplicados = $request->getParam('puntosAplicados'); 
    $totalCart = $request->getParam('totalCart'); 
    if(!$descuentoTotal) { // quitar condicional si siempre se otorgarán puntos
        // actualizar si la venta se pago en su totalidad, sin descuentos
        $select = "SELECT * FROM Ve_DocVentaDescuento WHERE IdDocVentaDescuento=1";
        $stmt = $this->db->query($select);
        $stmt->execute();
        $tablaDescuento = $stmt->fetch(); 

        if ($totalCart >= $tablaDescuento['PorCada']) {
            $puntosAdicionales = ($totalCart / $tablaDescuento['PorCada']) * $tablaDescuento['Puntos'];
            $update = "UPDATE Ve_DocVentaCliente SET Puntos=Puntos+$puntosAdicionales WHERE IdCliente=$idCliente";
            $stmt = $this->db->prepare($update);
            $updated = $stmt->execute();
        }
    }
    if ($puntosAplicados) {
        $update = "UPDATE Ve_DocVentaCliente SET Puntos=Puntos-$puntosAplicados WHERE IdCliente=$idCliente";
        $stmt = $this->db->prepare($update);
        $updated = $stmt->execute();
    }
    // END PUNTOS CLIENTE

    // return $response->withJson(array('insertId' => $nroTarjeta));
    
    $data = array(
        'insertId' => $idDocVenta
    );
    
    return $response->withJson($data);
});


$app->get('/preorden/count', function (Request $request, Response $response, array $args) {
    $select = "SELECT COUNT(*) as total FROM Ve_Preorden";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});


$app->get('/preorden', function (Request $request, Response $response) {
    $filter = $request->getParam('filter');
    $select = "SELECT Ve_PreOrden.IdPreOrden, Ve_PreOrden.IdCliente, Ve_DocVentaCliente.Cliente, Ve_DocVentaCliente.DniRuc, Ve_PreOrden.FechaReg 
        FROM Ve_PreOrden INNER JOIN Ve_DocVentaCliente ON Ve_PreOrden.IdCliente = Ve_DocVentaCliente.IdCliente";
    $select .= " WHERE Ve_DocVentaCliente.Cliente LIKE '%". $filter ."%' 
                 OR Ve_DocVentaCliente.DniRuc LIKE '" . $filter . "%'";
    $select .= " ORDER BY Ve_PreOrden.IdPreOrden DESC";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
    
});

$app->get('/preorden/detalle', function (Request $request, Response $response) {
    $idPreOrden = $request->getParam('idPreOrden');

    $select = "SELECT * FROM Ve_PreOrdenDet WHERE IdPreOrden=$idPreOrden";
    
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
    
});

$app->get('/cliente', function (Request $request, Response $response, array $args) {
    $idCliente = $request->getParam('idCliente');

    $select = "SELECT * FROM Ve_DocVentaCliente WHERE IdCliente=$idCliente";
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();    

    return $response->withJson($data);
});











$app->get('/usuarios', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM Seg_Usuario WHERE (Anulado != 1 OR Anulado IS NULL)";
    $select .= " AND Seg_Usuario.Usuario LIKE '%" . $request->getParam('q') . "%' ";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});

$app->get('/clientes', function (Request $request, Response $response, array $args) {
    $select = "SELECT *, IFNULL(CONCAT(DniRuc, ' - ', Cliente), '-') AS ClienteDniRuc FROM Ve_DocVentaCliente WHERE (Anulado != 1 OR Anulado IS NULL)";
    $select .= " AND Ve_DocVentaCliente.Cliente LIKE '%" . $request->getParam('q') . "%'";
    $select .= " OR Ve_DocVentaCliente.DniRuc LIKE '%" . $request->getParam('q') . "%'";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});

$app->post('/clientes', function (Request $request, Response $response) {
    $cliente = $request->getParam('Cliente');
    $dniRuc = $request->getParam('DniRuc');
    $direccion = $request->getParam('Direccion');
    $telefono = $request->getParam('Telefono');
    $email = $request->getParam('Email');

    $insert = $this->db->insert(array('Cliente', 'DniRuc', 'Direccion', 'Telefono', 'Email', 'Anulado', 'FechaReg'))
                       ->into('Ve_DocVentaCliente')
                       ->values(array($cliente, $dniRuc, $direccion, $telefono, $email, '0', getNow()));
    
    $insertId = $insert->execute();

    $select = "SELECT * FROM Ve_DocVentaCliente WHERE IdCliente=$insertId";
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();  

    return $response->withJson($data);
});


// REPORTES 
$app->get('/reporte/stock', function (Request $request, Response $response, array $args) use ($app) {
    $idAlmacen = $request->getParam('idAlmacen');
    
    $res = $app->subRequest('GET', '/productos/stock', 'idAlmacen=' . $idAlmacen);
    $productos = (string) $res->getBody();
    $productos = json_decode($productos, true);
    
    $excel = new Spreadsheet();
    //$sheet = $excel->setActiveSheetIndex(0);
    $sheet = $excel->getActiveSheet();
    $sheet->setCellValue('A1', 'CODIGO');
    $sheet->setCellValue('B1', 'CATEGORIA');
    $sheet->setCellValue('C1', 'PRODUCTO');
    $sheet->setCellValue('D1', 'MARCA');
    $sheet->setCellValue('E1', 'MODELO');
    $sheet->setCellValue('F1', 'COLOR');
    $sheet->setCellValue('G1', 'TALLA');
    $sheet->setCellValue('H1', 'GENERO');
    $sheet->setCellValue('I1', 'BOTAPIE');
    $sheet->setCellValue('J1', 'STOCK');

    $cont = 3;
    foreach($productos as $prod) {
        $sheet->setCellValue('A'.$cont, $prod['CodigoBarra']);
        $sheet->setCellValue('B'.$cont, $prod['ProductoCategoria']);
        $sheet->setCellValue('C'.$cont, $prod['Producto']);
        $sheet->setCellValue('D'.$cont, $prod['ProductoMarca']);
        $sheet->setCellValue('E'.$cont, $prod['ProductoModelo']);
        $sheet->setCellValue('F'.$cont, $prod['Color']);
        $sheet->setCellValue('G'.$cont, $prod['ProductoTalla']);
        $sheet->setCellValue('H'.$cont, $prod['Genero']);
        $sheet->setCellValue('I'.$cont, $prod['Botapie']);
        $sheet->setCellValue('J'.$cont, $prod['stock']);
        $cont += 1;
    }

    $excelWriter = new Xlsx($excel);

    $fileName = 'stock' . getNow('Y-m-d-H-i-s').  '.xlsx';
    $excelFileName = __DIR__ . '/reporte/' . $fileName;
    $excelWriter->save($excelFileName);
    // For Excel2007 and above .xlsx files   
    // $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response = $response->withHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    $stream = fopen($excelFileName, 'r+');
    return $response->withBody(new \Slim\Http\Stream($stream));
});








$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();