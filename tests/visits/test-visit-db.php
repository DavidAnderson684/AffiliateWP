<?php
namespace AffWP\Visit\Database;

use AffWP\Tests\UnitTestCase;

/**
 * Tests for Affiliate_WP_Visits_DB class
 *
 * @covers Affiliate_WP_Visits_DB
 * @group database
 * @group visits
 */
class Tests extends UnitTestCase {

	/**
	 * Test affiliates.
	 *
	 * @access public
	 * @var array
	 */
	public static $affiliates = array();

	/**
	 * Test visits.
	 *
	 * @access public
	 * @var array
	 */
	public static $visits = array();

	/**
	 * Set up fixtures once.
	 */
	public static function wpSetUpBeforeClass() {
		self::$affiliates = parent::affwp()->affiliate->create_many( 4 );

		for ( $i = 0; $i <= 3; $i++ ) {
			self::$visits[ $i ] = parent::affwp()->visit->create( array(
				'context' => "foo-{$i}"
			) );
		}

		// Create a visit with an empty context.
		self::$visits[4] = parent::affwp()->visit->create();
	}

	/**
	 * @covers \Affiliate_WP_Visits_DB::get_columns()
	 */
	public function test_get_columns_should_return_all_columns() {
		$columns = array(
			'visit_id'     => '%d',
			'affiliate_id' => '%d',
			'referral_id'  => '%d',
			'url'          => '%s',
			'referrer'     => '%s',
			'campaign'     => '%s',
			'context'      => '%s',
			'ip'           => '%s',
			'date'         => '%s',
		);

		$this->assertEqualSets( $columns, affiliate_wp()->visits->get_columns() );
	}

	/**
	 * @covers Affiliate_WP_Visits_DB::get_visits()
	 */
	public function test_get_visits_should_return_array_of_Visit_objects_if_not_count_query() {
		$results = affiliate_wp()->visits->get_visits();

		// Check a random visit.
		$this->assertInstanceOf( 'AffWP\Visit', $results[0] );
	}

	/**
	 * @covers Affiliate_WP_Visits_DB::get_visits()
	 */
	public function test_get_visits_should_turn_integer_if_count_query() {
		$results = affiliate_wp()->visits->get_visits( array(), $count = true );

		$this->assertTrue( is_numeric( $results ) );
	}

	/**
	 * @covers Affiliate_WP_Visits_DB::get_visits()
	 */
	public function test_get_visits_fields_ids_should_return_an_array_of_ids_only() {
		$results = affiliate_wp()->visits->get_visits( array(
			'fields' => 'ids'
		) );

		$this->assertEqualSets( self::$visits, $results );
	}

	/**
	 * @covers Affiliate_WP_Visits_DB::get_visits()
	 */
	public function test_get_visits_invalid_fields_arg_should_return_regular_Visit_object_results() {
		$visits = array_map( 'affwp_get_visit', self::$visits );

		$results = affiliate_wp()->visits->get_visits( array(
			'fields' => 'foo'
		) );

		$this->assertEqualSets( $visits, $results );

	}

	/**
	 * @covers Affiliate_WP_Visits_DB::get_visits()
	 */
	public function test_get_visits_with_singular_visit_id_should_return_that_visit() {
		$results = affiliate_wp()->visits->get_visits( array(
			'visit_id' => self::$visits[0],
			'fields'   => 'ids',
		) );

		$this->assertSame( self::$visits[0], $results[0] );
	}

	/**
	 * @covers Affiliate_WP_Visits_DB::get_visits()
	 */
	public function test_get_visits_with_multiple_visits_should_return_only_those_visits() {
		$visits = array( self::$visits[1], self::$visits[3] );

		$results = affiliate_wp()->visits->get_visits( array(
			'visit_id' => $visits,
			'fields'   => 'ids',
		) );

		$this->assertEqualSets( $visits, $results );
	}
}
