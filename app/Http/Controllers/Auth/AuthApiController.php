<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Seguridad\MenuController;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\Auth\ForgotMail;
use App\Models\Seguridad\PasswordResets;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{

    /**
     * Inicio de Sección al Sistema.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */
    public function login(LoginRequest $request)
    {
        try {
            $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
            if (!auth()->attempt(array($fieldType => $request->email, 'password' => $request->password))) return response()->json(['msj' => 'Usuario no encontrado en nuestra base de datos.'], 401);

            $user = User::find(Auth::user()->id);

            $tokenResult = $user->createToken('Personal Access Token');

            $token = $tokenResult->token;

            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            //obtengo los roles y permisos
            $user = (object) [
                'usuario_id' => encrypt($user->id),
                'name' => $user->name,
                'email' => $user->email,
                'user_name' => $user->user_name,
                'profile_photo_path' => $user->profile_photo_path,
            ];

            return response()->json([
                'user' => $user,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
            ]);
        } catch (Exception $e) {
            return response()->json(['mensaje' => $e->getMessage()], 500);
        }
    }

    /**
     * Cerrar Sección.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * @OA\Get(path="/api/auth/logout",tags={"auth"}, security={ {"bearer_token": {} }},
     *      @OA\Response(response=200,description="Success",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="Successfully logged out"),)),
     *      @OA\Response(response=401,description="Devuelve cuando el usuario no está autenticado",
     *          @OA\JsonContent(@OA\Property(property="msj", type="string", example="Usuario no encontrado en nuestra base de datos.")))
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
