<?php


namespace Backend\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Complex
 */
class Complex
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $taxIdentifier;

    /**
     * @var string
     */
    private $infoWallPhotoPath = '';

    /**
     * @var int|null
     */
    private $duePaymentDay = 5;

    /**
     * @var bool
     */
    private $totalVisibleAmount = '1';

    /**
     * @var bool
     */
    private $visibleBalance = '1';

    /**
     * @var string|null
     */
    private $qrCodePath;

    /**
     * @var string|null
     */
    private $address;

    /**
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @var string|null
     */
    private $emergencyPhoneNumber;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $responsiblePerson;

    /**
     * @var string|null
     */
    private $paymentInstruction;

    /**
     * @var string|null
     */
    private $help;

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
     * @var \Backend\AdminBundle\Entity\Business
     */
    private $business;

    /**
     * @var \Backend\AdminBundle\Entity\ComplexType
     */
    private $complexType;

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
     * Set name.
     *
     * @param string|null $name
     *
     * @return Complex
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set taxIdentifier.
     *
     * @param string|null $taxIdentifier
     *
     * @return Complex
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
     * Set infoWallPhotoPath.
     *
     * @param string $infoWallPhotoPath
     *
     * @return Complex
     */
    public function setInfoWallPhotoPath($infoWallPhotoPath)
    {
        $this->infoWallPhotoPath = $infoWallPhotoPath;

        return $this;
    }

    /**
     * Get infoWallPhotoPath.
     *
     * @return string
     */
    public function getInfoWallPhotoPath()
    {
        return $this->infoWallPhotoPath;
    }

    /**
     * Set duePaymentDay.
     *
     * @param int|null $duePaymentDay
     *
     * @return Complex
     */
    public function setDuePaymentDay($duePaymentDay = null)
    {
        $this->duePaymentDay = $duePaymentDay;

        return $this;
    }

    /**
     * Get duePaymentDay.
     *
     * @return int|null
     */
    public function getDuePaymentDay()
    {
        return $this->duePaymentDay;
    }

    /**
     * Set totalVisibleAmount.
     *
     * @param bool $totalVisibleAmount
     *
     * @return Complex
     */
    public function setTotalVisibleAmount($totalVisibleAmount)
    {
        $this->totalVisibleAmount = $totalVisibleAmount;

        return $this;
    }

    /**
     * Get totalVisibleAmount.
     *
     * @return bool
     */
    public function getTotalVisibleAmount()
    {
        return $this->totalVisibleAmount;
    }

    /**
     * Set visibleBalance.
     *
     * @param bool $visibleBalance
     *
     * @return Complex
     */
    public function setVisibleBalance($visibleBalance)
    {
        $this->visibleBalance = $visibleBalance;

        return $this;
    }

    /**
     * Get visibleBalance.
     *
     * @return bool
     */
    public function getVisibleBalance()
    {
        return $this->visibleBalance;
    }

    /**
     * Set qrCodePath.
     *
     * @param string|null $qrCodePath
     *
     * @return Complex
     */
    public function setQrCodePath($qrCodePath = null)
    {
        $this->qrCodePath = $qrCodePath;

        return $this;
    }

    /**
     * Get qrCodePath.
     *
     * @return string|null
     */
    public function getQrCodePath()
    {
        return $this->qrCodePath;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Complex
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
     * Set phoneNumber.
     *
     * @param string|null $phoneNumber
     *
     * @return Complex
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
     * Set emergencyPhoneNumber.
     *
     * @param string|null $emergencyPhoneNumber
     *
     * @return Complex
     */
    public function setEmergencyPhoneNumber($emergencyPhoneNumber = null)
    {
        $this->emergencyPhoneNumber = $emergencyPhoneNumber;

        return $this;
    }

    /**
     * Get emergencyPhoneNumber.
     *
     * @return string|null
     */
    public function getEmergencyPhoneNumber()
    {
        return $this->emergencyPhoneNumber;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Complex
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
     * @return Complex
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
     * Set paymentInstruction.
     *
     * @param string|null $paymentInstruction
     *
     * @return Complex
     */
    public function setPaymentInstruction($paymentInstruction = null)
    {
        $this->paymentInstruction = $paymentInstruction;

        return $this;
    }

    /**
     * Get paymentInstruction.
     *
     * @return string|null
     */
    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }

    /**
     * Set help.
     *
     * @param string|null $help
     *
     * @return Complex
     */
    public function setHelp($help = null)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Get help.
     *
     * @return string|null
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Complex
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
     * @return Complex
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
     * @return Complex
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
     * Set business.
     *
     * @param \Backend\AdminBundle\Entity\Business|null $business
     *
     * @return Complex
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
     * Set complexType.
     *
     * @param \Backend\AdminBundle\Entity\ComplexType|null $complexType
     *
     * @return Complex
     */
    public function setComplexType(\Backend\AdminBundle\Entity\ComplexType $complexType = null)
    {
        $this->complexType = $complexType;

        return $this;
    }

    /**
     * Get complexType.
     *
     * @return \Backend\AdminBundle\Entity\ComplexType|null
     */
    public function getComplexType()
    {
        return $this->complexType;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return Complex
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
     * @return Complex
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


    public function __toString(){

        return $this->getName();
    }

    /**
     * @var string|null
     */
    private $zipCode;

    /**
     * @var \Backend\AdminBundle\Entity\GeoState
     */
    private $geoState;


    /**
     * Set zipCode.
     *
     * @param string|null $zipCode
     *
     * @return Complex
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
     * Set geoState.
     *
     * @param \Backend\AdminBundle\Entity\GeoState|null $geoState
     *
     * @return Complex
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

    /**
     * @var \Backend\AdminBundle\Entity\GeoCountry
     */
    private $phoneCountry;


    /**
     * Set phoneCountry.
     *
     * @param \Backend\AdminBundle\Entity\GeoCountry|null $phoneCountry
     *
     * @return Complex
     */
    public function setPhoneCountry(\Backend\AdminBundle\Entity\GeoCountry $phoneCountry = null)
    {
        $this->phoneCountry = $phoneCountry;

        return $this;
    }

    /**
     * Get phoneCountry.
     *
     * @return \Backend\AdminBundle\Entity\GeoCountry|null
     */
    public function getPhoneCountry()
    {
        return $this->phoneCountry;
    }
    /**
     * @var int|null
     */
    private $teamCorrelativeTenant;

    /**
     * @var int|null
     */
    private $teamCorrelativeAdmin;


    /**
     * Set teamCorrelativeTenant.
     *
     * @param int|null $teamCorrelativeTenant
     *
     * @return Complex
     */
    public function setTeamCorrelativeTenant($teamCorrelativeTenant = null)
    {
        $this->teamCorrelativeTenant = $teamCorrelativeTenant;

        return $this;
    }

    /**
     * Get teamCorrelativeTenant.
     *
     * @return int|null
     */
    public function getTeamCorrelativeTenant()
    {
        return $this->teamCorrelativeTenant;
    }

    /**
     * Set teamCorrelativeAdmin.
     *
     * @param int|null $teamCorrelativeAdmin
     *
     * @return Complex
     */
    public function setTeamCorrelativeAdmin($teamCorrelativeAdmin = null)
    {
        $this->teamCorrelativeAdmin = $teamCorrelativeAdmin;

        return $this;
    }

    /**
     * Get teamCorrelativeAdmin.
     *
     * @return int|null
     */
    public function getTeamCorrelativeAdmin()
    {
        return $this->teamCorrelativeAdmin;
    }
    /**
     * @var int|null
     */
    private $teamCorrelative;


    /**
     * Set teamCorrelative.
     *
     * @param int|null $teamCorrelative
     *
     * @return Complex
     */
    public function setTeamCorrelative($teamCorrelative = null)
    {
        $this->teamCorrelative = $teamCorrelative;

        return $this;
    }

    /**
     * Get teamCorrelative.
     *
     * @return int|null
     */
    public function getTeamCorrelative()
    {
        return $this->teamCorrelative;
    }
    /**
     * @Assert\File(maxSize="3M", mimeTypes={ "image/png", "image/jpeg", "image/jpg" })
     */
    private $avatarPath;


    /**
     * Set avatarPath.
     *
     * @param string|null $avatarPath
     *
     * @return Complex
     */
    public function setAvatarPath($avatarPath = null)
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

    /**
     * Get avatarPath.
     *
     * @return string|null
     */
    public function getAvatarPath()
    {
        return $this->avatarPath;
    }


    public function getAvatarUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/images/complex/';
    }
    /**
     * @var bool
     */
    private $latePayment = false;


    /**
     * Set latePayment.
     *
     * @param bool $latePayment
     *
     * @return Complex
     */
    public function setLatePayment($latePayment)
    {
        $this->latePayment = $latePayment;

        return $this;
    }

    /**
     * Get latePayment.
     *
     * @return bool
     */
    public function getLatePayment()
    {
        return $this->latePayment;
    }
}
