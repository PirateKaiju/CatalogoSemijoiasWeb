<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;

    //use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    use Symfony\Component\Validator\Validator\ValidatorInterface;
    

    use App\Entity\User;
    use Symfony\Component\HttpFoundation\Request;


    class UserController extends AbstractController{

        private $passwordEncoder;

        public function __construct(UserPasswordEncoderInterface $passwordEncoder){
            $this->passwordEncoder = $passwordEncoder;
        }
        
        /**
         * @Route("/dashboard" , methods={"GET"}, name="user_dashboard")
         * 
         */
        public function dashboard(){
            return $this->render('users/dashboard.html.twig');
        }

        /**
         * @Route("/users/register" , methods={"GET"})
         * 
         */

        public function register(){

            $user = new User();

            $form = $this->createFormBuilder($user)
                ->setAction($this->generateUrl("createusr"))
                ->setMethod('POST')
                ->add('email', TextType::class)
                ->add('password', PasswordType::class)
                ->add('submit', SubmitType::class)
                ->getForm();

            return $this->render('users/register.html.twig', [
                'form' => $form->createView(),
            ]);

            //https://symfony.com/doc/current/form/form_customization.html

        }

        /**
         * @Route("/users/create", methods={"POST"}, name="createusr")
         * 
         */

        public function create(Request $request, ValidatorInterface $validator){

            
            $entityManager = $this->getDoctrine()->getManager();

            $user = new User();

            $data = $request->request->get('form');

            //var_dump($data);
            //die();

            $user->setEmail($data['email']);

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $data['password']
            ));

            $errors = $validator->validate($user);

            if(count($errors) > 0){
                return new Response((string) $errors, 400);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');


            //var_dump($request);
        }

    }

?>