<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class DataSearchService
{
    /**
     * Applies a search filter to an Eloquent query builder.
     *
     * This method takes a query builder, a search term, and an array of columns
     * and adds the necessary "where" clauses to perform the search.
     *
     * @param Builder $query The Eloquent query builder instance (e.g., User::query()).
     * @param string|null $searchTerm The string to search for.
     * @param array $searchableColumns The database columns to search within (e.g., ['name', 'email']).
     * @return Builder The modified query builder with the search logic applied.
     */
    public function applySearch(Builder $query, ?string $searchTerm, array $searchableColumns): Builder
    {
        // If the search term is empty or null, do nothing and return the original query.
        if (is_null($searchTerm) || $searchTerm === '') {
            return $query;
        }

        // Apply a nested "where" clause. This is important because it groups
        // all the "orWhere" conditions in parentheses, preventing conflicts with
        // other "where" clauses you might add to the query later.
        $query->where(function (Builder $q) use ($searchTerm, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                // The "orWhere" ensures that a match in ANY of the specified columns will be included.
                $q->orWhere($column, 'like', '%' . $searchTerm . '%');
            }
        });

        // Return the modified query builder.
        return $query;
    }
}
