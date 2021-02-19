<?php

namespace A3020\SearchText\Files;

use A3020\SearchText\Highlight;
use JsonSerializable;

final class Result implements JsonSerializable
{
    const TYPE_FILE_NAME_MATCH = 1;
    const TYPE_FILE_CONTENT_MATCH = 2;

    private $searchFor;
    private $relativePath;
    private $contentMatches;

    /**
     * @var Highlight
     */
    private $highlight;

    public function __construct(Highlight $highlight)
    {
        $this->highlight = $highlight;
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
            'relative_path_highlighted' => $this->getPathHighlighted(),
            'relative_path' => $this->getPath(),
            'content_highlighted' => $this->getContentHighlighted(),
            'type' => $this->getType(),
        ];
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->relativePath;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->contentMatches
            ? self::TYPE_FILE_CONTENT_MATCH
            : self::TYPE_FILE_NAME_MATCH;
    }

    /**
     * @return string|null
     */
    private function getPathHighlighted()
    {
        $highlighted = $this->highlight->change(
            $this->getPath(),
            $this->searchFor,
            150
        );

        return $highlighted
            ? $highlighted
            : $this->getPath();
    }

    /**
     * @return string
     */
    private function getContentHighlighted()
    {
        if (!$this->contentMatches) {
            return '';
        }

        // Concatenate the lines that matched the search.
        $content = implode(' ', $this->contentMatches);

        // To fix encoding issues with e.g. json strings in files.
        $content = htmlentities($content);

        $highlighted = $this->highlight->change($content, $this->searchFor);

        return $highlighted ? $highlighted : $content;
    }

    /**
     * @param string $relativePath
     *
     * @return static
     */
    public function setRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;

        return $this;
    }

    /**
     * @param string $searchFor
     *
     * @return static
     */
    public function setSearchFor($searchFor)
    {
        $this->searchFor = $searchFor;

        return $this;
    }

    /**
     * @param array $contentMatches
     *
     * @return static
     */
    public function setContentMatches($contentMatches)
    {
        $this->contentMatches = $contentMatches;

        return $this;
    }
}
