<?php
namespace Backend\AdminBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


use Backend\AdminBundle\Entity\Invoice;

 
class DeleteSharePropertyCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('bc:deleteshareproperty')
            ->setDescription('elimina las publicaciones compartidas de propiedades con mas de 30 días de antiguedad');
            //->addArgument('my_argument', InputArgument::OPTIONAL, 'Argument description')			
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
    
        //GET PAYMENT DAY
        $shared = $em->getRepository('BackendAdminBundle:Ticket')->getOldSharedProperties();

        if(!empty($shared)){

            foreach ($shared as $key => $value) {

                $objTicket = $em->getRepository('BackendAdminBundle:Ticket')->find($value);
                $em->remove($objTicket);
                $em->flush();
            }

        }



		
        	 
    }
}
