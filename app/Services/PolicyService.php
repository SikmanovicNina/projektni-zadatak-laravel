<?php

namespace App\Services;

use App\Models\Policy;

class PolicyService
{
    /**
     * Get all policies.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPolicies()
    {
        return Policy::all();
    }

    /**
     * Update an existing genre.
     *
     * @param Policy $policy
     * @param array $data
     * @return Policy
     */
    public function updatePolicy(Policy $policy, array $data)
    {
        $policy->update($data);
        return $policy;
    }
}
