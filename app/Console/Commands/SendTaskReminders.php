<?php

namespace App\Console\Commands;

use App\Mail\TaskReminderMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTaskReminders extends Command
{
    protected $signature   = 'tasks:send-reminders';
    protected $description = 'Send email reminders to users with overdue tasks';

    public function handle(): void
    {
        $overdueTasks = Task::with(['assignedTo', 'meeting'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->startOfDay())
            ->whereIn('status', ['open', 'bezig'])
            ->get()
            ->groupBy('assigned_to_user_id');

        if ($overdueTasks->isEmpty()) {
            $this->info('No overdue tasks found.');
            return;
        }

        foreach ($overdueTasks as $userId => $tasks) {
            $user = User::find($userId);

            if (! $user || ! $user->email) {
                continue;
            }

            Mail::to($user->email)->send(new TaskReminderMail($user, $tasks));

            $this->info("Reminder sent to {$user->name} ({$tasks->count()} overdue task(s)).");
        }
    }
}
