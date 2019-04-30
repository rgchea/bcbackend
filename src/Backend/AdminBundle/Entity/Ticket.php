<?php

namespace Backend\AdminBundle\Entity;

/**
 * Ticket
 */
class Ticket
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $description;

    /**
     * @var bool
     */
    private $isPublic = '1';

    /**
     * @var int|null
     */
    private $userRating;

    /**
     * @var int|null
     */
    private $tenantRating;

    /**
     * @var \DateTime
     */
    private $createdAt = '0000-00-00 00:00:00';

    /**
     * @var \DateTime
     */
    private $updatedAt = '0000-00-00 00:00:00';

    /**
     * @var bool
     */
    private $enabled = '1';

    /**
     * @var \Backend\AdminBundle\Entity\TenantContract
     */
    private $tenantContract;

    /**
     * @var \Backend\AdminBundle\Entity\Property
     */
    private $property;

    /**
     * @var \Backend\AdminBundle\Entity\CommonAreaReservation
     */
    private $commonAreaReservation;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $assignedTo;

    /**
     * @var \Backend\AdminBundle\Entity\TicketStatus
     */
    private $ticketStatus;

    /**
     * @var \Backend\AdminBundle\Entity\TicketCategory
     */
    private $ticketCategory;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\TicketType
     */
    private $ticketType;

    /**
     * @var \Backend\AdminBundle\Entity\Business
     */
    private $business;

    /**
     * @var \Backend\AdminBundle\Entity\Complex
     */
    private $complex;

    /**
     * @var \Backend\AdminBundle\Entity\ComplexSector
     */
    private $complexSector;


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
     * Set title.
     *
     * @param string $title
     *
     * @return Ticket
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Ticket
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
     * Set isPublic.
     *
     * @param bool $isPublic
     *
     * @return Ticket
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic.
     *
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set userRating.
     *
     * @param int|null $userRating
     *
     * @return Ticket
     */
    public function setUserRating($userRating = null)
    {
        $this->userRating = $userRating;

        return $this;
    }

    /**
     * Get userRating.
     *
     * @return int|null
     */
    public function getUserRating()
    {
        return $this->userRating;
    }

    /**
     * Set tenantRating.
     *
     * @param int|null $tenantRating
     *
     * @return Ticket
     */
    public function setTenantRating($tenantRating = null)
    {
        $this->tenantRating = $tenantRating;

        return $this;
    }

    /**
     * Get tenantRating.
     *
     * @return int|null
     */
    public function getTenantRating()
    {
        return $this->tenantRating;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Ticket
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
     * @return Ticket
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
     * @return Ticket
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
     * Set tenantContract.
     *
     * @param \Backend\AdminBundle\Entity\TenantContract|null $tenantContract
     *
     * @return Ticket
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
     * Set property.
     *
     * @param \Backend\AdminBundle\Entity\Property|null $property
     *
     * @return Ticket
     */
    public function setProperty(\Backend\AdminBundle\Entity\Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property.
     *
     * @return \Backend\AdminBundle\Entity\Property|null
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set commonAreaReservation.
     *
     * @param \Backend\AdminBundle\Entity\CommonAreaReservation|null $commonAreaReservation
     *
     * @return Ticket
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
     * Set assignedTo.
     *
     * @param \Backend\AdminBundle\Entity\User|null $assignedTo
     *
     * @return Ticket
     */
    public function setAssignedTo(\Backend\AdminBundle\Entity\User $assignedTo = null)
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    /**
     * Get assignedTo.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     * Set ticketStatus.
     *
     * @param \Backend\AdminBundle\Entity\TicketStatus|null $ticketStatus
     *
     * @return Ticket
     */
    public function setTicketStatus(\Backend\AdminBundle\Entity\TicketStatus $ticketStatus = null)
    {
        $this->ticketStatus = $ticketStatus;

        return $this;
    }

    /**
     * Get ticketStatus.
     *
     * @return \Backend\AdminBundle\Entity\TicketStatus|null
     */
    public function getTicketStatus()
    {
        return $this->ticketStatus;
    }

    /**
     * Set ticketCategory.
     *
     * @param \Backend\AdminBundle\Entity\TicketCategory|null $ticketCategory
     *
     * @return Ticket
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

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return Ticket
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
     * @return Ticket
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
     * Set ticketType.
     *
     * @param \Backend\AdminBundle\Entity\TicketType|null $ticketType
     *
     * @return Ticket
     */
    public function setTicketType(\Backend\AdminBundle\Entity\TicketType $ticketType = null)
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    /**
     * Get ticketType.
     *
     * @return \Backend\AdminBundle\Entity\TicketType|null
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * Set business.
     *
     * @param \Backend\AdminBundle\Entity\Business|null $business
     *
     * @return Ticket
     */
    public function setBusiness(\Backend\AdminBundle\Entity\Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get business.
     *
     * @return \Backend\AdminBundle\Entity\Business|null
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * Set complex.
     *
     * @param \Backend\AdminBundle\Entity\Complex|null $complex
     *
     * @return Ticket
     */
    public function setComplex(\Backend\AdminBundle\Entity\Complex $complex = null)
    {
        $this->complex = $complex;

        return $this;
    }

    /**
     * Get complex.
     *
     * @return \Backend\AdminBundle\Entity\Complex|null
     */
    public function getComplex()
    {
        return $this->complex;
    }

    /**
     * Set complexSector.
     *
     * @param \Backend\AdminBundle\Entity\ComplexSector|null $complexSector
     *
     * @return Ticket
     */
    public function setComplexSector(\Backend\AdminBundle\Entity\ComplexSector $complexSector = null)
    {
        $this->complexSector = $complexSector;

        return $this;
    }

    /**
     * Get complexSector.
     *
     * @return \Backend\AdminBundle\Entity\ComplexSector|null
     */
    public function getComplexSector()
    {
        return $this->complexSector;
    }
    /**
     * @var string
     */
    private $possibleSolution;

    /**
     * @var int|null
     */
    private $ratingToUser;

    /**
     * @var int|null
     */
    private $ratingToTenant;


    /**
     * Set possibleSolution.
     *
     * @param string $possibleSolution
     *
     * @return Ticket
     */
    public function setPossibleSolution($possibleSolution)
    {
        $this->possibleSolution = $possibleSolution;

        return $this;
    }

    /**
     * Get possibleSolution.
     *
     * @return string
     */
    public function getPossibleSolution()
    {
        return $this->possibleSolution;
    }

    /**
     * Set ratingToUser.
     *
     * @param int|null $ratingToUser
     *
     * @return Ticket
     */
    public function setRatingToUser($ratingToUser = null)
    {
        $this->ratingToUser = $ratingToUser;

        return $this;
    }

    /**
     * Get ratingToUser.
     *
     * @return int|null
     */
    public function getRatingToUser()
    {
        return $this->ratingToUser;
    }

    /**
     * Set ratingToTenant.
     *
     * @param int|null $ratingToTenant
     *
     * @return Ticket
     */
    public function setRatingToTenant($ratingToTenant = null)
    {
        $this->ratingToTenant = $ratingToTenant;

        return $this;
    }

    /**
     * Get ratingToTenant.
     *
     * @return int|null
     */
    public function getRatingToTenant()
    {
        return $this->ratingToTenant;
    }
}
