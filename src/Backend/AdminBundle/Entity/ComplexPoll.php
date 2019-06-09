<?php

namespace Backend\AdminBundle\Entity;

/**
 * ComplexPoll
 */
class ComplexPoll
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt = '0000-00-00 00:00:00';

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\Poll
     */
    private $poll;

    /**
     * @var \Backend\AdminBundle\Entity\Complex
     */
    private $complex;


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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ComplexPoll
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
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return ComplexPoll
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
     * Set poll.
     *
     * @param \Backend\AdminBundle\Entity\Poll|null $poll
     *
     * @return ComplexPoll
     */
    public function setPoll(\Backend\AdminBundle\Entity\Poll $poll = null)
    {
        $this->poll = $poll;

        return $this;
    }

    /**
     * Get poll.
     *
     * @return \Backend\AdminBundle\Entity\Poll|null
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * Set complex.
     *
     * @param \Backend\AdminBundle\Entity\Complex|null $complex
     *
     * @return ComplexPoll
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
     * @return ComplexPoll
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
     * @return ComplexPoll
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
