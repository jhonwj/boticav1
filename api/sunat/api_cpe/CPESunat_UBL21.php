<?php

require_once('../funcionesGlobales/validaciones.php');

function cpeFacturaPrueba($ruta) {
    $doc = new DOMDocument();
    $doc->formatOutput = FALSE;
    $doc->preserveWhiteSpace = TRUE;
    $doc->encoding = 'ISO-8859-1';
    $xmlCPE = '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
xmlns:ccts="urn:un:unece:uncefact:documentation:2"
xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2"
xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<ext:UBLExtensions>
<ext:UBLExtension>
<ext:ExtensionContent>
</ext:ExtensionContent>
</ext:UBLExtension>
</ext:UBLExtensions>
<cbc:UBLVersionID>2.1</cbc:UBLVersionID>
<cbc:CustomizationID>2.0</cbc:CustomizationID>
<cbc:ProfileID schemeName="SUNAT:Identificador de Tipo de Operación"
schemeAgencyName="PE:SUNAT"
schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo17">0101</cbc:ProfileID>
<cbc:ID>FS21-4370</cbc:ID>
<cbc:IssueDate>2017-06-20</cbc:IssueDate>
<cbc:IssueTime>09:12:31</cbc:IssueTime>
<cbc:InvoiceTypeCode listAgencyName="PE:SUNAT" listName="SUNAT:Identificador de Tipo de Documento"
listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">01</cbc:InvoiceTypeCode>
<cbc:Note languageLocaleID="1002">TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO
PRESTADO GRATUITAMENTE</cbc:Note>
<cbc:Note languageLocaleID="3000">0501002017062000451</cbc:Note>
<cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency"
listAgencyName=" United Nations Economic Commission for Europe">PEN</cbc:DocumentCurrencyCode>
<cbc:LineCountNumeric>1</cbc:LineCountNumeric>
<cac:Signature>
<cbc:ID>IDSignKG</cbc:ID>
<cac:SignatoryParty>
<cac:PartyIdentification>
<cbc:ID>20100066603</cbc:ID>
</cac:PartyIdentification>
<cac:PartyName>
<cbc:Name>SOPORTE TECNOLOGICO EIRL</cbc:Name>
</cac:PartyName>
</cac:SignatoryParty>
<cac:DigitalSignatureAttachment>
<cac:ExternalReference>
<cbc:URI>#SignST</cbc:URI>
</cac:ExternalReference>
</cac:DigitalSignatureAttachment>
</cac:Signature>
<cac:AccountingSupplierParty>
<cac:Party>
<cac:PartyName>
<cbc:Name>Tu Soporte</cbc:Name>
</cac:PartyName>
<cac:PartyTaxScheme>
<cbc:RegistrationName>
<![CDATA[Soporte Tecnológicos EIRL]]></cbc:RegistrationName>
<CompanyID schemeID="6" schemeName="SUNAT:Identificador de Documento de
Identidad" schemeAgencyName="PE:SUNAT"
schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">20100066603</CompanyID>
<cac:RegistrationAddress>
<cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">0000</cbc:AddressTypeCode>
</cac:RegistrationAddress>
<cac:TaxScheme>
<cbc:ID>-</cbc:ID>
</cac:TaxScheme>
</cac:PartyTaxScheme>
</cac:Party>
</cac:AccountingSupplierParty>
<cac:AccountingCustomerParty>
<cac:Party>
<cac:PartyTaxScheme>
<cbc:RegistrationName>Boticas y Bazares S. A.</cbc:RegistrationName>
<CompanyID schemeID="6" schemeName="SUNAT:Identificador de Documento de
Identidad" schemeAgencyName="PE:SUNAT"
schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">20889666312</CompanyID>
<cac:TaxScheme>
<cbc:ID>-</cbc:ID>
</cac:TaxScheme>
</cac:PartyTaxScheme>
</cac:Party>
</cac:AccountingCustomerParty>
<cac:TaxTotal>
<cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
<cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
<cac:TaxSubtotal>
<cbc:TaxableAmount currencyID="PEN">1250.00</cbc:TaxableAmount>
<cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
<cac:TaxCategory>
<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier"
schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
<cac:TaxScheme>
<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
<cbc:Name>INAFECTO</cbc:Name>
<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
</cac:TaxScheme>
</cac:TaxCategory>
</cac:TaxSubtotal>
</cac:TaxTotal>
<cac:LegalMonetaryTotal>
<cbc:LineExtensionAmount currencyID="PEN">0.00</cbc:LineExtensionAmount>
<cbc:TaxInclusiveAmount currencyID="PEN">0.00</cbc:TaxInclusiveAmount>
<cbc:AllowanceTotalAmount currencyID="PEN">0.00</cbc:AllowanceTotalAmount>
<cbc:ChargeTotalAmount currencyID="PEN">0.00</cbc:ChargeTotalAmount>
<cbc:PayableAmount currencyID="PEN">0.00</cbc:PayableAmount>
</cac:LegalMonetaryTotal>
<cac:InvoiceLine>
<cbc:ID>1</cbc:ID>
<cbc:InvoicedQuantity unitCode="NIU" unitCodeListID="UN/ECE rec 20"
unitCodeListAgencyName="
Europe">1</cbc:InvoicedQuantity>
<cbc:LineExtensionAmount currencyID="PEN">0.00</cbc:LineExtensionAmount>
<cac:PricingReference>
<cac:AlternativeConditionPrice>
<cbc:PriceAmount currencyID="PEN">1250.00</cbc:PriceAmount>
<cbc:PriceTypeCode listName="SUNAT:Indicador de Tipo de Precio"
listAgencyName= "PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">02</cbc:PriceTypeCode>
</cac:AlternativeConditionPrice>
</cac:PricingReference>
<cac:TaxTotal>
<cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
<cac:TaxSubtotal>
<cbc:TaxableAmount currencyID="PEN">1250.00</cbc:TaxableAmount>
<cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
<cac:TaxCategory>
<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier"
schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
<cbc:Percent>18.00</cbc:Percent>
<cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="SUNAT:Codigo
de Tipo de Afectación del IGV"
listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">35</cbc:TaxExemptionReasonCode>
<cac:TaxScheme>
<cbc:ID schemeID="UN/ECE 5153" schemeName="Tax Scheme Identifier"
schemeAgencyName="United Nations Economic Commission for Europe">9998</cbc:ID>
<cbc:Name>INAFECTO</cbc:Name>
<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
</cac:TaxScheme>
</cac:TaxCategory>
</cac:TaxSubtotal>
</cac:TaxTotal>
<cac:Item>
<cbc:Description>Televisor plasma de 42", marca "RCA"</cbc:Description>
<cac:CommodityClassification>
<cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US"
listName="Item Classification">52161505</cbc:ItemClassificationCode>
</cac:CommodityClassification>
</cac:Item>
<cac:Price>
<cbc:PriceAmount currencyID="PEN">0.00</cbc:PriceAmount>
</cac:Price>
</cac:InvoiceLine>
</Invoice>
            ';

    $doc->loadXML($xmlCPE);
    $doc->save(dirname(__FILE__) . '/' . $ruta . '.XML');

    return '1';
}

function cpeFactura($ruta, $cabecera, $detalle) {
    try {
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        //$doc->encoding = 'ISO-8859-1';
        $doc->encoding = 'utf-8';
        $xmlCPE = '<?xml version="1.0" encoding="utf-8"?>
<Invoice xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2">
	<ext:UBLExtensions>
		<ext:UBLExtension>
			<ext:ExtensionContent>
			</ext:ExtensionContent>
		</ext:UBLExtension>
	</ext:UBLExtensions>
	<cbc:UBLVersionID>2.1</cbc:UBLVersionID>
	<cbc:CustomizationID schemeAgencyName="PE:SUNAT">2.0</cbc:CustomizationID>
	<cbc:ProfileID schemeName="Tipo de Operacion" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51">' . $cabecera["TIPO_OPERACION"] . '</cbc:ProfileID>
	<cbc:ID>' . $cabecera["NRO_COMPROBANTE"] . '</cbc:ID>
	<cbc:IssueDate>' . $cabecera["FECHA_DOCUMENTO"] . '</cbc:IssueDate>
	<cbc:IssueTime>00:00:00</cbc:IssueTime>
	<cbc:DueDate>' . $cabecera["FECHA_VTO"] . '</cbc:DueDate>
	<cbc:InvoiceTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01" listID="0101" name="Tipo de Operacion" listSchemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51">' . $cabecera["COD_TIPO_DOCUMENTO"] . '</cbc:InvoiceTypeCode>';
	if ($cabecera["TOTAL_LETRAS"] <> "") {
            $xmlCPE = $xmlCPE .
                '<cbc:Note languageLocaleID="1000">' . $cabecera["TOTAL_LETRAS"] . '</cbc:Note>';
        }
        $xmlCPE = $xmlCPE .
                '<cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency" listAgencyName="United Nations Economic Commission for Europe">' . $cabecera["COD_MONEDA"] . '</cbc:DocumentCurrencyCode>
            <cbc:LineCountNumeric>' . count($detalle) . '</cbc:LineCountNumeric>';
        if ($cabecera["NRO_OTR_COMPROBANTE"] <> "") {
            $xmlCPE = $xmlCPE .
                    '<cac:OrderReference>
                    <cbc:ID>' . $cabecera["NRO_OTR_COMPROBANTE"] . '</cbc:ID>
            </cac:OrderReference>';
        }
        if ($cabecera["NRO_GUIA_REMISION"] <> "") {
        $xmlCPE = $xmlCPE .
                '<cac:DespatchDocumentReference>
		<cbc:ID>' . $cabecera["NRO_GUIA_REMISION"] . '</cbc:ID>
		<cbc:IssueDate>' . $cabecera["FECHA_GUIA_REMISION"] . '</cbc:IssueDate>
		<cbc:DocumentTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">' . $cabecera["COD_GUIA_REMISION"] . '</cbc:DocumentTypeCode>
            </cac:DespatchDocumentReference>';
        }
        $xmlCPE = $xmlCPE .
            '<cac:Signature>
		<cbc:ID>' . $cabecera["NRO_COMPROBANTE"] . '</cbc:ID>
		<cac:SignatoryParty>
			<cac:PartyIdentification>
				<cbc:ID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
			</cac:PartyIdentification>
			<cac:PartyName>
				<cbc:Name>' . $cabecera["RAZON_SOCIAL_EMPRESA"] . '</cbc:Name>
			</cac:PartyName>
		</cac:SignatoryParty>
		<cac:DigitalSignatureAttachment>
			<cac:ExternalReference>
				<cbc:URI>#' . $cabecera["NRO_COMPROBANTE"] . '</cbc:URI>
			</cac:ExternalReference>
		</cac:DigitalSignatureAttachment>
	</cac:Signature>
	<cac:AccountingSupplierParty>
		<cac:Party>
			<cac:PartyIdentification>
				<cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
			</cac:PartyIdentification>
			<cac:PartyName>
				<cbc:Name><![CDATA[' . $cabecera["NOMBRE_COMERCIAL_EMPRESA"] . ']]></cbc:Name>
			</cac:PartyName>
			<cac:PartyTaxScheme>
				<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:RegistrationName>
				<cbc:CompanyID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:CompanyID>
				<cac:TaxScheme>
					<cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
				</cac:TaxScheme>
			</cac:PartyTaxScheme>
			<cac:PartyLegalEntity>
				<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:RegistrationName>
				<cac:RegistrationAddress>
					<cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI" />
					<cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">0000</cbc:AddressTypeCode>
					<cbc:CityName><![CDATA[' . $cabecera["DEPARTAMENTO_EMPRESA"] . ']]></cbc:CityName>
					<cbc:CountrySubentity><![CDATA[' . $cabecera["PROVINCIA_EMPRESA"] . ']]></cbc:CountrySubentity>
					<cbc:District><![CDATA[' . $cabecera["DISTRITO_EMPRESA"] . ']]></cbc:District>
					<cac:AddressLine>
						<cbc:Line><![CDATA[' . $cabecera["DIRECCION_EMPRESA"] . ']]></cbc:Line>
					</cac:AddressLine>
					<cac:Country>
						<cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country">' . $cabecera["CODIGO_PAIS_EMPRESA"] . '</cbc:IdentificationCode>
					</cac:Country>
				</cac:RegistrationAddress>
			</cac:PartyLegalEntity>
			<cac:Contact>
				<cbc:Name><![CDATA[' . $cabecera["CONTACTO_EMPRESA"] . ']]></cbc:Name>
			</cac:Contact>
		</cac:Party>
	</cac:AccountingSupplierParty>
	<cac:AccountingCustomerParty>
		<cac:Party>
			<cac:PartyIdentification>
				<cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_CLIENTE"] . '</cbc:ID>
			</cac:PartyIdentification>
			<cac:PartyName>
				<cbc:Name><![CDATA[' . $cabecera["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:Name>
			</cac:PartyName>
			<cac:PartyTaxScheme>
				<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
				<cbc:CompanyID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_CLIENTE"] . '</cbc:CompanyID>
				<cac:TaxScheme>
					<cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_CLIENTE"] . '</cbc:ID>
				</cac:TaxScheme>
			</cac:PartyTaxScheme>
			<cac:PartyLegalEntity>
				<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
				<cac:RegistrationAddress>
					<cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">' . $cabecera["COD_UBIGEO_CLIENTE"] . '</cbc:ID>
					<cbc:CityName><![CDATA[' . $cabecera["DEPARTAMENTO_CLIENTE"] . ']]></cbc:CityName>
					<cbc:CountrySubentity><![CDATA[' . $cabecera["PROVINCIA_CLIENTE"] . ']]></cbc:CountrySubentity>
					<cbc:District><![CDATA[' . $cabecera["DISTRITO_CLIENTE"] . ']]></cbc:District>
					<cac:AddressLine>
						<cbc:Line><![CDATA[' . $cabecera["DIRECCION_CLIENTE"] . ']]></cbc:Line>
					</cac:AddressLine>                                        
					<cac:Country>
						<cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country">' . $cabecera["COD_PAIS_CLIENTE"] . '</cbc:IdentificationCode>
					</cac:Country>
				</cac:RegistrationAddress>
			</cac:PartyLegalEntity>
		</cac:Party>
	</cac:AccountingCustomerParty>';

// 	if ($cabecera["TOTAL_DESCUENTO"] > 0) {
// 		$xmlCPE = $xmlCPE .
// 				'<cac:AllowanceCharge>
// 				<cbc:ChargeIndicator>false</cbc:ChargeIndicator>
// 				<cbc:AllowanceChargeReasonCode listName="Cargo/descuento" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo53">00</cbc:AllowanceChargeReasonCode>
// 				<cbc:Amount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_DESCUENTO"] . '</cbc:Amount>
// 				</cac:AllowanceCharge>';
//  }
		
		//forma de pago
		If (count($cabecera["detalle_forma_pago"])==0) {
			$xmlCPE = $xmlCPE .
				'<cac:PaymentTerms>        
				<cbc:ID>FormaPago</cbc:ID>
				<cbc:PaymentMeansID schemeAgencyName="PE:SUNAT">Contado</cbc:PaymentMeansID>
				</cac:PaymentTerms>';
		}else{
			for ($z = 0; $z < count($cabecera["detalle_forma_pago"]); $z++) {
				$xmlCPE = $xmlCPE . "<cac:PaymentTerms>        
				<cbc:ID>FormaPago</cbc:ID>
				<cbc:PaymentMeansID schemeAgencyName='PE:SUNAT'>" . $cabecera["detalle_forma_pago"][$z]["COD_FORMA_PAGO"] . "</cbc:PaymentMeansID>";

				If ($cabecera["detalle_forma_pago"][$z]["MONTO_FORMA_PAGO"]  > 0) {
					$xmlCPE = $xmlCPE .  "<cbc:Amount currencyID='" . $cabecera["COD_MONEDA"] . "'>" . $cabecera["detalle_forma_pago"][$z]["MONTO_FORMA_PAGO"] . "</cbc:Amount>";
				}
				If ($cabecera["detalle_forma_pago"][$z]["FECHA_FORMA_PAGO"]!= "") {
				$xmlCPE = $xmlCPE .  "<cbc:PaymentDueDate>"  . $cabecera["detalle_forma_pago"][$z]["FECHA_FORMA_PAGO"] .  "</cbc:PaymentDueDate>";
				}
				$xmlCPE = $xmlCPE .  " </cac:PaymentTerms>";
			}
		}

	$xmlCPE = $xmlCPE .
	'<cac:TaxTotal>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_IGV"] . '</cbc:TaxAmount>
		<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_GRAVADAS"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_IGV"] . '</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
					<cbc:Name>IGV</cbc:Name>
					<cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
                if ($cabecera["TOTAL_ISC"]>0){
                $xmlCPE = $xmlCPE .
                '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_ISC"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_ISC"] . '</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">2000</cbc:ID>
					<cbc:Name>ISC</cbc:Name>
					<cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
                }
                //CAMPO NUEVO
                if ($cabecera["TOTAL_EXPORTACION"]>0){
                $xmlCPE = $xmlCPE .
                '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_EXPORTACION"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">G</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9995</cbc:ID>
					<cbc:Name>EXP</cbc:Name>
					<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
                }
                if ($cabecera["TOTAL_GRATUITAS"]>0){
                $xmlCPE = $xmlCPE .
                '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_GRATUITAS"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">Z</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
					<cbc:Name>GRA</cbc:Name>
					<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
				}
				// var_dump($cabecera["TOTAL_EXONERADAS"]);exit();
                if ($cabecera["TOTAL_EXONERADAS"]>0){
                $xmlCPE = $xmlCPE .
                '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_EXONERADAS"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
					<cbc:Name>EXO</cbc:Name>
					<cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
                }
                if ($cabecera["TOTAL_INAFECTA"]>0){
                $xmlCPE = $xmlCPE .
                '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_INAFECTA"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
					<cbc:Name>INAFECTO</cbc:Name>
					<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
                }
                if ($cabecera["TOTAL_OTR_IMP"]>0){
                $xmlCPE = $xmlCPE .
                '<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_OTR_IMP"] . '</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_OTR_IMP"] . '</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
				<cac:TaxScheme>
					<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9999</cbc:ID>
					<cbc:Name>OTR</cbc:Name>
					<cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>';
                }
                //TOTAL=GRAVADA+IGV+EXONERADA
                //NO ENTRA GRATUITA(INAFECTA) NI DESCUENTO
                //SUB_TOTAL=PRECIO(SIN IGV) * CANTIDAD
	$xmlCPE = $xmlCPE .
       '</cac:TaxTotal>
	<cac:LegalMonetaryTotal>
		<cbc:LineExtensionAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["SUB_TOTAL"] . '</cbc:LineExtensionAmount>
		<cbc:TaxInclusiveAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL"] . '</cbc:TaxInclusiveAmount>
		<cbc:AllowanceTotalAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_DESCUENTO"] . '</cbc:AllowanceTotalAmount>
		<cbc:ChargeTotalAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:ChargeTotalAmount>
		<cbc:PayableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL"] . '</cbc:PayableAmount>
	</cac:LegalMonetaryTotal>';
    for ($i = 0; $i < count($detalle); $i++) {
		if ($detalle[$i]["txtCOD_TIPO_OPERACION"]=="10"){ 
        $xmlCPE = $xmlCPE . '<cac:InvoiceLine>
		<cbc:ID>' . $detalle[$i]["txtITEM"] . '</cbc:ID>
		<cbc:InvoicedQuantity unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="United Nations Economic Commission for Europe">' . $detalle[$i]["txtCANTIDAD_DET"] . '</cbc:InvoicedQuantity>
		<cbc:LineExtensionAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIMPORTE_DET"] . '</cbc:LineExtensionAmount>
		<cac:PricingReference>
			<cac:AlternativeConditionPrice>
				<cbc:PriceAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtPRECIO_DET"] . '</cbc:PriceAmount>
				<cbc:PriceTypeCode listName="Tipo de Precio" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">' . $detalle[$i]["txtPRECIO_TIPO_CODIGO"] . '</cbc:PriceTypeCode>
			</cac:AlternativeConditionPrice>
		</cac:PricingReference>
		<cac:TaxTotal>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIGV"] . '</cbc:TaxAmount>
			<cac:TaxSubtotal>
				<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIMPORTE_DET"] . '</cbc:TaxableAmount>
				<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIGV"] . '</cbc:TaxAmount>
				<cac:TaxCategory>
					<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
					<cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
					<cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="SUNAT:Codigo de Tipo de Afectación del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
					<cac:TaxScheme>
						<cbc:ID schemeID="UN/ECE 5153" schemeName="Tax Scheme Identifier" schemeAgencyName="United Nations Economic Commission for Europe">1000</cbc:ID>
						<cbc:Name>IGV</cbc:Name>
						<cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
					</cac:TaxScheme>
				</cac:TaxCategory>
			</cac:TaxSubtotal>
		</cac:TaxTotal>
		<cac:Item>
			<cbc:Description><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtDESCRIPCION_DET"])) ? $detalle[$i]["txtDESCRIPCION_DET"] : "") . ']]></cbc:Description>
			<cac:SellersItemIdentification>
				<cbc:ID><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtCODIGO_DET"])) ? $detalle[$i]["txtCODIGO_DET"] : "") . ']]></cbc:ID>
			</cac:SellersItemIdentification>
		</cac:Item>
		<cac:Price>
			<cbc:PriceAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtPRECIO_SIN_IGV_DET"] . '</cbc:PriceAmount>
		</cac:Price>
	</cac:InvoiceLine>';
	}
	if ($detalle[$i]["txtCOD_TIPO_OPERACION"]=="20"){ 
        $xmlCPE = $xmlCPE . '<cac:InvoiceLine>
		<cbc:ID>' . $detalle[$i]["txtITEM"] . '</cbc:ID>
		<cbc:InvoicedQuantity unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="United Nations Economic Commission for Europe">' . $detalle[$i]["txtCANTIDAD_DET"] . '</cbc:InvoicedQuantity>
		<cbc:LineExtensionAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIMPORTE_DET"] . '</cbc:LineExtensionAmount>
		<cac:PricingReference>
			<cac:AlternativeConditionPrice>
				<cbc:PriceAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtPRECIO_DET"] . '</cbc:PriceAmount>
				<cbc:PriceTypeCode listName="Tipo de Precio" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">' . $detalle[$i]["txtPRECIO_TIPO_CODIGO"] . '</cbc:PriceTypeCode>
			</cac:AlternativeConditionPrice>
		</cac:PricingReference>
		<cac:TaxTotal>
			<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIGV"] . '</cbc:TaxAmount>
			<cac:TaxSubtotal>
				<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIMPORTE_DET"] . '</cbc:TaxableAmount>
				<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtIGV"] . '</cbc:TaxAmount>
				<cac:TaxCategory>
					<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
					<cbc:Percent>' . $cabecera["POR_IGV"] . '</cbc:Percent>
					<cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="SUNAT:Codigo de Tipo de Afectación del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $detalle[$i]["txtCOD_TIPO_OPERACION"] . '</cbc:TaxExemptionReasonCode>
					<cac:TaxScheme>
						<cbc:ID schemeID="UN/ECE 5153" schemeName="Tax Scheme Identifier" schemeAgencyName="United Nations Economic Commission for Europe">9997</cbc:ID>
						<cbc:Name>EXO</cbc:Name>
						<cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
					</cac:TaxScheme>
				</cac:TaxCategory>
			</cac:TaxSubtotal>
		</cac:TaxTotal>
		<cac:Item>
			<cbc:Description><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtDESCRIPCION_DET"])) ? $detalle[$i]["txtDESCRIPCION_DET"] : "") . ']]></cbc:Description>
			<cac:SellersItemIdentification>
				<cbc:ID><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtCODIGO_DET"])) ? $detalle[$i]["txtCODIGO_DET"] : "") . ']]></cbc:ID>
			</cac:SellersItemIdentification>
		</cac:Item>
		<cac:Price>
			<cbc:PriceAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $detalle[$i]["txtPRECIO_SIN_IGV_DET"] . '</cbc:PriceAmount>
		</cac:Price>
	</cac:InvoiceLine>';
	}
    }
    $xmlCPE = $xmlCPE . '</Invoice>';

        $doc->loadXML($xmlCPE);
        $doc->save(dirname(__FILE__) . '/' . $ruta . '.XML');
    } catch (Exception $e) {
        //$nombre_archivo = "logs.txt";
        //echo 'Excepción capturada: ', $e->getMessage(), "\n";
        $mensaje = $e->getMessage();
        $file = fopen("logs.txt", "w");
        fwrite($file, $mensaje . PHP_EOL);
        fwrite($file, "Otra más" . PHP_EOL);
        fclose($file);
    }
    return '1';
}

function cpeNC($ruta, $cabecera, $detalle) {
    $doc = new DOMDocument();
    $doc->formatOutput = FALSE;
    $doc->preserveWhiteSpace = TRUE;
	$doc->encoding = 'utf-8';
    //$doc->encoding = 'ISO-8859-1';
    //$doc->encoding = 'utf-8';
    $xmlCPE = '<?xml version="1.0" encoding="UTF-8"?>
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent>
            </ext:ExtensionContent>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2.0</cbc:CustomizationID>
    <cbc:ID>'.$cabecera["NRO_COMPROBANTE"].'</cbc:ID>
    <cbc:IssueDate>'.$cabecera["FECHA_DOCUMENTO"].'</cbc:IssueDate>
    <cbc:IssueTime>00:00:00</cbc:IssueTime>
    <cbc:DocumentCurrencyCode>'.$cabecera["COD_MONEDA"].'</cbc:DocumentCurrencyCode>
    <cac:DiscrepancyResponse>
        <cbc:ReferenceID>'.$cabecera["NRO_DOCUMENTO_MODIFICA"].'</cbc:ReferenceID>
        <cbc:ResponseCode>'.$cabecera["COD_TIPO_MOTIVO"].'</cbc:ResponseCode>
        <cbc:Description><![CDATA['.$cabecera["DESCRIPCION_MOTIVO"].']]></cbc:Description>
    </cac:DiscrepancyResponse>
    <cac:BillingReference>
        <cac:InvoiceDocumentReference>
            <cbc:ID>'.$cabecera["NRO_DOCUMENTO_MODIFICA"].'</cbc:ID>
            <cbc:DocumentTypeCode>'.$cabecera["TIPO_COMPROBANTE_MODIFICA"].'</cbc:DocumentTypeCode>
        </cac:InvoiceDocumentReference>
    </cac:BillingReference>
    <cac:Signature>
        <cbc:ID>IDSignST</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $cabecera["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#SignatureSP</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $cabecera["NOMBRE_COMERCIAL_EMPRESA"] . ']]></cbc:Name>
            </cac:PartyName>
            <cac:PartyLegalEntity>
<cbc:RegistrationName><![CDATA['.$cabecera["RAZON_SOCIAL_EMPRESA"].']]></cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">0000</cbc:AddressTypeCode>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera["NRO_DOCUMENTO_CLIENTE"] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
<cbc:RegistrationName><![CDATA[' . $cabecera["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
		</cac:AccountingCustomerParty>';

	   $xmlCPE = $xmlCPE .
	   '<cac:TaxTotal>
        <cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$cabecera["TOTAL_IGV"].'</cbc:TaxAmount>
        <cac:TaxSubtotal>
<cbc:TaxableAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$cabecera["TOTAL_GRAVADAS"].'</cbc:TaxableAmount>
<cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$cabecera["TOTAL_IGV"].'</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
		</cac:TaxSubtotal>';	

		if ($cabecera["TOTAL_ISC"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_ISC"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_ISC"] . '</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">2000</cbc:ID>
				<cbc:Name>ISC</cbc:Name>
				<cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
			}
			//CAMPO NUEVO
			if ($cabecera["TOTAL_EXPORTACION"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_EXPORTACION"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">G</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9995</cbc:ID>
				<cbc:Name>EXP</cbc:Name>
				<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
			}
			if ($cabecera["TOTAL_GRATUITAS"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_GRATUITAS"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">Z</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
				<cbc:Name>GRA</cbc:Name>
				<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
			}
			// var_dump($cabecera["TOTAL_EXONERADAS"]);exit();
			if ($cabecera["TOTAL_EXONERADAS"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_EXONERADAS"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
				<cbc:Name>EXO</cbc:Name>
				<cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
			}
			if ($cabecera["TOTAL_INAFECTA"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_INAFECTA"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
				<cbc:Name>INAFECTO</cbc:Name>
				<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
			}
			if ($cabecera["TOTAL_OTR_IMP"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_OTR_IMP"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_OTR_IMP"] . '</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9999</cbc:ID>
				<cbc:Name>OTR</cbc:Name>
				<cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
}
//TOTAL=GRAVADA+IGV+EXONERADA
//NO ENTRA GRATUITA(INAFECTA) NI DESCUENTO
//SUB_TOTAL=PRECIO(SIN IGV) * CANTIDAD
$xmlCPE = $xmlCPE .
'</cac:TaxTotal>
    <cac:LegalMonetaryTotal>
        <cbc:PayableAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$cabecera["TOTAL"].'</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>';
	
    

for ($i = 0; $i < count($detalle); $i++) {
	if ($detalle[$i]["txtCOD_TIPO_OPERACION"]=="10"){ 		
$xmlCPE = $xmlCPE .'<cac:CreditNoteLine>
        <cbc:ID>'.$detalle[$i]["txtITEM"].'</cbc:ID>
<cbc:CreditedQuantity unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '">' . $detalle[$i]["txtCANTIDAD_DET"] . '</cbc:CreditedQuantity>
<cbc:LineExtensionAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIMPORTE_DET"].'</cbc:LineExtensionAmount>
        <cac:PricingReference>
            <cac:AlternativeConditionPrice>
<cbc:PriceAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtPRECIO_DET"].'</cbc:PriceAmount>
                <cbc:PriceTypeCode>'.$detalle[$i]["txtPRECIO_TIPO_CODIGO"].'</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
        </cac:PricingReference>
        <cac:TaxTotal>
<cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIGV"].'</cbc:TaxAmount>
            <cac:TaxSubtotal>
<cbc:TaxableAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIMPORTE_DET"].'</cbc:TaxableAmount>
<cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIGV"].'</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:Percent>'.$cabecera["POR_IGV"].'</cbc:Percent>
<cbc:TaxExemptionReasonCode>'.$detalle[$i]["txtCOD_TIPO_OPERACION"].'</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID>1000</cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        <cac:Item>
<cbc:Description><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtDESCRIPCION_DET"]))?$detalle[$i]["txtDESCRIPCION_DET"]:"") . ']]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtCODIGO_DET"]))?$detalle[$i]["txtCODIGO_DET"]:"") . ']]></cbc:ID>
            </cac:SellersItemIdentification>
        </cac:Item>
        <cac:Price>
<cbc:PriceAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtPRECIO_DET"].'</cbc:PriceAmount>
        </cac:Price>
    </cac:CreditNoteLine>';
}
if ($detalle[$i]["txtCOD_TIPO_OPERACION"]=="20"){ 		
	$xmlCPE = $xmlCPE .'<cac:CreditNoteLine>
			<cbc:ID>'.$detalle[$i]["txtITEM"].'</cbc:ID>
	<cbc:CreditedQuantity unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '">' . $detalle[$i]["txtCANTIDAD_DET"] . '</cbc:CreditedQuantity>
	<cbc:LineExtensionAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIMPORTE_DET"].'</cbc:LineExtensionAmount>
			<cac:PricingReference>
				<cac:AlternativeConditionPrice>
	<cbc:PriceAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtPRECIO_DET"].'</cbc:PriceAmount>
					<cbc:PriceTypeCode>'.$detalle[$i]["txtPRECIO_TIPO_CODIGO"].'</cbc:PriceTypeCode>
				</cac:AlternativeConditionPrice>
			</cac:PricingReference>
			<cac:TaxTotal>
	<cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIGV"].'</cbc:TaxAmount>
				<cac:TaxSubtotal>
	<cbc:TaxableAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIMPORTE_DET"].'</cbc:TaxableAmount>
	<cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIGV"].'</cbc:TaxAmount>
					<cac:TaxCategory>
						<cbc:Percent>0</cbc:Percent>
	<cbc:TaxExemptionReasonCode>'.$detalle[$i]["txtCOD_TIPO_OPERACION"].'</cbc:TaxExemptionReasonCode>
						<cac:TaxScheme>
							<cbc:ID>9997</cbc:ID>
							<cbc:Name>EXO</cbc:Name>
							<cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
						</cac:TaxScheme>
					</cac:TaxCategory>
				</cac:TaxSubtotal>
			</cac:TaxTotal>
			<cac:Item>
	<cbc:Description><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtDESCRIPCION_DET"]))?$detalle[$i]["txtDESCRIPCION_DET"]:"") . ']]></cbc:Description>
				<cac:SellersItemIdentification>
					<cbc:ID><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtCODIGO_DET"]))?$detalle[$i]["txtCODIGO_DET"]:"") . ']]></cbc:ID>
				</cac:SellersItemIdentification>
			</cac:Item>
			<cac:Price>
	<cbc:PriceAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtPRECIO_DET"].'</cbc:PriceAmount>
			</cac:Price>
		</cac:CreditNoteLine>';
	}	
}

    $xmlCPE = $xmlCPE . '</CreditNote>';
    $doc->loadXML($xmlCPE);
    $doc->save(dirname(__FILE__) . '/' . $ruta . '.XML');

    return '1';
}

function cpeND($ruta, $cabecera, $detalle) {
    $doc = new DOMDocument();
    $doc->formatOutput = FALSE;
    $doc->preserveWhiteSpace = TRUE;
	$doc->encoding = 'utf-8';
    //$doc->encoding = 'ISO-8859-1';
    //$doc->encoding = 'utf-8';
    $xmlCPE = '<?xml version="1.0" encoding="UTF-8"?>
<DebitNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent>
            </ext:ExtensionContent>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2.0</cbc:CustomizationID>
    <cbc:ID>'.$cabecera["NRO_COMPROBANTE"].'</cbc:ID>
    <cbc:IssueDate>'.$cabecera["FECHA_DOCUMENTO"].'</cbc:IssueDate>
    <cbc:IssueTime>00:00:00</cbc:IssueTime>
    <cbc:DocumentCurrencyCode>'.$cabecera["COD_MONEDA"].'</cbc:DocumentCurrencyCode>
    <cac:DiscrepancyResponse>
        <cbc:ReferenceID>'.$cabecera["NRO_DOCUMENTO_MODIFICA"].'</cbc:ReferenceID>
        <cbc:ResponseCode>'.$cabecera["COD_TIPO_MOTIVO"].'</cbc:ResponseCode>
        <cbc:Description><![CDATA['.$cabecera["DESCRIPCION_MOTIVO"].']]></cbc:Description>
    </cac:DiscrepancyResponse>
    <cac:BillingReference>
        <cac:InvoiceDocumentReference>
            <cbc:ID>'.$cabecera["NRO_DOCUMENTO_MODIFICA"].'</cbc:ID>
            <cbc:DocumentTypeCode>'.$cabecera["TIPO_COMPROBANTE_MODIFICA"].'</cbc:DocumentTypeCode>
        </cac:InvoiceDocumentReference>
    </cac:BillingReference>
    <cac:Signature>
        <cbc:ID>IDSignST</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA['.$cabecera["RAZON_SOCIAL_EMPRESA"].']]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#SignatureSP</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $cabecera["NOMBRE_COMERCIAL_EMPRESA"] . ']]></cbc:Name>
            </cac:PartyName>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA['.$cabecera["RAZON_SOCIAL_EMPRESA"].']]></cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">0000</cbc:AddressTypeCode>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cabecera["NRO_DOCUMENTO_CLIENTE"].'</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
<cbc:RegistrationName><![CDATA['.$cabecera["RAZON_SOCIAL_CLIENTE"].']]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$cabecera["TOTAL_IGV"].'</cbc:TaxAmount>
        <cac:TaxSubtotal>
<cbc:TaxableAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$cabecera["TOTAL_GRAVADAS"].'</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$cabecera["TOTAL_IGV"].'</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
		</cac:TaxSubtotal>';


		if ($cabecera["TOTAL_ISC"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_ISC"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_ISC"] . '</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">2000</cbc:ID>
				<cbc:Name>ISC</cbc:Name>
				<cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
			}
			//CAMPO NUEVO
			if ($cabecera["TOTAL_EXPORTACION"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_EXPORTACION"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">G</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9995</cbc:ID>
				<cbc:Name>EXP</cbc:Name>
				<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
			}
			if ($cabecera["TOTAL_GRATUITAS"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_GRATUITAS"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">Z</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
				<cbc:Name>GRA</cbc:Name>
				<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
			}
			// var_dump($cabecera["TOTAL_EXONERADAS"]);exit();
			if ($cabecera["TOTAL_EXONERADAS"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_EXONERADAS"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
				<cbc:Name>EXO</cbc:Name>
				<cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
			}
			if ($cabecera["TOTAL_INAFECTA"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_INAFECTA"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">0.00</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
				<cbc:Name>INAFECTO</cbc:Name>
				<cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
			}
			if ($cabecera["TOTAL_OTR_IMP"]>0){
			$xmlCPE = $xmlCPE .
			'<cac:TaxSubtotal>
		<cbc:TaxableAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_OTR_IMP"] . '</cbc:TaxableAmount>
		<cbc:TaxAmount currencyID="' . $cabecera["COD_MONEDA"] . '">' . $cabecera["TOTAL_OTR_IMP"] . '</cbc:TaxAmount>
		<cac:TaxCategory>
			<cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
			<cac:TaxScheme>
				<cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9999</cbc:ID>
				<cbc:Name>OTR</cbc:Name>
				<cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
			</cac:TaxScheme>
		</cac:TaxCategory>
	</cac:TaxSubtotal>';
}
//TOTAL=GRAVADA+IGV+EXONERADA
//NO ENTRA GRATUITA(INAFECTA) NI DESCUENTO
//SUB_TOTAL=PRECIO(SIN IGV) * CANTIDAD
$xmlCPE = $xmlCPE .

    '</cac:TaxTotal>
    <cac:RequestedMonetaryTotal>
<cbc:PayableAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$cabecera["TOTAL"].'</cbc:PayableAmount>
    </cac:RequestedMonetaryTotal>';
	
for ($i = 0; $i < count($detalle); $i++) {
	if ($detalle[$i]["txtCOD_TIPO_OPERACION"]=="10"){
        $xmlCPE = $xmlCPE . '
    <cac:DebitNoteLine>
        <cbc:ID>'.$detalle[$i]["txtITEM"].'</cbc:ID>
<cbc:DebitedQuantity unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '">'.$detalle[$i]["txtCANTIDAD_DET"].'</cbc:DebitedQuantity>
<cbc:LineExtensionAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIMPORTE_DET"].'</cbc:LineExtensionAmount>
        <cac:PricingReference>
            <cac:AlternativeConditionPrice>
<cbc:PriceAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtPRECIO_DET"].'</cbc:PriceAmount>
<cbc:PriceTypeCode>'.$detalle[$i]["txtPRECIO_TIPO_CODIGO"].'</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
        </cac:PricingReference>
        <cac:TaxTotal>		
<cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIGV"].'</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIMPORTE_DET"].'</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIGV"].'</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:Percent>'.$cabecera["POR_IGV"].'</cbc:Percent>
<cbc:TaxExemptionReasonCode>'.$detalle[$i]["txtCOD_TIPO_OPERACION"].'</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID>1000</cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
		
<cac:Item>
<cbc:Description><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtDESCRIPCION_DET"]))?$detalle[$i]["txtDESCRIPCION_DET"]:"") . ']]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtCODIGO_DET"]))?$detalle[$i]["txtCODIGO_DET"]:"") . ']]></cbc:ID>
            </cac:SellersItemIdentification>
        </cac:Item>
<cac:Price>
<cbc:PriceAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtPRECIO_DET"].'</cbc:PriceAmount>
</cac:Price>
	</cac:DebitNoteLine>';
	}

	if ($detalle[$i]["txtCOD_TIPO_OPERACION"]=="20"){
        $xmlCPE = $xmlCPE . '
    <cac:DebitNoteLine>
        <cbc:ID>'.$detalle[$i]["txtITEM"].'</cbc:ID>
<cbc:DebitedQuantity unitCode="' . $detalle[$i]["txtUNIDAD_MEDIDA_DET"] . '">'.$detalle[$i]["txtCANTIDAD_DET"].'</cbc:DebitedQuantity>
<cbc:LineExtensionAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIMPORTE_DET"].'</cbc:LineExtensionAmount>
        <cac:PricingReference>
            <cac:AlternativeConditionPrice>
<cbc:PriceAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtPRECIO_DET"].'</cbc:PriceAmount>
<cbc:PriceTypeCode>'.$detalle[$i]["txtPRECIO_TIPO_CODIGO"].'</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
        </cac:PricingReference>
        <cac:TaxTotal>		
<cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIGV"].'</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIMPORTE_DET"].'</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtIGV"].'</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:Percent>0</cbc:Percent>
<cbc:TaxExemptionReasonCode>'.$detalle[$i]["txtCOD_TIPO_OPERACION"].'</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID>9997</cbc:ID>
                        <cbc:Name>EXO</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
		
<cac:Item>
<cbc:Description><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtDESCRIPCION_DET"]))?$detalle[$i]["txtDESCRIPCION_DET"]:"") . ']]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID><![CDATA[' . ValidarCaracteresInv((isset($detalle[$i]["txtCODIGO_DET"]))?$detalle[$i]["txtCODIGO_DET"]:"") . ']]></cbc:ID>
            </cac:SellersItemIdentification>
        </cac:Item>
<cac:Price>
<cbc:PriceAmount currencyID="'.$cabecera["COD_MONEDA"].'">'.$detalle[$i]["txtPRECIO_DET"].'</cbc:PriceAmount>
</cac:Price>
	</cac:DebitNoteLine>';
	}
}

$xmlCPE = $xmlCPE . '</DebitNote>';
    $doc->loadXML($xmlCPE);
    $doc->save(dirname(__FILE__) . '/' . $ruta . '.XML');

    return '1';
}

function cpeBajaSunat($ruta,$cabecera, $detalle) {
    $doc = new DOMDocument();
    $doc->formatOutput = FALSE;
    $doc->preserveWhiteSpace = TRUE;
    $doc->encoding = 'ISO-8859-1';
    $xmlCPE = '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?><VoidedDocuments xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:VoidedDocuments-1" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<ext:UBLExtensions>
<ext:UBLExtension>
<ext:ExtensionContent>
</ext:ExtensionContent>
</ext:UBLExtension>
</ext:UBLExtensions>
<cbc:UBLVersionID>2.0</cbc:UBLVersionID>
<cbc:CustomizationID>1.0</cbc:CustomizationID>
<cbc:ID>'.$cabecera["CODIGO"].'-'.$cabecera["SERIE"].'-'.$cabecera["SECUENCIA"].'</cbc:ID>
<cbc:ReferenceDate>'.$cabecera["FECHA_REFERENCIA"].'</cbc:ReferenceDate>
<cbc:IssueDate>'.$cabecera["FECHA_BAJA"].'</cbc:IssueDate>
<cac:Signature>
<cbc:ID>IDSignKG</cbc:ID>
<cac:SignatoryParty>
<cac:PartyIdentification>
<cbc:ID>'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</cbc:ID>
</cac:PartyIdentification>
<cac:PartyName>
<cbc:Name>'.ValidarCaracteresInv($cabecera["RAZON_SOCIAL"]).'</cbc:Name>
</cac:PartyName>
</cac:SignatoryParty>
<cac:DigitalSignatureAttachment>
<cac:ExternalReference>
<cbc:URI>#'.$cabecera["SERIE"].'-'.$cabecera["SECUENCIA"].'</cbc:URI>
</cac:ExternalReference>
</cac:DigitalSignatureAttachment>
</cac:Signature>
<cac:AccountingSupplierParty>
<cbc:CustomerAssignedAccountID>'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</cbc:CustomerAssignedAccountID>
<cbc:AdditionalAccountID>'.$cabecera["TIPO_DOCUMENTO"].'</cbc:AdditionalAccountID>
<cac:Party>
<cac:PartyLegalEntity>
<cbc:RegistrationName><![CDATA['.ValidarCaracteresInv($cabecera["RAZON_SOCIAL"]).']]></cbc:RegistrationName>
</cac:PartyLegalEntity>
</cac:Party>
</cac:AccountingSupplierParty>';

for ($i = 0; $i < count($detalle); $i++) {
$xmlCPE = $xmlCPE . '<sac:VoidedDocumentsLine>
<cbc:LineID>'.$detalle[$i]["ITEM"].'</cbc:LineID>
<cbc:DocumentTypeCode>'.$detalle[$i]["TIPO_COMPROBANTE"].'</cbc:DocumentTypeCode>
<sac:DocumentSerialID>'.$detalle[$i]["SERIE"].'</sac:DocumentSerialID>
<sac:DocumentNumberID>'.$detalle[$i]["NUMERO"].'</sac:DocumentNumberID>
<sac:VoidReasonDescription><![CDATA['.ValidarCaracteresInv($detalle[$i]["DESCRIPCION"]).']]></sac:VoidReasonDescription>
</sac:VoidedDocumentsLine>';
}
$xmlCPE = $xmlCPE . '</VoidedDocuments>';

$doc->loadXML($xmlCPE);
$doc->save(dirname(__FILE__) . '/' . $ruta . '.XML');

return 'XML BAJA CREADO';
}

function cpeResumenBoleta($ruta,$cabecera, $detalle){
    $doc = new DOMDocument();
    $doc->formatOutput = FALSE;
    $doc->preserveWhiteSpace = TRUE;
    $doc->encoding = 'ISO-8859-1';
    $xmlCPE = '<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>
    <SummaryDocuments 
	xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1" 
	xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
	xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
	xmlns:ds="http://www.w3.org/2000/09/xmldsig#" 
	xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" 
	xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1"
	xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" 
	xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2">
	<ext:UBLExtensions>
		<ext:UBLExtension>
                        <ext:ExtensionContent>
			</ext:ExtensionContent>
		</ext:UBLExtension>
	</ext:UBLExtensions>
	<cbc:UBLVersionID>2.0</cbc:UBLVersionID>
	<cbc:CustomizationID>1.1</cbc:CustomizationID>
	<cbc:ID>' . $cabecera["CODIGO"] . '-' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:ID>
	<cbc:ReferenceDate>' . $cabecera["FECHA_REFERENCIA"] . '</cbc:ReferenceDate>
	<cbc:IssueDate>' . $cabecera["FECHA_DOCUMENTO"] . '</cbc:IssueDate>
	<cac:Signature>
		<cbc:ID>' . $cabecera["CODIGO"] . '-' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:ID>
		<cac:SignatoryParty>
			<cac:PartyIdentification>
				<cbc:ID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
			</cac:PartyIdentification>
			<cac:PartyName>
				<cbc:Name>' . $cabecera["RAZON_SOCIAL"] . '</cbc:Name>
			</cac:PartyName>
		</cac:SignatoryParty>
		<cac:DigitalSignatureAttachment>
			<cac:ExternalReference>
				<cbc:URI>' . $cabecera["CODIGO"] . '-' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:URI>
			</cac:ExternalReference>
		</cac:DigitalSignatureAttachment>
	</cac:Signature>
	<cac:AccountingSupplierParty>
		<cbc:CustomerAssignedAccountID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:CustomerAssignedAccountID>
		<cbc:AdditionalAccountID>' . $cabecera["TIPO_DOCUMENTO"] . '</cbc:AdditionalAccountID>
		<cac:Party>
			<cac:PartyLegalEntity>
				<cbc:RegistrationName>' . $cabecera["RAZON_SOCIAL"] . '</cbc:RegistrationName>
			</cac:PartyLegalEntity>
		</cac:Party>
	</cac:AccountingSupplierParty>';
    /*
private int ITEM;
	private String TIPO_COMPROBANTE;
	private String NRO_COMPROBANTE;
	private String TIPO_DOCUMENTO;
	private String NRO_DOCUMENTO;
	private String TIPO_COMPROBANTE_REF;
	private String NRO_COMPROBANTE_REF;
	private String STATU;
	private String COD_MONEDA;
	private double TOTAL;
	private double GRAVADA;
	private double ISC;
	private double IGV;
	private double OTROS;
	private int CARGO_X_ASIGNACION;
	private double MONTO_CARGO_X_ASIG;
	private String COD_TIPO_IMPORTE1;
	private double EXONERADO;
	private String COD_TIPO_IMPORTE2;
	private double INAFECTO;
	private String COD_TIPO_IMPORTE3;
	private double EXPORTACION;
	private String COD_TIPO_IMPORTE4;
	private double GRATUITAS;
	private String COD_TIPO_IMPORTE5;
	private String ESTADOS;
    */
	for ($i = 0; $i < count($detalle); $i++) {
        $xmlCPE = $xmlCPE . '<sac:SummaryDocumentsLine>
		<cbc:LineID>' . $detalle[$i]["ITEM"] . '</cbc:LineID>
		<cbc:DocumentTypeCode>' . $detalle[$i]["TIPO_COMPROBANTE"] . '</cbc:DocumentTypeCode>
		<cbc:ID>' . $detalle[$i]["NRO_COMPROBANTE"] . '</cbc:ID>
		<cac:AccountingCustomerParty>
			<cbc:CustomerAssignedAccountID>' . $detalle[$i]["NRO_DOCUMENTO"] . '</cbc:CustomerAssignedAccountID>
			<cbc:AdditionalAccountID>' . $detalle[$i]["TIPO_DOCUMENTO"] . '</cbc:AdditionalAccountID>
		</cac:AccountingCustomerParty>';
                if ($detalle[$i]["TIPO_COMPROBANTE"]=="07"||$detalle[$i]["TIPO_COMPROBANTE"]=="08"){
		 $xmlCPE = $xmlCPE . '<cac:BillingReference>
			<cac:InvoiceDocumentReference>
				<cbc:ID>' . $detalle[$i]["NRO_COMPROBANTE_REF"] . '</cbc:ID>
				<cbc:DocumentTypeCode>' . $detalle[$i]["TIPO_COMPROBANTE_REF"] . '</cbc:DocumentTypeCode>
			</cac:InvoiceDocumentReference>
		</cac:BillingReference>';
                }
		$xmlCPE = $xmlCPE . '<cac:Status>
			<cbc:ConditionCode>' . $detalle[$i]["STATU"] . '</cbc:ConditionCode>
		</cac:Status>                
		<sac:TotalAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["TOTAL"] . '</sac:TotalAmount>
		
                <sac:BillingPayment>
			<cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["GRAVADA"] . '</cbc:PaidAmount>
			<cbc:InstructionID>01</cbc:InstructionID>
		</sac:BillingPayment>';
                
                if ($detalle[$i]["EXONERADO"] > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
			<cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["EXONERADO"] . '</cbc:PaidAmount>
			<cbc:InstructionID>02</cbc:InstructionID>
		</sac:BillingPayment>';
                }
                
                if ($detalle[$i]["INAFECTO"] > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
			<cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["INAFECTO"] . '</cbc:PaidAmount>
			<cbc:InstructionID>03</cbc:InstructionID>
		</sac:BillingPayment>';
                }
                
                if ($detalle[$i]["EXPORTACION"] > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
			<cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["EXPORTACION"] . '</cbc:PaidAmount>
			<cbc:InstructionID>04</cbc:InstructionID>
		</sac:BillingPayment>';
                }
                
                if ($detalle[$i]["GRATUITAS"] > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
			<cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["GRATUITAS"] . '</cbc:PaidAmount>
			<cbc:InstructionID>05</cbc:InstructionID>
		</sac:BillingPayment>';
                }
                
                if ($detalle[$i]["MONTO_CARGO_X_ASIG"] > 0) {
                    $xmlCPE = $xmlCPE . '<cac:AllowanceCharge>';
                    if ($detalle[$i]["CARGO_X_ASIGNACION"] == 1) {
                        $xmlCPE = $xmlCPE . '<cbc:ChargeIndicator>true</cbc:ChargeIndicator>';
                    }else{
                        $xmlCPE = $xmlCPE . '<cbc:ChargeIndicator>false</cbc:ChargeIndicator>';
                    }
                    $xmlCPE = $xmlCPE . '<cbc:Amount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["MONTO_CARGO_X_ASIG"] . '</cbc:Amount>
                    </cac:AllowanceCharge>';
                }
                if($detalle[$i]["ISC"]>0){
		$xmlCPE = $xmlCPE . '<cac:TaxTotal>
			<cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["ISC"] . '</cbc:TaxAmount>
			<cac:TaxSubtotal>
				<cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["ISC"] . '</cbc:TaxAmount>
				<cac:TaxCategory>
					<cac:TaxScheme>
						<cbc:ID>2000</cbc:ID>
						<cbc:Name>ISC</cbc:Name>
						<cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
					</cac:TaxScheme>
				</cac:TaxCategory>
			</cac:TaxSubtotal>
		</cac:TaxTotal>';
                }
                $xmlCPE = $xmlCPE . '<cac:TaxTotal>
			<cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["IGV"] . '</cbc:TaxAmount>
			<cac:TaxSubtotal>
				<cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["IGV"] . '</cbc:TaxAmount>
				<cac:TaxCategory>
					<cac:TaxScheme>
						<cbc:ID>1000</cbc:ID>
						<cbc:Name>IGV</cbc:Name>
						<cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
					</cac:TaxScheme>
				</cac:TaxCategory>
			</cac:TaxSubtotal>
		</cac:TaxTotal>';
                
                if($detalle[$i]["OTROS"]>0){
                $xmlCPE = $xmlCPE . '<cac:TaxTotal>
			<cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["OTROS"] . '</cbc:TaxAmount>
			<cac:TaxSubtotal>
				<cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["OTROS"] . '</cbc:TaxAmount>
				<cac:TaxCategory>
					<cac:TaxScheme>
						<cbc:ID>9999</cbc:ID>
						<cbc:Name>OTROS</cbc:Name>
						<cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
					</cac:TaxScheme>
				</cac:TaxCategory>
			</cac:TaxSubtotal>
		</cac:TaxTotal>';
                }
	$xmlCPE = $xmlCPE . '</sac:SummaryDocumentsLine>';
    }
    $xmlCPE = $xmlCPE . '</SummaryDocuments>';

    $doc->loadXML($xmlCPE);
    $doc->save(dirname(__FILE__) . '/' . $ruta . '.XML');

    return 'XML RESUMEN BOLETA CREADO';
}


?>