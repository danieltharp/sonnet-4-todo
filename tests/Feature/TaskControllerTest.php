<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_unauthenticated_user_cannot_create_task(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Test Task',
            'description' => 'Test Description',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_task(): void
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'user_id' => $this->user->id,
            'status' => 'incomplete',
        ]);
    }

    public function test_task_creation_validation_fails_without_title(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), [
                'description' => 'Task without title',
            ]);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_user_can_view_create_task_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('tasks.create'));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.create');
    }

    public function test_user_can_view_their_own_task_edit_form(): void
    {
        $task = Task::factory()->forUser($this->user)->create();

        $response = $this->actingAs($this->user)
            ->get(route('tasks.edit', $task));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.edit');
        $response->assertViewHas('task', $task);
    }

    public function test_user_cannot_view_another_users_task_edit_form(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->forUser($otherUser)->create();

        $response = $this->actingAs($this->user)
            ->get(route('tasks.edit', $task));

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_own_task(): void
    {
        $task = Task::factory()->forUser($this->user)->create();
        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('tasks.update', $task), $updatedData);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $updatedData['title'],
            'description' => $updatedData['description'],
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_cannot_update_another_users_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->forUser($otherUser)->create();

        $response = $this->actingAs($this->user)
            ->put(route('tasks.update', $task), [
                'title' => 'Unauthorized Update',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_own_task(): void
    {
        $task = Task::factory()->forUser($this->user)->create();

        $response = $this->actingAs($this->user)
            ->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->forUser($otherUser)->create();

        $response = $this->actingAs($this->user)
            ->delete(route('tasks.destroy', $task));

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_can_toggle_task_completion_status(): void
    {
        $task = Task::factory()->forUser($this->user)->create(['status' => 'incomplete']);

        $response = $this->actingAs($this->user)
            ->patch(route('tasks.toggle', $task));

        $response->assertRedirect(route('dashboard'));

        $task->refresh();
        $this->assertEquals('complete', $task->status);
        $this->assertNotNull($task->completed_at);
    }

    public function test_user_can_toggle_completed_task_back_to_incomplete(): void
    {
        $task = Task::factory()->forUser($this->user)->completed()->create();

        $response = $this->actingAs($this->user)
            ->patch(route('tasks.toggle', $task));

        $response->assertRedirect(route('dashboard'));

        $task->refresh();
        $this->assertEquals('incomplete', $task->status);
        $this->assertNull($task->completed_at);
    }

    public function test_dashboard_displays_user_tasks_correctly(): void
    {
        // Create incomplete tasks
        $incompleteTasks = Task::factory()
            ->forUser($this->user)
            ->count(2)
            ->create(['status' => 'incomplete']);

        // Create completed tasks
        $completedTasks = Task::factory()
            ->forUser($this->user)
            ->completed()
            ->count(3)
            ->create();

        // Create tasks for another user (should not appear)
        $otherUser = User::factory()->create();
        Task::factory()->forUser($otherUser)->count(2)->create();

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');

        // Check that only the current user's tasks are displayed
        foreach ($incompleteTasks as $task) {
            $response->assertSee($task->title);
        }

        foreach ($completedTasks as $task) {
            $response->assertSee($task->title);
        }
    }

    public function test_task_validation_title_is_required(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), [
                'description' => 'Description without title',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_task_validation_title_max_length(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), [
                'title' => str_repeat('a', 256), // Over 255 character limit
                'description' => 'Valid description',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_task_can_be_created_without_description(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), [
                'title' => 'Task without description',
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Task without description',
            'description' => null,
            'user_id' => $this->user->id,
        ]);
    }
}
