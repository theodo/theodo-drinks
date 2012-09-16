<?php

namespace Theodo\DrinksBundle\Controller\Frontend;

use Theodo\DrinksBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * UserController class.
 *
 * @Extra\Route(service="drinks.frontend_user_controller")
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class UserController extends Controller
{
    /**
     * @Extra\Route("/balance", name="frontend_user_balance")
     * @Extra\Method("GET")
     * @Extra\Template()
     *
     * @return array
     */
    public function balanceAction()
    {
        $form = $this->createForm(new \Theodo\DrinksBundle\Form\TransactionRepaymentType());
        $user = $this->container->get('security.context')->getToken()->getUser();

        return array(
            'user' => $user,
            'form' => $form->createView(),
        );
    }

    /**
     * @Extra\Route("/transactions", name="frontend_user_transactions")
     * @Extra\Method("GET")
     * @Extra\Template()
     *
     * @return array
     */
    public function transactionsAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $transactions = $this->getDocumentManager()->getRepository('TheodoDrinksBundle:Transaction')
                        ->findByUser($user);

        return array(
            'user'         => $user,
            'transactions' => $transactions,
        );
    }

    /**
     * @Extra\Route("/repay", name="frontend_user_repay")
     * @Extra\Method({"GET", "POST"})
     * @Extra\Template("TheodoDrinksBundle:Frontend/User:balance.html.twig")
     *
     * @param Request $request
     * @return array
     */
    public function repayAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new \Theodo\DrinksBundle\Form\TransactionRepaymentType());

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $manager = $this->getDocumentManager();

                $credit = $this->container->get('transaction.factory')->createRepayment($user, $data['amount'] * 100);
                $manager->persist($credit);
                $manager->flush();

                return new RedirectResponse($this->container->get('router')->generate('frontend_user_transactions'));
            }
        }

        return array(
            'user' => $user,
            'form' => $form->createView(),
        );
    }

    /**
     * @Extra\Route("/repay", name="frontend_user_repay_all")
     * @Extra\Method({"GET", "POST"})
     * @Extra\Template("TheodoDrinksBundle:Frontend/User:balance.html.twig")
     *
     * @param Request $request
     * @return array
     */
    public function repayAllAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $credit = $this->containe->get('transaction.factory')->createRepayment($user, abs($user->getBalance()));

        $manager = $this->getDocumentManager();
        $manager->persist($credit);
        $manager->flush();

        return new RedirectResponse($this->container->get('router')->generate('frontend_user_transactions'));
    }
}
