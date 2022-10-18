<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Status;
use App\Http\Tools\ParamTools;
use Illuminate\Support\Facades\Validator;
use DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
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
            $params = $request->all();
            $query       = $this->getQuery();
            $txt_search  = ParamTools::get_value($params, 's');
            $sort        = ParamTools::get_value($params, 'sort', 'id');
            $order       = ParamTools::get_value($params, 'order', 'desc');
            $limit       = ParamTools::get_value($params, 'size', 10);


            $query->when($txt_search, function ($q, $txt_search) {
                $q->where('name', 'like', "%$txt_search%")
                    ->orWhere('email', 'like', "%$txt_search%");
            });
            $query->with(['roles']);
            $query->orderBy($sort, $order);
            
            return $limit !== -1 ? $query->paginate($limit) : $query->get();
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->hasAnyPermission(['manage-users']))
            return response()->json([
                'data' => [
                    'message' => 'User does not have permission to perform this action.'
                ],
            ], 401);

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), 
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed',
                'password_confirmation' => 'required',
                'role_id' => 'required|integer'
            ]);

            if($validator->fails()){
                return response()->json([
                    'data' => [
                        'message' => 'Validation error.',
                    ],
                    'errors' => $validator->errors()
                ], 401);
            }

            
            $body = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ];
            $role = Role::find($request->role_id);
            $user = $this->getQuery()->create($body);
            $user->assignRole($role);

            DB::commit();
            return response()->json([
                'data' => ['message'=>'Success']
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'data' => [
                    'message' => $th->getMessage()
                ],
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try {
            return response()->json([
                'data' => User::with(['roles'])->find($id)
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!Auth::user()->hasAnyPermission(['manage-users']))
            return response()->json([
                'data' => [
                    'message' => 'User does not have permission to perform this action.'
                ],
            ], 401);

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), 
            [
                'name' => 'required|string',
                'password' => 'confirmed',
                'role_id' => 'required|integer'
            ]);

            if($validator->fails()){
                return response()->json([
                    'data' => [
                        'message' => 'Validation error.',
                    ],
                    'errors' => $validator->errors()
                ], 401);
            }

            $body = empty($request->password) 
                ? ['name' => $request->name]
                : ['name' => $request->name, 'password' => bcrypt($request->password)];
            $role = Role::find($request->role_id);
            $user = User::findOrFail($id);
            $user->update($body);
            $user->syncRoles($role);

            DB::commit();
            return response()->json([
                'data' => ['message'=>'Success']
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'data' => [
                    'message' => $th->getMessage()
                ],
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->hasAnyPermission(['manage-users']))
            return response()->json([
                'data' => [
                    'message' => 'User does not have permission to perform this action.'
                ],
            ], 401);

        $user = User::find($id);

        $super_admin = User::role('Super-Admin')->count();
        if( $super_admin <= 1 && $user->hasRole('Super-Admin'))
            return response()->json([
                'data' => [
                    'message' => 'It is required to consist at least 1 Super Admin.'
                ],
            ], 500);


        $user->delete();

        try {
            return response()->json([
                'data' => ['message'=>'success']
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
    
    public function roles(){
        $data = Role::all();
        return response()->json([
            'data' => $data
        ], 200);
    }


    private function getQuery()
    {
        return User::query();
    }
}
