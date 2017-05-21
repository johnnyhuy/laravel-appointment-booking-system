<?php

namespace Tests\Integration;

use Tests\TestCase;

use App\Activity;
use App\Booking;
use App\BusinessOwner;
use App\Customer;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon;

class ActivityTest extends TestCase
{
    /**
     * Calls functions before executing tests
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

        // Date is tomorrow
        $this->date = Carbon::now()->addDay()->toDateString();
    }

    /**
     * Activity has many bookings, make 4 bookings and assign it to an activity
     *
     * @return void
     */
    public function testActivityHasManyBookings()
    {
        // When activity has 4 bookings
        factory(Booking::class, 4)->create([
            'activity_id' => $this->activity->id,
        ]);

        // Then there exists 4 bookings from activity
        $this->assertCount(4, Activity::first()->bookings);
    }

    /**
     * Create an activity from Business Owner view (admin)
     *
     * @return void
     */
    public function testAdminCreateActivity()
    {
        // Build activity data
        $activityData = [
            'name' => 'Activity Name',
            'description' => 'Description',
            'duration' => '02:00',
        ];

        // Send a POST request to admin/activity
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

        // Check create activity success message
        $response->assertSessionHas('message', 'Activity has successfully been created.');

        // Check if activity exists in the database
        $this->assertDatabaseHas('activities', [
            'name' => $activityData['name'],
            'description' => $activityData['description'],
            'duration' => $activityData['duration'],
        ]);
    }

    /**
     * Edit an activity from Business Owner view (admin)
     *
     * @return void
     */
    public function testAdminEditActivity()
    {
        $initActivity = factory(Activity::class)->create();

        // Build activity data from edited activity data
        $actData = [
            'name' => 'lorem',
            'description' => 'lorem ipsum lorem lorem ipsum lorem',
            'duration' => '04:30',
        ];

        // Send PUT/PATCH request to admin/activity/{activity}
        $response = $this->actingAs($this->bo, 'web_admin')->json('PUT', 'admin/activity/' . $initActivity->id, $actData);

        // Check edit activity success message
        $response->assertSessionHas('message', 'Activity has successfully been edited.');

        // Check if activity has been edited in the database
        $this->assertDatabaseHas('activities', [
            'id' => $initActivity->id,
            'name' => $actData['name'],
            'description' => $actData['description'],
            'duration' => $actData['duration'],
        ]);
    }

    /**
     * Remove an activity from Business Owner view (admin)
     *
     * @return void
     */
    public function testAdminRemoveActivity()
    {
        // Create an activity
        $activity = factory(Activity::class)->create();

        // Send DELETE request to admin/activity/{activity}
        $response = $this->actingAs($this->bo, 'web_admin')->json('DELETE', 'admin/activity/' . $activity->id);

        // Check if redirected after request
        $response->assertRedirect('admin/activity');

        // Check remove activity success message
        $response->assertSessionHas('message', 'Activity has successfully been removed.');

        // Check if activity does not exist in the database
        $this->assertEquals(null, Activity::find($activity->id));
    }

    /**
     * Remove an activity from Business Owner view (admin)
     *
     * @return void
     */
    public function testActivityValidation()
    {
        // Create fake data
        $activity = factory(Activity::class)->make();

        // Build activity data
        $activityData = [
            'name' => $activity->name,
            'description' => $activity->description,
            'duration' => $activity->duration,
        ];


        // User inputs name less than 2 characters
        // Rebuild activity data
        $activityData = [
            'name' => 'a'
        ];

        // Send a POST request to admin/activity
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity name must be at least 2 characters.'
        ]);


        // User inputs name more than 32 characters
        // Rebuild activity data
        $activityData = [
            'name' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ];

        // Send a POST request to admin/activity
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity name may not be greater than 32 characters.'
        ]);

        // There exists an activity name
        $existActivity = factory(Activity::class)->create();

        // User inputs name more than 32 characters
        // Rebuild activity data
        $activityData = [
            'name' => $existActivity->name
        ];

        // Send a POST request to admin/activity
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity name has already been taken.'
        ]);


        // User inputs name with special characters
        // Rebuild activity data
        $activityData = [
            'name' => '@ct1v!ty n@me'
        ];

        // Send a POST request to admin/activity
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity name is invalid, do not use special characters.'
        ]);


        // User inputs description more than 64 characters
        // Rebuild activity data
        $activityData = [
            'description' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ];

        // Send a POST request to admin/activity
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The description may not be greater than 64 characters.'
        ]);

        // User inputs an invalid time format in duration
        // Rebuild activity data
        $activityData = [
            'duration' => 'lorem'
        ];

        // Send a POST request to admin/activity
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The duration field must be in the correct time format (e.g. 4:00 or 16:30).'
        ]);

        // User inputs an invalid time format in duration
        // Rebuild activity data
        $activityData = [
            'duration' => '00:00'
        ];

        // Send a POST request to admin/activity
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The duration field cannot be zero.'
        ]);
    }
}
