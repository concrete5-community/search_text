<?php

namespace Concrete\Package\SearchText\Controller\SinglePage\Dashboard\System;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\Redirect;

final class SearchText extends DashboardPageController
{
    public function view()
    {
        return Redirect::to('/dashboard/system/search_text/database');
    }
}
