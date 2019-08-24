<?php

namespace Backend\AdminBundle\Entity;

/**
 * PaymentLog
 */
class PaymentLog
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $paymentAmount = 0.0;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \Backend\AdminBundle\Entity\PropertyContractTransaction
     */
    private $propertyContractTransaction;

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
     * Set paymentAmount.
     *
     * @param float $paymentAmount
     *
     * @return PaymentLog
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return PaymentLog
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
     * @return PaymentLog
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
     * Set propertyContractTransaction.
     *
     * @param \Backend\AdminBundle\Entity\PropertyContractTransaction|null $propertyContractTransaction
     *
     * @return PaymentLog
     */
    public function setPropertyContractTransaction(\Backend\AdminBundle\Entity\PropertyContractTransaction $propertyContractTransaction = null)
    {
        $this->propertyContractTransaction = $propertyContractTransaction;

        return $this;
    }

    /**
     * Get propertyContractTransaction.
     *
     * @return \Backend\AdminBundle\Entity\PropertyContractTransaction|null
     */
    public function getPropertyContractTransaction()
    {
        return $this->propertyContractTransaction;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return PaymentLog
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
     * @return PaymentLog
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
     * @var string|null
     */
    private $status;


    /**
     * Set status.
     *
     * @param string|null $status
     *
     * @return PaymentLog
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }
}
