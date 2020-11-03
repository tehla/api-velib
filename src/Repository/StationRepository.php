<?php

namespace App\Repository;

use App\Entity\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StationRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Station::class);
	}

	/**
	 * @param float $lat
	 * @param float $long
	 * @param $limit
	 * @return Station[]
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function nearest(float $lat, float $long, $limit): iterable
	{
		$connection = $this->_em->getConnection();
		$sql = "SELECT id FROM velib.stations ORDER BY location::point <-> POINT(?,?) LIMIT ?";
		$ids = $connection->fetchFirstColumn($sql, [$lat, $long, $limit]);
		return $this->findBy(['id' => $ids]);
	}
}
