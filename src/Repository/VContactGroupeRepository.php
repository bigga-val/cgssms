<?php

namespace App\Repository;

use App\Entity\VContactGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VContactGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VContactGroupe::class);
    }

    /**
     * Récupère tous les contacts d’un utilisateur donné
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('v.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche full-text sur nom, postnom, téléphone, fonction, adresse
     */
    public function search(int $userId, string $value): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.user = :userId')
            ->andWhere('v.nom LIKE :val
                        OR v.postnom LIKE :val
                        OR v.telephone LIKE :val
                        OR v.fonction LIKE :val
                        OR v.adresse LIKE :val')
            ->setParameter('userId', $userId)
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('v.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Filtrer seulement par organisation
     */
    public function findByOrganisation(int $userId, string $organisation): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.user = :userId')
            ->andWhere('v.organisation = :org')
            ->setParameter('userId', $userId)
            ->setParameter('org', $organisation)
            ->orderBy('v.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Filtrer par groupe (groupe = designation du groupe)
     */
    public function findByGroupe(int $userId, string $groupe): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.user = :userId')
            ->andWhere('v.groupe = :grp')
            ->setParameter('userId', $userId)
            ->setParameter('grp', $groupe)
            ->orderBy('v.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }



}
