<?php

namespace App\Http\Controllers;

use App\Models\PreCP;
use App\Models\Folios;
use App\Models\Pedimentos;
use App\Models\Software;
use App\Models\Bitacora;
use App\Models\PreCPDetalle;
use App\Models\PreCPOrigenDestino;
use App\Http\Resources\PreCPResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class PreCPController extends Controller
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

        $precpResponse = PreCP::where('idcliente', $id)->get();
        return PreCPResource::collection($precpResponse);
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
        $precpResponse = PreCP::where('idcliente', $idinterno)->get();
        return PreCPResource::collection($precpResponse);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $documento = $request->documento;
        $detalles = $request->detalles;
        $origenes = $request->origenes;
        $destinos = $request->destinos;
        $documento['fecha'] = new Carbon();
        if(!isset($documento['id'])){
            $codigo = strtoupper(str_random(10));
            $documento['codigo'] = $codigo;
    
            $software = Software::where('rfc', $request->rfcsoftware)->first();
            if(!is_null($software)){
                $documento['idsoftwarecarga'] = $software ? $software->id : '';
            }
        }else{
            $codigo=$documento['codigo'];
        }
        
        $numtotalmercancias = 0;
        $pesobrutototal = 0;



        foreach($detalles as $key => $detalle){
            $numtotalmercancias += 1;
            $pesobrutototal += $detalle['pesoenkg'];
        }


        // foreach($origenes as $key => $detalle){
        //     // kmrecorridos
        // }

        // foreach($destinos as $key => $detalle){
        //     // kmrecorridos
        // }

        $documento['numtotalmercancias'] = $numtotalmercancias;        
        $documento['pesobrutototal'] = $pesobrutototal;     
           
        if(!isset($documento['id'])){
            $precp = new PreCP();
            $precp->fill($documento);
            $precp->save();
            $precpResponse = PreCP::where('codigo', $codigo)->first();
            
        } else{
            $precpResponse = PreCP::where('id', $documento['id'])->first();
            // unset($documento['fechaimportacion']);
            $precpResponse->fill($documento);
            $precpResponse->save();

        }

        $id = $precpResponse->id;

        $detalleshtml = '';
        PreCPDetalle::where('idprecp', $id)->delete();
        foreach($detalles as $key => $detalle){
            if(isset($detalle['pedimentos'])){
                $pedimentos = $detalle['pedimentos'];
                unset($detalle['pedimentos']);
            }
            $detalle['idprecp'] = $id;
            $precpdetalle = new PreCPDetalle();
            $precpdetalle->fill($detalle);
            $precpdetalle->save();
            $esmatpeligroso = $precpdetalle->materialpeligroso == 0 ? "No" : "Si";

            $detalleshtml = $detalleshtml . "<tr style='border-bottom:1px solid #bdc5cc'>
                <td style='text-align: left; padding: 10px; border-bottom:1px solid #bdc5cc'>". $precpdetalle->clavearticulo ." - ". $precpdetalle->descripcion ." <br> Clave Producto: ". $precpdetalle->bienestransp ." <br> Unidad: ". $precpdetalle->unidad ." - ". $precpdetalle->claveunidad ." <br> Cantidad: ". $precpdetalle->cantidad ." Peso Total: ". $precpdetalle->pesoenkg ." ". $documento['unidadpeso'] ." <br> Material Peligroso: ". $esmatpeligroso ." - ". $precpdetalle->clavematpeligroso ." <br> Tipo Embalaje: ". $precpdetalle->claveembalaje ." <br> Pedimento: ". $precpdetalle->pedimentos ." <br>Fraccion arancelaria: ". $precpdetalle->fraccionarancelaria ." <br>uuid Comercio Exterior: ". $precpdetalle->uuidcomercioext ." <br></td>
            </tr>";
            if(isset($pedimentos)){
                $detalleResponse = PreCPDetalle::where('idprecp', $id)->where('descripcion', $precpdetalle->descripcion)->orderBy('id', 'desc')->first();
                foreach($pedimentos as $key => $pedimento){
                    $data = [
                        "idprecp"=> $id,
                        "idprecpdetalle" => $detalleResponse->id,
                        "pedimento" => $pedimento
                    ];
                    $pedimentoRequest = new Pedimentos();
                    $pedimentoRequest->fill($data);
                    $pedimentoRequest->save();
                }
            }

        }
        $origenhtml = '';
        PreCPOrigenDestino::where('idprecp', $id)->delete();
        foreach($origenes as $key => $detalle2){
            $detalle2['idprecp'] = $id;
            $detalle2['origendestino'] = 'Origen';
            $precpOrigen = new PreCPOrigenDestino();
            $precpOrigen->fill($detalle2);
            $precpOrigen->save();
            $origenhtml = "<td style='text-align: left; padding: 10px'><b>Origen:</b><br>". $precpOrigen->nombre ."<br> Residencia Fiscal: ". $precpOrigen->residenciafiscal ." <br> ". $precpOrigen->rfc ." <br> ". $precpOrigen->fechasalidallegada ." ". $precpOrigen->horasalidallegada ." <br> País Origen: ".
            $precpOrigen->pais ." <br>". $precpOrigen->calle ." ". $precpOrigen->numext ." - ". $precpOrigen->numint .", ". $precpOrigen->c_colonia ." - " . $precpOrigen->colonia .", ". $precpOrigen->cp .", ". $precpOrigen->c_municpio ." - ". $precpOrigen->municipio .", ". $precpOrigen->clave_entfed ." - ". $precpOrigen->estado ."</td>";
        }
        $destinohtml = '';
        foreach($destinos as $key => $detalle1){
            $detalle1['idprecp'] = $id;
            $detalle1['origendestino'] = 'Destino';
            $precpDestino = new PreCPOrigenDestino();
            $precpDestino->fill($detalle1);
            $precpDestino->save();
            $destinohtml = "<td style='text-align: left; padding: 10px'><b>Destino:</b><br>". $precpDestino->nombre ."<br> Residencia Fiscal: ". $precpDestino->residenciafiscal ." <br> ". $precpDestino->rfc ." <br> ". $precpDestino->fechasalidallegada ." ". $precpDestino->horasalidallegada ." <br> País Origen: ".
            $precpDestino->pais ." <br>". $precpDestino->calle ." ". $precpDestino->numext ." - ". $precpDestino->numint .", ". $precpDestino->c_colonia ." - " . $precpDestino->colonia .", ". $precpDestino->cp .", ". $precpDestino->c_municpio ." - ". $precpDestino->municipio .", ". $precpDestino->clave_entfed ." - ". $precpDestino->estado ."</td>";
        }

        $subject = "Pre-Carta Porte "  . $id;
        $for = $documento['email'];
        $mail = "<html><head></head><body><div style='margin-left:auto; margin-right: auto; width:650px;'><table style='width:650px; font-size:14px'><tr><td><img src='https://cartaportemexico.com/images/logoscpm2178x63.png?crc=449637831' alt=''></td></tr><tr><td style='text-align: center'><h1>Tu carta porte ha sido creada con exito</h1><h3>Muestra tú codigo a tu transportista </h3></td></tr></table><br><table style='width:650px'><tr><td style='margin-left:auto; margin-right:auto'><b>Id:". $precpResponse->id ."</b></td></tr><tr><td style='margin-left:auto; margin-right:auto'><b>Codigo: ". $precpResponse->codigo."</b></td></tr></table><br><!-- Datos generales --><table style='border:1px solid #acb7c0; border-radius: 10px; width:650px; '><tr><td style='text-align: left; padding: 10px'><b>Datos Generales:</b><br> ". $precpResponse->nombrefiscal." <br> ". $precpResponse->nombrecomercial." <br> ". $precpResponse->rfc." <br> ". $precpResponse->email." <br> Uso CFDI: ". $precpResponse->usocfdisat." <br> Unidad de peso: ". $precpResponse->unidadpeso."<br>Transporte internacional: ". $precpResponse->transpinternac." - ". $precpResponse->entradasalidamerc." <br> Pais Origen/Destino: ". $precpResponse->paisorigendestino."</td></tr></table><br><!-- Origen --><table style='border:1px solid #acb7c0; border-radius: 10px; width:650px; '><tr>". $origenhtml ."</tr></table><br><!-- Destino --><table style='border:1px solid #acb7c0; border-radius: 10px; width:650px; '><tr>". $destinohtml ."</tr></table><br><!-- Mercancia --><b style='text-align: left; padding: 10px'>Mercancia:</b><br><table style='border:1px solid #acb7c0; border-radius: 10px; width:650px; '>" . $detalleshtml . "</table><br><div style='width:650px;font-size:14px; font-style: oblique; text-align: right'>Esta pre-carta porte fue creada con (<a href='https://cartaportemexico.com'>cartaportemexico.com</a>)</div></div></body></html>";
            
        $data = [
            "from" => env('MAIL_FROM_ADDRESS', 'no-responder@cartaportemexico.com'),env('MAIL_FROM_NAME', 'CARTAPORTEMEXICO'),
            "to" => $for,
            "subject" => $subject,
            "content" => $mail
        ];

        Mail::send([], [], function($message) use ($data) {
            $message->from($data['from']);
            $message->to($data['to']);
            $message->subject($data['subject']);
            $message->setBody($data['content'], 'text/html');
        });
        
        return [
            'documento' => $precpResponse,
            'codigo' => $codigo
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
        $precp = PreCP::where('codigo', $codigo)->first();
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

        $precpdetalle = PreCPDetalle::where('idprecp', $precp->id)->get();
        foreach($precpdetalle as $key => $detalle){
            $precpdetalle[$key]->pedimentos = Pedimentos::where('idprecpdetalle', $detalle->id)->get();            
        }
        $precporigen = PreCPOrigenDestino::where('idprecp', $precp->id)->where('origendestino', 'Origen')->get();
        $precpdestino = PreCPOrigenDestino::where('idprecp', $precp->id)->where('origendestino', 'Destino')->get();
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
            'Detalles' => $precpdetalle,
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
     * @param  \App\PreCP  $empresa
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $empresa = new PreCP();
        
        $empresaResponse = $empresa->createOrUpdate($data, $idinterno);
    
            PreCPResource::withoutWrapping();
            return new PreCPResource($empresaResponse);
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
        $empresa = PreCP::findOrFail($idinterno);
        if ($empresa !== null) {
            $empresa->fill($request->all());

                    $empresa->save();
            
                    return $this->show($empresa->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PreCP  $empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreCP $empresa)
    {
        //
    }

    public function getXML(Request $request)
    {
        // if($request->hasFile('IMG_20200715_153300.jpg')) {
            $file = $request->file('file');
             
            //get filename with extension
            // $filenamewithextension = $file->getClientOriginalName();
     
            // //get filename without extension
            // $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
     
            // //get file extension
            // $extension = $file->getClientOriginalExtension();
     
            // //filename to store
            // $filenametostore = trim($Cliente->rfc) . '.'.$extension;
     
            // //Upload File to external server
            // // Storage::disk('ftp')->put($filenametostore, fopen($file, 'r+'));
    

            // $basePath = "../public/regimen/";
            // $ruta2 = $basePath . $filenametostore;
            // file_put_contents($ruta2, fopen($file, 'r+'));
            $xmlString = file_get_contents($file); 
            $xmlString = str_replace("<tfd:", "<cfdi:", $xmlString);
            $xmlString = str_replace("<cfdi:", "<", $xmlString);
            $xmlString = str_replace("</cfdi:", "</", $xmlString);
            $xmlString = str_replace("@attributes", "attributes", $xmlString);
            $xmlObject = simplexml_load_string(utf8_encode($xmlString)); 

            

            $json = json_encode($xmlObject); 
            $phpArray = json_decode($json, true);  

            

            Return $phpArray;
    }
}
