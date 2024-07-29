<?php


namespace App\Http\Traits\Premium;

use App\Models\PaquetesPremium;
use App\Models\User;
use App\Models\UsuariosPremium;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait traitPremium
{
    public function index()
    {
        // Obtener el id del usuario logueado
        $userId = Auth::id();
        $user = User::Find($userId);
        if ($user->usuario_premium) {

            $info_premium = UsuariosPremium::where('id_user', $userId)->first();

            $info_paquete = PaquetesPremium::where('id', $info_premium->id_paquete)->first();

            if (!$info_paquete) {
                throw new \Exception("No se encontró un paquete con el monto especificado.");
            }

            $nombre_paquete = $info_paquete->nombre_paquete.' X '.$info_paquete->cantidad_dias.' Días';
            $referencia_pago = $info_premium->pay_payment_id;
            $premium_hasta = $info_premium->fecha_vencimiento;

            /* print_r($nombre_paquete);
            die(); */

            $data = [
                'nombre_paquete' => $nombre_paquete,
                'referencia_pago' => $referencia_pago,
                'premium_hasta' => $premium_hasta
            ];

            return view('premium.infoPremium', ['nombre_paquete' => $data['nombre_paquete'], 'referencia_pago' => $referencia_pago,
                'premium_hasta' => $premium_hasta]);

        } else {

            return view('premium.index', []);

        }
    }
}
