<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Documento;
use App\Models\CPConceptos;
use App\Models\Folios;
use App\Models\Bitacora;
use App\Models\SWAccesos;
use App\Http\Resources\EmpresaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use SWServices\Authentication\AuthenticationService  as Authentication;
use SWServices\Usuario\UsuarioService as UsuarioSW;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client;
use App\User;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {  
        
        $empresa = Empresa::get();    

        return EmpresaResource::collection($empresa);
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

    public function obtenerUsuariosSW(Request $request){

        $data = $request->all();
        $params = array(
            "url"=>"https://services.sw.com.mx",
            "user"=> "controlatutienda@gmail.com",
            "password"=> "COS.2808.H"
        );
        try{
            header('Content-type: application/json');
            Authentication::auth($params);
            $token = Authentication::Token();

            $params = array(
                'url'=> "https://api.sw.com.mx",
                'token'=> $token->data->token
            );
            $resultadoUsuario = UsuarioSW::set($params);
            $result = UsuarioSW::ObtenerUsuarios();

            return [
                "resultado" => $result
            ];
            try{
                
            }catch(Exception $e){

            }

        } catch(Exception $e){

        }
    }

    public function crearUsuarioSW(Request $request){
        $user = auth('api')->user();
        $userid = $user->idempresa;

        $data = $request->all();
        $params = array(
            "url"=>env('SW_SERVICE_ADM', 'https://api.test.sw.com.mx'),
            "user"=> env('SW_USER_ADM', 'controlatutienda@gmail.com'),
            "password"=> env('SW_PASSWORD_ADM', 'Compart+SW')
        );
        try{
            header('Content-type: application/json');
            Authentication::auth($params);
            $token = Authentication::Token();

            $params = array(
                'url'=> env('SW_SERVICE_ADM', 'https://api.test.sw.com.mx'),
                'token'=> $token->data->access_token,
                
                'email'=> $data['email'],
                'password'=>$data['password'],
                'name'=>$data['name'],
                'rfc'=>$data['rfc'],
                'profile'=> 3,
                'stamps'=> 0,
                'unlimited' => false,
                'active' => true
            );
            $resultadoUsuario = UsuarioSW::set($params);
            $result = UsuarioSW::CreateUser();
            if($result->status === 'success'){
                DB::table('swaccesos')->insert([
                    'idempresa' => $userid,
                    'usuario' => $data['email'],
                    'contrasena' => $data['password'],
                    'idsw' => $result->data
                ]);
                
            }
            return [
                "resultado" => $result
            ];
            try{
                
            }catch(Exception $e){

            }

        } catch(Exception $e){

        }
    }

    public function asignarTimbres(Request $request, $id){
        $folio = $id;
        $documento = Documento::where('folio', $folio)->where('tipodocto', 'Folios')->first();
        $concepto = CPConceptos::where('iddocumento', $documento->id)->first();
        $empresaid = $documento->idcliente;
        // $user = auth('api')->user();
        // $userid = $user->idempresa;

        $data = $request->all();

        $accesos = SWAccesos::where('idempresa', $empresaid)->first();
        $params = array(
            "url"=>env('SW_SERVICE_ADM', 'https://api.test.sw.com.mx'),
            "user"=> env('SW_USER_ADM', 'controlatutienda@gmail.com'),
            "password"=> env('SW_PASSWORD_ADM', 'Compart+SW')
        );

        $payment_id = $data['data']['id'];

        $client = new Client();
        $url = "https://api.mercadopago.com/v1/payments/" . $payment_id . "?access_token=" . env('MP_ACCESS_TOKEN', 'TEST-4613792530556861-060314-b403a7a0bbf43d91437cf5639e4e9b03-1136093054');
        $request = $client->request('GET', $url);
        $response = json_decode($request->getBody());

        // $response = HTTP::get($url);
        // $response = json_decode($response);
       
        if($response->status == 'approved'){
            try{
                header('Content-type: application/json');
                Authentication::auth($params);
                $token = Authentication::Token();
                $cantidad = (int) $concepto->cantidad;
    
                $params = array(
                    'url'=> env('SW_SERVICE_ADM', 'https://api.test.sw.com.mx'),
                    'token'=> $token->data->access_token,
                    
                    'idUser'=> $accesos->idsw,
                    'stamps' => $cantidad,
                    'comentario' => "compra de " . $cantidad . " folios"
    
                    
                );
                $resultadoUsuario = UsuarioSW::set($params);
                $result = UsuarioSW::AsignarTimbres();
    
                if($result->status === 'success'){
                    $folios = Folios::where('idempresa', $empresaid)->first();
                    $folios->foliostimbres = $folios->foliostimbres + $cantidad;
                    $folios->fechaultcompratimbres = new Carbon();
                    $folios->save();
                    $documento->valorpagado = $documento->total;
                    $documento->save();
                }
                return [
                    "resultado" => $result
                ];
                try{
                    
                }catch(Exception $e){
    
                }
    
            } catch(Exception $e){
    
            }

        }
        // {
        //     "id": 12345,
        //     "live_mode": true,
        //     "type": "payment",
        //     "date_created": "2015-03-25T10:04:58.396-04:00",
        //     "application_id": 123123123,
        //     "user_id": 44444,
        //     "version": 1,
        //     "api_version": "v1",
        //     "action": "payment.created",
        //     "data": {
        //         "id": "999999999"
        //     }
        //   }
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $empresa = new Empresa();
        
        $empresa->fill($request->all());
        $data['cancelado'] = 0;
        $id = $empresa->insertGetId($data);
        $empresa->id = $id;

        if($empresa->tipoEmpresa === 3){

        } else {
            $data = [
                'idempresa' => $id,
                'foliostimbres' => 0,
                'foliosprecp' => 10
            ];
            $folio = new Folios();

            $folio->fill($data);
            $folio->save(); 
        }
        // $bitacora = new Bitacora();
        // $bitacora->idempresa = $id;
        // $bitacora->fecha = new Carbon();
        // $bitacora->idprecp = $precp->id;
        // $bitacora->observacion = 'Importacion pre carta porte';
        // $bitacora->idsoftware = $software->id;
        // $bitacora->tipo = 2;
        // $bitacora->save();
        
        $pass = bcrypt($empresa->contrasena);

        $arreglo_datos = [
            'idempresa' => $id,
            'name' => $empresa->nombrefiscal,
            'email' => $empresa->email,
            'password' => $pass
        ];
        User::create($arreglo_datos);  

        $subject = "Registro Carta Porte Mexico";
        $for = $empresa->email;
        if($empresa->tipoEmpresa === 3){
            $mail = "<html><div style='margin-left:auto; margin-right: auto; width:650px;'><table style='width:650px; font-size:14px'><tr><td><img src='https://cartaportemexico.com/images/logoscpm2178x63.png?crc=449637831' alt=''></td></tr><tr><td style='text-align: center'><h1>Bienvenido a Carta Porte Mexico</h1><h3></h3></td></tr><tr><td><p>Gracias por registrarte en carta porte México, <br> A partir de ahora podras crear y editar tus pre-carta porte, asi como guardar tus articulos, origenes y destinos para facilitar tus capturas<br>Recuerda iniciar sesion con tu email ycontraseña</p></td></tr></table><br></div></html>";
        } else {
            $mail = "<html><div style='margin-left:auto; margin-right: auto; width:650px;'><table style='width:650px; font-size:14px'><tr><td><img src='https://cartaportemexico.com/images/logoscpm2178x63.png?crc=449637831' alt=''></td></tr><tr><td style='text-align: center'><h1>Bienvenido a Carta Porte Mexico</h1><h3></h3></td></tr><tr><td><p>Gracias por registrarte en carta porte México, <br> A partir de ahora podras importar y exportar tus pre-carta porte<br>Recuerda iniciar sesion con tu email y contraseña</p></td></tr></table><br></div></html>";            
        }
            
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
        
        EmpresaResource::withoutWrapping();
        return new EmpresaResource($empresa);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $idinterno
     * @return \Illuminate\Http\Response
     */
    public function show($idinterno)
    {
        EmpresaResource::withoutWrapping();
        return new EmpresaResource(Empresa::find($idinterno));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idinterno)
    {
        $data = $request->all();

        $empresa = new Empresa();
        
        $empresaResponse = $empresa->createOrUpdate($data, $idinterno);
    
            EmpresaResource::withoutWrapping();
            return new EmpresaResource($empresaResponse);
    }

    public function createUser(Request $request)
    {
        $pass = bcrypt($request->pass);
        $arreglo_datos = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $pass
        ];
        $user = User::where('email', $request->email)->first();
        return User::create($arreglo_datos);
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
        $empresa = Empresa::findOrFail($idinterno);
        if ($empresa !== null) {
            $empresa->fill($request->all());

                    $empresa->save();
            
                    return $this->show($empresa->idinterno);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Empresa $empresa)
    {
        //
    }
}
