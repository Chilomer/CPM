<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Empresa;
use App\Models\Clientes;
use App\Models\Personal;
use App\Models\Polizas;
use App\Models\SWAccesos;
use App\Models\Folios;
use App\Models\Pedimentos;
use App\Models\Software;
use App\Models\Bitacora;
use App\Models\Vehiculos;
use App\Models\Aseguradoras;
use App\Models\CPMercancia;
use App\Models\CPOrigenDestino;
use App\Models\CPConceptos;
use App\Http\Resources\DocumentoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use SWServices\Authentication\AuthenticationService  as Authentication;
use SWServices\AccountBalance\AccountBalanceService as AccountBalanceService;
use SWServices\JSonIssuer\JsonEmisionTimbrado as jsonEmisionTimbrado;
use Dompdf\Dompdf;
use Dompdf\Options;

class CPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {  
        $user = auth('api')->user();
        $id = $user->idempresa;

        $precpResponse = Documento::where('idempresa', $id)->get();
        return DocumentoResource::collection($precpResponse);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function showall(Request $request, $idinterno)
    {
        $cpResponse = Documento::where('idempresa', $idinterno)->get();
        return DocumentoResource::collection($cpResponse);
    }

    public function generateIdCCP() {
        // Caracteres con letra "C"
        $prefix = "CCC"; // Reemplaza "XX" con los caracteres específicos que deseas usar
        
        // RFC 4122
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
        
        // Patrón del Estándar documentación técnica CP V3.0
        $idCCP = $prefix . '-' . $uuid; // Combinando el prefijo con el UUID generado
        
        return $idCCP;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $empresa = Empresa::where('id', $userid)->first();

        $accesos = SWAccesos::where('idempresa', $userid)->first();

        $contienematpeligroso = false;

        

        $emisor = [
            "Rfc" => $empresa->rfc,
            "Nombre" => $empresa->nombrefiscal,
            "RegimenFiscal" => $empresa->regimenfiscal
        ];

        $documento = $request->documento;
        $detalles = $request->detalles;
        $origenes = $request->origenes;
        $destinos = $request->destinos;
        $concepto = $request->concepto;
        $documento['idempresa'] = $userid;
        // $folio = null;
        $documentoAnterior = DB::table('documento')->where('idempresa', $userid)->orderBy('folio', 'desc')->first();
        // Documento::where('idempresa', $userid)->orderBy('folio', 'desc')->first();
        if($documentoAnterior == null){
            $folio = 1;
        } else {
            $folio = $documentoAnterior->folio + 1;
        }

        $documento['folio'] = $folio;
        
        $numtotalmercancias = 0;
        $pesobrutototal = 0;



        foreach($detalles as $key => $detalle){
            $numtotalmercancias += 1;
            $pesobrutototal += $detalle['pesoenkg'];
        }


        $documento['numtotalmercancias'] = $numtotalmercancias;        
        $documento['peso'] = $pesobrutototal;     
           
        if(!isset($documento['id'])){
            $cp = new Documento();
            $cp->fill($documento);
            $cp->save();
            $cpResponse = Documento::orderBy('id', "desc")->first();
            
        } else{
            $cpResponse = Documento::where('id', $documento['id'])->first();
            $cpResponse->fill($documento);
            $cpResponse->save();
        }

        $cliente = Clientes::where('id', $cpResponse->idcliente)->first();

        if($documento['tipocomprobante'] === 'T'){
            $receptor = [
                "Rfc" => $empresa->rfc,
                "Nombre" => $empresa->nombrefiscal,
                "UsoCFDI" => $cpResponse->usocfdi,
                // "DomicilioFiscalReceptor": "42501",
                // "RegimenFiscalReceptor": "601",
                // "UsoCFDI": "S01"
            ];
        }
        $receptor = [
            "Rfc" => $cpResponse->rfc,
            "Nombre" => $cpResponse->nombre,
            "UsoCFDI" => $cpResponse->usocfdi
            // "DomicilioFiscalReceptor": "42501",
                // "RegimenFiscalReceptor": "601",
                // "UsoCFDI": "S01"
        ];

        $id = $cpResponse->id;

        $detalleshtml = '';
        $mercancia= null;              
        CPMercancia::where('iddocumento', $id)->delete();
        $mercanciahtml = "";
        foreach($detalles as $key => $detalle){
            if(isset($detalle['pedimentos'])){
                $pedimentos = $detalle['pedimentos'];
                unset($detalle['pedimentos']);
            }
            $detalle['iddocumento'] = $id;
            $cpdetalle = new CPMercancia();
            $cpdetalle->fill($detalle);
            $cpdetalle->save();
            $esmatpeligroso = $cpdetalle->materialpeligroso == 0 ? "No" : "Si";

            
            
            // $detalleshtml = $detalleshtml . "<tr style='border-bottom:1px solid #bdc5cc'>
            //     <td style='text-align: left; padding: 10px; border-bottom:1px solid #bdc5cc'>". $cpdetalle->clavearticulo ." - ". $cpdetalle->descripcion ." <br> Clave Producto: ". $cpdetalle->bienestransp ." <br> Unidad: ". $cpdetalle->unidad ." - ". $cpdetalle->claveunidad ." <br> Cantidad: ". $cpdetalle->cantidad ." Peso Total: ". $cpdetalle->pesoenkg ." ". $documento['unidadpeso'] ." <br> Material Peligroso: ". $esmatpeligroso ." - ". $cpdetalle->clavematpeligroso ." <br> Tipo Embalaje: ". $cpdetalle->claveembalaje ." <br> Pedimento: ". $cpdetalle->pedimentos ." <br>Fraccion arancelaria: ". $cpdetalle->fraccionarancelaria ." <br>uuid Comercio Exterior: ". $cpdetalle->uuidcomercioext ." <br></td>
            // </tr>";
            $pedimentos = null;
            $pedimentoshtml = "";
            if(isset($pedimentos)){
                $detalleResponse = CPMercancia::where('iddocumento', $id)->where('descripcion', $cpdetalle->descripcion)->orderBy('id', 'desc')->first();
                foreach($pedimentos as $key => $pedimento){
                    $data = [
                        "idcpmercancia" => trim($detalleResponse->id),
                        "pedimento" => trim($pedimento),
                        "iddocumento" => trim($id)
                    ];
                    $pedimentoRequest = new Pedimentos();
                    $pedimentoRequest->fill($data);
                    $pedimentoRequest->save();
                    $pedimentos[$key] = [
                        "pedimento" => $pedimento
                    ];
                    $pedimentoshtml = $pedimentoshtml . $pedimento;
                }
            }

            $mercanciahtml = "<tr style='border-bottom:1px solid #bdc5cc '>
                <td style='border:1px solid #acb7c0; border-radius: 10px; width:650px; text-align: left; padding: 10px; border-bottom:1pxsolid#bdc5cc '>". $cpdetalle->clavearticulo ." - ". $cpdetalle->descripcion ." <br>
                Clave Bienes transp: ". $cpdetalle->bienestransp ." <br>
                Unidad: ". $cpdetalle->unidad ." - ". $cpdetalle->claveunidad ." <br>
                Cantidad: ". $cpdetalle->cantidad ." Peso Total:". $cpdetalle->pesoenkg ." ". $cpResponse->unidadpeso ." <br>
                Material Peligroso: ". $esmatpeligroso ." - ". $cpdetalle->clavematpeligroso ." <br>
                Tipo Embalaje: ". $cpdetalle->claveembalaje ." <br>
                Pedimento: ". $pedimentoshtml ." <br>
                Fraccion arancelaria: ". $cpdetalle->fraccionarancelaria ." <br>
                uuid Comercio Exterior: ". $cpdetalle->uuidcomercioext ." <br>
                </td>
                </tr>";

            
            $cantidadTransporta = null;
            $cantidadTransporta[0] = [
                "Cantidad" => $cpdetalle->cantidad, //cantidad de mercancias
                "IDDestino" => "DE000001",
                "IDOrigen" => "OR000001"
            ];
            $mercancia[$key] = [
                "BienesTransp"=> trim($cpdetalle->bienestransp),
                "Descripcion" => trim($cpdetalle->descripcion),
                "Cantidad"=> $cpdetalle->cantidad,
                "CantidadTransporta" => $cantidadTransporta,
                // "ClaveSTCC" => "123456", //cual es
                "ClaveUnidad" => trim($cpdetalle->claveunidad),
                "Dimensiones" => trim($cpdetalle->dimensiones),
                
                // "Moneda" => 99,
                "MonedaSpecified" => false,
                "PesoEnKg" => $cpdetalle->pesoenkg,
                "Unidad" => trim($cpdetalle->unidad)
            ];
            if($pedimentos !== null){
                $mercancia[$key]["Pedimentos"] = $pedimentos;
            }
            if($cpdetalle->fraccionarancelaria !== null){
                $mercancia[$key]["FraccionArancelaria"] = trim($cpdetalle->fraccionarancelaria);
            }

            if($cpResponse->transporteinternacional =='Si' && $cpResponse->entradasalidamerc == 'Entrada'){
                $documentacionAduanera = [
                    "TipoDocumento"=> trim($cpdetalle->tipodocumento),
                    "NumPedimento"=> trim($cpdetalle->numpedimento),
                    "RFCImpo"=> trim($cpdetalle->rfcimpo)
                ];
                $mercancia[$key]["DocumentacionAduanera"][0] = $documentacionAduanera;
            }

            if($cpResponse->transporteinternacional =='Si' && $cpResponse->entradasalidamerc == 'Salida'){
                $mercancia[$key]["UUIDComercioExt"] = trim($cpdetalle->uuidcomercioext);
                $mercancia[$key]["TipoMateria"] = trim($cpdetalle->tipomateria);
                $mercancia[$key]["DescripcionMateria"] = trim($cpdetalle->descripcionmateria);


            }
            if($detalle['puedematerialpeligroso'] == true){
                $mercancia[$key]["MaterialPeligroso"] = trim($cpdetalle->materialpeligroso);
                $mercancia[$key]["MaterialPeligrosoSpecified"] = true;
                if($mercancia[$key]["MaterialPeligroso"] == 'Si'){
                    $contienematpeligroso = true;
                    $mercancia[$key]["CveMaterialPeligroso"] = trim($cpdetalle->clavematpeligroso);
                    $mercancia[$key]["Embalaje"] = trim($cpdetalle->claveembalaje);
                }
            } else {
                $mercancia[$key]["MaterialPeligrosoSpecified"] = false;
            }

        }

        $origenhtml = '';
        $ubicaciones = null;

        CPOrigenDestino::where('iddocumento', $id)->delete();
        foreach($origenes as $key => $detalle2){
            $detalle2['iddocumento'] = trim($id);
            $detalle2['origendestino'] = 'Origen';
            $cpOrigen = new CPOrigenDestino();
            $cpOrigen->fill($detalle2);
            $cpOrigen->save();

            $domicilio = [
                "Calle" => trim($cpOrigen->calle),
                "CodigoPostal" => trim($cpOrigen->cp),
                "Estado" => trim($cpOrigen->clave_entfed),
                "NumeroExterior" => trim($cpOrigen->numext),
                "Pais" => trim($cpOrigen->pais),
                // "Referencia" => trim($cpOrigen->referencia)
            ];
            // $origenhtml = "<td style='text-align: left; padding: 10px'><b>Origen:</b><br>". $cpOrigen->nombre ."<br> Residencia Fiscal: ". $cpOrigen->residenciafiscal ." <br> ". $cpOrigen->rfc ." <br> ". $cpOrigen->fechasalidallegada ." ". $cpOrigen->horasalidallegada ." <br> País Origen: ".
            // $cpOrigen->pais ." <br>". $cpOrigen->calle ." ". $cpOrigen->numext ." - ". $cpOrigen->numint .", ". $cpOrigen->c_colonia ." - " . $cpOrigen->colonia .", ". $cpOrigen->cp .", ". $cpOrigen->c_municpio ." - ". $cpOrigen->municipio .", ". $cpOrigen->clave_entfed ." - ". $cpOrigen->estado ."</td>";
            $ubicaciones[0] = [
                "TipoUbicacion"=> "Origen", //0 origen 1 destino
                "IDUbicacion"=> "OR000001",
                "RFCRemitenteDestinatario"=> trim($cpOrigen->rfc),
                "NombreRemitenteDestinatario"=> trim($cpOrigen->nombre),
                "FechaHoraSalidaLlegada" => '2022-03-09T00:18:10',
                "Domicilio" => $domicilio,
                // "TipoEstacion"=> "01",
                // "TipoEstacionSpecified"=> false,
                // "ResidenciaFiscal" => 'MEX'
            ];
            if($cpResponse->transpinternac == "Si" && $cpResponse->entradasalidamerc == "Entrada"){
                $ubicaciones[0]["ResidenciaFiscal"] = $cpOrigen->residenciafiscal;
                $ubicaciones[0]["NumRegIdTrib"] = $cpOrigen->numregidtrib;
            }
        }

        
        
        $destinohtml = '';
        foreach($destinos as $key => $detalle1){
            $detalle1['iddocumento'] = $id;
            $detalle1['origendestino'] = 'Destino';
            $cpDestino = new CPOrigenDestino();
            $cpDestino->fill($detalle1);
            $cpDestino->save();
            $domicilioDestino = [
                "Calle" => trim($cpDestino->calle),
                "CodigoPostal" => trim($cpDestino->cp),
                "Estado" => trim($cpDestino->clave_entfed),
                "NumeroExterior" => trim($cpDestino->numext),
                "Pais" => trim($cpDestino->pais),
                // "Referencia" => trim($cpDestino->referencia)
            ];

            $ubicaciones[1] = [
                "DistanciaRecorrida" => trim($cpDestino->distanciarecorrida),
                "DistanciaRecorridaSpecified" => true,
                "Domicilio" => $domicilioDestino,
                "FechaHoraSalidaLlegada" => '2022-03-10T00:18:10',
                "IDUbicacion"=> "DE000001",
                "NombreRemitenteDestinatario"=> trim($cpDestino->nombre),
                "RFCRemitenteDestinatario"=> trim($cpDestino->rfc),
                //residenciafiscal numregidtrib
                // "TipoEstacion"=> "01",
                "TipoEstacionSpecified"=> false,
                // "ResidenciaFiscal" => 'MEX',
                "TipoUbicacion"=> "Destino" //0 origen 1 destino
            ];
            if($cpResponse->transpinternac == "Si" && $cpResponse->entradasalidamerc == "Salida"){
                $ubicaciones[1]["ResidenciaFiscal"] = $cpOrigen->residenciafiscal;
                $ubicaciones[1]["NumRegIdTrib"] = $cpOrigen->numregidtrib;
            }

            // $destinohtml = "<td style='text-align: left; padding: 10px'><b>Destino:</b><br>". $cpDestino->nombre ."<br> Residencia Fiscal: ". $cpDestino->residenciafiscal ." <br> ". $cpDestino->rfc ." <br> ". $cpDestino->fechasalidallegada ." ". $cpDestino->horasalidallegada ." <br> País Origen: ".
            // $cpDestino->pais ." <br>". $cpDestino->calle ." ". $cpDestino->numext ." - ". $cpDestino->numint .", ". $cpDestino->c_colonia ." - " . $cpDestino->colonia .", ". $cpDestino->cp .", ". $cpDestino->c_municpio ." - ". $cpDestino->municipio .", ". $cpDestino->clave_entfed ." - ". $cpDestino->estado ."</td>";
        }
        $concepto['iddocumento'] = $id;
        CPConceptos::where('iddocumento', $id)->delete();
        $conceptoResource = new CPConceptos();
        $conceptoResource->fill($concepto);
        $conceptoResource->save();
        
        // $subject = "Carta Porte "  . $id;
        // $for = $documento['email'];
        // $mail = "<html><head></head><body><div style='margin-left:auto; margin-right: auto; width:650px;'><table style='width:650px; font-size:14px'><tr><td><img src='https://cartaportemexico.com/images/logoscpm2178x63.png?crc=449637831' alt=''></td></tr><tr><td style='text-align: center'><h1>Tu carta porte ha sido creada con exito</h1><h3>Muestra tú codigo a tu transportista </h3></td></tr></table><br><table style='width:650px'><tr><td style='margin-left:auto; margin-right:auto'><b>Id:". $precpResponse->id ."</b></td></tr><tr><td style='margin-left:auto; margin-right:auto'><b>Codigo: ". $precpResponse->codigo."</b></td></tr></table><br><!-- Datos generales --><table style='border:1px solid #acb7c0; border-radius: 10px; width:650px; '><tr><td style='text-align: left; padding: 10px'><b>Datos Generales:</b><br> ". $precpResponse->nombrefiscal." <br> ". $precpResponse->nombrecomercial." <br> ". $precpResponse->rfc." <br> ". $precpResponse->email." <br> Uso CFDI: ". $precpResponse->usocfdisat." <br> Unidad de peso: ". $precpResponse->unidadpeso."<br>Transporte internacional: ". $precpResponse->transpinternac." - ". $precpResponse->entradasalidamerc." <br> Pais Origen/Destino: ". $precpResponse->paisorigendestino."</td></tr></table><br><!-- Origen --><table style='border:1px solid #acb7c0; border-radius: 10px; width:650px; '><tr>". $origenhtml ."</tr></table><br><!-- Destino --><table style='border:1px solid #acb7c0; border-radius: 10px; width:650px; '><tr>". $destinohtml ."</tr></table><br><!-- Mercancia --><b style='text-align: left; padding: 10px'>Mercancia:</b><br><table style='border:1px solid #acb7c0; border-radius: 10px; width:650px; '>" . $detalleshtml . "</table><br><div style='width:650px;font-size:14px; font-style: oblique; text-align: right'>Esta pre-carta porte fue creada con (<a href='https://cartaportemexico.com'>cartaportemexico.com</a>)</div></div></body></html>";
            
        // $data = [
        //     "from" => env('MAIL_FROM_ADDRESS', 'no-responder@cartaportemexico.com'),env('MAIL_FROM_NAME', 'CARTAPORTEMEXICO'),
        //     "to" => $for,
        //     "subject" => $subject,
        //     "content" => $mail
        // ];

        // Mail::send([], [], function($message) use ($data) {
        //     $message->from($data['from']);
        //     $message->to($data['to']);
        //     $message->subject($data['subject']);
        //     $message->setBody($data['content'], 'text/html');
        // });



        $params = array(
            "url"=>env('SW_SERVICE', 'http://services.test.sw.com.mx'),
            "user"=> $accesos->usuario,
            "password"=> $accesos->contrasena,
        );
        try
        {
            header('Content-type: application/json');
            Authentication::auth($params);
            $token = Authentication::Token();

            $params = array(
                'url'=> env('SW_SERVICE', 'http://services.test.sw.com.mx'),
                'token'=> $token->data->token,
            );
        
            try {
                AccountBalanceService::Set($params);
                $result = AccountBalanceService::GetAccountBalance();
                // return ["result" => $result];
                
                
                $conceptos = null;
                $trasladosDetalle = null;
                $retencionesDetalle = null;
                $conceptohtml = "" ;
                // iva 002 o ieps 003 
                if($conceptoResource->importe_iva > 0){                    
                    $trasladosDetalle[0] = [
                        "Impuesto"=> "002",
                        "TipoFactor"=> "Tasa",
                        "TasaOCuota"=> $conceptoResource->p_iva,// seis decimales
                        "Base"=> $conceptoResource->base_iva,
                        "Importe"=> $conceptoResource->importe_iva
                    ];
                    $conceptohtml = $conceptohtml . "   Impuesto: 002 " . $conceptoResource->p_iva . " Importe IVA: " . $conceptoResource->importe_iva;
                    // si concepto tiene retencion iva o retencion isr
                    if($conceptoResource->importe_ret_iva){
                        $retencionesDetalle[0] = [
                            "TasaOCuota" => $conceptoResource->p_retencion_iva,
                            "Impuesto" => "002", //iva 002 isr 001
                            "TipoFactor" => "Tasa",
                            "Base" => $conceptoResource->base_iva, //base iva  base isr
                            "Importe" => $conceptoResource->importe_ret_iva
                        ];
                        $conceptohtml = $conceptohtml . "Retencion IVA: 002" . $conceptoResource->p_retencion_iva ." Importe Ret IVA: " . $conceptoResource->importe_ret_iva;
                    }
                    
                    
                }

                if($conceptoResource->importe_ieps > 0){                    
                    $trasladosDetalle[1] = [
                        "Impuesto"=> "003",
                        "TipoFactor"=> "Tasa",
                        "TasaOCuota"=> $conceptoResource->p_ieps,
                        "Base"=> $conceptoResource->base_ieps,
                        "Importe"=> $conceptoResource->importe_ieps
                    ];  
                    $conceptohtml = $conceptohtml . "   Impuesto: 003 " . $conceptoResource->p_ieps . " Importe IEPS: " . $conceptoResource->importe_ieps;         
                }

                if($conceptoResource->importe_ret_isr){
                    $retencionesDetalle[1] = [
                        "TasaOCuota" => $conceptoResource->p_retencion_isr,
                        "Impuesto" => "001", //iva 002 isr 001
                        "TipoFactor" => "Tasa",
                        "Base" => $conceptoResource->base_iva, //base iva  base isr
                        "Importe" => $conceptoResource->importe_ret_isr
                    ];
                    $conceptohtml = $conceptohtml . "Retencion ISR: 001" . $conceptoResource->p_retencion_isr . " Importe Ret ISR: " . $conceptoResource->importe_ret_isr;
                }

                $impuestosDetalle = [
                    "Traslados" => $trasladosDetalle,
                ];

                if($retencionesDetalle !== null){
                    $impuestosDetalle["Retenciones"] = $retencionesDetalle;
                }


                if($cpResponse->tipocomprobante === 'I'){
                    $conceptos[0] = [
                        "ClaveProdServ"=> trim($conceptoResource->claveprodserv),
                        "NoIdentificacion"=> trim($conceptoResource->clavearticulo),
                        "Cantidad"=> $conceptoResource->cantidad,
                        "ClaveUnidad"=> trim($conceptoResource->claveunidad),
                        "Unidad"=> $conceptoResource->presentacion !== "" ? trim($conceptoResource->presentacion) : null,
                        "Descripcion"=> trim($conceptoResource->descripcion),
                        "ValorUnitario"=> $conceptoResource->precio,
                        "Importe"=> $conceptoResource->importe, //cantidad * precio
                        "Impuestos"=> $impuestosDetalle,
                        "ObjetoImp" => "01"
                    ];
                } else {
                    $conceptos[0] = [
                        "ClaveProdServ"=> trim($conceptoResource->claveprodserv),
                        "NoIdentificacion"=> trim($conceptoResource->clavearticulo),
                        "Cantidad"=> trim($conceptoResource->cantidad),
                        "ClaveUnidad"=> trim($conceptoResource->claveunidad),
                        "Unidad"=> trim($conceptoResource->presentacion),
                        "Descripcion"=> trim($conceptoResource->descripcion),
                        "ValorUnitario"=> 0,
                        "Importe"=> 0
                    ];
                }
                $retenciones = null;
                if($cpResponse->retencion_iva > 0){
                    $retenciones[0] = [
                        "Impuesto" => "002",
                        "Importe" => $cpResponse->retencion_iva
                    ];
                }
                if($cpResponse->retencion_isr > 0){
                    $retenciones[1] = [
                        "Impuesto" => "001",
                        "Importe" => $cpResponse->retencion_isr
                    ];
                }
                $traslados = null;
                if($cpResponse->iva > 0){
                    $traslados[0] = [
                        "Impuesto" => "002",
                        "TipoFactor" => "Tasa",
                        "TasaOCuota" => $conceptoResource->p_iva,
                        "Importe" => $cpResponse->iva//documento iva
                    ];
                }
                if($cpResponse->ieps > 0){
                    $traslados[1] = [
                        "Impuesto" => "001",
                        "TipoFactor" => "Tasa",
                        "TasaOCuota" => $conceptoResource->p_ieps,
                        "Importe" => $cpResponse->ieps//documento iva
                    ];
                }

                $totalimpuestosRet = 0;
                $totalimpuestosTras = 0;
                if($traslados !== null){
                    foreach($traslados as $key => $traslado){
                        $totalimpuestosTras += $traslado["Importe"];
                    }
                }

                if($retenciones !== null){
                    foreach($retenciones as $key => $retencion){
                        $totalimpuestosRet += $retencion["Importe"];
                    }
                }


                $impuestos = [
                    
                    "Traslados" => $traslados,
                    "TotalImpuestosRetenidos" => $totalimpuestosRet,
                    "TotalImpuestosTrasladados" => $totalimpuestosTras
                ];
                if($retenciones !== null){

                    $impuestos["Retenciones"] = $retenciones;
                }

                $complemento = null;
                $any = null;

                $figuraTransporte = null;

                $vehiculo = Vehiculos::where('id', $cpResponse->idvehiculo)->first();
                //TO DO REvisar chofer o personal

                // si vehiculo propio solo va chofer
                // si vehiculo no es propio tipofigura 02 propietario 03 arrendador 04 notificado
                
                $chofer = Personal::where('id', $cpResponse->idchofer)->first();



                $domiciliotransporte = [
                    "Calle" => trim($chofer->calle),
                    "CodigoPostal" => trim($chofer->cp),
                    "Estado" => trim($chofer->clave_entfed),
                    "NumeroExterior" => trim($chofer->numext),
                    "Pais" => trim($chofer->pais),
                    // "Referencia" => null
                ];
                // figura transporte 01 chofer y siempre va
                // si no es chofer agregar ParteTransporte
                $figuraTransporte[0] = [
                    "Domicilio" => $domiciliotransporte,
                    "NombreFigura" => trim($chofer->nombre),
                    "NumLicencia" => trim($chofer->numlicencia),//solo chofer 
                    "RFCFigura" => trim($chofer->rfc),
                    "TipoFigura" => "01"
                ];

                $figuraTransportehtml = '';

                if($vehiculo->vehiculopropio === 0){
                    $propietario = Personal::where('id', $vehiculo->idpropietario)->first();

                    $domiciliotransporte = [
                        "Calle" => trim($propietario->calle),
                        "CodigoPostal" => trim($propietario->cp),
                        "Estado" => trim($propietario->clave_entfed),
                        "NumeroExterior" => trim($propietario->numext),
                        "Pais" => trim($propietario->pais),
                        // "Referencia" => null
                    ];
                    // figura transporte 01 chofer y siempre va
                    // si no es chofer agregar ParteTransporte
                    $figuraTransporte[0] = [
                        "Domicilio" => $domiciliotransporte,
                        "NombreFigura" => $propietario->nombre,
                        "RFCFigura" => $propietario->rfc,
                        "TipoFigura" => $propietario->tipofigura,
                        "ParteTransporte" => $vehiculo->partetransporte,//solo chofer 
                    ];

                    $figuraTransportehtml = " <div style='width:100%; border-top: 1px solid grey'></div> 
                    Tipo Figura: ". $propietario->tipofigura ."  RFC: " . trim($propietario->rfc) . "   Parte Transporte: " . trim($vehiculo->partetransporte) . "   Codigo Postal: " . trim($propietario->cp) . " <br> ";
                }



                $identificacionVehicular = [
                    "AnioModeloVM" => trim($vehiculo->ano),
                    "ConfigVehicular" => trim($vehiculo->configautotransportefed),
                    "PesoBrutoVehicular" => trim($vehiculo->pesobrutovehicular),
                    "PlacaVM" => trim($vehiculo->placas)
                ];

                $remolques = null;
                $remolquehtml = '';
                if($cpResponse->idremolque1 !== null && $cpResponse->idremolque1 !== ''){
                    $remolque1 = Vehiculo::where('id', $cpResponse->idremolque1)->first();
                    $remolques[0] = [
                        "Placa" => trim($remolque1->placas),
                        "SubTipoRem" => trim($remolque1->subtiporemolque)
                    ];
                    $remolquehtml = "Remolque: " . $remolque1->placas . " Subtipo Remolque: " . trim($remolque1->subtiporemolque) . "<br>";
                }
                if($cpResponse->idremolque2 !== null && $cpResponse->idremolque2 !== ''){
                    $remolque2 = Vehiculo::where('id', $cpResponse->idremolque2)->first();
                    $remolques[1] = [
                        "Placa" => trim($remolque2->placas),
                        "SubTipoRem" => trim($remolque2->subtiporemolque)
                    ];
                    $remolquehtml = $remolque . "Remolque: " . $remolque2->placas . " Subtipo Remolque: " . trim($remolque2->subtiporemolque) . "<br>";
                }

                $polizaRespCivil = Polizas::where('id', $cpResponse->idpolizarespcivil)->first();
                $aseguradoraRespCivil = Aseguradoras::where('id', $polizaRespCivil->idaseguradora)->first();
                $polizaCarga = Polizas::where('id', $cpResponse->idpolizacarga)->first();
                $aseguradoraCarga = Aseguradoras::where('id', $polizaCarga->idaseguradora)->first();

                $seguros = [
                    "AseguraCarga" => trim($aseguradoraCarga->aseguradora),
                    "AseguraRespCivil" => trim($aseguradoraRespCivil->aseguradora),
                    "PolizaCarga" => trim($polizaCarga->poliza),
                    "PolizaRespCivil" => trim($polizaRespCivil->poliza),
                    "PrimaSeguro" => trim($polizaRespCivil->primaseguro)
                    // AseguraMedAmbiente si lleva matpeligroso
                    // PolizaMedAmbiente
                ];
                if($contienematpeligroso === true){
                    $polizaMedAmbiente = Polizas::where('id', $cpResponse->idpolizamedambiente)->first();
                    $aseguradoraMedAmbiente = Aseguradoras::where('id', $polizaMedAmbiente->idaseguradora)->first();
                    $seguros["PolizaMedAmbiente"] = trim($polizaMedAmbiente->primaseguro);
                    $seguros["AseguraMedAmbiente"] = trim($aseguradoraMedAmbiente->aseguradora);

                }

                $autotransporte = [
                    "PermSCT" => trim($vehiculo->tipopermisosct),
                    "NumPermisoSCT" => trim($vehiculo->numpermiso),
                    // "PesoBrutoVehicular"=>  trim($vehiculo->pesobruto),
                    "IdentificacionVehicular" => $identificacionVehicular,
                    "Seguros" => $seguros
                ];
                
                if($remolques !== null){
                    $autotransporte["Remolques"] = $remolques;
                }
                


                $mercancias = [
                    "PesoBrutoTotal"=> trim($cpResponse->peso),
                    "UnidadPeso"=>trim($cpResponse->c_unidadpeso),
                    "NumTotalMercancias"=> trim($cpResponse->numtotalmercancias),
                    "Autotransporte" => $autotransporte,
                    "LogisticaInversaRecoleccionDevolucion"=> trim($cpResponse->logisticainversarecolecciondevolucion),
                    // "CargoPorTasacion"=> 1, //0 ???
                    "CargoPorTasacionSpecified"=> false,
                    "Mercancia" => $mercancia,
                    "NumberOfNodesTransports"=> 1,
                    // "PesoNetoTotal"=> $cpResponse->peso,
                    "PesoNetoTotalSpecified"=> false,
                ];             

                // Caracteres con letra "C"
                $prefix = "CCC"; // Reemplaza "XX" con los caracteres específicos que deseas usar
                
                // RFC 4122
                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
                
                // Patrón del Estándar documentación técnica CP V3.0
                $idCCP = $prefix . '-' . $uuid; // Combinando el prefijo con el UUID generado
                

                $complementoData = [
                    "Version" => "3.0",
                    "IdCCP"=> $idCCP,
                    "TranspInternac" => trim($cpResponse->transporteinternacional), // validar si es si pongo entrasalidamerc y pais origendestino
                    "TotalDistRec"=> $cpResponse->totaldistrec,
                    "RegistroISTMO" => $cpResponse->registroistmo,
                    "TotalDistRecSpecified"=> true,
                    "UbicacionPoloOrigen"=> $cpResponse->ubicacionpoloorigen,
                    "UbicacionPoloDestino"=> $cpResponse->ubicacionpolodestino,
                    "FiguraTransporte" => $figuraTransporte,
                    "Mercancias" => $mercancias,
                    "Ubicaciones" => $ubicaciones
                ];
                if($complementoData['TranspInternac'] == "Si"){
                    $complementoData["EntradaSalidaMerc"] = trim($cpResponse->entradasalidamerc);
                    $complementoData["PaisOrigenDestino"] = trim($cpResponse->paisorigendestino);
                    $complementoData["RegimenAduanero"] = trim($cpResponse->regimenaduanero);
                }

                $any[0] = [
                    "cartaporte20:CartaPorte" => $complementoData,
                ];
                $complemento[0] = [
                    "Any" => $any
                ];

                $uuid = null;
                $uuid[0] = [
                    "UUID"=> "59AB274B-6CBC-4176-9BD7-BB67B28D3EA4"
                ];
                $cfdiRelacionado = [
                    "CfdiRelacionado" => $uuid,
                    "TipoRelacion" => "04"
                ];

                if($cpResponse->tipocomprobante === 'I'){
                
                    $cartaporte = [
                        "Version" => "4.0",
                        "Serie" => "CPM",
                        "Folio" => trim($cpResponse->folio),//cada empresa empieza en 1
                        "Fecha" => trim($cpResponse->fecha),
                        "SubTotal"=> $cpResponse->subtotal,
                        "Moneda"=> "MXN",
                        "FormaPago"=> trim($cpResponse->formapago),// 01 efectivo 02 cheque 03 spei 04 tarjeta credito 28 tarjeta debito
                        "MetodoPago"=> "PUE",
                        "Total"=> $cpResponse->total,
                        "TipoDeComprobante"=> trim($cpResponse->tipocomprobante),
                        //exportacion
                        "LugarExpedicion"=> trim($empresa->cp), //cp emisor
                        // "TipoCambio"=> 1,
                        "Emisor" => $emisor,
                        "Receptor" =>$receptor,
                        "Conceptos" => $conceptos,
                        "Impuestos" => $impuestos,
                        "Complemento" => $complemento
                    ];
                } else {
                    $cartaporte = [
                        "Version" => "4.0",
                        "Serie" => "CPM",
                        "Folio" => trim($cpResponse->folio),
                        "Fecha" => trim($cpResponse->fecha),
                        "SubTotal" => 0,
                        "Total" => 0,
                        "TipoDeComprobante" => "T",
                        // "NoCertificado"=> "30001000000400002335",
                        "LugarExpedicion" => trim($empresa->cp),
                        // "CfdiRelacionados" => $cfdiRelacionado,
                        
                        "Impuestos" => null,
                        "Moneda" => "MXN",
                        "Emisor" => $emisor,
                        "Receptor" =>$receptor,
                        "Conceptos" => $conceptos,
                        "Complemento" => $complemento
                    ];

                }

                // echo json_encode($cartaporte, JSON_PRETTY_PRINT);
                $json = json_encode($cartaporte);

                try{
                    $basePath = "../public/xml/";
                    $jsonIssuerStamp = JsonEmisionTimbrado::Set($params);
                    $resultadoJson = $jsonIssuerStamp::jsonEmisionTimbradoV4($json);
                    if($resultadoJson->status=="success"){  
                        $dataJson = $resultadoJson->data;
                        $ruta = $basePath.$resultadoJson->data->uuid.".xml";
                        file_put_contents($ruta, $resultadoJson->data->cfdi);
                        $nombreyRuta = $resultadoJson->data->uuid.".png";
                        imagepng(imagecreatefromstring(base64_decode($resultadoJson->data->qrCode)), $basePath.$nombreyRuta);
                        $ruta2 = $basePath . $nombreyRuta;
                        $paisorigendestino = $cpResponse->entradasalidamerc == 'Salida' ? $cpDestino->pais : $cpOrigen->pais;
                        $cadenaoriginalsat1 = substr($dataJson->cadenaOriginalSAT, 0,100);
                        $cadenaoriginalsat2 = substr($dataJson->cadenaOriginalSAT, 100, 100);
                        $cadenaoriginalsat3 = substr($dataJson->cadenaOriginalSAT, 200, 100);
                        $cadenaoriginalsat4 = substr($dataJson->cadenaOriginalSAT, 300, 100);
                        $cadenaoriginalsat5 = substr($dataJson->cadenaOriginalSAT, 400, 100);
                        $selloCFDI = substr($dataJson->selloCFDI, 0,100);
                        $selloCFDI2 = substr($dataJson->selloCFDI, 100,100);
                        $selloCFDI3 = substr($dataJson->selloCFDI, 200,100);
                        $selloCFDI4 = substr($dataJson->selloCFDI, 300,100);
                        $selloSAT = substr($dataJson->selloSAT, 0,100);
                        $selloSAT2 = substr($dataJson->selloSAT, 100,100);
                        $selloSAT3 = substr($dataJson->selloSAT, 200,100);
                        $selloSAT4 = substr($dataJson->selloSAT, 300,100);
                        $transporteint = " Transporte Internacional: " . $cpResponse->transporteinternacional;
                        if($cpResponse->transporteinternacional == 'Si'){
                            $transporteint = $transporteint . " Entrada/Salida: " . $cpResponse->entradasalidamerc . " Pais: " . $paisorigendestino;    
                        } 
                        $html="<html><head></head><body><div style='margin-left:auto; margin-right: auto; width:650px;'><table style='width:650px; font-size:14px'><tr><td><img height='70' width='220' style='max-height: 70px; height:70px;max-width:220px' src='https://cartaportemexico.com/images/logoscpm2178x63.png?crc=449637831 alt=' No se encontro la imagen ' /></td><td style=' width:10px '></td><td style=' text-align:left;border:1px solid #acb7c0; border-radius: 10px; padding: 10px; width:420px '><b>Folio Fiscal(UUID): " . $dataJson->uuid . "</b><br>Certificado Digital SAT: " . $dataJson->noCertificadoSAT . "<br>Certificado Sello Digital No: " . $dataJson->noCertificadoCFDI . " <br>Fecha Certificacion: " . $dataJson->fechaTimbrado." <br>RFC Prov. Certificacion: SPR190613I52<br>Lugar Expedicion:" . $empresa->cp . "<br>Version CFDI: 3.3<br></td></tr></table><br><br><table><tr><td style=' border:1px solid #acb7c0; border-radius: 10px; width:310px; padding:10px '><b>Emisor:</b> <br><b>RFC:" . $empresa->rfc . "</b> <br><b>" . $empresa->nombrefiscal ."</b> <br>". $empresa->cp ."<br>". $empresa->estado ." <br>". $empresa->regimenfiscal ." <br></td><td style=' width:10px '></td><td style=' border:1px solid #acb7c0; border-radius: 10px; width:300px; padding:10px '><b>Carta PorteNo.: " . $cpResponse->folio . "</b><br>Fecha Emision:" . $cpResponse->fecha . "<br>Condiciones de Pago: Contado <br>Metodo Pago: " . $cpResponse->metodopago . "<br>Forma de pago: " . $cpResponse->formapago . "<br>Moneda: MXN <br>Tipo Comprobante:  " . $cpResponse->tipocomprobante . "</td></tr></table><table><tr><td style=' border:1px solid #acb7c0; border-radius: 10px; width:650px; padding:10px '><b>Receptor</b><br><b>RFC: " . $cpResponse->rfc . "</b> <br>" . $cpResponse->nombre . " <br>" . $cpResponse->codigopostal . " <br>Uso CFDI: ". $cpResponse->usocfdi . "</td></tr></table><table><tr><td style=' border:1px solid #acb7c0; border-radius: 10px; width:650px; padding:10px '><b>Complemento Carta Porte</b><br>Versión: 2.0 Total Dist Recorrida: " . $cpResponse->totaldistrec . $transporteint . " Via: 01</td></tr></table><table><tr><td style=' border:1px solid #acb7c0; border-radius: 10px; width:650px; padding:10px '><b>Transporte</b><br>Placas: " . trim($vehiculo->placas) . "  Año de Fabricacion: " . trim($vehiculo->ano) ."   Permiso SCT: " . trim($vehiculo->tipopermisosct) . "   Num Permiso SCT: " . trim($vehiculo->numpermiso) . "   Configuración Vehicular: " . trim($vehiculo->configautotransportefed) . " <br>Aseguradora Resp/Civil: " . trim($aseguradoraRespCivil->aseguradora) . " Poliza Resp Civil: " . trim($polizaRespCivil->poliza) . "  Prima Seguro: " . trim($polizaRespCivil->primaseguro) . " <br " . $remolquehtml . "</td></tr></table><table><tr><td style=' border:1px solid #acb7c0; border-radius: 10px; width:650px; padding:10px '><b>Figura Transporte</b><br>Tipo Figura: 01  RFC: " . trim($chofer->rfc) . "   Num Licencia: " . trim($chofer->numlicencia) . "   Codigo Postal: " . trim($chofer->cp) . " <br> " . $figuraTransportehtml . "</td></tr></table><table><tr><td style=' border:1px solid #acb7c0; border-radius: 10px; width:650px; padding:10px '><b>Conceptos</b><br>Descripcion " . trim($conceptoResource->descripcion) . " <br>Clave PyS: " . trim($conceptoResource->claveprodserv) . "   Clave Unidad: " . trim($conceptoResource->claveunidad) . "   Clave: " . trim($conceptoResource->clavearticulo) . "   Unidad Medida: " .  $conceptoResource->presentacion . " <br>Cantidad: " . $conceptoResource->cantidad . "   Valor Unitario: " . $conceptoResource->precio . $conceptohtml . "</td></tr></table><table style=' '><tr><td style='border:1px solid #acb7c0; border-radius: 10px; width:650px; text-align: left; padding: 10px '><b>Origen:</b><br>". $cpOrigen->nombre  . " <br>Residencia Fiscal: ". $cpOrigen->residenciafiscal ." <br>". $cpOrigen->rfc ." <br>". $cpOrigen->fechasalidallegada ." ". $cpOrigen->horasalidallegada ." <br>País Origen: ". $cpOrigen->pais." <br>". $cpOrigen->calle ." ". $cpOrigen->numext ." - ". $cpOrigen->numint .", ". $cpOrigen->c_colonia ." - " . $cpOrigen->colonia .", ". $cpOrigen->cp .", ". $cpOrigen->c_municpio ." - ". $cpOrigen->municipio .", ". $cpOrigen->clave_entfed." - ". $cpOrigen->estado ."</td></tr></table><br><!-- Destino --><table style=' '><tr><td style=' border:1px solid #acb7c0; border-radius: 10px; width:650px; text-align: left; padding: 10px '><b>Destino:</b><br>". $cpDestino->nombre ."<br>Residencia Fiscal: ". $cpDestino->residenciafiscal ." <br>". $cpDestino->rfc ." <br>". $cpDestino->fechasalidallegada ." ". $cpDestino->horasalidallegada ." <br>País Origen: ". $cpDestino->pais." <br>". $cpDestino->calle ." ". $cpDestino->numext ." - ". $cpDestino->numint .", ". $cpDestino->c_colonia ." - " . $cpDestino->colonia .", ". $cpDestino->cp .", ". $cpDestino->c_municpio ." - ". $cpDestino->municipio .", ". $cpDestino->clave_entfed." - ". $cpDestino->estado ."</td></tr></table><br><!-- Mercancia --><b style=' text-align: left; padding: 10px '>Mercancia:</b><br><table style=' '>" . $mercanciahtml . "</table><br><table style=' margin-left:auto; margin-right: auto; width:650px; font-size: 8px '><tr><td style=' width:470px; '><div style=' border-top:2pxsolid #acb7c0; width:470px; '></div><table style=' width:470px; '><tr><td style=' max-width: 470px; width:470px; word-break: break-all; font-size:8px '><b>Cadena Original delComplemento de certificacion digital del SAT:</b> <br>" . $cadenaoriginalsat1 . " <br>" . $cadenaoriginalsat2 . " <br>" . $cadenaoriginalsat3 . " <br>" . $cadenaoriginalsat4 . " <br>" . $cadenaoriginalsat5 . "</td></tr></table><div style=' border-top:1px solid #acb7c0; '></div><table style=' width:470px; '><tr><td style=' width:470px; word-break: break-all; font-size:8px '><b>Sello Digital delEmisor:</b> <br>" . $selloCFDI . " <br>" . $selloCFDI2 . "<br>" . $selloCFDI3 . "<br>" . $selloCFDI4 . "</td></tr></table><div style=' border-top:1px solid #acb7c0; '></div><table style=' width:470px; '><tr><td style=' width:470px; word-break: break-all; font-size:8px '><b>Sello digital del SAT:</b><br>" . $selloSAT . "<br>" . $selloSAT2 . "<br>" . $selloSAT3 . "<br>" . $selloSAT4 . "<br></td></tr></table></td><td style=' width:180px; '><img height=' 180 ' width=' 180 ' style=' max-height: 180px; height:180px;max-width:180px ' src='https://ubuntu.cartaportemexico.com/xml/ " . $resultadoJson->data->uuid . ".png ' alt=' No se encontrola imagen ' /></td></tr></table><!-- </footer> --><div style=' width:650px;font-size:8px; font-style: oblique; text-align: left; padding:5px '>* Este documento es una representacion impresa de un CFDI</div><div style=' width:650px;font-size:8px; font-style: oblique;text-align: right '>Este documento fue creado con Carta Porte Mexico (<a href=' http://www.cartaportemexico.com'>cartaportemexico.com</a>)</div></body><style>footer {position: fixed;bottom: 0cm;left: 0cm;right: 0cm;height: 2cm;font-size: 10px;}</style></html>";
                        $options = new Options();
                        $options->set('isRemoteEnabled',true); 
                        $dompdf = new Dompdf($options);
                        $dompdf->loadHtml($html);
                        $dompdf->render();
                        $output = $dompdf->output();
                        $ruta3 = $basePath . $resultadoJson->data->uuid .".pdf";
                        file_put_contents($ruta3, $output);

                        $subject = "CartaPorte";
                        $for = "diegochilomer@hotmail.com";
                        $data = ["name" => 'Diego'];
                        Mail::raw('Envio Carta Porte ', function($msj) use($subject,$for, $ruta, $ruta2, $ruta3){
                            $msj->from(env('MAIL_FROM_ADDRESS', 'diegochilomer@gmail.com'),env('MAIL_FROM_NAME', 'DIEGO'));
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($ruta);
                            $msj->attach($ruta2);
                            $msj->attach($ruta3);
                        });

                        $cpResponse->uuid = $resultadoJson->data->uuid;
                        $cpResponse->sellosat = $resultadoJson->data->selloSAT;
                        $cpResponse->sellodigital = $resultadoJson->data->selloCFDI;
                        $cpResponse->cadena = $resultadoJson->data->cadenaOriginalSAT;
                        // $cpResponse->archivo_xml = $resultadoJson->data->uuid.".xml";
                        $cpResponse->fechatimbrado = $resultadoJson->data->fechaTimbrado;
                        $cpResponse->save();
                        // $cpResponse->certificado = $resultadoJson->data->noCertificadoCFDI;
                        // $cpResponse->idE_Cert = $e_cert->idinterno;

                        return [
                            "Status" => 'Exito',
                        ];
                    }
                    else{
                        $rutaJsonError = $basePath . 'Error' . $cpResponse->folio .".txt";
                        $error = 'Error folio ' . $cpResponse->folio . ' RFC: ' .  $empresa->rfc . '\n Nombre: ' .  $empresa->nombrefiscal . '\n JSON: ' . json_encode($cartaporte, JSON_PRETTY_PRINT);
                        $file = fopen($rutaJsonError, "w+");
                        fwrite($file, "\n");
                        fwrite($file, ' RFC: ' .  $empresa->rfc . "\n");
                        fwrite($file, ' Nombre: ' .  $empresa->nombrefiscal . "\n");
                        fwrite($file, ' Mensaje: ' .  $resultadoJson->message . "\n");
                        fwrite($file, ' Mensaje Detalle: ' .  $resultadoJson->messageDetail . "\n");
                        fwrite($file, ' JSON: ' . json_encode($cartaporte, JSON_PRETTY_PRINT));

                        $subject = "CartaPorte Error Folio" . $cpResponse->folio;
                        $for = "diegochilomer@hotmail.com";
                        Mail::raw('Envio Carta Porte ', function($msj) use($subject,$for, $rutaJsonError){
                            $msj->from(env('MAIL_FROM_ADDRESS', 'diegochilomer@gmail.com'),env('MAIL_FROM_NAME', 'DIEGO'));
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($rutaJsonError);
                            
                        });
                        
                        // file_put_contents($rutaJsonError, $file);
                        return [
                            "Status" => 'Error',
                            "Mensaje" => $resultadoJson->message,
                            "MensajeDetalle" => $resultadoJson->messageDetail
                        ];
                    }
                }
                catch(Exception $e){
                    return $e->getMessage();
                }

            }
            catch(Exception $e){
                return $e->getMessage();
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        
        return [
            'documento' => $documento
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $codigo)
    {   
        $precp = CP::where('codigo', $codigo)->first();
        if(is_null($precp)){
            return [
                'respuesta' => false,
                'mensaje' => 'codigo no existe'];
        }
        
        $user = auth('api')->user();
        $userid = $user->idempresa;
        $folios = Folios::where('idempresa', $userid)->first();
        $message = '';
        if($precp->idcliente !== $userid){

        
            if(is_null($folios)){
                return [
                    'respuesta' => false,
                    'mensaje' => 'No existe registro de folios para esta empresa'
                ];
            } 
            if($folios->foliosprecp <= 0) {
                return [
                    'respuesta' => false,
                    'mensaje' => 'No cuenta con folios suficientes para importar cartaporte'
                ];
            }
            $software = Software::where('rfc', $request->rfcsoftware)->first();
            if(is_null($software)){
                $message = 'La precp se descargó pero el software no se encontro';
            }
            $precp->idsoftwaredescarga = $software ? $software->id : '';
        }
        $precp->idempresadescarga = $userid;
        $precp->fechaimportacion = new Carbon();
        $precp->save();

        $cpdetalle = CPMercancia::where('idprecp', $precp->id)->get();
        foreach($cpdetalle as $key => $detalle){
            $cpdetalle[$key]->pedimentos = Pedimentos::where('idcpdetalle', $detalle->id)->get();            
        }
        $precporigen = CPOrigenDestino::where('idprecp', $precp->id)->where('origendestino', 'Origen')->get();
        $precpdestino = CPOrigenDestino::where('idprecp', $precp->id)->where('origendestino', 'Destino')->get();
        if($precp->idcliente !== $userid){
            $folios->foliosprecp --;
            $folios->save();
        

            $bitacora = new Bitacora();
            $bitacora->idempresa = $userid;
            $bitacora->fecha = new Carbon();
            $bitacora->idprecp = $precp->id;
            $bitacora->observacion = 'Importacion pre carta porte';
            $bitacora->idsoftware = $software->id;
            $bitacora->tipo = 2;
            $bitacora->save();
        }
        

        

        return[
            'Documento' => $precp,
            'Detalles' => $cpdetalle,
            'Origenes' => $precporigen,
            'Destinos' => $precpdestino,
            'Folios' => is_null($folios) ? '' : $folios->foliosprecp ,
            'respuesta' => true,
            'mensaje' => $message
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CP  $empresa
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $empresa = new CP();
        
        $empresaResponse = $empresa->createOrUpdate($data, $idinterno);
    
            DocumentoResource::withoutWrapping();
            return new DocumentoResource($empresaResponse);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idinterno)
    {
        $empresa = CP::findOrFail($idinterno);
        if ($empresa !== null) {
            $empresa->fill($request->all());

                    $empresa->save();
            
                    return $this->show($empresa->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CP  $empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy(CP $empresa)
    {
        //
    }
}
