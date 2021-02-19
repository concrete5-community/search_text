<?php

namespace A3020\SearchText\Database;

use Concrete\Core\Database\Connection\Connection;

final class PrimaryKey
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
     * Get a list of primary keys for a table.
     *
     * @param string $table
     *
     * @return array|null
     *
     * @example ['cID', 'ptID'] for Pages.
     */
    public function get($table)
    {
        $data = $this->connection
            ->fetchAll('SHOW KEYS FROM ' . $table . ' WHERE Key_name = "PRIMARY"');

        if ($data) {
            return array_column($data, 'Column_name');
        }

        return null;
    }
}
