<?php

namespace Backend\AdminBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
    
    /**
     * @var string
     */
    private $name = '';


    /**
     * @var string
     */
    private $lastName = '';

    /**
     * @var \Backend\AdminBundle\Entity\Role
     */
    private $role;

    /**
     * @var string
     */
    private $nit = '';

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set role
     *
     * @param \Backend\AdminBundle\Entity\Role $role
     *
     * @return User
     */
    public function setRole(\Backend\AdminBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Backend\AdminBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }
	
	public function __toString(){
		return $this->getName()." ".$this->getLastName();
	}	



    /**
     * Set nit
     *
     * @param string $nit
     *
     * @return User
     */
    public function setNit($nit)
    {
        $this->nit = $nit;

        return $this;
    }

    /**
     * Get nit
     *
     * @return string
     */
    public function getNit()
    {
        return $this->nit;
    }


    /**
     * @var string
     */
    private $gender = 'N/A';


    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }
	
	
    /**
     * @var string
     */
    private $dpi = '';

    /**
     * @var integer
     */
    private $pin;


    /**
     * Set dpi
     *
     * @param string $dpi
     *
     * @return User
     */
    public function setDpi($dpi)
    {
        $this->dpi = $dpi;

        return $this;
    }

    /**
     * Get dpi
     *
     * @return string
     */
    public function getDpi()
    {
        return $this->dpi;
    }

    /**
     * Set pin
     *
     * @param integer $pin
     *
     * @return User
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * Get pin
     *
     * @return integer
     */
    public function getPin()
    {
        return $this->pin;
    }
    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $allied;


    /**
     * Set allied
     *
     * @param \Backend\AdminBundle\Entity\User $allied
     *
     * @return User
     */
    public function setAllied(\Backend\AdminBundle\Entity\User $allied = null)
    {
        $this->allied = $allied;

        return $this;
    }

    /**
     * Get allied
     *
     * @return \Backend\AdminBundle\Entity\User
     */
    public function getAllied()
    {
        return $this->allied;
    }
    /**
     * @var string
     */
    private $sapCode;


    /**
     * Set sapCode
     *
     * @param string $sapCode
     *
     * @return User
     */
    public function setSapCode($sapCode)
    {
        $this->sapCode = $sapCode;

        return $this;
    }

    /**
     * Get sapCode
     *
     * @return string
     */
    public function getSapCode()
    {
        return $this->sapCode;
    }
    /**
     * @var string
     */
    private $businessName = '';


    /**
     * Set businessName
     *
     * @param string $businessName
     *
     * @return User
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get businessName
     *
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }
    /**
     * @var \DateTime
     */
    private $birthdate;

    /**
     * @var int
     */
    private $rating = '1';

    /**
     * @var int
     */
    private $generalRating = '1';

    /**
     * @var int|null
     */
    private $activationConfirmCode;

    /**
     * @var int
     */
    private $level = '1';

    #* @Assert\NotBlank(message="Please, upload the photo.")#

    /**
     * @Assert\File(mimeTypes={ "image/png", "image/jpeg" })
     */
    private $avatarPath;


    /**
     * Set birthdate.
     *
     * @param \DateTime $birthdate
     *
     * @return User
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate.
     *
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set rating.
     *
     * @param int $rating
     *
     * @return User
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating.
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set generalRating.
     *
     * @param int $generalRating
     *
     * @return User
     */
    public function setGeneralRating($generalRating)
    {
        $this->generalRating = $generalRating;

        return $this;
    }

    /**
     * Get generalRating.
     *
     * @return int
     */
    public function getGeneralRating()
    {
        return $this->generalRating;
    }

    /**
     * Set activationConfirmCode.
     *
     * @param int|null $activationConfirmCode
     *
     * @return User
     */
    public function setActivationConfirmCode($activationConfirmCode = null)
    {
        $this->activationConfirmCode = $activationConfirmCode;

        return $this;
    }

    /**
     * Get activationConfirmCode.
     *
     * @return int|null
     */
    public function getActivationConfirmCode()
    {
        return $this->activationConfirmCode;
    }

    /**
     * Set level.
     *
     * @param int $level
     *
     * @return User
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set avatarPath.
     *
     * @param string|null $avatarPath
     *
     * @return User
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
    /**
     * @var \DateTime
     */
    private $createdAt = '0000-00-00 00:00:00';

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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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

    public function getAvatarUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/images/avatars/';
    }

    /**
     * @var \Backend\AdminBundle\Entity\Business
     */
    private $business;


    /**
     * Set business.
     *
     * @param \Backend\AdminBundle\Entity\Business|null $business
     *
     * @return User
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
     * @var \Backend\AdminBundle\Entity\User
     */
    private $supervisor;


    /**
     * Set supervisor.
     *
     * @param \Backend\AdminBundle\Entity\User|null $supervisor
     *
     * @return User
     */
    public function setSupervisor(\Backend\AdminBundle\Entity\User $supervisor = null)
    {
        $this->supervisor = $supervisor;

        return $this;
    }

    /**
     * Get supervisor.
     *
     * @return \Backend\AdminBundle\Entity\User|null
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }
    /**
     * @var string
     */
    private $mobilePhone;




    /**
     * Set mobilePhone.
     *
     * @param string $mobilePhone
     *
     * @return User
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * Get mobilePhone.
     *
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @var \Backend\AdminBundle\Entity\GeoCountry
     */
    private $geoCountry;


    /**
     * Set geoCountry.
     *
     * @param \Backend\AdminBundle\Entity\GeoCountry|null $geoCountry
     *
     * @return User
     */
    public function setGeoCountry(\Backend\AdminBundle\Entity\GeoCountry $geoCountry = null)
    {
        $this->geoCountry = $geoCountry;

        return $this;
    }

    /**
     * Get geoCountry.
     *
     * @return \Backend\AdminBundle\Entity\GeoCountry|null
     */
    public function getGeoCountry()
    {
        return $this->geoCountry;
    }
}
