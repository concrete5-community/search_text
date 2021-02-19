<?php

namespace Concrete\Package\SearchText\Controller\SinglePage\Dashboard\System\SearchText;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Page\Controller\DashboardPageController;

final class Files extends DashboardPageController
{
    public function on_before_render()
    {
        $assetList = AssetList::getInstance();
        $assetList->register('css', 'search_text', 'css/search-text.css', [], 'search_text');
        $assetList->register('javascript', 'search_text/files', 'js/search-files.js', [], 'search_text');

        $ag = ResponseAssetGroup::get();
        $ag->requireAsset('css', 'search_text');
        $ag->requireAsset('javascript', 'search_text/files');

        $ag->addHeaderAsset('<script>
            var SEARCH_TEXT_SENT_PENDING = \'<i class="fa fa-hourglass-2"></i> ' . t('Request has been sent. Waiting...') . '\';
            var SEARCH_TEXT_ERROR_OCCURRED = \'<i class="fa fa-close"></i> ' . t('An error occurred.') . '\';
            var SEARCH_TEXT_SEARCH_FINISHED = \'<i class="fa fa-check"></i> ' . t('Search has finished. Results found:') . '\';
            var SEARCH_TEXT_NO_RESULTS = \'' . t('No results have been found.') . '\';
        </script>');

        parent::on_before_render();
    }

    public function view()
    {
        $this->set('pageTitle', t('Search Text in Files'));
        $this->set('results', []);
        $this->set('fileTypeOptions', $this->getFileTypeOptions());

        $this->set('excludedDirectories', implode("\n", [
            DIRNAME_CORE,
            DIRNAME_APPLICATION . '/files',
        ]));
    }

    private function getFileTypeOptions()
    {
        return [
            '*' => t('All'),
            '*.php' => t('PHP'),
            '*.txt' => t('Text'),
            '*.json' => t('JSON'),
            '*.xml' => t('XML'),
        ];
    }
}
