<?php
namespace Backend\AdminBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Backend\AdminBundle\Entity\AlliedHouseProduct;
 
class AdjustCostPricesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('cdp:costprices')
            ->setDescription('Ajusta los precios de compra de cada casa del pollo, este es importante');
            //->addArgument('my_argument', InputArgument::OPTIONAL, 'Argument description')			
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
    
        // Do whatever
        
	  
		$alliedHouseProduct = $em->getRepository('BackendAdminBundle:AlliedHouseProduct')->getRowsToUpdate();
		//var_dump($alliedHouseProduct);die;	
		
		foreach ($alliedHouseProduct as $houseProduct) {
			
			//var_dump($houseProduct);die;
		
			$params = array();
			$params["idcliente"] = $houseProduct["sap_code"];
			$params["idproducto"] = $houseProduct["product_code"];
			
				
			$result = $this->getContainer()->get("services")->callSophia("get_precio_por_cliente_y_producto", $params, "get_precio_por_cliente_y_productoResult");
			//var_dump($result);die;
			
			if(isset($result["PreciosPorClienteYProducto"][0])){
				$precioBase = floatval(number_format($result["PreciosPorClienteYProducto"][0]["PrecioBase"], 2, '.', ''));
				
				//UPDATE 
				$houseProductID = intval($houseProduct["id"]);
				
				//$objAlliedHouseProduct = $em->getRepository('BackendAdminBundle:AlliedHouseProduct')->findBy(array("alliedHouse" => $alliedHouseID, "product" => $productID));
				$updateRow = $em->getRepository('BackendAdminBundle:AlliedHouseProduct')->updatePrices($precioBase, $houseProduct["id"]);
					
			}			
		} 		
				
		       
		
        	 
    }
}
