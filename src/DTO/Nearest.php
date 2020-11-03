<?php

namespace App\DTO;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\NearestStationController;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "get"= {
 *	 "method"="GET",
 *   "path"="/stations/nearest",
 *     "controller"=NearestStationController::class
 *	 },
 *     "post"={
 * 		"method"="POST",
 *      "path"="/stations/nearest",
 *      "controller"=NearestStationController::class
 *
 *     },
 *	 },
 *     itemOperations={}
 * )
 */
class Nearest
{
	public $lat;

	public $long;

	public $limit;
}
