<?php
namespace Backend\AdminBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


use Backend\AdminBundle\Entity\Invoice;

 
class SendInvoiceCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('bc:invoices')
            ->setDescription('envia facturas de todos los complejos de los business');
            //->addArgument('my_argument', InputArgument::OPTIONAL, 'Argument description')			
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
    
        //GET PAYMENT DAY
        $setting = $em->getRepository('BackendAdminBundle:AdminSetting')->find(1);
        $duePaymentDay = $setting->getDuePaymentDay();

		$arrComplexFee = $em->getRepository('BackendAdminBundle:Complex')->getComplexForInvoice($duePaymentDay);
		//die;
		//var_dump($arrComplexFee);die;

        //var_dump(gmdate('Y-m-d'));die;

        if(!empty($arrComplexFee)){

            foreach ($arrComplexFee as $complex) {

                /*
                $arrItems = array(
                    array('name' => 'description', 'contents' => $complex["complex_name"]),# Item name / description
                    array('name' => 'item_code','contents' => ''), # You can add item code to track inventory)
                    array('name' => 'qty', 'contents' => 1),# Item quantity
                    array('name' => 'amount', 'contents' => $complex["fee"]), #item price
                    array('name' => 'taxed',  'contents' => 0.00)
                );
                */

                $myFee = floatval($complex["fee"]);

                //if($myFee > 0){

                    $body = array(
                        array('name' => 'cid', 'contents' => $complex["customer_id"]),
                        //array('name' => 'cid', 'contents' => 295),//CUSTOMER ID
                        array('name' => 'admin_id', 'contents' => 0),
                        array('name' => 'status', 'contents' => 'Published'),
                        array('name' => 'currency', 'contents' => 'USD'),
                        array('name' => 'invoicdnum', 'contents' => 'INV#'),
                        array('name' => 'show_quantity_as', 'contents' => 'Qty'),
                        array('name' => 'cn', 'contents' => ''),

                        /* Possible values for due date
                        due_on_receipt : Due On Receipt
                        days3 : +3 days
                        days5 : + 5 days
                        days7 : + 7 days
                        days10 : + 10 days
                        days15 : + 15 days
                        days30 : + 30 days
                        days45 : + 45 days
                        days60 : + 50 days
                        */
                        array('name' => 'duedate', 'contents' => 'due_on_receipt'),
                        array('name' => 'repeat', 'contents' => 0),
                        array('name' => 'discount_type', 'contents' => 'p'),
                        array('name' => 'discount_amount', 'contents' => '0'),
                        array('name' => 'notes', 'contents' => ''),

                        array('name' => 'items[0][description]', 'contents' => $complex["complex_name"]),///detalle
                        array('name' => 'items[0][item_code]', 'contents' => ''),
                        array('name' => 'items[0][qty]', 'contents' => 1),
                        array('name' => 'items[0][amount]', 'contents' => $myFee),
                        array('name' => 'items[0][taxed]', 'contents' => 0),


                        array('name' => 'idate', 'contents' => gmdate('Y-m-d')),

                    )
                    ;
                    //print "<pre>";
                    //var_dump($body);die;

                    $objCountry = $em->getRepository('BackendAdminBundle:GeoCountry')->find(intval($complex["country_id"]));
                    $response = $this->getApplication()->getKernel()->getContainer()->get('services')->callBCInfo($objCountry,"POST", "invoice", $body);
                    //var_dump($response);die;

                    $business = $em->getRepository('BackendAdminBundle:Business')->findOneByCustomerId($complex["customer_id"]);
                    if($business){
                        //$business = $business[0];

                        $invoice = new Invoice();
                        $invoice->setBusiness($business);
                        $invoice->setDescription($complex["complex_name"]);
                        $invoice->setCreatedAt(new \DateTime(gmdate('Y-m-d h:i:s')));
                        $invoice->setUpdatedAt(new \DateTime(gmdate('Y-m-d h:i:s    ')));
                        $invoice->setSent(0);
                        $invoice->setAmount($myFee);
                    }


                    if($response["error"] == false){//CODE 200 ok and Error false
                        ///INSERT ON DB O JUST PULL THE INFO FROM .INFO?
                        $invoice->setSent(1);

                    }


                    $em->persist($invoice);
                    $em->flush();


            }
        }

    }
}
