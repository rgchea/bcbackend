<?php
namespace Backend\AdminBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


use Backend\AdminBundle\Lib\CloudOnex;

 
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

                $body = array(
                    //array('name' => 'cid', 'contents' => $complex["customer_id"]),
                    array('name' => 'cid', 'contents' => 295),
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

                    array('name' => 'items[0][description]', 'contents' => $complex["complex_name"]),
                    array('name' => 'items[0][item_code]', 'contents' => ''),
                    array('name' => 'items[0][qty]', 'contents' => 1),
                    array('name' => 'items[0][amount]', 'contents' => 100),
                    array('name' => 'items[0][taxed]', 'contents' => 0),


                    array('name' => 'idate', 'contents' => gmdate('Y-m-d')),

                )
                ;
                //print "<pre>";
                //var_dump($body);die;


                $response = $this->getApplication()->getKernel()->getContainer()->get('services')->callBCInfo("POST", "invoice", $body);
                //var_dump($response);die;


                /*
                $body = array(
                    array('name' => 'account', 'contents' => 'Alissa WhiteGluz50'),
                    array('name' => 'phone', 'contents' => '+18000006934')
                )
                ;
                */

                //$createCustomer = $this->getApplication()->getKernel()->getContainer()->get('services')->callBCInfo("POST", "customer", $body);
                //var_dump($createCustomer);die;


                die;

                if($response){
                    ///INSERT ON DB O JUST PULL THE INFO FROM .INFO?
                }




            }

        }

				
		       
		
        	 
    }
}
