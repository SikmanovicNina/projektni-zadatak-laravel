<?php

namespace App\Http\Controllers;

use App\Http\Resources\PolicyResource;
use App\Models\Policy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    /**
     *  Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $policies = Policy::all();

        return response()->json([
            'status' => 'success',
            'data' => PolicyResource::collection($policies)
        ]);
    }


    /**
     *  Update the specified resource in storage.
     *
     * @param Request $request
     * @param Policy $policy
     * @return JsonResponse
     */
    public function update(Request $request, Policy $policy)
    {
        $validatedData = $request->validate([
            'period' => ['required', 'integer', 'min:1'],
        ]);

        $policy->update($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new PolicyResource($policy)
        ]);
    }
}
