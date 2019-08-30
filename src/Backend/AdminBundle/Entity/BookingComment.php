<?php

namespace Backend\AdminBundle\Entity;

/**
 * BookingComment
 */
class BookingComment
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $commentDescription = '';

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
    private $enabled = true;

    /**
     * @var \Backend\AdminBundle\Entity\CommonAreaReservation
     */
    private $commonAreaReservation;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $likedBy;


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
     * Set commentDescription.
     *
     * @param string $commentDescription
     *
     * @return BookingComment
     */
    public function setCommentDescription($commentDescription)
    {
        $this->commentDescription = $commentDescription;

        return $this;
    }

    /**
     * Get commentDescription.
     *
     * @return string
     */
    public function getCommentDescription()
    {
        return $this->commentDescription;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return BookingComment
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
     * @return BookingComment
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
     * @return BookingComment
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
     * Set commonAreaReservation.
     *
     * @param \Backend\AdminBundle\Entity\CommonAreaReservation|null $commonAreaReservation
     *
     * @return BookingComment
     */
    public function setCommonAreaReservation(\Backend\AdminBundle\Entity\CommonAreaReservation $commonAreaReservation = null)
    {
        $this->commonAreaReservation = $commonAreaReservation;

        return $this;
    }

    /**
     * Get commonAreaReservation.
     *
     * @return \Backend\AdminBundle\Entity\CommonAreaReservation|null
     */
    public function getCommonAreaReservation()
    {
        return $this->commonAreaReservation;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return BookingComment
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
     * @return BookingComment
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

    /**
     * Set likedBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $likedBy
     *
     * @return BookingComment
     */
    public function setLikedBy(\Backend\AdminBundle\Entity\User $likedBy = null)
    {
        $this->likedBy = $likedBy;

        return $this;
    }

    /**
     * Get likedBy.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getLikedBy()
    {
        return $this->likedBy;
    }
}
