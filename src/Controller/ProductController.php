<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

    class ProductController extends AbstractController{

        /**
         * @Route("/products")
         * @Method({"GET"})
         */
        public function index(){

            return $this->render("products/index.html.twig", []);
        }

        /**
         * @Route("/products/")
         */
        public function show(){



        }

        public function create(){
            


        }

        public function update(){
            


        }

        public function delete(){



        }

    }


?>