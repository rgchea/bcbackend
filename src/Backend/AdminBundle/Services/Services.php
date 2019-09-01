<?php
namespace Backend\AdminBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Security;

#use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use GuzzleHttp\Client;


class Services extends Controller
{
    
    private $em;
    protected $container;
    private $security;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container, Security $security){
        $this->em = $entityManager;
        $this->container = $container;
        $this->security = $security;
        $this->translator = $this->get('translator');
    }
    
    public function getRandomCode($length = 8, $entity = 'User', $min = false){
        
        if($min){
            $chars = "_ABCDEFGHIJKLMNO-PQRSTUVWX_Yab_cdefghijklmnnopqrs-tu-vwxyz011234567_89"; 
        }else{
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXY023456789"; 
        }

        srand((double)microtime()*1000000); 
        $i = 0; 
        $pass = '' ; 

        while ($i <= $length) { 
            $num = rand() % 33; 
            $tmp = substr($chars, $num, 1); 
            $pass = $pass . $tmp; 
            $i++; 
        }

        if($entity == 'User'){
            $code_field = 'secret_token';
        }else{
            $code_field = 'code';
        }

        $doesCodeExistsBefore = $this->em->getRepository('BackendAdminBundle:' . $entity)
            ->findOneBy(array($code_field => $pass));

        if($doesCodeExistsBefore){
            $pass = $this->getRandomCode($length);
        }

        return $pass; 
    }
    

	
	public function getUser(){

		//return $this->container->get('security.context')->getToken()->getUser();
        return $this->security->getUser();
	}
	

	

	public function generalTemplateMail($subject, $to, $bodyHtml, $from = null){
		
	    $mailer = $this->get('mailer');
	    $message = $mailer->createMessage()
	        ->setSubject($subject)
	        //->setTo('rchea@operalogistica.com');
	        ->setTo($to);
			
			if($from != null){
				$message->setFrom($from);
			}
			else{
				$message->setFrom("info@bettercondos.com", "BetterCondos");
			}


        $message->setBody(
            $this->container->get('templating')->render('BackendAdminBundle:Default:email.html.twig', array('body_html' => $bodyHtml)),
            'text/html'
        );

			/*
        $message->addPart(

            $this->container->get('templating')->render('BackendAdminBundle:Default:email.html.twig', array('body_text' => $bodyText)),
            'plain/text'
        );
			*/


		//var_dump($mailer->send($message));die;
	    return $mailer->send($message); 			
	}	
		
		
	public function checkUsername($username, $id){
		
		$em = $this->getDoctrine()->getManager();
		$check = $em->getRepository('BackendAdminBundle:User')->findByUsername($username);
		
		//var_dump($check);die;
		
		
		$checkId = $em->getRepository('BackendAdminBundle:User')->findById($id);
		
		
		if($id == 0){
			if(count($check)){
				return TRUE;	
			}
			else{
				return FALSE;
			}					
		}
		else{
			if(empty($check) || empty($checkId)){
				return FALSE;
			}
			else{
				if($check[0]->getId() == $checkId[0]->getId()){
					return FALSE;	
				}
				else{
					return TRUE;
				}
				
			}
			
			
		}
		
	}	
	
	public function checkEmail($email, $id){


		
		$em = $this->getDoctrine()->getManager();
		$check = $em->getRepository('BackendAdminBundle:User')->findByEmail($email);
		$checkId = $em->getRepository('BackendAdminBundle:User')->findById($id);
		
		
		if($id == 0){
		    //print "entra nuevo";die;
			//new
			if(count($check)){
				return TRUE;	
			}
			else{
				return FALSE;
			}					
		}
		else{
			//edit
			
			/*
			print "entra";
			var_dump($check);
			var_dump($checkId[0]);
			die;
			 * 
			 */
			
			if(count($check) &&  count($checkId)){
				if($check[0]->getId() == $checkId[0]->getId()){
					return FALSE;	
				}
				else{
					return TRUE;
				}			
				
			}
			else{
				return FALSE;
			}
		}	
		
	}
	

	//public function checkExistence($username, $email, $id){
	public function checkExistence($email, $id){
		
		//$checkUser = $this->checkUsername($username, $id);
		$checkEmail = $this->checkEmail($email, $id);
		//var_dump($checkEmail);die;
		$warning = "";/*
		if($checkUser){

			$warning = $this->translator->trans('label_user_taken');
		}
		elseif ($checkEmail) {
			$warning = $this->translator->trans('label_mail_taken');;

		}
	    */

        if ($checkEmail) {
            $warning = $this->translator->trans('label_mail_taken');;

        }

		return $warning;
	}	


	public function flashCustom($request, $message){
		
		$request->getSession()->getFlashBag()->add('warning',$message);
	}

	public function flashWarning($request){
		
		$request->getSession()->getFlashBag()->add('warning',$this->translator->trans('label_flash_save_warning_msg'));
	}
	
	public function flashSuccess($request){
		
		$request->getSession()->getFlashBag()->add('success', $this->translator->trans('label_flash_save_success_msg'));
	}
	
	public function flashWarningForeignKey($request){
		//type success, warning, danger, info
		$request->getSession()->getFlashBag()->add('warning',$this->translator->trans('label_flash_delete_msg'));
	}	
	
	public function userHasAccess(){
		
		$session = new Session();
		$item = $session->get('item');
		//var_dump($item);
		$userAccess = $session->get('user_access');
		
		if($userAccess != null){

			$object = (object) $userAccess;	
			
			//print "<pre>";
			//var_dump($object);die;
			/*
			foreach ($object as $key => $value) {
				
			}
			 **/
			foreach ($object as $key => $value) {
				if($value["moduleType"] == "menu"){
					/*
					print "entra";
					print_r($value);
					 *
					 */
					foreach ($value as $llave => $valor) {
						if(is_array($valor) ){
							if($item == $valor["systemName"]){
								//print "ENTRA PUTO";DIE;
								return true;
							}						
						}
						//print_r($valor);

					}
				}
				else{
					if($item == $value["systemName"]){
						return true;
					}
				}				
			}			 
			//die;
			
			
		}
		
		
		return false;
	}
	
	
	public function getUserAccess(){
		
		$session = new Session();
		
		$role = "ROLE_USER";
		$clientAccess = array();
		
		$user = $this->getUser();
		$role = $user->getRole();
		//var_dump($client->getRoles());die;
		
		$userAccess = $this->em->getRepository('BackendAdminBundle:ModuleAccess')->getUserAccessByRole($role->getId());
		//print "<pre>";
		//var_dump($userAccess);die;
		$session->set("user_access", $userAccess);
		$session->set("user_role", $role);
		//get permissions
			
	}
	
	
	
	
	public function systemNotification($to, $name, $description, $type = null){
		
		//print "entra";die;
		//fosUserId
		//createdBy
		//NotificationType ->Recordatorio
		//name
		//enabled -> 1
		//registrationDate
		//expirationDate
		//description
		//createdAt
		//alreadRead -> 0
		$notification = $this->em->getRepository('BackendAdminBundle:Notification')->systemNotification($to, $name, $description, $type);
		
	}
	
	
	
	public function setVars($item){
		
		$session = new Session();
		
		$em = $this->em;
        $session = new Session();
        $session->set('item', $item);
		
		$auth_checker = $this->get('security.authorization_checker');
		$auth = $auth_checker->isGranted('ROLE_USER');
		
		if(!$auth){
			throw new AccessDeniedException();//el usuario está loggeado
		}
			
			
		$user = $this->getUser();
		$session->set('user_logged', $user);
		//var_dump($user);die;
		
		//var_dump($user);die;

		/*
		if($user->getServiceCenter() == NULL){
			$session->set('user_service_center', 0);	
		}
		else{
			$session->set('user_service_center', $user->getServiceCenter()->getId());
		}		
		*/	
		
		
						
		$this->getUserAccess();
		$access = $this->userHasAccess();

		//var_dump($access);die;
    	
		if(!$access){
			throw new AccessDeniedException();
		}		
		//DIE;
        $session->set('userLogged', $em->getRepository('BackendAdminBundle:User')->find($user));


		//complex
        if($user->getRole()->getName() == "SUPER ADMIN"){
            /*
            if (!$session->has("sessionComplex")) {
                $session->set("sessionComplex", 0);
            }


            //if (!$session->has("myComplexes")) {
                $complexes = $em->getRepository('BackendAdminBundle:Complex')->findByEnabled(1);
                $arrReturn = array();
                foreach ($complexes as $row) {
                    if($row->getBusiness()){
                        $arrReturn[$row->getBusiness()->getName()."-".$row->getName()] = $row->getId();
                    }

                }

                $session->set("myComplexes", $complexes);
            //}
            */

        }
        else{

            if (!$session->has("sessionComplex")) {
                $complexes = $em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($user->getId());

                foreach ($complexes as $key => $value){
                    $session->set("sessionComplex", $value);
                    break;
                }
            }


            //if (!$session->has("myComplexes")) {
                $complexes = $em->getRepository('BackendAdminBundle:Complex')->getComplexByUser($user->getId());
                $session->set("myComplexes", $complexes);

            //}
        }




    }	
	

	function quitar_tildes($cadena) {
		$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
		$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
		$texto = str_replace($no_permitidas, $permitidas ,$cadena);
		return $texto;
	}	
	
	

		
	public function authUser($username, $password){
	
	        $securityContext = $this->container->get('security.context');
	        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
	            return $securityContext->getToken()->getUser();
	        }
	
	        /* Validate the User */
	        $user_manager = $this->container->get('fos_user.user_manager');
	        $factory = $this->container->get('security.encoder_factory');
	        //$user = $user_manager->loadUserByUsername($username);
	        
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository('BackendAdminBundle:User')->findBy(array("username"=>$username, "enabled" => 1));
		        	        
			if (!$user) {

	            http_response_code(401);
	            echo "Validation Failed";
	            die;		
			}
			else{
				$user = $user[0];
				
				$userID = $user->getId();
				
				$factory = $this->container->get('security.encoder_factory');
				$salt = $user->getSalt();
				$encoder = $factory->getEncoder($user);
				
				
		        if(!$encoder->isPasswordValid($user->getPassword(), $password, $salt)) {
			        	
		            http_response_code(401);
		            echo "Validation Failed";
		            die;						
				}	
				
				/*
	            $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
	            $this->container->get("security.context")->setToken($token); //now the user is logged in
	
	            //now dispatch the login event
	            $request = $this->container->get("request");
	            $event = new InteractiveLoginEvent($request, $token);
	            $this->container->get("event_dispatcher")->dispatch("security.interactive_login", $event);
				 * */
	
	            return true;				        
			
				/*				
		        $encoder = $factory->getEncoder($user);
		        $validated = $encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt());
		        if (!$validated) {
		            http_response_code(400);
		            echo "Validation Failed";
		            die;
		        } else {
		            $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
		            $this->container->get("security.context")->setToken($token); //now the user is logged in
		
		            //now dispatch the login event
		            $request = $this->container->get("request");
		            $event = new InteractiveLoginEvent($request, $token);
		            $this->container->get("event_dispatcher")->dispatch("security.interactive_login", $event);
		
		            return $user;
		        }
				 * 
				 */
	    	}
	}


	public function serviceDataTable($request, $repository, $results, $myItems){


            // Get the parameters from DataTable Ajax Call
            if ($request->getMethod() == 'POST')
            {
                $draw = intval($request->request->get('draw'));
                $start = $request->request->get('start');
                $length = $request->request->get('length');
                $search = $request->request->get('search');
                $orders = $request->request->get('order');
                $columns = $request->request->get('columns');

            }
            else // If the request is not a POST one, die hard
                die;

            // Returned objects are of type Town
            $objects = $results["results"];
            // Get total number of objects
            $total_objects_count = $this->getQueryCount($repository);

            // Get total number of results
            //$selected_objects_count = count($objects);
            // Get total number of filtered data
            $filtered_objects_count = $results["countResult"];

            // Construct response
            $response = '{
            "draw": '.$draw.',
            "recordsTotal": '.$total_objects_count.',
            "recordsFiltered": '.$filtered_objects_count.',
            "data": [';

            $response .= $myItems;


            $response .= ']}';

            // Send all this stuff back to DataTables
            $returnResponse = new JsonResponse();
            $returnResponse->setJson($response);

            return $returnResponse;

    }


    public function blameOnMe($entity, $type = null, $user = null){

        if($user == null){
            $user = $this->getUser();
        }

        $gtmNow = gmdate("Y-m-d H:i:s");

        if($type == "create"){
            $entity->setCreatedBy($user);
            $entity->setCreatedAt(new \DateTime($gtmNow));
        }

        $entity->setUpdatedBy($user);
        $entity->setUpdatedAt(new \DateTime($gtmNow));



    }


    public function getQueryCount($repository){

        //$repository = $this->getDoctrine()->getRepository(Product::class);

        return $repository
            ->createQueryBuilder('object')
            ->select("count(object.id)")
            ->where("object.enabled = 1")
            ->getQuery()
            ->getSingleScalarResult();


    }


    public function dateMysqlToUSA($myDate){
        return date('m-d-Y', strtotime(str_replace('-', '/', $myDate)));
    }


    public function dateUSAToMysql($myDate){
        return date('Y-m-d', strtotime(str_replace('-', '/', $myDate)));
    }


    public function getBCToken(){

        //https://gameboard.space/oauth/v2/token?client_id=8_3e1vtdyx3hkwoo88sgcsog8c8g8ws84o8soo4skockk40owkck&client_secret=46f4hlczw9gkokswc0go8w4wcc4gc0o44wsgw8cs44ck8kckoo&grant_type=client_credentials

        $params = ['headers' => ['Content-Type' => 'application/json']];

        $client = new \GuzzleHttp\Client();
        $response = $client->request("GET", 'https://gameboard.space/oauth/v2/token?client_id=8_3e1vtdyx3hkwoo88sgcsog8c8g8ws84o8soo4skockk40owkck&client_secret=46f4hlczw9gkokswc0go8w4wcc4gc0o44wsgw8cs44ck8kckoo&grant_type=client_credentials');

        //print "<pre>";
        //var_dump($response->getStatusCode()); # 200
        //var_dump($response->getHeaderLine('content-type')); # 'application/json; charset=utf8'

        if($response->getStatusCode() == 200){
            $arrResponse = json_decode($response->getBody(), true);

            $repo = $this->em->getRepository('BackendAdminBundle:AdminSetting')->find(1);

            $repo->setSpaceApiToken($arrResponse["access_token"]);
            $this->em->persist($repo);
            $this->em->flush();

            return true;

        }
        else{
            return false;
        }

        //return $arrResponse["access_token"]; # '{"id": 1420053, "name": "guzzle", ...}'

    }

    public function callBCSpace1($method, $service, $body = null){
        return 1;
    }

    public function callBCSpace($method, $service, $body = null){

        //$token = "ZGE4ZmU1OWFhZTk0MjQzNTY5MzdmZjU0MmRiNmE2NGNiY2ZiMzcxY2MxYmE2OWUxNmFlZGUxZDRiZjMyOGU5ZQZGE4ZmU1OWFhZTk0MjQzNTY5MzdmZjU0MmRiNmE2NGNiY2ZiMzcxY2MxYmE2OWUxNmFlZGUxZDRiZjMyOGU5ZQ";
        $repo = $this->em->getRepository('BackendAdminBundle:AdminSetting')->find(1);
        $token = $repo->getSpaceApiToken();

        $gameboardURL = "https://gameboard.space/api/v1/%s";
//        $gameboardURL = "https://gameboard.space/api/v1/%s.json";

        $client = new \GuzzleHttp\Client();
        if($method == "GET"){
            $params = ['headers' => ['Authorization' => 'Bearer '.$token, 'Accept' => 'application/json', 'Cache-Control' => 'no-cache', 'Content-Type' => 'application/json']];
            $response = $client->request($method, sprintf($gameboardURL, $service), $params);
        }
        else{
            //$myfile = fopen("webdictionary.txt", "w") or die("Unable to open file!");

            $params = [
                'headers' => ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json', 'Cache-Control' => 'no-cache' ],
                'json' => $body,
                //'debug' => $myfile,
//                'config' => [
//                    'curl' => [
//                        'body_as_string' => true,
//                    ],
//                ]
            ];

//            var_dump($params);

            $response = $client->request($method, sprintf($gameboardURL, $service), $params);

            //var_dump($response);

            //fclose($myfile);
            //var_dump($response->getBody()->getContents());
            //die;




//            $params = ['headers' => ['Authorization' => 'Bearer '.$token, 'Accept' => 'application/json', 'Cache-Control' => 'no-cache', 'Content-Type' => 'application/json'],
//                        'json' => $body];

            //var_dump($params);die;

//            $response = $client->post('https://gameboard.space/api/v1/'.$service, $params);

        }


        //print "<pre>";
        //var_dump($response->getStatusCode()); # 200
        //var_dump($response->getHeaderLine('content-type')); # 'application/json; charset=utf8'
        //die;

        $arrResponse = json_decode($response->getBody(), true);
        //$arrResponse =  $arrResponse["recordset"];
        return $arrResponse; # '{"id": 1420053, "name": "guzzle", ...}'


    }


    public function callSendgrid($jsonData, $templateID, $to){

        //$token = "ZGE4ZmU1OWFhZTk0MjQzNTY5MzdmZjU0MmRiNmE2NGNiY2ZiMzcxY2MxYmE2OWUxNmFlZGUxZDRiZjMyOGU5ZQZGE4ZmU1OWFhZTk0MjQzNTY5MzdmZjU0MmRiNmE2NGNiY2ZiMzcxY2MxYmE2OWUxNmFlZGUxZDRiZjMyOGU5ZQ";
        $repo = $this->em->getRepository('BackendAdminBundle:AdminSetting')->find(1);
        $apiKey = $repo->getSendgridApiKey();
        //var_dump($apiKey);die;

        $client = new \GuzzleHttp\Client();

        $body = '{
                       "from":{
                          "email":"admin@gameboard.tech"
                       },
                       "personalizations":[
                          {
                             "to":[
                                        {
                                           "email":"'.$to.'"
                                        }
                                     ],                              
                             "dynamic_template_data":{'.$jsonData.'}
                          }
                       ],
                       "template_id":"'.$templateID.'"
                    }';

        /*
         *
         * "news":
                [
                    {"article": "test", "text": "otro test"},
                    {"article": "test1", "text": "otro test1"}
                ]

         * */

        $body = json_decode($body, true);
        $params = ['headers' => ['Authorization' => 'Bearer '.$apiKey, 'Accept' => 'application/json', 'Cache-Control' => 'no-cache', 'Content-Type' => 'application/json'],
            'json' => $body];

        //var_dump($params);die;

        $response = $client->post('https://api.sendgrid.com/v3/mail/send', $params);

        //var_dump($response->getBody()->getContents());
        //die;


        //print "<pre>";
        //var_dump($response->getStatusCode()); # 200
        //var_dump($response->getHeaderLine('content-type')); # 'application/json; charset=utf8'
        //die;

        $arrResponse = json_decode($response->getBody(), true);
        //$arrResponse =  $arrResponse["recordset"];
        return $arrResponse; # '{"id": 1420053, "name": "guzzle", ...}'



    }


    public function callBCInfo($method, $service, $body = null){

        //var_dump($body);die;
        $token = "sr4ibginrtzlbvpqftsqx2phqimkw8jy4wx1iyvj";


        if($body != null){
            $params =   [
                'headers' => ['Authorization' => 'Bearer '.$token],
                //'form_params' => $body
                'multipart' => $body

            ];

        }
        else{
            $params =   ['headers' => ['Authorization' => 'Bearer '.$token]];

        }

        $client = new \GuzzleHttp\Client(['verify' => false]);

        $response = $client->request($method, 'https://bettercondos.info/?ng=api/v2/'.$service, $params);
        //var_dump($response);die;

        $code = $response->getStatusCode();

        if($code == 200){
            $arrResponse = json_decode($response->getBody(), true);
        }
        else{
            $arrResponse =  array("error" => true);
        }
        //var_dump($response->getHeaderLine('content-type')); # 'application/json; charset=utf8'



        //var_dump($arrResponse); # '{"id": 1420053, "name": "guzzle", ...}'
        //die;
        return $arrResponse;


    }


    public function serviceSendSMS($message, $phone){

        //returns an instance of Vresh\TwilioBundle\Service\TwilioWrapper

        $twilio = $this->get('twilio.api');

        $message = $twilio->account->messages->sendMessage(
            '+12512377418', // From a Twilio number in your account
            $phone, // Text any number
            $message
        );

        //get an instance of \Service_Twilio
        //$otherInstance = $twilio->createInstance('BBBB', 'CCCCC');
        if($message){
            return $message->sid;
        }
        else{
            return false;
        }

    }




    public function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }

    public function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max-1)];
        }

        return $token;
    }


    public function setSessionComplex($complexID){
        $session = new Session();
        $complex = $session->set('sessionComplex', $complexID);

    }


    public function getSessionComplex(){

        $session = new Session();
        $complex = $session->get('sessionComplex');

        return $complex;

    }


    function time_elapsed_A($secs){

        //$date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        //echo $date->format('d-m-Y H:i:s');

        $bit = array(
            'y' => $secs / 31556926 % 12,
            'w' => $secs / 604800 % 52,
            'd' => $secs / 86400 % 7,
            'h' => $secs / 3600 % 24,
            'm' => $secs / 60 % 60,
            's' => $secs % 60
        );

        foreach($bit as $k => $v)
            if($v > 0)$ret[] = $v . $k;

        return join(' ', $ret);
    }


    /**
     * Returns an string clean of UTF8 characters. It will convert them to a similar ASCII character
     * www.unexpectedit.com
     */
    function cleanString($text) {
        // 1) convert á ô => a o

        /*
        $text = preg_replace("/[áàâãªä]/u","a",$text);
        $text = preg_replace("/[ÁÀÂÃÄ]/u","A",$text);
        $text = preg_replace("/[ÍÌÎÏ]/u","I",$text);
        $text = preg_replace("/[íìîï]/u","i",$text);
        $text = preg_replace("/[éèêë]/u","e",$text);
        $text = preg_replace("/[ÉÈÊË]/u","E",$text);
        $text = preg_replace("/[óòôõºö]/u","o",$text);
        $text = preg_replace("/[ÓÒÔÕÖ]/u","O",$text);
        $text = preg_replace("/[úùûü]/u","u",$text);
        $text = preg_replace("/[ÚÙÛÜ]/u","U",$text);
        $text = preg_replace("/[’‘‹›‚]/u","'",$text);
        $text = preg_replace("/[“”«»„]/u",'"',$text);
        $text = str_replace("–","-",$text);
        $text = str_replace(" "," ",$text);
        $text = str_replace("ç","c",$text);
        $text = str_replace("Ç","C",$text);
        $text = str_replace("ñ","n",$text);
        $text = str_replace("Ñ","N",$text);
         * */
        $text = trim($text);
        $text = $this->quitar_tildes($text);

        //2) Translation CP1252. &ndash; => -
        $trans = get_html_translation_table(HTML_ENTITIES);
        $trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
        $trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
        $trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
        $trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
        $trans[chr(134)] = '&dagger;';    // Dagger
        $trans[chr(135)] = '&Dagger;';    // Double Dagger
        $trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
        $trans[chr(137)] = '&permil;';    // Per Mille Sign
        $trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
        $trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
        $trans[chr(140)] = '&OElig;';    // Latin Capital Ligature OE
        $trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
        $trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
        $trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
        $trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
        $trans[chr(149)] = '&bull;';    // Bullet
        $trans[chr(150)] = '&ndash;';    // En Dash
        $trans[chr(151)] = '&mdash;';    // Em Dash
        $trans[chr(152)] = '&tilde;';    // Small Tilde
        $trans[chr(153)] = '&trade;';    // Trade Mark Sign
        $trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
        $trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
        $trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
        $trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
        $trans['euro'] = '&euro;';    // euro currency symbol
        ksort($trans);

        foreach ($trans as $k => $v) {
            $text = str_replace($v, $k, $text);
        }

        // 3) remove <p>, <br/> ...
        $text = strip_tags($text);

        // 4) &amp; => & &quot; => '
        $text = html_entity_decode($text);

        // 5) remove Windows-1252 symbols like "TradeMark", "Euro"...
        //$text = preg_replace('/[^(\x20-\x7F)]*/','', $text);

        /*
        $targets=array('\r\n','\n','\r','\t');
        $results=array(" "," "," ","");
        $text = str_replace($targets,$results,$text);
         * */

        //XML compatible
        /*
        $text = str_replace("&", "and", $text);
        $text = str_replace("<", ".", $text);
        $text = str_replace(">", ".", $text);
        $text = str_replace("\\", "-", $text);
        $text = str_replace("/", "-", $text);
         *
         */

        //cleanString(utf8_encode($val));
        return (($text));
    }



}