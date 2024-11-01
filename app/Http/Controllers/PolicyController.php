<?php

namespace App\Http\Controllers;

use App\Http\Requests\PolicyRequest;
use App\Http\Resources\PolicyResource;
use App\Models\Policy;
use App\Services\PolicyService;
use Illuminate\Http\JsonResponse;

class PolicyController extends Controller
{
    public function __construct(protected PolicyService $policyService)
    {
    }

    /**
     *  Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $policies = $this->policyService->getAllPolicies();

        return response()->json([
            'status' => 'success',
            'data' => PolicyResource::collection($policies)
        ]);
    }


    /**
     *  Update the specified resource in storage.
     *
     * @param PolicyRequest $request
     * @param Policy $policy
     * @return JsonResponse
     */
    public function update(PolicyRequest $request, Policy $policy)
    {
        $validatedData = $request->validated();

        $policy = $this->policyService->updatePolicy($policy, $validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new PolicyResource($policy)
        ]);
    }
}
