<?php

namespace A3020\SearchText\Ajax;

use A3020\SearchText\PermissionsTrait;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;

final class SearchDatabase extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait, PermissionsTrait;

    public function view()
    {
        $this->checkPermissions();

        $token = $this->app->make('token');

        if (!$token->validate('a3020.search_text')) {
            throw new UserMessageException($token->getErrorMessage());
        }

        set_time_limit(30);

        /** @var \A3020\SearchText\Database\Search $search */
        $search = $this->app->make(\A3020\SearchText\Database\Search::class);

        $response = $this->app->make(ResponseFactoryInterface::class);

        return $response->json([
            'results' => $search->results(),
        ]);
    }
}

