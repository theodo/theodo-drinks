<?php

namespace Theodo\DrinksBundle\Controller\Backend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Theodo\DrinksBundle\Controller\Controller;
use Theodo\DrinksBundle\Document\User;

/**
 * UserController class.
 *
 * @Extra\Route("/users", service="drinks.backend_user_controller")
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class UserController extends Controller
{
    /**
     * @Extra\Route("/list", name="backend_user_list")
     * @Extra\Template()
     *
     * @return array
     */
    public function listAction()
    {
        $users = $this->getDocumentManager()
            ->getRepository('TheodoDrinksBundle:User')
            ->findAll();

        return array('users' => $users);
    }

    /**
     * @Extra\Route("/delete/{id}", name="backend_user_delete")
     * @Extra\ParamConverter("user", class="TheodoDrinksBundle:User")
     *
     * @param \Theodo\DrinksBundle\Document\User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(User $user)
    {
        $dm = $this->getDocumentManager();
        $dm->remove($user);
        $dm->flush();

        return $this->redirect('backend_user_list');
    }
}
