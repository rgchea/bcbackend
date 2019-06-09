<?php

namespace Backend\AdminBundle\Entity;

/**
 * UserAgreementPlan
 */
class UserAgreementPlan
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $isActive = '1';

    /**
     * @var \DateTime
     */
    private $expirationDate = '0000-00-00 00:00:00';

    /**
     * @var bool
     */
    private $monthlyPayment = '0';

    /**
     * @var bool
     */
    private $annualPayment = '0';

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
     * @var \Backend\AdminBundle\Entity\AgreementPlan
     */
    private $agreementPlan;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\UserBusiness
     */
    private $userBusiness;


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
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return UserAgreementPlan
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set expirationDate.
     *
     * @param \DateTime $expirationDate
     *
     * @return UserAgreementPlan
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate.
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set monthlyPayment.
     *
     * @param bool $monthlyPayment
     *
     * @return UserAgreementPlan
     */
    public function setMonthlyPayment($monthlyPayment)
    {
        $this->monthlyPayment = $monthlyPayment;

        return $this;
    }

    /**
     * Get monthlyPayment.
     *
     * @return bool
     */
    public function getMonthlyPayment()
    {
        return $this->monthlyPayment;
    }

    /**
     * Set annualPayment.
     *
     * @param bool $annualPayment
     *
     * @return UserAgreementPlan
     */
    public function setAnnualPayment($annualPayment)
    {
        $this->annualPayment = $annualPayment;

        return $this;
    }

    /**
     * Get annualPayment.
     *
     * @return bool
     */
    public function getAnnualPayment()
    {
        return $this->annualPayment;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return UserAgreementPlan
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
     * @return UserAgreementPlan
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
     * @return UserAgreementPlan
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
     * Set agreementPlan.
     *
     * @param \Backend\AdminBundle\Entity\AgreementPlan|null $agreementPlan
     *
     * @return UserAgreementPlan
     */
    public function setAgreementPlan(\Backend\AdminBundle\Entity\AgreementPlan $agreementPlan = null)
    {
        $this->agreementPlan = $agreementPlan;

        return $this;
    }

    /**
     * Get agreementPlan.
     *
     * @return \Backend\AdminBundle\Entity\AgreementPlan|null
     */
    public function getAgreementPlan()
    {
        return $this->agreementPlan;
    }

    /**
     * Set updatedBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $updatedBy
     *
     * @return UserAgreementPlan
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
     * @return UserAgreementPlan
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
     * Set userBusiness.
     *
     * @param \Backend\AdminBundle\Entity\UserBusiness|null $userBusiness
     *
     * @return UserAgreementPlan
     */
    public function setUserBusiness(\Backend\AdminBundle\Entity\UserBusiness $userBusiness = null)
    {
        $this->userBusiness = $userBusiness;

        return $this;
    }

    /**
     * Get userBusiness.
     *
     * @return \Backend\AdminBundle\Entity\UserBusiness|null
     */
    public function getUserBusiness()
    {
        return $this->userBusiness;
    }
    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $user;


    /**
     * Set user.
     *
     * @param \Backend\AdminBundle\Entity\User|null $user
     *
     * @return UserAgreementPlan
     */
    public function setUser(\Backend\AdminBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
