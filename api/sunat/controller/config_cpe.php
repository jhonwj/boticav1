<?php

require_once('../api_cpe/CPESunat_UBL21.php');
require_once('../api_cpe/Signature.php');
require_once('../api_cpe/cpe_envio.php');

function cpe(
//===============DATOS DE LA EMPRESA===============
$tipo_proceso, $ruc, $usuario_sol, $pass_sol, $pass_firma,
 //=============DATOS DEL COMPROBANTE============
        $cab,
 //=============detalle==============
        $detalle
) {

    //===============mensajes==============
    $mensaje_xml = "";
    $hash_cpe = ""; //hash_cpe
    $hash_cdr = "";
    //========================variables=======================
    $ruta_archivo = '../api_cpe/';
    $archivo = $ruc . '-' . $cab['txtCOD_TIPO_DOCUMENTO'] . '-' . $cab['txtNRO_COMPROBANTE'];
    $ruta = '';
    $ruta_cdr = '';
    $ruta_firma = '';
    $ruta_ws = '';

    //========================configuracion de(SERVIDOR)=======================
    if ($tipo_proceso == '1') {
        $ruta = $ruta_archivo . 'PRODUCCION/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'PRODUCCION/' . $ruc . "/";
        $ruta_firma = $ruta_archivo . 'FIRMA/' . $ruc . '.pfx';
        $ruta_ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
    }
    if ($tipo_proceso == '2') {
        $ruta = $ruta_archivo . 'HOMOLOGACION/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'HOMOLOGACION/' . $ruc . "/";
        $ruta_firma = $ruta_archivo . 'FIRMA/' . $ruc . '.pfx';
        $ruta_ws = 'https://www.sunat.gob.pe/ol-ti-itcpgem-sqa/billService';
    }
    if ($tipo_proceso == '3') {
        $ruta = $ruta_archivo . 'BETA/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'BETA/' . $ruc . "/";
        if (file_exists('FIRMA/' . $ruc . '.pfx')) {
            $ruta_firma = 'FIRMA/' . $ruc . '.pfx';
        } else {
            $ruta_firma = 'FIRMABETA/FIRMABETA.pfx';
            $pass_firma = '123456';
        }
        $ruta_ws = 'https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService';
    }
    /*CAMPOS NUEVO:
                    TIPO_OPERACION
                    FECHA_VTO
                    POR_IGV
                    CONTACTO_EMPRESA
                    TOTAL_EXPORTACION
                    *-------nuevos campos cliente (no obligatorios)-------
                    COD_UBIGEO_CLIENTE
                    DEPARTAMENTO_CLIENTE
                    PROVINCIA_CLIENTE
                    DISTRITO_CLIENTE
    */    
    $cabecera = array(
        'TIPO_OPERACION' => (isset($cab['txtTIPO_OPERACION'])) ? $cab['txtTIPO_OPERACION'] : "",
        'TOTAL_GRAVADAS' => (isset($cab['txtTOTAL_GRAVADAS'])) ? $cab['txtTOTAL_GRAVADAS'] : "0",
        'TOTAL_INAFECTA' => (isset($cab['txtTOTAL_INAFECTA'])) ? $cab['txtTOTAL_INAFECTA'] : "0",
        'TOTAL_EXONERADAS' => (isset($cab['txtTOTAL_EXONERADAS'])) ? $cab['txtTOTAL_EXONERADAS'] : "0",
        'TOTAL_GRATUITAS' => (isset($cab['txtTOTAL_GRATUITAS'])) ? $cab['txtTOTAL_GRATUITAS'] : "0",
        'TOTAL_PERCEPCIONES' => (isset($cab['txtTOTAL_PERCEPCIONES'])) ? $cab['txtTOTAL_PERCEPCIONES'] : "0",
        'TOTAL_RETENCIONES' => (isset($cab['txtTOTAL_RETENCIONES'])) ? $cab['txtTOTAL_RETENCIONES'] : "0",
        'TOTAL_DETRACCIONES' => (isset($cab['txtTOTAL_DETRACCIONES'])) ? $cab['txtTOTAL_DETRACCIONES'] : "0",
        'TOTAL_BONIFICACIONES' => (isset($cab['txtTOTAL_BONIFICACIONES'])) ? $cab['txtTOTAL_BONIFICACIONES'] : "0",
        'TOTAL_EXPORTACION' => (isset($cab['txtTOTAL_EXPORTACION'])) ? $cab['txtTOTAL_EXPORTACION'] : "0",
        'TOTAL_DESCUENTO' => (isset($cab['txtTOTAL_DESCUENTO'])) ? $cab['txtTOTAL_DESCUENTO'] : "0",
        'SUB_TOTAL' => (isset($cab['txtSUB_TOTAL'])) ? $cab['txtSUB_TOTAL'] : "0",
        'POR_IGV' => (isset($cab['txtPOR_IGV'])) ? $cab['txtPOR_IGV'] : "0",//campo nuevo
        'TOTAL_IGV' => (isset($cab['txtTOTAL_IGV'])) ? $cab['txtTOTAL_IGV'] : "0",
        'TOTAL_ISC' => (isset($cab['txtTOTAL_ISC'])) ? $cab['txtTOTAL_ISC'] : "0",
        'TOTAL_OTR_IMP' => (isset($cab['txtTOTAL_OTR_IMP'])) ? $cab['txtTOTAL_OTR_IMP'] : "0",
        'TOTAL' => (isset($cab['txtTOTAL'])) ? $cab['txtTOTAL'] : "0",
        'TOTAL_LETRAS' => $cab['txtTOTAL_LETRAS'],
        'NRO_GUIA_REMISION' => $cab['txtNRO_GUIA_REMISION'],
        'COD_GUIA_REMISION' => $cab['txtCOD_GUIA_REMISION'],
        'NRO_OTR_COMPROBANTE' => $cab['txtNRO_OTR_COMPROBANTE'],
        'COD_OTR_COMPROBANTE' => $cab['txtCOD_OTR_COMPROBANTE'],
        //==============================================
        'TIPO_COMPROBANTE_MODIFICA' => (isset($cab['txtTIPO_COMPROBANTE_MODIFICA'])) ? $cab['txtTIPO_COMPROBANTE_MODIFICA'] : "",
        'NRO_DOCUMENTO_MODIFICA' => (isset($cab['txtNRO_DOCUMENTO_MODIFICA'])) ? $cab['txtNRO_DOCUMENTO_MODIFICA'] : "",
        'COD_TIPO_MOTIVO' => (isset($cab['txtCOD_TIPO_MOTIVO'])) ? $cab['txtCOD_TIPO_MOTIVO'] : "",
        'DESCRIPCION_MOTIVO' => (isset($cab['txtDESCRIPCION_MOTIVO'])) ? $cab['txtDESCRIPCION_MOTIVO'] : "",
        //===============================================
        'NRO_COMPROBANTE' => $cab['txtNRO_COMPROBANTE'],
        'FECHA_DOCUMENTO' => $cab['txtFECHA_DOCUMENTO'],
        'FECHA_VTO' => $cab['txtFECHA_VTO'],
        'COD_TIPO_DOCUMENTO' => $cab['txtCOD_TIPO_DOCUMENTO'],
        'COD_MONEDA' => $cab['txtCOD_MONEDA'],
        //==========FORMA DE PAGO=========
        'detalle_forma_pago' => $cab['detalle_forma_pago'],
        //======================CLIENTE======================
        'NRO_DOCUMENTO_CLIENTE' => $cab['txtNRO_DOCUMENTO_CLIENTE'],
        'RAZON_SOCIAL_CLIENTE' => $cab['txtRAZON_SOCIAL_CLIENTE'],
        'TIPO_DOCUMENTO_CLIENTE' => $cab['txtTIPO_DOCUMENTO_CLIENTE'], //RUC
        'DIRECCION_CLIENTE' => $cab['txtDIRECCION_CLIENTE'],
        //--------------------nuevos campos cliente
        'COD_UBIGEO_CLIENTE' => (isset($cab['txtCOD_UBIGEO_CLIENTE'])) ? $cab['txtCOD_UBIGEO_CLIENTE'] : "",
        'DEPARTAMENTO_CLIENTE' => (isset($cab['txtDEPARTAMENTO_CLIENTE'])) ? $cab['txtDEPARTAMENTO_CLIENTE'] : "",
        'PROVINCIA_CLIENTE' => (isset($cab['txtPROVINCIA_CLIENTE'])) ? $cab['txtPROVINCIA_CLIENTE'] : "",
        'DISTRITO_CLIENTE' => (isset($cab['txtDISTRITO_CLIENTE'])) ? $cab['txtDISTRITO_CLIENTE'] : "",
        //--------------------fin nuevos campos cliente
        'CIUDAD_CLIENTE' => (isset($cab['txtCIUDAD_CLIENTE'])) ? $cab['txtCIUDAD_CLIENTE'] : "",
        'COD_PAIS_CLIENTE' => $cab['txtCOD_PAIS_CLIENTE'],       
        //======================EMPRESA======================
        'NRO_DOCUMENTO_EMPRESA' => $cab['txtNRO_DOCUMENTO_EMPRESA'],
        'TIPO_DOCUMENTO_EMPRESA' => $cab['txtTIPO_DOCUMENTO_EMPRESA'], //RUC
        'NOMBRE_COMERCIAL_EMPRESA' => $cab['txtNOMBRE_COMERCIAL_EMPRESA'],
        'CODIGO_UBIGEO_EMPRESA' => $cab['txtCODIGO_UBIGEO_EMPRESA'],
        'DIRECCION_EMPRESA' => $cab['txtDIRECCION_EMPRESA'],
        'DEPARTAMENTO_EMPRESA' => $cab['txtDEPARTAMENTO_EMPRESA'],
        'PROVINCIA_EMPRESA' => $cab['txtPROVINCIA_EMPRESA'],
        'DISTRITO_EMPRESA' => $cab['txtDISTRITO_EMPRESA'],
        'CODIGO_PAIS_EMPRESA' => $cab['txtCODIGO_PAIS_EMPRESA'],
        'RAZON_SOCIAL_EMPRESA' => $cab['txtRAZON_SOCIAL_EMPRESA'],
        'CONTACTO_EMPRESA' => $cab['txtCONTACTO_EMPRESA'],//nuevo campo
        //====================INFORMACION PARA ANTICIPO=====================//
        'FLG_ANTICIPO' => (isset($cab['txtFLG_ANTICIPO'])) ? $cab['txtFLG_ANTICIPO'] : "0",
        //====================REGULAR ANTICIPO=====================//
        'FLG_REGU_ANTICIPO' => (isset($cab['txtFLG_REGU_ANTICIPO'])) ? $cab['txtFLG_REGU_ANTICIPO'] : "0",
        'NRO_COMPROBANTE_REF_ANT' => (isset($cab['txtNRO_COMPROBANTE_REF_ANT'])) ? $cab['txtNRO_COMPROBANTE_REF_ANT'] : "",
        'MONEDA_REGU_ANTICIPO' => (isset($cab['txtMONEDA_REGU_ANTICIPO'])) ? $cab['txtMONEDA_REGU_ANTICIPO'] : "",
        'MONTO_REGU_ANTICIPO' => (isset($cab['txtMONTO_REGU_ANTICIPO'])) ? $cab['txtMONTO_REGU_ANTICIPO'] : "0",
        'TIPO_DOCUMENTO_EMP_REGU_ANT' => (isset($cab['txtTIPO_DOCUMENTO_EMP_REGU_ANT'])) ? $cab['txtTIPO_DOCUMENTO_EMP_REGU_ANT'] : "",
        'NRO_DOCUMENTO_EMP_REGU_ANT' => (isset($cab['txtNRO_DOCUMENTO_EMP_REGU_ANT'])) ? $cab['txtNRO_DOCUMENTO_EMP_REGU_ANT'] : ""
    );

    //=======================creacion: factura, firma, envio======================
    $flg_firma = "1";
    if ($cab['txtCOD_TIPO_DOCUMENTO'] == '01') {
        $mensaje_xml = cpeFactura($ruta, $cabecera, $detalle);
        $flg_firma = "0";
    }
    if ($cab['txtCOD_TIPO_DOCUMENTO'] == '03') {
        $mensaje_xml = cpeFactura($ruta, $cabecera, $detalle);
        $flg_firma = "0";
    }
    if ($cab['txtCOD_TIPO_DOCUMENTO'] == '07') {
        $mensaje_xml = cpeNC($ruta, $cabecera, $detalle);
        $flg_firma = "0";
    }
    if ($cab['txtCOD_TIPO_DOCUMENTO'] == '08') {
        $mensaje_xml = cpeND($ruta, $cabecera, $detalle);
        $flg_firma = "0";
    }
    $hash_cpe = Signature($flg_firma, $ruta, $ruta_firma, $pass_firma);
    $mensaje_envio = cpeEnvio($ruc, $usuario_sol, $pass_sol, $ruta, $ruta_cdr, $archivo, $ruta_ws);

    $response['mensaje_xml'] = $mensaje_xml;
    $response['hash_cpe'] = $hash_cpe;
    $response['hash_cdr'] = $mensaje_envio;

    return $response;
}

function cpeBaja(
//===============DATOS DE LA EMPRESA===============
$tipo_proceso, $ruc, $usuario_sol, $pass_sol, $pass_firma,
 //=============DATOS DEL COMPROBANTE============
        $cab,
 //=============detalle==============
        $detalle
) {

    //===============mensajes==============
    $mensaje_xml = "";
    $hash_cpe = ""; //hash_cpe
    $hash_cdr = "";
    //========================variables=======================
    $ruta_archivo = '../api_cpe/';
    $archivo = $ruc . '-' . $cab['CODIGO'] . '-' . $cab['SERIE'] . '-' . $cab['SECUENCIA'];
    $ruta = '';
    $ruta_cdr = '';
    $ruta_firma = '';
    $ruta_ws = '';

    //========================configuracion de(SERVIDOR)=======================
    if ($tipo_proceso == '1') {
        $ruta = $ruta_archivo . 'PRODUCCION/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'PRODUCCION/' . $ruc . "/";
        $ruta_firma = $ruta_archivo . 'FIRMA/' . $ruc . '.pfx';
        $ruta_ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
    }
    if ($tipo_proceso == '2') {
        $ruta = $ruta_archivo . 'HOMOLOGACION/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'HOMOLOGACION/' . $ruc . "/";
        $ruta_firma = $ruta_archivo . 'FIRMA/' . $ruc . '.pfx';
        $ruta_ws = 'https://www.sunat.gob.pe/ol-ti-itcpgem-sqa/billService';
    }
    if ($tipo_proceso == '3') {
        $ruta = $ruta_archivo . 'BETA/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'BETA/' . $ruc . "/";
        if (file_exists('FIRMA/' . $ruc . '.pfx')) {
            $ruta_firma = 'FIRMA/' . $ruc . '.pfx';
        } else {
            $ruta_firma = 'FIRMABETA/FIRMABETA.pfx';
            $pass_firma = '123456';
        }
        $ruta_ws = 'https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService';
    }
    //=======================creacion: factura, firma, envio======================
    $flg_firma = "0";
    if ($cab['CODIGO'] == 'RA') {
        $mensaje_xml = cpeBajaSunat($ruta, $cab, $detalle);
        $flg_firma = "0";
    }
    $hash_cpe = Signature($flg_firma, $ruta, $ruta_firma, $pass_firma);
    $mensaje_envio = cpeEnvioBaja($ruc, $usuario_sol, $pass_sol, $ruta, $ruta_cdr, $archivo, $ruta_ws);

    $response['mensaje_xml'] = $mensaje_xml;
    $response['hash_cpe'] = $hash_cpe;
    $response['hash_cdr'] = $mensaje_envio;

    return $response;
}


function ResumenBoleta(
//===============DATOS DE LA EMPRESA===============
$tipo_proceso, $ruc, $usuario_sol, $pass_sol, $pass_firma,
 //=============DATOS DEL COMPROBANTE============
        $cab,
 //=============detalle==============
        $detalle
) {

    //===============mensajes==============
    $mensaje_xml = "";
    $hash_cpe = ""; //hash_cpe
    $hash_cdr = "";
    //========================variables=======================
    $ruta_archivo = '../api_cpe/';
    $archivo = $ruc . '-' . $cab['CODIGO'] . '-' . $cab['SERIE'] . '-' . $cab['SECUENCIA'];
    $ruta = '';
    $ruta_cdr = '';
    $ruta_firma = '';
    $ruta_ws = '';

    //========================configuracion de(SERVIDOR)=======================
    if ($tipo_proceso == '1') {
        $ruta = $ruta_archivo . 'PRODUCCION/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'PRODUCCION/' . $ruc . "/";
        $ruta_firma = $ruta_archivo . 'FIRMA/' . $ruc . '.pfx';
        $ruta_ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
    }
    if ($tipo_proceso == '2') {
        $ruta = $ruta_archivo . 'HOMOLOGACION/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'HOMOLOGACION/' . $ruc . "/";
        $ruta_firma = $ruta_archivo . 'FIRMA/' . $ruc . '.pfx';
        $ruta_ws = 'https://www.sunat.gob.pe/ol-ti-itcpgem-sqa/billService';
    }
    if ($tipo_proceso == '3') {
        $ruta = $ruta_archivo . 'BETA/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'BETA/' . $ruc . "/";
        if (file_exists('FIRMA/' . $ruc . '.pfx')) {
            $ruta_firma = 'FIRMA/' . $ruc . '.pfx';
        } else {
            $ruta_firma = 'FIRMABETA/FIRMABETA.pfx';
            $pass_firma = '123456';
        }
        $ruta_ws = 'https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService';
    }
    //=======================creacion: factura, firma, envio======================
    $flg_firma = "0";
    if ($cab['CODIGO'] == 'RC') {
        $mensaje_xml = cpeResumenBoleta($ruta, $cab, $detalle);
        $flg_firma = "0";
    }
    $hash_cpe = Signature($flg_firma, $ruta, $ruta_firma, $pass_firma);
    $mensaje_envio = cpeEnvioResumenBoleta($ruc, $usuario_sol, $pass_sol, $ruta, $ruta_cdr, $archivo, $ruta_ws);

    $response['mensaje_xml'] = $mensaje_xml;
    $response['hash_cpe'] = $hash_cpe;
    $response['hash_cdr'] = $mensaje_envio;

    return $response;
}


function cpeConsultaTicket($tipo_proceso, $ruc, $usuario_sol, $pass_sol, $ticket, $tipo_comprobante, $nro_comprobante) {

    //===============mensajes==============
    $mensaje_xml = "";
    $hash_cpe = ""; //hash_cpe
    $hash_cdr = "";
    //========================variables=======================
    $ruta_archivo = '../api_cpe/';
    $archivo = $ruc . '-' . $tipo_comprobante . '-' . $nro_comprobante;
    $ruta = '';
    $ruta_cdr = '';
    $ruta_firma = '';
    $ruta_ws = '';

    //========================configuracion de(SERVIDOR)=======================
    if ($tipo_proceso == '1') {
        $ruta = $ruta_archivo . 'PRODUCCION/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'PRODUCCION/' . $ruc . "/";
        $ruta_firma = $ruta_archivo . 'FIRMA/' . $ruc . '.pfx';
        $ruta_ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
    }
    if ($tipo_proceso == '2') {
        $ruta = $ruta_archivo . 'HOMOLOGACION/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'HOMOLOGACION/' . $ruc . "/";
        $ruta_firma = $ruta_archivo . 'FIRMA/' . $ruc . '.pfx';
        $ruta_ws = 'https://www.sunat.gob.pe/ol-ti-itcpgem-sqa/billService';
    }
    if ($tipo_proceso == '3') {
        $ruta = $ruta_archivo . 'BETA/' . $ruc . "/" . $archivo;
        $ruta_cdr = $ruta_archivo . 'BETA/' . $ruc . "/";
        if (file_exists('FIRMA/' . $ruc . '.pfx')) {
            $ruta_firma = 'FIRMA/' . $ruc . '.pfx';
        } else {
            $ruta_firma = 'FIRMABETA/FIRMABETA.pfx';
            $pass_firma = '123456';
        }
        $ruta_ws = 'https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService';
    }
  
    $mensaje_envio = consultaEnvioTicket($ruc, $usuario_sol, $pass_sol,$ticket,$archivo,$ruta_cdr, $ruta_ws);

    $response['mensaje_xml'] = $mensaje_xml;
    $response['hash_cpe'] = $hash_cpe;
    $response['hash_cdr'] = $mensaje_envio;

    return $response;
}


?>