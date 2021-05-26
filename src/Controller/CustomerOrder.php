<?php

namespace WebWMS\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Entity\CustomerOrder AS CustomerOrders;
use WebWMS\Entity\CustomerOrderPos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use WebWMS\Form\CustomerOrderType;
use WebWMS\Entity\Article;

class CustomerOrder extends AbstractController
{
    /**
     * @Route("/customer_order_ajax", name="customer_order_ajax")
     */
    public function getAllCustomerOrders()
    {
        return $this->getDoctrine()->getRepository(CustomerOrders::class)->getAllCustomerOrders();
    }

    /**
     * @Route("/customer_order_pos_ajax", name="customer_order_pos_ajax")
     */
    public function getAllCustomerOrdersPos()
    {
        return $this->getDoctrine()->getRepository(CustomerOrderPos::class)->getAllCustomerOrderPos();
    }

    /**
     * @Route("/auftrag", name="customer_orders")
     */
    public function index(): Response
    {
        return $this->render('customer_order/index.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Übersicht Aufträge',
            'customer_order' => $this->getAllCustomerOrders(),
            'customer_order_pos' => $this->getAllCustomerOrdersPos()
        ]);
    }

    /*public function addNewCustomerOrder(Request $request) {

        $customerOrder = new CustomerOrders();

        $form = $this->createFormBuilder($customerOrder)
            ->add('customer_order_nr', TextType::class)
            ->add('customer_order_reference', TextType::class)
            ->add('Save',SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg btn-block'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customerOrder);
            $em->flush();
            return new Response('Customer Order added successfuly');
        }

        return $this->render('customer_order/add_customer_order.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Auftrag anlegen',
            'article' => $this->getAllArticleAjax(),
            'lastId' => $this->getLastInsertId()[0],
            'form' => $form->createView()
        ]);
    }*/


    /**
     * @Route("/auftrag_anlegen", name="new_customer_order")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addNewCustomerOrder(EntityManagerInterface $em, Request $request)
    {
        //dd($request);

        $form = $this->createForm(CustomerOrderType::class);
        $form->handleRequest($request);
        //dd($form->getData());
        if ($form->isSubmitted() && $form->isValid()) {

            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            /** @var CustomerOrders $customerOrder */
            $customerOrder = $form->getData();
            //dd($customerOrder);

            //$customerOrder = new CustomerOrders();
            //$customerOrderPos = new CustomerOrderPos();
            /*$customerOrder->setCustomerId($data['customer_order[customer_id]']);
            $customerOrder->setCustomerOrderDate($data['customer_order[customer_order_date]']);
            $customerOrder->setCustomerOrderId($data['customer_order[customer_order_id]']);
            $customerOrder->setCustomerOrderNr($data['customer_order[customer_order_nr]']);
            $customerOrder->setCustomerOrderOrderDate($data['customer_order[customer_order_order_date]']);
            $customerOrder->setCustomerOrderReference($data['customer_order[customer_order_reference]']);*/
            $customerOrder->setUsrId((int)$this->getUser());

            //$em = $this->getDoctrine()->getManager();

            $em->persist($customerOrder);
            //$em->persist($customerOrderPos);
            $em->flush();
            //return new Response('News added successfuly');

            $this->addFlash('success', 'Der Auftrag und die Position(en) wurden erfolgreich angelegt.');

            return $this->redirectToRoute('new_customer_order');
        }
        return $this->render('customer_order/add_customer_order.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Auftrag anlegen',
            'article' => $this->getAllArticleAjax(),
            'lastId' => $this->getLastCustomerOrderId()[0],
            'customerForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/article_order_ajax", name="article_order_ajax")
     */
    public function getAllArticleAjax()
    {
        return $this->getDoctrine()->getRepository(Article::class)->getArticle();
    }

    /**
     * Get last customer order id
     * @return object[]
     */
    public function getLastCustomerOrderId() {
        $customerOrderRepository =$this->getDoctrine()->getRepository(CustomerOrders::class);

        return $customerOrderRepository->findBy(array(),array('customer_order_id'=>'DESC'),1,0);
    }
}
