<?php

use ObjectivePHP\Package\FastRoute\Config\FastRoute;
use ObjectivePHP\Router\Config\UrlAlias;
use Project\Action\Album\Listing as AlbumListing;
use Project\Action\Home;

return [
        // route aliasing
        new FastRoute('home', '/', AlbumListing::class),
        new FastRoute('Album/list', '/albums', AlbumListing::class),
];