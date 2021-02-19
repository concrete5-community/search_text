<?php

namespace A3020\SearchText\Ajax;

use A3020\SearchText\PermissionsTrait;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;

final class SearchFiles extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait, PermissionsTrait;

    /**
     * @return mixed
     *
     * @throws UserMessageException
     */
    public function view()
    {
        $this->checkPermissions();

        $token = $this->app->make('token');

        if (!$token->validate('a3020.search_text.files')) {
            throw new UserMessageException($token->getErrorMessage());
        }

        set_time_limit(30);

        /** @var \A3020\SearchText\Files\Search $search */
        $search = $this->app->make(\A3020\SearchText\Files\Search::class);

        /** @var ResponseFactoryInterface $response */
        $response = $this->app->make(ResponseFactoryInterface::class);

        $results = $search->results();

        // Alphabetically sort the results by relative path.
        usort($results, function($a, $b) {
            return $a->getPath() < $b->getPath();
        });

        return $response->json([
            'results' => $results,
        ]);
    }
}

