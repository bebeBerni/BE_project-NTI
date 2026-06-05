<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectApplication;
use App\Services\EmailService;

class SendDeadlineReminders extends Command
{
    protected $signature = 'deadline:reminders';

    protected $description = 'Send project deadline reminder emails';

    public function handle(EmailService $emailService): int
    {
        $daysToCheck = [3, 1];

        foreach ($daysToCheck as $daysRemaining) {

            $projects = Project::whereDate(
                'deadline',
                now()->addDays($daysRemaining)->toDateString()
            )->get();

            foreach ($projects as $project) {

                $applications = ProjectApplication::where(
                    'project_id',
                    $project->id
                )->get();

                foreach ($applications as $application) {

                    $user = User::find(
                        $application->submitted_by_user_id
                    );

                    if ($user) {

                        $emailService->sendDeadlineReminderEmail(
                            $user,
                            $project,
                            $daysRemaining
                        );
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
