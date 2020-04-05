<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

    class ProductController extends AbstractController{

        /**
         * @Route("/products", name="product_index")
         * @Method({"GET"})
         */
        public function index(){

            return $this->render("products/index.html.twig", []);
        }

        /**
             * @Route("/products/{id}"), requeriments={"id" = "\d+"}
             * @Method({"GET"})
         */
        public function show($id){



        }

        public function create(){
            


        }

        public function update(){
            


        }

        public function delete(){



        }

    }


?>