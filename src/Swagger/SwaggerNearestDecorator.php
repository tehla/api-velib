<?php

namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SwaggerNearestDecorator implements NormalizerInterface
{
	private $decorated;

	public function __construct(NormalizerInterface $decorated)
	{
		$this->decorated = $decorated;
	}

	/**
	 * @inheritDoc
	 */
	public function normalize($object, string $format = null, array $context = [])
	{
		$docs = $this->decorated->normalize($object, $format, $context);

		$customDefinitions = [
			[
				'name' => 'lat',
				'description' => 'Latitude',
				'in' => 'query',
				'required' => 'true',
				'type' => 'float'
			],[
				'name' => 'long',
				'description' => 'Longitude',
				'in' => 'query',
				'required' => 'true',
				'type' => 'float'
			],[
				'name' => 'limit',
				'description' => 'Limite de recherche.',
				'in' => 'query',
				'required' => 'true',
				'type' => 'integer'
			]
		];
		foreach ($customDefinitions as $definition) {
			$docs['paths']['/api/stations/nearest']['get']['parameters'][] = $definition;
		}

		return $docs;
	}

	/**
	 * @inheritDoc
	 */
	public function supportsNormalization($data, string $format = null)
	{
		return $this->decorated->supportsNormalization($data, $format);
	}
}