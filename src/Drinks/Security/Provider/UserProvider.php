<?php

namespace Drinks\Security\Provider;

use Drinks\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Bundle\OAuthBundle\Security\Core\UserProvider\OAuthUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * UserProvider class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class UserProvider extends OAuthUserProvider
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var String
     */
    protected $documentClass;

    public function __construct(DocumentManager $manager, $documentClass)
    {
        $this->dm = $manager;
        $this->documentClass = $documentClass;
    }
    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     */
    public function loadUserByUsername($username)
    {
        if (false != strpos($username, '@')) {
            if (false == strpos($username, 'theodo.fr')) {
                throw new \Symfony\Component\Security\Core\Exception\AuthenticationException('The mail is not valid.');
            }

            $parts = explode('@', $username);
            $name = reset($parts);
        } else {
            $name = $username;
        }

        $user = $this->dm->getRepository($this->documentClass)->findOneBy(array('name' => $name));

        if (!$user) {
            $user = $this->createUser($name);
        }

        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     *
     * @return Boolean
     */
    public function supportsClass($class)
    {
        return $class == $this->documentClass;
    }

    /**
     * Create, save and return a new user.
     *
     * @param $name
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function createUser($name)
    {
        $user = new $this->documentClass();
        $user->setName($name);

        $this->dm->persist($user);
        $this->dm->flush();

        return $user;
    }
}
