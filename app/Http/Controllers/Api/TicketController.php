<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Status;
use App\Http\Tools\ParamTools;
use Illuminate\Support\Facades\Validator;
use DB;

class TicketController extends Controller
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
                    ->orWhere('summary', 'like', "%$txt_search%")
                    ->orWhere('description', 'like', "%$txt_search%");
            });
            $query->with(['type', 'status','issuer', 'resolver', 'severity', 'priority']);
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
        if(!Auth::user()->hasAnyPermission(['create-bugs', 'create-feature-requests', 'create-test-cases']))
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
                'summary' => 'required|string',
                'description' => 'required|string',
                'type_id' => 'required|integer',
            ]);

            if($validator->fails()){
                return response()->json([
                    'data' => [
                        'message' => 'Validation error.',
                    ],
                    'errors' => $validator->errors()
                ], 401);
            }

            $body = $request->all();
            $body["assigner_id"] = $request->user()->id;
            $body["status_id"] = Status::where('slug','to-do')->first()->id;
            $data = $this->getQuery()->create($body);
            DB::commit();
            return response()->json([
                'data' => $data,
                'body'=>$body
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
                'data' => Ticket::with(['severity', 'priority'])->find($id)
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
        if(!Auth::user()->hasAnyPermission(['edit-bugs']))
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
                'summary' => 'required|string',
                'description' => 'required|string',
                'type_id' => 'required|integer',
                'severity_id' => 'required|integer',
                'priority_id' => 'required|integer'
            ]);

            if($validator->fails()){
                return response()->json([
                    'data' => [
                        'message' => 'Validation error.',
                    ],
                    'errors' => $validator->errors()
                ], 401);
            }

            $body = $request->all();
            $ticket = Ticket::find($id);
            $ticket->update($body);
            DB::commit();
            return response()->json([
                "data" => ["message"=>
                "success"]
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
        if(!Auth::user()->hasAnyPermission(['delete-bugs']))
            return response()->json([
                'data' => [
                    'message' => 'User does not have permission to perform this action.'
                ],
            ], 401);

        $ticket = Ticket::find($id);
        $ticket->delete();

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

    /**
     * Update the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resolve($id)
    {
        if(!Auth::user()->hasAnyPermission(['resolve-bugs', 'resolve-feature-requests','resolve-test-cases']))
            return response()->json([
                'data' => [
                    'message' => 'User does not have permission to perform this action.'
                ],
            ], 401);

        $ticket = Ticket::find($id);
        $resolve_status = Status::where('slug', 'resolved')->first();

        $ticket->status()->associate($resolve_status);
        $ticket->resolver()->associate(Auth::user());
        $ticket->save();

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


    private function getQuery()
    {
        return Ticket::query();
    }
}
