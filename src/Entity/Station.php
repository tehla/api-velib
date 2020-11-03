<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\StationRepository")
 * @ORM\Table(name="velib.stations")
 */
class Station
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue(strategy="UUID")
	 * @ORM\Column(name="id", type="guid")
	 */
	public $id;

	/**
	 * @ORM\Column(name="code", unique=true)
	 */
	public $code;

	/**
	 * @var
	 * @ORM\Column(type="geometry", options={"geometry_type"="POINT"})
	 */
	public $location;

	/**
	 * @ORM\Column(type="integer")
	 */
	public $capacity;

	/**
	 * @ORM\Column(type="string")
	 */
	public $name;
}
