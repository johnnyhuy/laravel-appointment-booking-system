<?php

namespace Tests\Integration;

use Tests\TestCase;

use App\Activity;
use App\Booking;
use App\BusinessOwner;
use App\BusinessTime;
use App\Customer;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon as Time;

class BusinessTimeTest extends TestCase
{
    /**
     * Calls functions before executing tests
     *
     * @return void
     */
    public function setUp()
    {
        // Continue to run the rest of the test
        parent::setUp();

        // Create models
        $this->bo = factory(BusinessOwner::class)->create();
        $this->customer = factory(Customer::class)->create();
        $this->employee = factory(Employee::class)->create();
        $this->activity = factory(Activity::class)->create([
            'duration' => '02:00'
        ]);

        // Build valid business time data
        $this->btData = [
            'start_time' => '06:00',
            'end_time' => '17:00',
            'day' => 'MONDAY',
        ];
    }

    /**
     * Business owner visits business time page
     *
     * @return void
     */
    public function testShowBusinessTimes()
    {
        // Send a GET response
        $response = $this->actingAs($this->bo, 'web_admin')->get('admin/times');

        // Check if page successfully loaded
        $response->assertStatus(200);
    }

    /**
     * Business owner adds business times
     *
     * @return void
     */
    public function testCreateBusinessTime()
    {
        // Send a POST request
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', 'admin/times', $this->btData);

        // Check create business time success message
        $response->assertSessionHas('message', 'Business time has successfully been created.');

        // Check if business time exists in the database
        $this->assertDatabaseHas('business_times', [
            'start_time'    => $this->btData['start_time'] . ':00',
            'end_time'      => $this->btData['end_time'] . ':00',
            'day'           => $this->btData['day'],
        ]);
    }

    /**
     * Business times validation rule testing
     *
     * @return void
     */
    public function testCreateBusinessTimeValidation()
    {
        // User selects nothing in day field
        // Build time data
        $this->btData['day'] = '';

        // Send POST request to admin/times
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/times', $this->btData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The day field is required.'
        ]);


        // User selects nothing in start time field
        // Build time data
        $this->btData['start_time'] = '';

        // Send POST request to admin/times
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/times', $this->btData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The start time field is required.'
        ]);


        // User selects nothing in end time field
        // Build time data
        $this->btData['end_time'] = '';

        // Send POST request to admin/times
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/times', $this->btData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The end time field is required.'
        ]);


        // User inputs invalid day
        // Build time data
        $this->btData['day'] = 'asdasd';

        // Send POST request to admin/times
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/times', $this->btData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The day field must be a valid day (e.g. Monday, Tuesday).'
        ]);


        // User inputs invalid start time
        // Build time data
        $this->btData['start_time'] = '@@@';

        // Send POST request to admin/times
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/times', $this->btData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The start time field must be in the correct 24-hour time format.'
        ]);


        // User inputs invalid end time
        // Build time data
        $this->btData['end_time'] = '@@@';

        // Send POST request to admin/times
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/times', $this->btData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The end time field must be in the correct 24-hour time format.'
        ]);


        // User inputs start time after end time
        // Build time data
        $this->btData['start_time'] = '10:00';
        $this->btData['end_time'] = '09:00';

        // Send POST request to admin/times
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/times', $this->btData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The start time must be before the end time.'
        ]);

        // User inputs start time same as end time
        // Build time data
        $this->btData['start_time'] = '09:00';
        $this->btData['end_time'] = '09:00';

        // Send POST request to admin/times
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/times', $this->btData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The end time must be after the start time.'
        ]);

        // If day has already been taken
        // Create an existing business time
        factory(BusinessTime::class)->create();

        // Build time data
        $this->btData['day'] = 'MONDAY';
        $this->btData['end_time'] = '09:00';
        $this->btData['end_time'] = '17:00';

        // Send POST request to admin/times
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/times', $this->btData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The day has already been taken.'
        ]);
    }

    /**
     * Business owner edit business times
     *
     * @return void
     */
    public function testEditBusinessTime()
    {

    }

    /**
     * Business owner edit business times
     *
     * @return void
     */
    public function testRemoveBusinessTime()
    {

    }
}