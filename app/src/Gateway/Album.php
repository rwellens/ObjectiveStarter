<?php
/**
 * Album.php
 *
 * @date        27/11/2016
 * @file        Album.php
 */

namespace Project\Gateway;

use Doctrine\ORM\EntityManager;
use Project\Entity\Album as AlbumEntity;

/**
 * Album
 */
class Album
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->setEntityManager($entityManager);
    }

    /**
     * @param $id
     *
     * @return AlbumEntity
     */
    public function fetch($id): AlbumEntity
    {
        return $this->getEntityManager()->getRepository(AlbumEntity::class)->find($id);
    }


    /**
     * @return AlbumEntity[]
     */
    public function fetchAll(): array
    {
        return $this->getEntityManager()->getRepository(AlbumEntity::class)->findAll();
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager): self
    {
        $this->entityManager = $entityManager;

        return $this;
    }

}