<?php
/**
 * Album.php
 *
 * @date        27/11/2016
 * @file        Album.php
 */

namespace Project\Service;

use Project\Entity\Album as AlbumEntity;
use Project\Gateway\Album as AlbumGateway;

/**
 * Album
 */
class Album
{

    /**
     * @var AlbumGateway
     */
    protected $gateway;


    /**
     * Album constructor.
     *
     * @param AlbumGateway $gateway
     */
    public function __construct(AlbumGateway $gateway)
    {
        $this->setGateway($gateway);
    }


    /**
     * @return AlbumEntity[]
     */
    public function getAlbums()
    {
        return $this->getGateway()->fetchAll();
    }

    /**
     * @return AlbumGateway
     */
    public function getGateway(): AlbumGateway
    {
        return $this->gateway;
    }

    /**
     * @param AlbumGateway $gateway
     *
     * @return self
     */
    public function setGateway(AlbumGateway $gateway): self
    {
        $this->gateway = $gateway;

        return $this;
    }



}