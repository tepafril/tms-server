<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Http\Tools\ParamTools;
use Auth;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Type;

class DashboardController extends Controller
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
            $all_tickets = Ticket::count();

            $bugs = Type::where('slug', 'bugs')->withCount('tickets')->first();
            $feature_requests = Type::where('slug', 'feature-requests')->withCount('tickets')->first();
            $test_cases = Type::where('slug', 'test-cases')->withCount('tickets')->first();


            return response()->json([
                'data' => [
                    'all_tickets' => $all_tickets,
                    'bugs' => $bugs->tickets_count,
                    'feature_requests' => $feature_requests->tickets_count,
                    'test_cases' => $test_cases->tickets_count,
                ]
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
}
