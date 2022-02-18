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
// $config['db']['user']   = "neurosys_ESTICAM";
$config['db']['pass']   = "";
// $config['db']['pass']   = "IX!!q!t(&Fc^";
$config['db']['dbname'] = "fact_iESTICAM";
// $config['db']['dbname'] = "neurosys_ESTICAM";

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

$app->post('/login', function (Request $request, Response $response, array $args) {
    
    $user = $request->getParam('user');
    $pass = $request->getParam('password');

    $select = "SELECT * FROM Seg_Usuario WHERE Usuario='$user' AND Password='$pass'";
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();  

    return $response->withJson($data);
});

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

// $app->get('/codigobarra', function (Request $request, Response $response, array $args) {
//     $q = $request->getParam('q');
//     $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;

//     $select = "SELECT * FROM Gen_Producto";
//     $select .= " WHERE CodigoBarra LIKE '" . $q . "%' ";

//     if ($limit) {
//         // $limit = $request->getParam('limit');
//         $offset = 0;
//         if ($request->getParam('page')) {
//             $page = $request->getParam('page');
//             $offset = (--$page) * $limit;
//         }
//         $select .= " LIMIT " . $limit;
//         $select .= " OFFSET " . $offset;
//     }

//     $stmt = $this->db->query($select);
//     $stmt->execute();
//     $data = $stmt->fetchAll();

//     return $response->withJson($data);
// });

// $app->post('/codigobarra', function (Request $request, Response $response, array $args) {

//     $productoMarca = $request->getParam('ProductoMarca');
//     $anulado = 0;

//     $insert = $this->db->insert(array('ProductoMarca', 'Anulado', 'FechaReg'))
//                        ->into('Gen_ProductoMarca')
//                        ->values(array($productoMarca, $anulado, getNow()));
//     $insertId = $insert->execute();

//     return $response->withJson(array("insertId" => $insertId, "ProductoMarca" => $productoMarca));
// });

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

    $limit = $request->getParam('limit') ? $request->getParam('limit') :  20;
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
    $strIngresosCajas = stringIngresoCajaProducto($idProducto, $idAlmacen, $fechaHasta);
    $strSalidas = stringSalidaUndProducto($idProducto, $idAlmacen, $fechaHasta);
    $strSalidasCajas = stringSalidaCajaProducto($idProducto, $idAlmacen, $fechaHasta);
    $strVentas = stringSalidaVentaUndProducto($idProducto, $idAlmacen, $fechaHasta);
    $strVentasCajas = stringSalidaVentaCajaProducto($idProducto, $idAlmacen, $fechaHasta);

    $select = $strIngresos . ' UNION ALL ' . $strIngresosCajas . ' UNION ALL ' . $strSalidas . ' UNION ALL ' .  $strSalidasCajas . ' UNION ALL ' . $strVentas . ' UNION ALL ' . $strVentasCajas; 
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



/* ESTICAM */
$app->get('/habitaciones', function (Request $request, Response $response, array $args) {
    $select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca,
        Gen_ProductoMedicion.ProductoMedicion, Ve_DocVentaCliente.DniRuc, Ve_DocVentaCliente.Cliente, Ve_DocVentaCliente.IdCliente,
        -- Ve_PreOrdenTotal.granTotal, Cb_CajaBancoAdelanto.sumAdelanto, Cb_CajaBancoGasto.sumGasto 
        (Ve_PreOrdenTotal.granTotal - Cb_CajaBancoAdelanto.sumAdelanto + Cb_CajaBancoGasto.sumGasto) AS granTotalCalculado,
        Ve_PreOrden.IdProforma, Ve_ProformaTotal.TotalProf 
        FROM Gen_Producto
        INNER JOIN Gen_ProductoCategoria ON Gen_Producto.IdProductoCategoria = Gen_ProductoCategoria.IdProductoCategoria
        INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
        INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
        LEFT JOIN Ve_PreOrden ON Gen_Producto.IdPreOrden = Ve_PreOrden.IdPreOrden
        LEFT JOIN (
            SELECT Ve_Proforma.*, SUM(Ve_ProformaDet.Cantidad * Ve_ProformaDet.Precio) AS TotalProf
            FROM Ve_Proforma
            INNER JOIN Ve_ProformaDet ON Ve_Proforma.IdProforma = Ve_ProformaDet.IdProforma
            WHERE Ve_Proforma.IdProforma=Ve_Proforma.IdProforma
            GROUP BY Ve_Proforma.IdProforma
        ) Ve_ProformaTotal ON Ve_ProformaTotal.IdProforma = Ve_PreOrden.IdProforma 
        LEFT JOIN Ve_DocVentaCliente ON Gen_Producto.IdClienteReserva = Ve_DocVentaCliente.IdCliente
        LEFT JOIN (
            SELECT Ve_PreOrdenDet.IdPreOrden, SUM(Ve_PreOrdenDet.Cantidad * Ve_PreOrdenDet.Precio) AS granTotal 
            FROM Ve_PreOrdenDet, Gen_Producto 
            WHERE Ve_PreOrdenDet.IdPreOrden = Gen_Producto.IdPreOrden GROUP BY Ve_PreOrdenDet.IdPreOrden
        ) Ve_PreOrdenTotal ON Ve_PreOrdenTotal.IdPreOrden = Gen_Producto.IdPreOrden
        LEFT JOIN (
            SELECT Cb_CajaBanco.IdCajaBanco, Cb_CajaBanco.IdPreOrden, SUM(Cb_CajaBanco.Importe) AS sumAdelanto 
            FROM Gen_Producto, Cb_CajaBanco  
            INNER JOIN Cb_TipoCajaBanco ON Cb_TipoCajaBanco.IdTipoCajaBanco = Cb_CajaBanco.IdTipoCajaBanco 
            WHERE Cb_TipoCajaBanco.Tipo = 0 AND Cb_CajaBanco.IdPreOrden = Gen_Producto.IdPreOrden 
            GROUP BY Cb_CajaBanco.IdPreOrden
        ) Cb_CajaBancoAdelanto ON Cb_CajaBancoAdelanto.IdPreOrden = Gen_Producto.IdPreOrden
        LEFT JOIN (
            SELECT Cb_CajaBanco.IdCajaBanco, Cb_CajaBanco.IdPreOrden, SUM(Cb_CajaBanco.Importe) AS sumGasto 
            FROM Gen_Producto, Cb_CajaBanco  
            INNER JOIN Cb_TipoCajaBanco ON Cb_TipoCajaBanco.IdTipoCajaBanco = Cb_CajaBanco.IdTipoCajaBanco 
            WHERE Cb_TipoCajaBanco.Tipo = 1 AND Cb_CajaBanco.IdPreOrden = Gen_Producto.IdPreOrden 
            GROUP BY Cb_CajaBanco.IdPreOrden
        ) Cb_CajaBancoGasto ON Cb_CajaBancoGasto.IdPreOrden = Gen_Producto.IdPreOrden";

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

$app->get('/productos/masrotacion', function (Request $request, Response $response, array $args) {

    $idProducto = 1;
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaDesde = $request->getParam('fechaDesde') ? $request->getParam('fechaDesde') : '2017-01-01T00:00:00' ;
    $fechaHasta = $request->getParam('fechaHasta');

    $strIngresoUnd = stringIngresoUnd(false, $idAlmacen, $fechaHasta, $fechaDesde);
    $strSalidaUnd = stringSalidaUnd(false, $idAlmacen, $fechaHasta, $fechaDesde);

    $strIngresoCaja = stringIngresoCaja(false, $idAlmacen, $fechaHasta, $fechaDesde);
    $strSalidaCaja = stringSalidaCaja(false, $idAlmacen, $fechaHasta, $fechaDesde);

    $strSalidaVentaUnd = stringSalidaVentaUnd(false, $idAlmacen, $fechaHasta, $fechaDesde);
    $strSalidaVentaCaja = stringSalidaVentaCaja(false, $idAlmacen, $fechaHasta, $fechaDesde);

    $strIngresoUndCount = stringIngresoUndCount(false, $idAlmacen, $fechaHasta, $fechaDesde);

    $select = "SELECT Gen_Producto.*, Gen_ProductoCategoria.ProductoCategoria, Gen_ProductoMarca.ProductoMarca,
        Gen_ProductoMedicion.ProductoMedicion,
        IFNULL(IngresoUnd.cantidad, 0) AS StockIngresoUnd,
        IFNULL(SalidaUnd.cantidad, 0) AS StockSalidaUnd,
        IFNULL(SalidaVentaUnd.cantidad, 0) AS StockSalidaVentaUnd,
        IFNULL(SalidaVentaCaja.cantidad, 0) AS StockSalidaVentaCaja,
        (IFNULL(SalidaVentaUnd.cantidad, 0) + IFNULL(SalidaVentaCaja.cantidad, 0)) AS TotalVentas,
        IFNULL(IngresoUndCount.cantidad, 0) AS TotalCompras,
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
        LEFT JOIN $strSalidaVentaCaja AS SalidaVentaCaja ON Gen_Producto.IdProducto = SalidaVentaCaja.IdProducto
        LEFT JOIN $strIngresoUndCount AS IngresoUndCount ON Gen_Producto.IdProducto = IngresoUndCount.IdProducto
        ORDER BY TotalVentas DESC";

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

    $this->db->query('SET SQL_BIG_SELECTS=1');
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
    $productoDesc = $request->getParam('ProductoDesc');
    $productoDesc2 = $request->getParam('ProductoDesc2');
    $productoDesc3 = $request->getParam('ProductoDesc3');
    $productoModelo = $request->getParam('ProductoModelo') ? $request->getParam('ProductoModelo') : '';
    $fechaReg = getNow();
    $hash = time();
    // $controlaStock = 1;
    $porcentajeUtilidad = $request->getParam('PorcentajeUtilidad')?$request->getParam('PorcentajeUtilidad'):0;
    $genero = $request->getParam('Genero');
    $color = $request->getParam('Color');
    $botapie = $request->getParam('Botapie');
    $anulado = $request->getParam('Anulado');
    $categoria = $request->getParam('categoria')['ProductoCategoria'];
    $productoPresentacion = $request->getParam('ProductoPresentacion');
    $esPadre = $request->getParam('EsPadre');
    $stockMinimo = $request->getParam('StockMinimo');
    $codigoBarra = $request->getParam('CodigoBarra');
    $precioPorMayor = $request->getParam('PrecioPorMayor') ? $request->getParam('PrecioPorMayor') : null;
    $stockPorMayor = $request->getParam('StockPorMayor') ? $request->getParam('StockPorMayor') : null;
    $esHabitacion = $request->getParam('EsHabitacion');
    $piso = $request->getParam('Piso');
    $controlaStock = $request->getParam('ControlaStock') ? $request->getParam('ControlaStock') : 0;

    $productosDet = $request->getParam('productosDet');
    $precioCosto = $request->getParam('PrecioCosto') ? $request->getParam('PrecioCosto') : 0;
    $precioContado = $request->getParam('PrecioContado') ? $request->getParam('PrecioContado') : 0;
    $precioEspecial = $request->getParam('PrecioEspecial') ? $request->getParam('PrecioEspecial') : 0;
    $preciosPorProducto = json_encode($request->getParam('PreciosPorProducto'));

    // Actualizamos el producto si le pasamos el ID
    if ($request->getParam('IdProducto')) {
        // aqui se actualiza el producto si existe
        $idProducto = $request->getParam('IdProducto');
        // $codigoBarra = $request->getParam('CodigoBarra');
        $precioConvenio = $request->getParam('precioConvenio');



        $update = $this->db->update(array(
                            "CodigoBarra" =>                    $codigoBarra,
                            "Producto" =>                       $producto,
                            "ProductoDesc" =>                   $productoDesc,
                            "ProductoDesc2" =>                  $productoDesc2,
                            "ProductoDesc3" =>                  $productoDesc3,
                            "IdProductoMarca" =>                $idProductoMarca,
                            "IdProductoFormaFarmaceutica" =>    $idProductoFormaFarmaceutica,
                            "IdProductoMedicion" =>             $idProductoMedicion,
                            "IdProductoCategoria" =>            $idProductoCategoria,
                            // "IdProductoModelo" => $idProductoModelo,
                            // "IdProductoTalla" => $idProductoTalla,
                            "ControlaStock" =>                  $controlaStock,
                            "PorcentajeUtilidad" =>             $porcentajeUtilidad,
                            "Genero" =>                         $genero, 
                            "Color" =>                          $color, 
                            "Botapie" =>                        $botapie, 
                            "Anulado" =>                        $anulado,
                            "ProductoModelo" =>                 $productoModelo,
                            "PrecioCosto" =>                    $precioCosto,
                            "PrecioContado" =>                  $precioContado,
                            "precioConvenio" =>                 $precioConvenio,
                            "PrecioEspecial" =>                 $precioEspecial,
                            "ProductoPresentacion" =>           $productoPresentacion,
                            "EsPadre" =>                        $esPadre,
                            "StockMinimo" =>                    $stockMinimo,
                            "PrecioPorMayor" =>                 $precioPorMayor,
                            "StockPorMayor"=>                   $stockPorMayor,
                            "EsHabitacion" =>                   $esHabitacion,
                            "Piso" =>                           $piso,
                            "PreciosPorProducto" =>             $preciosPorProducto
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
        return $response->withJson(array("insertId" => $idProducto));

        // return $response->withJson(array("affectedRows" => $productosDet));



    }
    // Fin actualizacion producto

    // Inicio verificar Producto
    $select = "SELECT * FROM Gen_Producto
        WHERE Producto = '" . $producto . "'";
    
    if ($codigoBarra) { 
        $select .= " OR CodigoBarra = '" . $codigoBarra . "'";
    }

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
    $insert = $this->db
        ->insert(
            array(
                'IdProductoMarca', 
                'IdProductoFormaFarmaceutica', 
                'IdProductoMedicion', 
                'IdProductoCategoria', 
                'Producto', 'ProductoDesc', 
                'ProductoDesc2', 
                'ProductoDesc3', 
                'FechaReg', 
                'Hash', 
                'ControlaStock', 
                'PorcentajeUtilidad', 
                'Genero', 
                'Color', 
                'Botapie', 
                'Anulado', 
                'ProductoModelo', 
                'ProductoPresentacion', 
                'EsPadre', 
                'PrecioPorMayor', 
                'StockMinimo',
                'StockPorMayor', 
                'CodigoBarra', 
                'PrecioCosto', 
                'PrecioContado',
                'PrecioEspecial',
                'EsHabitacion', 
                'Piso', 
                'PreciosPorProducto'))
        ->into('Gen_Producto')
        ->values(
            array(
                $idProductoMarca, 
                $idProductoFormaFarmaceutica, 
                $idProductoMedicion, 
                $idProductoCategoria, 
                $producto, 
                $productoDesc, 
                $productoDesc2, 
                $productoDesc3, 
                $fechaReg, 
                $hash, 
                $controlaStock, 
                $porcentajeUtilidad, 
                $genero, 
                $color, 
                $botapie, 
                $anulado, 
                $productoModelo, 
                $productoPresentacion, 
                $esPadre, 
                $precioPorMayor, 
                $stockMinimo, 
                $stockPorMayor,
                $codigoBarra, 
                $precioCosto, 
                $precioContado, 
                $precioEspecial,
                $esHabitacion, 
                $piso, 
                $preciosPorProducto));
    $insertId = $insert->execute();

    // Generando codigo de barras  // actualizar el nombre para que sea unico
    /* $codigoBarra = substr($categoria, 0, 2) . $insertId . substr($producto, 0, 2);
    $update = $this->db->update(array("CodigoBarra" => $codigoBarra, "Producto" => $producto . '-' . $codigoBarra))
                       ->table('Gen_Producto')
                       ->where('IdProducto', '=', $insertId);
    $affectedRows = $update->execute(); */
    $codigoBarra = $insertId;

    $update = $this->db->update(array("CodigoBarra" => $codigoBarra))
                       ->table('Gen_Producto')
                       ->where('IdProducto', '=', $insertId);
    $affectedRows = $update->execute(); 

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

$app->post('/producto/imagen/{id}', function (Request $request, Response $response, array $args) {

    if( empty($_FILES) ) {
        return;
    }
    
    $nombreImagen   = $_FILES['imagen']['name'];
    $archivo        = $_FILES['imagen']['tmp_name'];
    $ruta           = '../resources/images/productos';
    $extension      = end(explode('.', $nombreImagen));

    $nombreImagen = time() . '.' . $extension;
    $ruta = $ruta . '/' . $nombreImagen;
    
    move_uploaded_file($archivo, $ruta);
    
    $select = "UPDATE Gen_Producto SET Imagen='$nombreImagen' WHERE IdProducto='$args[id]'";

    $stmt = $this->db->prepare($select);
    $data = $stmt->execute();

    return $response->withJson(array("insertId" => $args['id']));
});



$app->get('/movimiento/productos', function (Request $request, Response $response, array $args) {
    $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
    $hashMovimiento = $request->getParam('hash');

    $select = "SELECT DATE_FORMAT(Lo_Movimiento.FechaReg, '%d%m%Y') as Fecha, Lo_MovimientoDetalle.IdProducto, Gen_Producto.Producto, Gen_ProductoMarca.ProductoMarca, Gen_Producto.ProductoModelo,
        Gen_Producto.Color, Gen_Producto.CodigoBarra, Gen_Producto.PrecioContado, Lo_MovimientoDetalle.Cantidad,
        Gen_Producto.Botapie
        FROM Lo_MovimientoDetalle
        INNER JOIN Lo_Movimiento ON Lo_MovimientoDetalle.hashMovimiento = Lo_Movimiento.Hash
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
    $fechaVenCredito = $request->getParam('movimiento')['FechaVenCredito'] ? $request->getParam('movimiento')['FechaVenCredito'] : NULL;
    $fechaPeriodoTributario = $request->getParam('movimiento')['FechaPeriodoTributario'];
    $tipoCambio = $request->getParam('movimiento')['TipoCambio'] ? $request->getParam('movimiento')['TipoCambio'] : 1;
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
            $descripcion = isset($producto['Descripcion']) ? $producto['Descripcion'] : '';

            if ($tieneIgv === 1) {
                $precio += $precio * 0.18;
            }

            $insert = $this->db->insert(array('hashMovimiento', 'IdProducto', 'Cantidad', 'TieneIgv', 'Precio', 'IdLote', 'Descripcion'))
            ->into('Lo_MovimientoDetalle')
            ->values(array($hash, $idProducto, $cantidad, $tieneIgv, $precio, $idLote, $descripcion));
            $insert->execute();

            // start actualizar precioventa producto
            $alterarProductos = $request->getParam('movimiento')['alterarProductos'];
            if ($alterarProductos) {

                // if ($request->getParam('movimiento')['Moneda'] === 'USD') {
                //     $select = "SELECT * FROM Gen_Moneda WHERE Moneda='USD'";
                //     $stmt = $this->db->query($select);
                //     $stmt->execute();
                //     $moneda = $stmt->fetch();

                //     $precio *= $moneda['TipoCambio'];
                //     $nuevoPrecioContado *= $moneda['TipoCambio'];
                // }

                $precio *= $tipoCambio;
                // $nuevoPrecioContado *= $tipoCambio;

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

$app->get('/movimientos/granTotal', function (Request $request, Response $response) {

    $IdPreOrden = $request->getParam('idPreOrden');

    // $select = "SELECT IdPreOrden, SUM(Cantidad * Precio) AS granTotal FROM Ve_PreOrdenDet WHERE IdPreOrden = $IdPreOrden";
    $select = "SELECT IdPreOrden, SUM(Cantidad * Precio) AS granTotal FROM Ve_PreOrdenDet WHERE IdPreOrden = $IdPreOrden";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});



$app->get('/monedas', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('Gen_Moneda')->whereLike('Moneda', '%' . $request->getParam('q') . '%');
    $stmt = $select->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});

$app->get('/almacenes/id/{id}', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM Lo_Almacen WHERE Anulado=0 AND IdAlmacen=$args[id]";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});

$app->get('/almacenes', function (Request $request, Response $response, array $args) {
    // $select = $this->db->select()->from('Lo_Almacen');
    // $stmt = $select->execute();
    // $data = $stmt->fetchAll();

    // return $response->withJson($data);


    $select = "SELECT * FROM Lo_Almacen WHERE Anulado=0";

    $stmt = $this->db->query($select);
    $stmt->execute();
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

$app->get('/puntoventa/id/{id}', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM Ve_DocVentaPuntoVenta WHERE Anulado=0 AND IdDocVentaPuntoVenta=$args[id]";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});

$app->get('/puntoventa/primero', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM Ve_DocVentaPuntoVenta WHERE Anulado=0 ORDER BY IdDocVentaPuntoVenta ASC LIMIT 1 ";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});


$app->get('/consultarRUC', function (Request $request, Response $response, array $args) use ($app) {
    $res = $app->subRequest('GET', 'empresa/id/1');
    $empresa = (string) $res->getBody();
    $empresa = json_decode($empresa, true);
    if ($_GET['type'] == "RUC") {
        $ch = curl_init($empresa['CONSULTARUCURL'].'ruc/'.$_GET['numero'].'?token='.$empresa['CONSULTARUCTOKEN']);
    } else {
        $ch = curl_init($empresa['CONSULTARUCURL'].'dni/'.$_GET['numero'].'?token='.$empresa['CONSULTARUCTOKEN']);
    }

    $headers = array(
        "Content-Type: application/json; charset=UTF-8",
        "Cache-Control: no-cache",
        "Pragma: no-cache"
    );
    //var_dump("https://www.facturacionelectronica.us/facturacion/controller/ws_consulta_rucdni_v2.php?usuario=20573027125&password=5i573m45&documento=" . $_GET['type'] . "&nro_documento=" . $_GET['numero']);
    // $ch = curl_init("https://www.facturacionelectronica.us/facturacion/controller/ws_consulta_rucdni_v2.php?usuario=20573027125&password=5i573m45&documento=" . $_GET['type'] . "&nro_documento=" . $_GET['numero']);
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
    $select = "(SELECT Lo_Movimiento.MovimientoFecha AS Fecha,  Lo_MovimientoTipo.CodSunat, Lo_MovimientoTipo.TipoMovSunat as TipoDocSunat,
     Lo_MovimientoTipo.TipoMovimiento AS Detalle, Lo_Movimiento.Serie, Lo_Movimiento.Numero, Lo_Proveedor.Proveedor AS Nombres,
    Gen_Moneda.Simbolo AS Moneda,
    Lo_MovimientoDetalle.Cantidad AS IngresoCantidad, Lo_MovimientoDetalle.Precio AS IngresoPrecio,
    '0' AS SalidaCantidad, '0' AS SalidaPrecio,
    '0' AS Descuento FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        LEFT JOIN Lo_Proveedor ON Lo_Movimiento.IdProveedor = Lo_Proveedor.IdProveedor
        LEFT JOIN Gen_Moneda ON Lo_Movimiento.Moneda = Gen_Moneda.Moneda
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenDestino = $idAlmacen
            AND Lo_MovimientoDetalle.IdProducto=$idProducto AND Lo_Movimiento.Anulado=0
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";

    return $select;
}
function stringIngresoCajaProducto($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT Lo_Movimiento.MovimientoFecha AS Fecha, Lo_MovimientoTipo.CodSunat, Lo_MovimientoTipo.TipoMovSunat as TipoDocSunat,
    Lo_MovimientoTipo.TipoMovimiento AS Detalle, Lo_Movimiento.Serie, Lo_Movimiento.Numero, Lo_Proveedor.Proveedor AS Nombres, 
    Gen_Moneda.Simbolo AS Moneda,
    (Lo_MovimientoDetalle.Cantidad * Gen_ProductoDet.Cantidad) AS IngresoCantidad, Lo_MovimientoDetalle.Precio AS IngresoPrecio, 
    '0' AS SalidaCantidad, '0' AS SalidaPrecio, 
    '0' AS Descuento FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        INNER JOIN Gen_ProductoDet ON Lo_MovimientoDetalle.IdProducto = Gen_ProductoDet.IdProducto
        LEFT JOIN Lo_Proveedor ON Lo_Movimiento.IdProveedor = Lo_Proveedor.IdProveedor
        LEFT JOIN Gen_Moneda ON Lo_Movimiento.Moneda = Gen_Moneda.Moneda
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenDestino = $idAlmacen
            AND Gen_ProductoDet.IdProductoDet=$idProducto AND Lo_Movimiento.Anulado=0 
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";

    return $select;
}
function stringSalidaUndProducto($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT Lo_Movimiento.MovimientoFecha AS Fecha, Lo_MovimientoTipo.CodSunat, Lo_MovimientoTipo.TipoMovSunat as TipoDocSunat,
    Lo_MovimientoTipo.TipoMovimiento AS Detalle, Lo_Movimiento.Serie, Lo_Movimiento.Numero, Lo_Proveedor.Proveedor AS Nombres,
    'S/' AS Moneda,
    '0' AS IngresoCantidad, '0' AS IngresoPrecio,
    (CASE
        WHEN Lo_MovimientoTipo.CodSunat = '07' AND Lo_Movimiento.NotaIdMotivo NOT IN ('01', '02', '06', '07') THEN '0'
        ELSE Lo_MovimientoDetalle.Cantidad
    END) AS SalidaCantidad, 
    (CASE
        WHEN Lo_MovimientoTipo.CodSunat = '07' AND Lo_Movimiento.NotaIdMotivo NOT IN ('01', '02', '06', '07') THEN '0'
        ELSE Lo_MovimientoDetalle.Precio
    END) AS SalidaPrecio,
    '0' AS Descuento FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        LEFT JOIN Lo_Proveedor ON Lo_Movimiento.IdProveedor = Lo_Proveedor.IdProveedor
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenOrigen = $idAlmacen
            AND Lo_MovimientoDetalle.IdProducto=$idProducto AND Lo_Movimiento.Anulado=0
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";

    return $select;
}
function stringSalidaCajaProducto($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT Lo_Movimiento.MovimientoFecha AS Fecha, Lo_MovimientoTipo.CodSunat, Lo_MovimientoTipo.TipoMovSunat as TipoDocSunat,
    Lo_MovimientoTipo.TipoMovimiento AS Detalle, Lo_Movimiento.Serie, Lo_Movimiento.Numero, Lo_Proveedor.Proveedor AS Nombres,
    'S/' AS Moneda, 
    '0' AS IngresoCantidad, '0' AS IngresoPrecio, 
    (CASE
        WHEN Lo_MovimientoTipo.CodSunat = '07' AND Lo_Movimiento.NotaIdMotivo NOT IN ('01', '02', '06', '07') THEN '0' 
        ELSE (Lo_MovimientoDetalle.Cantidad * Gen_ProductoDet.Cantidad) 
    END) AS SalidaCantidad, 
    (CASE
        WHEN Lo_MovimientoTipo.CodSunat = '07' AND Lo_Movimiento.NotaIdMotivo NOT IN ('01', '02', '06', '07') THEN '0' 
        ELSE Lo_MovimientoDetalle.Precio 
    END) AS SalidaPrecio, 
    '0' AS Descuento FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        INNER JOIN Gen_ProductoDet ON Lo_MovimientoDetalle.IdProducto = Gen_ProductoDet.IdProducto
        LEFT JOIN Lo_Proveedor ON Lo_Movimiento.IdProveedor = Lo_Proveedor.IdProveedor
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenOrigen = $idAlmacen
            AND Gen_ProductoDet.IdProductoDet=$idProducto AND Lo_Movimiento.Anulado=0 
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')";

    return $select;
}
function stringSalidaVentaUndProducto($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT Ve_DocVenta.FechaDoc AS Fecha, Ve_DocVentaTipoDoc.CodSunat, Ve_DocVentaTipoDoc.TipoDocSunat,
    CONCAT('VENTA - ', Ve_DocVentaTipoDoc.TipoDoc) AS Detalle, Ve_DocVenta.Serie, Ve_DocVenta.Numero,
    Ve_DocVentaCliente.Cliente AS Nombres,
    'S/' AS Moneda,
    (CASE
        WHEN Ve_DocVentaTipoDoc.CodSunat = '07' AND NotaIdMotivo IN ('01', '02', '06', '07') THEN Ve_DocVentaDet.Cantidad
        ELSE '0'
    END) AS IngresoCantidad, 
    (CASE
        WHEN Ve_DocVentaTipoDoc.CodSunat = '07' AND NotaIdMotivo IN ('01', '02', '06', '07') THEN Ve_DocVentaDet.Precio
        ELSE '0'
    END) AS IngresoPrecio, 
    (CASE
        WHEN Ve_DocVentaTipoDoc.CodSunat = '07' THEN '0'
        ELSE Ve_DocVentaDet.Cantidad
    END) AS SalidaCantidad, 
    (CASE
        WHEN Ve_DocVentaTipoDoc.CodSunat = '07' THEN '0'
        ELSE Ve_DocVentaDet.Precio
    END) AS SalidaPrecio, 
    Ve_DocVentaDet.Descuento FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
        LEFT JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente= Ve_DocVentaCliente.IdCliente
        WHERE Ve_DocVenta.IdAlmacen = $idAlmacen
            AND Ve_DocVentaDet.IdProducto = $idProducto
            AND Ve_DocVenta.Anulado = 0
            AND Ve_DocVentaTipoDoc.VaRegVenta = 1
            AND Ve_DocVenta.FechaDoc < '$fechaHasta')";

    return $select;
}

function stringSalidaVentaCajaProducto($idProducto, $idAlmacen, $fechaHasta) {
    $select = "(SELECT Ve_DocVenta.FechaDoc AS Fecha, Ve_DocVentaTipoDoc.CodSunat, Ve_DocVentaTipoDoc.TipoDocSunat,
    CONCAT('VENTA - ', Ve_DocVentaTipoDoc.TipoDoc) AS Detalle, Ve_DocVenta.Serie, Ve_DocVenta.Numero, 
    Ve_DocVentaCliente.Cliente AS Nombres, 
    'S/' AS Moneda,
    (CASE
    WHEN Ve_DocVentaTipoDoc.CodSunat = '07' AND NotaIdMotivo IN ('01', '02', '06', '07') THEN (Gen_ProductoDet.cantidad * Ve_DocVentaDet.Cantidad)
    ELSE '0'
    END) AS IngresoCantidad, 
    (CASE
        WHEN Ve_DocVentaTipoDoc.CodSunat = '07' AND NotaIdMotivo IN ('01', '02', '06', '07') THEN Ve_DocVentaDet.Precio
        ELSE '0'
    END) AS IngresoPrecio, 
    (CASE
        WHEN Ve_DocVentaTipoDoc.CodSunat = '07' THEN '0'
        ELSE (Gen_ProductoDet.cantidad * Ve_DocVentaDet.Cantidad)
    END) AS SalidaCantidad, 
    (CASE
        WHEN Ve_DocVentaTipoDoc.CodSunat = '07' THEN '0'
        ELSE Ve_DocVentaDet.Precio
    END) AS SalidaPrecio,  
    Ve_DocVentaDet.Descuento FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
        LEFT JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente= Ve_DocVentaCliente.IdCliente
        INNER JOIN Gen_ProductoDet ON Ve_DocVentaDet.IdProducto=Gen_ProductoDet.IdProducto
        WHERE Ve_DocVenta.IdAlmacen = $idAlmacen
            AND Gen_ProductoDet.IdProductoDet = $idProducto 
            AND Ve_DocVenta.Anulado = 0
            AND Ve_DocVentaTipoDoc.VaRegVenta = 1
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
        $select = "(SELECT Gen_ProductoDet.IdProductoDet AS IdProducto, IFNULL(SUM(IF(Lo_MovimientoTipo.CodSunat = '07', IF(Lo_Movimiento.NotaIdMotivo IN ('01', '02', '06', '07'),Gen_ProductoDet.cantidad * Lo_MovimientoDetalle.Cantidad,0), Gen_ProductoDet.cantidad * Lo_MovimientoDetalle.Cantidad)), 0) AS cantidad
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
        $select = "(SELECT Lo_MovimientoDetalle.IdProducto, IFNULL(SUM(IF(Lo_MovimientoTipo.CodSunat = '07', IF(Lo_Movimiento.NotaIdMotivo IN ('01', '02', '06', '07'),Lo_MovimientoDetalle.Cantidad,0) ,Lo_MovimientoDetalle.Cantidad)), 0) AS cantidad FROM Lo_Movimiento
            INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
            INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
            WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenOrigen = $idAlmacen
                AND Lo_Movimiento.Anulado=0
                AND Lo_Movimiento.MovimientoFecha < '$fechaHasta'
            GROUP BY Lo_MovimientoDetalle.IdProducto)";
    }

    return $select;
}

function stringSalidaVentaUnd($idProducto, $idAlmacen, $fechaHasta, $fechaDesde = false) {
    $select = "(SELECT IFNULL(SUM(Ve_DocVentaDet.Cantidad), 0) AS cantidad FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
        WHERE Ve_DocVenta.IdAlmacen = $idAlmacen
            AND Ve_DocVentaDet.IdProducto = $idProducto
            AND Ve_DocVenta.Anulado = 0
            AND Ve_DocVentaTipoDoc.VaRegVenta = 1
            AND Ve_DocVenta.FechaDoc < '$fechaHasta')";

    if (!$idProducto) {
        $select = "(SELECT Ve_DocVentaDet.IdProducto,  IFNULL(SUM(IF(Ve_DocVentaTipoDoc.CodSunat = '07', IF(Ve_DocVenta.NotaIdMotivo IN ('01', '02', '06', '07'),-1*Ve_DocVentaDet.Cantidad,0),Ve_DocVentaDet.Cantidad)), 0) AS cantidad FROM Ve_DocVenta
            INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
            INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
            WHERE Ve_DocVenta.IdAlmacen = $idAlmacen
                AND Ve_DocVenta.Anulado = 0
                AND Ve_DocVentaTipoDoc.VaRegVenta = 1 " .
                ($fechaDesde ? " AND Ve_DocVenta.FechaDoc > '$fechaDesde' " : "")
                . " AND Ve_DocVenta.FechaDoc < '$fechaHasta'
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
        $select = "(SELECT Gen_ProductoDet.IdProductoDet AS IdProducto, IFNULL(SUM(IF(Ve_DocVentaTipoDoc.CodSunat = '07', IF(Ve_DocVenta.NotaIdMotivo IN ('01', '02', '06', '07'),-1*Gen_ProductoDet.cantidad * Ve_DocVentaDet.Cantidad,0), Gen_ProductoDet.cantidad * Ve_DocVentaDet.Cantidad)), 0) AS cantidad
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

function stringIngresoUndCount($idProducto, $idAlmacen, $fechaHasta, $fechaDesde = false) {
    /* $select = "(SELECT IFNULL(SUM(Lo_MovimientoDetalle.Cantidad), 0) AS cantidad FROM Lo_Movimiento
        INNER JOIN Lo_MovimientoDetalle On Lo_Movimiento.`Hash`=Lo_MovimientoDetalle.hashMovimiento
        INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
        WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenDestino = $idAlmacen
            AND Lo_MovimientoDetalle.IdProducto=$idProducto AND Lo_Movimiento.Anulado=0
            AND Lo_Movimiento.MovimientoFecha < '$fechaHasta')"; */

    if (!$idProducto) {
        $select = "(SELECT Lo_MovimientoDetalle.IdProducto, COUNT(Lo_Movimiento.Hash) AS cantidad FROM Lo_Movimiento
            INNER JOIN Lo_MovimientoDetalle ON Lo_Movimiento.Hash = Lo_MovimientoDetalle.hashMovimiento
            INNER JOIN Lo_MovimientoTipo ON Lo_Movimiento.IdMovimientoTipo = Lo_MovimientoTipo.IdMovimientoTipo
            WHERE Lo_MovimientoTipo.VaRegCompra = 1 AND Lo_Movimiento.IdAlmacenDestino = $idAlmacen
                AND Lo_Movimiento.Anulado=0 " .
                ($fechaDesde ? " AND Lo_Movimiento.MovimientoFecha > '$fechaDesde' " : "")
                . " AND Lo_Movimiento.MovimientoFecha < '$fechaHasta'
            GROUP BY Lo_MovimientoDetalle.IdProducto)";
    }

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



$app->get('/productos/stock[/{idAlmacen}]', function (Request $request, Response $response, array $args) {

    $idAlmacen = $request->getParam('idAlmacen');
    $fechaHasta = $request->getParam('fechaHasta') ? $request->getParam('fechaHasta') : getNow();

    if(isset($args['idAlmacen'])) {
        $idAlmacen = $args['idAlmacen'];
    }

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
    }  else if ($request->getParam('sumaValorizadoSin')) {
        $select = "SELECT
            SUM(Gen_Producto.PrecioCosto * (IFNULL(IngresoUnd.cantidad, 0) + IFNULL(IngresoCaja.cantidad, 0)  - IFNULL(SalidaCaja.cantidad, 0) - IFNULL(SalidaUnd.cantidad, 0) - IFNULL(SalidaVentaUnd.cantidad,0) - IFNULL(SalidaVentaCaja.cantidad,0))) AS sumaValorizadoSin
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
            $select .= " WHERE Gen_Producto.EsPadre = 0 AND (Gen_Producto.Producto LIKE '%" . (isset($filter['producto']) ? addslashes($filter['producto']) : '') . 
                       "%' AND Gen_Producto.CodigoBarra LIKE '" . (isset($filter['codigo']) ? addslashes($filter['codigo']) : '') . 
                       "%' ANd Gen_ProductoMarca.ProductoMarca LIKE '" . (isset($filter['marca']) ? addslashes($filter['marca']) : '') . 
                       "%' ANd Gen_Producto.ProductoModelo LIKE '" . (isset($filter['modelo']) ? addslashes($filter['modelo']) : '') . 
                       "%' AND Gen_ProductoCategoria.ProductoCategoria LIKE '%" . (isset($filter['categoria']) ? addslashes($filter['categoria']) : '') . 
                       "%'  ) "; 
            
        } else {
            $select .= " WHERE Gen_Producto.EsPadre = 0 AND (Gen_Producto.Producto LIKE '%" . $filter . 
                       "%' OR Gen_Producto.CodigoBarra LIKE '%" . $filter . 
                       "%' OR Gen_Producto.Color LIKE '%" . $filter . 
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

    $select .= "AND (Gen_Producto.ControlaStock=1 OR Gen_Producto.CodigoBarra='MANODEOBRA')";

    if(isset($request->getparam('filter')['minimo']) && !$request->getParam('sumaStock') && !$request->getParam('sumaValorizado') && !$request->getParam('sumaValorizadoSin')) {
        $filterMinimo = $request->getParam('filter')['minimo'];
        if ($filterMinimo) {
            $select .= " HAVING stock <= Gen_Producto.StockMinimo";
        }
    } else {
        if (isset($request->getParam('filter')['stock']) && !$request->getParam('sumaStock') && !$request->getParam('sumaValorizado') && !$request->getParam('sumaValorizadoSin')) {
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
    $this->db->query('SET SQL_BIG_SELECTS=1');
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


    $select = "SELECT * FROM Ve_DocVentaTipoDoc 
        WHERE TipoDoc LIKE '%" . $request->getParam('q') . "%' 
        AND Oculto=0 
        ORDER BY Orden ASC";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});

$app->get('/ventas/solonumero', function (Request $request, Response $response, array $args) {
    $serie = $request->getParam('serie');
    $idTipoDoc = $request->getParam('idTipoDoc');

    // Obtener siguiente Numero
    $selectNumero = "SELECT Numero + 1 AS NuevoNumero FROM Ve_DocVenta 
        WHERE IdTipoDoc=$idTipoDoc AND Serie='$serie'
        ORDER BY Numero DESC LIMIT 1";
    $stmt = $this->db->query($selectNumero);
    $stmt->execute();
    $selectNumero = $stmt->fetch();
    $numero = $selectNumero['NuevoNumero'];
    return $response->withJson(array(
        "numero" => $numero ? $numero : 1
    ));
});

$app->get('/ventas/lista/[{idPuntoVenta}]', function (Request $request, Response $response) { 
    $idPuntoVenta =  $request->getAttribute('idPuntoVenta', 0);
    $select = "SELECT Ve_DocVentaTipoDoc.CodSunat, CONCAT(Ve_DocVenta.Serie, '-', Ve_DocVenta.Numero) AS NroComprobante, Ve_DocVenta.idDocVenta AS IdDocVenta FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc";
    $select .= " WHERE Ve_DocVentaTipoDoc.CodSunat IN ('01', '03') AND Ve_DocVenta.Anulado=0 AND  Ve_DocVenta.IdDocVentaPuntoVenta = $idPuntoVenta AND
    CONCAT(Ve_DocVenta.Serie, '-', Ve_DocVenta.Numero) LIKE '%" . $request->getParam('q') . "%'  ";
    $select .= " LIMIT 10";

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

$app->get('/ventas/comision', function (Request $request, Response $response, array $args) {
    $select = $this->db->select()->from('GEN_EMPRESA')->where('IDEMPRESA', '=', 1);
    $stmt = $select->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});

$app->post('/ventas/comision', function (Request $request, Response $response) {
    $id = 1;
    $valorComision = $request->getParam('valorComision');
    
    $update = "UPDATE GEN_EMPRESA SET valorComision=$valorComision 
        WHERE IDEMPRESA=$id";
    
    $stmt = $this->db->prepare($update);
    $updated = $stmt->execute();

    return $response->withJson(array(
        "IDEMPRESA" => $id,
        "valorComision" => $valorComision
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

$app->post('/ventas/entregar', function (Request $request, Response $response) {
    $idDocVenta = $request->getParam('idDocVenta');

    $sql = "UPDATE Ve_DocVenta SET PorEntregar=0 WHERE idDocVenta='$idDocVenta' ";
        
    $stmt = $this->db->prepare($sql);
    $updated = $stmt->execute();
    
    return $response->withJson(array(
        "updated" => $updated,
        "IdDocVenta" => $idDocVenta
    ));
});

$app->get('/venta/detalle/comprobante', function (Request $request, Response $response) {
    $nroComprobante = $request->getParam('NroComprobante');
    $idDocVenta = $request->getParam('idDocVenta');
    $IdDocVentaPuntoVenta = $request->getParam('IdDocVentaPuntoVenta');
    $comprobante = explode('-', $nroComprobante );
    $serie = $comprobante[0];
    $numero = $comprobante[1];

    $select = "SELECT Ve_DocVentaDet.IdDocVentaDet,Ve_DocVentaDet.IdDocVenta,Ve_DocVentaDet.IdProducto,
    Ve_DocVentaDet.Cantidad AS cantidad,Ve_DocVentaDet.Cantidad AS cantxPrecio,'1' AS cantidadPres, 
    Ve_DocVentaDet.Precio AS precio, Ve_DocVentaDet.Precio AS PrecioCosto,Ve_DocVentaDet.Descuento AS descuento,
    false AS estadoPxP,Gen_Producto.Producto, 0 AS PorcentajeUtilidad,Ve_DocVenta.IdCliente,Gen_Producto.PrecioContado,
    Gen_Producto.precioConvenio,Gen_Producto.PrecioEspecial,Gen_ProductoMedicion.ProductoMedicion,Gen_ProductoMarca.ProductoMarca,
    Gen_Producto.PreciosPorProducto
    FROM Ve_DocVentaDet 
    INNER JOIN Ve_DocVenta ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.IdDocVenta
    INNER JOIN Gen_Producto ON Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto 
    INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
    INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
    WHERE Ve_DocVenta.idDocVenta=$idDocVenta ORDER BY Ve_DocVentaDet.IdDocVentaDet DESC ";
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});

$app->get('/ventas/detalle/entregar', function (Request $request, Response $response) {
    $idDocVentas = $request->getParam('idDocVenta');

    $select = "SELECT 
    vdet.IdDocVenta AS IdDocVenta,
    vdet.IdDocVentaDet AS IdDocVentaDet,
    vdet.Cantidad AS Cantidad,
    vdet.Precio AS Precio,
    prod.Producto AS Producto,
    ROUND(vdet.Cantidad - IFNULL((
        SELECT SUM(Cantidad) FROM Ve_DocVentaDetEntrega 
        where Ve_DocVentaDetEntrega.IdDocVentaDet = vdet.IdDocVentaDet
    ), 0), 2) AS entregar
    FROM Ve_DocVentaDet AS vdet 
    INNER JOIN Gen_Producto AS prod ON
    prod.IdProducto = vdet.IdProducto 
    WHERE IdDocVenta='$idDocVentas'";


    // $select = "SELECT 
    // vdet.IdDocVenta AS IdDocVenta,
    // vdet.IdDocVentaDet AS IdDocVentaDet,
    // vdet.Cantidad AS Cantidad,
    // vdet.Precio AS Precio,
    // prod.Producto AS Producto,
    // ROUND(vdet.Cantidad - IFNULL((
    //     SELECT SUM(Cantidad) FROM Ve_DocVentaDetEntrega 
    //     where Ve_DocVentaDetEntrega.IdDocVentaDet = vdet.IdDocVentaDet
    // ), 0), 2) AS entregar,
    // vdetEnt.Fecha AS Fecha 
    // FROM 
    // Ve_DocVentaDet AS vdet INNER JOIN Gen_Producto AS prod 
    // ON prod.IdProducto = vdet.IdProducto INNER JOIN Ve_DocVentaDetEntrega AS vdetEnt 
    // ON vdetEnt.IdDocVentaDet = vdet.IdDocVentaDet 
    // WHERE IdDocVenta='$idDocVentas'";


    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);

});

$app->post('/entregar', function (Request $request, Response $response) {

    date_default_timezone_set('America/Lima');
    $productos = $request->getParam('productos');
    
    foreach($productos as $producto) {
        if ($producto['cantEntregar']) {
        
            $idDocVentaDet  = $producto['IdDocVentaDet'];
            $cantidad       = $producto['cantEntregar'];
            $fecha          = date("Y-m-d h:i:s");

            $insert = $this->db->insert(
                array(
                    'IdDocVentaDet',
                    'Cantidad',
                    'Fecha'
                )
            )->into(
                'Ve_DocVentaDetEntrega'
            )->values(
                array(
                    $idDocVentaDet,
                    $cantidad,
                    $fecha
                )
            );
            $insert->execute();
        }
    }
    
    return $response->withJson(array("productoEntregado" => $productos, "Fecha" => $fecha));

});

$app->get('/entregar', function (Request $request, Response $response) {

    $idDocVentas = $request->getParam('idDocVenta');

    $select = "SELECT 
    vdetent.IdDocVentaDetEntrega AS IdDocVentaDetEntrega,
    prod.Producto AS Producto,
    vdetent.Cantidad AS Cantidad,
    vdetent.Fecha AS Fecha 
    FROM Ve_DocVentaDetEntrega AS vdetent
    
    INNER JOIN Ve_DocVentaDet AS vdet ON 
    vdetent.IdDocVentaDet = vdet.IdDocVentaDet 
    INNER JOIN Gen_Producto AS prod ON 
    prod.IdProducto = vdet.IdProducto 
    WHERE IdDocVenta='$idDocVentas'
    ORDER BY vdetent.Fecha DESC";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);    

});


$app->get('/ventas/detalle', function (Request $request, Response $response) {
    $idDocVentas = $request->getParam('idDocVentas');

    $select = "SELECT Ve_DocVentaDet.*, Gen_Producto.Producto,
        ROUND(Ve_DocVentaDet.Descuento, 2) AS Descuento,
        ROUND((Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio) - Ve_DocVentaDet.Descuento, 2) AS Subtotal,
        ROUND(Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio, 2) AS Total,
        Gen_Producto.CodigoBarra, Gen_ProductoMedicion.Codigo AS CodigoMedicion,Gen_ProductoMedicion.ProductoMedicion
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
        Ve_DocVenta.Anulado, 
        CONCAT(Ve_DocVenta.Serie, '-', Ve_DocVenta.Numero) as Correlativo,
        Ve_DocVenta.Serie, Ve_DocVenta.Numero, Ve_DocVentaCliente.Cliente, Ve_DocVenta.UsuarioReg,
        Ve_DocVentaTipoDoc.CodSunat, Ve_DocVentaCliente.DniRuc, Ve_DocVentaCliente.DniRuc, Ve_DocVentaCliente.Direccion,
        Ve_DocVentaTipoDoc.CodigoIgv, Ve_DocVenta.Estado, Ve_DocVenta.Hash_cpe, Ve_DocVenta.Hash_cdr, Ve_DocVenta.Msj_sunat,
        Ve_DocVenta.CodSunatModifica,
        Ve_DocVenta.NroComprobanteModifica,
        Ve_DocVenta.NotaIdMotivo,
        Ve_DocVenta.NotaDescMotivo,
        Ve_DocVenta.EsCredito, Ve_DocVenta.FechaCredito,
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
    if (!isset($filter['fechaInicio']) && is_array($filter)) {
        //$filter['fechaInicio'] = getNow('Y') . '-01-01';
        $filter['fechaInicio'] = getNow('Y-m-d');
    }

    if (!isset($filter['fechaFin']) && is_array($filter)) {
        $filter['fechaFin'] = getNow('Y-m-d');
    }

    $noFecha = $request->getParam('noFecha');
    if ($noFecha == 1 && isset($filter['fechaInicio']) && isset($filter['fechaFin'])) {
        unset($filter['fechaInicio']);
        unset($filter['fechaFin']);
    }

    if ($filter) {
        if(is_array($filter)) {

            if (isset($filter['vendedor']) && $filter['vendedor']) $filtros .= " AND Ve_DocVenta.UsuarioReg = '" . $filter['vendedor'] . "'";
            if (isset($filter['cliente']) && $filter['cliente']) $filtros .= " AND Ve_DocVentaCliente.Cliente = '" . $filter['cliente'] . "'";
            if (isset($filter['declarado'])) $filtros .= " AND Ve_DocVentaTipoDoc.VaRegVenta = " . $filter['declarado'];
            if (isset($filter['fechaInicio']) && isset($filter['fechaFin'])) $filtros .= " AND Ve_DocVenta.FechaDoc BETWEEN CAST('" . $filter['fechaInicio'] . "' AS DATETIME) AND CONCAT('" . $filter['fechaFin'] . "',' 23:59:59')";
            
        } else {
            $select .= " AND (Ve_DocVenta.idDocVenta LIKE '%" . $filter . 
                       "%' OR CONCAT(Ve_DocVenta.Serie, '-', Ve_DocVenta.Numero) LIKE '%" . $filter . 
                       "%' OR Ve_DocVentaTipoDoc.TipoDoc LIKE '%" . $filter . 
                       "%' OR Ve_DocVentaCliente.Cliente LIKE '%" . $filter . 
                       "%' OR Ve_DocVentaCliente.DniRuc LIKE '%" . $filter . 
                       "%' )";  
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

    $porEntregar = $request->getParam('porEntregar');
    if (!empty($porEntregar)) {
        $filtros .= " AND Ve_DocVenta.PorEntregar=1";
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
    //$select = "SELECT COUNT(*) as total FROM Ve_DocVenta WHERE Ve_DocVenta.IdAlmacen = $idAlmacen";
    $select = "SELECT COUNT(*) as total FROM Ve_DocVenta";

    if ($idAlmacen) {
        $select .= " WHERE Ve_DocVenta.IdAlmacen = $idAlmacen";
    }

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $response->withJson($data);
});




function obtenerStock($idAlmacen, $idProductos) {

    $fechaHasta = getNow();

    $strIngresoUnd = stringIngresoUnd(false, $idAlmacen, $fechaHasta);
    $strSalidaUnd = stringSalidaUnd(false, $idAlmacen, $fechaHasta);

    $strIngresoCaja = stringIngresoCaja(false, $idAlmacen, $fechaHasta);
    $strSalidaCaja = stringSalidaCaja(false, $idAlmacen, $fechaHasta);

    $strSalidaVentaUnd = stringSalidaVentaUnd(false, $idAlmacen, $fechaHasta);
    $strSalidaVentaCaja = stringSalidaVentaCaja(false, $idAlmacen, $fechaHasta);

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

    $select .= " WHERE Gen_Producto.IdProducto IN (" . implode(',', $idProductos) . ") ORDER BY Gen_Producto.IdProducto ASC";

    return $select;
}


$app->post('/ventas', function (Request $request, Response $response) {
    $vendedor = 'xx';
    $idComisionista = 0 ;
    if(isset($_SESSION['User'])) {
        $vendedor = $_SESSION['User'];
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
    $pagoCon = $request->getParam('PagoCon')=='' || $request->getParam('PagoCon')==null?0:$request->getParam('PagoCon');
    $codSunatModifica = $request->getParam('CodSunatModifica');
    $nroComprobanteModifica = $request->getParam('NroComprobanteModifica');
    $notaIdMotivo = $request->getParam('NotaIdMotivo');
    $notaDescMotivo = $request->getParam('NotaDescMotivo');
    $esCredito = $request->getParam('EsCredito');
    $porEntregar = $request->getParam('PorEntregar') ? $request->getParam('PorEntregar') : 0;
    $fechaCredito = $request->getParam('FechaCredito');
    $idPreOrden = $request->getParam('IdPreOrden');
    $campoDireccion = $request->getParam('CampoDireccion');

    $idComisionista = isset($request->getParam('comisionista')['IdCliente']) ? $request->getParam('comisionista')['IdCliente'] : $idComisionista;
    // $valorComision = $request->getParam('valorComision'); 
    if ($idComisionista <> 0) {
        $selectValorComision = "SELECT valorComision FROM GEN_EMPRESA 
            WHERE IDEMPRESA=1 LIMIT 1";
            $stmt = $this->db->query($selectValorComision);
            $stmt->execute();
            $selectValorComision = $stmt->fetch();
    }
    $valorComision = $selectValorComision['valorComision'] ? $selectValorComision['valorComision'] : 0;


    //VALIDAR STOCK
    $stockCorrecto = 0;
    $idProductos = $request->getParam('idProductos');
    $productosVenta = $request->getParam('productos');
    $stmt2 = $this->db->query(obtenerStock($idAlmacen, $idProductos));
    $stmt2->execute();
    $productosStock = $stmt2->fetchAll();

    foreach ($productosStock as $key => $prostock) {
        
        foreach ($productosVenta as $key => $proventa) {
          
            if($prostock['IdProducto']==$proventa['IdProducto']){

                if($proventa['cantidad'] > $prostock['stock']){
                    $stockCorrecto++;
                }

            }
        }
    }

/*     if($stockCorrecto > 0 && $codSunat != '07'){
        return $response->withJson(array("actualizar"=>true,"msg"=>"El stock de un producto ya a sido vendido, verifique por favor"));
    } */
    //FIN VALIDAR STOCK

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

        if($codSunatModifica =='01'  ){
            $serie = str_replace("_","F",$serie);
        }

        if($codSunatModifica =='03'  ){
            $serie = str_replace("_","B",$serie);
        }

        //$serie = $request->getParam('Serie'); 

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


    $insert = "INSERT INTO Ve_DocVenta (IdDocVentaPuntoVenta,IdCliente,IdTipoDoc,IdAlmacen,Serie,Numero,FechaDoc,Anulado,FechaReg,UsuarioReg,Hash, EsCredito, FechaCredito, PagoCon, CampoDireccion, valorComision, IdComisionista, PorEntregar, CodSunatModifica, NroComprobanteModifica, NotaIdMotivo, NotaDescMotivo)
        VALUES ($idDocVentaPuntoVenta, $idCliente, $idTipoDoc, $idAlmacen, '$serie', '$numero', '" . getNow() . "', $anulado, '" . getNow() . "', '$usuarioReg', UNIX_TIMESTAMP(), $esCredito, '$fechaCredito', '$pagoCon', '$campoDireccion', $valorComision, $idComisionista, '$porEntregar','$codSunatModifica', '$nroComprobanteModifica', '$notaIdMotivo', '$notaDescMotivo')";

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
            $descuento = $producto['descuento']?$producto['descuento']:0;
            $fechaAlquilerInicio = isset($producto['FechaAlquilerInicio']) ? $producto['FechaAlquilerInicio'] : (isset($producto['FechaAlquiler']) ? $producto['FechaAlquiler'] : null);
            $fechaAlquilerFin = isset($producto['FechaAlquilerFin']) ? $producto['FechaAlquilerFin'] : getNow();
            $descripcion = isset($producto['Descripcion']) ? $producto['Descripcion'] : '';
            $esManoDeObra = isset($producto['EsManoDeObra']) ? $producto['EsManoDeObra'] : 0;


            $insert = $this->db->insert(array('IdDocVenta', 'IdProducto', 'Cantidad', 'Precio', 'Descuento', 'FechaAlquilerFin', 'Descripcion', 'FechaAlquilerInicio', 'EsManoDeObra'))
                ->into('Ve_DocVentaDet')
                ->values(array($idDocVenta, $idProducto, $cantidad, $precio, $descuento, $fechaAlquilerFin, $descripcion, $fechaAlquilerInicio, $esManoDeObra ));

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

$app->post('/proformas', function (Request $request, Response $response) {
    $idCliente = $request->getParam('cliente')['IdCliente'];
    $usuarioReg = isset($request->getParam('vendedor')['Usuario']) ? $request->getParam('vendedor')['Usuario'] : 'xx';

    // Verificar si existe la proforma
    $select = "SELECT Numero, Anio FROM Ve_Proforma WHERE Anio='" . getNow("Y") . "' ORDER BY Numero DESC LIMIT 1";
    $stmt = $this->db->query($select);
    $stmt->execute();
    $proforma = $stmt->fetch(); 

	$numero = $proforma['Numero'];
	$anio = $proforma['Anio'];

	$newNumero = 1;
	$newAnio = getNow("Y");

	if ($numero && $anio) {
		$newNumero = $numero + 1;
    }
    
    // START INSERTAR NUEVA PROFORMA
	$insert = "INSERT INTO Ve_Proforma(Anio, Numero, IdCliente, FechaReg, UsuarioReg) VALUES($newAnio, $newNumero, $idCliente, '" . getNow() . "', '$usuarioReg')";
    
    $stmt = $this->db->prepare($insert);
    $inserted = $stmt->execute();
    $idProforma = $this->db->lastInsertId();
    // END INSERTAR NUEVA PROFORMA


    // START INSERTAR PROFORMA DETALLE
    $productos = $request->getParam('productos');
    //$descuentoTotal = 0;
    foreach($productos as $producto) {
        if ($producto['total'] > 0) {
            $idProducto     = $producto['IdProducto'];
            $descripcion    = $producto['Descripcion'];
            $cantidad       = $producto['cantidad'];
            $precio         = $producto['precio'];
            $descuento      = $producto['descuento'];
            $disabledTotal  = $producto['disabledProf'];
            
            $insert = $this->db->insert(array('IdProforma', 'IdProducto', 'DisabledTotal', 'Descripcion', 'Cantidad', 'Precio', 'Descuento'))
                ->into('Ve_ProformaDet')
                ->values(array($idProforma, $idProducto, $disabledTotal, $descripcion, $cantidad, $precio, $descuento));
            
            $insert->execute();
            //$descuentoTotal += $descuento;
        }
    }
    // END PROFORMA DETALLE

    $data = array(
        'insertId' => $idProforma
    );
    
    return $response->withJson($data);
});

$app->get('/proformas/id/{id}', function (Request $request, Response $response, array $args) {
    $select = "SELECT Ve_Proforma.*, Ve_DocVentaCliente.Cliente, Ve_DocVentaCliente.Direccion AS ClienteDireccion, Ve_DocVentaCliente.Email AS ClienteEmail
    
        FROM Ve_Proforma
        INNER JOIN Ve_DocVentaCliente ON Ve_Proforma.IdCliente = Ve_DocVentaCliente.IdCliente
        WHERE Ve_Proforma.IdProforma = '" . $args['id'] . "' ";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $proforma = $stmt->fetch();

    $select = "SELECT Ve_ProformaDet.IdProforma, Ve_ProformaDet.DisabledTotal, Ve_ProformaDet.Cantidad, Ve_ProformaDet.Precio, Ve_ProformaDet.Descuento, Gen_Producto.* FROM Ve_ProformaDet
    INNER JOIN Gen_Producto ON Ve_ProformaDet.IdProducto = Gen_Producto.IdProducto
    WHERE Ve_ProformaDet.IdProforma = '" . $args['id'] . "'";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $proformaDet = $stmt->fetchAll();    

    $proforma['productos'] = $proformaDet;
    
    return $response->withJson($proforma);
});


$app->get('/proformas/productos/{id}', function (Request $request, Response $response, array $args) {
    $select = "SELECT Ve_ProformaDet.IdProforma, Ve_ProformaDet.Cantidad, Ve_ProformaDet.Precio, Gen_Producto.* FROM Ve_ProformaDet
    INNER JOIN Gen_Producto ON Ve_ProformaDet.IdProducto = Gen_Producto.IdProducto
    WHERE Ve_ProformaDet.IdProforma = '" . $args['id'] . "'";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});


$app->get('/proformas', function (Request $request, Response $response) {
    $filter = $request->getParam('filter');
    $select = "SELECT Ve_Proforma.IdProforma, Ve_Proforma.IdCliente, Ve_Proforma.Anio, Ve_Proforma.Numero, Ve_Proforma.FechaReg, Ve_Proforma.UsuarioReg,  Ve_DocVentaCliente.Cliente,
        Ve_DocVentaCliente.DniRuc
        FROM Ve_Proforma
        LEFT JOIN Ve_DocVentaCliente ON Ve_Proforma.IdCliente = Ve_DocVentaCliente.IdCliente
        WHERE Ve_Proforma.Anulado = 0
        AND (Ve_DocVentaCliente.Cliente LIKE '%$filter%'
        OR Ve_DocVentaCliente.DniRuc LIKE '%$filter%'
        OR Ve_Proforma.Anio LIKE '%$filter%'
        OR Ve_Proforma.Numero LIKE '%$filter%'
        OR Ve_Proforma.FechaReg LIKE '%$filter%'
        OR Ve_Proforma.UsuarioReg LIKE '%$filter%') ";

    $select .= " ORDER BY Ve_Proforma.IdProforma DESC";

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


$app->get('/proformas/count', function (Request $request, Response $response, array $args) {
    $select = "SELECT COUNT(*) as total FROM Ve_Proforma";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});

$app->get('/proformas/detalle', function (Request $request, Response $response) {
    $idProforma = $request->getParam('idProforma');

    $select = "SELECT * FROM Ve_ProformaDet WHERE IdProforma=$idProforma";
    // $select = "SELECT Ve_ProformaDet.*, Gen_Producto.Producto FROM Ve_ProformaDet 
    //     INNER JOIN Gen_Producto ON Ve_ProformaDet.IdProducto = Gen_Producto.IdProducto     
    //     WHERE IdProforma=$idProforma ";
    // $select = "SELECT * FROM Ve_ProformaDet AS proDet JOIN Ve_Proforma AS pro WHERE IdProforma=$idProforma";
    
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
    
});

$app->post('/preorden/cajaybanco', function (Request $request, Response $response, array $args) {
    $idPreOrden = $request->getParam('IdPreOrden');
    $idHabitacion = $request->getParam('IdHabitacion');
    $adelanto = $request->getParam('Adelanto');
    $producto = $request->getParam('producto');
    $concepto = $request->getParam('concepto');
    $usuario = $request->getParam('Usuario');

    $vendedor = 'xx';
    if(isset($_SESSION['User'])) {
        $vendedor = $_SESSION['User'];
    }
    $usuarioReg = isset($usuario) ? $request->getParam('Usuario') : $vendedor;

    if ($idPreOrden) {
        $insert = "INSERT INTO Cb_CajaBanco (IdTipoCajaBanco, IdCuenta, FechaDoc, Concepto, Importe, Anulado, IdProveedor, IdCliente, EsDelVendedor, IdPreOrden, UsuarioReg) VALUES (2, 1, '" . getNow() . "', '$concepto', $adelanto, 0, 0, $producto[IdCliente], 1, $idPreOrden, '$usuarioReg')";
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

$app->post('/preorden/cajaybanco/gasto', function (Request $request, Response $response, array $args) {
    $idPreOrden = $request->getParam('IdPreOrden');
    $idHabitacion = $request->getParam('IdHabitacion');
    $gasto = $request->getParam('Gasto');
    $producto = $request->getParam('producto');
    $concepto = $request->getParam('concepto');
    $usuario = $request->getParam('Usuario');

    $vendedor = 'xx';
    if(isset($_SESSION['User'])) {
        $vendedor = $_SESSION['User'];
    }
    $usuarioReg = isset($usuario) ? $request->getParam('Usuario') : $vendedor;

    if ($idPreOrden) {
        $insert = "INSERT INTO Cb_CajaBanco (IdTipoCajaBanco, IdCuenta, FechaDoc, Concepto, Importe, Anulado, IdProveedor, IdCliente, EsDelVendedor, IdPreOrden, UsuarioReg) VALUES (3, 1, '" . getNow() . "', '$concepto', $gasto, 0, 0, $producto[IdCliente], 1, $idPreOrden, '$usuarioReg')";
        
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
    $idPreOrden     = $request->getParam('IdPreOrden');
    $tipoCajaBanco  = $request->getParam('TipoCajaBanco');

    if ($idPreOrden) {
        $select = "SELECT Cb_CajaBanco.*, Cb_TipoCajaBanco.Tipo FROM Cb_CajaBanco 
            INNER JOIN Cb_TipoCajaBanco ON Cb_CajaBanco.IdTipoCajaBanco=Cb_TipoCajaBanco.IdTipoCajaBanco
            WHERE IdPreOrden=$idPreOrden AND Cb_TipoCajaBanco.Tipo = $tipoCajaBanco";

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

    $idProforma = $request->getParam('IdProforma');

    if ($idProforma) {
        $sql = "UPDATE Ve_PreOrden SET IdProforma=$idProforma WHERE IdPreOrden=$idPreOrden ";

        $stmt = $this->db->prepare($sql);
        $updated = $stmt->execute();
    }

    if ($idPreOrden) {
        // Actualizar ProductoDet
        $sql = "DELETE FROM Ve_PreOrdenDet WHERE IdPreOrden='$idPreOrden' AND IdProducto != $idHabitacion";
        $stmt = $this->db->prepare($sql);
        $deleted = $stmt->execute();

        foreach($productos as $prod) {
            if ($prod['IdProducto'] != $idHabitacion) {
                $insertDet = $this->db->insert(array('IdPreOrden', 'IdProducto', 'Cantidad', 'Precio'))
                                    ->into('Ve_PreOrdenDet')
                                    ->values(array($idPreOrden, $prod['IdProducto'], $prod['Cantidad'], $prod['Precio']));
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

    $select = "SELECT Ve_PreOrdenDet.Cantidad, Gen_Producto.*, IFNULL(Ve_PreOrdenDet.Precio, Gen_Producto.PrecioContado) AS Precio, Ve_PreOrdenDet.IdPreOrden FROM Ve_PreOrdenDet
        INNER JOIN Gen_Producto ON Ve_PreOrdenDet.IdProducto = Gen_Producto.IdProducto
        WHERE Ve_PreOrdenDet.IdPreOrden=$idPreOrden";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);

});

$app->get('/preorden/proforma', function (Request $request, Response $response) {
    $idProforma = $request->getParam('idProforma');

    if ($idProforma) {
        $select = "SELECT Ve_Proforma.*, SUM(Ve_ProformaDet.Cantidad * Ve_ProformaDet.Precio) AS Total
            FROM Ve_Proforma
            INNER JOIN Ve_ProformaDet ON Ve_Proforma.IdProforma = Ve_ProformaDet.IdProforma
            WHERE Ve_Proforma.IdProforma=$idProforma
            GROUP BY Ve_Proforma.IdProforma";
    
        $stmt = $this->db->query($select);
        $stmt->execute();
        $data = $stmt->fetch();
    
        return $response->withJson($data);
    }
});

$app->get('/cuentas', function (Request $request, Response $response, array $args) {
    $select = "SELECT IdCuenta, Cuenta, Anulado FROM Cb_Cuenta WHERE Anulado=0";
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
        Ve_DocVenta.IdTipoDoc,
        CONCAT(Ve_DocVenta.Serie,'-', CONVERT(Ve_DocVenta.Numero, CHAR)) AS SerieNumero,
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
        END,Ve_DocVenta.Serie,'-' , CONVERT(Ve_DocVenta.Numero, CHAR) )AS Correlativo,
        ROUND(SUM((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2) AS Total,
        ROUND((SELECT IFNULL((SELECT SUM(Cb_CajaBancoDet.Importe) FROM Cb_CajaBancoDet WHERE Tipo='VE' AND Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2) AS Aplicado,
        
        ROUND( (SELECT IFNULL((SELECT SUM((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento)FROM
        Ve_DocVentaDet INNER JOIN Ve_DocVenta ON Ve_DocVentaDet.IdDocVenta=Ve_DocVenta.IdDocVenta WHERE Ve_DocVenta.IdTipoDoc=9 AND Ve_DocVenta.NroComprobanteModifica = SerieNumero AND Ve_DocVenta.Anulado=0  ),0)
        ),2) AS NotaCreApli,
            ROUND(SUM((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2) -
        ROUND((SELECT NotaCreApli) ,2)	-
            ROUND((SELECT IFNULL((SELECT SUM(Cb_CajaBancoDet.Importe) FROM Cb_CajaBancoDet WHERE Tipo='VE' AND Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2) AS Saldo,
        DocVentaNotas.Notas AS NotasCredito
        
        FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc=Ve_DocVentaTipoDoc.IdTipoDoc
        LEFT JOIN (SELECT Ve_DocVenta.NroComprobanteModifica,
        CONCAT( 
        '[', 
            GROUP_CONCAT( CONCAT( '{ \"IdDocVenta\":', Ve_DocVenta.idDocVenta ,',\"FechaDoc\":\"', Ve_DocVenta.FechaDoc , '\",\"Serie\":\"', Ve_DocVenta.Serie, '\",\"Numero\":',Ve_DocVenta.Numero,',\"Total\":',ROUND(((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2),' }' ) SEPARATOR ', '),
            ']'
        ) AS Notas 
        FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
        WHERE Ve_DocVenta.idTipoDoc=9 AND Ve_DocVenta.Anulado=0
        GROUP BY Ve_DocVenta.NroComprobanteModifica )  AS DocVentaNotas ON DocVentaNotas.NroComprobanteModifica = CONCAT(Ve_DocVenta.Serie,'-',Ve_DocVenta.Numero)
        
        
        
        WHERE Ve_DocVenta.IdCliente=$idCliente AND Ve_DocVenta.EsCredito=1 AND Ve_DocVenta.Anulado=0
        GROUP BY
        Ve_DocVenta.IdDocVenta,
        Ve_DocVenta.FechaDoc,
        Ve_DocVenta.FechaCredito,
        Ve_DocVentaTipoDoc.CodSunat,
        Ve_DocVenta.Serie,
        Ve_DocVenta.Numero
        HAVING ROUND(Saldo,2)>0
        
        ORDER BY FechaDoc;";

    }else{

        $select = "SELECT
        Ve_DocVentaCliente.DniRuc,
        Ve_DocVentaCliente.Cliente,
        Ve_DocVenta.IdDocVenta,
        Ve_DocVenta.FechaDoc,
        Ve_DocVenta.FechaCredito,
        Ve_DocVenta.IdTipoDoc,
        CONCAT(Ve_DocVenta.Serie,'-', CONVERT(Ve_DocVenta.Numero, CHAR)) AS SerieNumero,
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
        END,Ve_DocVenta.Serie,'-' , CONVERT(Ve_DocVenta.Numero, CHAR) )AS Correlativo,
        
        ROUND(SUM((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2) AS Total,
        ROUND((SELECT IFNULL((SELECT SUM(Cb_CajaBancoDet.Importe) FROM Cb_CajaBancoDet WHERE Tipo='VE' AND Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2) AS Aplicado,
        
        ROUND( (SELECT IFNULL((SELECT SUM((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento)FROM
        Ve_DocVentaDet INNER JOIN Ve_DocVenta ON Ve_DocVentaDet.IdDocVenta=Ve_DocVenta.IdDocVenta WHERE Ve_DocVenta.IdTipoDoc=9 AND Ve_DocVenta.NroComprobanteModifica = SerieNumero AND Ve_DocVenta.Anulado=0  ),0)
        ),2) AS NotaCreApli,
            ROUND(SUM((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2) -
        ROUND((SELECT NotaCreApli) ,2)	-
            ROUND((SELECT IFNULL((SELECT SUM(Cb_CajaBancoDet.Importe) FROM Cb_CajaBancoDet WHERE Tipo='VE' AND Cb_CajaBancoDet.IdDocDet=Ve_DocVenta.IdDocVenta),0)),2) AS Saldo,
        DocVentaNotas.Notas AS NotasCredito
        
        FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc=Ve_DocVentaTipoDoc.IdTipoDoc
        INNER JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente
        LEFT JOIN (SELECT Ve_DocVenta.NroComprobanteModifica,
        CONCAT( 
        '[', 
            GROUP_CONCAT( CONCAT( '{ \"IdDocVenta\":', Ve_DocVenta.idDocVenta ,',\"FechaDoc\":\"', Ve_DocVenta.FechaDoc , '\",\"Serie\":\"', Ve_DocVenta.Serie, '\",\"Numero\":',Ve_DocVenta.Numero,',\"Total\":',ROUND(((Ve_DocVentaDet.Precio*Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento ),2),' }' ) SEPARATOR ', '),
            ']'
        ) AS Notas 
        FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta=Ve_DocVentaDet.IdDocVenta
        WHERE Ve_DocVenta.idTipoDoc=9 AND Ve_DocVenta.Anulado=0
        GROUP BY Ve_DocVenta.NroComprobanteModifica )  AS DocVentaNotas ON DocVentaNotas.NroComprobanteModifica = CONCAT(Ve_DocVenta.Serie,'-',Ve_DocVenta.Numero)
            
        WHERE Ve_DocVenta.EsCredito=1 AND Ve_DocVenta.Anulado=0 ";

            $select .= " AND Ve_DocVenta.IdAlmacen = " . $request->getParam('idalmacen');
            $select .= " AND (Ve_DocVentaCliente.Cliente LIKE '%" . $request->getParam('q') . "%'";
            $select .= " OR Ve_DocVentaCliente.DniRuc LIKE '%" . $request->getParam('q') . "%') ";

        $select .="GROUP BY
        Ve_DocVenta.IdDocVenta,
        Ve_DocVenta.FechaDoc,
        Ve_DocVenta.FechaCredito,
        Ve_DocVentaTipoDoc.CodSunat,
        Ve_DocVenta.Serie,
        Ve_DocVenta.Numero
        HAVING ROUND(Saldo,2)>0
        
        ORDER BY FechaDoc";

        $limit = $request->getParam('limit') ? $request->getParam('limit') :  30;
        if ($limit) {
            $offset = 0;
            if ($request->getParam('page')) {
                $page = $request->getParam('page');
                $offset = (--$page) * $limit;
            }
            $select .= " LIMIT " . $limit;
            $select .= " OFFSET " . $offset;
        }
    }

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});

$app->get('/cliente/pagos', function (Request $request, Response $response, array $args) {
    $idCliente = $request->getParam('idCliente');
    $select = "SELECT Cb_CajaBanco.*,Cb_Cuenta.Cuenta FROM Cb_CajaBanco INNER JOIN Cb_Cuenta ON Cb_CajaBanco.IdCuenta = Cb_Cuenta.IdCuenta
    WHERE Cb_CajaBanco.IdCliente=$idCliente AND Cb_CajaBanco.EsDelVendedor=1 ORDER BY Cb_CajaBanco.FechaDoc DESC";

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
        WHERE EsCredito=1";
        $select .= " AND Ve_DocVenta.IdAlmacen = " . $request->getParam('idalmacen');
        $select .="
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

$app->post('/cliente/deudas/pagar', function (Request $request, Response $response) {
    $cajaBanco = $request->getParam('cajaBanco');
    $documentos = $request->getParam('documentos');
    $cuentas = $request->getParam('cuentas');
    $vendedor = 'xx';
    if(isset($_SESSION['user'])) {
        $vendedor = $_SESSION['user'];
    }
    $usuarioReg = isset($request->getParam('vendedor')['Usuario']) ? $request->getParam('vendedor')['Usuario'] : $vendedor;

    
    foreach ($cuentas as $cuenta) {
        $importe = 0;
        foreach($documentos as $doc) {

            if($doc['cuenta']==$cuenta){
                if (isset($doc['aplicar']) && $doc['aplicar'] > 0) {
                    $importe += $doc['aplicar'];
                }
            }  
        }

        if ($importe > 0) {
            $insert = $this->db->insert(array('IdTipoCajaBanco', 'IdCuenta', 'FechaDoc', 'Concepto', 'Importe', 'Anulado', 'UsuarioReg', 'IdCliente', 'EsDelVendedor'))
            ->into('Cb_CajaBanco')
            ->values(array(2, $cuenta , getNow(), $cajaBanco['Concepto'], $importe, 0, $usuarioReg, $cajaBanco['cliente']['IdCliente'], 1));
            $insertId = $insert->execute();

            foreach($documentos as $doc) {
                if (isset($doc['aplicar']) && $doc['aplicar'] > 0 && $doc['cuenta']==$cuenta ) {
                    $insertDet = $this->db->insert(array('IdCajaBanco', 'IdDocDet', 'Importe', 'Tipo'))
                        ->into('Cb_CajaBancoDet')
                        ->values(array($insertId, $doc['IdDocVenta'], $doc['aplicar'], 'VE'));
                    $insertDetId = $insertDet->execute();
                }
            }
        }

    }

    return $response->withJson(array(
        "idCajaBanco" => $insertId
    ));
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

    $limit = $request->getParam('limit') ? $request->getParam('limit') :  15;
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

//Reporte Utilidad Bruta SbVe_ReporteUtilidadBruta
$app->get('/reporte/utlidadbruta', function (Request $request, Response $response, array $args) {

    $fechaInicio = $request->getParam('fechaInicio');
    $fechaFin = $request->getParam('fechaFin');

    $select = "SELECT
	Gen_Producto.IdProducto,
	Gen_Producto.Producto,
	SUM(Ve_DocVentaDet.Cantidad) AS Cantidad,
	Gen_Producto.PrecioCosto,
	Ve_DocVentaDet.Precio AS PrecioVenta,
	ROUND(SUM(Ve_DocVentaDet.Cantidad) * Ve_DocVentaDet.Precio, 2) AS TotalVenta,
	ROUND(Gen_Producto.PrecioCosto * SUM(Ve_DocVentaDet.Cantidad), 2) AS TotalCosto,
	ROUND(SUM(Ve_DocVentaDet.Cantidad) * Ve_DocVentaDet.Precio - Gen_Producto.PrecioCosto * SUM(Ve_DocVentaDet.Cantidad), 2) AS UtilidadBruta
	FROM Ve_DocVenta
	INNER JOIN Ve_DocVentaDet ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta
	INNER JOIN Gen_Producto ON Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto
	WHERE Ve_DocVenta.Anulado=0 AND Ve_DocVenta.FechaDoc BETWEEN '$fechaInicio' AND '$fechaFin'
	GROUP BY Gen_Producto.IdProducto,
	Gen_Producto.Producto";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    return $response->withJson($data);
});

$app->get('/reporte/deudaclientes2', function (Request $request, Response $response, array $args) use ($app) {
    $idalmacen = $request->getParam('idalmacen');
    $limit = $request->getParam('limit') ? $request->getParam('limit') :  30;
    $res = $app->subRequest('GET', 'cliente/deudas', 'idalmacen=' . $idalmacen.'&limit='.$limit);


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
    $sheet->setCellValue('F1', 'APLICADO NOTA CRED.');
    $sheet->setCellValue('G1', 'SALDO');
    $sheet->setCellValue('H1', 'FECHA EMISION');
    $sheet->setCellValue('I1', 'FECHA VENCIMIENTO');
    $cont = 3;
    foreach($deudacliente as $prod) {
        $sheet->setCellValue('A'.$cont, $prod['DniRuc']);
        $sheet->setCellValue('B'.$cont, $prod['Cliente']);
        $sheet->setCellValue('C'.$cont, $prod['Correlativo']);
        $sheet->setCellValue('D'.$cont, 'S/. ' . $prod['Total']);
        $sheet->setCellValue('E'.$cont, 'S/. ' . $prod['Aplicado']);
        $sheet->setCellValue('F'.$cont, 'S/. ' . $prod['NotaCreApli']);
        $sheet->setCellValue('G'.$cont, 'S/. ' . $prod['Saldo']);
        $sheet->setCellValue('H'.$cont, date("d/m/Y", strtotime($prod['FechaDoc'])));
        $sheet->setCellValue('I'.$cont, date("d/m/Y", strtotime($prod['FechaCredito'])));
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
        $select .= " WHERE Ve_DocVenta.IdAlmacen = " . $request->getParam('idalmacen') ;
        $select .= " AND (Ve_DocVentaCliente.Cliente LIKE '%" . $request->getParam('q') . "%'";
        $select .= " OR Ve_DocVentaCliente.DniRuc LIKE '%" . $request->getParam('q') . "%') ";

        $select .= "GROUP BY Ve_DocVentaCliente.DniRuc
                     ORDER BY Total DESC, Ve_DocVentaCliente.Puntos DESC";

        $limit = $request->getParam('limit') ? $request->getParam('limit') :  15;
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

$app->get('/ranking/clientes/count', function (Request $request, Response $response, array $args) {
    $select  = "SELECT COUNT(*) as total
                FROM Ve_DocVenta
                INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.IdDocVenta=Ve_DocVentaDet.IdDocVenta
                INNER JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente =Ve_DocVentaCliente.IdCliente";
    $select .= " WHERE Ve_DocVenta.IdAlmacen = " . $request->getParam('idalmacen');
    $select .= " GROUP BY Ve_DocVentaCliente.DniRuc
                ORDER BY Total DESC, Ve_DocVentaCliente.Puntos DESC";
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $response->withJson($data);

});

$app->get('/clientes/count', function (Request $request, Response $response, array $args) {

    $select = "SELECT COUNT(*) AS total FROM Ve_DocVentaCliente";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});

$app->get('/clientes/comisionistas/count', function (Request $request, Response $response, array $args) {
    $idAlmacen = $request->getParam('idAlmacen');

    $select = "SELECT
	    COUNT(DISTINCT(Ve_DocVentaCliente.IdCliente)) AS total
        FROM
            Ve_DocVentaCliente
        INNER JOIN Ve_DocVenta ON
            Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente
        WHERE
            Ve_DocVentaCliente.esComisionista = 1
            AND Ve_DocVenta.valorComision IS NOT NULL ";
    
    if ($idAlmacen) {

    }

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);    

    return $response->withJson($data);
});

$app->get('/cliente/comisionistas', function (Request $request, Response $response, array $args) {
    $idAlmacen = $request->getParam('idAlmacen');

    $select = "SELECT Ve_DocVentaCliente.IdCliente, Ve_DocVentaCliente.Cliente, Ve_DocVentaCliente.DniRuc, 
        ROUND(
            (SUM((Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio) - Ve_DocVentaDet.Descuento) * (Ve_DocVenta.valorComision / 100))
            -
            IFNULL((SELECT SUM(Importe) FROM Cb_CajaBanco
            WHERE IdCliente = Ve_DocVentaCliente.IdCliente AND Cb_CajaBanco.IdTipoCajaBanco = 5 AND Cb_CajaBanco.Anulado = 0), 0)
        , 2) as Comision
        FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaDet ON Ve_DocVenta.idDocVenta = Ve_DocVentaDet.IdDocVenta
        INNER JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdComisionista = Ve_DocVentaCliente.IdCliente
        WHERE Ve_DocVentaCliente.esComisionista = 1 AND Ve_DocVenta.valorComision IS NOT NULL ";
    
    if ($idAlmacen) {
        $select .= " AND Ve_DocVenta.IdAlmacen = $idAlmacen ";
    }
        
    $select .= " GROUP BY Ve_DocVentaCliente.IdCliente";

    $limit = $request->getParam('limit');
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

$app->get('/cliente/comisionistas/pagos', function (Request $request, Response $response, array $args) {
    $idComisionista = $request->getParam('idComisionista');

    $select = "SELECT Cb_CajaBanco.*, Cb_TipoCajaBanco.TipoCajaBanco, Cb_CajaBanco.FechaDoc AS Fecha FROM Cb_CajaBanco
        INNER JOIN Cb_TipoCajaBanco ON Cb_CajaBanco.IdTipoCajaBanco = Cb_TipoCajaBanco.IdTipoCajabanco
        WHERE IdCliente = $idComisionista AND Cb_CajaBanco.Anulado = 0 ";

    // $limit = $request->getParam('limit') ? $request->getParam('limit') :  15;
    // if ($limit) {
    //     $offset = 0;
    //     if ($request->getParam('page')) {
    //         $page = $request->getParam('page');
    //         $offset = (--$page) * $limit;
    //     }
    //     $select .= " LIMIT " . $limit;
    //     $select .= " OFFSET " . $offset;
    // }

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();    

    return $response->withJson($data);
});


$app->post('/cliente/comisionistas/pagar', function (Request $request, Response $response) {
    $cliente = $request->getParam('cliente');
    $importe = $request->getParam('importe');
    $usuario = $_SESSION['user'];
    
    $insert = $this->db->insert(array('IdTipoCajaBanco', 'IdCuenta', 'FechaDoc', 'Concepto', 'Importe', 'Anulado', 'UsuarioReg', 'IdCliente'))
                    ->into('Cb_CajaBanco')
                    ->values(array(5, 1, getNow(), 'Pago a comisionista '. $cliente['Cliente'] , $importe, '0', $usuario, $cliente['IdCliente']));
    
    $insertId = $insert->execute();

    return $response->withJson(array(
        "idCajaBanco" => $insertId
    ));
});






$app->get('/tipodoc/{id}', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM Ve_DocVentaTipoDoc WHERE ";
    $select .= "  Ve_DocVentaTipoDoc.IdTipoDoc = '" . $args['id'] . "' ";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});


$app->get('/empresa/id/{id}', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM GEN_EMPRESA WHERE IDEMPRESA=$args[id]";

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

$app->get('/clientes/{dni}', function (Request $request, Response $response, array $args) {
    $select = "SELECT * FROM Ve_DocVentaCliente WHERE (Anulado != 1 OR Anulado IS NULL)";
    $select .= " AND Ve_DocVentaCliente.DniRuc = '" . $args['dni'] . "' LIMIT 1";
    
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});

$app->get('/clientes', function (Request $request, Response $response, array $args) {
    $select = "SELECT *, IFNULL(CONCAT(DniRuc, ' - ', Cliente, ' - ', IFNULL(NombreComercial, '')), '-') AS ClienteDniRuc FROM Ve_DocVentaCliente WHERE (Anulado != 1 OR Anulado IS NULL)";
    $select .= " AND (Ve_DocVentaCliente.Cliente LIKE '%" . $request->getParam('q') . "%' OR Ve_DocVentaCliente.NombreComercial LIKE '%" . $request->getParam('q') . "%'";
    $select .= " OR Ve_DocVentaCliente.DniRuc LIKE '%" . $request->getParam('q') . "%')  ";

    // $select = "SELECT *, IFNULL(CONCAT(DniRuc, ' - ', Cliente), '-') AS ClienteDniRuc FROM Ve_DocVentaCliente WHERE (Anulado != 1 OR Anulado IS NULL)";
    // $select .= " AND Ve_DocVentaCliente.Cliente LIKE '%" . $request->getParam('q') . "%'";
    // $select .= " OR Ve_DocVentaCliente.DniRuc LIKE '%" . $request->getParam('q') . "%'";

    $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;
    if ($limit && !$request->getParam('noLimit')) {
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

$app->get('/comisionistas', function (Request $request, Response $response, array $args) {
    $select = "SELECT *, IFNULL(CONCAT(DniRuc, ' - ', Cliente), '-') AS ClienteDniRuc FROM Ve_DocVentaCliente WHERE (Anulado != 1 OR Anulado IS NULL) AND esComisionista=1";
    $select .= " AND (Ve_DocVentaCliente.Cliente LIKE '%" . $request->getParam('q') . "%'";
    $select .= " OR Ve_DocVentaCliente.DniRuc LIKE '%" . $request->getParam('q') . "%') ";

    if ($request->getParam('sortBy')) {
        $sortBy = $request->getParam('sortBy');
        $sortDesc = $request->getParam('sortDesc');
        $orientation = $sortDesc ? 'DESC' : 'ASC';
        $select .= " ORDER BY " . $sortBy . " " . $orientation;
    }

    $limit = $request->getParam('limit') ? $request->getParam('limit') :  5;
    if ($limit && !$request->getParam('noLimit')) {
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
    $esComisionista = $request->getParam('esComisionista');
    $nombreComercial = $request->getParam('NombreComercial');

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
            "esComisionista" => $esComisionista,
            "NombreComercial" => $nombreComercial
        ))
       ->table('Ve_DocVentaCliente')
       ->where('IdCliente', '=', $idCliente);
        $affectedRows = $update->execute();
    } else {
        $insert = $this->db->insert(array('Cliente', 'DniRuc', 'Direccion', 'Direccion2', 'Direccion3', 'Telefono', 'Email', 'Anulado', 'FechaReg', 'Sexo', 'Ocupacion', 'FechaNacimiento', 'Nacionalidad', 'EsVIP', 'NombreComercial', 'esComisionista'))
                           ->into('Ve_DocVentaCliente')
                           ->values(array($cliente, $dniRuc, $direccion, $direccion2, $direccion3, $telefono, $email, '0', getNow(), $sexo, $ocupacion, $fechaNacimiento, $nacionalidad, $esVIP, $nombreComercial, $esComisionista));

        $insertId = $insert->execute();

    }

    $select = "SELECT * FROM Ve_DocVentaCliente WHERE IdCliente=$insertId";
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetch();

    return $response->withJson($data);
});

// DATOS GLOBALES DE LA EMPRESA
define('NRO_DOCUMENTO_EMPRESA', '10768688422');
define('TIPO_DOCUMENTO_EMPRESA', '6'); //1 DNI 6 RUC
define('TIPO_PROCESO', '01'); //01 PRODUCCION 03 BETA
define('RAZON_SOCIAL_EMPRESA', 'LEON DURAN AMANCIO VELAN');
define('NOMBRE_COMERCIAL_EMPRESA', 'FERRETERIA BRIANNA');
define('CODIGO_UBIGEO_EMPRESA', "250101");
define('DIRECCION_EMPRESA', "JR. SAN MARTIN NRO 625 HUANUCO - HUANUCO - HUANUCO");
define('DEPARTAMENTO_EMPRESA', "HUANUCO");
define('PROVINCIA_EMPRESA', "HUANUCO");
define('DISTRITO_EMPRESA', "HUANUCO");
define('TELEFONOS_EMPRESA', "948854626");

define('CODIGO_PAIS_EMPRESA', 'PE');
define('USUARIO_SOL_EMPRESA', 'BRIANA22'); // cambiar cuando se pase a produccion //NEURO123
define('PASS_SOL_EMPRESA', 'BrIaNa2022'); // cambiar cuando se pase a produccion

$app->post('/emitirelectronico', function (Request $request, Response $response) {
    include_once("../controllers/NumerosEnLetras/NumerosEnLetras.php");

    $docVenta = $request->getParam('docVenta');

    // Obtener venta.
    $select = "SELECT * FROM Ve_DocVenta WHERE idDocVenta=" . $docVenta['idDocVenta'];
    $stmt = $this->db->query($select);
    $stmt->execute();
    $venta = $stmt->fetch();

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
        $subtotal = $producto['Descuento'] > 0 ? $subtotal - $producto['Descuento'] : $subtotal;

        $json['txtITEM']=$n;
        $json["txtUNIDAD_MEDIDA_DET"] = $producto['CodigoMedicion'];
        $json["txtUNIDAD_MEDIDA_NOMBRE_DET"] = $producto['ProductoMedicion'];
        $json["txtCANTIDAD_DET"] = $producto['Cantidad'];
        $json["txtPRECIO_DET"] = round(($subtotal + $igv) / $producto['Cantidad'], 4);
        $json["txtPRECIO_DESC_DET"] = $producto['Descuento'];
        $json["txtSUB_TOTAL_DET"] = $subtotal;  //PRECIO * CANTIDAD
        $json["txtPRECIO_TIPO_CODIGO"] = "01"; // 02 valor referencial unitario en operaciones no onerosas
        $json["txtIGV"] = $igv;
        $json["txtISC"] = "0";
        // $json["txtIMPORTE_DET"] = $producto['Total']; //rowData.IMPORTE; //SUB_TOTAL + IGV
        $json["txtIMPORTE_DET"] = $subtotal; //rowData.IMPORTE; //SUB_TOTAL + IGV
        $json["txtCOD_TIPO_OPERACION"] = $docVenta['CodigoIgv']; //20 si es exonerado
        $json["txtCODIGO_DET"] = $producto['CodigoBarra'];
        $json["txtDESCRIPCION_DET"] = trim($producto['Producto']);
        // $json["txtPRECIO_SIN_IGV_DET"] = round($producto['Precio'] - ($producto['Precio'] * 0.18), 2);
        //$json["txtPRECIO_SIN_IGV_DET"] = $subtotal;
        $json["txtPRECIO_SIN_IGV_DET"] =  $docVenta['TieneIgv'] ? round($producto['Precio'] / 1.18, 4) : $producto['Precio'];

        $detalle[]=$json;
        $descuento += $producto['Descuento'];
    }

    // $gravadas = $docVenta['TieneIgv'] ? $docVenta['Total'] - ($docVenta['Total'] * 0.18) : $docVenta['Total'];
    $gravadas = $docVenta['TieneIgv'] ? round($docVenta['Total'] / 1.18, 2) : $docVenta['Total'];
    //$subtotal = ($descuento > 0) ? $gravadas + $descuento : $gravadas;
    $subtotal = $gravadas;

    //ELIMINAR ESPACIOS EN BLANCO DNI/RUC
    $docVenta['DniRuc'] = trim($docVenta['DniRuc']);

    //$total = ($descuento > 0) ? $subtotal - $descuento : '0';
    $data = array(
        "txtTIPO_OPERACION"=>"0101", // corregir esto despues
        "txtTOTAL_DESCUENTO" => $descuento,
        //"txtTOTAL_GRAVADAS"=> $gravadas,
        "txtSUB_TOTAL"=> $subtotal,
        "txtPOR_IGV"=> $docVenta['TieneIgv'] ? "18" : "0",
        // "txtTOTAL_IGV"=> $docVenta['TieneIgv'] ? round($docVenta['Total'] * 0.18, 2) : 0,
        "txtTOTAL_IGV"=> $docVenta['TieneIgv'] ? round($docVenta['Total'] - ($docVenta['Total'] / 1.18), 2) : 0,
        "txtTOTAL"=> $docVenta['Total'],
        "txtTOTAL_LETRAS"=> NumerosEnLetras::convertir(number_format($docVenta['Total'], 2, '.', ''),'SOLES',true),
        "txtNRO_COMPROBANTE"=> $docVenta['Serie'] . "-" . $docVenta['Numero'], //
        "txtFECHA_DOCUMENTO"=> date("Y-m-d", strtotime($docVenta['FechaDoc'])),
        "txtFECHA_VTO"=> date("Y-m-d", strtotime($docVenta['FechaDoc'])),
        "txtCOD_TIPO_DOCUMENTO"=> $docVenta['CodSunat'], //01=factura,03=boleta,07=notacrediro,08=notadebito
        "txtCOD_MONEDA"=> 'PEN', //PEN= PERU
        //'detalle_forma_pago' => [
        //   ["COD_FORMA_PAGO" => "Contado"]
        //],
        "txtVENDEDOR"=> $docVenta['UsuarioReg'], //VENDEDOR
        //==========documentos de referencia(nota credito, debito)=============
        "txtTIPO_COMPROBANTE_MODIFICA"=> isset($docVenta['CodSunatModifica']) && $docVenta['CodSunatModifica'] ? $docVenta['CodSunatModifica'] : "", //aqui completar
        "txtNRO_DOCUMENTO_MODIFICA"=> isset($docVenta['NroComprobanteModifica']) && $docVenta['NroComprobanteModifica'] ? $docVenta['NroComprobanteModifica'] : "",
        "txtCOD_TIPO_MOTIVO"=> isset($docVenta['NotaIdMotivo']) && $docVenta['NotaIdMotivo'] ? $docVenta['NotaIdMotivo'] : "",
        "txtDESCRIPCION_MOTIVO"=> isset($docVenta['NotaDescMotivo']) && $docVenta['NotaDescMotivo'] ? $docVenta['NotaDescMotivo'] : "", //$("[name='txtID_MOTIVO']
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

    if($docVenta['CodSunat'] == '07'){
        unset($data['detalle_forma_pago']);
    }


    if ($docVenta['CodigoIgv'] == "20") { // 20 = exonerado Igv
        $data["txtTOTAL_EXONERADAS"] = $docVenta['Total'];
    }

    // forma de pago
    if ($venta['EsCredito']) {
        $data['detalle_forma_pago'] = [
            [
                "COD_FORMA_PAGO" => "Credito",
                "MONTO_FORMA_PAGO" => $gravadas,
                "FECHA_FORMA_PAGO" => date('Y-m-d', strtotime($venta['FechaCredito']))
            ],
            [
                "COD_FORMA_PAGO" => "Cuota001",
                "MONTO_FORMA_PAGO" => $gravadas,
                "FECHA_FORMA_PAGO" => date('Y-m-d', strtotime($venta['FechaCredito']))
            ]
        ];
    } else {
        $data['detalle_forma_pago'] = [
            ["COD_FORMA_PAGO" => "Contado"]
        ];
    }

    if($request->getParam('generarXml'))
    {
        $data['generarXml'] = true;
        $resultado = $new->sendPostCPE(json_encode($data));
        return $response->withJson([
            'success' => true
        ]);
    }
    $data['generarXml'] = false;
    
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
        
        if($docVenta['CodSunat'] == '07'){
            $new->creaPDFNota(json_encode($data));
        }else{
            $new->creaPDF(json_encode($data));
        }

        // Si es nota de credito/debito insertar en tabla
        if ($docVenta['CodSunat'] == '07') { // nota de credito
            $sqlNC = "INSERT INTO Ve_DocVentaNotaCredito (idDocVenta, Hash_cpe, Hash_cdr, Msj_sunat) VALUES
                ($docVenta[idDocVenta], '$me[hash_cpe]', '$me[hash_cdr]', '$me[msj_sunat]')";
            $stmt = $this->db->prepare($sqlNC);
            $insert = $stmt->execute();
            $lastInsert = $this->db->lastInsertId();

            //return $response->withJson($me);
        }

        if ($docVenta['CodSunat'] == '08') {

        }
    }

    // Para los casos de factura, boleta y nota de credito se actualiza misma tabla
    $sql = "UPDATE Ve_DocVenta SET Estado=$estado, hash_cpe='$me[hash_cpe]', Hash_cdr='$me[hash_cdr]',
        Msj_sunat='$me[msj_sunat]' WHERE idDocVenta='$docVenta[idDocVenta]' ";

    $stmt = $this->db->prepare($sql);
    $updated = $stmt->execute();
    $me['Estado'] = $estado;
    

    return $response->withJson($me);
});


$app->post('/generarpdfelectronico', function (Request $request, Response $response) {
    include_once("../controllers/NumerosEnLetras/NumerosEnLetras.php");

    $docVenta = $request->getParam('docVenta');
    $new = new api_sunat();

    $detalle = array();
    $json = array();
    $n=0;
    $descuento = 0;

    foreach ($docVenta['productos'] as $producto) {
        $n=$n+1;

        $igv = $docVenta['TieneIgv'] ? round($producto['Total'] * 0.18, 2) : 0;
        $subtotal = $docVenta['TieneIgv'] ? $producto['Total'] - $igv : $producto['Total'];

        $json['txtITEM']=$n;
        $json["txtUNIDAD_MEDIDA_DET"] = $producto['CodigoMedicion'];
        $json["txtUNIDAD_MEDIDA_NOMBRE_DET"] = $producto['ProductoMedicion'];
        $json["txtCANTIDAD_DET"] = $producto['Cantidad'];
        $json["txtPRECIO_DET"] = $producto['Precio'];
        $json["txtPRECIO_DESC_DET"] = $producto['Descuento'];
        $json["txtSUB_TOTAL_DET"] = $subtotal;  //PRECIO * CANTIDAD
        $json["txtPRECIO_TIPO_CODIGO"] = "01"; // 02 valor referencial unitario en operaciones no onerosas
        $json["txtIGV"] = $igv;
        $json["txtISC"] = "0";
        $json["txtIMPORTE_DET"] = $producto['Total']; //rowData.IMPORTE; //SUB_TOTAL + IGV
        $json["txtCOD_TIPO_OPERACION"] = $docVenta['CodigoIgv']; //20 si es exonerado
        $json["txtCODIGO_DET"] = $producto['CodigoBarra'];
        $json["txtDESCRIPCION_DET"] = trim($producto['Producto']);

        $json["txtMEDICION"] = $producto['ProductoMedicion'];
        $json["txtLABORATORIO"] = $producto['ProductoMarca'];
        $json["txtLOTE"] = isset($producto['IdLote']) ? $producto['IdLote'] : '';
        $json["txtFECHAVEN"] = isset($producto['FechaVen']) ? $producto['FechaVen'] : '';
        $json["txtFECHAVENCIMIENTO"] = isset($producto['FechaVencimiento']) ? $producto['FechaVencimiento'] : '';

        //$json["txtPRECIO_SIN_IGV_DET"] = round($producto['Precio'] - ($producto['Precio'] * 0.18), 2);
        $json["txtPRECIO_SIN_IGV_DET"] = $subtotal;

        $detalle[]=$json;
        $descuento += $producto['Descuento'];
    }

    $gravadas = $docVenta['TieneIgv'] ? $docVenta['Total'] - ($docVenta['Total'] * 0.18) : $docVenta['Total'];
    $subtotal = ($descuento > 0) ? $gravadas + $descuento : $gravadas;

    //ELIMINAR ESPACIOS EN BLANCO DNI/RUC
    $docVenta['DniRuc'] = trim($docVenta['DniRuc']);
    
    //$total = ($descuento > 0) ? $subtotal - $descuento : '0';
    $data = array(
        "txtTIPO_OPERACION"=>"0101", // corregir esto despues
        "txtTOTAL_DESCUENTO" => $descuento,
        "txtTOTAL_GRAVADAS"=> $gravadas,
        "txtSUB_TOTAL"=> $subtotal,
        "txtPOR_IGV"=> $docVenta['TieneIgv'] ? "18" : "0",
        "txtTOTAL_IGV"=> $docVenta['TieneIgv'] ? round($docVenta['Total'] * 0.18, 2) : 0,
        "txtTOTAL"=> $docVenta['Total'],
        "txtTOTAL_LETRAS"=> NumerosEnLetras::convertir(number_format($docVenta['Total'], 2, '.', ''),'SOLES',true),
        "txtNRO_COMPROBANTE"=> $docVenta['Serie'] . "-" . $docVenta['Numero'], //
        "txtFECHA_DOCUMENTO"=> date("Y-m-d", strtotime($docVenta['FechaDoc'])),
        "txtFECHA_CREDITO"=> date("Y-m-d", strtotime($docVenta['FechaCredito'])),
        "txtFECHA_VTO"=> date("Y-m-d", strtotime($docVenta['FechaDoc'])),
        "txtCOD_TIPO_DOCUMENTO"=> $docVenta['CodSunat'], //01=factura,03=boleta,07=notacrediro,08=notadebito
        "txtCOD_MONEDA"=> 'PEN', //PEN= PERU
        "txtVENDEDOR"=> $docVenta['UsuarioReg'], //VENDEDOR
        "txtALMACEN"=> $docVenta['Almacen'],
        "txtALMACEN_DIRECCION" => $docVenta['DireccionAlmacen'],
        "txtALMACEN_ES_PRINCIPAL" => $docVenta['EsAlmacenPrincipal'],
        //==========documentos de referencia(nota credito, debito)=============
        "txtTIPO_COMPROBANTE_MODIFICA"=> isset($docVenta['CodSunatModifica']) && $docVenta['CodSunatModifica'] ? $docVenta['CodSunatModifica'] : "", //aqui completar
        "txtNRO_DOCUMENTO_MODIFICA"=> isset($docVenta['NroComprobanteModifica']) && $docVenta['NroComprobanteModifica'] ? $docVenta['NroComprobanteModifica'] : "",
        "txtCOD_TIPO_MOTIVO"=> isset($docVenta['NotaIdMotivo']) && $docVenta['NotaIdMotivo'] ? $docVenta['NotaIdMotivo'] : "",
        "txtDESCRIPCION_MOTIVO"=> isset($docVenta['NotaDescMotivo']) && $docVenta['NotaDescMotivo'] ? $docVenta['NotaDescMotivo'] : "", //$("[name='txtID_MOTIVO']
        //=================datos del cliente=================8
         "txtNRO_DOCUMENTO_CLIENTE"=> $docVenta['DniRuc'],
         "txtRAZON_SOCIAL_CLIENTE"=> $docVenta['Cliente'],
         "txtNOMBRE_COMERCIAL_CLIENTE"=> $docVenta['NombreComercial'],
         "txtTIPO_DOCUMENTO_CLIENTE"=> strlen($docVenta['DniRuc']) > 9 ? "6" : "1",//1 DNI 6 RUC
         "txtDIRECCION_CLIENTE"=>$docVenta['Direccion'],
         "txtES_CREDITO" => $docVenta['EsCredito'],
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
         "txtTELEFONOS_EMPRESA" => TELEFONOS_EMPRESA,
         "txtUSUARIO_SOL_EMPRESA"=> USUARIO_SOL_EMPRESA,
         "txtPASS_SOL_EMPRESA"=> PASS_SOL_EMPRESA,
         "txtTIPO_PROCESO"=> TIPO_PROCESO, //01 PRODUCCION 03 BETA
         "detalle"=>$detalle,
        //"detalle" => []
    );
    if ($docVenta['CodigoIgv'] == "20") { // 20 = exonerado Igv
        $data["txtTOTAL_EXONERADAS"] = $docVenta['Total'];
    }

    $data['hash_cpe'] = isset($docVenta['Hash_cpe']) ? $docVenta['Hash_cpe'] : '';

    if($docVenta['CodSunat']=='07'){
        $new->creaPDFNota(json_encode($data));
    }else{
        $new->creaPDF(json_encode($data));
    }
    

});

$app->get('/imprimirpdf/{id}', function (Request $request, Response $response, array $args) use ($app) {
    $id = $args['id'];

    $select = "SELECT Ve_DocVenta.idDocVenta, Ve_DocVenta.EsCredito, Ve_DocVenta.FechaCredito, Ve_DocVenta.FechaDoc, Ve_DocVentaTipoDoc.TipoDoc, Ve_DocVentaTipoDoc.TieneIgv,
        Ve_DocVenta.Anulado, Ve_DocVenta.Serie, Ve_DocVenta.Numero, Ve_DocVentaCliente.Cliente, Ve_DocVenta.UsuarioReg,
        Ve_DocVentaTipoDoc.CodSunat, Ve_DocVentaCliente.DniRuc, Ve_DocVentaCliente.DniRuc, Ve_DocVenta.CampoDireccion as Direccion,
        Ve_DocVentaTipoDoc.CodigoIgv, Ve_DocVenta.Estado, Ve_DocVenta.Hash_cpe, Ve_DocVenta.Hash_cdr, Ve_DocVenta.Msj_sunat,
        Ve_DocVenta.FechaCredito, Lo_Almacen.Almacen, Lo_Almacen.Direccion DireccionAlmacen, Lo_Almacen.EsPrincipal EsAlmacenPrincipal,
        Ve_DocVentaCliente.NombreComercial,Ve_DocVenta.CodSunatModifica,
        Ve_DocVenta.NroComprobanteModifica,
        Ve_DocVenta.NotaIdMotivo,
        Ve_DocVenta.NotaDescMotivo,
        IFNULL((SELECT SUM(ROUND((Ve_DocVentaDet.Precio * Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento, 2)) FROM Ve_DocVentaDet WHERE Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta), 0 ) AS Total
        FROM Ve_DocVenta
        INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
        LEFT JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente 
        INNER JOIN Lo_Almacen ON Ve_DocVenta.IdAlmacen = Lo_Almacen.IdAlmacen
        WHERE idDocVenta = $id ";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $venta = $stmt->fetch();

    $select = "SELECT Ve_DocVentaDet.*, Gen_Producto.Producto,
        ROUND(Ve_DocVentaDet.Descuento, 2) AS Descuento,
        ROUND((Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio) - Ve_DocVentaDet.Descuento, 2) AS Subtotal,
        ROUND(Ve_DocVentaDet.Cantidad * Ve_DocVentaDet.Precio, 2) AS Total,
        Gen_Producto.CodigoBarra, Gen_ProductoMedicion.Codigo AS CodigoMedicion,
        Gen_ProductoMedicion.ProductoMedicion, Gen_ProductoMarca.ProductoMarca
        FROM Ve_DocVentaDet
        INNER JOIN Gen_Producto ON Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto
        INNER JOIN Gen_ProductoMedicion ON Gen_Producto.IdProductoMedicion = Gen_ProductoMedicion.IdProductoMedicion
        INNER JOIN Gen_ProductoMarca ON Gen_Producto.IdProductoMarca = Gen_ProductoMarca.IdProductoMarca
        WHERE IdDocVenta = $id";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $productos = $stmt->fetchAll();

    $venta['productos'] = $productos;

    // Generar el PDF como archivo
    $res = $app->subRequest('POST', '/generarpdfelectronico', http_build_query(['docVenta' => $venta]));
    
    // Buscar el pdf y mostrarlo
    $response = $this->response->withHeader( 'Content-type', 'application/pdf' );
    $content = file_get_contents(dirname(__FILE__) . '/sunat/api_cpe/PRODUCCION/' . NRO_DOCUMENTO_EMPRESA . '/' . NRO_DOCUMENTO_EMPRESA . '-' . $venta['CodSunat'] . '-' . $venta['Serie'] . '-' . $venta['Numero'] . '.pdf');
    return $response->write($content);
});



$app->post('/moveraemitidos', function (Request $request, Response $response) {
    $idDocVenta = $request->getParam('idDocVenta');

    if ($idDocVenta) {
        $update = "UPDATE Ve_DocVenta SET Estado=2 WHERE idDocVenta=$idDocVenta";
        $stmt = $this->db->prepare($update);
        $updated = $stmt->execute();


        return $response->withJson(array(
            "updated" => $updated,
            "IdDocVenta" => $idDocVenta
        ));
    }
});

$app->post('/cambiaraanonimo', function (Request $request, Response $response) {
    $idDocVenta = $request->getParam('idDocVenta');

    if ($idDocVenta) {
        $update = "UPDATE Ve_DocVenta SET IdCliente=(SELECT IdCliente FROM `Ve_DocVentaCliente` WHERE DniRuc='00000000' LIMIT 1) WHERE idDocVenta=$idDocVenta";
        $stmt = $this->db->prepare($update);
        $updated = $stmt->execute();


        return $response->withJson(array(
            "updated" => $updated,
            "IdDocVenta" => $idDocVenta
        ));
    }
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
        $json['TIPO_COMPROBANTE'] = $boleta['CodSunat'];
        $json['NRO_COMPROBANTE'] = $boleta['Serie'] . '-' . $boleta['Numero'];
        $json['TIPO_DOCUMENTO'] = strlen($boleta['DniRuc']) > 9 ? "6" : "1";
        $json['NRO_DOCUMENTO'] = $boleta['DniRuc'];
        $json['TIPO_COMPROBANTE_REF'] = $boleta['CodSunatModifica'] ? $boleta['CodSunatModifica'] : '';
        $json['NRO_COMPROBANTE_REF'] = $boleta['NroComprobanteModifica'] ? $boleta['NroComprobanteModifica'] : '';
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
                    $json["txtCANTIDAD_DET"] = trim($producto['Cantidad']);
                    $json["txtPRECIO_DET"] = trim($producto['Precio']);
                    $json["txtIMPORTE_DET"] = $subtotal + $igv; //rowData.IMPORTE; //SUB_TOTAL + IGV
                    $json["txtCODIGO_DET"] = trim($producto['CodigoBarra']);
                    $json["txtDESCRIPCION_DET"] = trim($producto['Producto']);
                    $detallePdf[]=$json;
                }
                $dataPdf = array(
                    'txtTIPO_PROCESO' => TIPO_PROCESO,
                    'txtCOD_MONEDA' => $codMoneda,
                    'txtNRO_DOCUMENTO_EMPRESA' => NRO_DOCUMENTO_EMPRESA,
                    'txtCOD_TIPO_DOCUMENTO' => $boleta['CodSunat'],
                    'txtNRO_COMPROBANTE' => $boleta['Serie'] . '-' . $boleta['Numero'],
                    'txtSUB_TOTAL' => $boleta['Total'],
                    'txtTOTAL_IGV' => "0",
                    'txtTOTAL' => $boleta['Total'],
                    'txtTOTAL_LETRAS' => NumerosEnLetras::convertir(number_format($boleta['Total'], 2, '.', ''),'SOLES',true),
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
        $json['TIPO_COMPROBANTE'] = $factura['CodSunat'];
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


$app->get('/reporte/ventasproducto', function (Request $request, Response $response, array $args) use ($app) {
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaInicio = $request->getParam('fechaInicio');
    $fechaFin = $request->getParam('fechaFin');
    $declarado = $request->getParam('declarado') ? 1 : 0;
    
    $select = "SELECT Ve_DocVenta.idDocVenta, Ve_DocVenta.FechaDoc, Ve_DocVentaTipoDoc.TipoDoc, Ve_DocVentaTipoDoc.TieneIgv,
        Ve_DocVenta.Anulado, Ve_DocVenta.Serie, Ve_DocVenta.Numero, Ve_DocVentaCliente.Cliente, Ve_DocVenta.UsuarioReg,
        Ve_DocVentaTipoDoc.CodSunat, Ve_DocVentaCliente.DniRuc, Ve_DocVentaCliente.Direccion, Ve_DocVentaTipoDoc.CodigoIgv,
        IFNULL(SUM(ROUND((Ve_DocVentaDet.Precio * Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento, 2)), 0) AS Total,
        Gen_Producto.IdProducto,
        Gen_Producto.Producto,
        Ve_DocVentaDet.Precio,
        Ve_DocVentaDet.Cantidad,
        Ve_DocVentaDet.Descuento
        FROM
            Ve_DocVentaDet
                INNER JOIN
            Ve_DocVenta ON Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta
                INNER JOIN
            Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
                LEFT JOIN
            Ve_DocVentaCliente ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente
                INNER JOIN 
                Gen_Producto ON Ve_DocVentaDet.IdProducto = Gen_Producto.IdProducto
        WHERE
            Ve_DocVentaTipoDoc.VaRegVenta = $declarado
                AND Ve_DocVenta.FechaDoc BETWEEN CAST('" . $fechaInicio . "' AS DATETIME) AND CONCAT('" . $fechaFin . "', ' 23:59:59')
                AND Ve_DocVentaTipoDoc.CodSunat IN ('01', '03')
        GROUP BY Ve_DocVenta.idDocVenta
        ORDER BY Ve_DocVenta.FechaDoc DESC";

    $stmt = $this->db->query($select);
    $stmt->execute();
    $ventas = $stmt->fetchAll();

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template/reporteventas.xlsx');
    $sheet = $spreadsheet->getSheet(0);

    $cont = 2;
    foreach($ventas as $ven) {
        $exonerado = 0;
        $igv = 0;
        $subtotal = $ven['Total'];
        // $estado = 1; // 1 activo, 2 anulado
        if ($ven['TieneIgv']) {
            // $igv = $ven['Total'] * 0.18;
            // $subtotal = $ven['Total'] - $igv;
            $subtotal = number_format($ven['Total'] / 1.18, 2, '.', '');
            $igv = number_format($ven['Total'] - ($ven['Total'] / 1.18), 2, '.', '');
        }

        /*if ($ven['CodigoIgv'] == '20') {
            $subtotal = 0;
            $igv = 0;  
            $exonerado = $ven['Total'];
        }*/

        if ($ven['Anulado']) {
            $subtotal = 0;
            $igv = 0;
            $exonerado = 0;
            $ven['Total'] = 0;
            $ven['Cantidad'] = 0;
            $estado = 2;
        }

        $sheet->setCellValue('A'.$cont, date("d/m/Y", strtotime($ven['FechaDoc'])));
        $sheet->setCellValue('B'.$cont, $ven['CodSunat']);
        $sheet->setCellValue('C'.$cont, $ven['Anulado'] == 1 ? 'ANULADO' : 'ACTIVO');
        $sheet->setCellValue('D'.$cont, $ven['Serie']);
        $sheet->setCellValue('E'.$cont, $ven['Numero']);
        $sheet->setCellValue('F'.$cont, $ven['DniRuc']);
        $sheet->setCellValue('G'.$cont, $ven['Cliente']);
        $sheet->setCellValue('H'.$cont, $subtotal);
        $sheet->setCellValue('I'.$cont, $igv);
        $sheet->setCellValue('J'.$cont, $ven['Total']);
        $sheet->setCellValue('L'.$cont, $ven['Producto']);
        $sheet->setCellValue('M'.$cont, $ven['Precio']);
        $sheet->setCellValue('N'.$cont, $ven['Cantidad']);

        $sheet->duplicateStyle($sheet->getStyle('A'.$cont),'A'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('B'.$cont),'B'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('C'.$cont),'C'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('D'.$cont),'D'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('E'.$cont),'E'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('F'.$cont),'F'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('G'.$cont),'G'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('H'.$cont),'H'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('I'.$cont),'I'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('J'.$cont),'J'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('K'.$cont),'K'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('L'.$cont),'L'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('M'.$cont),'M'.($cont+1));
        $sheet->duplicateStyle($sheet->getStyle('N'.$cont),'N'.($cont+1));

        
        $cont += 1;
    }

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('reporte/reporteventas.xlsx');

    // return $response->withRedirect('/api/reporte/reporteventas.xlsx'); 

    echo "<script>window.location.href = '/api/reporte/reporteventas.xlsx'</script>";
    exit;
});

$app->get('/reporte/ventas', function (Request $request, Response $response, array $args) use ($app) {
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaInicio = $request->getParam('fechaInicio');
    $fechaFin = $request->getParam('fechaFin');
    $declarado = $request->getParam('declarado') ? 1 : 0;

    $res = $app->subRequest('GET', '/ventas', '&filter[fechaInicio]=' . $fechaInicio . '&filter[fechaFin]=' . $fechaFin . '&filter[declarado]=' . $declarado . '&sortBy=Ve_DocVenta.FechaDoc&sortDesc=DESC' .'&idAlmacen=' . $idAlmacen);
    // $res = $app->subRequest('GET', '/ventas', 'idAlmacen=' . $idAlmacen . '&filter[fechaInicio]=' . $fechaInicio . '&filter[fechaFin]=' . $fechaFin . '&filter[declarado]=' . $declarado . '&sortBy=Ve_DocVenta.FechaDoc&sortDesc=DESC');
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

        if ($ven['CodSunat'] == '07') {
            $exonerado = -1 * $exonerado;
            $ven['Total'] = -1 * $ven['Total'];
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
    $idalmacen = $request->getParam('idalmacen');
    $res = $app->subRequest('GET', "ranking/clientes", 'idalmacen=' . $idalmacen );

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
    $filter = $request->getParam('filter');

    $filterStr = http_build_query(array(
        'filter' => $filter
    ));

    $res = $app->subRequest('GET', '/productos/stock', 'idAlmacen=' . $idAlmacen . '&' . $filterStr );
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

$app->get('/reporte/stockmensual', function (Request $request, Response $response, array $args) use ($app) {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template/stockmensual.xlsx');
    $sheet = $spreadsheet->getSheet(0);
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaHasta = $request->getParam('fechaHasta') ? $request->getParam('fechaHasta') : getNow();
    
    // if ($request->getParam('controlaStock')) {
    //     $res = $app->subRequest('GET', '/productos/stock', 'controlaStock=1&idAlmacen=' . $idAlmacen . '&noLimit=1');
    // } else {
        $res = $app->subRequest('GET', '/productos/stock', 'idAlmacen=' . $idAlmacen . '&noLimit=1' . '&fechaHasta=' . $fechaHasta);
    // }
    
    $productos = (string) $res->getBody();
    $productos = json_decode($productos, true);

    $sheet->getCell('E1')->setValue($fechaHasta);

    $init = 3;
    foreach($productos as $prod) {
        $sheet->getCell('A'.$init)->setValue($prod['CodigoBarra']);
        $sheet->getCell('B'.$init)->setValue($prod['Producto']);
        $sheet->getCell('L'.$init)->setValue($prod['stock']);
        $sheet->getCell('M'.$init)->setValue($prod['PrecioCosto']);
        $sheet->getCell('N'.$init)->setValue($prod['stock'] * $prod['PrecioCosto']);
        $init += 1;
    }

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('reporte/stockmensual.xlsx');
    
    echo "<script>window.location.href = '/api/reporte/stockmensual.xlsx'</script>";
    exit;
    // return $response->withRedirect('/api/reporte/stockmensual.xlsx'); 
});


function kardexSaldo($kardex) {
    $newKardex = [];
    $saldo = 0;
    $saldoCostoTotal = 0;
    $saldoCostoUnitario = 0;
    foreach($kardex as $kar) {
        $saldo += ($kar['IngresoCantidad'] - $kar['SalidaCantidad']);
        $kar['saldo'] = $saldo;
        if ($kar['IngresoCantidad'] > 0) {
            $saldoCostoTotal = $saldoCostoTotal + ($kar['IngresoCantidad'] * $kar['IngresoPrecio']);
            // dxerror division por cero
            if ($saldo == 0) {
                $saldoCostoUnitario = $saldoCostoTotal;                
            } else {
                $saldoCostoUnitario = $saldoCostoTotal / $saldo;
            }
        } else {
            // la salida Precio es el ultimo costo unitario de saldo
            $kar['SalidaPrecio'] = round($saldoCostoUnitario, 2);

            $saldoCostoTotal = $saldo * $kar['SalidaPrecio'];
            $saldoCostoUnitario = $kar['SalidaPrecio'];
        }
        $kar['saldoCostoUnitario'] = round($saldoCostoUnitario, 2);
        $kar['saldoCostoTotal'] = round($saldoCostoTotal, 2);

        array_push($newKardex, $kar);
    }
    return $newKardex;
}
function kardexSaldoFecha ($kardex, $fechaDesde) {
    $inicialEntradas = 0; 
    $inicialSalidas = 0;
    $inicialSaldoCostoTotal = 0;
    $inicialSaldoCostoUnitario = 0;

    /*$filtro = function($item) use ($fechaDesde, $inicialEntradas, $inicialSalidas, $inicialSaldoCostoTotal, $inicialSaldoCostoUnitario) {
        if ($item['Fecha'] >= $fechaDesde) {
            return true;
        } else {
            $inicialEntradas += (float)($item['IngresoCantidad']);
            $inicialSalidas += (float)($item['SalidaCantidad']);
            $inicialSaldoCostoTotal = $item['saldoCostoTotal'];
            $inicialSaldoCostoUnitario = $item['saldoCostoUnitario'];
            return false;
        }
    };*/
    $newKardex = [];
    $kardexSaldo = kardexSaldo($kardex);
    foreach ($kardexSaldo as $item) {
        if ($item['Fecha'] >= $fechaDesde) {
            array_push($newKardex, $item);
        } else {
            $inicialEntradas += (float)($item['IngresoCantidad']);
            $inicialSalidas += (float)($item['SalidaCantidad']);
            $inicialSaldoCostoTotal = $item['saldoCostoTotal'];
            $inicialSaldoCostoUnitario = $item['saldoCostoUnitario'];
        }
    }
    
    // $newKardex = array_filter(kardexSaldo($kardex), $filtro);

    array_unshift($newKardex, array(
        'Fecha' => $fechaDesde,
        'TipoDocSunat' => 1 ,
        'IngresoCantidad' => $inicialEntradas,
        'IngresoPrecio' => 0,
        'SalidaCantidad' => $inicialSalidas,
        'SalidaPrecio' => 0,
        'saldo' => $inicialEntradas - $inicialSalidas,
        'saldoCostoTotal' => round($inicialSaldoCostoTotal, 2),
        'saldoCostoUnitario' => round($inicialSaldoCostoUnitario, 2)
    ));

    return $newKardex;
}



// TEMPLATE XLXS
$app->get('/reporte/kardexsimple', function (Request $request, Response $response, array $args) use ($app) {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template/formato12.1.xlsx');
    $formato = $spreadsheet->getSheet(0);
    // $formato->getColumnDimension('B')->setAutoSize(true);
    $idProducto = $request->getParam('idProducto');
    $producto = $request->getParam('producto');
    $codigoBarra = $request->getParam('codigoBarra');
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaDesde = $request->getParam('fechaDesde');
    $fechaHasta = $request->getParam('fechaHasta');

    $res = $app->subRequest('GET', '/productos/kardex/' . $idProducto, 'idAlmacen=' . $idAlmacen . '&fechaHasta=' . $fechaHasta);
    $kardex = (string) $res->getBody();
    $kardex = json_decode($kardex, true);
    
    $kardex = kardexSaldoFecha($kardex, $fechaDesde);
    $init = 16;

    foreach($kardex as $kar) {
        $formato->getCell('A'.$init)->setValue($kar['Fecha']);
        $formato->duplicateStyle($formato->getStyle('A'.$init),'A'.($init+1));

        $formato->getCell('B'.$init)->setValue(@$kar['CodSunat']);
        $formato->duplicateStyle($formato->getStyle('B'.$init),'B'.($init+1));

        $formato->getCell('C'.$init)->setValue(@$kar['Serie']);
        $formato->duplicateStyle($formato->getStyle('C'.$init),'C'.($init+1));

        $formato->getCell('D'.$init)->setValue(@$kar['Numero']);
        $formato->duplicateStyle($formato->getStyle('D'.$init),'D'.($init+1));

        $formato->getCell('E'.$init)->setValue($kar['TipoDocSunat']);
        $formato->duplicateStyle($formato->getStyle('E'.$init),'E'.($init+1));

        $formato->getCell('F'.$init)->setValue($kar['IngresoCantidad']);
        $formato->duplicateStyle($formato->getStyle('F'.$init),'F'.($init+1));
        
        $formato->getCell('G'.$init)->setValue($kar['SalidaCantidad']);
        $formato->duplicateStyle($formato->getStyle('G'.$init),'G'.($init+1));

        $formato->getCell('H'.$init)->setValue($kar['saldo']);
        $formato->duplicateStyle($formato->getStyle('H'.$init),'H'.($init+1));

        $init += 1;
    }
    $formato->getCell('A'.($init+2))->setValue('(1) Dirección del Establecimiento o Código según el Registro Único de Contribuyentes.');    

    $formato->getCell('E'.$init)->setValue('TOTALES');    
    $formato->getCell('F'.$init)->setValue('=SUM(F16:F'.($init-1).')');
    $formato->getCell('G'.$init)->setValue('=SUM(G16:G'.($init-1).')');
    $formato->getStyle('E'.$init)->getFont()->setBold( true );
    $formato->getStyle('F'.$init)->getFont()->setBold( true );
    $formato->getStyle('G'.$init)->getFont()->setBold( true );
    
    $formato->getCell('D4')->setValue('Del ' . $fechaDesde . ' al ' . $fechaHasta);
    $formato->getCell('D8')->setValue($codigoBarra);
    $formato->getCell('D10')->setValue($producto);


    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('reporte/formato12.1.xlsx');

    echo '<script type="text/javascript">
        location.href = "/api/reporte/formato12.1.xlsx";
    </script>';

    // return $response->withRedirect('/api/reporte/formato12.1.xlsx');
});

$app->get('/reporte/kardexvalorizado', function (Request $request, Response $response, array $args) use ($app) {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template/formato13.1.xlsx');
    $formato = $spreadsheet->getSheet(0);
    // $formato->getColumnDimension('B')->setAutoSize(true);
    $idProducto = $request->getParam('idProducto');
    $producto = $request->getParam('producto');
    $codigoBarra = $request->getParam('codigoBarra');
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaDesde = $request->getParam('fechaDesde');
    $fechaHasta = $request->getParam('fechaHasta');

    $res = $app->subRequest('GET', '/productos/kardex/' . $idProducto, 'idAlmacen=' . $idAlmacen . '&fechaHasta=' . $fechaHasta);
    $kardex = (string) $res->getBody();
    $kardex = json_decode($kardex, true);
    
    $kardex = kardexSaldoFecha($kardex, $fechaDesde);
    $init = 17;

    $saldoCostoUnitario = 0;
    
    foreach($kardex as $kar) {
        $formato->getCell('A'.$init)->setValue($kar['Fecha']);
        $formato->duplicateStyle($formato->getStyle('A'.$init),'A'.($init+1));

        $formato->getCell('B'.$init)->setValue(@$kar['CodSunat']);
        $formato->duplicateStyle($formato->getStyle('B'.$init),'B'.($init+1));

        $formato->getCell('C'.$init)->setValue(@$kar['Serie']);
        $formato->duplicateStyle($formato->getStyle('C'.$init),'C'.($init+1));

        $formato->getCell('D'.$init)->setValue(@$kar['Numero']);
        $formato->duplicateStyle($formato->getStyle('D'.$init),'D'.($init+1));

        $formato->getCell('E'.$init)->setValue($kar['TipoDocSunat']);
        $formato->duplicateStyle($formato->getStyle('E'.$init),'E'.($init+1));

        $formato->getCell('F'.$init)->setValue($kar['IngresoCantidad']);
        $formato->duplicateStyle($formato->getStyle('F'.$init),'F'.($init+1));

        $formato->getCell('G'.$init)->setValue(@$kar['IngresoPrecio']);
        $formato->duplicateStyle($formato->getStyle('G'.$init),'G'.($init+1));
        
        $formato->getCell('H'.$init)->setValue($kar['IngresoCantidad'] * $kar['IngresoPrecio']);
        $formato->duplicateStyle($formato->getStyle('H'.$init),'H'.($init+1));

        $formato->getCell('I'.$init)->setValue($kar['SalidaCantidad']);
        $formato->duplicateStyle($formato->getStyle('I'.$init),'I'.($init+1));

        $formato->getCell('J'.$init)->setValue(@$kar['SalidaPrecio']);
        $formato->duplicateStyle($formato->getStyle('J'.$init),'J'.($init+1));

        $formato->getCell('K'.$init)->setValue($kar['SalidaCantidad'] * $kar['SalidaPrecio']);
        $formato->duplicateStyle($formato->getStyle('K'.$init),'K'.($init+1));

        $formato->getCell('L'.$init)->setValue($kar['saldo']);
        $formato->duplicateStyle($formato->getStyle('L'.$init),'L'.($init+1));

        $formato->getCell('M'.$init)->setValue($kar['saldoCostoUnitario']);
        $formato->duplicateStyle($formato->getStyle('M'.$init),'M'.($init+1));

        $formato->getCell('N'.$init)->setValue(@$kar['saldoCostoTotal']);
        $formato->duplicateStyle($formato->getStyle('N'.$init),'N'.($init+1));

        $saldoCostoUnitario = $kar['saldoCostoUnitario'];

        $init += 1;
    }
    $formato->getCell('A'.($init+2))->setValue('(1) Dirección del Establecimiento o Código según el Registro Único de Contribuyentes.');    

    $formato->getCell('E'.$init)->setValue('TOTALES');    
    $formato->getCell('F'.$init)->setValue('=SUM(F17:F'.($init-1).')');
    $formato->getCell('H'.$init)->setValue('=SUM(H17:H'.($init-1).')');
    $formato->getCell('I'.$init)->setValue('=SUM(I17:I'.($init-1).')');
    $formato->getCell('K'.$init)->setValue('=SUM(K17:K'.($init-1).')');


    $formato->getStyle('E'.$init)->getFont()->setBold( true );
    $formato->getStyle('F'.$init)->getFont()->setBold( true );
    $formato->getStyle('H'.$init)->getFont()->setBold( true );
    $formato->getStyle('I'.$init)->getFont()->setBold( true );
    $formato->getStyle('K'.$init)->getFont()->setBold( true );
    
    $formato->getCell('D3')->setValue('Del ' . $fechaDesde . ' al ' . $fechaHasta);
    $formato->getCell('D7')->setValue($codigoBarra);
    $formato->getCell('D9')->setValue($producto);


    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('reporte/formato13.1.xlsx');

    echo '<script type="text/javascript">
        location.href = "/api/reporte/formato13.1.xlsx";
    </script>';

    //return $response->withRedirect('/api/reporte/formato13.1.xlsx');
});

$app->get('/reporte/formato37/exportar', function (Request $request, Response $response, array $args) use ($app) {
    /* $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template/formato3.7.xlsx');
    $formato = $spreadsheet->getSheet(0);
    $anio = $request->getParam('anio');
    $select = "SELECT * FROM tmp_formato37";
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();  
    $init = 12;
    foreach($data as $kar) {
        $formato->getCell('A'.$init)->setValue($kar['CodigoBarra']);
        $formato->duplicateStyle($formato->getStyle('A'.$init),'A'.($init+1));
        $formato->getCell('B'.$init)->setValue('01');
        $formato->duplicateStyle($formato->getStyle('B'.$init),'B'.($init+1));
        $formato->getCell('C'.$init)->setValue($kar['Producto']);
        $formato->duplicateStyle($formato->getStyle('C'.$init),'C'.($init+1));
        $formato->getCell('D'.$init)->setValue('07');
        $formato->duplicateStyle($formato->getStyle('D'.$init),'D'.($init+1));
        $formato->getCell('E'.$init)->setValue($kar['Saldo']);
        $formato->duplicateStyle($formato->getStyle('E'.$init),'E'.($init+1));
        $formato->getCell('F'.$init)->setValue($kar['SaldoCostoUnitario']);
        $formato->duplicateStyle($formato->getStyle('F'.$init),'F'.($init+1));
        $formato->getCell('G'.$init)->setValue($kar['SaldoCostoTotal']);
        $formato->duplicateStyle($formato->getStyle('G'.$init),'G'.($init+1));
        $init += 1;
    }
    $formato->getCell('F'.$init)->setValue('COSTO TOTAL GENERAL');    
    $formato->getCell('G'.$init)->setValue('=SUM(G12:G'.($init-1).')');
    $formato->getStyle('F'.$init)->getFont()->setBold( true );
    $formato->getStyle('G'.$init)->getFont()->setBold( true );
    
    $formato->getCell('E4')->setValue($anio);
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('reporte/formato3.7.xlsx'); */

    echo '<script type="text/javascript">
        location.href = "/api/reporte/formato3.7.xlsx";
    </script>';
});

$app->post('/reporte/formato37', function (Request $request, Response $response, array $args) use ($app) {
    $row = $request->getParam('row');
    // var_dump($row);exit();
    $idProductos = $request->getParam('idProductos');
    $idAlmacen = $request->getParam('idAlmacen');
    $anio = $request->getParam('anio');


    $fechaDesde = $anio . '-01-01';
    $fechaHasta = $anio . '-12-31';
    // $fechaDesde = $request->getParam('fechaDesde');
    // $fechaHasta = $request->getParam('fechaHasta');

    
    if (!$row) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template/formato3.7.xlsx');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('reporte/formato3.7.xlsx');
    }

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('reporte/formato3.7.xlsx');
    $formato = $spreadsheet->getSheet(0);
    $init = 12;
    $init += $row;
    
    
    if ($idProductos) {
        $select = "SELECT * FROM Gen_Producto WHERE ControlaStock=1 AND IdProducto IN (" . implode(',', $idProductos) . ")";
        $stmt = $this->db->query($select);
        $stmt->execute();
        $data = $stmt->fetchAll();  
        $productosSaldo = [];
    
        $productosSaldo = array_map(function($prod) use ($app, $idAlmacen, $fechaDesde, $fechaHasta) {
            $idProducto = $prod['IdProducto'];
            $res = $app->subRequest('GET', '/productos/kardex/' . $idProducto, 'idAlmacen=' . $idAlmacen . '&fechaHasta=' . $fechaHasta);
            $kardex = (string) $res->getBody();
            $kardex = json_decode($kardex, true);
            $kardex = kardexSaldoFecha($kardex, $fechaDesde);
            $final = array_merge($prod, end($kardex));
            return $final;
        }, $data); 
    
        foreach($productosSaldo as $kar) {
            /* $insert = "INSERT INTO tmp_formato37 (IdProducto, Producto, CodigoBarra, Saldo, SaldoCostoUnitario, SaldoCostoTotal)
                VALUES ('$kar[IdProducto]', '$kar[Producto]', '$kar[CodigoBarra]', '$kar[saldo]', '$kar[saldoCostoUnitario]', '$kar[saldoCostoTotal]')
                ON DUPLICATE KEY UPDATE Saldo='$kar[saldo]', SaldoCostoUnitario='$kar[saldoCostoUnitario]', SaldoCostoTotal='$kar[saldoCostoTotal]'";
            $stmt = $this->db->prepare($insert);
            $inserted = $stmt->execute();
            $idFormato = $this->db->lastInsertId();*/ 

            $formato->getCell('A'.$init)->setValue($kar['CodigoBarra']);
            $formato->duplicateStyle($formato->getStyle('A'.$init),'A'.($init+1));

            $formato->getCell('B'.$init)->setValue('01');
            $formato->duplicateStyle($formato->getStyle('B'.$init),'B'.($init+1));

            $formato->getCell('C'.$init)->setValue($kar['Producto']);
            $formato->duplicateStyle($formato->getStyle('C'.$init),'C'.($init+1));

            $formato->getCell('D'.$init)->setValue('07');
            $formato->duplicateStyle($formato->getStyle('D'.$init),'D'.($init+1));

            $formato->getCell('E'.$init)->setValue(@$kar['saldo']);
            $formato->duplicateStyle($formato->getStyle('E'.$init),'E'.($init+1));

            $formato->getCell('F'.$init)->setValue(@$kar['saldoCostoUnitario']);
            $formato->duplicateStyle($formato->getStyle('F'.$init),'F'.($init+1));

            $formato->getCell('G'.$init)->setValue(@$kar['saldoCostoTotal']);
            $formato->duplicateStyle($formato->getStyle('G'.$init),'G'.($init+1));

            $init += 1;
        }

        $formato->getCell('F'.$init)->setValue('COSTO TOTAL GENERAL');    
        $formato->getCell('G'.$init)->setValue('=SUM(G12:G'.($init-1).')');


        $config = array('SUMA_VALORIZADO' => $formato->getCell('G'.$init)->getCalculatedValue());
        $insert = "UPDATE GEN_EMPRESA SET CONFIG='" . serialize($config) . "' where IDEMPRESA=1";
        $stmt = $this->db->prepare($insert);
        $stmt->execute();


        // $formato->getStyle('F'.$init)->getFont()->setBold( true );
        // $formato->getStyle('G'.$init)->getFont()->setBold( true );
        
        $formato->getCell('E4')->setValue($anio);


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('reporte/formato3.7.xlsx');
    }

    return;
    //return $response->withRedirect('/api/reporte/formato13.1.xlsx');
});

$app->get('/reporte/articulosmovimientos', function (Request $request, Response $response, array $args) use ($app) {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template/masrotacion.xlsx');
    $formato = $spreadsheet->getSheet(0);
    // $formato->getColumnDimension('B')->setAutoSize(true);
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaDesde = $request->getParam('fechaDesde');
    $fechaHasta = $request->getParam('fechaHasta');

    $res = $app->subRequest('GET', '/productos/masrotacion', 'idAlmacen=' . $idAlmacen . '&fechaDesde=' . $fechaDesde . '&fechaHasta=' . $fechaHasta);
    $productos = (string) $res->getBody();
    $productos = json_decode($productos, true);

    $init = 9;

    foreach($productos as $prod) {
        $formato->getCell('A'.$init)->setValue($prod['CodigoBarra']);
        $formato->duplicateStyle($formato->getStyle('A'.$init),'A'.($init+1));

        $formato->getCell('B'.$init)->setValue($prod['Producto'] . ' - ' . $prod['ProductoCategoria'] . ' - ' . $prod['ProductoMarca']);
        $formato->duplicateStyle($formato->getStyle('B'.$init),'B'.($init+1));

        $formato->getCell('C'.$init)->setValue($prod['ProductoMedicion']);
        $formato->duplicateStyle($formato->getStyle('C'.$init),'C'.($init+1));

        $formato->getCell('D'.$init)->setValue($prod['TotalVentas']);
        $formato->duplicateStyle($formato->getStyle('D'.$init),'D'.($init+1));

        $formato->getCell('E'.$init)->setValue($prod['TotalCompras']);
        $formato->duplicateStyle($formato->getStyle('E'.$init),'E'.($init+1));
        $init += 1;
    }

    $formato->getCell('D3')->setValue('Del ' . $fechaDesde . ' al ' . $fechaHasta);

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('reporte/masrotacion.xlsx');

    echo '<script type="text/javascript">
        location.href = "/api/reporte/masrotacion.xlsx";
    </script>';

    // return $response->withRedirect('/api/reporte/formato12.1.xlsx');
});

$app->get('/reporte/ventasxvendedor', function (Request $request, Response $response, array $args) use ($app) {
    $fechaDesde = $request->getParam('fechaInicio');
    $fechaHasta = $request->getParam('fechaFin');
    $idTipoDoc = $request->getParam('idTipoDoc');
    $usuario = $request->getParam('usuario');
    $idAlmacen = $request->getParam('idAlmacen');
    $almacen = $request->getParam('almacen');

    $select = "SELECT Ve_DocVenta.idDocVenta, Ve_DocVenta.FechaDoc, Ve_DocVentaTipoDoc.TipoDoc,
    Ve_DocVenta.Serie, Ve_DocVenta.Numero, Ve_DocVentaCliente.Cliente, Ve_DocVenta.UsuarioReg,
    IFNULL((SELECT SUM(ROUND((Ve_DocVentaDet.Precio * Ve_DocVentaDet.Cantidad) - Ve_DocVentaDet.Descuento, 2)) FROM Ve_DocVentaDet WHERE Ve_DocVentaDet.IdDocVenta = Ve_DocVenta.idDocVenta), 0 ) AS Total
    FROM Ve_DocVenta
    INNER JOIN Ve_DocVentaTipoDoc ON Ve_DocVenta.IdTipoDoc = Ve_DocVentaTipoDoc.IdTipoDoc
    LEFT JOIN Ve_DocVentaCliente ON Ve_DocVenta.IdCliente = Ve_DocVentaCliente.IdCliente
    WHERE Ve_DocVenta.Anulado = 0 AND Ve_DocVenta.FechaDoc BETWEEN CONCAT('$fechaDesde',' 00:00:00')  AND CONCAT('$fechaHasta',' 23:59:59')";

    if($idTipoDoc!=''){
        $select .=" AND Ve_DocVentaTipoDoc.IdTipoDoc = $idTipoDoc";
    }

    if ($idAlmacen !='') {
        $filtros .= " AND Ve_DocVenta.IdAlmacen =  $idAlmacen";
    }

    $select .=" AND Ve_DocVenta.UsuarioReg  LIKE '%" . $usuario . "%' ";
    $select .=" ORDER BY  Ve_DocVenta.UsuarioReg, Ve_DocVenta.FechaDoc ASC ";
    $stmt = $this->db->query($select);
    $stmt->execute();
    $data = $stmt->fetchAll();

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template/reporteporvendedor.xlsx');
    $formato = $spreadsheet->getSheet(0);
    $init = 12;
    $formato->getColumnDimension('A')->setAutoSize(true);
    $formato->getColumnDimension('B')->setAutoSize(true);
    $formato->getColumnDimension('C')->setAutoSize(true);
    $formato->getColumnDimension('D')->setAutoSize(true);
    $formato->getColumnDimension('E')->setAutoSize(true);
    $formato->getColumnDimension('F')->setAutoSize(true);
    $formato->getColumnDimension('G')->setAutoSize(true);
    $formato->getColumnDimension('H')->setAutoSize(true);

    foreach($data as $value) {
        $formato->getCell('A'.$init)->setValue($value['idDocVenta']);
        $formato->duplicateStyle($formato->getStyle('A'.$init),'A'.($init+1));

        $formato->getCell('B'.$init)->setValue($value['FechaDoc']);
        $formato->duplicateStyle($formato->getStyle('B'.$init),'B'.($init+1));

        $formato->getCell('C'.$init)->setValue($value['TipoDoc']);
        $formato->duplicateStyle($formato->getStyle('C'.$init),'C'.($init+1));

        $formato->getCell('D'.$init)->setValue($value['Serie']);
        $formato->duplicateStyle($formato->getStyle('D'.$init),'D'.($init+1));

        $formato->getCell('E'.$init)->setValue($value['Numero']);
        $formato->duplicateStyle($formato->getStyle('E'.$init),'E'.($init+1));

        $formato->getCell('F'.$init)->setValue(@$value['Cliente']);
        $formato->duplicateStyle($formato->getStyle('F'.$init),'F'.($init+1));

        $formato->getCell('G'.$init)->setValue($value['UsuarioReg']);
        $formato->duplicateStyle($formato->getStyle('G'.$init),'G'.($init+1));

        $formato->getCell('H'.$init)->setValue($value['Total']);
        $formato->duplicateStyle($formato->getStyle('H'.$init),'H'.($init+1));

        if($idTipoDoc!=''){
            $formato->getCell('B8')->setValue($value['TipoDoc']);
        }else{
            $formato->getCell('B8')->setValue('TODOS');
        }

        if($almacen!=''){
            $formato->getCell('B9')->setValue($almacen);
        }else{
            $formato->getCell('B9')->setValue('TODOS');
        }



        $almacen = $request->getParam('almacen');
        $init += 1;
    }

    $formato->getCell('G'.$init)->setValue('SUMA TOTAL:');    
    $formato->getCell('H'.$init)->setValue('=SUM(H12:H'.($init-1).')');
    $formato->getStyle('G'.$init)->getFont()->setBold( true );
    $formato->getStyle('H'.$init)->getFont()->setBold( true );
    $formato->getCell('B5')->setValue($usuario==''?'TODOS':$usuario);
    $formato->getCell('B6')->setValue($fechaDesde);
    $formato->getCell('B7')->setValue($fechaHasta);

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('reporte/reporteporvendedor.xlsx');

    echo '<script type="text/javascript">
        location.href = "/api/reporte/reporteporvendedor.xlsx";
    </script>';

    //return $response->withRedirect('/api/reporte/formato13.1.xlsx');
});

$app->post('/cierrecaja', function (Request $request, Response $response) {
    $idTurno        = $request->getParam('idTurno');
    $user           = $request->getParam('user');
    $fechaCierre    = getNow();
    $ingresos       = $request->getParam('ingresos') ? $request->getParam('ingresos') : 0;
    $salidas        = $request->getParam('salidas') ? $request->getParam('salidas') : 0;
    $total          = $request->getParam('total') ? $request->getParam('total') :0;
    $totalVentas    = $request->getParam('totalVentas') ? $request->getParam('totalVentas') : 0;

    $usuarioReg = 'xx';
    if(isset($_SESSION['user'])) {
        $usuarioReg = $_SESSION['user'];
    }
    $usuarioReg = $user ? $user : $usuarioReg;

    $insert = "INSERT INTO Cb_CierreCaja (FechaCierre, IdTurno, UsuarioReg, Ingresos, Salidas, Ventas, Total)
        VALUES ('$fechaCierre', '$idTurno', '$usuarioReg', $ingresos, $salidas, $totalVentas, $total)";
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

$app->get('/cierrecaja/reporte', function (Request $request, Response $response, array $args) {
    $idAlmacen = $request->getParam('idAlmacen');
    $fechaDesde = $request->getParam('fechaDesde');
    $fechaHasta = $request->getParam('fechaHasta');
    $select = "SELECT Cb_CierreCaja.*, Seg_Usuario.IdAlmacen, Lo_Almacen.Almacen FROM Cb_CierreCaja 
        INNER JOIN Seg_Usuario ON Cb_CierreCaja.UsuarioReg = Seg_Usuario.Usuario 
        INNER JOIN Lo_Almacen ON Seg_Usuario.IdAlmacen = Lo_Almacen.IdAlmacen";
    if ($idAlmacen) {
        $select .= " WHERE Cb_CierreCaja.UsuarioReg IN ( SELECT Usuario FROM Seg_Usuario WHERE IdAlmacen = '$idAlmacen' ) ";
    } else {
        $select .= " WHERE Cb_CierreCaja.UsuarioReg IN ( SELECT Usuario FROM Seg_Usuario ) ";
    }
    $select .= " AND FechaCierre >= '$fechaDesde' AND FechaCierre <= '$fechaHasta' ORDER BY IdCierreCaja DESC";

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
        Ve_DocVenta.EsCredito,Ve_DocVentaTipoDoc.CodSunat,
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

    if($idDocVentas) {
        $select = "SELECT Cb_CajaBanco.IdCajaBanco, Cb_TipoCajaBanco.Tipo, Cb_CajaBanco.IdCuenta, Cb_Cuenta.Cuenta, Cb_CajaBanco.FechaDoc, 
        Cb_CajaBanco.Concepto  , Cb_CajaBanco.Importe, Ve_DocVenta.idDocVenta, Ve_DocVenta.Serie, Ve_DocVenta.Numero,
        Ve_DocVenta.Anulado AS AnuladoVenta, Ve_DocVenta.EsCredito AS EsCreditoVenta
          FROM Cb_CajaBanco
          INNER JOIN Cb_TipoCajaBanco ON Cb_CajaBanco.IdTipoCajaBanco = Cb_TipoCajaBanco.IdTipoCajaBanco
          INNER JOIN Cb_Cuenta ON Cb_CajaBanco.IdCuenta = Cb_Cuenta.IdCuenta
          LEFT JOIN Ve_PreOrden ON Cb_CajaBanco.IdPreOrden = Ve_PreOrden.IdPreOrden
          LEFT JOIN Ve_DocVenta ON Ve_PreOrden.IdDocVenta = Ve_DocVenta.idDocVenta";


          $select .= " WHERE Ve_DocVenta.idDocVenta IN ( " . implode(',', $idDocVentas) . " )";

      $select .= " AND Cb_TipoCajaBanco.Tipo = 0
          ORDER BY FechaDoc ASC;";

      $stmt = $this->db->query($select);
      $stmt->execute();
      $data = $stmt->fetchAll();


    } else {
        $data = [];
    }

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
