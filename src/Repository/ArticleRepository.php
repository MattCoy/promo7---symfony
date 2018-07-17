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
        $sql = 'SELECT article.id as idArticle, title, content, date_publi, user.*
                     FROM article INNER JOIN user 
                    ON article.user_id = user.id
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
                        ->innerJoin('a.user', 'u')
                        ->addSelect('u')
                        ->andWhere('a.date_publi > :date_post')
                        ->setparameter('date_post', $date_post)
                        ->orderBy('a.date_publi', 'DESC')
                        ->getQuery();
        //retourne un tableau d'objets trouvés
        return $querybuilder->execute();
    }

    /*
    On écrit une méthode qui va faire la jointure entre les articles et les users, de façon a tout récupérer d'un coup
    */
    public function myFindAll(){

        $querybuilder = $this->createQuerybuilder('a')
                        //on fait la jointure
                        //a.user fait référence à la propriété user del'entité article
                        ->innerJoin('a.user', 'u')
                        //on récupère les données de l'utilisateur associé pour éviter les requêtes supplémentaire
                        ->addSelect('u')
                        //on peut trier
                        ->orderBy('a.date_publi', 'DESC')
                        ->getQuery();

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
