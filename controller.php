<?php

namespace Concrete\Package\SearchText;

use A3020\SearchText\Installer;
use Concrete\Core\Package\Package;
use Concrete\Core\Routing\Router;

final class Controller extends Package
{
    protected $pkgHandle = 'search_text';
    protected $appVersionRequired = '8.3.1';
    protected $pkgVersion = '1.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/SearchText' => '\A3020\SearchText',
    ];

    public function getPackageName()
    {
        return t('Search Text');
    }

    public function getPackageDescription()
    {
        return t('Search text in your database.');
    }

    public function on_start()
    {
        /** @var \Concrete\Core\Routing\Router $router */
        $router = $this->app->make(Router::class);

        $router->register('/ccm/search_text/search','\A3020\SearchText\Ajax\Search::view');
        $router->register('/ccm/search_text/view_record','\A3020\SearchText\Ajax\ViewRecord::view');
    }

    public function install()
    {
        $pkg = parent::install();

        /** @var Installer $installer */
        $installer = $this->app->make(Installer::class);
        $installer->install($pkg);
    }
}
