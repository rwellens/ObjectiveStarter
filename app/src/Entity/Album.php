<?php

namespace Project\Entity;

use Doctrine\Common\Collections\ArrayCollection;


/**
 * @Entity
 * @Table(name="album")
 */
class Album extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $artiste;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return self
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getArtiste(): string
    {
        return $this->artiste;
    }

    /**
     * @param mixed $artiste
     *
     * @return self
     */
    public function setArtiste($artiste): self
    {
        $this->artiste = $artiste;

        return $this;
    }



}