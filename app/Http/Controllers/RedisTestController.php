<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class RedisTestController extends Controller
{
    public function index()
    {
        // Stockons une valeur dans le cache Redis
        Cache::put('ma_cle', 'Coucou, c’est Redis !', 10);

        // Récupérons la valeur
        $valeur = Cache::get('ma_cle');

        // Retourne un JSON pour voir le résultat
        return response()->json([
            'message' => 'La valeur récupérée depuis Redis',
            'valeur' => $valeur
        ]);
    }


    //message venant du controller
    public function test()
    {
        // Écrire une clé
        Redis::set('clé', 'Coucou Redis depuis le contrôleur !');

        // Lire la clé
        $valeur = Redis::get('clé');

        return "La valeur de la clé est : " . $valeur;
    }
}
