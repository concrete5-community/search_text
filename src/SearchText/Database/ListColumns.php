<?php

namespace A3020\SearchText\Database;

use Concrete\Core\Database\Connection\Connection;

final class ListColumns
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
     * Get an array with table column names.
     *
     * @param string $table
     *
     * @return array
     */
    public function get($table)
    {
        $data = $this->connection
            ->fetchAll('DESCRIBE ' . $table);

        return array_column($data, 'Field');
    }
}
