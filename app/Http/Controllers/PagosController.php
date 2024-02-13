<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Direcciones;
use App\Models\CPConceptos;
use App\Http\Resources\ClientesResource;
use Illuminate\Http\Request;
use App\Models\Documento;
use Illuminate\Support\Facades\DB;
use MercadoPago;  


class PagosController extends Controller
{
    public function __construct()  
   {  
     MercadoPago\SDK::setClientId(config('services.mercadopago.client_id'));  
     MercadoPago\SDK::setClientSecret(config('services.mercadopago.client_secret'));   
     MercadoPago\SDK::setAccessToken(config('services.mercadopago.token'));  
   }  
   public function checkout(Request $request)  
   {  
    $reques = $request->all();
    $documento= new Documento();
    $user = auth('api')->user();
    $userid = $user->idempresa;
    $data = [
      'idempresa' => 0,
      'tipodocto' => 'Folios',
      'idcliente' => $userid,
      'subtotal' => $reques['subtotal'],
      'iva' => $reques['iva'],
      'total' => $reques['total']
      // Usuario sw
      // passSW
      // RFC
      // nombre
    ];
    $documentoAnterior = DB::table('documento')->where('idempresa', 0)->orderBy('folio', 'desc')->first();
        // Documento::where('idempresa', $userid)->orderBy('folio', 'desc')->first();
        if($documentoAnterior == null){
            $folio = 1;
        } else {
            $folio = $documentoAnterior->folio + 1;
        }

        
        $documento->fill($data);
        $documento['folio'] = $folio;
        $documento->save();
        $cpResponse = Documento::orderBy('id', "desc")->first();

        $concepto['iddocumento'] = $cpResponse->id;
        $concepto['descripcion'] = $reques['descripcion'];
        $concepto['cantidad'] = $reques['cantidad'];
        $concepto['precio'] = $reques['total'];

        // CPConceptos::where('iddocumento', $id)->delete();
        $conceptoResource = new CPConceptos();
        $conceptoResource->fill($concepto);
        $conceptoResource->save();
        
    MercadoPago\SDK::setAccessToken(config('services.mercadopago.token')); 
     // valido que el usuario me envie su nombre y su email  
     // Crea un objeto de preferencia segun la documentación de MercadoPago  
     $name =$reques['nombre'];  
     $email =$reques['email'];  
     // una funcion que me crea un codigo de referencia que guardo en mi BD  
    //  $external_reference = $this->external_reference();  
     // inicia la creación de la preferencia  
     $preference = new MercadoPago\Preference();  
     // del artículo vendido  
     $item = new MercadoPago\Item();  
     $item->title = $reques['descripcion'];  
     $item->quantity = 1;  
     $item->unit_price = $reques['total'];  
     $preference->items = array($item);  
     //del comprador  
     $payer = new MercadoPago\Payer();  
     $payer->name = $name;  
     $payer->email = $email;  
     $preference->notification_url = "https://ubuntu.cartaportemexico.com/api/v1/empresa/asignarTimbres/" . $folio;
     $preference->payer = $payer;  
     // las url de retorno a donde mercadolibre nos redigirá despues de terminar el proceso de pago  
     // IMPORTANTE: No utilizar IPs en las url como 127.0.0.1 o 10.1.1.10 porque el SDK marcará un error       
     $preference->back_urls = array(  
       "success" => "http://cartaportemexico.com/Folios/" . $cpResponse->id . "/success",  
       "failure" => "http://cartaportemexico.com/Folios/" . $cpResponse->id . "failure",  
       "pending" => "http://cartaportemexico.com/Folios/" . $cpResponse->id . "/pending"  
     );  
    //  $preference->external_reference= $external_reference;  
     $preference->save();  
     $documento->idreferencia = $preference->id;
     $documento->save();
     // se guarda el pago en la BD en espera de las notificaciones por IPN  
    //  $payment = new Payment;  
    //  $payment->external_reference = $external_reference;  
    //  $payment->amount = $total;  
    //  $payment->name = $name;  
    //  $payment->email = $email;  
    //  $payment->estatus = 0;  
    //  $payment->save();  
     // retornamos a donde sea que este tu vista  
     return [
       "preference" => $preference,
       "id" => $preference->id,
       "initpoint" =>$preference->init_point,
       "sand_initpoint" =>$preference->sandbox_init_point
     ];
   }  

   public function checkoutSuperticket(Request $request)  
   {  
    $reques = $request->all();
    $documento= new Documento();
    $user = auth('api')->user();
    $userid = $user->idempresa;
    $data = [
      'idempresa' => 0,
      'tipodocto' => 'Folios',
      // 'idcliente' => $userid,
      'subtotal' => $reques['subtotal'],
      'iva' => $reques['iva'],
      'total' => $reques['total'],
      'usuariosw' => $reques['usuariosw'],
      'passsw' => $reques['passsw'],
      'rfc' => $reques['rfc'],
      'nombre' => $reques['nombre']
      // Usuario sw
      // passSW
      // RFC
      // nombre
    ];
    $documentoAnterior = DB::table('documento')->where('idempresa', 0)->orderBy('folio', 'desc')->first();
        // Documento::where('idempresa', $userid)->orderBy('folio', 'desc')->first();
        if($documentoAnterior == null){
            $folio = 1;
        } else {
            $folio = $documentoAnterior->folio + 1;
        }

        
        $documento->fill($data);
        $documento['folio'] = $folio;
        $documento->save();
        $cpResponse = Documento::orderBy('id', "desc")->first();

        $concepto['iddocumento'] = $cpResponse->id;
        $concepto['descripcion'] = $reques['descripcion'];
        $concepto['cantidad'] = $reques['cantidad'];
        $concepto['precio'] = $reques['total'];

        // CPConceptos::where('iddocumento', $id)->delete();
        $conceptoResource = new CPConceptos();
        $conceptoResource->fill($concepto);
        $conceptoResource->save();
        
    MercadoPago\SDK::setAccessToken(config('services.mercadopago.token')); 
     // valido que el usuario me envie su nombre y su email  
     // Crea un objeto de preferencia segun la documentación de MercadoPago  
     $name =$reques['nombre'];  
     $email =$reques['email'];  
     // una funcion que me crea un codigo de referencia que guardo en mi BD  
    //  $external_reference = $this->external_reference();  
     // inicia la creación de la preferencia  
     $preference = new MercadoPago\Preference();  
     // del artículo vendido  
     $item = new MercadoPago\Item();  
     $item->title = $reques['descripcion'];  
     $item->quantity = 1;  
     $item->unit_price = $reques['total'];  
     $preference->items = array($item);  
     //del comprador  
     $payer = new MercadoPago\Payer();  
     $payer->name = $name;  
     $payer->email = $email;  
     $preference->notification_url = "https://ubuntu.cartaportemexico.com/api/v1/empresa/asignarTimbres/" . $folio;
     $preference->payer = $payer;  
     // las url de retorno a donde mercadolibre nos redigirá despues de terminar el proceso de pago  
     // IMPORTANTE: No utilizar IPs en las url como 127.0.0.1 o 10.1.1.10 porque el SDK marcará un error       
     $preference->back_urls = array(  
       "success" => "http://cartaportemexico.com/Folios/" . $cpResponse->id . "/success",  
       "failure" => "http://cartaportemexico.com/Folios/" . $cpResponse->id . "failure",  
       "pending" => "http://cartaportemexico.com/Folios/" . $cpResponse->id . "/pending"  
     );  
    //  $preference->external_reference= $external_reference;  
     $preference->save();  
     $documento->idreferencia = $preference->id;
     $documento->save();
     // se guarda el pago en la BD en espera de las notificaciones por IPN  
    //  $payment = new Payment;  
    //  $payment->external_reference = $external_reference;  
    //  $payment->amount = $total;  
    //  $payment->name = $name;  
    //  $payment->email = $email;  
    //  $payment->estatus = 0;  
    //  $payment->save();  
     // retornamos a donde sea que este tu vista  
     return [
       "preference" => $preference,
       "id" => $preference->id,
       "initpoint" =>$preference->init_point,
       "sand_initpoint" =>$preference->sandbox_init_point
     ];
   }  
   // no olvidar crear las rutas   
   public function success(Request $request){  
     return 'success';     
   }  
   public function failure(Request $request){  
     return 'failure';  
   }  
   public function pending(Request $request){  
     return 'pending';  
   }  
}
