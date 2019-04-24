<?php
namespace Backend\AdminBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class Log extends Controller
{
    
    private $em;
    
    public function add($text, $user, $action = 'none'){
        $log = new \Backend\AdminBundle\Entity\RepairLog();
        $log->setAction($action);
        $log->setFosUser($user);
        $log->setText($text);
        $log->setCreatedAt(new \DateTime());
        
        $this->em->persist($log);
        $this->em->flush();
    }
    
    public function __construct(EntityManagerInterface $entityManager){
        $this->em = $entityManager;
    }
}