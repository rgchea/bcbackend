<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backend\MyFOSUserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\RequestContext;

use Backend\AdminBundle\Entity\User;
use Backend\AdminBundle\Form\UserType;





/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends Controller
{

    public function registerAction(Request $request)
    {
    	
		//var_dump($_REQUEST);die;
    	//print("aqui");
		//die;
        $em = $this->getDoctrine()->getManager();
		
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        //$formFactory = $this->get('opera_user.registration.form.factory');
		//die;
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        //$user = $userManager->createUser();
        $user = new User();
        $user->setEnabled(true);



        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
		//var_dump($user);die;
        $form->setData($user);
		//var_dump($user);die;
		$form->handleRequest($request);
		//var_dump($form);die;

        
		if(isset($_REQUEST["submit"])){
			$arrForm = $_REQUEST["form"];
			$user->setName($arrForm["name"]);
			$user->setLastName($arrForm["lastName"]);


            $objRole = $em->getRepository('BackendAdminBundle:Role')->findByName("ADMIN");
            $objRole = $objRole[0];
            //var_dump($objRole->getId());die;

            //print "<pre>";
            //var_dump($_REQUEST);DIE;
            //$newDate = date('Y-m-d', strtotime(str_replace('-', '/', $_REQUEST["form"]["birthdate"])));
            $birthdate = new \DateTime($arrForm["birthdate"]);
            $user->setBirthdate($birthdate);
            $user->setRole($objRole);
            $em->persist($user);


            //birthdate is set on RegistrationListener
            //$em = $this->getDoctrine()->getManager();
            //$em->persist($user);
            //$em->flush();

            if ($form->isValid()) {



                //$event = new FormEvent($form, $request);
                //$dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                //$userManager->updateUser($user);
                //$user->setUsername('HOLA');
                //var_dump($user);die;

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                //$dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }


		}		
		
		


		
		


		/*
		$schools = $this->get('doctrine.orm.entity_manager')->getRepository('BackendAdminBundle:School')->findBy(array("enabled" => 1), array("name" => "ASC"));
		$grades = $this->get('doctrine.orm.entity_manager')->getRepository('BackendAdminBundle:Grade')->findAll();
		*/

		
        return $this->render('BackendMyFOSUserBundle:Registration:register_content.html.twig', array(
            'form' => $form->createView(),
            //'schools' => $schools,
            //'grades' => $grades,
        ));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
    	
		
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
		//var_dump($email);die;
        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }
		
        $role = $user->getRole()->getName();

        $name = "Completar perfil";
        $description = "Debes de completar tu perfil para poder usar la plataforma, de lo contrario tu usuario no puede ser encontrado por los clientes y no tendras oportunidades de Transporte. Ir al menú perfil y luego en la pestaña perfil.";


        /*
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
         * */
		

        return $this->render('BackendMyFOSUserBundle:Registration:checkEmail.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction(Request $request, $token)
    {
		
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
		
		
		
        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
    	
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('Usted no tiene acceso a esta sección.');
        }

        return $this->render('BackendMyFOSUserBundle:Registration:confirmed.html.twig', array(
            'user' => $user,
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
        $em = $this->getDoctrine()->getManager();
		//$this->get("services")->setVars('user');
     	//$session = new Session();
		
     	$user = $this->getUser();
     	//$role = $user->getRole();
     	

		/*
		print "<pre>";
		var_dump($session->get("user_service_center"));die;
		 * */
    	
        $form = $this->createForm(UserType::class, $entity, array(
            'action' => $this->generateUrl('backend_admin_user_create'),
            'method' => 'POST',
            //'role' => $role->getName(),

        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }		
	
 
 
}
