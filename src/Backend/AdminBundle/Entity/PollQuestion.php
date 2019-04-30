<?php

namespace Backend\AdminBundle\Entity;

/**
 * PollQuestion
 */
class PollQuestion
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $question;

    /**
     * @var string|null
     */
    private $pollFilePhoto;

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
     * @var \Backend\AdminBundle\Entity\Poll
     */
    private $poll;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $updatedBy;

    /**
     * @var \Backend\AdminBundle\Entity\PollQuestionType
     */
    private $pollQuestionType;


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
     * Set question.
     *
     * @param string $question
     *
     * @return PollQuestion
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question.
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set pollFilePhoto.
     *
     * @param string|null $pollFilePhoto
     *
     * @return PollQuestion
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
     * @return PollQuestion
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
     * @return PollQuestion
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
     * @return PollQuestion
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
     * Set poll.
     *
     * @param \Backend\AdminBundle\Entity\Poll|null $poll
     *
     * @return PollQuestion
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
     * Set createdBy.
     *
     * @param \Backend\AdminBundle\Entity\User|null $createdBy
     *
     * @return PollQuestion
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
     * @return PollQuestion
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
     * Set pollQuestionType.
     *
     * @param \Backend\AdminBundle\Entity\PollQuestionType|null $pollQuestionType
     *
     * @return PollQuestion
     */
    public function setPollQuestionType(\Backend\AdminBundle\Entity\PollQuestionType $pollQuestionType = null)
    {
        $this->pollQuestionType = $pollQuestionType;

        return $this;
    }

    /**
     * Get pollQuestionType.
     *
     * @return \Backend\AdminBundle\Entity\PollQuestionType|null
     */
    public function getPollQuestionType()
    {
        return $this->pollQuestionType;
    }

}
