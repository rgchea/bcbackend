<?php

namespace Backend\AdminBundle\Entity;

/**
 * TicketFollower
 */
class TicketFollower
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $enabled = 1;

    /**
     * @var \Backend\AdminBundle\Entity\User
     */
    private $user;

    /**
     * @var \Backend\AdminBundle\Entity\Ticket
     */
    private $ticket;


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
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return TicketFollower
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
     * Set user.
     *
     * @param \Backend\AdminBundle\Entity\User|null $user
     *
     * @return TicketFollower
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

    /**
     * Set ticket.
     *
     * @param \Backend\AdminBundle\Entity\Ticket|null $ticket
     *
     * @return TicketFollower
     */
    public function setTicket(\Backend\AdminBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket.
     *
     * @return \Backend\AdminBundle\Entity\Ticket|null
     */
    public function getTicket()
    {
        return $this->ticket;
    }
}
