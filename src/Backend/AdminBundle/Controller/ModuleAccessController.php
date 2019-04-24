<?php

namespace Backend\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Backend\AdminBundle\Entity\ModuleAccess;
use Backend\AdminBundle\Form\ModuleAccessType;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * ModuleAccess controller.
 *
 */
class ModuleAccessController extends Controller
{


    /**
     * Lists all ModuleAccess entities.
     *
     */
    public function indexAction(Request $request)
    {
    	
		$this->get("services")->setVars('moduleAccess');
        $em = $this->getDoctrine()->getManager();
		$session = new Session();
		$roleID = $session->get('userLogged')->getRole()->getId();
		 
		//var_dump($roleID);die;

        //$entitiesAccess = $em->getRepository('BackendAdminBundle:ModuleAccess')->findBy(array('role' => $roleID));
        $entitiesModule = $em->getRepository('BackendAdminBundle:ModuleAccess')->getModules();
        $entitiesAccess = $em->getRepository('BackendAdminBundle:ModuleAccess')->findAll();
		//var_dump($entitiesModule);die;

        $locale = $request->getLocale();
        $entitiesRoles = $em->getRepository('BackendAdminBundle:Role')->findBy(array("endType" => "backend", "enabled" => 1), array("name" => "ASC"));


        return $this->render('BackendAdminBundle:ModuleAccess:index.html.twig', array(
            'entitiesAccess' => $entitiesAccess,
            'entitiesModule' => $entitiesModule,
            'entitiesRole' => $entitiesRoles,
            'locale' => $locale
        ));
    }
    /**
     * Creates a new ModuleAccess entity.
     *
     */
    public function createAction(Request $request)
    {
    	$this->get("services")->setVars('moduleAccess');
		$session = new Session();
		
	    $em = $this->getDoctrine()->getManager();    	
		
		unset($_REQUEST["PHPSESSID"]);
		unset($_REQUEST["REMEMBERME"]);
		//print "<pre>";
		//var_dump($_REQUEST);die;
		$roleID = $session->get('userLogged')->getRole()->getId();
		//var_dump($roleID);die;
		
		$clean = $em->getRepository('BackendAdminBundle:ModuleAccess')->cleanRoleAccess(0);
		
		foreach ($_REQUEST as $key => $value) {
			$splitKey = explode("_", $key);
			$roleID = $splitKey[0]; 
			$moduleID = $splitKey[1];
			
	        $entity = new ModuleAccess();
			
			
			$module = $em->getRepository('BackendAdminBundle:Module')->find($moduleID);
			$entity->setModule($module);
			$role = $em->getRepository('BackendAdminBundle:Role')->find($roleID);
			$entity->setRole($role);
			

	        $em->persist($entity);
	        $em->flush();
			
			
		}

		$this->get('services')->flashSuccess($request);
        return $this->redirect($this->generateUrl('backend_admin_moduleaccess'));

    }


}
