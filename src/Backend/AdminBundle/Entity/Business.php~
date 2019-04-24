<?php

namespace Backend\AdminBundle\Entity;

/**
 * Business
 */
class Business
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $taxIdentifier;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string|null
     */
    private $address = '';

    /**
     * @var string|null
     */
    private $zipCode;

    /**
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $responsiblePerson;

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
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\GeoState
     */
    private $geoState;


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
     * Set taxIdentifier.
     *
     * @param string|null $taxIdentifier
     *
     * @return Business
     */
    public function setTaxIdentifier($taxIdentifier = null)
    {
        $this->taxIdentifier = $taxIdentifier;

        return $this;
    }

    /**
     * Get taxIdentifier.
     *
     * @return string|null
     */
    public function getTaxIdentifier()
    {
        return $this->taxIdentifier;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Business
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Business
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set zipCode.
     *
     * @param string|null $zipCode
     *
     * @return Business
     */
    public function setZipCode($zipCode = null)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode.
     *
     * @return string|null
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set phoneNumber.
     *
     * @param string|null $phoneNumber
     *
     * @return Business
     */
    public function setPhoneNumber($phoneNumber = null)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber.
     *
     * @return string|null
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Business
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set responsiblePerson.
     *
     * @param string|null $responsiblePerson
     *
     * @return Business
     */
    public function setResponsiblePerson($responsiblePerson = null)
    {
        $this->responsiblePerson = $responsiblePerson;

        return $this;
    }

    /**
     * Get responsiblePerson.
     *
     * @return string|null
     */
    public function getResponsiblePerson()
    {
        return $this->responsiblePerson;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Business
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
     * @return Business
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
     * @return Business
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
     * @return Business
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
     * @return Business
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
     * Set geoState.
     *
     * @param \Backend\AdminBundle\Entity\GeoState|null $geoState
     *
     * @return Business
     */
    public function setGeoState(\Backend\AdminBundle\Entity\GeoState $geoState = null)
    {
        $this->geoState = $geoState;

        return $this;
    }

    /**
     * Get geoState.
     *
     * @return \Backend\AdminBundle\Entity\GeoState|null
     */
    public function getGeoState()
    {
        return $this->geoState;
    }


    public function __toString(){

        return $this->getName();
    }

    /**
     * @var string|null
     */
    private $taxName;

    /**
     * @var string|null
     */
    private $billingAddress = '';

    /**
     * @var string|null
     */
    private $billingZipCode;

    /**
     * @var \Backend\AdminBundle\Entity\GeoState
     */
    private $billingGeoState;


    /**
     * Set taxName.
     *
     * @param string|null $taxName
     *
     * @return Business
     */
    public function setTaxName($taxName = null)
    {
        $this->taxName = $taxName;

        return $this;
    }

    /**
     * Get taxName.
     *
     * @return string|null
     */
    public function getTaxName()
    {
        return $this->taxName;
    }

    /**
     * Set billingAddress.
     *
     * @param string|null $billingAddress
     *
     * @return Business
     */
    public function setBillingAddress($billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress.
     *
     * @return string|null
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set billingZipCode.
     *
     * @param string|null $billingZipCode
     *
     * @return Business
     */
    public function setBillingZipCode($billingZipCode = null)
    {
        $this->billingZipCode = $billingZipCode;

        return $this;
    }

    /**
     * Get billingZipCode.
     *
     * @return string|null
     */
    public function getBillingZipCode()
    {
        return $this->billingZipCode;
    }

    /**
     * Set billingGeoState.
     *
     * @param \Backend\AdminBundle\Entity\GeoState|null $billingGeoState
     *
     * @return Business
     */
    public function setBillingGeoState(\Backend\AdminBundle\Entity\GeoState $billingGeoState = null)
    {
        $this->billingGeoState = $billingGeoState;

        return $this;
    }

    /**
     * Get billingGeoState.
     *
     * @return \Backend\AdminBundle\Entity\GeoState|null
     */
    public function getBillingGeoState()
    {
        return $this->billingGeoState;
    }



    /**
     * @var int|null
     */
    private $customerId;


    /**
     * Set customerId.
     *
     * @param int|null $customerId
     *
     * @return Business
     */
    public function setCustomerId($customerId = null)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId.
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
