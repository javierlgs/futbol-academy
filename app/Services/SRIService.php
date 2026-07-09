<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\NotaCredito;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use DOMDocument;

class SRIService {
    private $ruc;
    private $ambiente;
    private $establecimiento;
    private $puntoEmision;
    private $certPath;
    private $certPassword;

    public function __construct() {
        $this->ruc = setting('ruc');
        $this->ambiente = setting('ambiente_sri');
        $this->establecimiento = setting('codigo_establecimiento');
        $this->puntoEmision = setting('codigo_punto_emision');
        $this->certPath = storage_path('app/' . setting('firma_electronica_path'));
        $this->certPassword = setting('firma_electronica_clave');
    }

    // --- MÉTODOS PARA FACTURAS ---

    public function generarFactura(Factura $factura) {
        $secuencial = str_pad($factura->id, 9, '0', STR_PAD_LEFT);
        $factura->numero_factura = "{$this->establecimiento}-{$this->puntoEmision}-{$secuencial}";
        $factura->clave_acceso = $this->generarClaveAcceso($factura, $secuencial, '01');
        $factura->ambiente = $this->ambiente;
        
        $xml = $this->generarXMLFactura($factura);
        $xmlFirmado = $this->firmarXadesBes($xml);
        
        $factura->xml_generado = $xmlFirmado;
        $factura->save();

        return $this->enviarSRI($factura);
    }

    private function generarXMLFactura(Factura $factura) {
        $codigoPorcentajeSRI = $factura->iva_codigo_sri;
        $ivaPorcentaje = (float) $factura->iva_porcentaje_usado;
        
        return '<?xml version="1.0" encoding="UTF-8"?>
<factura id="comprobante" version="1.1.0">
    <infoTributaria>
        <ambiente>'.$this->ambiente.'</ambiente>
        <tipoEmision>1</tipoEmision>
        <razonSocial>'.htmlspecialchars(setting('razon_social')).'</razonSocial>
        <nombreComercial>'.htmlspecialchars(setting('nombre_comercial')).'</nombreComercial>
        <ruc>'.$this->ruc.'</ruc>
        <claveAcceso>'.$factura->clave_acceso.'</claveAcceso>
        <codDoc>01</codDoc>
        <estab>'.$this->establecimiento.'</estab>
        <ptoEmi>'.$this->puntoEmision.'</ptoEmi>
        <secuencial>'.str_pad($factura->id, 9, '0', STR_PAD_LEFT).'</secuencial>
        <dirMatriz>'.htmlspecialchars(setting('direccion_matriz')).'</dirMatriz>
    </infoTributaria>
    <infoFactura>
        <fechaEmision>'.now()->format('d/m/Y').'</fechaEmision>
        <obligadoContabilidad>'.setting('obligado_contabilidad').'</obligadoContabilidad>
        <tipoIdentificacionComprador>'.$factura->cliente_tipo_identificacion.'</tipoIdentificacionComprador>
        <razonSocialComprador>'.htmlspecialchars($factura->cliente_nombre).'</razonSocialComprador>
        <identificacionComprador>'.$factura->cliente_identificacion.'</identificacionComprador>
        <direccionComprador>'.htmlspecialchars($factura->cliente_direccion).'</direccionComprador>
        <totalSinImpuestos>'.number_format($factura->subtotal_gravado, 2, '.', '').'</totalSinImpuestos>
        <totalDescuento>0.00</totalDescuento>
        <totalConImpuestos>
            <totalImpuesto>
                <codigo>2</codigo>
                <codigoPorcentaje>'.$codigoPorcentajeSRI.'</codigoPorcentaje>
                <baseImponible>'.number_format($factura->subtotal_gravado, 2, '.', '').'</baseImponible>
                <valor>'.number_format($factura->iva_valor, 2, '.', '').'</valor>
            </totalImpuesto>
        </totalConImpuestos>
        <propina>0.00</propina>
        <importeTotal>'.number_format($factura->total, 2, '.', '').'</importeTotal>
        <moneda>DOLAR</moneda>
        <pagos>
            <pago>
                <formaPago>01</formaPago>
                <total>'.number_format($factura->total, 2, '.', '').'</total>
            </pago>
        </pagos>
    </infoFactura>
    <detalles>
        <detalle>
            <codigoPrincipal>SERV001</codigoPrincipal>
            <descripcion>'.htmlspecialchars($factura->concepto).'</descripcion>
            <cantidad>1.00</cantidad>
            <precioUnitario>'.number_format($factura->subtotal_gravado, 2, '.', '').'</precioUnitario>
            <descuento>0.00</descuento>
            <precioTotalSinImpuesto>'.number_format($factura->subtotal_gravado, 2, '.', '').'</precioTotalSinImpuesto>
            <impuestos>
                <impuesto>
                    <codigo>2</codigo>
                    <codigoPorcentaje>'.$codigoPorcentajeSRI.'</codigoPorcentaje>
                    <tarifa>'.number_format($ivaPorcentaje, 2, '.', '').'</tarifa>
                    <baseImponible>'.number_format($factura->subtotal_gravado, 2, '.', '').'</baseImponible>
                    <valor>'.number_format($factura->iva_valor, 2, '.', '').'</valor>
                </impuesto>
            </impuestos>
        </detalle>
    </detalles>
</factura>';
    }

    // --- MÉTODOS PARA NOTAS DE CRÉDITO ---

    public function generarNotaCredito(Factura $factura, $motivo, $detalle) {
        $secuencial = str_pad(NotaCredito::count() + 1, 9, '0', STR_PAD_LEFT);
        $numeroNota = "{$this->establecimiento}-{$this->puntoEmision}-{$secuencial}";
        
        $nota = NotaCredito::create([
            'factura_id' => $factura->id,
            'numero_nota' => $numeroNota,
            'clave_acceso' => $this->generarClaveAcceso($factura, $secuencial, '04'),
            'motivo' => $motivo,
            'detalle_motivo' => $detalle,
            'valor_modificacion' => $factura->total,
        ]);

        $xml = $this->generarXMLNotaCredito($nota, $factura);
        $xmlFirmado = $this->firmarXadesBes($xml);
        $nota->xml_generado = $xmlFirmado;
        $nota->save();

        return $this->enviarSRINotaCredito($nota);
    }

    private function generarXMLNotaCredito(NotaCredito $nota, Factura $factura) {
        $ivaPorcentaje = (float) $factura->iva_porcentaje_usado;
        $codigoPorcentajeSRI = $factura->iva_codigo_sri;

        return '<?xml version="1.0" encoding="UTF-8"?>
<notaCredito id="comprobante" version="1.1.0">
    <infoTributaria>
        <ambiente>'.$this->ambiente.'</ambiente>
        <tipoEmision>1</tipoEmision>
        <razonSocial>'.htmlspecialchars(setting('razon_social')).'</razonSocial>
        <nombreComercial>'.htmlspecialchars(setting('nombre_comercial')).'</nombreComercial>
        <ruc>'.$this->ruc.'</ruc>
        <claveAcceso>'.$nota->clave_acceso.'</claveAcceso>
        <codDoc>04</codDoc>
        <estab>'.$this->establecimiento.'</estab>
        <ptoEmi>'.$this->puntoEmision.'</ptoEmi>
        <secuencial>'.str_pad($nota->id, 9, '0', STR_PAD_LEFT).'</secuencial>
        <dirMatriz>'.htmlspecialchars(setting('direccion_matriz')).'</dirMatriz>
    </infoTributaria>
    <infoNotaCredito>
        <fechaEmision>'.now()->format('d/m/Y').'</fechaEmision>
        <obligadoContabilidad>'.setting('obligado_contabilidad').'</obligadoContabilidad>
        <tipoIdentificacionComprador>'.$factura->cliente_tipo_identificacion.'</tipoIdentificacionComprador>
        <razonSocialComprador>'.htmlspecialchars($factura->cliente_nombre).'</razonSocialComprador>
        <identificacionComprador>'.$factura->cliente_identificacion.'</identificacionComprador>
        <codDocModificado>01</codDocModificado>
        <numDocModificado>'.$factura->numero_factura.'</numDocModificado>
        <fechaEmisionDocSustento>'.Carbon::parse($factura->created_at)->format('d/m/Y').'</fechaEmisionDocSustento>
        <totalSinImpuestos>'.number_format($factura->subtotal_gravado, 2, '.', '').'</totalSinImpuestos>
        <valorModificacion>'.number_format($nota->valor_modificacion, 2, '.', '').'</valorModificacion>
        <moneda>DOLAR</moneda>
        <totalConImpuestos>
            <totalImpuesto>
                <codigo>2</codigo>
                <codigoPorcentaje>'.$codigoPorcentajeSRI.'</codigoPorcentaje>
                <baseImponible>'.number_format($factura->subtotal_gravado, 2, '.', '').'</baseImponible>
                <valor>'.number_format($factura->iva_valor, 2, '.', '').'</valor>
            </totalImpuesto>
        </totalConImpuestos>
        <motivo>'.htmlspecialchars($nota->detalle_motivo).'</motivo>
    </infoNotaCredito>
    <detalles>
        <detalle>
            <codigoInterno>SERV001</codigoInterno>
            <descripcion>'.htmlspecialchars($factura->concepto).'</descripcion>
            <cantidad>1.00</cantidad>
            <precioUnitario>'.number_format($factura->subtotal_gravado, 2, '.', '').'</precioUnitario>
            <descuento>0.00</descuento>
            <precioTotalSinImpuesto>'.number_format($factura->subtotal_gravado, 2, '.', '').'</precioTotalSinImpuesto>
            <impuestos>
                <impuesto>
                    <codigo>2</codigo>
                    <codigoPorcentaje>'.$codigoPorcentajeSRI.'</codigoPorcentaje>
                    <tarifa>'.number_format($ivaPorcentaje, 2, '.', '').'</tarifa>
                    <baseImponible>'.number_format($factura->subtotal_gravado, 2, '.', '').'</baseImponible>
                    <valor>'.number_format($factura->iva_valor, 2, '.', '').'</valor>
                </impuesto>
            </impuestos>
        </detalle>
    </detalles>
</notaCredito>';
    }

    // --- MÉTODOS DE COMUNICACIÓN CON SRI ---

    private function enviarSRI(Factura $factura) {
        $url = $this->ambiente == '1' ? 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl' : 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
        $client = new \SoapClient($url, ['trace' => 1]);
        try {
            $response = $client->validarComprobante(['xml' => base64_encode($factura->xml_generado)]);
            if ($response->RespuestaRecepcionComprobante->estado == 'RECIBIDA') {
                $authUrl = $this->ambiente == '1' ? 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl' : 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
                $authResponse = (new \SoapClient($authUrl))->autorizacionComprobante(['claveAccesoComprobante' => $factura->clave_acceso]);
                $autorizacion = $authResponse->RespuestaAutorizacionComprobante->autorizaciones->autorizacion ?? null;
                if ($autorizacion && $autorizacion->estado == 'AUTORIZADO') {
                    $factura->estado_sri = 'AUTORIZADO';
                    $factura->numero_autorizacion = $autorizacion->numeroAutorizacion;
                    $factura->fecha_autorizacion = $autorizacion->fechaAutorizacion;
                    $factura->xml_autorizado = $autorizacion->comprobante;
                } else {
                    $factura->estado_sri = 'RECHAZADO';
                }
            } else {
                $factura->estado_sri = 'DEVUELTA';
            }
        } catch (\Exception $e) {
            $factura->estado_sri = 'ERROR';
        }
        $factura->save();
        return $factura;
    }

    private function enviarSRINotaCredito(NotaCredito $nota) {
        $url = $this->ambiente == '1' ? 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl' : 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
        $client = new \SoapClient($url, ['trace' => 1]);
        try {
            $response = $client->validarComprobante(['xml' => base64_encode($nota->xml_generado)]);
            if ($response->RespuestaRecepcionComprobante->estado == 'RECIBIDA') {
                $authUrl = $this->ambiente == '1' ? 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl' : 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
                $authResponse = (new \SoapClient($authUrl))->autorizacionComprobante(['claveAccesoComprobante' => $nota->clave_acceso]);
                $autorizacion = $authResponse->RespuestaAutorizacionComprobante->autorizaciones->autorizacion ?? null;
                if ($autorizacion && $autorizacion->estado == 'AUTORIZADO') {
                    $nota->estado_sri = 'AUTORIZADO';
                    $nota->numero_autorizacion = $autorizacion->numeroAutorizacion;
                    $nota->fecha_autorizacion = $autorizacion->fechaAutorizacion;
                    $nota->xml_autorizado = $autorizacion->comprobante;
                    $nota->factura->update(['estado_sri' => 'ANULADO', 'estado_pago' => 'anulada']);
                } else {
                    $nota->estado_sri = 'RECHAZADO';
                }
            } else {
                $nota->estado_sri = 'DEVUELTA';
            }
        } catch (\Exception $e) {
            $nota->estado_sri = 'ERROR';
        }
        $nota->save();
        return $nota;
    }

    // --- MÉTODOS AUXILIARES ---

    private function generarClaveAcceso(Factura $factura, $secuencial, $tipoComprobante) {
        $fecha = Carbon::parse($factura->created_at)->format('dmY');
        $codigoNumerico = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $clave = $fecha . $tipoComprobante . $this->ruc . $this->ambiente . $this->establecimiento . $this->puntoEmision . $secuencial . $codigoNumerico . '1';
        return $clave . $this->calcularDigitoVerificador($clave);
    }

    private function calcularDigitoVerificador($clave) {
        $multiplicadores = [2, 3, 4, 5, 6, 7];
        $suma = 0; $j = 0;
        for ($i = strlen($clave) - 1; $i >= 0; $i--) {
            $suma += intval($clave[$i]) * $multiplicadores[$j];
            $j = ($j + 1) % 6;
        }
        $mod = $suma % 11;
        return $mod == 0 ? 0 : ($mod == 1 ? 1 : 11 - $mod);
    }

    private function firmarXadesBes($xml) {
        if (!file_exists($this->certPath)) throw new \Exception('Certificado no encontrado');
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $pkcs12 = file_get_contents($this->certPath);
        openssl_pkcs12_read($pkcs12, $certs, $this->certPassword);
        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        $objDSig->addReference($doc, XMLSecurityDSig::SHA1, ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'], ['id_name' => 'ID', 'force_uri' => true]);
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        $objKey->loadKey($certs['pkey']);
        $objDSig->sign($objKey, $doc->documentElement);
        $objDSig->add509Cert($certs['cert'], true);
        return $doc->saveXML();
    }
}