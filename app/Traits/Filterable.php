<?php

namespace App\Traits;

trait Filterable
{
    public function applyFilters($query, array $filters = [], array $fields = [])
    {
        $query->when(
            $filters['search'] ?? false,
            fn ($query, $search) =>
            $query->where(
                fn ($query) =>
              collect($fields)->each(
                  fn ($field) =>
                $query->orWhere($field, 'like', '%' . $search . '%')
              )
            )
        );

        $query->when(
            $filters['role_id'] ?? false,
            fn ($query, $roleId) =>
            $query->where('role_id', $roleId)
        );

        return $query;
    }
}
