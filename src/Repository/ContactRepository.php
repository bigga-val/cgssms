<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }


        public function findContactsByUser($value): array
        {
            $em = $this->getEntityManager();
            $query = $em->createQuery(
                'select c.id, c.telephone, c.nom, c.postnom, c.adresse, c.fonction, g.designation, o.designation organisation 
                    from App\Entity\Contact c, App\Entity\Groupe g, App\Entity\Organisation o 
                    where c.groupe = g.id 
                    and g.organisation = o.id
                    and o.user = :user
            '
            );
            $query->setParameter('user', $value);
            //$query->setParameter('mydate', $mydate->format('Y-m-d'));
            return $query->getResult();
         }
    public function findContacts(): array
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'select c.id, c.telephone, c.nom, c.postnom, c.adresse, c.fonction, g.designation, o.designation organisation 
                    from App\Entity\Contact c, App\Entity\Groupe g, App\Entity\Organisation o 
                    where c.groupe = g.id 
                    and g.organisation = o.id
                    
            '
        );
        //$query->setParameter('user', $value);
        //$query->setParameter('mydate', $mydate->format('Y-m-d'));
        return $query->getResult();
    }




    //    /**
    //     * @return Contact[] Returns an array of Contact objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Contact
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
