<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Character;

class CharacterController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    // Validar y guardar los datos en la base de datos
    $data = $request->all(); // Obtener todos los datos en formato JSON

    // Crear una nueva instancia del modelo Character y asignar los valores
    $character = new Character();
    $character->nombre = $data['nombre'];
    $character->status = $data['status'];
    $character->especie = $data['especie'];
    $character->save();

    return response()->json(['message' => 'Datos guardados correctamente']);
    }

}
