<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiTest extends TestCase
{
    use   /*  RefreshDatabase, */  WithFaker;


    // run a sql query to get all the users at this point
    // $sql = "select * from users";
    // $users = DB::select($sql);
    // Log::info('users before delete: ' . json_encode($users));

    /**
     * Test user registration.
     */
    public function test_user_can_register()
    {
        // Login as the seeded user
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'ajay.ajayid.chauhan@gmail.com',
            'password' => 'password',
        ]);
        $token = $loginResponse->json('token');

        // Register a new user using the token
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users', [
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
        Log::info('response response: ' . json_encode($response));
        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'email']);
    }


    /**
     * Test user login.
     */
    public function test_user_can_login()
    {
        // Login as the seeded user
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'ajay.ajayid.chauhan@gmail.com',
            'password' => 'password',
        ]);
        $token = $loginResponse->json('token');
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/users', [
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
        $response = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function sample_project($userID)
    {
        return [
            "name" => $this->faker->unique()->sentence(),
            "description" => $this->faker->paragraphs(3, true),
            "start_date" => "2025-04-28",
            "end_date" => "2025-05-31",
            "status" => "in_progress",
            "created_at" => now(),
            "updated_at" => now(),
            "created_by" => $userID
        ];
    }
    /**
     * Test project CRUD operations.
     */
    public function test_project_crud()
    {
        $password = 'password';
        $user = User::factory()->create(["password" => bcrypt($password)]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);
        $token = $loginResponse->json('token');
        $userID = $loginResponse->json('user')['id'];

        // Create a project
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/projects', $this->sample_project($userID));


        $response->assertStatus(201)
            ->assertJsonStructure(['successFlag', 'data', 'message']);
        dump('✅ Project- creation of a project');

        $projectId = $response->json('data')['id'];

        // Read the project
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/projects/' . $projectId);
        $response->assertStatus(200)
            ->assertJson(['id' => $projectId]);
        dump('✅ Project- Read the project');


        // Update the project
        $UpdatedName =  $this->faker->unique()->word();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/projects/' . $projectId, [
                'name' => $UpdatedName,
                'description' => 'Updated description',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'successFlag' => true,
                'message' => 'Updated Successfully',
                'data' => [
                    'name' => $UpdatedName,
                ],
            ]);
        dump('✅ Project- Updating the project');

        // Delete the project

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/projects/' . $projectId);

        $response->assertStatus(200)
            ->assertJson([
                'successFlag' => true,
                'message' => 'Deleted Successfully',
                'data' => null
            ]);
        dump('✅ Project- Deleting of the project');
    }

    /**
     * Test task CRUD operations.
     */
    public function sample_task($projectId, $userID, $status = 'in_progress')
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'project_id' => $projectId,
            "assigned_to" => $userID,
            "start_date" => "2025-04-28",
            "end_date" => "2025-05-31",
            "status" => $status,
            "priority" => "medium",
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }

    public function test_task_crud()
    {
        $password = 'password';
        $user = User::factory()->create(["password" => bcrypt($password)]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);
        // Log::info('loginResponse: ' . json_encode($loginResponse));

        $token = $loginResponse->json('token');
        // Log::info('token: ' . $token);
        $userID = $loginResponse->json('user')['id'];
        Log::info('userID: ' . $userID);


        // Create a project
        $project = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/projects', $this->sample_project($userID));

        $project->assertStatus(201)
            ->assertJsonStructure(['successFlag', 'data', 'message']);
        dump('✅ Task- creation of a project');

        $projectId = $project->json('data')['id'];


        // Create
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tasks', $this->sample_task($projectId, $userID, 'not_started'));
        dump('✅ Task- creation of a task: not_started');

        dump('✅ Task- creation of a task');
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tasks', $this->sample_task($projectId, $userID, 'on_hold'));

        dump('✅ Task- creation of a task: on_hold');
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tasks', $this->sample_task($projectId, $userID, 'completed'));

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tasks', $this->sample_task($projectId, $userID, 'on_hold'));
        dump('✅ Task- creation of a task: completed');


        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'title', 'description', 'project_id']);
        $taskId = $response->json('id');
        dump('✅ Task- creation of a task: not_started');

        // Read
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/tasks/' . $taskId);
        $response->assertStatus(200)->assertJsonStructure(['id', 'title', 'description', 'project', 'assigned_user']);
        dump('✅ Task- reading the task');


        // Update
        $UpdatedName =  $this->faker->unique()->word();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/tasks/' . $taskId, [
                'title' => $UpdatedName,
                'description' => $this->faker->paragraphs(3, true),
                'status' => 'in_progress',
            ]);
        dump('✅ Task- updating the task');

        $response->assertStatus(200)
            ->assertJson(['title' => $UpdatedName]);

        // Delete
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/tasks/' . $taskId);
        $response->assertStatus(204);
        dump('✅ Task- deleting the task');
    }
}
