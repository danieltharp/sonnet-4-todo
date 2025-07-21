<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->forUser($user)->create();

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
    }

    public function test_task_has_fillable_attributes(): void
    {
        $task = new Task();
        $expectedFillable = ['title', 'description', 'status', 'completed_at'];

        $this->assertEquals($expectedFillable, $task->getFillable());
    }

    public function test_task_casts_completed_at_as_datetime(): void
    {
        $task = Task::factory()->completed()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $task->completed_at);
    }

    public function test_mark_as_complete_method(): void
    {
        $task = Task::factory()->create([
            'status' => 'incomplete',
            'completed_at' => null,
        ]);

        $task->markAsComplete();

        $this->assertEquals('complete', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_mark_as_incomplete_method(): void
    {
        $task = Task::factory()->completed()->create();

        $task->markAsIncomplete();

        $this->assertEquals('incomplete', $task->fresh()->status);
        $this->assertNull($task->fresh()->completed_at);
    }

    public function test_is_complete_method_returns_true_for_complete_task(): void
    {
        $task = Task::factory()->completed()->create();

        $this->assertTrue($task->isComplete());
    }

    public function test_is_complete_method_returns_false_for_incomplete_task(): void
    {
        $task = Task::factory()->create(['status' => 'incomplete']);

        $this->assertFalse($task->isComplete());
    }

    public function test_task_can_be_created_without_description(): void
    {
        $task = Task::factory()->withoutDescription()->create();

        $this->assertNull($task->description);
        $this->assertNotNull($task->title);
    }

    public function test_task_default_status_is_incomplete(): void
    {
        $task = Task::factory()->create();

        $this->assertEquals('incomplete', $task->status);
        $this->assertNull($task->completed_at);
    }

    public function test_task_creation_with_all_attributes(): void
    {
        $user = User::factory()->create();
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'incomplete',
        ];

        $task = Task::factory()->forUser($user)->create($taskData);

        $this->assertEquals($taskData['title'], $task->title);
        $this->assertEquals($taskData['description'], $task->description);
        $this->assertEquals($taskData['status'], $task->status);
        $this->assertEquals($user->id, $task->user_id);
    }
}
