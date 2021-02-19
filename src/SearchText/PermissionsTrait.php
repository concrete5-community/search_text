<?php

namespace A3020\SearchText;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

trait PermissionsTrait
{
    // Make sure users can access the view routes only if they
    // have permissions to the dashboard single page.
    public function checkPermissions()
    {
        $page = Page::getByPath('/dashboard/system/search_text/database');
        $cp = $this->app->make(Checker::class, [$page]);
        if (!$page || $page->isError() || !$cp->canViewPage()) {
            throw new UserMessageException(t('Access Denied'));
        }
    }
}
