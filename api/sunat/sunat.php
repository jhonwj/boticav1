<?php 
class api_sunat {
	
    public function creaPDF($data){
    
        $rutapdf = $_SERVER['HTTP_HOST'] . '/api/sunat/plugins/dompdf/';	
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $rutapdf."index.php?",
            CURLOPT_USERAGENT => "Codular Sample cURL Request",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        curl_close($curl);
    }
        
    public function creaPDFNota($id, $arch){
    
        $rutapdf=$arch.'/plugins/dompdf/';	
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $rutapdf."nota.php?id=".$id,
            CURLOPT_USERAGENT => "Codular Sample cURL Request"
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        curl_close($curl);
    }
    
    public function sendPostCPE($data) {
        
        $headers = array(
            "Content-Type: application/json; charset=UTF-8",
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        );
        $ch = curl_init($_SERVER['HTTP_HOST'] . "/api/sunat/controller/controller_cpe.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, "PRUEBA:LOG");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        // Se cierra el recurso CURL y se liberan los recursos del sistema
        curl_close($ch);
        if (!$response) {
            return false;
        } else {
            return $response;
        }
        //return $data;
    }
    
    public function sendresumen($data) {
            
        $headers = array(
            "Content-Type: application/json; charset=UTF-8",
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        );

        $ch = curl_init($_SERVER['HTTP_HOST'] . "/api/sunat/controller/controller_cpe_resumen_boleta.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, "PRUEBA:LOG");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        // Se cierra el recurso CURL y se liberan los recursos del sistema
        curl_close($ch);
        if (!$response) {
            return false;
        } else {
            return $response;
        }
        //return $data;
    }
    
    public function sendbaja($data) {
            
        $headers = array(
            "Content-Type: application/json; charset=UTF-8",
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        );

        $ch = curl_init($_SERVER['HTTP_HOST'] . "/api/sunat/controller/controller_cpe_baja.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, "PRUEBA:LOG");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        // Se cierra el recurso CURL y se liberan los recursos del sistema
        curl_close($ch);
        if (!$response) {
            return false;
        } else {
            return $response;
        }
        
    
        //return $data;
    }
    
        
    public function sendticket($data) {
            
        $headers = array(
            "Content-Type: application/json; charset=UTF-8",
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        );
        $ch = curl_init($_SERVER['HTTP_HOST'] . "/api/sunat/controller/controller_cpe_ticket.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, "PRUEBA:LOG");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        // Se cierra el recurso CURL y se liberan los recursos del sistema
        curl_close($ch);
        if (!$response) {
            return false;
        } else {
            return $response;
        }
        
        //return $data;
    } 
    
    
}
    
    
    