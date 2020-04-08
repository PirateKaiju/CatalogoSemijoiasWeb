<?php

    namespace App\Controller;

    use App\Entity\Product;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Symfony\Component\Form\Extension\Core\Type\IntegerType; 
    use Symfony\Component\Form\Extension\Core\Type\MoneyType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Validator\Validator\ValidatorInterface;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ProductController extends AbstractController{

        /**
         * @Route("/products", name="product_index")
         * @Method({"GET"})
         */
        public function index(){

            //TODO: PAGINATION

            return $this->render("products/index.html.twig", []);
        }

        /**
             * @Route("/products/{id}", requirements={"id" = "\d+"})
             * @Method({"GET"})
         */
        public function show($id){

            $product = $this->getDoctrine()
                ->getRepository(Product::class)
                ->find($id);

            if(!$product){
                //TODO: HANDLE PRODUCT NOT BEING FOUND
            }

            return $this->render("products/show.html.twig", ['product' => $product]);

        }

        /**
         * @Route("/products/create", methods={"GET"})
         * @IsGranted("ROLE_USER")
         */

        public function create(){
            
            //Maybe create the form here?

            $product = new Product();

            $form = $this->createFormBuilder($product)
                ->setAction($this->generateUrl('store_product'))
                ->setMethod('POST')
                ->add('name', TextType::class)
                ->add('price', MoneyType::class)
                ->add('description', TextType::class)
                ->add('quantity', IntegerType::class)
                ->add('submit', SubmitType::class)
                ->getForm();

            return $this->render("products/create.html.twig", [
                'form' => $form->createView(),
            ]);

        }

        /**
         * @Route("/products/store", methods={"POST"}, name="store_product")
         * @IsGranted("ROLE_USER")
         */

        public function store(Request $request, ValidatorInterface $validator){

            $entityManager = $this->getDoctrine()->getManager();

            $data = $request->get('form');

            $product = new Product();

            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setQuantity($data['quantity']);
            $product->setDescription($data['description']);

            $errors = $validator->validate($product);

            if(count($errors) > 0){
                //TODO: SHOW ERRORS
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');

        }

        public function update(){
            


        }

        public function delete(){



        }

    }


?>