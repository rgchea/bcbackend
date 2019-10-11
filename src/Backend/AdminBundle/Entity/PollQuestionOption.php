<?php

namespace Backend\AdminBundle\Entity;

/**
 * PollQuestionOption
 */
class PollQuestionOption
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $questionOption;

    /**
     * @var string|null
     */
    private $pollFilePhoto;

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
     * @var \Backend\AdminBundle\Entity\PollQuestion
     */
    private $pollQuestion;

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
     * Set questionOption.
     *
     * @param string|null $questionOption
     *
     * @return PollQuestionOption
     */
    public function setQuestionOption($questionOption = null)
    {
        $this->questionOption = $questionOption;

        return $this;
    }

    /**
     * Get questionOption.
     *
     * @return string|null
     */
    public function getQuestionOption()
    {
        return $this->questionOption;
    }

    /**
     * Set pollFilePhoto.
     *
     * @param string|null $pollFilePhoto
     *
     * @return PollQuestionOption
     */
    public function setPollFilePhoto($pollFilePhoto = null)
    {
        $this->pollFilePhoto = $pollFilePhoto;

        return $this;
    }

    /**
     * Get pollFilePhoto.
     *
     * @return string|null
     */
    public function getPollFilePhoto()
    {
        return $this->pollFilePhoto;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return PollQuestionOption
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
     * @return PollQuestionOption
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
     * @return PollQuestionOption
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
     * Set pollQuestion.
     *
     * @param \Backend\AdminBundle\Entity\PollQuestion|null $pollQuestion
     *
     * @return PollQuestionOption
     */
    public function setPollQuestion(\Backend\AdminBundle\Entity\PollQuestion $pollQuestion = null)
    {
        $this->pollQuestion = $pollQuestion;

        return $this;
    }

    /**
     * Get pollQuestion.
     *
     * @return \Backend\AdminBundle\Entity\PollQuestion|null
     */
    public function getPollQuestion()
    {
        return $this->pollQuestion;
    }

    /**
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return PollQuestionOption
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
     * @return PollQuestionOption
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
        return $this->getQuestionOption();
    }


}
