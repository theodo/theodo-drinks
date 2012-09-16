<?php

namespace Theodo\DrinksBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Controller class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
abstract class Controller extends ContainerAware
{
    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->container->get('doctrine.odm.mongodb.document_manager');
    }

    /**
     * @param string $type
     * @param null $data
     * @param array $options
     * @param \Symfony\Component\Form\FormBuilderInterface $parent
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createForm($type = 'form', $data = null, array $options = array(), FormBuilderInterface $parent = null)
    {
        return $this->container
            ->get('form.factory')
            ->create($type, $data, $options, $parent);
    }
}
