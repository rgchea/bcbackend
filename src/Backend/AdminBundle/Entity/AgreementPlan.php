<?php

namespace Backend\AdminBundle\Entity;

/**
 * AgreementPlan
 */
class AgreementPlan
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $ticketLimit = 1;

    /**
     * @var int
     */
    private $userLimit = 1;

    /**
     * @var float
     */
    private $fee = 0.00;

    /**
     * @var bool
     */
    private $montlyPayment = 0;

    /**
     * @var bool
     */
    private $yearlyPayment = 0;

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
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;


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
     * Set ticketLimit.
     *
     * @param int $ticketLimit
     *
     * @return AgreementPlan
     */
    public function setTicketLimit($ticketLimit)
    {
        $this->ticketLimit = $ticketLimit;

        return $this;
    }

    /**
     * Get ticketLimit.
     *
     * @return int
     */
    public function getTicketLimit()
    {
        return $this->ticketLimit;
    }

    /**
     * Set userLimit.
     *
     * @param int $userLimit
     *
     * @return AgreementPlan
     */
    public function setUserLimit($userLimit)
    {
        $this->userLimit = $userLimit;

        return $this;
    }

    /**
     * Get userLimit.
     *
     * @return int
     */
    public function getUserLimit()
    {
        return $this->userLimit;
    }

    /**
     * Set fee.
     *
     * @param float $fee
     *
     * @return AgreementPlan
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee.
     *
     * @return float
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set montlyPayment.
     *
     * @param bool $montlyPayment
     *
     * @return AgreementPlan
     */
    public function setMontlyPayment($montlyPayment)
    {
        $this->montlyPayment = $montlyPayment;

        return $this;
    }

    /**
     * Get montlyPayment.
     *
     * @return bool
     */
    public function getMontlyPayment()
    {
        return $this->montlyPayment;
    }

    /**
     * Set yearlyPayment.
     *
     * @param bool $yearlyPayment
     *
     * @return AgreementPlan
     */
    public function setYearlyPayment($yearlyPayment)
    {
        $this->yearlyPayment = $yearlyPayment;

        return $this;
    }

    /**
     * Get yearlyPayment.
     *
     * @return bool
     */
    public function getYearlyPayment()
    {
        return $this->yearlyPayment;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return AgreementPlan
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
     * @return AgreementPlan
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
     * @return AgreementPlan
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
     * Set updatedBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $updatedBy
     *
     * @return AgreementPlan
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
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return AgreementPlan
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
     * @var int
     */
    private $propertyLimitFrom = '1';

    /**
     * @var int
     */
    private $propertyLimitTo = '1';


    /**
     * Set propertyLimitFrom.
     *
     * @param int $propertyLimitFrom
     *
     * @return AgreementPlan
     */
    public function setPropertyLimitFrom($propertyLimitFrom)
    {
        $this->propertyLimitFrom = $propertyLimitFrom;

        return $this;
    }

    /**
     * Get propertyLimitFrom.
     *
     * @return int
     */
    public function getPropertyLimitFrom()
    {
        return $this->propertyLimitFrom;
    }

    /**
     * Set propertyLimitTo.
     *
     * @param int $propertyLimitTo
     *
     * @return AgreementPlan
     */
    public function setPropertyLimitTo($propertyLimitTo)
    {
        $this->propertyLimitTo = $propertyLimitTo;

        return $this;
    }

    /**
     * Get propertyLimitTo.
     *
     * @return int
     */
    public function getPropertyLimitTo()
    {
        return $this->propertyLimitTo;
    }
}
