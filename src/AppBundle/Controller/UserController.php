<?php

namespace AppBundle\Controller;  
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\User;

class UserController extends Controller { 
   /** 
      * @Route("/", name="users") 
   */ 
   public function usersAction(Request $request) {
   		$repository = $this->getDoctrine()->getRepository(User::class);
   		$users = $repository->findAll();
   		return $this->render('all_user.html.twig', [
	        'users' => $users,
	    ]);
   } 

    /** 
      * @Route("/user/add", name="add") 
   */ 
   public function addAction(Request $request) { 
	   	$user = new User();


	   	$form = $this->createFormBuilder($user)
	        ->add('email', EmailType::class, ['required' => true, 'attr' => ['class' => 'form-control mb-2']])
	        ->add('password', PasswordType::class, ['required' => true, 'attr' => ['class' => 'form-control mb-2']])
	        ->add('save', SubmitType::class, ['label' => 'Create User', 'attr' => ['class' => 'btn btn-primary'],])
	        ->getForm();

    	$form->handleRequest($request);

	    if ($form->isSubmitted() && $form->isValid()) {
	        
	        $user = $form->getData();

	        $entityManager = $this->getDoctrine()->getManager();
	        $entityManager->persist($user);
	        $entityManager->flush();

	        return $this->redirectToRoute('success');
	    }

	    return $this->render('add_user.html.twig', [
	        'form' => $form->createView(),
	    ]);

	}

	/** 
      * @Route("/success", name="success") 
    */ 
	public function success(){
		return $this->render('task_success.html.twig');
	}
}