<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Invitation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms an Invitation to an invitation code.
 */
class InvitationToCodeTransformer implements DataTransformerInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Invitation) {
            throw new UnexpectedTypeException($value, 'AppBundle\Entity\Invitation');
        }

        return $value->getCode();
    }

    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $invitation = $this->entityManager
                           ->getRepository('AppBundle\Entity\Invitation')
                           ->findOneBy(array(
                                'code' => $value
                            ));
        $user = $this->entityManager
                     ->getRepository('AppBundle\Entity\User')
                     ->findOneBy(array(
                        'invitation' => $invitation
                     )); 
        if($user){
            return null;
        }
        return $invitation;
    }
}
