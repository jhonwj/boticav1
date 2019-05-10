<?php
// quitar esto cuando este mejorada la validacion
// include_once('../views/validateUser.php');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'sunat/sunat.php';

// EXCEL 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$config['debug'] = true;
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = "127.0.0.1";
$config['db']['user']   = "root";
// $config['db']['user']   = "neurosys_hotel";
$config['db']['pass']   = "";
// $config['db']['pass']   = "IX!!q!t(&Fc^";
$config['db']['dbname'] = "neurosys_hotel";
// $config['db']['dbname'] = "neurosys_hotel";

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
    $select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca, Gen_ProductoMedicion.ProductoMedicion
    FROM Gen_Producto 
    INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
    INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
    INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion ";

    
    if ($request->getParam('codigoBarra')) {
        $select .= " WHERE Gen_Producto.CodigoBarra = '" . $request->getParam('codigoBarra') . "'";        
    } else {
        $select .= " WHERE Gen_Producto.IdProducto = '" . $args['id'] . "'";
    }

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
$app->get('/productos/presentaciones', function (Request $request, Response $response, array $args) {
    $select = "SELECT DISTINCT ProductoPresentacion FROM Gen_Producto";
    $select .= " WHERE Gen_Producto.ProductoPresentacion LIKE '%" . $request->getParam('q') . "%' ";

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

$app->get('/productosdet', function (Request $request, Response $response, array $args) {
    $idProducto = $request->getParam('idProducto');

    $select = "SELECT Gen_Producto.Producto, Gen_Producto.PrecioCosto, Gen_Producto.PrecioContado, Gen_ProductoDet.* FROM Gen_ProductoDet 
        INNER JOIN Gen_Producto ON Gen_ProductoDet.IdProductoDet = Gen_Producto.IdProducto 
        WHERE Gen_ProductoDet.IdProducto=$idProducto";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});

$app->get('/productos/eshijo', function (Request $request, Response $response, array $args) {
    $idProducto = $request->getParam('idProducto');

    $select = "SELECT * FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProductoDet=$idProducto";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();
    if ($data) {
        $data = array("esHijo" => 1);
    }else {
        $data = array("esHijo" => 0);
    }
    return $response->withJson($data);
});
$app->get('/productos/espadre', function (Request $request, Response $response, array $args) {
    $idProducto = $request->getParam('idProducto');

    $select = "SELECT * FROM Gen_ProductoDet WHERE Gen_ProductoDet.IdProducto=$idProducto";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();
    if ($data) {
        $data = array("esPadre" => 1);
    }else {
        $data = array("esPadre" => 0);
    }
    return $response->withJson($data);
});

$app->get('/productos', function (Request $request, Response $response, array $args) {
    $select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca,
        Gen_ProductoMedicion.ProductoMedicion
        FROM Gen_Producto 
        INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
        INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
        INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion ";
    
    if ($request->getParam('filter')) {
        $filter = $request->getParam('filter');

        if(is_array($filter)) {
            $select .= " WHERE Gen_Producto.Producto LIKE '%" . (isset($filter['producto']) ? addslashes($filter['producto']) : '') . 
                       "%' AND Gen_Producto.CodigoBarra LIKE '" . (isset($filter['codigo']) ? addslashes($filter['codigo']) : '') . 
                       "%' ANd Gen_ProductoMarca.ProductoMarca LIKE '" . (isset($filter['marca']) ? addslashes($filter['marca']) : '') . 
                       "%' ANd Gen_Producto.ProductoModelo LIKE '" . (isset($filter['modelo']) ? addslashes($filter['modelo']) : '') . 
                       "%' AND Gen_ProductoCategoria.ProductoCategoria LIKE '" . (isset($filter['categoria']) ? addslashes($filter['categoria']) : '') . 
                       "%' "; 
            
        } else {
            $select .= " WHERE Gen_Producto.Producto LIKE '%" . $filter . 
                       "%' OR Gen_Producto.CodigoBarra LIKE '%" . $filter . 
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
        $select .= " WHERE Gen_Producto.Anulado=0 AND (Gen_Producto.Producto LIKE '%" . $request->getParam('q') . "%' OR Gen_Producto.ProductoModelo LIKE '" . $request->getParam('q') . "%')";
    }

    if($request->getParam('controlaStock')) {
        $select .= " AND Gen_Producto.ControlaStock=1";
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



/* HOTEL */
$app->get('/habitaciones', function (Request $request, Response $response, array $args) {
    $select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca,
        Gen_ProductoMedicion.ProductoMedicion, Ve_DocVentaCliente.DniRuc, Ve_DocVentaCliente.Cliente, Ve_DocVentaCliente.IdCliente
        FROM Gen_Producto 
        INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
        INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
        INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
        LEFT JOIN Ve_DocVentaCliente ON Gen_Producto.IdClienteReserva = Ve_DocVentaCliente.IdCliente ";
    
        $select .= " WHERE Gen_Producto.Anulado=0 AND Gen_Producto.EsHabitacion=1";

    if ($request->getParam('sortBy')) {
        $sortBy = $request->getParam('sortBy');
        $sortDesc = $request->getParam('sortDesc');
        $orientation = $sortDesc ? 'DESC' : 'ASC';
        $select .= " ORDER BY " . $sortBy . " " . $orientation;
    }

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

$app->post('/habitaciones/alquilar', function (Request $request, Response $response) {
    $idProducto = $request->getParam('IdProducto');
    $idCliente = $request->getParam('IdCliente');

    $cliente = $request->getParam('cliente');
    $lugarProcedencia = $request->getParam('cliente')['LugarProcedencia'];
    $medioTransporte = $request->getParam('cliente')['MedioTransporte'];
    $proximoDestino = $request->getParam('cliente')['ProximoDestino'];

    $fechaAlquilerInicio = $request->getParam('cliente')['FechaAlquilerInicio'];
    $fechaAlquilerFin = $request->getParam('cliente')['FechaAlquilerFin'];

    // Crear la preorden
    $insert = $this->db->insert(array('IdCliente', 'FechaReg', 'LugarProcedencia', 'MedioTransporte', 'ProximoDestino', 'FechaAlquilerInicio', 'FechaAlquilerFin'))
                       ->into('Ve_PreOrden')
                       ->values(array($idCliente, getNow(), $lugarProcedencia, $medioTransporte, $proximoDestino, $fechaAlquilerInicio, $fechaAlquilerFin));
    
    $insertId = $insert->execute();

    // Insertar preorden Det
    $insertDet = $this->db->insert(array('IdPreOrden', 'IdProducto', 'Cantidad'))
                       ->into('Ve_PreOrdenDet')
                       ->values(array($insertId, $idProducto, 1));
    $insertDetId = $insertDet->execute();

    // Actualizar habitacion, cambio de estado
    $update = "UPDATE Gen_Producto SET EstadoHabitacion=3, IdPreOrden=$insertId, IdClienteReserva=$idCliente, FechaAlquilerFin='" . $fechaAlquilerFin . "', FechaAlquiler='" . $fechaAlquilerInicio . "' WHERE IdProducto=$idProducto";
    $stmt = $this->db->prepare($update);
    $updated = $stmt->execute();
    
    $data = array(
        "insertId" => $insertId
    );

    return $response->withJson($data);
});

$app->post('/habitaciones/liberar', function (Request $request, Response $response) {
    $idProducto = $request->getParam('IdProducto');

    // Actualizar habitacion, cambio de estado
    $update = "UPDATE Gen_Producto SET EstadoHabitacion=1, EstaSucio=0, IdPreOrden=NULL, IdClienteReserva=NULL, FechaReserva=NULL, FechaAlquiler=NULL WHERE IdProducto=$idProducto";
    $stmt = $this->db->prepare($update);
    $updated = $stmt->execute();
    
    return $response->withJson(array(
        "updated" => $updated,
        "IdProducto" => $idProducto
    ));
});

$app->post('/habitaciones/reservar', function (Request $request, Response $response) {
    $idProducto = $request->getParam('IdProducto');
    $idCliente = $request->getParam('IdCliente');
    $fechaReserva = $request->getParam('FechaReserva') ? $request->getParam('FechaReserva') : getNow();
    $vuelo = $request->getParam('Vuelo') ? $request->getParam('Vuelo') : '';

    // Actualizar habitacion, cambio de estado
    $update = "UPDATE Gen_Producto SET EstadoHabitacion=2, Vuelo='$vuelo', IdPreOrden=NULL, IdClienteReserva=$idCliente, FechaReserva='" . $fechaReserva . "' WHERE IdProducto=$idProducto";
    $stmt = $this->db->prepare($update);
    $updated = $stmt->execute();
    
    return $response->withJson(array(
        "updated" => $updated,
        "IdProducto" => $idProducto,
        "quer" => $update
    ));
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


$app->post('/productos/delete', function (Request $request, Response $response) {
    $id = $request->getParam('id');

    if ($id) {
        // Solo anulamos por ahora, mas adelante hacer la comprobacion si el producto existe en las relaciones antes de eliminar
        // definitivamente.
        // $sql = "DELETE FROM Gen_Producto WHERE IdProducto='$id'";
        $update = "UPDATE Gen_Producto SET Anulado=1 WHERE IdProducto=$id";
        $stmt = $this->db->prepare($update);
        $updated = $stmt->execute();

    
        return $response->withJson(array(
            "updated" => $updated,
            "IdProducto" => $id
        ));
    }
});


$app->post('/productos', function (Request $request, Response $response) {
    $idProductoMarca = $request->getParam('marca')['IdProductoMarca'];
    $idProductoMedicion = $request->getParam('medicion')['IdProductoMedicion'];
    $idProductoCategoria = $request->getParam('categoria')['IdProductoCategoria'];
    // $idProductoModelo = $request->getParam('modelo')['IdProductoModelo'];
    // $idProductoTalla = $request->getParam('talla')['IdProductoTalla'];
    $idProductoFormaFarmaceutica = 1;
    $producto = $request->getParam('Producto');
    $productoModelo = $request->getParam('ProductoModelo') ? $request->getParam('ProductoModelo') : '';
    $fechaReg = getNow();
    $hash = time();
    // $controlaStock = 1;
    $porcentajeUtilidad = $request->getParam('PorcentajeUtilidad');
    $genero = $request->getParam('Genero');
    $color = $request->getParam('Color');
    $botapie = $request->getParam('Botapie');
    $anulado = $request->getParam('Anulado');
    $categoria = $request->getParam('categoria')['ProductoCategoria'];
    $productoPresentacion = $request->getParam('ProductoPresentacion');
    $esPadre = $request->getParam('EsPadre');
    $stockMinimo = $request->getParam('StockMinimo');
    $codigoBarra = $request->getParam('CodigoBarra');
    $esHabitacion = $request->getParam('EsHabitacion');
    $piso = $request->getParam('Piso');
    $controlaStock = $request->getParam('ControlaStock') ? $request->getParam('ControlaStock') : 0;

    $productosDet = $request->getParam('productosDet');
    $precioCosto = $request->getParam('PrecioCosto') ? $request->getParam('PrecioCosto') : 0;
    $precioContado = $request->getParam('PrecioContado') ? $request->getParam('PrecioContado') : 0;

    // Actualizamos el producto si le pasamos el ID
    if ($request->getParam('IdProducto')) {
        // aqui se actualiza el producto si existe
        $idProducto = $request->getParam('IdProducto');
        // $codigoBarra = $request->getParam('CodigoBarra');
        


        $update = $this->db->update(array(
                            "CodigoBarra" => $codigoBarra,
                            "Producto" => $producto,
                            "IdProductoMarca" => $idProductoMarca,
                            "IdProductoFormaFarmaceutica" => $idProductoFormaFarmaceutica,
                            "IdProductoMedicion" => $idProductoMedicion,
                            "IdProductoCategoria" => $idProductoCategoria,
                            // "IdProductoModelo" => $idProductoModelo,
                            // "IdProductoTalla" => $idProductoTalla,
                            "ControlaStock" => $controlaStock,
                            "PorcentajeUtilidad" => $porcentajeUtilidad,
                            "Genero" => $genero, "Color" => $color, "Botapie" => $botapie, "Anulado" => $anulado,
                            "ProductoModelo" => $productoModelo,
                            "PrecioCosto" => $precioCosto,
                            "PrecioContado" => $precioContado,
                            "ProductoPresentacion" => $productoPresentacion,
                            "EsPadre" => $esPadre,
                            "StockMinimo" => $stockMinimo,
                            "EsHabitacion" => $esHabitacion,
                            "Piso" => $piso
                        ))
                       ->table('Gen_Producto')
                       ->where('IdProducto', '=', $idProducto);
        $affectedRows = $update->execute();
        // Actualizar ProductoDet
        $sql = "DELETE FROM Gen_ProductoDet WHERE IdProducto='$idProducto'";
        $stmt = $this->db->prepare($sql);
        $deleted = $stmt->execute();
        
        if ($productosDet && $esPadre) {
            foreach($productosDet as $prod) {
                $insertDet = $this->db->insert(array('IdProducto', 'IdProductoDet', 'Cantidad'))
                                    ->into('Gen_ProductoDet')
                                    ->values(array($idProducto, $prod['IdProductoDet'], $prod['Cantidad']));
                $insertDetId = $insertDet->execute();
            }
        }
        return $response->withJson(array("affectedRows" => $productosDet));
    }
    // Fin actualizacion producto

    // Inicio verificar Producto
    $select = "SELECT * FROM Gen_Producto
        WHERE Producto = '" . $producto
        . "' OR CodigoBarra = '" . $codigoBarra . "'";

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
    $insert = $this->db->insert(array('IdProductoMarca', 'IdProductoFormaFarmaceutica', 'IdProductoMedicion', 'IdProductoCategoria', 'Producto', 'FechaReg', 'Hash', 'ControlaStock', 'PorcentajeUtilidad', 'Genero', 'Color', 'Botapie', 'Anulado', 'ProductoModelo', 'ProductoPresentacion', 'EsPadre', 'StockMinimo', 'CodigoBarra', 'PrecioCosto', 'PrecioContado', 'EsHabitacion', 'Piso'))
                       ->into('Gen_Producto')
                       ->values(array($idProductoMarca, $idProductoFormaFarmaceutica, $idProductoMedicion, $idProductoCategoria, $producto, $fechaReg, $hash, $controlaStock, $porcentajeUtilidad, $genero, $color, $botapie, $anulado, $productoModelo, $productoPresentacion, $esPadre, $stockMinimo, $codigoBarra, $precioCosto, $precioContado, $esHabitacion, $piso));
    $insertId = $insert->execute();
    
    // Generando codigo de barras  // actualizar el nombre para que sea unico
    /* $codigoBarra = substr($categoria, 0, 2) . $insertId . substr($producto, 0, 2);
    
    $update = $this->db->update(array("CodigoBarra" => $codigoBarra, "Producto" => $producto . '-' . $codigoBarra))
                       ->table('Gen_Producto')
                       ->where('IdProducto', '=', $insertId);
    $affectedRows = $update->execute(); */

    // Insertar productoDet
    if ($productosDet) {
        foreach($productosDet as $prod) {
            $insertDet = $this->db->insert(array('IdProducto', 'IdProductoDet', 'Cantidad'))
                                  ->into('Gen_ProductoDet')
                                  ->values(array($insertId, $prod['IdProducto'], $prod['Cantidad']));
            $insertDetId = $insertDet->execute();
        }
    }

    return $response->withJson(array("insertId" => $insertId));

});



$app->get('/movimiento/productos', function (Request $request, Response $response, array $args) {
    $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
    $hashMovimiento = $request->getParam('hash');
    
    $select = "SELECT Lo_MovimientoDetalle.IdProducto, Gen_Producto.Producto, Gen_ProductoMarca.ProductoMarca, Gen_Producto.ProductoModelo,
        Gen_Producto.Color, Gen_Producto.CodigoBarra, Gen_Producto.PrecioContado, Lo_MovimientoDetalle.Cantidad,
        Gen_Producto.Botapie 
        FROM Lo_MovimientoDetalle 
        INNER JOIN Gen_Producto ON Lo_MovimientoDetalle.IdProducto = Gen_Producto.IdProducto
        INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
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
    //$movimientoFecha = $request->getParam('movimiento')['MovimientoFecha'];
    $movimientoFecha = getNow();
    $almacenOrigen = $request->getParam('movimiento')['almacenOrigen']['IdAlmacen'];
    $almacenDestino = $request->getParam('movimiento')['almacenDestino']['IdAlmacen'];
    $anulado = 0;
    $fechaReg = getNow();
    $usuarioReg = 'jeam';
    $hash = time();
    //$fechaStock = $request->getParam('movimiento')['FechaStock'];
    $fechaStock = getNow();
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


$app->get('/almacenes/primero', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM Lo_Almacen WHERE Anulado=0 ORDER BY IdAlmacen ASC LIMIT 1 ";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();

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
    
    if (!$idProducto) {
        $select = "(SELECT Lo_MovimientoDetalle.IdProducto, IFNULL(SUM(Lo_MovimientoDetalle.Cantidad), 0) AS cantidad FROM Lo_Movimiento
            INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
            INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
            WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenDestino = $idAlmacen
                AND Lo_Movimiento.Anulado=0 
                AND Lo_Movimiento.MovimientoFecha < '$fechaHasta'
            GROUP BY Lo_MovimientoDetalle.IdProducto)";
    }

    return $select;
}
function stringIngresoCaja($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT IFNULL(SUM(Lo_MovimientoDetalle.Cantidad), 0) AS cantidad FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        INNER JOIN Gen_ProductoDet ON Lo_MovimientoDetalle.IdProducto = Gen_ProductoDet.IdProducto
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenDestino = $idAlmacen
            AND Gen_ProductoDet.IdProductoDet=$idProducto AND Lo_Movimiento.Anulado=0 
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";
    
    if (!$idProducto) {
        $select = "(SELECT Gen_ProductoDet.IdProductoDet AS IdProducto, IFNULL(SUM(Lo_MovimientoDetalle.Cantidad * Gen_ProductoDet.Cantidad), 0) AS cantidad 
            FROM Lo_Movimiento
            INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
            INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
            INNER JOIN Gen_ProductoDet ON Lo_MovimientoDetalle.IdProducto = Gen_ProductoDet.IdProducto
            WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenDestino = $idAlmacen
                AND Lo_Movimiento.Anulado=0 
                AND Lo_Movimiento.MovimientoFecha < '$fechaHasta'
            GROUP BY Gen_ProductoDet.IdProductoDet)";
    }
    // print_r($select);exit();
    return $select;
}

function stringSalidaCaja($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT IFNULL(SUM(Lo_MovimientoDetalle.Cantidad), 0) AS cantidad FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        INNER JOIN Gen_ProductoDet ON Lo_MovimientoDetalle.IdProducto = Gen_ProductoDet.IdProducto
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenOrigen = $idAlmacen
            AND Gen_ProductoDet.IdProductoDet=$idProducto AND Lo_Movimiento.Anulado=0 
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";
    
    if (!$idProducto) {
        $select = "(SELECT Gen_ProductoDet.IdProductoDet AS IdProducto, IFNULL(SUM(Lo_MovimientoDetalle.Cantidad * Gen_ProductoDet.Cantidad), 0) AS cantidad 
            FROM Lo_Movimiento
            INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
            INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
            INNER JOIN Gen_ProductoDet ON Lo_MovimientoDetalle.IdProducto = Gen_ProductoDet.IdProducto
            WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenOrigen = $idAlmacen
                AND Lo_Movimiento.Anulado=0 
                AND Lo_Movimiento.MovimientoFecha < '$fechaHasta'
            GROUP BY Gen_ProductoDet.IdProductoDet)";
    }
    // print_r($select);exit();
    return $select;
}

function stringSalidaUnd($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT IFNULL(SUM(Lo_MovimientoDetalle.Cantidad), 0) AS cantidad FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenOrigen = $idAlmacen
            AND Lo_MovimientoDetalle.IdProducto=$idProducto AND Lo_Movimiento.Anulado=0 
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";
    
    if (!$idProducto) {
        $select = "(SELECT Lo_MovimientoDetalle.IdProducto, IFNULL(SUM(Lo_MovimientoDetalle.Cantidad), 0) AS cantidad FROM Lo_Movimiento
            INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
            INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
            WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenOrigen = $idAlmacen
                AND Lo_Movimiento.Anulado=0 
                AND Lo_Movimiento.MovimientoFecha < '$fechaHasta'
            GROUP BY Lo_MovimientoDetalle.IdProducto)";
    }

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

    if (!$idProducto) {
        $select = "(SELECT Ve_DocVentaDet.IdProducto, IFNULL(SUM(Ve_DocVentaDet.Cantidad), 0) AS cantidad FROM Ve_DocVenta
            INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
            INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
            WHERE Ve_DocVenta.IdAlmacen = $idAlmacen
                AND Ve_DocVenta.Anulado = 0
                AND Ve_DocVentaTipoDoc.VaRegVenta = 1
                AND Ve_DocVenta.FechaDoc < '$fechaHasta'
            GROUP BY Ve_DocVentaDet.IdProducto)";
    }
    // print_r($select);exit();
    return $select;
}

function stringSalidaVentaCaja($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT IFNULL(SUM(Gen_ProductoDet.cantidad * Ve_DocVentaDet.Cantidad), 0) AS cantidad FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
        INNER JOIN Gen_ProductoDet ON Ve_DocVentaDet.IdProducto=Gen_ProductoDet.IdProducto
        WHERE Ve_DocVenta.IdAlmacen = $idAlmacen
            AND Gen_ProductoDet.IdProductoDet = $idProducto
            AND Ve_DocVenta.Anulado = 0
            AND Ve_DocVentaTipoDoc.VaRegVenta = 1
            AND Ve_DocVenta.FechaDoc < '$fechaHasta')";

    if (!$idProducto) {
        $select = "(SELECT Gen_ProductoDet.IdProductoDet AS IdProducto, IFNULL(SUM(Gen_ProductoDet.cantidad * Ve_DocVentaDet.Cantidad), 0) AS cantidad 
            FROM Ve_DocVenta
            INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
            INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
            INNER JOIN Gen_ProductoDet ON Ve_DocVentaDet.IdProducto=Gen_ProductoDet.IdProducto
            WHERE Ve_DocVenta.IdAlmacen = $idAlmacen
                AND Ve_DocVenta.Anulado = 0
                AND Ve_DocVentaTipoDoc.VaRegVenta = 1
                AND Ve_DocVenta.FechaDoc < '$fechaHasta'
            GROUP BY Gen_ProductoDet.IdProductoDet)";
    }
    //print_r($select);exit();
    return $select;
}

/*

SELECT Lo_MovimientoDetalle.IdProducto, Gen_ProductoDet.IdProductoDet, (Lo_MovimientoDetalle.Cantidad * Gen_ProductoDet.Cantidad) AS cantidad
/*IFNULL(SUM(Lo_MovimientoDetalle.Cantidad), 0) AS cantidad */
/*
FROM Lo_Movimiento 
INNER JOIN Lo_MovimientoDetalle ON Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento 
INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo 
INNER JOIN Gen_ProductoDet ON Lo_MovimientoDetalle.IdProducto = Gen_ProductoDet.IdProducto

WHERE Lo_MovimientoTipo.VaRegCompra = 1 
	AND Lo_Movimiento.IdAlmacenDestino = 1 
	AND Lo_Movimiento.Anulado=0 
	AND Lo_Movimiento.MovimientoFecha < '2018-07-23 17:25:36' 

GROUP BY Gen_ProductoDet.IdProductoDet

*/

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

    // $strIngresoUnd = stringIngresoUnd('Gen_Producto.IdProducto', $idAlmacen, $fechaHasta);
    // $strSalidaUnd = stringSalidaUnd('Gen_Producto.IdProducto', $idAlmacen, $fechaHasta);
    // $strSalidaVentaUnd = stringSalidaVentaUnd('Gen_Producto.IdProducto', $idAlmacen, $fechaHasta);
    
    $strIngresoUnd = stringIngresoUnd(false, $idAlmacen, $fechaHasta);
    $strSalidaUnd = stringSalidaUnd(false, $idAlmacen, $fechaHasta);

    $strIngresoCaja = stringIngresoCaja(false, $idAlmacen, $fechaHasta);
    $strSalidaCaja = stringSalidaCaja(false, $idAlmacen, $fechaHasta);

    $strSalidaVentaUnd = stringSalidaVentaUnd(false, $idAlmacen, $fechaHasta);
    $strSalidaVentaCaja = stringSalidaVentaCaja(false, $idAlmacen, $fechaHasta);
    
    /*$select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca,
            Gen_ProductoTalla.ProductoTalla, Gen_ProductoMedicion.ProductoMedicion,
            
            $strIngresoUnd AS StockIngresoUnd, 
            $strSalidaUnd AS StockSalidaUnd,
            $strSalidaVentaUnd AS StockSalidaVentaUnd,  
            ((SELECT StockIngresoUnd) - (SELECT StockSalidaUnd) - (SELECT StockSalidaVentaUnd)) AS stock 


            FROM Gen_Producto 
            INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
            INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
            INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
            LEFT JOIN Gen_ProductoTalla ON Gen_Producto.IdProductoTalla = Gen_ProductoTalla.IdProductoTalla ";*/
    
    if ($request->getParam('sumaStock')) {
        $select = "SELECT
            SUM(IFNULL(IngresoUnd.cantidad, 0) + IFNULL(IngresoCaja.cantidad, 0)  - IFNULL(SalidaCaja.cantidad, 0) - IFNULL(SalidaUnd.cantidad, 0) - IFNULL(SalidaVentaUnd.cantidad,0) - IFNULL(SalidaVentaCaja.cantidad,0)) AS sumaStock

            FROM Gen_Producto 
            INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
            INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
            INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
            LEFT JOIN $strIngresoUnd AS IngresoUnd ON Gen_Producto.IdProducto = IngresoUnd.IdProducto
            LEFT JOIN $strSalidaUnd AS SalidaUnd ON Gen_Producto.IdProducto = SalidaUnd.IdProducto
            LEFT JOIN $strIngresoCaja AS IngresoCaja ON Gen_Producto.IdProducto = IngresoCaja.IdProducto
            LEFT JOIN $strSalidaCaja AS SalidaCaja ON Gen_Producto.IdProducto = SalidaCaja.IdProducto
            LEFT JOIN $strSalidaVentaUnd AS SalidaVentaUnd ON Gen_Producto.IdProducto = SalidaVentaUnd.IdProducto 
            LEFT JOIN $strSalidaVentaCaja AS SalidaVentaCaja ON Gen_Producto.IdProducto = SalidaVentaCaja.IdProducto";
    } else if ($request->getParam('sumaValorizado')) {
        $select = "SELECT
            SUM(Gen_Producto.PrecioContado * (IFNULL(IngresoUnd.cantidad, 0) + IFNULL(IngresoCaja.cantidad, 0)  - IFNULL(SalidaCaja.cantidad, 0) - IFNULL(SalidaUnd.cantidad, 0) - IFNULL(SalidaVentaUnd.cantidad,0) - IFNULL(SalidaVentaCaja.cantidad,0))) AS sumaValorizado

            FROM Gen_Producto 
            INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
            INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
            INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
            LEFT JOIN $strIngresoUnd AS IngresoUnd ON Gen_Producto.IdProducto = IngresoUnd.IdProducto
            LEFT JOIN $strSalidaUnd AS SalidaUnd ON Gen_Producto.IdProducto = SalidaUnd.IdProducto
            LEFT JOIN $strIngresoCaja AS IngresoCaja ON Gen_Producto.IdProducto = IngresoCaja.IdProducto
            LEFT JOIN $strSalidaCaja AS SalidaCaja ON Gen_Producto.IdProducto = SalidaCaja.IdProducto
            LEFT JOIN $strSalidaVentaUnd AS SalidaVentaUnd ON Gen_Producto.IdProducto = SalidaVentaUnd.IdProducto 
            LEFT JOIN $strSalidaVentaCaja AS SalidaVentaCaja ON Gen_Producto.IdProducto = SalidaVentaCaja.IdProducto";
    } else {
        $select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca,
            Gen_ProductoMedicion.ProductoMedicion,
            IFNULL(IngresoUnd.cantidad, 0) AS StockIngresoUnd,
            IFNULL(SalidaUnd.cantidad, 0) AS StockSalidaUnd,
            IFNULL(SalidaVentaUnd.cantidad, 0) AS StockSalidaVentaUnd,
            IFNULL(SalidaVentaCaja.cantidad, 0) AS StockSalidaVentaCaja,

            (IFNULL(IngresoUnd.cantidad, 0) + IFNULL(IngresoCaja.cantidad, 0)  - IFNULL(SalidaCaja.cantidad, 0) - IFNULL(SalidaUnd.cantidad, 0) - IFNULL(SalidaVentaUnd.cantidad,0) - IFNULL(SalidaVentaCaja.cantidad,0)) AS stock

            FROM Gen_Producto 
            INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
            INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
            INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
            LEFT JOIN $strIngresoUnd AS IngresoUnd ON Gen_Producto.IdProducto = IngresoUnd.IdProducto
            LEFT JOIN $strSalidaUnd AS SalidaUnd ON Gen_Producto.IdProducto = SalidaUnd.IdProducto
            LEFT JOIN $strIngresoCaja AS IngresoCaja ON Gen_Producto.IdProducto = IngresoCaja.IdProducto
            LEFT JOIN $strSalidaCaja AS SalidaCaja ON Gen_Producto.IdProducto = SalidaCaja.IdProducto
            LEFT JOIN $strSalidaVentaUnd AS SalidaVentaUnd ON Gen_Producto.IdProducto = SalidaVentaUnd.IdProducto 
            LEFT JOIN $strSalidaVentaCaja AS SalidaVentaCaja ON Gen_Producto.IdProducto = SalidaVentaCaja.IdProducto";
    }
            


    if ($request->getParam('filter')) {
        $filter = $request->getParam('filter');
        if(is_array($filter)) {
            $select .= " WHERE Gen_Producto.EsPadre = 0 AND ( Gen_Producto.Producto LIKE '%" . (isset($filter['producto']) ? addslashes($filter['producto']) : '') . 
                       "%' AND Gen_Producto.CodigoBarra LIKE '" . (isset($filter['codigo']) ? addslashes($filter['codigo']) : '') . 
                       "%' ANd Gen_ProductoMarca.ProductoMarca LIKE '" . (isset($filter['marca']) ? addslashes($filter['marca']) : '') . 
                       "%' ANd Gen_Producto.ProductoModelo LIKE '" . (isset($filter['modelo']) ? addslashes($filter['modelo']) : '') . 
                       "%' AND Gen_ProductoCategoria.ProductoCategoria LIKE '" . (isset($filter['categoria']) ? addslashes($filter['categoria']) : '') . 
                       "%' ) "; 
            
        } else {
            $select .= " WHERE Gen_Producto.EsPadre = 0 AND ( Gen_Producto.Producto LIKE '%" . $filter . 
                       "%' OR Gen_Producto.CodigoBarra LIKE '%" . $filter . 
                       "%' OR Gen_ProductoMarca.ProductoMarca LIKE '%" . $filter . 
                       "%' OR Gen_Producto.ProductoModelo LIKE '%" . $filter . 
                       "%' OR Gen_ProductoCategoria.ProductoCategoria LIKE '%" . $filter . 
                       "%' ) ";        
        }
    } else {
        $select .= " WHERE Gen_Producto.EsPadre = 0 AND Gen_Producto.Producto LIKE '%" . $request->getParam('q') . "%' ";
    }

    if ($request->getParam('idProductos')) {
        // mandar array de IDS 
        $idProductos = $request->getParam('idProductos');
        $select .= " AND Gen_Producto.IdProducto IN (" . implode(',', $idProductos) . ")";
    }

    $select .= "AND Gen_Producto.ControlaStock=1 ";

    if(isset($request->getparam('filter')['minimo']) && !$request->getParam('sumaStock') && !$request->getParam('sumaValorizado')) {
        $filterMinimo = $request->getParam('filter')['minimo'];
        if ($filterMinimo) {
            $select .= " HAVING stock <= Gen_Producto.StockMinimo";
        }
    } else {
        if (isset($request->getParam('filter')['stock']) && !$request->getParam('sumaStock') && !$request->getParam('sumaValorizado')) {
            $filterStock = $request->getParam('filter')['stock'];
            if ($filterStock == 'mayor') {
                $select .= " HAVING stock > 0";
            }
            if ($filterStock == 'menor') {
                $select .= " HAVING stock <= 0";
            }
        }
    }



    if ($request->getParam('sortBy')) {
        $sortBy = $request->getParam('sortBy');
        $sortDesc = $request->getParam('sortDesc');
        $orientation = $sortDesc ? 'DESC' : 'ASC';
        $select .= " ORDER BY " . $sortBy . " " . $orientation;
    }
    

    if ($request->getParam('limit') && !$request->getParam('count')) {
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

    if($request->getParam('count')) {
        $data = array('total' => $stmt->rowCount());
    }
    
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
    //$select = $this->db->select()->from('Ve_DocVentaTipoDoc')->whereLike('TipoDoc', '%' . $request->getParam('q') . '%');


    $select = "SELECT * FROM Ve_DocVentaTipoDoc WHERE TipoDoc LIKE '%" . $request->getParam('q') . "%' ORDER BY Orden ASC";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});

$app->get('/ventas/tipos/electronicos', function (Request $request, Response $response, array $args) {
    //$select = $this->db->select()->from('Ve_DocVentaTipoDoc')->whereLike('TipoDoc', '%' . $request->getParam('q') . '%');


    $select = "SELECT * FROM Ve_DocVentaTipoDoc WHERE TipoDoc LIKE '%" . $request->getParam('q') . "%' AND EsElectronico=1 ORDER BY Orden ASC";

    $stmt = $this->db->query($select);
    $stmt->execute();
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
    $idTipoDoc = $request->getParam('idTipoDoc');
    // $fechaInicio = $fechaInicio ? $fechaInicio : getNow('Y') . '-01-01';
    $fechaInicio = $fechaInicio ? $fechaInicio : getNow('Y-m-d');
    $fechaFin = $fechaFin ? $fechaFin : getNow('Y-m-d');

    $select = "SELECT ROUND(SUM((Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio) - Ve_DocVentaDet.Descuento), 2) AS total 
        FROM Ve_DocVentaDet
        INNER JOIN Ve_DocVenta ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta
        WHERE Ve_DocVenta.Anulado=0 AND Ve_DocVenta.FechaDoc BETWEEN CAST('" . $fechaInicio . "' AS DATETIME) AND CONCAT('" . $fechaFin . "',' 23:59:59')";
    //print_r($select);exit();

    if($idTipoDoc) {
        $select .= " AND Ve_DocVenta.IdTipoDoc = $idTipoDoc";
    }

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
    $idTipoDoc = $request->getParam('idTipoDoc');

    $select = "SELECT ROUND(SUM((Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio) - Ve_DocVentaDet.Descuento), 2) AS total FROM Ve_DocVentaDet
    INNER JOIN Ve_DocVenta ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta
    INNER JOIN Seg_Usuario ON Ve_DocVenta.UsuarioReg = Seg_Usuario.Usuario
    WHERE Ve_DocVenta.Anulado=0 AND Seg_Usuario.Usuario = '$usuario' 
    AND Ve_DocVenta.FechaDoc BETWEEN CAST('" . $fechaInicio . "' AS DATETIME) AND CONCAT('" . $fechaFin . "',' 23:59:59')";
    //print_r($select);exit();
    
    if($idTipoDoc) {
        $select .= " AND Ve_DocVenta.IdTipoDoc = $idTipoDoc";
    }

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});


$app->get('/ventas/detalle', function (Request $request, Response $response) {
    $idDocVentas = $request->getParam('idDocVentas');

    $select = "SELECT Ve_DocVentaDet.*, Gen_Producto.Producto, 
        ROUND((Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio) - Ve_DocVentaDet.Descuento, 2) AS Subtotal,
        ROUND(Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio, 2) AS Total,
        Gen_Producto.CodigoBarra, Gen_ProductoMedicion.Codigo AS CodigoMedicion
        FROM Ve_DocVentaDet 
        INNER JOIN Gen_Producto ON Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto
        INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
        WHERE IdDocVenta IN (" . implode(',', $idDocVentas) . ")";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();
    
    return $response->withJson($data);

});

$app->get('/ventas', function (Request $request, Response $response) { 
    $filtros = '';

    $select = "SELECT Ve_DocVenta.idDocVenta, Ve_DocVenta.FechaDoc, Ve_DocVentaTipoDoc.TipoDoc, Ve_DocVentaTipoDoc.TieneIgv, 
        Ve_DocVenta.Anulado, Ve_DocVenta.Serie, Ve_DocVenta.Numero, Ve_DocVentaCliente.Cliente, Ve_DocVenta.UsuarioReg,
        Ve_DocVentaTipoDoc.CodSunat, Ve_DocVentaCliente.DniRuc, Ve_DocVentaCliente.DniRuc, Ve_DocVentaCliente.Direccion,
        Ve_DocVentaTipoDoc.CodigoIgv, Ve_DocVenta.Estado, Ve_DocVenta.Hash_cpe, Ve_DocVenta.Hash_cdr, Ve_DocVenta.Msj_sunat,
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

    $idTipoDoc = $request->getParam('idTipoDoc');
    if($idTipoDoc) {
        $filtros .= " AND Ve_DocVenta.IdTipoDoc IN (" . implode(',', $idTipoDoc) . ")";
    }

    $estado = $request->getParam('estado');
    // var_dump($estado);exit();
    if($estado || $estado == "0") {
        $filtros .= " AND Ve_DocVenta.Estado=$estado";
    }

    $anulado = $request->getParam('anulado');
    if(!is_null($anulado) && $anulado != '' && ($anulado == 0 || $anulado == 1)) {
        $filtros .= " AND Ve_DocVenta.Anulado='$anulado'";
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
    $codSunat = $request->getParam('tipoVenta')['CodSunat'];
    $idAlmacen = $request->getParam('almacen')['IdAlmacen']; 
    // $serie = $request->getParam('Serie'); 
    // $numero = $request->getParam('Numero');
    $anulado = 0;
    $usuarioReg = isset($request->getParam('vendedor')['Usuario']) ? $request->getParam('vendedor')['Usuario'] : $vendedor;
    $pagoCon = $request->getParam('PagoCon');
    
    $esCredito = $request->getParam('EsCredito');
    $fechaCredito = $request->getParam('FechaCredito');
    $idPreOrden = $request->getParam('IdPreOrden');
    $campoDireccion = $request->getParam('CampoDireccion');
    
    // OBTENER SERIE
    $selectSerie = "SELECT Serie FROM Ve_DocVentaPuntoVentaDet WHERE IdDocVentaPuntoVenta=$idDocVentaPuntoVenta AND IdDocVentaTipoDoc=$idTipoDoc";
    
    $stmt = $this->db->query($selectSerie);
    $stmt->execute();
    $selectSerie = $stmt->fetch();
    $serie = $selectSerie['Serie'] ? $selectSerie['Serie'] : $request->getParam('Serie');
    
    /*if(is_numeric($idPreOrden)) {
        $selectPre = "SELECT * FROM Ve_PreOrden WHERE IdPreOrden = $idPreOrden";

        $stmt = $this->db->query($selectPre);
        $stmt->execute();
        $preOrden = $stmt->fetch(PDO::FETCH_ASSOC);  

        $insert = "INSERT INTO Ve_DocVenta (IdDocVentaPuntoVenta,IdCliente,IdTipoDoc,IdAlmacen,Serie,Numero,FechaDoc,Anulado,FechaReg,UsuarioReg,Hash, EsCredito, FechaCredito, PagoCon, IdPreOrden, LugarProcedencia, MedioTransporte, ProximoDestino)
        VALUES ($idDocVentaPuntoVenta, $idCliente, $idTipoDoc, $idAlmacen, '$serie', '$numero', '" . getNow() . "', $anulado, '" . getNow() . "', '$usuarioReg', UNIX_TIMESTAMP(), $esCredito, '$fechaCredito', '$pagoCon', $idPreOrden, '" . $preOrden['LugarProcedencia'] . "', '" . $preOrden['MedioTransporte'] . "', '" . $preOrden['ProximoDestino'] . "')";
    } else if(is_array($idPreOrden)) {
        $idPreOrdens = [];
        foreach($idPreOrden as $pre) {
            $update = "UPDATE Ve_PreOrden SET IdDocVenta=Puntos+$puntosAdicionales WHERE IdCliente=$idCliente";
            $stmt = $this->db->prepare($update);
            $updated = $stmt->execute();

            $idPreOrdens[] = $pre['IdPreOrden'];
        }
        // print_r($idPreOrdens);exit();
        $insert = "INSERT INTO Ve_DocVenta (IdDocVentaPuntoVenta,IdCliente,IdTipoDoc,IdAlmacen,Serie,Numero,FechaDoc,Anulado,FechaReg,UsuarioReg,Hash, EsCredito, FechaCredito, PagoCon, IdPreOrden)
        VALUES ($idDocVentaPuntoVenta, $idCliente, $idTipoDoc, $idAlmacen, '$serie', '$numero', '" . getNow() . "', $anulado, '" . getNow() . "', '$usuarioReg', UNIX_TIMESTAMP(), $esCredito, '$fechaCredito', '$pagoCon', '" . serialize($idPreOrdens) . "')";
    } else {
        $insert = "INSERT INTO Ve_DocVenta (IdDocVentaPuntoVenta,IdCliente,IdTipoDoc,IdAlmacen,Serie,Numero,FechaDoc,Anulado,FechaReg,UsuarioReg,Hash, EsCredito, FechaCredito, PagoCon)
        VALUES ($idDocVentaPuntoVenta, $idCliente, $idTipoDoc, $idAlmacen, '$serie', '$numero', '" . getNow() . "', $anulado, '" . getNow() . "', '$usuarioReg', UNIX_TIMESTAMP(), $esCredito, '$fechaCredito', '$pagoCon')";
    }*/

    // OBTENER EL SIGUIENTE NUMERO
    if ($codSunat == '07' || $codSunat == '08') { // 07 nota de credito, 08 nota de debito
        $selectNumero = "SELECT Numero + 1 AS NuevoNumero FROM Ve_DocVenta 
            WHERE IdTipoDoc=$idTipoDoc AND Serie='$serie'
            ORDER BY Numero DESC LIMIT 1";
    } else {
        $selectNumero = "SELECT Numero + 1 AS NuevoNumero FROM Ve_DocVenta 
            WHERE IdDocVentaPuntoVenta=$idDocVentaPuntoVenta AND IdTipoDoc=$idTipoDoc 
            ORDER BY Numero DESC LIMIT 1";
    }
    

    $stmt = $this->db->query($selectNumero);
    $stmt->execute();
    $selectNumero = $stmt->fetch();
    $numero = $selectNumero['NuevoNumero'] ? $selectNumero['NuevoNumero'] : 1;


    $insert = "INSERT INTO Ve_DocVenta (IdDocVentaPuntoVenta,IdCliente,IdTipoDoc,IdAlmacen,Serie,Numero,FechaDoc,Anulado,FechaReg,UsuarioReg,Hash, EsCredito, FechaCredito, PagoCon, CampoDireccion)
        VALUES ($idDocVentaPuntoVenta, $idCliente, $idTipoDoc, $idAlmacen, '$serie', '$numero', '" . getNow() . "', $anulado, '" . getNow() . "', '$usuarioReg', UNIX_TIMESTAMP(), $esCredito, '$fechaCredito', '$pagoCon', '$campoDireccion')";

    $stmt = $this->db->prepare($insert);
    $inserted = $stmt->execute();
    $idDocVenta = $this->db->lastInsertId();
    // END INSERTAR NUEVA VENTA
    
    // Actualizar Todas las PreOrden
    if ($idPreOrden) {
        foreach($idPreOrden as $pre) {
            $update = "UPDATE Ve_PreOrden SET IdDocVenta=$idDocVenta WHERE IdPreOrden=" . $pre['IdPreOrden'];
            $stmt = $this->db->prepare($update);
            $updated = $stmt->execute();
            $idHabitacion = 0;

            foreach($request->getParam('productos') as $producto) {
                if (!$producto['EsHabitacion']) {
                    $update = "UPDATE Ve_PreOrdenDet SET Cantidad=Cantidad-" . $producto['cantidad'] . " WHERE IdPreOrden=" . $pre['IdPreOrden'] . " AND IdProducto=" . $producto['IdProducto'];
                    $stmt = $this->db->prepare($update);
                    $updated = $stmt->execute();


                    $select = "SELECT * FROM Ve_PreOrdenDet WHERE IdPreOrden=" . $pre['IdPreOrden'] . " AND IdProducto=" . $producto['IdProducto'];
                    $stmt = $this->db->query($select);
                    $stmt->execute();
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (isset($data['Cantidad']) && ($producto['cantidad'] > $data['Cantidad'])) {
                        $sql = "DELETE FROM Ve_PreOrdenDet WHERE IdPreOrden=" . $pre['IdPreOrden'] . " AND IdProducto=" . $producto['IdProducto'];
                        $stmt = $this->db->prepare($sql);
                        $deleted = $stmt->execute();
                    }
                } else {
                    $idHabitacion = $producto['IdProducto'];
                }

            }

            $select = "SELECT COUNT(*) as total FROM Ve_PreOrdenDet 
            INNER JOIN Gen_Producto ON Ve_PreOrdenDet.IdProducto = Gen_Producto.IdProducto 
            WHERE Gen_Producto.EsHabitacion = 0 AND  
                Ve_PreOrdenDet.IdPreOrden=" . $pre['IdPreOrden'] . " AND Ve_PreOrdenDet.Cantidad > 0";
            $stmt = $this->db->query($select);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data['total'] <= 0) {
                $update = "UPDATE Ve_PreOrden SET Anulado=1 WHERE IdPreOrden=" . $pre['IdPreOrden'];
                $stmt = $this->db->prepare($update);
                $updated = $stmt->execute();

                $update = "UPDATE Gen_Producto SET EstadoHabitacion=1, EstaSucio=1 WHERE IdProducto=" . $idHabitacion;
                $stmt = $this->db->prepare($update);
                $updated = $stmt->execute();
            }
        }
    }
    
    // START INSERTAR VENTA DETALLE
    $productos = $request->getParam('productos');
    $descuentoTotal = 0;
    foreach($productos as $producto) {
        // if ($producto['total'] > 0) {
            $idProducto = $producto['IdProducto'];
            $cantidad = $producto['cantidad'];
            $precio = $producto['precio'];
            $descuento = $producto['descuento'];
            $fechaAlquilerInicio = isset($producto['FechaAlquilerInicio']) ? $producto['FechaAlquilerInicio'] : (isset($producto['FechaAlquiler']) ? $producto['FechaAlquiler'] : null);
            $fechaAlquilerFin = isset($producto['FechaAlquilerFin']) ? $producto['FechaAlquilerFin'] : getNow();
            $descripcion = isset($producto['Descripcion']) ? $producto['Descripcion'] : null;
            
            
            $insert = $this->db->insert(array('IdDocVenta', 'IdProducto', 'Cantidad', 'Precio', 'Descuento', 'FechaAlquilerFin', 'Descripcion', 'FechaAlquilerInicio'))
                ->into('Ve_DocVentaDet')
                ->values(array($idDocVenta, $idProducto, $cantidad, $precio, $descuento, $fechaAlquilerFin, $descripcion, $fechaAlquilerInicio ));
            
            $insert->execute();
            $descuentoTotal += $descuento;
        // }
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

$app->post('/preorden/cajaybanco', function (Request $request, Response $response, array $args) {
    $idPreOrden = $request->getParam('IdPreOrden');
    $idHabitacion = $request->getParam('IdHabitacion');
    $adelanto = $request->getParam('Adelanto');
    $producto = $request->getParam('producto');
    $concepto = $request->getParam('concepto');

    if ($idPreOrden) {
        $insert = "INSERT INTO Cb_CajaBanco (IdTipoCajaBanco, IdCuenta, FechaDoc, Concepto, Importe, Anulado, IdProveedor, IdCliente, EsDelVendedor, IdPreOrden) VALUES (2, 1, '" . getNow() . "', '$concepto', $adelanto, 0, 0, $producto[IdCliente], 0, $idPreOrden)";
        $stmt = $this->db->prepare($insert);
        $inserted = $stmt->execute();
        $idCajaBanco = $this->db->lastInsertId();

        if ($idCajaBanco) {
            $select = "SELECT * FROM Cb_CajaBanco where IdCajaBanco=$idCajaBanco";

            $stmt = $this->db->query($select);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);    

            return $response->withJson($data);
        }
    }
});


$app->get('/preorden/cajaybanco', function (Request $request, Response $response, array $args) {
    $idPreOrden = $request->getParam('IdPreOrden');

    if ($idPreOrden) {
        $select = "SELECT * FROM Cb_CajaBanco where IdPreOrden=$idPreOrden";

        $stmt = $this->db->query($select);
        $stmt->execute();
        $data = $stmt->fetchAll();    

        return $response->withJson($data);
    }
});


$app->post('/preorden/detalle', function (Request $request, Response $response, array $args) {
    $idPreOrden = $request->getParam('IdPreOrden');
    $productos = $request->getParam('productos');
    $idHabitacion = $request->getParam('IdHabitacion');

    if ($idPreOrden) {
        // Actualizar ProductoDet
        $sql = "DELETE FROM Ve_PreOrdenDet WHERE IdPreOrden='$idPreOrden' AND IdProducto != $idHabitacion";
        $stmt = $this->db->prepare($sql);
        $deleted = $stmt->execute();

        foreach($productos as $prod) {
            if ($prod['IdProducto'] != $idHabitacion) {
                $insertDet = $this->db->insert(array('IdPreOrden', 'IdProducto', 'Cantidad'))
                                    ->into('Ve_PreOrdenDet')
                                    ->values(array($idPreOrden, $prod['IdProducto'], $prod['Cantidad']));
                $insertDetId = $insertDet->execute();
            }
        }
        return $response->withJson(array("affectedRows" => $productos));
    }
});

$app->get('/preorden/count', function (Request $request, Response $response, array $args) {
    $select = "SELECT COUNT(*) as total FROM Ve_PreOrden where Anulado=0";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});


$app->get('/preorden', function (Request $request, Response $response) {
    $filter = $request->getParam('filter');
    $select = "SELECT Ve_PreOrden.IdPreOrden, Ve_PreOrden.IdCliente, Ve_DocVentaCliente.Cliente, Ve_DocVentaCliente.DniRuc, Ve_PreOrden.FechaReg 
        FROM Ve_PreOrden INNER JOIN Ve_DocVentaCliente ON Ve_PreOrden.IdCliente = Ve_DocVentaCliente.IdCliente";
    $select .= " WHERE Ve_PreOrden.Anulado=0 AND (Ve_DocVentaCliente.Cliente LIKE '%". $filter ."%' 
                 OR Ve_DocVentaCliente.DniRuc LIKE '" . $filter . "%')";
    $select .= " ORDER BY Ve_PreOrden.IdPreOrden DESC";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
    
});

$app->get('/preorden/detalle', function (Request $request, Response $response) {
    $idPreOrden = $request->getParam('idPreOrden');

    $select = "SELECT Ve_PreOrdenDet.Cantidad, Gen_Producto.*, Ve_PreOrdenDet.IdPreOrden FROM Ve_PreOrdenDet  
        INNER JOIN Gen_Producto ON Ve_PreOrdenDet.IdProducto = Gen_Producto.IdProducto
        WHERE Ve_PreOrdenDet.IdPreOrden=$idPreOrden";
    
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
    
});

$app->get('/cliente', function (Request $request, Response $response, array $args) {
    $idCliente = $request->getParam('idCliente');
    $dni = $request->getParam('dni');
    
    if ($dni) {
        $select = "SELECT * FROM Ve_DocVentaCliente WHERE DniRuc='$dni'";
    } else {
        $select = "SELECT * FROM Ve_DocVentaCliente WHERE IdCliente='$idCliente'";
    } 
    
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();    

    return $response->withJson($data);
});

$app->get('/cliente/deudas', function (Request $request, Response $response, array $args) {
    $idCliente = $request->getParam('idCliente');
    if ($idCliente) {
    $select = "SELECT
        Ve_DocVenta.IdDocVenta,
        Ve_DocVenta.FechaDoc,
        Ve_DocVenta.FechaCredito,
        CONCAT(CASE WHEN Ve_DocVentaTipoDoc.CodSunat ='01' THEN
            'F'
        ELSE
            CASE WHEN Ve_DocVentaTipoDoc.CodSunat='03' THEN
                'B'
            ELSE
                CASE WHEN Ve_DocVentaTipoDoc.CodSunat='12' THEN
                    'T'
                ELSE
                    'OTRO'
                END
            END
        END,Ve_DocVenta.Serie,'-' , convert(Ve_DocVenta.Numero, char) )as Correlativo,
        Round(Sum((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2) as Total,
        Round((SELECT IfNull((SELECT Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='VE' And Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2) as Aplicado,
        Round(Sum((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2) -
        Round((SELECT IfNull((SELECT Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='VE' And Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2) as Saldo
        From Ve_DocVenta
        INNER JOIN Ve_DocVentaDet On Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc On Ve_DocVenta.IdTipoDoc=Ve_DocVentaTipoDoc.IdTipoDoc
        Where EsCredito=1 and Ve_DocVenta.IdCliente=$idCliente
        
        Group by
        Ve_DocVenta.IdDocVenta,
        Ve_DocVenta.FechaDoc,
        Ve_DocVenta.FechaCredito,
        Ve_DocVentaTipoDoc.CodSunat,
        Ve_DocVenta.Serie,
        Ve_DocVenta.Numero
        HAVING Round(Sum((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento),2) -
        Round((SELECT IfNull((SElect Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='VE' And Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2)>0
        Order by FechaDoc;";

    } else {
    $select = "SELECT
        Ve_DocVentaCliente.DniRuc,
        Ve_DocVentaCliente.Cliente,
        Ve_DocVenta.IdDocVenta,
        Ve_DocVenta.FechaDoc,
        Ve_DocVenta.FechaCredito,
        CONCAT(CASE WHEN Ve_DocVentaTipoDoc.CodSunat ='01' THEN
            'F'
        ELSE
            CASE WHEN Ve_DocVentaTipoDoc.CodSunat='03' THEN
                'B'
            ELSE
                CASE WHEN Ve_DocVentaTipoDoc.CodSunat='12' THEN
                    'T'
                ELSE
                    'OTRO'
                END
            END
        END,Ve_DocVenta.Serie,'-' , convert(Ve_DocVenta.Numero, char) )as Correlativo,
        Round(Sum((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2) as Total,
        Round((SELECT IfNull((SELECT Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='VE' And Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2) as Aplicado,
        Round(Sum((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2) -
        Round((SELECT IfNull((SELECT Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='VE' And Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2) as Saldo
        From Ve_DocVenta
        INNER JOIN Ve_DocVentaDet On Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc On Ve_DocVenta.IdTipoDoc=Ve_DocVentaTipoDoc.IdTipoDoc
        INNER JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente
        WHERE EsCredito=1";
        
            $select .= " AND (Ve_DocVentaCliente.Cliente LIKE '%" . $request->getParam('q') . "%'";
            $select .= " OR Ve_DocVentaCliente.DniRuc LIKE '%" . $request->getParam('q') . "%') ";

        $select .="
        Group by
        Ve_DocVenta.IdDocVenta,
        Ve_DocVenta.FechaDoc,
        Ve_DocVenta.FechaCredito,
        Ve_DocVentaTipoDoc.CodSunat,
        Ve_DocVenta.Serie,
        Ve_DocVenta.Numero
        HAVING Round(Sum((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento),2) -
        Round((SELECT IfNull((SElect Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='VE' And Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2)>0
        Order by FechaDoc;";
    }


    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});

$app->get('/clientes/deuda/count', function (Request $request, Response $response, array $args) {
    $select = "SELECT COUNT(*) as total
        FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet On Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc On Ve_DocVenta.IdTipoDoc=Ve_DocVentaTipoDoc.IdTipoDoc
        INNER JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente
        WHERE EsCredito=1
        GROUP BY
        Ve_DocVenta.IdDocVenta,
        Ve_DocVenta.FechaDoc,
        Ve_DocVenta.FechaCredito,
        Ve_DocVentaTipoDoc.CodSunat,
        Ve_DocVenta.Serie,
        Ve_DocVenta.Numero
        HAVING ROUND(SUM((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento),2) -
        ROUND((SELECT IFNULL((SELECT SUM(Cb_CajaBancoDet.Importe) FROM Cb_CajaBancoDet WHERE Tipo='VE' AND Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2)>0
        ORDER BY FechaDoc;";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});

$app->get('/proveedores/deuda/count', function (Request $request, Response $response, array $args) {
    $select = "SELECT COUNT(*) AS total
    From Lo_Movimiento
    INNER JOIN Lo_MovimientoDetalle ON Lo_Movimiento.Hash=Lo_MovimientoDetalle.hashMovimiento
    INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo=Lo_MovimientoTipo.IdMovimientoTipo
    INNER JOIN Lo_Proveedor ON Lo_Movimiento.IdProveedor = Lo_Proveedor.IdProveedor
    Where EsCredito=1
    Group by
    Lo_Movimiento.Hash,
    Lo_Movimiento.MovimientoFecha,
    Lo_Movimiento.FechaVenCredito,
    Lo_MovimientoTipo.CodSunat,
    Lo_Movimiento.Serie,
    Lo_Movimiento.Numero
    HAVING Round(Sum(Lo_MovimientoDetalle.Precio*Lo_MovimientoDetalle.Cantidad),2) -
    Round((SELECT IfNull((Select Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='MO' And Cb_CajaBancoDet.Hash=Lo_Movimiento.Hash),0)),2)>0
    Order by MovimientoFecha;";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});

$app->get('/proveedores/deudas', function (Request $request, Response $response, array $args) {
    $select = "SELECT
    Lo_Movimiento.Hash as Id,
    Lo_Proveedor.Ruc,
    Lo_Proveedor.Proveedor,
    Lo_Movimiento.MovimientoFecha as FechaDoc,
    Lo_Movimiento.FechaVenCredito as FechaCredito,
    CONCAT(CASE WHEN Lo_MovimientoTipo.CodSunat ='01' THEN
        'F'
    ELSE
        CASE WHEN Lo_MovimientoTipo.CodSunat='03' THEN
            'B'
        ELSE
            CASE WHEN Lo_MovimientoTipo.CodSunat='12' THEN
                'T'
            ELSE
                'OTRO'
            END
        END
    END,Lo_Movimiento.Serie,'-' , convert(Lo_Movimiento.Numero, char) )as Correlativo,
    Round(Sum(Lo_MovimientoDetalle.Precio*Lo_MovimientoDetalle.Cantidad),2) as Total,
    Round((SELECT IfNull((SELECT Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='MO' And Cb_CajaBancoDet.Hash=Lo_Movimiento.Hash),0)),2) as Aplicado,
    Round(SUM(Lo_MovimientoDetalle.Precio*Lo_MovimientoDetalle.Cantidad),2) -
    Round((SELECT IfNull((SELECT Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='MO' And Cb_CajaBancoDet.Hash=Lo_Movimiento.Hash),0)),2) as Saldo
    From Lo_Movimiento
    Inner Join Lo_MovimientoDetalle On Lo_Movimiento.Hash=Lo_MovimientoDetalle.hashMovimiento
    Inner Join Lo_MovimientoTipo On Lo_Movimiento.IdMovimientoTipo=Lo_MovimientoTipo.IdMovimientoTipo
    INNER JOIN Lo_Proveedor On Lo_Movimiento.IdProveedor = Lo_Proveedor.IdProveedor
    Where EsCredito=1";

            $select .= " AND (Lo_Proveedor.Ruc LIKE '%" . $request->getParam('q') . "%'";
            $select .= " OR Lo_Proveedor.Proveedor LIKE '%" . $request->getParam('q') . "%') ";

    $select .= "        
    Group by
    Lo_Movimiento.Hash,
    Lo_Movimiento.MovimientoFecha,
    Lo_Movimiento.FechaVenCredito,
    Lo_MovimientoTipo.CodSunat,
    Lo_Movimiento.Serie,
    Lo_Movimiento.Numero
    HAVING Round(Sum(Lo_MovimientoDetalle.Precio*Lo_MovimientoDetalle.Cantidad),2) -
    Round((SELECT IfNull((Select Sum(Cb_CajaBancoDet.Importe) From Cb_CajaBancoDet Where Tipo='MO' And Cb_CajaBancoDet.Hash=Lo_Movimiento.Hash),0)),2)>0
    Order by MovimientoFecha";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});

$app->get('/reporte/deudaclientes2', function (Request $request, Response $response, array $args) use ($app) {

    $res = $app->subRequest('GET', 'cliente/deudas');

    
    $deudacliente = (string) $res->getBody();
    $deudacliente = json_decode($deudacliente, true);
    
    $excel = new Spreadsheet();
    //$sheet = $excel->setActiveSheetIndex(0);
    $sheet = $excel->getActiveSheet();
    $sheet->setCellValue('A1', 'DNI / RUC');
    $sheet->setCellValue('B1', 'CLIENTE');
    $sheet->setCellValue('C1', 'SERIE');
    $sheet->setCellValue('D1', 'TOTAL');
    $sheet->setCellValue('E1', 'APLICADO');
    $sheet->setCellValue('F1', 'SALDO');
    $sheet->setCellValue('G1', 'FECHA EMISION');
    $sheet->setCellValue('H1', 'FECHA VENCIMIENTO');
    $cont = 3;
    foreach($deudacliente as $prod) {
        $sheet->setCellValue('A'.$cont, $prod['DniRuc']);
        $sheet->setCellValue('B'.$cont, $prod['Cliente']);
        $sheet->setCellValue('C'.$cont, $prod['Correlativo']);
        $sheet->setCellValue('D'.$cont, 'S/. ' . $prod['Total']);
        $sheet->setCellValue('E'.$cont, 'S/. ' . $prod['Aplicado']);
        $sheet->setCellValue('F'.$cont, 'S/. ' . $prod['Saldo']);
        $sheet->setCellValue('G'.$cont, date("d/m/Y", strtotime($prod['FechaDoc'])));
        $sheet->setCellValue('H'.$cont, date("d/m/Y", strtotime($prod['FechaCredito'])));
        $cont += 1;
    }

    $excelWriter = new Xlsx($excel);

    $fileName = 'deudacliente2' . getNow('Y-m-d-H-i-s').  '.xlsx';
    $excelFileName = __DIR__ . '/reporte/' . $fileName;
    $excelWriter->save($excelFileName);

    echo "<script>window.location.href = '/api/reporte/" . $fileName . "'</script>";
    exit;
});

$app->get('/reporte/deudaproveedores', function (Request $request, Response $response, array $args) use ($app) {

    $res = $app->subRequest('GET', 'proveedores/deudas');

    
    $deudacliente = (string) $res->getBody();
    $deudacliente = json_decode($deudacliente, true);
    
    $excel = new Spreadsheet();
    //$sheet = $excel->setActiveSheetIndex(0);
    $sheet = $excel->getActiveSheet();
    $sheet->setCellValue('A1', 'DNI / RUC');
    $sheet->setCellValue('B1', 'PROVEEDOR');
    $sheet->setCellValue('C1', 'SERIE');
    $sheet->setCellValue('D1', 'TOTAL');
    $sheet->setCellValue('E1', 'APLICADO');
    $sheet->setCellValue('F1', 'SALDO');
    $sheet->setCellValue('G1', 'FECHA EMISION');
    $sheet->setCellValue('H1', 'FECHA VENCIMIENTO');

    $cont = 3;
    foreach($deudacliente as $prod) {
        $sheet->setCellValue('A'.$cont, $prod['Ruc']);
        $sheet->setCellValue('B'.$cont, $prod['Proveedor']);
        $sheet->setCellValue('C'.$cont, $prod['Correlativo']);
        $sheet->setCellValue('D'.$cont, 'S/. ' . $prod['Total']);
        $sheet->setCellValue('E'.$cont, 'S/. ' . $prod['Aplicado']);
        $sheet->setCellValue('F'.$cont, 'S/. ' . $prod['Saldo']);
        $sheet->setCellValue('G'.$cont, date("d/m/Y", strtotime($prod['FechaDoc'])));
        $sheet->setCellValue('H'.$cont, date("d/m/Y", strtotime($prod['FechaCredito'])));
        $cont += 1;
    }

    $excelWriter = new Xlsx($excel);

    $fileName = 'deudaproveedores' . getNow('Y-m-d-H-i-s').  '.xlsx';
    $excelFileName = __DIR__ . '/reporte/' . $fileName;
    $excelWriter->save($excelFileName);

    echo "<script>window.location.href = '/api/reporte/" . $fileName . "'</script>";
    exit;
});

$app->get('/ranking/clientes', function (Request $request, Response $response, array $args) {
    $dni = $request->getParam('q');
    $select = "SELECT
        Ve_DocVentaCliente.DniRuc,
        Ve_DocVentaCliente.Cliente,
        Ve_DocVentaCliente.Puntos,
        Ve_DocVentaCliente.FechaNacimiento,
        Ve_DocVenta.IdDocVenta,
        Round(Sum((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2) as Total
        FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente =Ve_DocVentaCliente.IdCliente";

        $select .= " WHERE (Ve_DocVentaCliente.Cliente LIKE '%" . $request->getParam('q') . "%'";
        $select .= " OR Ve_DocVentaCliente.DniRuc LIKE '%" . $request->getParam('q') . "%') ";       

        $select .= "GROUP BY Ve_DocVentaCliente.DniRuc
                     ORDER BY Total DESC, Ve_DocVentaCliente.Puntos DESC";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);

});

$app->get('/ranking/clientes/count', function (Request $request, Response $response, array $args) {
    $select = "SELECT COUNT(*) as total FROM Ve_DocVentaCliente";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);

});







$app->get('/tipodoc/{id}', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM Ve_DocVentaTipoDoc WHERE ";
    $select .= "  Ve_DocVentaTipoDoc.IdTipoDoc = '" . $args['id'] . "' ";

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

$app->get('/usuarios/{usuario}', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM Seg_Usuario WHERE (Anulado != 1 OR Anulado IS NULL)";
    $select .= " AND Seg_Usuario.Usuario = '$args[usuario]' ";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();    

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
    $direccion2 = $request->getParam('Direccion2');
    $direccion3 = $request->getParam('Direccion3');
    $telefono = $request->getParam('Telefono');
    $email = $request->getParam('Email');
    $sexo = $request->getParam('Sexo');
    $ocupacion = $request->getParam('Ocupacion');
    $fechaNacimiento = $request->getParam('FechaNacimiento') ? $request->getParam('FechaNacimiento') : NULL;
    $nacionalidad = $request->getParam('Nacionalidad');
    $esVIP = $request->getParam('EsVIP');

    $idCliente = $request->getParam('IdCliente');
    $insertId = $idCliente;
    if ($idCliente) {
        $update = $this->db->update(array(
            "Cliente" => $cliente,
            'DniRuc' => $dniRuc,
            'Direccion' => $direccion,
            'Direccion2' => $direccion2,
            'Direccion3' => $direccion3,
            'Telefono' => $telefono,
            'Email' => $email,
            "Sexo" => $sexo,
            "Ocupacion" => $ocupacion,
            "FechaNacimiento" => $fechaNacimiento,
            "Nacionalidad" => $nacionalidad,
            "EsVIP" => $esVIP,
        ))
       ->table('Ve_DocVentaCliente')
       ->where('IdCliente', '=', $idCliente);
        $affectedRows = $update->execute();
    } else {
        $insert = $this->db->insert(array('Cliente', 'DniRuc', 'Direccion', 'Direccion2', 'Direccion3', 'Telefono', 'Email', 'Anulado', 'FechaReg', 'Sexo', 'Ocupacion', 'FechaNacimiento', 'Nacionalidad', 'EsVIP'))
                           ->into('Ve_DocVentaCliente')
                           ->values(array($cliente, $dniRuc, $direccion, $direccion2, $direccion3, $telefono, $email, '0', getNow(), $sexo, $ocupacion, $fechaNacimiento, $nacionalidad, $esVIP));
        
        $insertId = $insert->execute();

    }

    $select = "SELECT * FROM Ve_DocVentaCliente WHERE IdCliente=$insertId";
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();  

    return $response->withJson($data);
});












// DATOS GLOBALES DE LA EMPRESA
define('NRO_DOCUMENTO_EMPRESA', '20393999463');
define('TIPO_DOCUMENTO_EMPRESA', '6'); //1 DNI 6 RUC
define('TIPO_PROCESO', '01'); //01 PRODUCCION 03 BETA
define('RAZON_SOCIAL_EMPRESA', 'INVERSIONES TURISTICOS JHOR BUSH S.A.C');
define('NOMBRE_COMERCIAL_EMPRESA', 'INVERSIONES TURISTICOS JHOR BUSH S.A.C');
define('CODIGO_UBIGEO_EMPRESA', "250101");
define('DIRECCION_EMPRESA', "JR. SALAVERRY NRO. 673 UCAYALI - CORONEL PORTILLO - CALLERIA");
define('DEPARTAMENTO_EMPRESA', "UCAYALI");
define('PROVINCIA_EMPRESA', "CORONEL PORTILLO");
define('DISTRITO_EMPRESA', "CALLERIA");
define('CODIGO_PAIS_EMPRESA', 'PE');
define('USUARIO_SOL_EMPRESA', 'BUSH12JO'); // cambiar cuando se pase a produccion //NEURO123
define('PASS_SOL_EMPRESA', 'BUSH12jO'); // cambiar cuando se pase a produccion

$app->post('/emitirelectronico', function (Request $request, Response $response) {
    include_once("../controllers/NumerosEnLetras/NumerosEnLetras.php");

    $docVenta = $request->getParam('docVenta');
    $new = new api_sunat();

    $detalle = array();
    $json = array();
    $n=0;
    $descuento = 0;

    foreach ($docVenta['productos'] as $producto) {
        $n=$n+1;

        // $igv = $docVenta['TieneIgv'] ? round($producto['Total'] * 0.18, 2) : 0;
        $igv = $docVenta['TieneIgv'] ? round($producto['Total'] - ($producto['Total'] / 1.18) , 2) : 0;
        $subtotal = $docVenta['TieneIgv'] ? $producto['Total'] - $igv : $producto['Total'];

        $json['txtITEM']=$n;
        $json["txtUNIDAD_MEDIDA_DET"] = $producto['CodigoMedicion'];
        $json["txtCANTIDAD_DET"] = $producto['Cantidad'];
        $json["txtPRECIO_DET"] = $producto['Precio'];
        $json["txtSUB_TOTAL_DET"] = $subtotal;  //PRECIO * CANTIDAD                       
        $json["txtPRECIO_TIPO_CODIGO"] = "01"; // 02 valor referencial unitario en operaciones no onerosas
        $json["txtIGV"] = $igv;
        $json["txtISC"] = "0";
        // $json["txtIMPORTE_DET"] = $producto['Total']; //rowData.IMPORTE; //SUB_TOTAL + IGV
        $json["txtIMPORTE_DET"] = $subtotal; //rowData.IMPORTE; //SUB_TOTAL + IGV
        $json["txtCOD_TIPO_OPERACION"] = $docVenta['CodigoIgv']; //20 si es exonerado
        $json["txtCODIGO_DET"] = $producto['CodigoBarra'];
        $json["txtDESCRIPCION_DET"] = $producto['Producto'];
        // $json["txtPRECIO_SIN_IGV_DET"] = round($producto['Precio'] - ($producto['Precio'] * 0.18), 2);
        $json["txtPRECIO_SIN_IGV_DET"] = $subtotal;
        
        $detalle[]=$json;
        $descuento += $producto['Descuento'];
    }

    // $gravadas = $docVenta['TieneIgv'] ? $docVenta['Total'] - ($docVenta['Total'] * 0.18) : $docVenta['Total'];
    $gravadas = $docVenta['TieneIgv'] ? round($docVenta['Total'] / 1.18, 2) : $docVenta['Total'];
    $subtotal = ($descuento > 0) ? $gravadas + $descuento : $gravadas;

    //$total = ($descuento > 0) ? $subtotal - $descuento : '0';

    $data = array(
        "txtTIPO_OPERACION"=>"0101", // corregir esto despues
        "txtTOTAL_DESCUENTO" => $descuento,
        "txtTOTAL_GRAVADAS"=> $gravadas,
        "txtSUB_TOTAL"=> $subtotal,
        "txtPOR_IGV"=> $docVenta['TieneIgv'] ? "18" : "0", 
        // "txtTOTAL_IGV"=> $docVenta['TieneIgv'] ? round($docVenta['Total'] * 0.18, 2) : 0,
        "txtTOTAL_IGV"=> $docVenta['TieneIgv'] ? round($docVenta['Total'] - ($docVenta['Total'] / 1.18), 2) : 0,
        "txtTOTAL"=> $docVenta['Total'],
        "txtTOTAL_LETRAS"=> NumerosEnLetras::convertir(number_format($docVenta['Total'], 2),'SOLES',true), 
        "txtNRO_COMPROBANTE"=> $docVenta['Serie'] . "-" . $docVenta['Numero'], //
        "txtFECHA_DOCUMENTO"=> date("Y-m-d", strtotime($docVenta['FechaDoc'])),
        "txtFECHA_VTO"=> date("Y-m-d", strtotime($docVenta['FechaDoc'])),
        "txtCOD_TIPO_DOCUMENTO"=> $docVenta['CodSunat'], //01=factura,03=boleta,07=notacrediro,08=notadebito
        "txtCOD_MONEDA"=> 'PEN', //PEN= PERU
        //==========documentos de referencia(nota credito, debito)=============
        "txtTIPO_COMPROBANTE_MODIFICA"=> isset($docVenta['codSunatModifica']) && $docVenta['codSunatModifica'] ? $docVenta['codSunatModifica'] : "", //aqui completar
        "txtNRO_DOCUMENTO_MODIFICA"=> isset($docVenta['nroComprobanteModifica']) && $docVenta['nroComprobanteModifica'] ? $docVenta['nroComprobanteModifica'] : "",
        "txtCOD_TIPO_MOTIVO"=> isset($docVenta['notaIdMotivo']) && $docVenta['notaIdMotivo'] ? $docVenta['notaIdMotivo'] : "",
        "txtDESCRIPCION_MOTIVO"=> isset($docVenta['notaDescMotivo']) && $docVenta['notaDescMotivo'] ? $docVenta['notaDescMotivo'] : "", //$("[name='txtID_MOTIVO']
        //=================datos del cliente=================8
         "txtNRO_DOCUMENTO_CLIENTE"=> $docVenta['DniRuc'],
         "txtRAZON_SOCIAL_CLIENTE"=> $docVenta['Cliente'],
         "txtTIPO_DOCUMENTO_CLIENTE"=> strlen($docVenta['DniRuc']) > 9 ? "6" : "1",//1 DNI 6 RUC
         "txtDIRECCION_CLIENTE"=>$docVenta['Direccion'],
         "txtCIUDAD_CLIENTE"=>"",
         "txtCOD_PAIS_CLIENTE"=>"PE",
        //=================datos de LA EMPRESA=================	
         "txtNRO_DOCUMENTO_EMPRESA" => NRO_DOCUMENTO_EMPRESA,
         "txtTIPO_DOCUMENTO_EMPRESA"=> TIPO_DOCUMENTO_EMPRESA,
         "txtNOMBRE_COMERCIAL_EMPRESA"=> NOMBRE_COMERCIAL_EMPRESA,
         "txtCODIGO_UBIGEO_EMPRESA"=> CODIGO_UBIGEO_EMPRESA,
         "txtDIRECCION_EMPRESA"=> DIRECCION_EMPRESA,
         "txtDEPARTAMENTO_EMPRESA"=> DEPARTAMENTO_EMPRESA,
         "txtPROVINCIA_EMPRESA"=> PROVINCIA_EMPRESA,
         "txtDISTRITO_EMPRESA"=> DISTRITO_EMPRESA,
         "txtCODIGO_PAIS_EMPRESA"=> CODIGO_PAIS_EMPRESA,
         "txtRAZON_SOCIAL_EMPRESA"=> RAZON_SOCIAL_EMPRESA,
         "txtUSUARIO_SOL_EMPRESA"=> USUARIO_SOL_EMPRESA,
         "txtPASS_SOL_EMPRESA"=> PASS_SOL_EMPRESA,
         "txtTIPO_PROCESO"=> TIPO_PROCESO, //01 PRODUCCION 03 BETA
         "detalle"=>$detalle,
        //"detalle" => []
    );
    if ($docVenta['CodigoIgv'] == "20") { // 20 = exonerado Igv
        $data["txtTOTAL_EXONERADAS"] = $docVenta['Total'];
    }
    
    $resultado = $new->sendPostCPE(json_encode($data));
    $me = json_decode($resultado, true);
    
    // Datos $me
    $me['data'] = array(
        "txtNRO_COMPROBANTE" => $data['txtNRO_COMPROBANTE']
    );
    // print_r($data);exit();
    $estado = 1;
    if ($me['cod_sunat'] == '0') {
        $estado = 2;
        // generamos PDF para su descarga
        $data['hash_cpe'] = $me['hash_cpe'];
        $new->creaPDF(json_encode($data));
        
        // Si es nota de credito/debito insertar en tabla
        if ($docVenta['CodSunat'] == '07') { // nota de credito
            $sqlNC = "INSERT INTO Ve_DocVentaNotaCredito (idDocVenta, Hash_cpe, Hash_cdr, Msj_sunat) VALUES 
                ($docVenta[idDocVenta], '$me[hash_cpe]', '$me[hash_cdr]', '$me[msj_sunat]')";
            $stmt = $this->db->prepare($sqlNC);
            $insert = $stmt->execute();
            $lastInsert = $this->db->lastInsertId();
            
            return $response->withJson($me);
        }

        if ($docVenta['CodSunat'] == '08') {

        }
    }

    // Nota de credito
    if ($docVenta['CodSunat'] == '07') {
        

    } else if($docVenta['CodSunat'] == '08') {


    } else {
        // Para los casos de factura y boleta
        $sql = "UPDATE Ve_DocVenta SET Estado=$estado, hash_cpe='$me[hash_cpe]', Hash_cdr='$me[hash_cdr]', 
            Msj_sunat='$me[msj_sunat]' WHERE idDocVenta='$docVenta[idDocVenta]' ";
        
        $stmt = $this->db->prepare($sql);
        $updated = $stmt->execute();
        $me['Estado'] = $estado;
    }

    return $response->withJson($me);
});

$app->post('/emitirelectronicoboleta', function (Request $request, Response $response) {
    include_once("../controllers/NumerosEnLetras/NumerosEnLetras.php");

    $new = new api_sunat();
    $boletas = $request->getParam('boletas');
    $statu = $request->getParam('statu') ? $request->getParam('statu') : 1;
    $codSunat = $request->getParam('codSunat');
    $codMoneda = "PEN";
    
    $fechaReferencia = $request->getParam('fechaReferencia'); // "2018-03-28";
    $fechaDocumento = getNow('Y-m-d');
    
    $serie = getNow('Ymd');
    // Inicio Correlativo
    $selectSec = "SELECT Secuencia FROM Sunat_Resumen WHERE Serie=$serie AND CodSunat=$codSunat ORDER BY Secuencia DESC LIMIT 1";
    $stmtSec = $this->db->query($selectSec);
    $stmtSec->execute();
    $secuencia = $stmtSec->fetch()['Secuencia'];  
    $secuencia += 1; // cambiar correlativo
    // Fin Correlativo

    $detalle = array();
    $json = array();
    $n=0;
    foreach($boletas as $boleta) {
        $n=$n+1;

        $json['ITEM'] = $n;
        $json['TIPO_COMPROBANTE'] = "03";
        $json['NRO_COMPROBANTE'] = $boleta['Serie'] . '-' . $boleta['Numero'];
        $json['TIPO_DOCUMENTO'] = strlen($boleta['DniRuc']) > 9 ? "6" : "1";
        $json['NRO_DOCUMENTO'] = $boleta['DniRuc'];
        $json['TIPO_COMPROBANTE_REF'] = "";
        $json['NRO_COMPROBANTE_REF'] = "";
        $json['STATU'] = $statu ; // 1 declara boleta , 3 mandar anuladas... primero envio y luego se anula
        $json['COD_MONEDA'] = $codMoneda;
        $json['TOTAL'] = $boleta['Total'];
        $json['GRAVADA'] = $boleta['Total']; // $boleta['Total'] - IGV //corregir gravada en Factura

        $json['ISC'] = "0";
        $json['IGV'] = "0";
        $json['OTROS'] = "0";
        $json['CARGO_X_ASIGNACION'] = "1";
        $json['MONTO_CARGO_X_ASIG'] = "0";
        $json['EXONERADO'] = $boleta['Total'];
        $json['INAFECTO'] = "0";
        $json['EXPORTACION'] = "0";
        $json['GRATUITAS'] = "0";
        $detalle[]=$json;
    }

    $data = array(
        "NRO_DOCUMENTO_EMPRESA" => NRO_DOCUMENTO_EMPRESA,
        "RAZON_SOCIAL" => RAZON_SOCIAL_EMPRESA,
        "TIPO_DOCUMENTO" => TIPO_DOCUMENTO_EMPRESA,
        "CODIGO" => "RC",
        "SERIE" => $serie,
        "SECUENCIA" => $secuencia, 
        "FECHA_REFERENCIA" => $fechaReferencia,
        "FECHA_DOCUMENTO" => $fechaDocumento,
        "TIPO_PROCESO" => TIPO_PROCESO,
        "USUARIO_SOL_EMPRESA" => USUARIO_SOL_EMPRESA,
        "PASS_SOL_EMPRESA" => PASS_SOL_EMPRESA,
        "detalle" => $detalle
    );
    
    $resultado = $new->sendresumen(json_encode($data));
    $me = json_decode($resultado, true);
    $estado = 1;
    $me['ticket'] = $me['msj_sunat'];

    if ($me['cod_sunat'] == '0') {

        // insert correlativo del resumen
        $insert = "INSERT INTO Sunat_Resumen (Serie, Secuencia, CodSunat) VALUES ('$serie', $secuencia, '$codSunat')";
        $stmt = $this->db->prepare($insert);
        $inserted = $stmt->execute();
        $lastInsert = $this->db->lastInsertId();
        $estado = ($statu == 3) ? 0 : 2; // si se manda a anular el estado sera 0

        // Inicio Consulta Ticket
        $dataTicket = array(
            "TIPO_PROCESO" => TIPO_PROCESO, 
            "NRO_DOCUMENTO_EMPRESA" => NRO_DOCUMENTO_EMPRESA,
            "USUARIO_SOL_EMPRESA" => USUARIO_SOL_EMPRESA,
            "PASS_SOL_EMPRESA" => PASS_SOL_EMPRESA,
            "TICKET" => $me['msj_sunat'],
            "TIPO_DOCUMENTO" => "RC",
            "NRO_DOCUMENTO" => $serie . '-' . $secuencia
        );
        $resTicket = $new->sendticket(json_encode($dataTicket));
        $meTicket = json_decode($resTicket, true);
        if ($meTicket['cod_sunat'] == '0') {
            // $me = $meTicket;
            $me['msj_sunat'] = $meTicket['msj_sunat'];
            $me['hash_cdr'] = $meTicket['hash_cdr'];

            // si todo es correcto generar pdf para cada boleta
            foreach($boletas as $boleta) {
                $detallePdf = array();
                $json = array();
                $n=0;

                foreach ($boleta['productos'] as $producto) {
                    $n=$n+1;
                    $igv = $boleta['TieneIgv'] ? round($producto['Subtotal'] * 0.18, 2) : 0;
                    $subtotal = $boleta['TieneIgv'] ? $producto['Subtotal'] - $igv : $producto['Subtotal'];
                    $json["txtCANTIDAD_DET"] = $producto['Cantidad'];
                    $json["txtPRECIO_DET"] = $producto['Precio'];
                    $json["txtIMPORTE_DET"] = $subtotal + $igv; //rowData.IMPORTE; //SUB_TOTAL + IGV
                    $json["txtCODIGO_DET"] = $producto['CodigoBarra'];
                    $json["txtDESCRIPCION_DET"] = $producto['Producto'];
                    $detallePdf[]=$json;
                }
                $dataPdf = array(
                    'txtTIPO_PROCESO' => TIPO_PROCESO,
                    'txtCOD_MONEDA' => $codMoneda,
                    'txtNRO_DOCUMENTO_EMPRESA' => NRO_DOCUMENTO_EMPRESA,
                    'txtCOD_TIPO_DOCUMENTO' => '03',
                    'txtNRO_COMPROBANTE' => $boleta['Serie'] . '-' . $boleta['Numero'],
                    'txtSUB_TOTAL' => $boleta['Total'],
                    'txtTOTAL_IGV' => "0",
                    'txtTOTAL' => $boleta['Total'],
                    'txtTOTAL_LETRAS' => NumerosEnLetras::convertir(number_format($boleta['Total'], 2),'SOLES',true),
                    'txtFECHA_DOCUMENTO' => date("Y-m-d", strtotime($boleta['FechaDoc'])),
                    'txtTIPO_DOCUMENTO_CLIENTE' => strlen($boleta['DniRuc']) > 9 ? "6" : "1",
                    'txtNRO_DOCUMENTO_CLIENTE' => $boleta['DniRuc'],
                    'txtNOMBRE_COMERCIAL_EMPRESA' => NOMBRE_COMERCIAL_EMPRESA,
                    'txtDIRECCION_EMPRESA' => DIRECCION_EMPRESA,
                    'txtRAZON_SOCIAL_CLIENTE' => $boleta['Cliente'],
                    'txtDIRECCION_CLIENTE' => $boleta['Direccion'],
                    'detalle' => $detallePdf
                );
                
                // generamos PDF para su descarga
                $dataPdf['hash_cpe'] = $me['hash_cpe'];
                //$new->creaPDF(json_encode($dataPdf)); // aqui crea el PDF uno a uno
                
            }
        }

        // $me['dataTicket'] = $dataTicket;
        $me['meticket'] = $meTicket;
        // Fin Consulta Ticket
    }

    $cadenaIds = [];
    foreach($boletas as $boleta) {
        $cadenaIds[] = $boleta['idDocVenta'];
    }
    
    if($statu == 3) { // si se manda a anular
        $sql = "UPDATE Ve_DocVenta SET Estado=$estado, hash_cpe='$me[hash_cpe]', Hash_cdr='$me[hash_cdr]', 
            Msj_sunat='$me[msj_sunat]' WHERE idDocVenta IN (" . implode(',', $cadenaIds) . ")";
    } else { //si se declara
        $sql = "UPDATE Ve_DocVenta SET Estado=$estado, hash_cpe='$me[hash_cpe]', Hash_cdr='$me[hash_cdr]', 
        Msj_sunat='$me[msj_sunat]', Ticket='$me[ticket]' WHERE idDocVenta IN (" . implode(',', $cadenaIds) . ")";
    }
    //$me['sql'] = $sql;
    $stmt = $this->db->prepare($sql);
    $updated = $stmt->execute();
    $me['Estado'] = $estado;
    // $me['data'] = $data; // ocultar en produccion
    
    return $response->withJson($me);
});

$app->post('/bajaelectronico', function (Request $request, Response $response) {
    $new = new api_sunat();
    $facturas = $request->getParam('facturas');
    $codSunat = $request->getParam('codSunat');
    $descripcion = $request->getParam('descripcion');

    // $fechaReferencia = $request->getParam('fechaReferencia'); // "2018-03-28";
    $fechaReferencia = date("Y-m-d", strtotime($facturas[0]['FechaDoc']));
    $fechaBaja = getNow('Y-m-d');

    $serie = getNow('Ymd');
    // Inicio Correlativo
    $selectSec = "SELECT Secuencia FROM Sunat_Resumen WHERE Serie=$serie AND CodSunat=$codSunat ORDER BY Secuencia DESC LIMIT 1";
    $stmtSec = $this->db->query($selectSec);
    $stmtSec->execute();
    $secuencia = $stmtSec->fetch()['Secuencia'];  
    $secuencia += 1; // cambiar correlativo
    // Fin Correlativo

    $detalle = array();
    $json = array();
    $n=0;
    foreach($facturas as $factura) {
        $n=$n+1;

        $json['ITEM'] = $n;
        $json['TIPO_COMPROBANTE'] = "01";
        $json['SERIE'] = $factura['Serie'];
        $json['NUMERO'] = $factura['Numero'];
        $json['DESCRIPCION'] = $descripcion;
        $detalle[]=$json;
    }

    $data = array(
        "NRO_DOCUMENTO_EMPRESA" => NRO_DOCUMENTO_EMPRESA,
        "RAZON_SOCIAL" => RAZON_SOCIAL_EMPRESA,
        "TIPO_DOCUMENTO" => TIPO_DOCUMENTO_EMPRESA,
        "CODIGO" => "RA",
        "SERIE" => $serie,
        "SECUENCIA" => $secuencia, 
        "FECHA_REFERENCIA" => $fechaReferencia,
        "FECHA_BAJA" => $fechaBaja,
        "TIPO_PROCESO" => TIPO_PROCESO,
        "USUARIO_SOL_EMPRESA" => USUARIO_SOL_EMPRESA,
        "PASS_SOL_EMPRESA" => PASS_SOL_EMPRESA,
        "detalle" => $detalle
    );

    $resultado = $new->sendbaja(json_encode($data));
    $me = json_decode($resultado, true);
    $estado = 1;

    if ($me['cod_sunat'] == '0') {
        // insert correlativo del resumen
        $insert = "INSERT INTO Sunat_Resumen (Serie, Secuencia, CodSunat) VALUES ('$serie', $secuencia, '$codSunat')";
        $stmt = $this->db->prepare($insert);
        $inserted = $stmt->execute();
        $lastInsert = $this->db->lastInsertId();
        $estado = 0; // estado 0 anulado / 1 pendiente / 2 es emitido 

        // Inicio Consulta Ticket
        $dataTicket = array(
            "TIPO_PROCESO" => TIPO_PROCESO, 
            "NRO_DOCUMENTO_EMPRESA" => NRO_DOCUMENTO_EMPRESA,
            "USUARIO_SOL_EMPRESA" => USUARIO_SOL_EMPRESA,
            "PASS_SOL_EMPRESA" => PASS_SOL_EMPRESA,
            "TICKET" => $me['msj_sunat'],
            "TIPO_DOCUMENTO" => "RA",
            "NRO_DOCUMENTO" => $serie . '-' . $secuencia
        );
        $resTicket = $new->sendticket(json_encode($dataTicket));
        $meTicket = json_decode($resTicket, true);
        if ($meTicket['cod_sunat'] == '0') {
            $me = $meTicket;
        }

        // $me['dataTicket'] = $dataTicket;
        $me['meticket'] = $meTicket;
        // Fin Consulta Ticket
    }

    $cadenaIds = [];
    foreach($facturas as $factura) {
        $cadenaIds[] = $factura['idDocVenta'];
    }
    
    $sql = "UPDATE Ve_DocVenta SET Estado=$estado, hash_cpe='$me[hash_cpe]', Hash_cdr='$me[hash_cdr]', 
        Msj_sunat='$me[msj_sunat]', AnuladoDesc='$descripcion' WHERE idDocVenta IN (" . implode(',', $cadenaIds) . ")";

    //$me['sql'] = $sql;
    $stmt = $this->db->prepare($sql);
    $updated = $stmt->execute();
    $me['Estado'] = $estado;
     $me['data'] = $data; // ocultar en produccion
    
    return $response->withJson($me);
    
}); 



$app->get('/reporte/habitaciones', function (Request $request, Response $response, array $args) use ($app) {
    $fechaInicio = $request->getParam('fechaInicio');
    $fechaFin = $request->getParam('fechaFin');

    $select = "SELECT Ve_PreOrden.IdPreOrden, Ve_DocVenta.idDocVenta, PreOrdenHabitacion.Producto, PreOrdenHabitacion.ProductoMarca, Ve_DocVentaCliente.Cliente, 
        Ve_DocVentaCliente.Direccion, Ve_DocVentaCliente.DniRuc, 
        Ve_DocVentaCliente.Sexo, Ve_DocVentaCliente.Ocupacion, Ve_DocVentaCliente.FechaNacimiento, Ve_DocVentaCliente.Nacionalidad, 
        Ve_PreOrden.FechaReg, Ve_PreOrden.LugarProcedencia, Ve_PreOrden.MedioTransporte, Ve_PreOrden.ProximoDestino,
        Ve_PreOrden.FechaAlquilerInicio AS FechaAlquilerInicio, Ve_PreOrden.FechaAlquilerFin, 
        /* DocVentaHabitacion.FechaAlquilerFin,  */
        (DocVentaHabitacion.Cantidad * DocVentaHabitacion.Precio) - DocVentaHabitacion.Descuento AS Tarifa
        FROM Ve_PreOrden
        
        INNER JOIN Ve_DocVentaCliente ON Ve_PreOrden.IdCliente = Ve_DocVentaCliente.IdCliente
        LEFT JOIN Ve_DocVenta ON Ve_PreOrden.IdDocVenta = Ve_DocVenta.idDocVenta
        
        INNER JOIN 
        (SELECT  Ve_PreOrdenDet.IdPreOrden, Gen_Producto.EsHabitacion, Gen_Producto.Producto, Gen_ProductoMarca.ProductoMarca
        FROM Ve_PreOrdenDet
        INNER JOIN Gen_Producto ON Ve_PreOrdenDet.IdProducto = Gen_Producto.IdProducto
        LEFT JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
        WHERE Gen_Producto.EsHabitacion = 1
        GROUP BY Ve_PreOrdenDet.IdPreOrden) AS PreOrdenHabitacion ON Ve_PreOrden.IdPreOrden = PreOrdenHabitacion.IdPreOrden
        
        LEFT JOIN
        (SELECT Ve_DocVentaDet.IdDocVenta, Ve_DocVentaDet.IdProducto, Ve_DocVentaDet.FechaAlquilerInicio, 
        Ve_DocVentaDet.FechaAlquilerFin, Ve_DocVentaDet.Cantidad, Ve_DocVentaDet.Precio, Ve_DocVentaDet.Descuento
        FROM Ve_DocVentaDet
        INNER JOIN Gen_Producto ON Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto
        WHERE Gen_Producto.EsHabitacion = 1
        GROUP BY Ve_DocVentaDet.IdDocVenta) AS DocVentaHabitacion ON Ve_PreOrden.IdDocVenta = DocVentaHabitacion.IdDocVenta
        
        WHERE PreOrdenHabitacion.EsHabitacion = 1 AND Ve_PreOrden.FechaAlquilerInicio BETWEEN '" . $fechaInicio . " 00:00:00'" . " AND '" . $fechaFin . " 23:59:59'" . " 
        ORDER BY Ve_PreOrden.IdPreOrden DESC";
        //var_dump($select); exit();
        
        $stmt = $this->db->query($select);
        $stmt->execute();
        $data = $stmt->fetchAll();    

        return $response->withJson($data);

});



$app->get('/reporte/ventas', function (Request $request, Response $response, array $args) use ($app) {
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaInicio = $request->getParam('fechaInicio');
    $fechaFin = $request->getParam('fechaFin');
    $declarado = $request->getParam('declarado') ? 1 : 0;
    
    $res = $app->subRequest('GET', '/ventas', 'idAlmacen=' . $idAlmacen . '&filter[fechaInicio]=' . $fechaInicio . '&filter[fechaFin]=' . $fechaFin . '&filter[declarado]=' . $declarado . '&sortBy=Ve_DocVenta.FechaDoc&sortDesc=DESC');
    $ventas = (string) $res->getBody();
    $ventas = json_decode($ventas, true);
    // print_r($ventas);exit();
    
    $excel = new Spreadsheet();
    //$sheet = $excel->setActiveSheetIndex(0);
    $sheet = $excel->getActiveSheet();
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'FECHAE');
    $sheet->setCellValue('C1', 'FECHAV');
    $sheet->setCellValue('D1', 'TIPOC');
    $sheet->setCellValue('E1', 'SERIE');
    $sheet->setCellValue('F1', 'NUMERO');
    $sheet->setCellValue('G1', 'TIPODOC');
    $sheet->setCellValue('H1', 'DOCUMENTO');
    $sheet->setCellValue('I1', 'NOMBRE');
    $sheet->setCellValue('J1', 'BASEI');
    $sheet->setCellValue('K1', 'IGV');
    $sheet->setCellValue('L1', 'EXONERADO');
    $sheet->setCellValue('M1', 'RETENCION');
    $sheet->setCellValue('N1', 'TOTAL');

    // $sheet->setCellValue('N1', 'ES ANULADO');
    

    $cont = 3;
    foreach($ventas as $ven) {
        $exonerado = 0;
        $igv = 0;
        $subtotal = $ven['Total'];
        if ($ven['TieneIgv']) {
            // $igv = $ven['Total'] * 0.18;
            // $subtotal = $ven['Total'] - $igv;
            $subtotal = round($ven['Total'] / 1.18, 2);
            $igv = round($ven['Total'] - $subtotal, 2);
        }

        if ($ven['CodigoIgv'] == '20') {
            $subtotal = 0;
            $igv = 0;  
            $exonerado = $ven['Total'];
        }

        if ($ven['Anulado']) {
            $subtotal = 0;
            $igv = 0;
            $exonerado = 0;
            $ven['Total'] = 0;
        }

        $sheet->setCellValue('A'.$cont, $ven['idDocVenta']);
        $sheet->setCellValue('B'.$cont, date("d/m/Y", strtotime($ven['FechaDoc'])));
        $sheet->setCellValue('C'.$cont, date("d/m/Y", strtotime($ven['FechaDoc'])));
        $sheet->setCellValue('D'.$cont, $ven['CodSunat']);
        $sheet->setCellValue('E'.$cont, $ven['Serie']);
        $sheet->setCellValue('F'.$cont, $ven['Numero']);

        // $sheet->setCellValue('F'.$cont, $ven['TipoDoc']);
        $sheet->setCellValue('G'.$cont, strlen($ven['DniRuc']) > 9 ? "6" : "1");
        $sheet->setCellValue('H'.$cont, $ven['DniRuc']);
        $sheet->setCellValue('I'.$cont, $ven['Cliente']);
        $sheet->setCellValue('J'.$cont, $subtotal);
        $sheet->setCellValue('K'.$cont, $igv);
        $sheet->setCellValue('L'.$cont, $exonerado);
        $sheet->setCellValue('M'.$cont, 0);
        $sheet->setCellValue('N'.$cont, $ven['Total']);
        
        // $sheet->setCellValue('N'.$cont, $ven['Anulado']);;
        
        $cont += 1;
    }

    $excelWriter = new Xlsx($excel);

    $fileName = 'reporteventas.xlsx';
    $excelFileName = __DIR__ . '/reporte/' . $fileName;
    $excelWriter->save($excelFileName);
    // For Excel2007 and above .xlsx files   
    // $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    /*$response = $response->withHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    $stream = fopen($excelFileName, 'r+');
    return $response->withBody(new \Slim\Http\Stream($stream));*/

    echo "<script>window.location.href = '/api/reporte/" . $fileName . "'</script>";
    exit;
});

$app->get('/reporte/rankingclientes', function (Request $request, Response $response, array $args) use ($app) {

    $res = $app->subRequest('GET', 'ranking/clientes');

    
    $deudacliente = (string) $res->getBody();
    $deudacliente = json_decode($deudacliente, true);
    
    $excel = new Spreadsheet();
    //$sheet = $excel->setActiveSheetIndex(0);
    $sheet = $excel->getActiveSheet();
    $sheet->setCellValue('A1', 'DNI / RUC');
    $sheet->setCellValue('B1', 'CLIENTE');
    $sheet->setCellValue('C1', 'FECHA NACIMIENTO');
    $sheet->setCellValue('D1', 'PUNTOS');
    $sheet->setCellValue('E1', 'TOTAL');

    $cont = 3;
    foreach($deudacliente as $prod) {
        $sheet->setCellValue('A'.$cont, $prod['DniRuc']);
        $sheet->setCellValue('B'.$cont, $prod['Cliente']);
        $sheet->setCellValue('C'.$cont, date("d/m/Y", strtotime($prod['FechaNacimiento'])));
        $sheet->setCellValue('D'.$cont, $prod['Puntos']);
        $sheet->setCellValue('E'.$cont, 'S/. ' . $prod['Total']);
        $cont += 1;
    }

    $excelWriter = new Xlsx($excel);

    $fileName = 'rankingclientes' . getNow('Y-m-d-H-i-s').  '.xlsx';
    $excelFileName = __DIR__ . '/reporte/' . $fileName;
    $excelWriter->save($excelFileName);

    echo "<script>window.location.href = '/api/reporte/" . $fileName . "'</script>";
    exit;
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
    $sheet->setCellValue('E1', 'PRESENTACION');
    $sheet->setCellValue('F1', 'PRECIO CONTADO');
    $sheet->setCellValue('G1', 'STOCK');

    $cont = 3;
    foreach($productos as $prod) {
        $sheet->setCellValue('A'.$cont, $prod['CodigoBarra']);
        $sheet->setCellValue('B'.$cont, $prod['ProductoCategoria']);
        $sheet->setCellValue('C'.$cont, $prod['Producto']);
        $sheet->setCellValue('D'.$cont, $prod['ProductoMarca']);
        $sheet->setCellValue('E'.$cont, $prod['ProductoPresentacion']);;
        $sheet->setCellValue('F'.$cont, 'S/. ' . $prod['PrecioContado']);
        $sheet->setCellValue('G'.$cont, $prod['stock']);
        $cont += 1;
    }

    $excelWriter = new Xlsx($excel);

    $fileName = 'stock' . getNow('Y-m-d-H-i-s').  '.xlsx';
    $excelFileName = __DIR__ . '/reporte/' . $fileName;
    $excelWriter->save($excelFileName);
    // For Excel2007 and above .xlsx files   
    // $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    /*$response = $response->withHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    $stream = fopen($excelFileName, 'r+');
    return $response->withBody(new \Slim\Http\Stream($stream));*/

    echo "<script>window.location.href = '/api/reporte/" . $fileName . "'</script>";
    exit;
});


$app->post('/cierrecaja', function (Request $request, Response $response) {
    $idTurno = $request->getParam('idTurno');
    $user = $request->getParam('user');
    $fechaCierre = getNow();
    $usuarioReg = 'xx';
    if(isset($_SESSION['user'])) {
        $usuarioReg = $_SESSION['user'];
    }
    $usuarioReg = $user ? $user : $usuarioReg;
    
    $insert = "INSERT INTO Cb_CierreCaja (FechaCierre, IdTurno, UsuarioReg)
        VALUES ('$fechaCierre', '$idTurno', '$usuarioReg')";
    $stmt = $this->db->prepare($insert);
    $inserted = $stmt->execute();
    $idCierreCaja = $this->db->lastInsertId();
    
    // Actualizar DocVenta
    $updateVenta = "UPDATE Ve_DocVenta SET IdCierre=$idCierreCaja WHERE IdCierre IS NULL 
        AND UsuarioReg='" . $usuarioReg . "'";
    $stmt = $this->db->prepare($updateVenta);
    $updatedVenta = $stmt->execute(); 

    // Actualizar CajaBanco asignando cierre solo de un vendedor
    $updateCajaBanco = "UPDATE Cb_CajaBanco SET IdCierre=$idCierreCaja WHERE IdCierre IS NULL
        AND EsDelVendedor=1 AND UsuarioReg='" . $usuarioReg . "'";
    $stmt = $this->db->prepare($updateCajaBanco);
    $updatedCajaBanco = $stmt->execute();

    return $response->withJson(array(
        "idTurno" => $idTurno,
        "fechaCierre" => $fechaCierre,
        "idCierraCaja" => $idCierreCaja
    ));
});


$app->get('/cierrecaja', function (Request $request, Response $response, array $args) {
    $idCierre = $request->getParam('idCierre');
    $filter = $request->getParam('filter');

    $select = "SELECT * FROM Cb_CierreCaja WHERE (FechaCierre LIKE '%$filter%' OR UsuarioReg LIKE '%$filter%')";

    if ($idCierre) {
        $select .= " AND IdCierreCaja=$idCierre";
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


$app->get('/cierrecaja/count', function (Request $request, Response $response, array $args) {

    $select = "SELECT COUNT(*) AS total FROM Cb_CierreCaja";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});

$app->get('/cierrecaja/ventas', function (Request $request, Response $response, array $args) {
    $idCierre = $request->getParam('idCierre');

    $select = "SELECT
        Ve_DocVenta.idDocVenta, Ve_DocVentaPuntoVenta.PuntoVenta, Ve_DocVentaCliente.Cliente, Ve_DocVenta.FechaDoc, Ve_DocVenta.Anulado,
        Ve_DocVentaTipoDoc.TipoDoc, Ve_DocVenta.Serie, Ve_DocVenta.Numero, SUM((Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio) - Ve_DocVentaDet.Descuento) as Total,
        Ve_DocVenta.EsCredito,
        (SELECT Ve_DocVentaMetodoPagoDet.NroTarjeta FROM Ve_DocVentaMetodoPagoDet WHERE Ve_DocVentaMetodoPagoDet.IdDocVenta = Ve_DocVenta.idDocVenta AND Ve_DocVentaMetodoPagoDet.IdMetodoPago = 1) AS EfectivoDesc,
        (SELECT Ve_DocVentaMetodoPagoDet.NroTarjeta FROM Ve_DocVentaMetodoPagoDet WHERE Ve_DocVentaMetodoPagoDet.IdDocVenta = Ve_DocVenta.idDocVenta AND Ve_DocVentaMetodoPagoDet.IdMetodoPago = 2) AS VisaDesc,
        (SELECT Ve_DocVentaMetodoPagoDet.NroTarjeta FROM Ve_DocVentaMetodoPagoDet WHERE Ve_DocVentaMetodoPagoDet.IdDocVenta = Ve_DocVenta.idDocVenta AND Ve_DocVentaMetodoPagoDet.IdMetodoPago = 3) AS MastercardDesc,

        IFNULL((SELECT SUM(Ve_DocVentaMetodoPagoDet.Importe) FROM Ve_DocVentaMetodoPagoDet WHERE Ve_DocVentaMetodoPagoDet.IdDocVenta = Ve_DocVenta.idDocVenta AND Ve_DocVentaMetodoPagoDet.IdMetodoPago = 1), 0) AS Efectivo,
        IFNULL((SELECT SUM(Ve_DocVentaMetodoPagoDet.Importe) FROM Ve_DocVentaMetodoPagoDet WHERE Ve_DocVentaMetodoPagoDet.IdDocVenta = Ve_DocVenta.idDocVenta AND Ve_DocVentaMetodoPagoDet.IdMetodoPago = 2), 0) AS Visa,
        IFNULL((SELECT SUM(Ve_DocVentaMetodoPagoDet.Importe) FROM Ve_DocVentaMetodoPagoDet WHERE Ve_DocVentaMetodoPagoDet.IdDocVenta = Ve_DocVenta.idDocVenta AND Ve_DocVentaMetodoPagoDet.IdMetodoPago = 3), 0) AS Mastercard
        FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVentaTipoDoc.IdTipoDoc = Ve_DocVenta.IdTipoDoc
        LEFT JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente
        INNER JOIN Ve_DocVentaPuntoVenta ON Ve_DocVenta.IdDocVentaPuntoVenta = Ve_DocVentaPuntoVenta.IdDocVentaPuntoVenta" ;
    
    if($idCierre) {
        $select .= " WHERE Ve_DocVenta.IdCierre = $idCierre";
    } else {
        $select .= " WHERE Ve_DocVenta.IdCierre IS NULL";
    }

    if($request->getParam('usuario')) {
        $select .= " AND Ve_DocVenta.UsuarioReg = '" . $request->getParam('usuario') . "'";
    }
    
    $select .= " GROUP BY Ve_DocVenta.idDocVenta
        ORDER BY Ve_DocVentaPuntoVenta.IdDocVentaPuntoVenta ASC, Ve_DocVenta.FechaDoc ASC;";
    // print_r($select); exit();
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    
    
    return $response->withJson($data);
});
$app->get('/cierrecaja/ingresos', function (Request $request, Response $response, array $args) {
    $idCierre = $request->getParam('idCierre');
    
    $select = "SELECT Cb_CajaBanco.IdCajaBanco, Cb_TipoCajaBanco.Tipo, Cb_CajaBanco.IdCuenta, Cb_Cuenta.Cuenta, Cb_CajaBanco.FechaDoc, Cb_CajaBanco.Concepto, Cb_CajaBanco.Importe
        FROM Cb_CajaBanco
        INNER JOIN Cb_TipoCajaBanco ON Cb_CajaBanco.IdTipoCajaBanco = Cb_TipoCajaBanco.IdTipoCajaBanco
        INNER JOIN Cb_Cuenta ON Cb_CajaBanco.IdCuenta = Cb_Cuenta.IdCuenta";
    
    if($idCierre) {
        $select .= " WHERE Cb_CajaBanco.IdCierre=$idCierre";
    } else {
        $select .= " WHERE Cb_CajaBanco.IdCierre IS NULL";
    }

    if($request->getParam('usuario')) {
        $select .= " AND EsDelVendedor=1 AND Cb_CajaBanco.UsuarioReg = '" . $request->getParam('usuario') . "'";
    }

    $select .= " AND Cb_TipoCajaBanco.Tipo = 0
        ORDER BY FechaDoc ASC;";
    
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});

$app->get('/cierrecaja/ingresos/adelantos', function (Request $request, Response $response, array $args) {
    $idDocVentas = $request->getParam('idDocVentas');

    $select = "SELECT Cb_CajaBanco.IdCajaBanco, Cb_TipoCajaBanco.Tipo, Cb_CajaBanco.IdCuenta, Cb_Cuenta.Cuenta, Cb_CajaBanco.FechaDoc, Cb_CajaBanco.Concepto  , Cb_CajaBanco.Importe, Ve_DocVenta.idDocVenta, Ve_DocVenta.Serie, Ve_DocVenta.Numero 
          FROM Cb_CajaBanco
          INNER JOIN Cb_TipoCajaBanco ON Cb_CajaBanco.IdTipoCajaBanco = Cb_TipoCajaBanco.IdTipoCajaBanco
          INNER JOIN Cb_Cuenta ON Cb_CajaBanco.IdCuenta = Cb_Cuenta.IdCuenta
          LEFT JOIN Ve_PreOrden ON Cb_CajaBanco.IdPreOrden = Ve_PreOrden.IdPreOrden
          LEFT JOIN Ve_DocVenta ON Ve_PreOrden.IdDocVenta = Ve_DocVenta.idDocVenta";
    
      if($idDocVentas) {
          $select .= " WHERE Ve_DocVenta.idDocVenta IN ( " . implode(',', $idDocVentas) . " )";
      }                                                                                                                                                                                                               
      $select .= " AND Cb_TipoCajaBanco.Tipo = 0
          ORDER BY FechaDoc ASC;";
    
      $stmt = $this->db->query($select);
      $stmt->execute();
      $data = $stmt->fetchAll();
   
      return $response->withJson($data);

});

$app->get('/cierrecaja/salidas', function (Request $request, Response $response, array $args) use ($app) {
    $idCierre = $request->getParam('idCierre');
    
    $select = "SELECT Cb_CajaBanco.IdCajaBanco, Cb_TipoCajaBanco.Tipo, Cb_CajaBanco.IdCuenta, Cb_Cuenta.Cuenta, Cb_CajaBanco.FechaDoc, Cb_CajaBanco.Concepto, Cb_CajaBanco.Importe
        FROM Cb_CajaBanco
        INNER JOIN Cb_TipoCajaBanco ON Cb_CajaBanco.IdTipoCajaBanco = Cb_TipoCajaBanco.IdTipoCajaBanco
        INNER JOIN Cb_Cuenta ON Cb_CajaBanco.IdCuenta = Cb_Cuenta.IdCuenta";

    if($idCierre) {
        $select .= " WHERE Cb_CajaBanco.IdCierre=$idCierre";
    } else {
        $select .= " WHERE Cb_CajaBanco.IdCierre IS NULL";
    }

    if($request->getParam('usuario')) {
        $select .= " AND EsDelVendedor=1 AND Cb_CajaBanco.UsuarioReg = '" . $request->getParam('usuario') . "'";
    }

    $select .= " AND Cb_TipoCajaBanco.Tipo = 1
        ORDER BY FechaDoc ASC;";
    
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});





$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();
