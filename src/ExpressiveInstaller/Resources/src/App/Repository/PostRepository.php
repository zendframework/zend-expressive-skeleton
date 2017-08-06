<?php

namespace App\Repository;

use App\Entity\Post;
use Kocal\Expressive\Database\Doctrine\DoctrineRepository;

/**
 * Class PostRepository
 */
class PostRepository extends DoctrineRepository
{
    // Some methods are already implemented, like all(), first(), last(), ...

    // You can add custom methods

    /**
     * @return Post[]
     */
    public function getTwoLastPosts()
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->setMaxResults(2)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
