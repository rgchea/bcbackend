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

        $myLocale = $this->translator->getLocale();

        $objTerm = $this->em->getRepository('BackendAdminBundle:TermCondition')->find(1);
        if($objTerm){
            $termCondition = $objTerm;
        }
        else{
            $termCondition = "";
        }



        return $this->render('BackendAdminBundle:Register:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'new' => 1,
            'countries' => $countries,
            'termCondition' => $termCondition

        ));
    }


	
    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {

        //var_dump($this->get('mailer'));die;

        /*
        print "<pre>";
        var_dump($_REQUEST);DIE;
        */

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

        $token = $this->get('services')->getBCToken();
        if($token){

            if ($checkExistence == "") {

                $entity->setPlainPassword($plainPassword);
                //var_dump($entity);die;

                $entity->setUsername($myRequest["username"]);
                $entity->setEnabled(0);

                $regToken = sha1(uniqid());
                $entity->setRegisterToken($regToken);

                $entity->setMobilePhone(trim($myRequest["phone"]));
                $objCountry = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findByShortName(trim($myRequest["country"]));
                $objCountry = $objCountry[0];
                $entity->setGeoCountry($objCountry);

                $objRole = $this->em->getRepository('BackendAdminBundle:Role')->findByName("ADMIN");
                $objRole = $objRole[0];
                $role = $objRole->getName();
                $entity->setRole($objRole);

                /*
                $newDate = $this->get('services')->dateUSAToMysql($myRequest["birthdate"]);
                $birthdate = new \DateTime($newDate);
                $entity->setBirthdate($birthdate);
                */

                //link to the business

                //$entity->setBusiness();


                $this->get("services")->blameOnMe($entity, "create");

                $this->em->persist($entity);
                $this->em->flush();

                $myLocale = $this->translator->getLocale();


                $body = array();
                $body['email'] = $entity->getEmail();
                $body['username'] = $entity->getEmail();
                $body['firstName'] = $entity->getName();
                $body['lastName'] = $entity->getName();
                $body['locale'] = $myLocale;

                $createUser = $this->get('services')->callBCSpace("POST", "users", $body);
                if($createUser){
                    //ok
                }


                $this->get('services')->flashSuccess($request);
                //return $this->redirect($this->generateUrl(''));


                /////SEND REGISTRATION MAIL
                /// Usuario de acceso a Bettercondos.space
                //Usuario de acceso a bettercondos.info
                //Links
                //Sitio web bettercondos.tech
                //Sitio de soporte y documentación.
                //Datos de contacto
                ///
                //generalTemplateMail($subject, $to, $bodyHtml, $bodyText = null,  $from = null){

                //Admin
                $bodyHtml = "<b>Email: </b>".$entity->getEmail()."<br/>";
                $bodyHtml .= $this->translator->trans('mail.register_confirm_body')."&nbsp;";


                $baseURL = str_replace($request->getPathInfo(), "", $request->getUri())."/".$myLocale ;
                $href = $baseURL."/business/new/?regtoken=".$regToken;
                $bodyHtml .= "<a href='".$href."'>".$this->translator->trans('mail.register_confirm_click')."</a>";

                //contact
                $bodyHtml .= "<br/><br/>".$this->translator->trans('label_register_contact');

                $to = $entity->getEmail();
                $message = $this->get('services')->generalTemplateMail($this->translator->trans('mail.register_confirm_subject'), $to, $bodyHtml);

                //var_dump($message);die;

                //return $this->redirectToRoute('backend_admin_business_new', array('userID' => $entity->getId()));

                return $this->redirectToRoute('backend_admin_register_confirm', array('email' => $entity->getEmail(),));


            }
            else{

                $this->get('services')->flashCustom($request, $checkExistence);


            }

        }
        else{
            ///SYSTEM LOG GAMIFICATION
            ///
        }

        $this->get('services')->flashWarning($request);


        $countries = $this->em->getRepository('BackendAdminBundle:GeoCountry')->findBy(array("enabled" => 1));
        $myLocale = $this->translator->getLocale();

        $objTerm = $this->em->getRepository('BackendAdminBundle:TermCondition')->find(1);
        if($objTerm){
            $termCondition = $objTerm;
        }
        else{
            $termCondition = "";
        }


        return $this->render('BackendAdminBundle:Register:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'new' => 1,
            'countries' => $countries,
            'termCondition' => $termCondition

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



    public function confirmAction(Request $request)
    {

        $this->initialise();
        $email = trim($_REQUEST["email"]);

        return $this->render('BackendAdminBundle:Register:confirm.html.twig', array('email' => $email));
    }


}
