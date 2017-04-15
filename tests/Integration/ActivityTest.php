<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Employee;
use App\Activity;
use App\Booking;

use Carbon\Carbon;

class ActivityTest extends TestCase 
{
	// Rollback database actions once test is complete with this trait
    use DatabaseTransactions;

    // Test without middleware
    use WithoutMiddleware;

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
        // Create fake data
        $activity = factory(Activity::class)->make();
        
        // Build activity data
        $activityData = [
            'name' => $activity->name,
            'description' => $activity->description,
            'duration' => $activity->duration,
        ];

        // Send a POST request to /admin/activity
        $response = $this->json('POST', '/admin/activity', $activityData);

        // Check create activity success message
        $response->assertSessionHas('message', 'Activity has successfully been created.');

        // Check if redirected after request
        $response->assertRedirect('/admin/activity');

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
        // Create an activity
        $initActivity = factory(Activity::class)->create();
        $editedActivity = factory(Activity::class)->make();

        // Build activity data from edited activity data
        $activityData = [
            'name' => $editedActivity->name,
            'description' => $editedActivity->description,
            'duration' => $editedActivity->duration,
        ];
        
        // Send PUT/PATCH request to /admin/activity/{activity}
        $response = $this->json('PUT', '/admin/activity/' . Activity::find($initActivity->id), $activityData);

        // Check edit activity success message
        $response->assertSessionHas('message', 'Activity has successfully been edited.');

        // Check if redirected after request
        $response->assertRedirect('/admin/activity');

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
        // Create an activity

        // Send DELETE request to /admin/activity/{activity}

        // Check remove activity success message

        // Check if activity does not exist in the database
    }
}
