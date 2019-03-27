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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\User;

class UserController extends Controller { 
   /** 
      * @Route("/", name="welcome") 
   */ 
   public function usersAction(Request $request) {
   		$repository = $this->getDoctrine()->getRepository(User::class);
   		$users = $repository->findAll();
   		return $this->render('home.html.twig', [
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
	        ->add('save', SubmitType::class, ['label' => 'Add User', 'attr' => ['class' => 'btn btn-sm btn-primary'],])
	        ->getForm();

    	$form->handleRequest($request);

	    if ($form->isSubmitted() && $form->isValid()) {
	        
	        $user = $form->getData();
	        $password = password_hash($user->getPassword(), PASSWORD_BCRYPT);
            $user->setPassword($password);

	        $entityManager = $this->getDoctrine()->getManager();
	        $entityManager->persist($user);
	        $entityManager->flush();

	        return $this->redirect('/user/' . $user->getId());
	    }

	    return $this->render('add_user.html.twig', [
	        'form' => $form->createView(),
	    ]);

	}

	/** 
      * @Route("/user/{userId}", name="user_desc") 
    */ 
	public function showAction($userId){
		// $session = new Session();
		// $session->start();
		// if ( $session->get('email') == null ) {
		// 	return $this->redirect('/');
		// }

	    $user = $this->getDoctrine()
	        ->getRepository(User::class)
	        ->find($userId);

	    if (empty($user)) {
            $this->addFlash('error', 'User not found');
            return $this->redirectToRoute('users');
        }
        
        return $this->render('detail.html.twig', array(
            'user' => $user
        ));

	    
	}

	    /** 
      * @Route("/login", name="login") 
   */ 
   public function loginAction(Request $request) {

   		$user = new User();
	    $form = $this->createFormBuilder($user)
	        ->add('email', EmailType::class, ['required' => true, 'attr' => ['class' => 'form-control mb-2']])
	        ->add('password', PasswordType::class, ['required' => true, 'attr' => ['class' => 'form-control mb-2']])
	        ->add('save', SubmitType::class, ['label' => 'Login', 'attr' => ['class' => 'btn btn-sm btn-primary'],])
	        ->getForm();

	    $form->handleRequest($request);

	    if ($form->isSubmitted() ) {
	    	// && $form->isValid()
		    $data = $form->getData();

		    // var_dump($data);
		    $email = $data->getEmail();
		    $password = $data->getPassword();


		    $repository = $this->getDoctrine()->getRepository(User::class);
	   		$user = $repository->findOneBy(['email' => $email]);

	   		if (empty($user)) {
	   			return $this->render('login.html.twig', [
	   				'form' => $form->createView(),
			        'last_email' => $email,
			        'error'         => 'Email/Password authentication failed',
			    ]);
	   		}


	   		$password_match = password_verify($password, $user->getPassword());

	   		if ($password_match) {
	   // 			$session = new Session();
				// $session->start();
				// $session->set('email', $email);
	   			return $this->redirect('/user/' . $user->getId());

	   		}else{
	   			return $this->render('login.html.twig', [
	   				'form' => $form->createView(),
			        'last_email' => $email,
			        'error'         => 'Email/Password authentication failed',
			    ]);
	   		}

		}
            


	    return $this->render('login.html.twig', [
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