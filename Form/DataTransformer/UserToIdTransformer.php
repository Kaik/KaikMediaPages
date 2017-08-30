<?php

/**
 * KaikMedia PagesModule
 *
 * @package    KaikmediaPagesModule
 * @author     Kaik <contact@kaikmedia.com>
 * @copyright  KaikMedia
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @link       https://github.com/Kaik/KaikMediaPages.git
 */

namespace Kaikmedia\PagesModule\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class UserToIdTransformer implements DataTransformerInterface
{
    /**
     *
     * @var ObjectManager
     */
    private $om;

    /**
     *
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (user) to a string (id).
     *
     * @param Customer|null $user
     * @return string
     */
    public function transform($user)
    {
        if (null === $user) {
            return "";
        }

        return $user->getUname();
    }

    /**
     * Transforms a string (uname) to an object (user).
     *
     * @param string $uname
     * @return User|null
     * @throws TransformationFailedException if object (user) is not found.
     */
    public function reverseTransform($uname)
    {
        if (! $uname) {
            return null;
        }

        $user = $this->om->getRepository('Zikula\UsersModule\Entity\UserEntity')->findOneBy([
            'uname' => $uname
        ]);

        if (null === $user) {
            throw new TransformationFailedException(sprintf('A user with uid "%s" does not exist!', $uname));
        }

        return $user;
    }
}