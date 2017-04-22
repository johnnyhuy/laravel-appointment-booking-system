<?php

namespace Tests\Integration;

use Tests\TestCase;

use App\BusinessOwner;
use App\Employee;
use App\Activity;
use App\Booking;

use Carbon\Carbon;

class ActivityTest extends TestCase 
{
    /**
     * Activity has many bookings, make 4 bookings and assign it to an activity
     *
     * @return void
     */
    public function testActivityHasManyBookings()
    {
    	// Given activity created
        $activity = factory(Activity::class)->create();
        
        // When activity has 4 bookings
        factory(Booking::class, 4)->create([
            'activity_id' => $activity->id,
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
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

        // Create fake data
        $activity = factory(Activity::class)->make();
        
        // Build activity data
        $activityData = [
            'name' => $activity->name,
            'description' => $activity->description,
            'duration' => $activity->duration,
        ];

        // Send a POST request to admin/activity
        $response = $this->actingAs($bo, 'web_admin')->json('POST', 'admin/activity', $activityData);
        
        // Check if redirected after request
        $response->assertRedirect('admin/activity');

        // Check create activity success message
        $response->assertSessionHas('message', 'Activity has successfully been created.');

        // Check if activity exists in the database
        $this->assertDatabaseHas('activities', [
            'id' => 1,
            'name' => $activity->name,
            'description' => $activity->description,
            'duration' => $activity->duration,
        ]);
    }

    /**
     * Edit an activity from Business Owner view (admin)
     *
     * @return void
     */
    public function testAdminEditActivity()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

        // Create an activity
        $initActivity = factory(Activity::class)->create();
        $editedActivity = factory(Activity::class)->make();

        // Build activity data from edited activity data
        $activityData = [
            'name' => $editedActivity->name,
            'description' => $editedActivity->description,
            'duration' => $editedActivity->duration,
        ];
        
        // Send PUT/PATCH request to admin/activity/{activity}
        $response = $this->actingAs($bo, 'web_admin')->json('PUT', 'admin/activity/' . $initActivity->id, $activityData);

        // Check if redirected after request
        $response->assertRedirect('admin/activity');

        // Check edit activity success message
        $response->assertSessionHas('message', 'Activity has successfully been edited.');

        // Check if activity has been edited in the database
        $this->assertDatabaseHas('activities', [
            'id' => 1,
            'name' => $editedActivity->name,
            'description' => $editedActivity->description,
            'duration' => $editedActivity->duration,
        ]);
    }

    /**
     * Remove an activity from Business Owner view (admin)
     *
     * @return void
     */
    public function testAdminRemoveActivity()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

        // Create an activity
        $activity = factory(Activity::class)->create();

        // Send DELETE request to admin/activity/{activity}
        $response = $this->actingAs($bo, 'web_admin')->json('DELETE', 'admin/activity/' . $activity->id);

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
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();
        
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
        $response = $this->actingAs($bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

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
        $response = $this->actingAs($bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity name may not be greater than 32 characters.'
        ]);

        // User inputs name with special characters
        // Rebuild activity data
        $activityData = [
            'name' => '@ct1v!ty n@me'
        ];

        // Send a POST request to admin/activity
        $response = $this->actingAs($bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

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
        $response = $this->actingAs($bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

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
        $response = $this->actingAs($bo, 'web_admin')->json('POST', 'admin/activity', $activityData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The duration field must be in the correct time format (e.g. 4:00 or 16:30).'
        ]);
    }
}
