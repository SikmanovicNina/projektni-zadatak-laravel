<?php

namespace App\Services;

use App\Models\Publisher;

class PublisherService
{
    /**
     * Get all publishers with filtering and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getAllPublishers(array $filters, int $perPage)
    {
        return Publisher::filter($filters)->paginate($perPage);
    }

    /**
     * Store a new publisher in the database.
     *
     * @param array $data
     * @return Publisher
     */
    public function createPublisher(array $data)
    {
        return Publisher::create($data);
    }

    /**
     * Update an existing publisher's details.
     *
     * @param Publisher $publisher
     * @param array $data
     * @return Publisher
     */
    public function updatePublisher(Publisher $publisher, array $data)
    {
        $publisher->update($data);
        return $publisher;
    }

    /**
     * Delete a publisher from the database.
     *
     * @param Publisher $publisher
     * @return void
     */
    public function deletePublisher(Publisher $publisher)
    {
        $publisher->delete();
    }
}
