<?php

namespace Theodo\DrinksBundle\Controller\Frontend;

use Theodo\DrinksBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * StockController class.
 *
 * @Extra\Route(service="drinks.frontend_stock_controller")
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class StockController extends Controller
{
    /**
     * @Extra\Route("/stocks", name="frontend_stock_list")
     * @Extra\Method("GET")
     * @Extra\Template()
     *
     * @return array
     */
    public function listAction()
    {
        $drinks = $this->getDocumentManager()
            ->getRepository('TheodoDrinksBundle:Drink')
            ->findAll();

        return array('drinks' => $drinks);
    }

    /**
     * @Extra\Route("/stocks/update", name="frontend_stock_update")
     * @Extra\Method({"GET", "POST"})
     * @Extra\Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array|RedirectResponse
     */
    public function updateAction(Request $request)
    {
        $dm = $this->getDocumentManager();

        $users = $dm->getRepository('TheodoDrinksBundle:User')->findAll();
        $drinks = $dm->getRepository('TheodoDrinksBundle:Drink')->findAll();

        $form = $this->createForm(
            new \Theodo\DrinksBundle\Form\RestockingType(),
            null,
            array(
                'drinks' => $drinks,
                'users'  => $users
            )
        );

        if ($request->isMethod('post')) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $restocking = $this->container->get('restocking.factory')->createRestocking($form->getData());

                    $dm->persist($restocking);
                $dm->flush();

                return $this->redirect('frontend_stock_list');
            }
        }

        return array('form' => $form->createView());
    }
}
