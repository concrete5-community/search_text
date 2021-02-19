<?php

namespace A3020\SearchText\Database;

use JsonSerializable;

class Result implements JsonSerializable
{
    private $table;
    private $identifiers;
    private $searchResult;

    public function __construct($table, $identifiers, $searchResult)
    {
        $this->table = $table;
        $this->identifiers = $identifiers;
        $this->searchResult = $searchResult;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'table' => $this->table,
            'identifiers' => $this->identifiers,
            'search_result' => $this->searchResult,
        ];
    }
}
