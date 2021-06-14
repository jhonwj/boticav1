<?php

error_reporting(E_ALL ^ E_NOTICE);
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
// Se incluye el archivo que contiene la clase generica
require_once('config_cpe.php');
require_once('../funcionesGlobales/validaciones.php');

//$array = explode("/", $_SERVER['REQUEST_URI']);
$bodyRequest = file_get_contents("php://input");

// Decodifica el cuerpo de la solicitud y lo guarda en un array de PHP
$cab = json_decode($bodyRequest, true);
$detalle = $cab['detalle'];

//==================PARCEAMOS LA CABECERA===================
    /*CAMPOS NUEVO:
                    TIPO_OPERACION
                    FECHA_VTO
                    POR_IGV
                    CONTACTO_EMPRESA
                    *-------nuevos campos cliente (no obligatorios)-------
                    COD_UBIGEO_CLIENTE
                    DEPARTAMENTO_CLIENTE
                    PROVINCIA_CLIENTE
                    DISTRITO_CLIENTE
    */  
$cabecera = array(
    'txtTIPO_OPERACION' => (isset($cab['txtTIPO_OPERACION'])) ? $cab['txtTIPO_OPERACION'] : "",
    'txtTOTAL_GRAVADAS' => (isset($cab['txtTOTAL_GRAVADAS'])) ? $cab['txtTOTAL_GRAVADAS'] : "0",
    'txtTOTAL_INAFECTA' => (isset($cab['txtTOTAL_INAFECTA'])) ? $cab['txtTOTAL_INAFECTA'] : "0",
    'txtTOTAL_EXONERADAS' => (isset($cab['txtTOTAL_EXONERADAS'])) ? $cab['txtTOTAL_EXONERADAS'] : "0",
    'txtTOTAL_GRATUITAS' => (isset($cab['txtTOTAL_GRATUITAS'])) ? $cab['txtTOTAL_GRATUITAS'] : "0",
    'txtTOTAL_PERCEPCIONES' => (isset($cab['txtTOTAL_PERCEPCIONES'])) ? $cab['txtTOTAL_PERCEPCIONES'] : "0",
    'txtTOTAL_RETENCIONES' => (isset($cab['txtTOTAL_RETENCIONES'])) ? $cab['txtTOTAL_RETENCIONES'] : "0",
    'txtTOTAL_DETRACCIONES' => (isset($cab['txtTOTAL_DETRACCIONES'])) ? $cab['txtTOTAL_DETRACCIONES'] : "0",
    'txtTOTAL_BONIFICACIONES' => (isset($cab['txtTOTAL_BONIFICACIONES'])) ? $cab['txtTOTAL_BONIFICACIONES'] : "0",
    'txtTOTAL_EXPORTACION' => (isset($cab['txtTOTAL_EXPORTACION'])) ? $cab['txtTOTAL_EXPORTACION'] : "0",
    'txtTOTAL_DESCUENTO' => (isset($cab['txtTOTAL_DESCUENTO'])) ? $cab['txtTOTAL_DESCUENTO'] : "0",
    'txtSUB_TOTAL' => (isset($cab['txtSUB_TOTAL'])) ? $cab['txtSUB_TOTAL'] : "0",
    'txtPOR_IGV' => (isset($cab['txtPOR_IGV'])) ? $cab['txtPOR_IGV'] : "0",//campo nuevo
    'txtTOTAL_IGV' => (isset($cab['txtTOTAL_IGV'])) ? $cab['txtTOTAL_IGV'] : "0",
    'txtTOTAL_ISC' => (isset($cab['txtTOTAL_ISC'])) ? $cab['txtTOTAL_ISC'] : "0",
    'txtTOTAL_OTR_IMP' => (isset($cab['txtTOTAL_OTR_IMP'])) ? $cab['txtTOTAL_OTR_IMP'] : "0",
    'txtTOTAL' => (isset($cab['txtTOTAL'])) ? $cab['txtTOTAL'] : "0",
    'txtTOTAL_LETRAS' => $cab['txtTOTAL_LETRAS'],
    //=======================otros documentos, guia remision=============================   
    'txtNRO_GUIA_REMISION' => (isset($cab['txtNRO_GUIA_REMISION'])) ? $cab['txtNRO_GUIA_REMISION'] : "",
    'txtCOD_GUIA_REMISION' => (isset($cab['txtCOD_GUIA_REMISION'])) ? $cab['txtCOD_GUIA_REMISION'] : "",
    'txtNRO_OTR_COMPROBANTE' => (isset($cab['txtNRO_OTR_COMPROBANTE'])) ? $cab['txtNRO_OTR_COMPROBANTE'] : "",
    'txtCOD_OTR_COMPROBANTE' => (isset($cab['txtCOD_OTR_COMPROBANTE'])) ? $cab['txtCOD_OTR_COMPROBANTE'] : "",
    //==============================================
    'txtTIPO_COMPROBANTE_MODIFICA' => (isset($cab['txtTIPO_COMPROBANTE_MODIFICA'])) ? $cab['txtTIPO_COMPROBANTE_MODIFICA'] : "",
    'txtNRO_DOCUMENTO_MODIFICA' => (isset($cab['txtNRO_DOCUMENTO_MODIFICA'])) ? $cab['txtNRO_DOCUMENTO_MODIFICA'] : "",
    'txtCOD_TIPO_MOTIVO' => (isset($cab['txtCOD_TIPO_MOTIVO'])) ? $cab['txtCOD_TIPO_MOTIVO'] : "",
    'txtDESCRIPCION_MOTIVO' => (isset($cab['txtDESCRIPCION_MOTIVO'])) ? $cab['txtDESCRIPCION_MOTIVO'] : "",
    //===============================================
    'txtNRO_COMPROBANTE' => $cab['txtNRO_COMPROBANTE'],
    'txtFECHA_DOCUMENTO' => $cab['txtFECHA_DOCUMENTO'],
    'txtFECHA_VTO' => $cab['txtFECHA_VTO'],
    'txtCOD_TIPO_DOCUMENTO' => $cab['txtCOD_TIPO_DOCUMENTO'],
    'txtCOD_MONEDA' => $cab['txtCOD_MONEDA'],
    //==========FORMA DE PAGO=========
    'detalle_forma_pago' => $cab['detalle_forma_pago'],
    //========================datos del cliente=========================
    'txtNRO_DOCUMENTO_CLIENTE' => $cab['txtNRO_DOCUMENTO_CLIENTE'],
    'txtRAZON_SOCIAL_CLIENTE' => ValidarCaracteresInv($cab['txtRAZON_SOCIAL_CLIENTE']),
    'txtTIPO_DOCUMENTO_CLIENTE' => $cab['txtTIPO_DOCUMENTO_CLIENTE'], //RUC
    'txtDIRECCION_CLIENTE' => ValidarCaracteresInv((isset($cab['txtDIRECCION_CLIENTE'])) ? $cab['txtDIRECCION_CLIENTE'] : ""),
    //--------------------nuevos campos cliente
    'txtCOD_UBIGEO_CLIENTE' => (isset($cab['txtCOD_UBIGEO_CLIENTE'])) ? $cab['txtCOD_UBIGEO_CLIENTE'] : "",
    'txtDEPARTAMENTO_CLIENTE' => (isset($cab['txtDEPARTAMENTO_CLIENTE'])) ? $cab['txtDEPARTAMENTO_CLIENTE'] : "",
    'txtPROVINCIA_CLIENTE' => (isset($cab['txtPROVINCIA_CLIENTE'])) ? $cab['txtPROVINCIA_CLIENTE'] : "",
    'txtDISTRITO_CLIENTE' => (isset($cab['txtDISTRITO_CLIENTE'])) ? $cab['txtDISTRITO_CLIENTE'] : "",
    //--------------------fin nuevos campos cliente
    'txtCIUDAD_CLIENTE' => ValidarCaracteresInv((isset($cab['txtCIUDAD_CLIENTE'])) ? $cab['txtCIUDAD_CLIENTE'] : ""),
    'txtCOD_PAIS_CLIENTE' => $cab['txtCOD_PAIS_CLIENTE'],
    //========================datos de la empresa=========================
    'txtNRO_DOCUMENTO_EMPRESA' => $cab['txtNRO_DOCUMENTO_EMPRESA'],
    'txtTIPO_DOCUMENTO_EMPRESA' => $cab['txtTIPO_DOCUMENTO_EMPRESA'], //RUC
    'txtNOMBRE_COMERCIAL_EMPRESA' => ValidarCaracteresInv((isset($cab['txtNOMBRE_COMERCIAL_EMPRESA'])) ? $cab['txtNOMBRE_COMERCIAL_EMPRESA'] : ""),
    'txtCODIGO_UBIGEO_EMPRESA' => $cab['txtCODIGO_UBIGEO_EMPRESA'],
    'txtDIRECCION_EMPRESA' => (isset($cab['txtDIRECCION_EMPRESA'])) ? $cab['txtDIRECCION_EMPRESA'] : "",
    'txtDEPARTAMENTO_EMPRESA' => (isset($cab['txtDEPARTAMENTO_EMPRESA'])) ? $cab['txtDEPARTAMENTO_EMPRESA'] : "",
    'txtPROVINCIA_EMPRESA' => (isset($cab['txtPROVINCIA_EMPRESA'])) ? $cab['txtPROVINCIA_EMPRESA'] : "",
    'txtDISTRITO_EMPRESA' => (isset($cab['txtDISTRITO_EMPRESA'])) ? $cab['txtDISTRITO_EMPRESA'] : "",
    'txtCODIGO_PAIS_EMPRESA' => $cab['txtCODIGO_PAIS_EMPRESA'],
    'txtRAZON_SOCIAL_EMPRESA' => ValidarCaracteresInv($cab['txtRAZON_SOCIAL_EMPRESA']),
    'txtCONTACTO_EMPRESA' => ValidarCaracteresInv((isset($cab['txtCONTACTO_EMPRESA'])) ? $cab['txtCONTACTO_EMPRESA'] : ""),//nuevo campo
    //====================INFORMACION PARA ANTICIPO=====================//
    'txtFLG_ANTICIPO' => (isset($cab['txtFLG_ANTICIPO'])) ? $cab['txtFLG_ANTICIPO'] : "0",
    //====================REGULAR ANTICIPO=====================//
    'txtFLG_REGU_ANTICIPO' => (isset($cab['txtFLG_REGU_ANTICIPO'])) ? $cab['txtFLG_REGU_ANTICIPO'] : "0",
    'txtNRO_COMPROBANTE_REF_ANT' => (isset($cab['txtNRO_COMPROBANTE_REF_ANT'])) ? $cab['txtNRO_COMPROBANTE_REF_ANT'] : "",
    'txtMONEDA_REGU_ANTICIPO' => (isset($cab['txtMONEDA_REGU_ANTICIPO'])) ? $cab['txtMONEDA_REGU_ANTICIPO'] : "",
    'txtMONTO_REGU_ANTICIPO' => (isset($cab['txtMONTO_REGU_ANTICIPO'])) ? $cab['txtMONTO_REGU_ANTICIPO'] : "0",
    'txtTIPO_DOCUMENTO_EMP_REGU_ANT' => (isset($cab['txtTIPO_DOCUMENTO_EMP_REGU_ANT'])) ? $cab['txtTIPO_DOCUMENTO_EMP_REGU_ANT'] : "",
    'txtNRO_DOCUMENTO_EMP_REGU_ANT' => (isset($cab['txtNRO_DOCUMENTO_EMP_REGU_ANT'])) ? $cab['txtNRO_DOCUMENTO_EMP_REGU_ANT'] : "",
//====================DATOS SUNAT=====================//
    "txtUSUARIO_SOL_EMPRESA" => (isset($cab['txtUSUARIO_SOL_EMPRESA'])) ? $cab['txtUSUARIO_SOL_EMPRESA'] : "MODDATOS",
    "txtPASS_SOL_EMPRESA" => (isset($cab['txtPASS_SOL_EMPRESA'])) ? $cab['txtPASS_SOL_EMPRESA'] : "moddatos",
    "txtTIPO_PROCESO" => (isset($cab['txtTIPO_PROCESO'])) ? $cab['txtTIPO_PROCESO'] : "3",    
);

// 123456 es el PIN
$mensaje_cpe = cpe($cab['txtTIPO_PROCESO'], $cab['txtNRO_DOCUMENTO_EMPRESA'], $cab['txtUSUARIO_SOL_EMPRESA'], $cab['txtPASS_SOL_EMPRESA'], 'ARDIAGGI2021', $cabecera, $detalle);

$resultado['hash_cpe'] = $mensaje_cpe['hash_cpe'];
$resultado['cod_sunat'] = $mensaje_cpe['hash_cdr']['cod_sunat'];//str_replace("SOAP-ENV:CLIENT.", "", $mensaje_cpe['hash_cdr']['cod_sunat']);
$resultado['msj_sunat'] = str_replace("'","",$mensaje_cpe['hash_cdr']['msj_sunat']);
$resultado['hash_cdr'] = $mensaje_cpe['hash_cdr']['hash_cdr'];

print_json($resultado);

function print_json($data) {
    header("HTTP/1.1");
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data, JSON_PRETTY_PRINT);
}

?>