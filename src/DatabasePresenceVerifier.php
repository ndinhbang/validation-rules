<?php

namespace Ndinhbang\ValidationRules;

use Illuminate\Support\Collection;
use Illuminate\Validation\DatabasePresenceVerifier as LaravelDatabasePresenceVerifier;

class DatabasePresenceVerifier extends LaravelDatabasePresenceVerifier
{
    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param string $collection
     * @param string $column
     * @param array $value
     * @param int|string|null $excludeId
     * @param string|null $idColumn
     * @param array $extra
     * @return Collection
     */
    public function getItems(string $collection, string $column, array $value, int|string $excludeId = null, string $idColumn = null, array $extra = []): \Illuminate\Support\Collection
    {
        $query = $this->table($collection)->whereIn($column, $value);

        if (!is_null($excludeId) && $excludeId !== 'NULL') {
            $query->where($idColumn ?: 'id', '<>', $excludeId);
        }
        // todo: add query cache
        return $this->addConditions($query, $extra)->get();
    }
}
