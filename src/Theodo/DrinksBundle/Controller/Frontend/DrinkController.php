<?php

namespace Theodo\DrinksBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Theodo\DrinksBundle\Controller\Controller;

/**
 * DrinkController class.
 *
 * @Extra\Route(service="drinks.frontend_drink_controller")
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class DrinkController extends Controller
{
    /**
     * @Extra\Route("/select", name="frontend_drink_select")
     * @Extra\Method({"GET", "POST"})
     * @Extra\Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function selectAction(Request $request)
    {
        $manager = $this->getDocumentManager();
        $user    = $this->container->get('security.context')->getToken()->getUser();

        $drinks = $manager->getRepository('TheodoDrinksBundle:Drink')
            ->findAvailables();

        $drinkChoices = array();
        foreach ($drinks as $drink) {
            $drinkChoices[$drink->getId()] = (string) $drink;
        }

        $form = $this->createForm(
            new \Theodo\DrinksBundle\Form\DrinkSelectionType(),
            array(
                'user_id' => $user->getId()
            ),
            array(
                'drink_choices' => $drinkChoices,
            )
        );

        if ($request->isMethod('post')) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $drink = $manager->getRepository('TheodoDrinksBundle:Drink')
                    ->findOneBy(array('id' => $data['drink_id']));

                $user = $manager->getRepository('TheodoDrinksBundle:User')
                    ->findOneBy(array('id' => $data['user_id']));

                if ('now' == $data['payment']) {
                    list($credit, $debit) = $this->container->get('transaction.factory')->createCompleteTransaction($user, $drink);

                    $manager->persist($credit);
                    $manager->persist($debit);
                } else {
                    $debit = $this->container->get('transaction.factory')->createDebit($user, $drink);

                    $manager->persist($debit);
                }

                $manager->flush();

                return $this->redirect('frontend_user_transactions');
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Extra\Route("/leaderboard", name="frontend_drink_leaderboard")
     * @Extra\Method("GET")
     * @Extra\Template()
     *
     * @return array
     */
    public function leaderboardAction()
    {
        $manager = $this->container->get('doctrine.odm.mongodb.document_manager');

        $users = $manager->getRepository('TheodoDrinksBundle:User')
            ->findBy(array(), array('drinks' => 'desc'));

        return array('users' => $users);
    }
}
