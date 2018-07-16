<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /*
    Méthode qui va récupérer les articles dont la date de publication est plus récente que la date donnée en paramètre
    On peut le faire en SQL
    Retourne un tableau de tableaux
    */
    public function findAllPostedAfter($date_post): array
    {
        //on récupère l'objet pdo qui permet de se connecter à la base => le résultat du try catch
        $connexion = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM article 
                    WHERE date_publi > :date_post 
                    ORDER BY date_publi DESC';
        $select = $connexion->prepare($sql);
        $select->bindValue(':date_post', $date_post);
        $select->execute();
        //on renvoie un tableau de tableau
        return $select->fetchAll();

    }

    /*
    Méthode qui fait exactement la même chose que findAllpostedAfter(), mais en objet
    Cette méthode va retourner un tableau d'ojets Article
    */

    public function findAllpostedAfter2($date_post): array
    {
        $querybuilder = $this->createQueryBuilder('a')
                        ->andWhere('a.date_publi > :date_post')
                        ->setparameter('date_post', $date_post)
                        ->orderBy('a.date_publi', 'DESC')
                        ->getQuery();
        //retourne un tableau d'objets trouvés
        return $querybuilder->execute();
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
