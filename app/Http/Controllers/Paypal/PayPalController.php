<?php

namespace App\Http\Controllers\Paypal;

use App\Http\Controllers\Controller;
use App\Models\Anuncios;
use App\Models\PaquetesPremium;
use App\Models\User;
use App\Models\UsuariosPremium;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    private $base;
    private $client;

    public function __construct()
    {
        $this->base = 'https://api-m.sandbox.paypal.com';
        $this->client = new Client();
    }

    private function generateAccessToken()
    {
        try {
            $clientId = env('PAYPAL_CLIENT_ID');
            $clientSecret = env('PAYPAL_SECRET');

            if (!$clientId || !$clientSecret) {
                throw new \Exception('MISSING_API_CREDENTIALS');
            }

            $response = $this->client->post("{$this->base}/v1/oauth2/token", [
                'auth' => [$clientId, $clientSecret],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['access_token'];
        } catch (RequestException $e) {
            Log::error('Failed to generate Access Token:', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function handleResponse($response)
    {
        try {
            $jsonResponse = json_decode($response->getBody()->getContents(), true);
            return [
                'jsonResponse' => $jsonResponse,
                'httpStatusCode' => $response->getStatusCode(),
            ];
        } catch (\Exception $e) {
            throw new \Exception($response->getBody()->getContents());
        }
    }

    public function createOrder(Request $request)
    {
        try {
            $cart = $request->input('cart');
            Log::info('shopping cart information passed from the frontend createOrder() callback:', ['cart' => $cart]);

            $accessToken = $this->generateAccessToken();
            if (!$accessToken) {
                return response()->json(['error' => 'Failed to generate access token.'], 500);
            }

            $price = PaquetesPremium::Find($cart['id'])->precio;

            $url = "{$this->base}/v2/checkout/orders";
            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'EUR',
                            'value' => $price,
                        ],
                    ],
                ],
            ];

            $user = Auth::user();

            $email = $user->email;
            $name = $user->name;

            /* $payer = [
                'email_address' => $email,
                'name' => [
                    'given_name' => $name,
                    'surname' => ''
                ]
            ];*/

            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$accessToken}",
                ],
                'json' => $payload,
            ]);

            $result = $this->handleResponse($response);
            return response()->json($result['jsonResponse'], $result['httpStatusCode']);
        } catch (\Exception $e) {
            Log::error('Failed to create order:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create order.', 'error' => $e->getMessage()], 500);
        }
    }

    public function captureOrder($id)
    {
        try {
            // Asumiendo que ya tienes el $data disponible
            $accessToken = $this->generateAccessToken();

            if (!$accessToken) {
                return response()->json(['error' => 'Failed to generate access token.'], 500);
            }

            $requestUrl = "{$this->base}/v2/checkout/orders/$id/capture";

            $response = $this->client->request('POST', $requestUrl, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer $accessToken"
                ]
            ]);

            /* return (string) ($response->getBody()); */
            $data = json_decode($response->getBody(), true);

            //dd($data);

            $response = [];

            if ($data['status'] === 'COMPLETED') {
                // Obtener el paymentId y el monto pagado, de $data
                $payPalPaymentId = $data['purchase_units'][0]['payments']['captures'][0]['id'];
                $amount = $data['purchase_units'][0]['payments']['captures'][0]['amount']['value'];

                // Convierte el valor a un entero
                $amountInteger = intval($amount);

                $update = 0;

                // Obtener información del paquete
                $info_paquete = PaquetesPremium::where('precio', $amountInteger)->first();

                if (!$info_paquete) {
                    throw new \Exception("No se encontró un paquete con el monto especificado.");
                }

                $id_paquete = $info_paquete->id;
                $cant_dias = $info_paquete->cantidad_dias;

                // Obtener el id del usuario logueado
                $userId = Auth::id();
                $user_premium = User::Find($userId);

                // Calcular fechas de creación y vencimiento
                $fechaCreacion = Carbon::now();

                if($user_premium->usuario_premium == 1){
                    $info_premium = UsuariosPremium::where('id_user', $userId)->orderBy('id', 'desc')->first();
                    $fechaCreacionString = $info_premium->fecha_vencimiento;
                    $fechaCreacion = Carbon::parse($fechaCreacionString); // Convertir la cadena a una instancia de Carbon
                    $update = 1;
                }

                $fechaVencimiento = $fechaCreacion->copy()->addDays($cant_dias);

                // Realizar el insert en la tabla usuarios_premium
                $create_premium = DB::table('usuarios_premium')->insert([
                    'id_user' => $userId,
                    'id_paquete' => $id_paquete,
                    'pay_payment_id' => $payPalPaymentId,
                    'fecha_creacion' => $fechaCreacion,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'estado' => 1 // o cualquier valor que necesites
                ]);

                if($update == 0){
                    $user_premium->usuario_premium = 1;
                    $user_premium->save();

                    $anuncios_premium = [
                        'premium' => true,
                        'fecha_reactivacion' => now(),
                    ];

                    // Realizar la actualización de los anuncios premium
                    Anuncios::where('id_usuario', $user_premium->id)
                            ->where('estado', 1)
                            ->update($anuncios_premium);
                }

            }

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('Failed to register premium user:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to register premium user.', 'error' => $e->getMessage()], 500);
        }
    }
}
