<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class APIController extends Controller {
    public function consumirAPI() {
        $client = new Client();
        $response = $client->get('https://rickandmortyapi.com/api/character', [
            'verify' => false, // Deshabilita la verificación de certificados SSL
        ]);

        $responseBody = (string) $response->getBody(); // Obtiene el cuerpo de la respuesta como una cadena
        $responseArray = json_decode($responseBody, true);

        return view('api.index', compact('responseArray'));
    }
}
