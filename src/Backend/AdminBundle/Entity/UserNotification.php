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
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var bool
     */
    private $enabled = true;

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
    private $updatedAt;

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
    /**
     * @var \Backend\AdminBundle\Entity\CommonAreaReservation
     */
    private $commonAreaReservation;


    /**
     * Set commonAreaReservation.
     *
     * @param \Backend\AdminBundle\Entity\CommonAreaReservation|null $commonAreaReservation
     *
     * @return UserNotification
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
     * @var bool
     */
    private $isRead = false;


    /**
     * Set isRead.
     *
     * @param bool $isRead
     *
     * @return UserNotification
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead.
     *
     * @return bool
     */
    public function getIsRead()
    {
        return $this->isRead;
    }
    /**
     * @var \Backend\AdminBundle\Entity\TenantContract
     */
    private $tenantContract;


    /**
     * Set tenantContract.
     *
     * @param \Backend\AdminBundle\Entity\TenantContract|null $tenantContract
     *
     * @return UserNotification
     */
    public function setTenantContract(\Backend\AdminBundle\Entity\TenantContract $tenantContract = null)
    {
        $this->tenantContract = $tenantContract;

        return $this;
    }

    /**
     * Get tenantContract.
     *
     * @return \Backend\AdminBundle\Entity\TenantContract|null
     */
    public function getTenantContract()
    {
        return $this->tenantContract;
    }
    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $sentTo;


    /**
     * Set sentTo.
     *
     * @param \Backend\AdminBundle\Entity\User|null $sentTo
     *
     * @return UserNotification
     */
    public function setSentTo(\Backend\AdminBundle\Entity\User $sentTo = null)
    {
        $this->sentTo = $sentTo;

        return $this;
    }

    /**
     * Get sentTo.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getSentTo()
    {
        return $this->sentTo;
    }
    /**
     * @var string|null
     */
    private $title;

    /**
     * @var int|null
     */
    private $reminder;

    /**
     * @var bool
     */
    private $isScheduled = false;

    /**
     * @var \DateTime
     */
    private $scheduledTime;

    /**
     * @var \Backend\AdminBundle\Entity\Complex
     */
    private $complexToNotify;

    /**
     * @var \Backend\AdminBundle\Entity\ComplexSector
     */
    private $sectorToNotify;

    /**
     * @var \Backend\AdminBundle\Entity\Property
     */
    private $propertyToNotify;


    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return UserNotification
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set reminder.
     *
     * @param int|null $reminder
     *
     * @return UserNotification
     */
    public function setReminder($reminder = null)
    {
        $this->reminder = $reminder;

        return $this;
    }

    /**
     * Get reminder.
     *
     * @return int|null
     */
    public function getReminder()
    {
        return $this->reminder;
    }

    /**
     * Set isScheduled.
     *
     * @param bool $isScheduled
     *
     * @return UserNotification
     */
    public function setIsScheduled($isScheduled)
    {
        $this->isScheduled = $isScheduled;

        return $this;
    }

    /**
     * Get isScheduled.
     *
     * @return bool
     */
    public function getIsScheduled()
    {
        return $this->isScheduled;
    }

    /**
     * Set scheduledTime.
     *
     * @param \DateTime $scheduledTime
     *
     * @return UserNotification
     */
    public function setScheduledTime($scheduledTime)
    {
        $this->scheduledTime = $scheduledTime;

        return $this;
    }

    /**
     * Get scheduledTime.
     *
     * @return \DateTime
     */
    public function getScheduledTime()
    {
        return $this->scheduledTime;
    }

    /**
     * Set complexToNotify.
     *
     * @param \Backend\AdminBundle\Entity\Complex|null $complexToNotify
     *
     * @return UserNotification
     */
    public function setComplexToNotify(\Backend\AdminBundle\Entity\Complex $complexToNotify = null)
    {
        $this->complexToNotify = $complexToNotify;

        return $this;
    }

    /**
     * Get complexToNotify.
     *
     * @return \Backend\AdminBundle\Entity\Complex|null
     */
    public function getComplexToNotify()
    {
        return $this->complexToNotify;
    }

    /**
     * Set sectorToNotify.
     *
     * @param \Backend\AdminBundle\Entity\ComplexSector|null $sectorToNotify
     *
     * @return UserNotification
     */
    public function setSectorToNotify(\Backend\AdminBundle\Entity\ComplexSector $sectorToNotify = null)
    {
        $this->sectorToNotify = $sectorToNotify;

        return $this;
    }

    /**
     * Get sectorToNotify.
     *
     * @return \Backend\AdminBundle\Entity\ComplexSector|null
     */
    public function getSectorToNotify()
    {
        return $this->sectorToNotify;
    }

    /**
     * Set propertyToNotify.
     *
     * @param \Backend\AdminBundle\Entity\Property|null $propertyToNotify
     *
     * @return UserNotification
     */
    public function setPropertyToNotify(\Backend\AdminBundle\Entity\Property $propertyToNotify = null)
    {
        $this->propertyToNotify = $propertyToNotify;

        return $this;
    }

    /**
     * Get propertyToNotify.
     *
     * @return \Backend\AdminBundle\Entity\Property|null
     */
    public function getPropertyToNotify()
    {
        return $this->propertyToNotify;
    }



    /**
     * @var \Backend\AdminBundle\Entity\TicketCategory
     */
    private $ticketCategory;


    /**
     * Set ticketCategory.
     *
     * @param \Backend\AdminBundle\Entity\TicketCategory|null $ticketCategory
     *
     * @return UserNotification
     */
    public function setTicketCategory(\Backend\AdminBundle\Entity\TicketCategory $ticketCategory = null)
    {
        $this->ticketCategory = $ticketCategory;

        return $this;
    }

    /**
     * Get ticketCategory.
     *
     * @return \Backend\AdminBundle\Entity\TicketCategory|null
     */
    public function getTicketCategory()
    {
        return $this->ticketCategory;
    }
}
