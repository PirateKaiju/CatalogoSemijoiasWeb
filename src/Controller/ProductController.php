<?php

    namespace App\Controller;

    use App\Entity\Product;
use App\Service\FileUploader;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductController extends AbstractController{

        /**
         * @Route("/products", name="product_index")
         * @Method({"GET"})
         */
        public function index(Request $request, $page = 1, $limit = 3){

            //TODO: PAGINATION
            $curPage = $request->get("page");
            //var_dump($curPage);

            if(isset($curPage)){
                $page = $curPage;
            }

            
            $products = $this->getDoctrine()
                ->getManager()
                ->getRepository(Product::class)
                ->createQueryBuilder('p')//@php-ignore                
                ->getQuery();
                //->setFirstResult($limit * ($page - 1))
                //->setMaxResults($limit);
                /*->getRepository(Product::class)
                ->

                var_dump($products);*/
            //var_dump($products);

            //$query = $this->createQueryBuilder()
            
            //var_dump(get_class($products));

            $paginator = new Paginator($products);

            //var_dump(count($paginator));

            $totalItens = count($paginator);
            $totalPages = ceil($totalItens / $limit);

            $paginator->getQuery()
                ->setFirstResult($limit * ($page - 1))
                ->setMaxResults($limit);

            /*var_dump($paginator);
            
            die();*/

            /*foreach($paginator as $item){

                echo(serialize($item)); //FOR CHECKING PURPOSES ONLY

            }*/

            return $this->render("products/index.html.twig", ["paginator" => $paginator, "total_pages" => $totalPages]);
        }

        /**
         * @Route("/products/{id}", methods = {"GET"}, requirements={"id" = "\d+"}, name="show_product")
         * 
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
                
                ->add('image', FileType::class, [
                    'label' => "Image for the product",
                    'mapped' => false,
                    'required' => false,

                ])
                
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

        public function store(Request $request, ValidatorInterface $validator, FileUploader $fileUploader){

            $entityManager = $this->getDoctrine()->getManager();

            $data = $request->get('form');

            $product = new Product();

            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setQuantity($data['quantity']);
            $product->setDescription($data['description']);

            //Receiving image from the form this
            //TODO: Change this to a more formal approach
            //Maybe use a Form Type?
            $form = $this->createFormBuilder($product)
                ->add('image', FileType::class, [
                    'label' => "Image for the product",
                    'mapped' => false,
                    'required' => false,
                ])
                ->getForm();

            $form->handleRequest($request);
            /*$uploadedFile = $form['image']->getData();

            var_dump($uploadedFile);
            die();*/


            $imageFile = $form['image']->getData();

            if($imageFile){
                $imageFilename = $fileUploader->upload($imageFile);
                $product->setImageFilename($imageFilename);
            }


            $errors = $validator->validate($product);

            if(count($errors) > 0){
                //TODO: SHOW ERRORS
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');

        }
        /**
         * @Route("/products/{id}/edit", methods={"GET"})
         * @IsGranted("ROLE_USER")
         */

        public function edit($id){

            $entityManager = $this->getDoctrine()->getManager();

            $product = $entityManager
                ->getRepository(Product::class)
                ->find($id);

            if(!$product){
                //TODO: HANDLE PRODUCT NOT BEING FOUND
            }

            $form = $this->createFormBuilder($product)
                ->setAction($this->generateUrl('update_product', ['id' => $id]))
                ->setMethod('PUT')
                ->add('name', TextType::class)
                ->add('price', MoneyType::class)
                ->add('description', TextType::class)
                ->add('quantity', IntegerType::class)
                ->add('submit', SubmitType::class)
                ->getForm();

            return $this->render("products/edit.html.twig" , [ "form" => $form->createView(), ]);

        }

        /**
         * @Route("/products/{id}", methods={"PUT"}, requirements={"id" = "\d+"}, name="update_product")
         * 
         * @IsGranted("ROLE_USER")
         */

        public function update(Request $request, $id = null){

            //return new Response("Reached!");
            $entityManager = $this->getDoctrine()->getManager();

            $product = $entityManager
                ->getRepository(Product::class)
                ->find($id);

            if(!$product){
                //TODO: HANDLE PRODUCT NOT BEING FOUND
            }

            $data = $request->get('form');

            //die(var_dump($product));

            $product->setName($data["name"]);
            $product->setPrice($data["price"]);
            $product->setQuantity($data['quantity']);
            $product->setDescription($data['description']);

            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        /**
         * @Route("/product/{id}/delete", methods={"DELETE"}, requirements={"id" = "\d+"})
         * @IsGranted("ROLE_USER")
         */

        public function delete($id = null){

            $product = $this->getDoctrine()
                ->getManager()
                ->getRepository(Product::class)
                ->find($id);

            if(!$product){
                //TODO: HANDLE PRODUCT NOT BEING FOUND
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($product);

            $entityManager->flush();

            return $this->redirectToRoute('product_index');

        }

    }


?>