<?php

namespace Backend\AdminBundle\Entity;
use Symfony\Component\Intl\Locale;

/**
 * CommonAreaReservationStatus
 */
class CommonAreaReservationStatus
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nameES = '';

    /**
     * @var string
     */
    private $nameEN = '';

    /**
     * @var string|null
     */
    private $comment;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var bool
     */
    private $enabled = '1';

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nameES.
     *
     * @param string $nameES
     *
     * @return CommonAreaReservationStatus
     */
    public function setNameES($nameES)
    {
        $this->nameES = $nameES;

        return $this;
    }

    /**
     * Get nameES.
     *
     * @return string
     */
    public function getNameES()
    {
        return $this->nameES;
    }

    /**
     * Set nameEN.
     *
     * @param string $nameEN
     *
     * @return CommonAreaReservationStatus
     */
    public function setNameEN($nameEN)
    {
        $this->nameEN = $nameEN;

        return $this;
    }

    /**
     * Get nameEN.
     *
     * @return string
     */
    public function getNameEN()
    {
        return $this->nameEN;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return CommonAreaReservationStatus
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return CommonAreaReservationStatus
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return CommonAreaReservationStatus
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return CommonAreaReservationStatus
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return CommonAreaReservationStatus
     */
    public function setCreatedBy(\Backend\AdminBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $updatedBy
     *
     * @return CommonAreaReservationStatus
     */
    public function setUpdatedBy(\Backend\AdminBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    public function __toString(){

        $locale = Locale::getDefault();

        return $locale == "en" ? $this->getNameEN() : $this->getNameES();
    }

}
