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
    use   RefreshDatabase,    WithFaker;

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

    public function sample_discussion($projectId, $userId)
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(2, true),
            'project_id' => $projectId,
            'user_id' => $userId,
        ];
    }

    public function test_discussion_crud()
    {
        $password = 'password';
        $user = User::factory()->create(["password" => bcrypt($password)]);
        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);
        $token = $loginResponse->json('token');
        $userID = $loginResponse->json('user')['id'];

        // Create a project for the discussion
        $projectResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/projects', $this->sample_project($userID));
        $projectResponse->assertStatus(201);
        $projectId = $projectResponse->json('data')['id'];

        // Create a discussion
        $discussionData = $this->sample_discussion($projectId, $userID);
        $createResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/discussions', $discussionData);
        $createResponse->assertStatus(201)
            ->assertJsonStructure(['successFlag', 'message', 'data']);
        $discussionId = $createResponse->json('data')['id'];
        dump('✅ Discussion- creation of a discussion');

        // Read the discussion
        $readResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/discussions/' . $discussionId);
        $readResponse->assertStatus(200)
            ->assertJson(['id' => $discussionId]);
        dump('✅ Discussion- reading the discussion');

        // Update the discussion
        $updatedTitle = $this->faker->sentence();
        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/discussions/' . $discussionId, [
                'title' => $updatedTitle,
                'content' => $this->faker->paragraphs(2, true)
            ]);
        $updateResponse->assertStatus(200)
            ->assertJson([
                'successFlag' => true,
                'message' => 'Updated Successfully',
                'data' => [
                    'title' => $updatedTitle,
                ],
            ]);
        dump('✅ Discussion- updating the discussion');

        // Delete the discussion
        $deleteResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/discussions/' . $discussionId);
        $deleteResponse->assertStatus(200)
            ->assertJson([
                'successFlag' => true,
                'message' => 'Deleted Successfully',
                'data' => null
            ]);
        dump('✅ Discussion- deleting the discussion');
    }

    public function sample_bug_ticket($projectId, $userId, $priority = 'medium', $status = 'open')
    {
        return [
            'title' => $this->faker->streetName(),
            'description' => $this->faker->streetName(),
            'status' => $status,
            'priority' => $priority,
            'project_id' => $projectId,
            'assigned_to' => $userId,
            'screenshot' => null,
        ];
    }

    public function test_bug_ticket_crud()
    {
        $password = 'password';
        $user = User::factory()->create(["password" => bcrypt($password)]);
        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);
        $token = $loginResponse->json('token');
        $userID = $loginResponse->json('user')['id'];

        // Create a project for the bug ticket
        $projectResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/projects', $this->sample_project($userID));
        $projectResponse->assertStatus(201);
        $projectId = $projectResponse->json('data')['id'];

        // Create a bug ticket
        $bugData = $this->sample_bug_ticket($projectId, $userID);
        $createResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/bugs', $bugData);
        $createResponse->assertStatus(201)
            ->assertJsonStructure(['id', 'title', 'description', 'status', 'priority', 'project_id', 'assigned_to']);
        $bugId = $createResponse->json('id');
        dump('✅ BugTicket- creation of a bug ticket');

        // Read the bug ticket
        $readResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/bugs/' . $bugId);
        $readResponse->assertStatus(200)
            ->assertJson(['id' => $bugId]);
        dump('✅ BugTicket- reading the bug ticket');

        // Update the bug ticket
        $updatedTitle = $this->faker->sentence();
        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/bugs/' . $bugId, [
                'title' => $updatedTitle,
                'description' => $this->faker->streetName(),
                'priority' => 'high',
                'status' => 'closed',
            ]);
        $updateResponse->assertStatus(200)
            ->assertJson([
                'id' => $bugId,
                'title' => $updatedTitle,
                'priority' => 'high',
                'status' => 'closed',
            ]);
        dump('✅ BugTicket- updating the bug ticket');

        // Delete the bug ticket
        $deleteResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/bugs/' . $bugId);
        $deleteResponse->assertStatus(204);
        dump('✅ BugTicket- deleting the bug ticket');
    }
}
