<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Folios;
use App\Models\SWAccesos;
use App\User;

class AuthController extends Controller
{
    //
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function create($request)
    {
        $pass = bcrypt($request->pass);
        $arreglo_datos = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $pass
        ];
        $user = User::where('email', $email)->first();
        return User::create($arreglo_datos);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth('api')->user();
        $empresa = Empresa::where('id', $user->idempresa)->first();
        $folios= Folios::where('idempresa', $empresa->id)->first();
        $usuarioSW= SWAccesos::where('idempresa', $empresa->id)->first();
        if(is_null($usuarioSW)){
            $usuarioSWExiste = false;
        } else {
            $usuarioSWExiste = true;
        }
        return [
            "user" => $user,
            "empresa" => $empresa,
            "folios" => $folios,
            "usuarioSW" =>$usuarioSWExiste
        ];
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' => auth()->factory()->getTTL() * 60
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
