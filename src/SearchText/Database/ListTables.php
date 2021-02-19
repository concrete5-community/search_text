<?php

namespace A3020\SearchText\Database;

use Concrete\Core\Database\Connection\Connection;

final class ListTables
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get an array with table names.
     *
     * @return array
     */
    public function get()
    {
        $data = $this->connection
            ->fetchAll('
            SELECT
                TABLE_NAME AS `name`
            FROM information_schema.TABLES as db
            WHERE table_schema = ?
            ORDER BY table_name ASC
        ', [
            $this->connection->getDatabase(),
        ]);

        return array_column($data, 'name');
    }
}
