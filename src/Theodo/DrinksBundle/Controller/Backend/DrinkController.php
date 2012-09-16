<?php

namespace Theodo\DrinksBundle\Controller\Backend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Theodo\DrinksBundle\Controller\Controller;
use Theodo\DrinksBundle\Document\Drink;

/**
 * DrinkController class.
 *
 * @Extra\Route("/drinks", service="drinks.backend_drink_controller")
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class DrinkController extends Controller
{
    /**
     * @Extra\Route("/list", name="backend_drink_list")
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
     * @Extra\Route("/delete/{id}", name="backend_drink_delete")
     * @Extra\ParamConverter("drink", class="Theodo\DrinksBundle\Document\Drink")
     *
     * @param \Theodo\DrinksBundle\Document\Drink $drink
     */
    public function deleteAction(Drink $drink)
    {
        throw new \Exception('Implement this.');
    }

    /**
     * @Extra\Route("/new", name="backend_drink_new")
     * @Extra\Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function newAction(Request $request)
    {
        $drink = new Drink();

        $form = $this->createForm(new \Theodo\DrinksBundle\Form\DrinkType(), $drink);

        if ($request->isMethod('post')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDocumentManager();
                $em->persist($drink);
                $em->flush($drink);

                $request->getSession()->getFlashBag()->add('notice', sprintf('%s created.', $drink->getName()));

                return $this->redirect('backend_drink_list');
            }
        }

        return array('form' => $form->createView());
    }
}
