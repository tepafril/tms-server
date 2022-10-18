<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Type;
use App\Http\Tools\ParamTools;
use Auth;
use App\Models\User;

class TypeController extends Controller
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
            $user = $request->user();
            $permissions =  $user->permission->pluck("name");

            for ($i=0; $i<count($permissions); $i++) {
                $permissions[$i] = str_replace('create-', '', $permissions[$i]);
                $permissions[$i] = str_replace('edit-', '', $permissions[$i]);
            }

            $query              = $this->getQuery();
            $disabled_types     = $query->whereNotIn('slug', $permissions)->get();
            $types              = $this->getQuery()->get();

            return response()->json([
                'data' => ['disabled_types' => $disabled_types, 'types' => $types]
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
        return Type::query();
    }
}
