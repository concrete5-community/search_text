<?php

namespace A3020\SearchText\Ajax;

use A3020\SearchText\Highlight;
use A3020\SearchText\PermissionsTrait;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\View\View;
use Exception;

final class ViewRecord extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait, PermissionsTrait;

    /** @var Highlight */
    protected $highlight;

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws UserMessageException
     */
    public function view()
    {
        $this->checkPermissions();

        $this->highlight = $this->app->make(Highlight::class);

        /** @var Connection $db */
        $db = $this->app->make(Connection::class);

        /** @var ResponseFactoryInterface $response */
        $response = $this->app->make(ResponseFactoryInterface::class);

        $table = $this->request->query->get('table');
        $identifiers = $this->request->query->get('identifiers');
        $searchFor = $this->request->query->get('search_for');

        if (!$table || !$identifiers || !$searchFor) {
            // Not translated, because very unlikely error.
            throw new UserMessageException('Invalid request');
        }

        try {
            $where = [];
            foreach (json_decode($identifiers, true) as $identifier) {
                $where[] = $db->quoteIdentifier($identifier['key']) . ' = ' . $db->quote($identifier['value']);
            }

            $record = $db->fetchAssoc('SELECT * FROM ' . $db->quoteIdentifier($table) . '
                WHERE ' . implode(' AND ', $where)
            );

            if (!$record) {
                throw new UserMessageException(t('Record does not exist (anymore).'));
            }

            $view = new View('view_record');
            $view->setPackageHandle('search_text');
            $view->addScopeItems([
                'record' => $this->alterRecord($record),
            ]);

            return $response->view($view);
        } catch (Exception $e) {
            // Logging the error of an AJAX request makes it
            // easier to help clients resolve issues.
            $this->app->make('log')
                ->addError($e->getMessage());

            throw new UserMessageException($e->getMessage());
        }
    }

    /**
     * Changes a table record to make it look nicer in the dialog.
     *
     * @param array $record
     *
     * @return array
     */
    private function alterRecord($record)
    {
        foreach ($record as $column => &$value) {
            $value = $this->getValue($value);
        }

        return $record;
    }

    /**
     * Style / render a table value.
     *
     * This method also escapes HTML.
     *
     * @param string $value
     * @return string
     */
    private function getValue($value)
    {
        $searchFor = $this->request->query->get('search_for');

        $match = $this->highlight->change(h($value), $searchFor);
        if ($match) {
            return $match;
        }

        if ($value === null) {
            return '<i>NULL</i>';
        }

        return h($value);
    }
}

