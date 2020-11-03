<?php

namespace App\Command;

use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetStationsCommand extends Command implements LoggerAwareInterface
{
	use LoggerAwareTrait;

	const NAME = 'velib:stations:get';
	const DATASET = 'velib-emplacement-des-stations';

	/** @var HttpClientInterface */
	private $httpClient;

	/** @var string */
	private $parisApiUrl;

	/** @var EntityManagerInterface */
	private $em;

	public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $em, string $parisApiUrl )
	{
		parent::__construct(self::NAME);
		$this->httpClient = $httpClient;
		$this->em = $em;
		$this->parisApiUrl = $parisApiUrl;
	}

	protected function configure()
	{
		$this->setName(self::NAME);
		$this->addOption('slice', null, InputOption::VALUE_OPTIONAL, "Interroger toute l'API par tranches de résultats", '10');

	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int|void
	 * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$ui = new SymfonyStyle($input, $output);
		$this->countStations();

		for($start=0 ; $start<=$this->countStations() ; $start += $input->getOption('slice')) {
			foreach ($this->getStations($start, $input->getOption('slice')) as $station) {
				try {
					$this->em->persist($station);
					$ui->writeln(json_encode($station));
					$this->em->flush();
				} catch (ORMException $e) {//Non blocking exception, in case of duplicate key with Station::$code ppty
					$this->logger->debug($e->getMessage());
				}
			}
		}

		exit(0);
	}

	/**
	 * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
	 */
	private function countStations()
	{
		$response = $this->httpClient->request('GET', sprintf('%s?dataset=%s&rows=0', $this->parisApiUrl, self::DATASET))
			->toArray();
		return $response['nhits'];
	}

	/**
	 * @param $start
	 * @param $rows
	 * @return iterable|Station[]
	 * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
	 * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
	 */
	private function getStations(int $start, int $rows): iterable
	{
		$response = $this->httpClient->request(
			'GET',
			sprintf('%s?dataset=%s&start=%s&rows=%s', $this->parisApiUrl, self::DATASET, $start, $rows))
			->toArray();
		foreach ($response['records'] as $record) {
			$station = $this->em->getRepository(Station::class)->findOneBy(['code' => $record['fields']['stationcode']])
				?: new Station();
			$station->code = $record['fields']['stationcode'];
			$station->name = $record['fields']['name'];
			$station->capacity = $record['fields']['capacity'];
			$station->location = sprintf('POINT(%s %s)',
				$record['geometry']['coordinates'][0], $record['geometry']['coordinates'][1]);
			yield $station;
		}

		return [];
	}
}
