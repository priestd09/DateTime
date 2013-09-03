<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

/**
 * Representation of a timezone.
 *
 * <pre>
 * <?php
 *
 * use ICanBoogie\TimeZone;
 *
 * $zone = new TimeZone('Europe/Paris');
 *
 * echo $zone;                     // "Europe/Paris"
 * echo $zone->offset;             // 3600
 * echo $zone->location;           // FR,48.86667,2.33333
 * echo $zone->location->latitude; // 48.86667
 * </pre>
 *
 * @property-read TimeZoneLocation $location Location information for the timezone.
 * @property-read string $name Name of the timezone.
 * @property-read int $offset Timezone offset from UTC.
 */
class TimeZone extends \DateTimeZone
{
	static private $utc_time;
	static private $cache;

	/**
	 * Returns a timezone according to the specified source.
	 *
	 * If the source is already an instance of {@link Zone}, it is returned as is.
	 *
	 * Note: Instances created by the method are shared. That is, equivalent sources yield
	 * the same instance.
	 *
	 * @param mixed $source Source of the timezone.
	 *
	 * @return \ICanBoogie\Time\Zone
	 */
	static public function from($source)
	{
		if ($source instanceof self)
		{
			return $source;
		}
		else if ($source instanceof \DateTimeZone)
		{
			$source = $source->getName();
		}

		if (empty(self::$cache[$source]))
		{
			self::$cache[$source] = new static($source);
		}

		return self::$cache[$source];
	}

	/**
	 * Location of the timezone.
	 *
	 * @var TimeZoneLocation
	 */
	private $location;

	/**
	 * Returns the {@link $location}, {@link $name} and {@link $offset} properties.
	 *
	 * @throws PropertyNotDefined in attempt to get an unsupported  property.
	 */
	public function __get($property)
	{
		switch ($property)
		{
			case 'location':

				if (!$this->location)
				{
					$this->location = TimeZoneLocation::from($this);
				}

				return $this->location;

			case 'name':

				return $this->getName();

			case 'offset':

				$utc_time = self::$utc_time;

				if (!$utc_time)
				{
					self::$utc_time = $utc_time = new \DateTime('now', new \DateTimeZone('utc'));
				}

				return $this->getOffset($utc_time);
		}

		if (class_exists('ICanBoogie\PropertyNotDefined'))
		{
			throw new PropertyNotDefined(array($property, $this));
		}
		else
		{
			throw new \RuntimeException("Property no defined: $property.");
		}
	}

	/**
	 * Returns the name of the timezone.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getName();
	}
}