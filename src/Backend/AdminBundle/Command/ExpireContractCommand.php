<?php
namespace Backend\AdminBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


use Backend\AdminBundle\Entity\PropertyContract;

 
class ExpireContractCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('bc:expirecontract')
            ->setDescription('revisa los contractos que vencen cambia su estado y envia notificacion');
            //->addArgument('my_argument', InputArgument::OPTIONAL, 'Argument description')			
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $myServices = $this->getApplication()->getKernel()->getContainer()->get('services');
        $myTranslator = $this->getApplication()->getKernel()->getContainer()->get('translator');

		$arrContracts = $em->getRepository('BackendAdminBundle:PropertyContract')->checkExpiration();

		//var_dump($arrContracts);die;

        if(!empty($arrContracts)){

            foreach ($arrContracts as $contract) {

                $propertyContract = $em->getRepository('BackendAdminBundle:PropertyContract')->find(intval($contract["id"]));
                $tenantContract = $propertyContract->getMainTenantContract();

                $propertyContract->setIsActive(0);
                //$propertyContract->setEnabled(0);
                $em->persist($propertyContract);

                $property = $em->getRepository('BackendAdminBundle:Property')->find(intval($contract["property_id"]));
                $property->setIsAvailable(1);
                $em->persist($property);

                ///SEND MAIL NOTIFY THE OWNER

                //new message from sendgrid
                $myLocale = $property->getComplex()->getBusiness()->getGeoState()->getGeoCountry()->getLocale();
                if($myLocale == "es"){
                    $templateID = "d-cbf8bac669184dafa57cbb3f31be19b3";
                }
                else{
                    $templateID = "d-9f23bf6df1214e3e88fe5300922427ca";
                }

                //tenant_name
                //property_address
                //complex_name
                $myJson = '"tenant_name": "'.$tenantContract->getUser()->getName().'",';
                $myJson .= '"property_address": "'.$property->getPropertyNumber().' '.$property->getAddress().'",';
                $myJson .= '"complex_name": "'.$property->getComplex()->getName().'",';

                $mail = $tenantContract->getUser()->getEmail();
                $mail = "renatochea@gmail.com";
                //var_dump($templateID);die;

                $sendgridResponse = $myServices->callSendgrid($myJson, $templateID, $mail);


                $myServices->blameOnMe($propertyContract, 'update');

                $em->flush();
                die;

                $title = $myTranslator->trans("push.contract_cancel_title");
                $description = $myTranslator->trans("label_property")." ".$property->getPropertyNumber().": ". $myTranslator->trans("push.contract_cancel_desc");
                $myServices->sendPushNotification($tenantContract->getUser(), $title, $description);


            }

        }


    }
}
