<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Http\Tools\ParamTools;
use Auth;
use App\Models\User;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try
        {
            $levels              = $this->getQuery()->get();

            return response()->json([
                'data' => ['levels' => $levels]
            ], 200);
        }
        catch (\Throwable $th)
        {
            return response()->json([
                'data' => [
                    'message' => $th->getMessage()
                ],
            ], 500);
        }
    }

    private function getQuery()
    {
        return Level::query();
    }
}
