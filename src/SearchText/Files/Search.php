<?php

namespace A3020\SearchText\Files;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Request;
use Exception;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

final class Search implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var Result[]
     */
    private $results = [];

    public function __construct(Request $request, Finder $finder)
    {
        $this->request = $request;
        $this->finder = $finder;
    }

    /**
     * Search files for a specific string.
     *
     * @return array
     *
     * @throws Exception
     */
    public function results()
    {
        $searchFor = $this->request->request->get('searchFor');
        $searchIn = $this->request->request->has('searchIn');
        $limitResults = (int) $this->request->request->get('limitResults', 50);

        foreach ($this->getIterator() as $fileInfo) {
            /** @var SplFileInfo $fileInfo */

            $relativePath = str_replace(DIR_BASE, '', $fileInfo->getPathname());

            // Search in file name.
            if (stripos($relativePath, $searchFor) !== false) {
                $this->results[] = $this
                    ->makeResult()
                    ->setRelativePath($relativePath);
            }

            // Search in file content.
            if ($searchIn && $fileInfo->isFile() && $this->isInFile(
                $fileInfo->getPathname(),
                $searchFor
            )) {
                // The file contains the phrase. Let's find out where.
                $matches = $this->findInFile(
                    $fileInfo->getPathname(),
                    $searchFor
                );

                $this->results[] = $this
                    ->makeResult()
                    ->setRelativePath($relativePath)
                    ->setContentMatches($matches)
                    ->setRelativePath($relativePath);
            }

            if (count($this->results) >= $limitResults) {
                break;
            }
        }

        return $this->results;
    }

    /**
     * Check if a text occurs in a file.
     *
     * @param string $absolutePath
     * @param string $searchFor
     *
     * @return bool
     */
    private function isInFile($absolutePath, $searchFor)
    {
        $contents = file_get_contents($absolutePath);

        return stripos($contents, $searchFor) !== false;
    }

    /**
     * Return lines of a file that match with a text pattern.
     *
     * @param string $absolutePath
     * @param string $searchFor
     *
     * @return array
     */
    private function findInFile($absolutePath, $searchFor)
    {
        $contents = file_get_contents($absolutePath);

        $pattern = '/.*'.$searchFor.'.*/';

        if (preg_match_all($pattern, $contents, $matches)) {
            return $matches[0];
        }

        return [];
    }

    /**
     * @return \Iterator|\Symfony\Component\Finder\SplFileInfo[]
     *
     * @throws Exception
     */
    private function getIterator()
    {
        return $this->finder
            ->in(DIR_BASE)
            ->exclude($this->getExcludedDirectories())
            ->ignoreUnreadableDirs(true)
            ->name($this->getIncludedFileNames())
            ->getIterator();
    }

    /**
     * @return array
     */
    private function getExcludedDirectories()
    {
        $excluded = $this->request->request->get('excludedDirectories');

        return array_filter(
            array_map('trim',
                explode("\n", $excluded)
            )
        );
    }

    /**
     * @return Result
     */
    private function makeResult()
    {
        /** @var Result $result */
        $result = $this->app->make(Result::class);
        $result->setSearchFor($this->request->request->get('searchFor'));

        return $result;
    }

    private function getIncludedFileNames()
    {
        return $this->request->request->get('fileType', '*');
    }
}
