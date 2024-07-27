<?php

namespace App\Http\Controllers\Paypal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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

            $url = "{$this->base}/v2/checkout/orders";
            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'EUR',
                            'value' => '5',
                        ],
                    ],
                ],
            ];

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

        $data = json_decode($response->getBody(), true);

        //dd($data);

        if ($data['status'] === 'COMPLETED') {
            // Obtener el paymentId y el monto pagado, de $data
            $payPalPaymentId = $data['purchase_units'][0]['payments']['captures'][0]['id'];
            $amount = $data['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
        }

        return response()->json($data);



        /*  $url = "{$this->base}/v2/checkout/orders";
        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value' => '5',
                    ],
                ],
            ],
        ];

        $response = $this->client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$accessToken}",
            ],
            'json' => $payload,
        ]);

        $result = $this->handleResponse($response);
        return response()->json($result['jsonResponse'], $result['httpStatusCode']); */
    }
}
