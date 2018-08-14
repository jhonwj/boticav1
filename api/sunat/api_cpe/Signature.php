<?php

/*
 * 
 * https://github.com/formapro/xmlseclib ====>librerias
  pagina de ejemplo
  http://xmlstackoverflow.blogspot.pe/2015/01/signing-xml-with-php-xmlseclibsphp.html  ====>ejemplo firma
 * 
  https://stackoverflow.com/questions/35565255/php-xmlseclibs-cant-sign-the-node-i-want ====ejemplo donde colocar firma
 */

require_once('libFirma/XMLSecurityKey.php');
require_once('libFirma/XMLSecurityDSig.php');
require_once('libFirma/XMLSecEnc.php');

//$xml_semilla = "<getToken>\n\t<item>\n\t\t<semilla></semilla>\n\t</item>\n</getToken>";
function Signature($flg_firma, $ruta, $ruta_firma, $pass_firma) {
    //====$flg_firma(NOS INDICARA EN QUE FIRMO EN EL NODO CERO O 1 ESO DEPENDE DEL DOCUMENTO)
    //SI ES (01,03,07,08) FIRMO EN EL NODO UNO 
    //SI ES COMPROBANTE DE PERCEPCION O RETENCION FIRMO EN NODO CERO
    
    
    
    $doc = new DOMDocument();

    $doc->formatOutput = FALSE;
    $doc->preserveWhiteSpace = TRUE;
    $doc->load(dirname(__FILE__) . '/' . $ruta . '.XML');

    $objDSig = new XMLSecurityDSig(FALSE);
    $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
    $options['force_uri'] = TRUE;
    $options['id_name'] = 'ID';
    $options['overwrite'] = FALSE;

    $objDSig->addReference($doc, XMLSecurityDSig::SHA1, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'), $options);
    $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'private'));

    $pfx = file_get_contents(dirname(__FILE__) . "/" . $ruta_firma);
    $key = array();

    openssl_pkcs12_read($pfx, $key, $pass_firma);
    $objKey->loadKey($key["pkey"]);
    $objDSig->add509Cert($key["cert"], TRUE, FALSE);
    $objDSig->sign($objKey, $doc->documentElement->getElementsByTagName("ExtensionContent")->item($flg_firma));

    $atributo = $doc->getElementsByTagName('Signature')->item(0);
    $atributo->setAttribute('Id', 'SignatureSP');
    
    //===================rescatamos Codigo(HASH_CPE)==================
    $hash_cpe = $doc->getElementsByTagName('DigestValue')->item(0)->nodeValue;

    $doc->save(dirname(__FILE__) . '/' . $ruta . '.XML');
    
    //echo $hash_cpe;
        
    return $hash_cpe;
}

//Signature('0','BETA/20100078792-20-R003-642','FIRMABETA/CDAVON2017.pfx','Avon2017');
?>