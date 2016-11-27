<?php
/**
 * List.php
 *
 * @date        26/11/2016
 * @file        Create.php
 */

namespace Project\Action\Album;

use ObjectivePHP\Application\Action\RenderableAction;
use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Application\View\Helper\Vars;

/**
 * List
 */
class Listing extends RenderableAction
{
    function run(ApplicationInterface $app)
    {
        /**
         * @var \Project\Service\Album $serviceAlbum
         */
        $serviceAlbum = $this->getService('service.album');

        $albums = $serviceAlbum->getAlbums();
        
        Vars::set('albums', $albums);
    }

}