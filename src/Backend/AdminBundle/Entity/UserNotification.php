<?php

namespace Backend\AdminBundle\Entity;

/**
 * UserNotification
 */
class UserNotification
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $read = '0';

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $createdAt = '0000-00-00 00:00:00';

    /**
     * @var bool
     */
    private $enabled = '1';

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $sentBy;

    /**
     * @var \Backend\AdminBundle\Entity\NotificationType
     */
    private $notificationType;

    /**
     * @var \Backend\AdminBundle\Entity\Ticket
     */
    private $ticket;


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
     * Set read.
     *
     * @param bool $read
     *
     * @return UserNotification
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Get read.
     *
     * @return bool
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return UserNotification
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return UserNotification
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
     * @return UserNotification
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
     * Set sentBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $sentBy
     *
     * @return UserNotification
     */
    public function setSentBy(\Backend\AdminBundle\Entity\User $sentBy = null)
    {
        $this->sentBy = $sentBy;

        return $this;
    }

    /**
     * Get sentBy.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getSentBy()
    {
        return $this->sentBy;
    }

    /**
     * Set notificationType.
     *
     * @param \Backend\AdminBundle\Entity\NotificationType|null $notificationType
     *
     * @return UserNotification
     */
    public function setNotificationType(\Backend\AdminBundle\Entity\NotificationType $notificationType = null)
    {
        $this->notificationType = $notificationType;

        return $this;
    }

    /**
     * Get notificationType.
     *
     * @return \Backend\AdminBundle\Entity\NotificationType|null
     */
    public function getNotificationType()
    {
        return $this->notificationType;
    }

    /**
     * Set ticket.
     *
     * @param \Backend\AdminBundle\Entity\Ticket|null $ticket
     *
     * @return UserNotification
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
     * @var \DateTime
     */
    private $updatedAt = '0000-00-00 00:00:00';

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;


    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return UserNotification
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
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return UserNotification
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
     * @return UserNotification
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
     * @var string
     */
    private $notice;


    /**
     * Set notice.
     *
     * @param string $notice
     *
     * @return UserNotification
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Get notice.
     *
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
    }
}
