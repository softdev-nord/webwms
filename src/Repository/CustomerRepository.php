<?php

namespace WebWMS\Repository;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    // Abfrage aller Kunden
    /**
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCustomers()
    {
        $connection = $this->getEntityManager()->getConnection();

        $numOfBoxKd = !empty($_GET['numOfBoxKd']) ? $_GET['numOfBoxKd'] : '';
        $nameKd = !empty($_GET['customer_nr']) ? strtolower(trim($_GET['customer_nr'])) : '';

        $boxName = 'customer_nr';

        switch ($numOfBoxKd)
        {
            case 1:
                $boxName = 'customer_name';
                break;
            case 2:
                $boxName = 'customer_address_addition';
                break;
            case 3:
                $boxName = 'customer_address_street';
                break;
            case 4:
                $boxName = 'customer_address_street_nr';
                break;
            case 5:
                $boxName = 'customer_country_code';
                break;
            case 6:
                $boxName = 'customer_zip_code';
                break;
            case 7:
                $boxName = 'customer_city';
                break;
            case 8:
                $boxName = 'id';
                break;
        }

        $data = [];
        if (isset($_GET['name_kd']))
        {
            $nameKd = strtolower(trim($_GET['name_kd']));

            $sqlKd = "SELECT customer_nr, customer_name, customer_address_addition, 
                        customer_address_street, customer_address_street_nr, customer_country_code, 
                        customer_zip_code, customer_city, id FROM customer WHERE LOWER($boxName) LIKE '" . $nameKd . "%'";
            $stmt = $connection->query($sqlKd);

            while ($rowKd = $stmt->fetch())
            {
                $nameKd = $rowKd['customer_nr'] . '|'. $rowKd['customer_name'] . '|' . $rowKd['customer_address_addition'] . '|' . $rowKd['customer_address_street'] . '|' . $rowKd['customer_address_street_nr'] . '|' . $rowKd['customer_country_code'] . '|' . $rowKd['customer_zip_code'] . '|' . $rowKd['customer_city'] . '|' . $rowKd['id'];
                array_push($data, $nameKd);
            }
        }
        return new JsonResponse($data);

    }
}
