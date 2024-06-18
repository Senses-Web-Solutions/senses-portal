<?php

namespace App\Actions\ActionLogs;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\QueueableAction\QueueableAction;
use Illuminate\Support\Str;

class LogUpdatedDiffActionLog
{
    use QueueableAction;

    public function fetchModelTitles($changes)
    {
        $overrides = [
            "department_status_id" => "Status",
            "parent_task_id" => "Task",
            "related_task_id" => "Task",
            "task_type_id" => "TaskType",
            "sla_code_id" => "SlaCode",
            "next_task_route_id" => "TaskRoute",
            "contact_id" => "User",
            "public_contact_id" => "User",
            "secondary_public_contact_id" => "User",
            "sales_contact_id" => "User",
            "project_manager_id" => "User",
            "current_task_step_id" => "AssignmentGroupStep",
            "contractor_company_id" => "Company",
            "venue_id" => "Venue",
            "company_id" => "Company",
            "department_id" => "Department",
            "disposal_site_id" => "Venue",
        ];

        foreach ($changes as $changed => $values) {
            if (str_ends_with($changed, "_id")) {
                $snake_model = str_replace('_id', '', $changed);
                $PascalModel = Str::ucfirst(Str::camel($snake_model));
                $modelName = $overrides[$changed] ?? $PascalModel;

                foreach ($values as $key => $value) {
                    if ($modelName == "User") {
                        $changes[$snake_model][$key] = optional(User::withHidden()->withTrashed()->find($value))->full_name;
                    } else {
                        $changes[$snake_model][$key] = rescue(function() use($modelName, $value) {
                            $modelTitle = "App\\Models\\$modelName"::withHidden()->withTrashed()->find($value)?->title;

                            if ($modelTitle) {
                                return $modelTitle;
                            } else {
                                return "id: " . $value;
                            }
                        }, function() use($value) {
                            return 'id: '. $value;
                        });
                    }
                }
            }
        }

        return $changes;
    }

    public function formatChanges($changes)
    {
        $changes = $this->fetchModelTitles($changes);

        return $changes;
    }
}
