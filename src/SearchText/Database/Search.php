<?php

namespace A3020\SearchText\Database;

use A3020\SearchText\Highlight;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\Request;

final class Search implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var ListTables
     */
    private $listTables;

    /**
     * @var Connection
     */
    private $db;

    /**
     * @var ListColumns
     */
    private $listColumns;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Highlight
     */
    private $highlight;

    /**
     * @var PrimaryKey
     */
    private $primaryKey;

    /** @var Result[] */
    private $results = [];

    public function __construct(
        Request $request,
        ListTables $listTables,
        ListColumns $listColumns,
        Connection $db,
        Highlight $highlight,
        PrimaryKey $primaryKey
    )
    {
        $this->request = $request;
        $this->listTables = $listTables;
        $this->listColumns = $listColumns;
        $this->db = $db;
        $this->highlight = $highlight;
        $this->primaryKey = $primaryKey;
    }

    /**
     * Search all tables for a specific string.
     *
     * @return array
     */
    public function results()
    {
        $searchFor = $this->request->request->get('searchFor');

        foreach ($this->getTables() as $table) {
            $this->searchInTable($table, $searchFor);
        }

        return $this->results;
    }

    /**
     * Search a table for string occurrences.
     *
     * @param string $table
     * @param string $searchFor
     */
    private function searchInTable($table, $searchFor)
    {
        $columns = $this->listColumns->get($table);

        $queryParts = [];
        foreach ($columns as $column) {
            $queryParts[] = "`" . $column . "` LIKE '%" . $searchFor . "%'";
        }

        $queryParts = implode(' OR ', $queryParts);

        foreach ($this->db->fetchAll('SELECT * FROM ' . $table . ' WHERE ' . $queryParts) as $row) {
            $columnData = '';
            foreach ($row as $column) {
                if (stripos($column, $searchFor) !== false) {
                    $columnData .= $this->highlight->change(h($column), $searchFor);
                }
            }

            $this->results[] = new Result(
                $table,
                $this->getIdentifier($table, $row),
                $columnData
            );
        }
    }

    /**
     * Get a list of tables the user wants to search through.
     *
     * @return array
     */
    private function getTables()
    {
        $allTables = $this->listTables->get();

        $included = (array) $this->request->request->get('tablesIncluded', []);
        $excluded = (array) $this->request->request->get('tablesExcluded', []);

        // Search in all tables, because the user
        // didn't include/exclude any specific tables.
        if (count($included) === 0 && count($excluded) === 0) {
            return $allTables;
        }

        if (count($included) === 0) {
            $included = $allTables;
        } else {
            // Make sure only valid tables are included.
            $included = array_intersect($allTables, $included);
        }

        // Remove excluded tables.
        return array_values(array_diff($included, $excluded));
    }

    /**
     * Get the identifiers for a specific table / row.
     *
     * @param string $table
     * @param array $rowData
     *
     * @example 'cID: 159, cvID: 1'
     *
     * @return array
     */
    private function getIdentifier($table, $rowData)
    {
        $keys = $this->primaryKey->get($table);

        $identifiers = [];
        foreach ($keys as $key) {
            if (isset($rowData[$key])) {
                $identifiers[] = [
                    'key' => $key,
                    'value' => $rowData[$key],
                ];
            }
        }

        return $identifiers;
    }
}
