<?php

namespace App\Controller;

use App\DTO\Nearest;
use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class NearestStationController
{
	/** @var EntityManagerInterface */
	private $em;

	private $stack;

	public function __construct(EntityManagerInterface $em, RequestStack $stack)
	{
		$this->em = $em;
		$this->stack = $stack;
	}

	/**
	 * @param array|Nearest $data
	 * @return Station[]
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function __invoke($data)
	{
		if ($this->stack->getCurrentRequest()->isMethod('GET')) {
			$data = new Nearest();
			$data->lat = $this->stack->getCurrentRequest()->query->get('lat');
			$data->long = $this->stack->getCurrentRequest()->query->get('long');
			$data->limit = $this->stack->getCurrentRequest()->query->get('limit');
		}

		return $this->em->getRepository(Station::class)->nearest($data->lat, $data->long, $data->limit);
	}
}
