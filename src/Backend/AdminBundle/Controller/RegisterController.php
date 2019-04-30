<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


use Backend\AdminBundle\Entity\User;
use Backend\AdminBundle\Form\RegisterType;

use Backend\AdminBundle\Lib\MailManager;

/**
 * User controller.
 *
 */
class RegisterController extends Controller
{

    protected $em;
    protected $translator;
    protected $repository;
    private  $renderer;
    private $role;
    private $session;



    // Set up all necessary variable
    protected function initialise()
    {
        $this->session = new Session();
        $this->em = $this->getDoctrine()->getManager();
        $this->repository = $this->em->getRepository('BackendAdminBundle:User');
        $this->translator = $this->get('translator');
        $this->renderer = $this->get('templating');


    }




    /**
     * Creates a new User entity.
     *
     */
    public function newAction(Request $request)
    {

    	$this->initialise();


        $entity = new User();
        $form   = $this->createCreateForm($entity);

        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));

	
        return $this->render('BackendAdminBundle:Register:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'new' => 1,
            'countries' => $countries

        ));
    }


	
    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {

        //var_dump($this->get('mailer'));die;


        //print "<pre>";
        //var_dump($_REQUEST);DIE;

        $this->initialise();

		//print $this->getParameter('avatars_directory');die;
		
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $plainPassword = $form['password']->getData();
        $myRequest = $_REQUEST["register"];
        $email = trim($myRequest["email"]);
        $myRequest["username"] = $email;

        $checkExistence = $this->get('services')->checkExistence($email, 0);


        if ($checkExistence == "") {

            $entity->setPlainPassword($plainPassword);
            //var_dump($entity);die;

            $entity->setUsername($myRequest["username"]);
            $entity->setEnabled(1);

            $objRole = $this->em->getRepository('BackendAdminBundle:Role')->findByName("ADMIN");
            $objRole = $objRole[0];
            $role = $objRole->getName();
            $entity->setRole($objRole);

            $newDate = $this->get('services')->dateUSAToMysql($myRequest["birthdate"]);
            $birthdate = new \DateTime($newDate);
            $entity->setBirthdate($birthdate);

            //link to the business

            //$entity->setBusiness();


            $this->get("services")->blameOnMe($entity, "create");
			
            $this->em->persist($entity);
            $this->em->flush();


			$this->get('services')->flashSuccess($request);
            //return $this->redirect($this->generateUrl(''));


            /////SEND REGISTRATION SUCCESS MAIL
            /// Usuario de acceso a Bettercondos.space
            //Usuario de acceso a bettercondos.info
            //Links
            //Sitio web bettercondos.tech
            //Sitio de soporte y documentación.
            //Datos de contacto
            ///
            //generalTemplateMail($subject, $to, $bodyHtml, $bodyText = null,  $from = null){

            //Admin
            $bodyHtml = $this->translator->trans('label_register_complete_msg')."<br/>";
            $bodyHtml .= "<b>Email:</b>".$entity->getEmail()."<br/><br/>";

            //contact
            $bodyHtml .= $this->translator->trans('label_register_contact');

            $to = $entity->getEmail();
            $message = $this->get('services')->generalTemplateMail($this->translator->trans('label_register_complete'), $to, $bodyHtml);

            //var_dump($message);die;


            //AUTO LOGIN
            $user = $entity;
            //Handle getting or creating the user entity likely with a posted form
            // The third parameter "main" can change according to the name of your firewall in security.yml
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);

            // If the firewall name is not main, then the set value would be instead:
            // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
            $this->get('session')->set('_security_main', serialize($token));

            // Fire the login event manually
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);


            return $this->redirectToRoute('backend_admin_business_new', array('userID' => $entity->getId()));
            /*
            return $this->render('BackendAdminBundle:Register:createBusiness.html.twig', array(
                'user' => $entity,

            ));
            */



        }
        else{

            $this->get('services')->flashCustom($request, $checkExistence);


        }


        /*
        return $this->render('BackendAdminBundle:Register:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),

        ));
        */

        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));


        return $this->render('BackendAdminBundle:Register:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'new' => 1,
            'countries' => $countries

        ));

    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($entity)
    {

        $this->initialise();
        $form = $this->createForm(RegisterType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_user_create'),
            'method' => 'POST',
        ));


        return $form;
    }






}
