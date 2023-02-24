<?php

namespace App\traits;


use Doctrine\ORM\Mapping as ORM ;
use Doctrine\DBAL\Types\Types ;
trait TimeStampTrait{

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist()]
    public function onPrePersiste()
    {
        $this->createdAt=new \DateTime();
        $this->updatedAt=new \DateTime();
    }

#[ORM\PreUpdate()]
    public function onPreUpdate()
    {
        $this->updatedAt=new \DateTime();
    }
}