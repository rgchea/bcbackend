<?php

namespace Backend\AdminBundle\Entity;

/**
 * TicketComment
 */
class TicketComment
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
    private $createdAt = '0000-00-00 00:00:00';

    /**
     * @var bool
     */
    private $enabled = '1';

    /**
     * @var \Backend\AdminBundle\Entity\Ticket
     */
    private $ticket;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

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
     * @return TicketComment
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
     * @return TicketComment
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
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return TicketComment
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
     * Set ticket.
     *
     * @param \Backend\AdminBundle\Entity\Ticket|null $ticket
     *
     * @return TicketComment
     */
    public function setTicket(\Backend\AdminBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket.
     *
     * @return \Backend\AdminBundle\Entity\Ticket|null
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return TicketComment
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
     * Set likedBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $likedBy
     *
     * @return TicketComment
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
    /**
     * @var \DateTime
     */
    private $updatedAt = '0000-00-00 00:00:00';

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;


    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return TicketComment
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
     * Set updatedBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $updatedBy
     *
     * @return TicketComment
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
}
