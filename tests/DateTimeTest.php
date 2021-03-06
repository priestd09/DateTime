<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\ICanBoogie\Time;

use ICanBoogie\DateTime;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		date_default_timezone_set('Europe/Paris');
	}

	public function test_now()
	{
		$d = DateTime::now();
		$now = new \DateTime('now');
		$this->assertEquals(date_default_timezone_get(), $d->zone->name);
		$this->assertEquals($d->year, $now->format('Y'));
		$this->assertEquals($d->month, $now->format('m'));
		$this->assertEquals($d->day, $now->format('d'));
		$this->assertEquals($d->hour, $now->format('H'));
		$this->assertEquals($d->minute, $now->format('i'));
		$this->assertEquals($d->second, $now->format('s'));
	}

	public function test_none()
	{
		$d = DateTime::none();
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals(-1, $d->year);
		$this->assertEquals(11, $d->month);
		$this->assertEquals(30, $d->day);

		$d = DateTime::none('Asia/Tokyo');
		$this->assertEquals('Asia/Tokyo', $d->zone->name);
		$this->assertTrue($d->is_empty);

		$d = DateTime::none(new \DateTimeZone('Asia/Tokyo'));
		$this->assertEquals('Asia/Tokyo', $d->zone->name);
		$this->assertTrue($d->is_empty);
	}

	public function test_from()
	{
		$d = DateTime::from(new \DateTime('2001-01-01 01:01:01', new \DateTimeZone('Europe/Paris')));
		$this->assertEquals('Europe/Paris', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from(new \DateTime('2001-01-01 01:01:01', new \DateTimeZone('Europe/Paris')), new \DateTimeZone('UTC'));
		$this->assertEquals('Europe/Paris', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from(new \DateTime('2001-01-01 01:01:01', new \DateTimeZone('UTC')));
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from(new \DateTime('2001-01-01 01:01:01', new \DateTimeZone('UTC')), new \DateTimeZone('Europe/Paris'));
		$this->assertEquals('UTC', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from('2001-01-01 01:01:01', new \DateTimeZone('UTC'));
		$this->assertEquals('UTC', (string) $d->zone);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from('2001-01-01 01:01:01', new \DateTimeZone('Europe/Paris'));
		$this->assertEquals('Europe/Paris', $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);

		$d = DateTime::from('2001-01-01 01:01:01');
		$this->assertEquals(date_default_timezone_get(), $d->zone->name);
		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);
	}

	public function test_change()
	{
		$d = new DateTime('2001-01-01 01:01:01');

		$this->assertEquals('2009-01-01 01:01:01', $d->change(array('year' => 2009))->as_db);
		$this->assertEquals(2009, $d->year);
		$this->assertEquals('2009-09-01 01:01:01', $d->change(array('month' => 9))->as_db);
		$this->assertEquals(9, $d->month);
		$this->assertEquals('2009-09-09 01:01:01', $d->change(array('day' => 9))->as_db);
		$this->assertEquals(9, $d->day);
		$this->assertEquals('2009-09-09 09:01:01', $d->change(array('hour' => 9))->as_db);
		$this->assertEquals(9, $d->hour);
		$this->assertEquals('2009-09-09 09:09:01', $d->change(array('minute' => 9))->as_db);
		$this->assertEquals(9, $d->minute);
		$this->assertEquals('2009-09-09 09:09:09', $d->change(array('second' => 9))->as_db);
		$this->assertEquals(9, $d->second);

		$d->change
		(
			array
			(
				'year' => 2001,
				'month' => 1,
				'day' => 1,
				'hour' => 1,
				'minute' => 1,
				'second' => 1
			)
		);

		$this->assertEquals('2001-01-01 01:01:01', $d->as_db);
	}

	public function test_change_cascade()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 01:02:00', $d->change(array('minute' => 2), true)->as_db);
		$d = new DateTime('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 02:00:00', $d->change(array('hour' => 2), true)->as_db);
		$d = new DateTime('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 01:02:02', $d->change(array('minute' => 2, 'second' => 2), true)->as_db);
		$d = new DateTime('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 02:02:00', $d->change(array('hour' => 2, 'minute' => 2), true)->as_db);
		$d = new DateTime('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 02:02:02', $d->change(array('hour' => 2, 'minute' => 2, 'second' => 2), true)->as_db);
		# check fix: zero values don't cascade
		$d = new DateTime('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 01:00:00', $d->change(array('minute' => 0), true)->as_db);
		$d = new DateTime('2001-01-01 01:01:01');
		$this->assertEquals('2001-01-01 00:00:00', $d->change(array('hour' => 0), true)->as_db);
	}

	public function test_get_year()
	{
		$d = new DateTime('2012-12-16 15:00:00');
		$this->assertEquals(2012, $d->year);
		$d = new DateTime('0000-12-16 15:00:00');
		$this->assertEquals(0, $d->year);
		$d = new DateTime('9999-12-16 15:00:00');
		$this->assertEquals(9999, $d->year);
	}

	public function test_set_year()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->year = 2009;
		$this->assertEquals('2009-01-01 01:01:01', $d->as_db);
	}

	public function test_get_quarter()
	{
		$d = new DateTime('2012-01-16 15:00:00');
		$this->assertEquals(1, $d->quarter);
		$d = new DateTime('2012-02-16 15:00:00');
		$this->assertEquals(1, $d->quarter);
		$d = new DateTime('2012-03-16 15:00:00');
		$this->assertEquals(1, $d->quarter);
		$d = new DateTime('2012-04-16 15:00:00');
		$this->assertEquals(2, $d->quarter);
		$d = new DateTime('2012-05-16 15:00:00');
		$this->assertEquals(2, $d->quarter);
		$d = new DateTime('2012-06-16 15:00:00');
		$this->assertEquals(2, $d->quarter);
		$d = new DateTime('2012-07-16 15:00:00');
		$this->assertEquals(3, $d->quarter);
		$d = new DateTime('2012-08-16 15:00:00');
		$this->assertEquals(3, $d->quarter);
		$d = new DateTime('2012-09-16 15:00:00');
		$this->assertEquals(3, $d->quarter);
		$d = new DateTime('2012-10-16 15:00:00');
		$this->assertEquals(4, $d->quarter);
		$d = new DateTime('2012-11-16 15:00:00');
		$this->assertEquals(4, $d->quarter);
		$d = new DateTime('2012-12-16 15:00:00');
		$this->assertEquals(4, $d->quarter);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_quarter()
	{
		$d = DateTime::now();
		$d->quarter = true;
	}

	public function test_get_month()
	{
		$d = new DateTime('2012-01-16 15:00:00');
		$this->assertEquals(1, $d->month);
		$d = new DateTime('2012-06-16 15:00:00');
		$this->assertEquals(6, $d->month);
		$d = new DateTime('2012-12-16 15:00:00');
		$this->assertEquals(12, $d->month);
	}

	public function test_set_month()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->month = 9;
		$this->assertEquals('2001-09-01 01:01:01', $d->as_db);
	}

	public function test_get_week()
	{
		$d = new DateTime('2012-01-01 15:00:00');
		$this->assertEquals(52, $d->week);
		$d = new DateTime('2012-01-16 15:00:00');
		$this->assertEquals(3, $d->week);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_week()
	{
		$d = DateTime::now();
		$d->week = true;
	}

	public function test_get_year_day()
	{
		$d = new DateTime('2012-01-01 15:00:00');
		$this->assertEquals(1, $d->year_day);
		$d = new DateTime('2012-12-31 15:00:00');
		$this->assertEquals(366, $d->year_day);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_year_day()
	{
		$d = DateTime::now();
		$d->year_day = true;
	}

	/**
	 * Sunday must be 7, Monday must be 1.
	 */
	public function test_get_weekday()
	{
		$d = new DateTime('2012-12-17 15:00:00');
		$this->assertEquals(1, $d->weekday);
		$d = new DateTime('2012-12-18 15:00:00');
		$this->assertEquals(2, $d->weekday);
		$d = new DateTime('2012-12-19 15:00:00');
		$this->assertEquals(3, $d->weekday);
		$d = new DateTime('2012-12-20 15:00:00');
		$this->assertEquals(4, $d->weekday);
		$d = new DateTime('2012-12-21 15:00:00');
		$this->assertEquals(5, $d->weekday);
		$d = new DateTime('2012-12-22 15:00:00');
		$this->assertEquals(6, $d->weekday);
		$d = new DateTime('2012-12-23 15:00:00');
		$this->assertEquals(7, $d->weekday);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_weekday()
	{
		$d = DateTime::now();
		$d->weekday = true;
	}

	public function test_get_day()
	{
		$d = new DateTime('2012-12-16 15:00:00');
		$this->assertEquals(16, $d->day);
		$d = new DateTime('2012-12-17 15:00:00');
		$this->assertEquals(17, $d->day);
		$d = new DateTime('2013-01-01 03:00:00');
		$this->assertEquals(1, $d->day);
	}

	public function test_set_day()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->day = 9;
		$this->assertEquals('2001-01-09 01:01:01', $d->as_db);
	}

	public function test_get_hour()
	{
		$d = new DateTime('2013-01-01 01:23:45');
		$this->assertEquals(1, $d->hour);
	}

	public function test_set_hour()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->hour = 9;
		$this->assertEquals('2001-01-01 09:01:01', $d->as_db);
	}

	public function test_get_minute()
	{
		$d = new DateTime('2013-01-01 01:23:45');
		$this->assertEquals(23, $d->minute);
	}

	public function test_set_minute()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->minute = 9;
		$this->assertEquals('2001-01-01 01:09:01', $d->as_db);
	}

	public function test_get_second()
	{
		$d = new DateTime('2013-01-01 01:23:45');
		$this->assertEquals(45, $d->second);
	}

	public function test_set_second()
	{
		$d = new DateTime('2001-01-01 01:01:01');
		$d->second = 9;
		$this->assertEquals('2001-01-01 01:01:09', $d->as_db);
	}

	public function test_get_is_monday()
	{
		$d = new DateTime('2013-02-04 21:00:00', 'utc');
		$this->assertTrue($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_monday()
	{
		$d = new DateTime('2013-02-04 21:00:00', 'utc');
		$d->is_monday = true;
	}

	public function test_get_is_tuesday()
	{
		$d = new DateTime('2013-02-05 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertTrue($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_tuesday()
	{
		$d = new DateTime('2013-02-05 21:00:00', 'utc');
		$d->is_tuesday = true;
	}

	public function test_get_is_wednesday()
	{
		$d = new DateTime('2013-02-06 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertTrue($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_wednesday()
	{
		$d = new DateTime('2013-02-06 21:00:00', 'utc');
		$d->is_wednesday = true;
	}

	public function test_get_is_thursday()
	{
		$d = new DateTime('2013-02-07 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertTrue($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_thursday()
	{
		$d = new DateTime('2013-02-07 21:00:00', 'utc');
		$d->is_thursday = true;
	}

	public function test_get_is_friday()
	{
		$d = new DateTime('2013-02-08 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertTrue($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_friday()
	{
		$d = new DateTime('2013-02-08 21:00:00', 'utc');
		$d->is_friday = true;
	}

	public function test_get_is_saturday()
	{
		$d = new DateTime('2013-02-09 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertTrue($d->is_saturday);
		$this->assertFalse($d->is_sunday);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_saturday()
	{
		$d = new DateTime('2013-02-09 21:00:00', 'utc');
		$d->is_saturday = true;
	}

	public function test_get_is_sunday()
	{
		$d = new DateTime('2013-02-10 21:00:00', 'utc');
		$this->assertFalse($d->is_monday);
		$this->assertFalse($d->is_tuesday);
		$this->assertFalse($d->is_wednesday);
		$this->assertFalse($d->is_thursday);
		$this->assertFalse($d->is_friday);
		$this->assertFalse($d->is_saturday);
		$this->assertTrue($d->is_sunday);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_sunday()
	{
		$d = new DateTime('2013-02-10 21:00:00', 'utc');
		$d->is_sunday = true;
	}

	public function test_get_is_today()
	{
		$d = DateTime::now();
		$d->zone = 'Asia/Tokyo';
		$this->assertTrue($d->is_today);
		$this->assertFalse($d->tomorrow->is_today);
		$this->assertFalse($d->yesterday->is_today);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_today()
	{
		$d = DateTime::now();
		$d->is_today = true;
	}

	public function test_get_is_past()
	{
		$d = DateTime::now();
		$d->zone = 'Asia/Tokyo';
		$d->timestamp -= 3600;
		$this->assertTrue($d->is_past);
		$d->timestamp += 7200;
		$this->assertFalse($d->is_past);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_past()
	{
		$d = DateTime::now();
		$d->is_past = true;
	}

	public function test_get_is_future()
	{
		$d = DateTime::now();
		$d->zone = 'Asia/Tokyo';
		$d->timestamp -= 3600;
		$this->assertFalse($d->is_future);
		$d->timestamp += 7200;
		$this->assertTrue($d->is_future);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_future()
	{
		$d = DateTime::now();
		$d->is_future = true;
	}

	public function test_get_is_empty()
	{
		$d = DateTime::none();
		$this->assertTrue($d->is_empty);
		$d = new DateTime('0000-00-00 00:00:00');
		$this->assertTrue($d->is_empty);
		$d = new DateTime('0000-00-00');
		$this->assertTrue($d->is_empty);
		$d = new DateTime('now');
		$this->assertFalse($d->is_empty);
		$d = new DateTime('@0');
		$this->assertFalse($d->is_empty);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_empty()
	{
		DateTime::now()->is_empty = true;
	}

	public function test_get_tomorrow()
	{
		$d = new DateTime('2013-02-10 21:21:21', 'utc');
		$this->assertEquals('2013-02-11 00:00:00', $d->tomorrow->as_db);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_tomorrow()
	{
		$d = DateTime::now();
		$d->tomorrow = true;
	}

	public function test_get_yesterday()
	{
		$d = new DateTime('2013-02-10 21:21:21', 'utc');
		$this->assertEquals('2013-02-09 00:00:00', $d->yesterday->as_db);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_yesterday()
	{
		$d = DateTime::now();
		$d->yesterday = true;
	}

	public function test_get_monday()
	{
		$d = new DateTime('2013-02-05 11:11:11');
		$this->assertEquals('2013-02-04 00:00:00', $d->monday->as_db);
		$d = new DateTime('2013-02-04 11:11:11');
		$this->assertEquals('2013-02-04 00:00:00', $d->monday->as_db);
		$d = new DateTime('2013-02-04 00:00:00');
		$this->assertEquals('2013-02-04 00:00:00', $d->monday->as_db);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_monday()
	{
		$d = DateTime::now();
		$d->monday = true;
	}

	public function test_get_sunday()
	{
		$d = new DateTime('2013-02-05 11:11:11');
		$this->assertEquals('2013-02-10 00:00:00', $d->sunday->as_db);
		$d = new DateTime('2013-02-10 11:11:11');
		$this->assertEquals('2013-02-10 00:00:00', $d->sunday->as_db);
		$d = new DateTime('2013-02-10 00:00:00');
		$this->assertEquals('2013-02-10 00:00:00', $d->sunday->as_db);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_sunday()
	{
		$d = DateTime::now();
		$d->sunday = true;
	}

	public function test_far_past()
	{
		$d = new DateTime('-4712-12-07 12:06:46');
		$this->assertEquals(-4712, $d->year);
		$this->assertEquals('-4712-12-07 12:06:46', $d->as_db);
	}

	public function test_far_future()
	{
		$d = new DateTime('4712-12-07 12:06:46');
		$this->assertEquals(4712, $d->year);
		$this->assertEquals('4712-12-07 12:06:46', $d->as_db);
	}

	public function test_get_utc()
	{
		$d = new DateTime('2013-03-06 18:00:00', 'Europe/Paris');
		$utc = $d->utc;

		$this->assertEquals('UTC', $utc->zone->name);
		$this->assertEquals('2013-03-06 17:00:00', $utc->as_db);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_utc()
	{
		$d = DateTime::now();
		$d->utc = null;
	}

	public function test_get_is_utc()
	{
		$d = DateTime::now();
		$d->zone = 'Asia/Tokyo';
		$this->assertFalse($d->is_utc);
		$d->zone = 'UTC';
		$this->assertTrue($d->is_utc);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_utc()
	{
		$d = DateTime::now();
		$d->is_utc = true;
	}

	public function test_get_local()
	{
		$d = new DateTime('2013-03-06 17:00:00', 'UTC');
		$local = $d->local;

		$this->assertEquals('Europe/Paris', $local->zone->name);
		$this->assertEquals('2013-03-06 18:00:00', $local->as_db);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_local()
	{
		$d = DateTime::now();
		$d->local = true;
	}

	public function test_get_is_local()
	{
		$d = DateTime::now();
		$d->zone = date_default_timezone_get() == 'UTC' ? 'Asia/Tokyo' : 'UTC';
		$this->assertFalse($d->is_local);
		$d->zone = date_default_timezone_get();
		$this->assertTrue($d->is_local);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_local()
	{
		$d = DateTime::now();
		$d->is_local = true;
	}

	public function test_get_is_dst()
	{
		$d = new DateTime('2013-02-03 21:00:00');
		$this->assertFalse($d->is_dst);
		$d = new DateTime('2013-08-03 21:00:00');
		$this->assertTrue($d->is_dst);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_is_dst()
	{
		$d = DateTime::now();
		$d->is_dst = true;
	}

	public function test_format()
	{
		$empty = DateTime::none();
		$this->assertEquals('0000-00-00', $empty->format(DateTime::DATE));
		$this->assertEquals('0000-00-00 00:00:00', $empty->format(DateTime::DB));
		$this->assertEquals('Wed, 30 Nov -0001 00:00:00 +0000', $empty->format(DateTime::RSS));
	}

	/*
	 * Predefined formats
	 */

	public function test_format_as_atom()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::ATOM), $now->format_as_atom());
	}

	public function test_get_as_atom()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::ATOM), $now->as_atom);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_atom()
	{
		$d = DateTime::now();
		$d->as_atom = true;
	}

	public function test_format_as_cookie()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::COOKIE), $now->format_as_cookie());
	}

	public function test_get_as_cookie()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::COOKIE), $now->as_cookie);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_cookie()
	{
		$d = DateTime::now();
		$d->as_cookie = true;
	}

	public function test_format_as_iso8601()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::ISO8601), $now->format_as_iso8601());
	}

	public function test_format_as_iso8601_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'Z', $now->format(DateTime::ISO8601)), $now->format_as_iso8601());
	}

	public function test_get_as_iso8601()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::ISO8601), $now->as_iso8601);
	}

	public function test_as_iso8601_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'Z', $now->format(DateTime::ISO8601)), $now->as_iso8601);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_iso8601()
	{
		$d = DateTime::now();
		$d->as_iso8601 = true;
	}

	public function test_format_as_rfc822()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC822), $now->format_as_rfc822());
	}

	public function test_format_as_rfc822_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTime::RFC822)), $now->format_as_rfc822());
	}

	public function test_get_as_rfc822()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC822), $now->as_rfc822);
	}

	public function test_get_as_rfc822_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTime::RFC822)), $now->as_rfc822);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_rfc822()
	{
		$d = DateTime::now();
		$d->as_rfc822 = true;
	}

	public function test_format_as_rfc850()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC850), $now->format_as_rfc850());
	}

	public function test_get_as_rfc850()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC850), $now->as_rfc850);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_rfc850()
	{
		$d = DateTime::now();
		$d->as_rfc850 = true;
	}

	public function test_format_as_rfc1036()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC1036), $now->format_as_rfc1036());
	}

	public function test_get_as_rfc1036()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC1036), $now->as_rfc1036);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_rfc1036()
	{
		$d = DateTime::now();
		$d->as_rfc1036 = true;
	}

	public function test_format_as_rfc1123()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC1123), $now->format_as_rfc1123());
	}

	public function test_format_as_rfc1123_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTime::RFC1123)), $now->format_as_rfc1123());
	}

	public function test_get_as_rfc1123()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC1123), $now->as_rfc1123);
	}

	public function test_get_as_rfc1123_utc()
	{
		$now = DateTime::now()->utc;
		$this->assertEquals(str_replace('+0000', 'GMT', $now->format(DateTime::RFC1123)), $now->as_rfc1123);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_rfc1123()
	{
		$d = DateTime::now();
		$d->as_rfc1123 = true;
	}

	public function test_format_as_rfc2822()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC2822), $now->format_as_rfc2822());
	}

	public function test_get_as_rfc2822()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC2822), $now->as_rfc2822);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_rfc2822()
	{
		$d = DateTime::now();
		$d->as_rfc2822 = true;
	}

	public function test_format_as_rfc3339()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC3339), $now->format_as_rfc3339());
	}

	public function test_get_as_rfc3339()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RFC3339), $now->as_rfc3339);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_rfc3339()
	{
		$d = DateTime::now();
		$d->as_rfc3339 = true;
	}

	public function test_format_as_rss()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RSS), $now->format_as_rss());
	}

	public function test_get_as_rss()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::RSS), $now->as_rss);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_rss()
	{
		$d = DateTime::now();
		$d->as_rss = true;
	}

	public function test_format_as_w3c()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::W3C), $now->format_as_w3c());
	}

	public function test_get_as_w3c()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::W3C), $now->as_w3c);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_w3c()
	{
		$d = DateTime::now();
		$d->as_w3c = true;
	}

	public function test_format_as_db()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::DB), $now->format_as_db());
		$empty = DateTime::none();
		$this->assertEquals('0000-00-00 00:00:00', $empty->format_as_db());
	}

	public function test_get_as_db()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::DB), $now->as_db);
		$empty = DateTime::none();
		$this->assertEquals('0000-00-00 00:00:00', $empty->as_db);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_db()
	{
		$d = DateTime::now();
		$d->as_db = true;
	}

	public function test_format_as_number()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::NUMBER), $now->format_as_number());
	}

	public function test_get_as_number()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::NUMBER), $now->as_number);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_number()
	{
		$d = DateTime::now();
		$d->as_number = true;
	}

	public function test_format_as_date()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::DATE), $now->format_as_date());
		$empty = DateTime::none();
		$this->assertEquals('0000-00-00', $empty->format_as_date());
	}

	public function test_get_as_date()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::DATE), $now->as_date);
		$empty = DateTime::none();
		$this->assertEquals('0000-00-00', $empty->as_date);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_date()
	{
		$d = DateTime::now();
		$d->as_date = true;
	}

	public function test_format_as_time()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::TIME), $now->format_as_time());
	}

	public function test_get_as_time()
	{
		$now = DateTime::now();
		$this->assertEquals($now->format(DateTime::TIME), $now->as_time);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotWritable
	 */
	public function test_set_as_time()
	{
		$d = DateTime::now();
		$d->as_time = true;
	}
}