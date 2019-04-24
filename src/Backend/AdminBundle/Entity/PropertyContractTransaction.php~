<?php

namespace Backend\AdminBundle\Entity;

/**
 * PropertyContractTransaction
 */
class PropertyContractTransaction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $paymentAmount = '0.00';

    /**
     * @var bool
     */
    private $status = '0';

    /**
     * @var string|null
     */
    private $paymentType;

    /**
     * @var string|null
     */
    private $transactionNumber;

    /**
     * @var \DateTime|null
     */
    private $paymentDate = '0000-00-00 00:00:00';

    /**
     * @var \DateTime
     */
    private $createdAt = '0000-00-00 00:00:00';

    /**
     * @var bool
     */
    private $enabled = '1';

    /**
     * @var \Backend\AdminBundle\Entity\PropertyContract
     */
    private $propertyContract;

    /**
     * @var \Backend\AdminBundle\Entity\CommonAreaReservation
     */
    private $commonAreaReservation;

    /**
     * @var \Backend\AdminBundle\Entity\PropertyTransactionType
     */
    private $propertyTransactionType;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $paidBy;

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
     * Set paymentAmount.
     *
     * @param float $paymentAmount
     *
     * @return PropertyContractTransaction
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * Get paymentAmount.
     *
     * @return float
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return PropertyContractTransaction
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set paymentType.
     *
     * @param string|null $paymentType
     *
     * @return PropertyContractTransaction
     */
    public function setPaymentType($paymentType = null)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * Get paymentType.
     *
     * @return string|null
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set transactionNumber.
     *
     * @param string|null $transactionNumber
     *
     * @return PropertyContractTransaction
     */
    public function setTransactionNumber($transactionNumber = null)
    {
        $this->transactionNumber = $transactionNumber;

        return $this;
    }

    /**
     * Get transactionNumber.
     *
     * @return string|null
     */
    public function getTransactionNumber()
    {
        return $this->transactionNumber;
    }

    /**
     * Set paymentDate.
     *
     * @param \DateTime|null $paymentDate
     *
     * @return PropertyContractTransaction
     */
    public function setPaymentDate($paymentDate = null)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate.
     *
     * @return \DateTime|null
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return PropertyContractTransaction
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
     * @return PropertyContractTransaction
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
     * Set propertyContract.
     *
     * @param \Backend\AdminBundle\Entity\PropertyContract|null $propertyContract
     *
     * @return PropertyContractTransaction
     */
    public function setPropertyContract(\Backend\AdminBundle\Entity\PropertyContract $propertyContract = null)
    {
        $this->propertyContract = $propertyContract;

        return $this;
    }

    /**
     * Get propertyContract.
     *
     * @return \Backend\AdminBundle\Entity\PropertyContract|null
     */
    public function getPropertyContract()
    {
        return $this->propertyContract;
    }

    /**
     * Set commonAreaReservation.
     *
     * @param \Backend\AdminBundle\Entity\CommonAreaReservation|null $commonAreaReservation
     *
     * @return PropertyContractTransaction
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
     * Set propertyTransactionType.
     *
     * @param \Backend\AdminBundle\Entity\PropertyTransactionType|null $propertyTransactionType
     *
     * @return PropertyContractTransaction
     */
    public function setPropertyTransactionType(\Backend\AdminBundle\Entity\PropertyTransactionType $propertyTransactionType = null)
    {
        $this->propertyTransactionType = $propertyTransactionType;

        return $this;
    }

    /**
     * Get propertyTransactionType.
     *
     * @return \Backend\AdminBundle\Entity\PropertyTransactionType|null
     */
    public function getPropertyTransactionType()
    {
        return $this->propertyTransactionType;
    }

    /**
     * Set paidBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $paidBy
     *
     * @return PropertyContractTransaction
     */
    public function setPaidBy(\Backend\AdminBundle\Entity\User $paidBy = null)
    {
        $this->paidBy = $paidBy;

        return $this;
    }

    /**
     * Get paidBy.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getPaidBy()
    {
        return $this->paidBy;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return PropertyContractTransaction
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
     * @return PropertyContractTransaction
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
     * @return PropertyContractTransaction
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
