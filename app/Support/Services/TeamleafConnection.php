<?php

namespace App\Support\Services;

use Exception;
use App\Models\Tag;
use App\Models\File;
use App\Models\Form;
use App\Models\Task;
use App\Models\User;
use App\Enums\Format;
use App\Models\Asset;
use App\Models\Venue;
use App\Models\Status;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Document;
use App\Models\TagGroup;
use App\Models\TaskRule;
use App\Models\TaskStep;
use App\Models\TaskType;
use App\Models\WorkType;
use App\Models\FloorPlan;
use App\Models\UtilityType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\FormStructure;
use App\Models\TaskRuleGroup;
use Shapefile\Geometry\Point;
use App\Models\TaskContractor;
use App\Models\AssignmentGroup;
use App\Models\ExternalService;
use App\Models\TaskRequirement;
use App\Models\ClientApplication;
use App\Models\ContractorPublish;
use App\Enums\ExternalServiceType;
use Illuminate\Support\Facades\DB;
use App\Models\AssignmentGroupStep;
use App\Models\InfrastructureAsset;
use Illuminate\Support\Facades\Http;
use App\Models\ClientApplicationType;
use App\Models\InfrastructureAssetType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\AssignmentGroupRuleGroup;
use App\Models\AssignmentGroupLoginStatus;
use App\Models\AssignmentGroupRequirement;
use App\Models\InfrastructureAssetCategory;
use App\Interfaces\Services\ExternalConnection;
use Illuminate\Database\Eloquent\Relations\Relation;

class SensesConnection implements ExternalConnection
{
    public function createTask(TaskContractor $taskContractor, Task $task) {
        if($taskContractor->linked_task_uuid) {
            throw new Exception('Task contractor already has a task');
        }

        $slaCustomerContactDate = $task->sla_customer_contact_date?->format(Format::DATETIME->value);
        $slaFirstAttendanceDate = $task->sla_first_attendance_date?->format(Format::DATETIME->value);
        $slaTaskCompleteDate = $task->sla_task_complete_date?->format(Format::DATETIME->value);

        $tags = [];
        foreach($task->tags as $tag) {
            array_push($tags, $this->externalTag($tag));
        }

        //todo adjust task create to check uuids and use when creating as we had to always provide so it creates
        $data = [
            'title' => $task->title,
            'external_service_uuid' => $taskContractor->externalService->meta['linked_external_service_uuid'], //other side will set depot/company
            'task_type_uuid' => $taskContractor->taskType->uuid,

            //todo swap to uuid and expect it to be there from floorplan
            // 'infrastructure_asset' => $task->infrastructureAsset ? [
            //     'uuid' => $task->infrastructureAsset->uuid,
            //     'reference' => $task->infrastructureAsset->reference,
            //     'utility_reference' => $task->infrastructureAsset->utility_reference,
            //     'geom_coords' => $this->formatGeometry($task->infrastructureAsset->geom),
            //     'meta' => $task->infrastructureAsset->meta,
            //     'infrastructure_asset_type_uuid' => $task->infrastructureAsset->infrastructureAssetType->uuid,
            // ] : null,
            'infrastructure_asset_uuid' => $task->infrastructureAsset?->uuid,
            'venue' => $task->venue ? $this->externalVenue($task->venue) : null,

            // 'contact' => $this->externalUser($task->contact, $taskContractor),
            // 'public_contact' => $this->externalUser($task->public_contact, $taskContractor),
            // 'secondary_public_contact' => $this->externalUser($task->secondary_public_contact, $taskContractor),
            // 'sales_contact' => $this->externalUser($task->sales_contact, $taskContractor),
            'work_instructions' => $taskContractor->work_instructions,
            'po_number' => $task->po_number,
            'reference' => $task->reference,
            'external_tags' => $tags,
            'service_agreement' => $task->service_agreement,
            'client_reference' => $task->client_reference,
            'sla_code_code' => $task->slaCode?->code,
            'sla_customer_contact_date' => $slaCustomerContactDate,
            'sla_first_attendance_date' => $slaFirstAttendanceDate,
            'sla_task_complete_date' => $slaTaskCompleteDate,
            'payment_reference' => $task->payment_reference,

            'project' => $task->project ? [
                'uuid' => $task->project->uuid,
                'title' => $task->project->title,
                'colour' => $task->project->colour
            ] : null,

            'work_stream' => $task->workStream ? [
                'uuid' => $task->workStream->uuid,
                'title' => $task->workStream->title,
                'colour' => $task->workStream->colour
            ] : null,

            'work_package' => $task->workPackage ? [
                'uuid' => $task->workPackage->uuid,
                'title' => $task->workPackage->title,
                'colour' => $task->workPackage->colour
            ] : null,

            'customer_type' => $task->customer_type,

            'task_contractor' => array_merge([
                'uuid' => $taskContractor->uuid,
                'linked_task_uuid' => $task->uuid,
                'task_type_uuid' => $task->taskType->uuid,
                'work_instructions' => $task->work_instructions,
                'parent' => !$taskContractor->parent,
                'external_service_uuid' => $taskContractor->externalService->meta['linked_external_service_uuid'] ?? null,
                'external_task_reference' => $task->id,
            ], $this->sensesFields($taskContractor, $taskContractor))
        ];

        $data = array_merge($data, $this->sensesFields($task, $taskContractor));

        $response = $this->post($taskContractor, '{+senses}/tasks', $data);

        if($response) {
            $taskContractor->linked_task_uuid = $response->json('uuid');
            $taskContractor->external_task_reference = $response->json('id');
            $taskContractor->save();
        }
    }

    public function createAssignmentGroup(TaskContractor $taskContractor, AssignmentGroup $assignmentGroup) {
        $data = $this->externalAssignmentGroup($taskContractor, $assignmentGroup);

        $data = array_merge($data, $this->sensesFields($assignmentGroup, $taskContractor));

        $this->post($taskContractor, '{+senses}/assignment-groups', $data);

    }

    public function updateAssignmentGroup(TaskContractor $taskContractor, AssignmentGroup $assignmentGroup) {
        $data = $this->externalAssignmentGroup($taskContractor, $assignmentGroup, steps:false);

        $data = array_merge($data, $this->sensesFields($assignmentGroup, $taskContractor));

        $this->patch($taskContractor, '{+senses}/assignment-groups/' . $assignmentGroup->uuid, $data);
    }

    public function updateAssignmentGroupStatus(TaskContractor $taskContractor, AssignmentGroup $assignmentGroup, Status $status) {
        $this->post($taskContractor, '{+senses}/assignment-groups/' . $assignmentGroup->uuid . '/status', ['slug' => $status->slug, 'receiving' => true]);
    }

    public function createComment(TaskContractor $taskContractor, Comment $comment)
    {
        $data = [
            'uuid' => $comment->uuid,
            'commentable_uuid' => $this->getModelUUID($comment->commentable, $taskContractor->linked_task_uuid),
            'commentable_type' => $comment->commentable_type,
            'content' => $comment->content,
            'show_on_app' => $comment->show_on_app,
            'type' => $comment->type,
            'sms' => $comment->sms,
            'email' => $comment->email,
            'file_uuids' => $comment->files()->pluck('uuid')->toArray()
        ];

        $data = array_merge($data, $this->sensesFields($comment, $taskContractor));

        $this->post($taskContractor, '{+senses}/comments', $data);
    }

    public function updateComment(ExternalService $externalService, Comment $comment)
    {
        $data = $this->externalComment($comment);

        $data = array_merge($data, $this->sensesFields($comment, $externalService));

        $this->patch($externalService, '{+senses}/comments/' . $comment->uuid, $data);
    }

    public function createAsset(TaskContractor $taskContractor, Asset $asset)
    {
        $this->post($taskContractor, '{+senses}/assets', $this->externalAsset($asset));
    }

    public function createUser(TaskContractor $taskContractor, User $user)
    {
        $this->post($taskContractor, '{+senses}/users', $this->externalUser($user, $taskContractor));
    }

    public function createFile(TaskContractor|ExternalService $resource, File $file)
    {
        $fileables = $this->resolveFileables($file, $resource);

        $data = [
            'uuid' => $file->uuid,
            'public' => $file->public,
            'app_visible' => $file->app_visible,
            'fileables' => $fileables->toArray(),
            'folder' => $file->folder,
        ];

        $data = array_merge($data, $this->sensesFields($file, $resource));

        $this->post($resource, '{+senses}/files', $data, $file, multiPartFlatten: true);
    }

    public function updateFile(TaskContractor|ExternalService $resource, File $file)
    {
        //get filelables directly to avoid ignoring newer relations added

        $fileables = $this->resolveFileables($file, $resource);

        $data = [
            'uuid' => $file->uuid,
            'public' => $file->public,
            'app_visible' => $file->app_visible,
            'fileables' => $fileables->toArray(),
            'folder' => $file->folder,
            'name' => $file->name
        ];

        $data = array_merge($data, $this->sensesFields($file, $resource));

        $this->patch($resource, '{+senses}/files/'. $file->uuid, $data, $file, multiPartFlatten: true);
    }

    public function createForm(TaskContractor $taskContractor, Form $form) {
        $deviceLocation = $this->externalDeviceLocation($form?->userActionLog);

        $data = [
            'uuid' => $form->uuid,
            'content' => $this->replaceFormContentIDsWithUUIDs($form),
            'form_structure_uuid' => $form->formStructure->uuid,
            'started_at' => $form->started_at?->format(Format::DATETIME->value),
            'completed_at' => $form->completed_at?->format(Format::DATETIME->value),
            'formable_uuid' => $form?->formable->uuid,
            'formable_type' => $form?->formable_type,
            'requirementable_uuid' => $form->requirementable?->uuid,
            'requirementable_type' => $form->requirementable_type,
            'meta' => $form->meta,
            'device_location' => $deviceLocation
        ];

        $data = array_merge($data, $this->sensesFields($form, $taskContractor));

        $this->post($taskContractor, '{+senses}/forms', $data);
    }

    public function updateForm(TaskContractor $taskContractor, Form $form) {
        $deviceLocation = $this->externalDeviceLocation($form?->userActionLog);

        $data = [
            'uuid' => $form->uuid,
            'content' => $this->replaceFormContentIDsWithUUIDs($form),
            'form_structure_uuid' => $form->formStructure->uuid,
            'started_at' => $form->started_at?->format(Format::DATETIME->value),
            'completed_at' => $form->completed_at?->format(Format::DATETIME->value),
            'formable_uuid' => $form?->formable->uuid,
            'formable_type' => $form?->formable_type,
            'requirementable_uuid' => $form->requirementable?->uuid,
            'requirementable_type' => $form->requirementable_type,
            'meta' => $form->meta,
            'device_location' => $deviceLocation
        ];

        $data = array_merge($data, $this->sensesFields($form, $taskContractor));

        $this->patch($taskContractor, '{+senses}/forms/' . $form->uuid, $data);
    }

    public function createTaskType(ExternalService $externalService, TaskType $taskType)
    {
        $data = [
            'uuid' => $taskType->uuid,
            'title' => $taskType->title,
            'colour' => $taskType->colour,
            'reference' => $taskType->reference,
        ];

        $data = array_merge($data, $this->sensesFields($taskType, $externalService));

        $this->post($externalService, '{+senses}/task-types', $data);
    }

    public function createWorkType(ExternalService $externalService, WorkType $workType)
    {
        $data = [
            'uuid' => $workType->uuid,
            'title' => $workType->title,
            'colour' => $workType->colour,
            'reference' => $workType->reference,
            'duration' => $workType->duration,
            // 'utility_type_form_structures' => $workType->utility_type_form_structures, //todo utility types needs syncing for this to work?
        ];

        $data = array_merge($data, $this->sensesFields($workType, $externalService));

        $this->post($externalService, '{+senses}/work-types', $data);
    }

    public function createTaskSteps(TaskContractor $taskContractor, TaskStep $taskStep) {
        $taskRequirements = [];
        foreach($taskStep->taskRequirements as $taskRequirement) {
            array_push($taskRequirements, $this->externalTaskRequirement($taskRequirement));
        }

        $data = [
            'uuid' => $taskStep->uuid,
            'task_uuid' => $taskContractor->linked_task_uuid,
            'title' => $taskStep->title,
            'order' => $taskStep->order,
            'started_at' => $taskStep->started_at?->completed_at?->format(Format::DATETIME->value),
            'completed_at' => $taskStep->started_at?->completed_at?->format(Format::DATETIME->value),
            'external_completed_by' => $this->externalUser($taskStep->completedBy, $taskContractor),
            'show_on_app' => $taskStep->show_on_app,
            'task_requirements' => empty($taskRequirements) ? null : $taskRequirements
        ];


        $this->post($taskContractor, '{+senses}/task-steps', $data);
    }

    public function createFormStructure(ExternalService $externalService, FormStructure $formStructure)
    {
        $data = [
            'uuid' => $formStructure->uuid,
            'title' => $formStructure->title,
            'type' => $formStructure->type,
            'structure' => $this->convertFormStructure($formStructure->structure),
            'actions' => $formStructure->actions,
            'complete_at_any_time' => $formStructure->complete_at_any_time,
            'client_visible' => $formStructure->client_visible,
            'customer_visible' => $formStructure->customer_visible,
            'sync' => false,    //stop children from sending back
            'draft' => false,
            'question_slugs' => $formStructure->question_slugs,
            'original_form_structure_uuid' => $formStructure->originalFormStructure?->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($formStructure, $externalService));

        $this->post($externalService, '{+senses}/form-structures', $data);
    }

    public function updateFormStructure(ExternalService $externalService, FormStructure $formStructure) {
        $data = [
            'external_uuid' => $formStructure->uuid,
            'title' => $formStructure->title,
            'type' => $formStructure->type,
            'structure' => $this->convertFormStructure($formStructure->structure),
            'actions' => $formStructure->actions,
            'complete_at_any_time' => $formStructure->complete_at_any_time,
            'client_visible' => $formStructure->client_visible,
            'customer_visible' => $formStructure->customer_visible,
            'sync' => false,    //stop children from sending back
            'draft' => false,
            'question_slugs' => $formStructure->question_slugs,
        ];

        $data = array_merge($data, $this->sensesFields($formStructure, $externalService));

        $this->patch($externalService, '{+senses}/form-structures/' . $formStructure->previousFormStructure->uuid, $data);
    }

    public function createTaskRuleGroup(ExternalService $externalService, TaskRuleGroup $taskRuleGroup)
    {

        $data = $this->externalTaskRuleGroup($taskRuleGroup, $externalService);

        $data = array_merge($data, $this->sensesFields($taskRuleGroup, $externalService));

        $this->post($externalService, '{+senses}/task-rule-groups', $data);
    }

    public function createAssignmentGroupRuleGroup(ExternalService $externalService, AssignmentGroupRuleGroup $assignmentGroupRuleGroup)
    {


        if(!isset($externalService->meta['linked_external_service_uuid'])) {
            return;
        }

        $data = $this->externalAssignmentGroupRuleGroup($assignmentGroupRuleGroup, $externalService);

        $data = array_merge($data, $this->sensesFields($assignmentGroupRuleGroup, $externalService));
        $data['company_uuid'] = null;
        $this->post($externalService, '{+senses}/assignment-group-rule-groups', $data);
    }

    public function createFloorPlan(ExternalService $externalService, FloorPlan $floorPlan)
    {

        $data = [
            'uuid' => $floorPlan->uuid,
            'name' => $floorPlan->name,
            'floor' => $floorPlan->floor,
            'venue_uuid' => $floorPlan->venue->uuid,
            'file_uuid' => $floorPlan->files[0]->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($floorPlan, $externalService));

        $this->post($externalService, '{+senses}/floor-plans', $data);
    }

    public function updateFloorPlan(ExternalService $externalService, FloorPlan $floorPlan)
    {

        $data = [
            'uuid' => $floorPlan->uuid,
            'name' => $floorPlan->name,
            'floor' => $floorPlan->floor,
            'venue_uuid' => $floorPlan->venue->uuid,
            'file_uuid' => $floorPlan->files[0]->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($floorPlan, $externalService));

        $this->patch($externalService, '{+senses}/floor-plans/' . $floorPlan->uuid, $data);
    }

    public function createVenue(ExternalService $externalService, Venue $venue)
    {

        $data = $this->externalVenue($venue);

        $data = array_merge($data, $this->sensesFields($venue, $externalService));

        $this->post($externalService, '{+senses}/venues/', $data);
    }

    public function updateVenue(ExternalService $externalService, Venue $venue)
    {
        $data = $this->externalVenue($venue);

        $data = array_merge($data, $this->sensesFields($venue, $externalService));

        $this->patch($externalService, '{+senses}/venues/' . $venue->uuid, $data);
    }

    public function updateClientApplicationType(ExternalService $externalService, ClientApplicationType $clientApplicationType)
    {
        $data = $this->externalClientApplicationType($clientApplicationType);

        $data = array_merge($data, $this->sensesFields($clientApplicationType, $externalService));

        $this->patch($externalService, '{+senses}/client-application-types/' . $clientApplicationType->uuid, $data);
    }

    public function createInfrastructureAsset(ExternalService $externalService, InfrastructureAsset $infrastructureAsset)
    {
        // I hate I Asset coordinates
        $coords = [$infrastructureAsset->geom?->getY(), $infrastructureAsset->geom?->getX()] ?? [];

        $data = [
            "uuid" => $infrastructureAsset->uuid,
            "content" => $infrastructureAsset->content,
            "downstream_uuid" => $infrastructureAsset?->downstream?->uuid ?? null,
            "floor_plan_geom" => $infrastructureAsset->floor_plan_geom,
            "floor_plan_uuid" => $infrastructureAsset?->floorPlan?->uuid ?? null,
            "form_structure_uuid" => $infrastructureAsset?->formStructure?->uuid ?? null,
            // faker()->latitude(-16.1, 32.88), faker()->longitude(40.18, 84.17)
            "geom_coords" => $coords,
            "infrastructure_asset_type_uuid" => $infrastructureAsset->infrastructureAssetType->uuid,
            "meta" => $infrastructureAsset->meta,
            "reference" => $infrastructureAsset->reference,
            "upstream_uuid" => $infrastructureAsset?->upstream?->uuid ?? null,
            "utility_type_uuid" => $infrastructureAsset?->utilityType?->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($infrastructureAsset, $externalService));

        $this->post($externalService, '{+senses}/infrastructure-assets', $data);
    }

    public function updateInfrastructureAsset(ExternalService $externalService, InfrastructureAsset $infrastructureAsset)
    {
        // I hate I Asset coordinates
        $coords = [$infrastructureAsset->geom?->getY(), $infrastructureAsset->geom?->getX()] ?? [];

        $data = [
            "uuid" => $infrastructureAsset->uuid,
            "content" => $infrastructureAsset->content,
            "downstream_uuid" => $infrastructureAsset?->downstream?->uuid ?? null,
            "floor_plan_geom" => $infrastructureAsset->floor_plan_geom,
            "floor_plan_uuid" => $infrastructureAsset?->floorPlan?->uuid ?? null,
            "form_structure_uuid" => $infrastructureAsset?->formStructure?->uuid ?? null,
            // faker()->latitude(-16.1, 32.88), faker()->longitude(40.18, 84.17)
            "geom_coords" => $coords,
            "infrastructure_asset_type_uuid" => $infrastructureAsset->infrastructureAssetType->uuid,
            "meta" => $infrastructureAsset->meta,
            "reference" => $infrastructureAsset->reference,
            "upstream_uuid" => $infrastructureAsset?->upstream?->uuid ?? null,
            "utility_type_uuid" => $infrastructureAsset?->utilityType?->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($infrastructureAsset, $externalService));

        $this->patch($externalService, '{+senses}/infrastructure-assets/' . $infrastructureAsset->uuid, $data);
    }

    public function createInfrastructureAssetType(ExternalService $externalService, InfrastructureAssetType $infrastructureAssetType)
    {
        $data = [
            "uuid" => $infrastructureAssetType->uuid,
            "title" => $infrastructureAssetType->title,
            "reference" => $infrastructureAssetType->reference,
            "infrastructure_asset_category_uuid" => $infrastructureAssetType->infrastructureAssetCategory->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($infrastructureAssetType, $externalService));

        $this->post($externalService, '{+senses}/infrastructure-asset-types', $data);
    }

    public function updateInfrastructureAssetType(ExternalService $externalService, InfrastructureAssetType $infrastructureAssetType)
    {
        $data = [
            "uuid" => $infrastructureAssetType->uuid,
            "title" => $infrastructureAssetType->title,
            "reference" => $infrastructureAssetType->reference,
            "infrastructure_asset_category_uuid" => $infrastructureAssetType->infrastructureAssetCategory->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($infrastructureAssetType, $externalService));

        $this->patch($externalService, '{+senses}/infrastructure-asset-types/' . $infrastructureAssetType->uuid, $data);
    }

    public function createInfrastructureAssetCategory(ExternalService $externalService, InfrastructureAssetCategory $infrastructureAssetCategory)
    {
        $data = [
            "uuid" => $infrastructureAssetCategory->uuid,
            "title" => $infrastructureAssetCategory->title,
            "reference" => $infrastructureAssetCategory->reference,
            "utility_type_uuid" => $infrastructureAssetCategory->utilityType->uuid,
            "form_structure_uuid" => $infrastructureAssetCategory?->formStructure?->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($infrastructureAssetCategory, $externalService));

        $this->post($externalService, '{+senses}/infrastructure-asset-categories', $data);
    }

    public function updateInfrastructureAssetCategory(ExternalService $externalService, InfrastructureAssetCategory $infrastructureAssetCategory)
    {
        $data = [
            "uuid" => $infrastructureAssetCategory->uuid,
            "title" => $infrastructureAssetCategory->title,
            "reference" => $infrastructureAssetCategory->reference,
            "utility_type_uuid" => $infrastructureAssetCategory->utilityType->uuid,
            "form_structure_uuid" => $infrastructureAssetCategory?->formStructure?->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($infrastructureAssetCategory, $externalService));

        $this->patch($externalService, '{+senses}/infrastructure-asset-categories/'. $infrastructureAssetCategory->uuid, $data);
    }

    public function createUtilityType(ExternalService $externalService, UtilityType $utilityType)
    {
        $data = [
            "uuid" => $utilityType->uuid,
            "title" => $utilityType->title,
            "slug" => $utilityType->slug,
            "map_type" => $utilityType->map_type,
            "form_structure_uuid" => $utilityType?->formStructure?->uuid,
            "map_icon" => $utilityType?->map_icon,
        ];

        $data = array_merge($data, $this->sensesFields($utilityType, $externalService));

        $this->post($externalService, '{+senses}/utility-types', $data);
    }

    public function updateUtilityType(ExternalService $externalService, UtilityType $utilityType)
    {
        $data = [
            "uuid" => $utilityType->uuid,
            "title" => $utilityType->title,
            "slug" => $utilityType->slug,
            "map_type" => $utilityType->map_type,
            "form_structure_uuid" => $utilityType?->formStructure?->uuid,
            "map_icon" => $utilityType?->map_icon,
        ];

        $data = array_merge($data, $this->sensesFields($utilityType, $externalService));

        $this->patch($externalService, '{+senses}/utility-types/'. $utilityType->uuid, $data);
    }

    public function updateTaskContractorStatus(TaskContractor $taskContractor, Status $status) {
        $data = [
            'status_slug' => $status->slug,
            'completed_at' => $taskContractor->completed_at?->format(Format::DATETIME->value),
            'external_completer' => $this->externalUser($taskContractor->completer, $taskContractor),
            'uuid' => $taskContractor->uuid,
            'linked_task_uuid' => $taskContractor->linked_task_uuid,
        ];

        $data = array_merge($data, $this->sensesFields($taskContractor, $taskContractor));

        // $this->post($taskContractor, '{+senses}/task-contractors/'. $taskContractor->uuid.'/status', $data);
        $this->post($taskContractor, '{+senses}/task-contractors-update-status', $data);
    }

    public function completeTaskContractor(TaskContractor $taskContractor) {
        $data = [
            'completed_at' => $taskContractor->completed_at,
            'completed_by' => $taskContractor->completer?->uuid,
            'external_completer' => $this->externalUser($taskContractor->completer, $taskContractor)
        ];

        $data = array_merge($data, $this->sensesFields($taskContractor, $taskContractor));

        $response = $this->httpClient($taskContractor)?->post('{+senses}/task-contractors/'. $taskContractor->uuid.'/complete-client-complete-sla', $data);

        $this->handleResponse($response);
    }

    public function completeTaskContractorClientCompleteSla(TaskContractor $taskContractor) {
        $data = [
            'sla_client_complete_completed_at' => $taskContractor->sla_client_complete_completed_at,
            'sla_client_complete_completed_by' => $taskContractor->slaClientCompleteCompleter?->uuid,
            'external_completer' => $this->externalUser($taskContractor->slaClientCompleteCompleter, $taskContractor)
        ];

        $data = array_merge($data, $this->sensesFields($taskContractor, $taskContractor));

        $response = $this->httpClient($taskContractor)?->post('{+senses}/task-contractors/'. $taskContractor->uuid.'/complete-client-complete-sla', $data);

        $this->handleResponse($response);
    }

    public function completeTaskContractorCustomerContactSla(TaskContractor $taskContractor) {
        $data = [
            'sla_customer_contact_complete_completed_at' => $taskContractor->sla_customer_contact_completed_at,
            'sla_customer_contact_completed_by' => $taskContractor->slaCustomerContactCompleter?->uuid,
            'external_completer' => $this->externalUser($taskContractor->slaCustomerContactCompleter, $taskContractor)
        ];

        $data = array_merge($data, $this->sensesFields($taskContractor, $taskContractor));

        $this->post($taskContractor, '{+senses}/task-contractors/' . $taskContractor->uuid . '/complete-customer-contact-sla', $data);
    }

    public function completeTaskContractorFirstAttendanceSla(TaskContractor $taskContractor) {
        $data = [
            'sla_first_attendance_completed_at' => $taskContractor->sla_first_attendance_completed_at,
            'sla_first_attendance_completed_by' => $taskContractor->slaFirstAttendanceCompleter?->uuid,
            'external_completer' => $this->externalUser($taskContractor->slaFirstAttendanceCompleter, $taskContractor)
        ];

        $data = array_merge($data, $this->sensesFields($taskContractor, $taskContractor));

        $this->post($taskContractor, '{+senses}/task-contractors/' . $taskContractor->uuid . '/complete-first-attendance-sla', $data);
    }

    public function completeTaskContractorTaskCompleteSla(TaskContractor $taskContractor) {
        $data = [
            'sla_task_complete_completed_at' => $taskContractor->sla_task_complete_completed_at,
            'sla_task_complete_completed_by' => $taskContractor->slaTaskCompleteCompleter?->uuid,
            'external_completer' => $this->externalUser($taskContractor->slaTaskCompleteCompleter, $taskContractor)
        ];

        $data = array_merge($data, $this->sensesFields($taskContractor, $taskContractor));

        $this->post($taskContractor, '{+senses}/task-contractors/' . $taskContractor->uuid . '/complete-task-complete-sla', $data);
    }

    public function createAssignmentGroupLoginStatus(TaskContractor $taskContractor, AssignmentGroupLoginStatus $assignmentGroupLoginStatus, bool $updateStatus = true)
    {
        $externalAssets = [];
        $externalUsers = [];

        if(isset($assignmentGroupLoginStatus->resources['asset_ids']) && !empty($assignmentGroupLoginStatus->resources['asset_ids'])) {
            $assets = Asset::whereIn('id', $assignmentGroupLoginStatus->resources['asset_ids'])->get();
            foreach($assets as $asset) {
                array_push($externalAssets, $this->externalAsset($asset));
            }
        }

        if(isset($assignmentGroupLoginStatus->resources['user_ids']) && !empty($assignmentGroupLoginStatus->resources['user_ids'])) {
            $users = User::whereIn('id', $assignmentGroupLoginStatus->resources['user_ids'])->get();
            foreach($users as $user) {
                array_push($externalUsers, $this->externalUser($user, $taskContractor));
            }
        }

        $data = [
            'uuid' => $assignmentGroupLoginStatus->uuid,
            'user_uuid' => $assignmentGroupLoginStatus->user->uuid,
            'assignment_group_uuid' => $assignmentGroupLoginStatus->assignmentGroup->uuid,
            'external_assets' => !empty($externalAssets) ? $externalAssets : null,
            'external_users' => !empty($externalUsers) ? $externalUsers : null,
            'login_date' => $assignmentGroupLoginStatus->login_date?->format(Format::DATETIME->value),
            'logout_date' => $assignmentGroupLoginStatus->logout_date?->format(Format::DATETIME->value),
        ];

        if(!$updateStatus) {
            $data['lock_assignment_group_status'] = true;
        }

        $data = array_merge($data, $this->sensesFields($assignmentGroupLoginStatus, $taskContractor));

        $this->post($taskContractor, '{+senses}/assignment-group-login-statuses', $data);
    }

    public function createDocument(ExternalService $externalService, Document $document)
    {
        $tagSlugs = [];

        foreach ($document->tags as $tag) {
            array_push($tagSlugs, $tag->slug);
        }

        $data = [
            "uuid" => $document->uuid,
            "title" => $document->title,
            "type" => $document->type,
            "show_on_app" => $document->show_on_app,
            "sync" => $document->sync,
            "tag_slugs" => $tagSlugs,
            "file_uuid" => $document?->files[0]?->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($document, $externalService));

        $this->post($externalService, '{+senses}/documents', $data);
    }

    public function updateDocument(ExternalService $externalService, Document $document)
    {
        $tagSlugs = [];

        foreach ($document->tags as $tag) {
            array_push($tagSlugs, $tag->slug);
        }

        $data = [
            "uuid" => $document->uuid,
            "title" => $document->title,
            "type" => $document->type,
            "show_on_app" => $document->show_on_app,
            "sync" => $document->sync,
            "tag_slugs" => $tagSlugs,
            "file_uuid" => $document?->files[0]?->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($document, $externalService));

        $this->patch($externalService, '{+senses}/documents/' . $document->uuid, $data);
    }

    public function createTag(ExternalService $externalService, Tag $tag)
    {
        $tagGroupSlugs = [];

        foreach ($tag->tagGroups as $tagGroup) {
            array_push($tagGroupSlugs, $tagGroup->slug);
        }

        $data = [
            "title" => $tag->title,
            "slug" => $tag->slug,
            "base_state" => $tag->base_state,
            "colour" => $tag->colour,
            "tag_group_slugs" => $tagGroupSlugs,
        ];

        $data = array_merge($data, $this->sensesFields($tag, $externalService));

        $this->post($externalService, '{+senses}/tags', $data);
    }

    public function createTagGroup(ExternalService $externalService, TagGroup $tagGroup)
    {
        $data = [
            "title" => $tagGroup->title,
            "slug" => $tagGroup->slug,
        ];

        $data = array_merge($data, $this->sensesFields($tagGroup, $externalService));

        $this->post($externalService, '{+senses}/tag-groups', $data);
    }

    public function updateAssignmentGroupLoginStatus(TaskContractor $taskContractor, AssignmentGroupLoginStatus $assignmentGroupLoginStatus) {
        $externalAssets = [];
        $externalUsers = [];

        if(isset($assignmentGroupLoginStatus->resources['asset_ids']) && !empty($assignmentGroupLoginStatus->resources['asset_ids'])) {
            $assets = Asset::whereIn('id', $assignmentGroupLoginStatus->resources['asset_ids'])->get();
            foreach($assets as $asset) {
                array_push($externalAssets, $this->externalAsset($asset));
            }
        }

        if(isset($assignmentGroupLoginStatus->resources['user_ids']) && !empty($assignmentGroupLoginStatus->resources['user_ids'])) {
            $users = User::whereIn('id', $assignmentGroupLoginStatus->resources['user_ids'])->get();
            foreach($users as $user) {
                array_push($externalUsers, $this->externalUser($user, $taskContractor));
            }
        }

        $data = [
            'external_assets' => !empty($externalAssets) ? $externalAssets : null,
            'external_users' => !empty($externalUsers) ? $externalUsers : null,
            'user_uuid' => $assignmentGroupLoginStatus->user->uuid,
            'assignment_group_uuid' => $assignmentGroupLoginStatus->assignmentGroup->uuid,
            'login_date' => $assignmentGroupLoginStatus->login_date?->format(Format::DATETIME->value),
            'logout_date' => $assignmentGroupLoginStatus->logout_date?->format(Format::DATETIME->value),
        ];

        $data = array_merge($data, $this->sensesFields($assignmentGroupLoginStatus, $taskContractor));

        $this->patch($taskContractor, '{+senses}/assignment-group-login-statuses/'. $assignmentGroupLoginStatus->uuid, $data);
    }

    public function completeAssignmentGroupStep(TaskContractor $taskContractor, AssignmentGroupStep $assignmentGroupStep)
    {
        $data = [
            'completed_at' => $assignmentGroupStep->completed_at?->format(Format::DATETIME->value),
            'completed_by' => $this->externalUser($assignmentGroupStep->completedBy, $taskContractor),
            'assignment_group_step_uuid' => $assignmentGroupStep->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($assignmentGroupStep, $taskContractor));

        $this->post($taskContractor, '{+senses}/assignment-groups/'. $assignmentGroupStep->assignmentGroup->uuid .'/complete-assignment-group-step', $data);
    }

    public function completeTaskStep(TaskContractor $taskContractor, TaskStep $taskstep)
    {
        $data = [
            'completed_at' => $taskstep->completed_at?->format(Format::DATETIME->value),
            'completed_by' => $this->externalUser($taskstep->completedBy, $taskContractor),
            'assignment_group_step_uuid' => $taskstep->uuid,
        ];

        $data = array_merge($data, $this->sensesFields($taskstep, $taskContractor));

        $this->post($taskContractor, '{+senses}/tasks/'. $taskContractor->linked_task_uuid .'/complete-task-step', $data);
    }

    public function completeAssignmentGroupRequirement(TaskContractor $taskContractor, AssignmentGroupRequirement $assignmentGroupRequirement)
    {
        $data = [
            'completed_at' => $assignmentGroupRequirement->completed_at?->format(Format::DATETIME->value),
            'completer' => $this->externalUser($assignmentGroupRequirement->completer, $taskContractor),
        ];

        $data = array_merge($data, $this->sensesFields($assignmentGroupRequirement, $taskContractor));

        $this->post($taskContractor, '{+senses}/assignment-group-requirements/'. $assignmentGroupRequirement->uuid .'/complete', $data);
    }

    public function completeTaskRequirement(TaskContractor $taskContractor, TaskRequirement $taskRequirement)
    {
        $data = [
            'completed_at' => $taskRequirement->completed_at?->format(Format::DATETIME->value),
            'completer' => $this->externalUser($taskRequirement->completer, $taskContractor),
        ];

        $data = array_merge($data, $this->sensesFields($taskRequirement, $taskContractor));

        $this->post($taskContractor, '{+senses}/task-requirements/'. $taskRequirement->uuid .'/complete', $data);
    }

    public function updateWorkType(ExternalService $externalService, WorkType $workType)
    {
        $data = [
            'uuid' => $workType->uuid,
            'title' => $workType->title,
            'colour' => $workType->colour,
            'reference' => $workType->reference,
            'duration' => $workType->duration,
            // 'utility_type_form_structures' => $workType->utility_type_form_structures, //todo utility types needs syncing for this to work?
        ];

        $data = array_merge($data, $this->sensesFields($workType, $externalService));

        $this->patch($externalService, '{+senses}/work-types/' . $workType->uuid, $data);
    }

    public function updateTaskType(ExternalService $externalService, TaskType $taskType) {
        $data = [
            'uuid' => $taskType->uuid,
            'title' => $taskType->title,
            'colour' => $taskType->colour,
            'reference' => $taskType->reference,
        ];

        $data = array_merge($data, $this->sensesFields($taskType, $externalService));

        $this->patch($externalService, '{+senses}/task-types/' . $taskType->uuid, $data);
    }

    public function updateTaskRuleGroup(ExternalService $externalService, TaskRuleGroup $taskRuleGroup) {
        $data = $this->externalTaskRuleGroup($taskRuleGroup, $externalService);

        $data = array_merge($data, $this->sensesFields($taskRuleGroup, $externalService));

        $this->patch($externalService, '{+senses}/task-rule-groups/' . $taskRuleGroup->uuid, $data);
    }

    public function updateAssignmentGroupRuleGroup(ExternalService $externalService, AssignmentGroupRuleGroup $assignmentGroupRuleGroup)
    {
        if(!isset($externalService->meta['linked_external_service_uuid'])) {
            return;
        }

        $data = $this->externalAssignmentGroupRuleGroup($assignmentGroupRuleGroup, $externalService);

        $data = array_merge($data, $this->sensesFields($assignmentGroupRuleGroup, $externalService));

        $this->patch($externalService, '{+senses}/assignment-group-rule-groups/' . $assignmentGroupRuleGroup->uuid, $data);
    }

    public function createClientApplicationType(ExternalService $externalService, ClientApplicationType $clientApplicationType)
    {
        $data = $this->externalClientApplicationType($clientApplicationType);

        $data = array_merge($data, $this->sensesFields($clientApplicationType, $externalService));

        $this->post($externalService, '{+senses}/client-application-types', $data);
    }

    public function createClientApplication(TaskContractor $taskContractor, ClientApplication $clientApplication)
    {
        $data = [
            "uuid" => $clientApplication->uuid,
            "task_uuid" => $taskContractor->linked_task_uuid, //external
            "client_application_type_uuid" => $clientApplication->clientApplicationType->uuid,
            "request_date" => $clientApplication->request_date?->format(Format::DATETIME->value),
            "form_structure_uuid" => $clientApplication->form?->formStructure->uuid,
            "content" => $this->replaceFormContentIDsWithUUIDs($clientApplication->form), //todo just send form in this request so its the same?
            'status_type' => $clientApplication->status_type,
            'external_service_uuid' => $taskContractor->externalService->meta['linked_external_service_uuid'] ?? null,
            'external_requested_by' => $this->externalUser($clientApplication->requester, $taskContractor),
            'external_responded_by' => $this->externalUser($clientApplication->responder, $taskContractor),
            "external_task_uuid" => $clientApplication->task->uuid //internal
        ];

        $data = array_merge($data, $this->sensesFields($clientApplication, $taskContractor));

        $this->post($taskContractor, '{+senses}/client-applications', $data);
    }

    public function updateClientApplication(TaskContractor $taskContractor, ClientApplication $clientApplication)
    {
    }

    public function createContractorPublish(TaskContractor $taskContractor, ContractorPublish $contractorPublish) {
        $data = [
            "uuid" => $contractorPublish->uuid,
            'details' => $contractorPublish->details,
            'task_uuid' => $taskContractor->linked_task_uuid,
            "external_task_uuid" => $contractorPublish->task->uuid //internal
        ];

        $data = array_merge($data, $this->sensesFields($contractorPublish, $taskContractor));

        $this->post($taskContractor, '{+senses}/contractor-publishes', $data);
    }

    public function reviewClientApplication(TaskContractor $taskContractor, ClientApplication $clientApplication, string $action = null)
    {
        $actionArray = [
            'action' => $action,
            'status_slug' => $clientApplication->status?->slug,
            'status_type' => $clientApplication->status_type,
            'response_date' => $clientApplication->response_date?->format(Format::DATETIME->value),
            'response_notes' => $clientApplication->response_notes,
            'responder' => $this->externalUser($clientApplication->responder, $taskContractor)
        ];

        $data = [
            "response_notes" => $clientApplication?->response_notes,
            "action" => $actionArray,
        ];

        $data = array_merge($data, $this->sensesFields($clientApplication, $taskContractor));

        $this->post($taskContractor, '{+senses}/client-applications/'.$clientApplication->uuid.'/review', $data);
    }

    public function deleteAssignmentGroup(TaskContractor $taskContractor, AssignmentGroup $assignmentGroup)
    {
        $this->delete($taskContractor, '{+senses}/assignment-groups/' . $assignmentGroup->uuid, $this->sensesFields($assignmentGroup, $taskContractor));
    }

    public function exists(TaskContractor|ExternalService $resource,  Model $model, string $url = null) : bool {
        if(!$url) {
            $url = '{+senses}/' . Str::of($model->getMorphClass())->plural()->slug();
        }

        $queryParams = ['filter' => ['uuid_exact' => $model->uuid], 'limit' => 1];
        if($model instanceof Task) {
            $queryParams = ['filter' => ['linked_uuid' => $model->uuid], 'limit' => 1];               //query by the contractor task link since uuids for tasks are different
        }
        else if($model instanceof Tag) {
            $queryParams = ['filter' => ['slug_exact' => $model->slug], 'limit' => 1];               //query by the contractor task link since uuids for tasks are different
        }

        if($model instanceof File) {
            $queryParams['filter']['pending_exact'] = false; //allow files to overwrite pending
        }

        $response = $this->get($resource, $url, $queryParams);

        if(!$response) {
            return false;
        }

        $data = $response->json('data', []);

        if(is_array($data) && count($data) >= 1) {
            return true;
        }

        return false;
    }

    public function httpClient(TaskContractor|ExternalService $taskContractor, bool $api = true)
    {
        if ($taskContractor instanceof TaskContractor) {
            $externalService = $taskContractor->externalService;
        } else {
            $externalService = $taskContractor;
        }

        if(!$externalService->active) {
            return null;
        }

        if(config('app.env') != 'production') {
            return null;
        }

        if($externalService->service_type != ExternalServiceType::SENSES) {
            throw new Exception('External service is not senses for SensesConnection');
        }

        $apiKey = $externalService->meta['api_key'] ?? null;
        if(!$apiKey) {
            throw new Exception('No senses api key found for external service ' . $externalService->id);
        }

        $url = $this->getBaseUrl($externalService, $api);

        return Http::withOptions(['verify' => false])->withToken($apiKey)
        ->acceptJson()
        ->withUrlParameters([
            'senses' => $url
        ]);
    }

    public function getBaseUrl(ExternalService|TaskContractor $resource, bool $api = true) {
        $externalService = $resource;
        if($resource instanceof TaskContractor) {
            $externalService = $resource->externalService;
        }

        $url = $externalService->meta['url'] ?? null;
        if(!$url) {
            throw new Exception('No senses client found for external service ' . $externalService->id);
        }

        $url = Str::endsWith($url, '/') ? $url : $url . '/';

        if($api) {
            $url .= 'api/v2';
        }

        return $url;
    }

    public function handleResponse($response) {
        $response?->throw();
    }

    public function externalUser(User $user = null, ExternalService|TaskContractor $resource = null) {
        if(!$user) {
            return null;
        }

        $externalService = $resource;
        if($resource instanceof TaskContractor) {
            $externalService = $resource->externalService;
        }

        return [
            'uuid' => $user->uuid,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
            'external_service_uuid' =>   $externalService?->meta['linked_external_service_uuid'] ?? null
        ];
    }

    public function externalAsset(Asset $asset = null) {
        if(!$asset) {
            return null;
        }

        return [
            'uuid' => $asset->uuid,
            'title' => $asset->title,
            'registration' => $asset->registration,
            'colour' => $asset->colour,
        ];
    }

    public function externalTaskRuleGroup(TaskRuleGroup $taskRuleGroup, ExternalService $externalService) {
        $formattedTaskRules = [];

        foreach ($taskRuleGroup->taskRules as $taskRule) {
            array_push($formattedTaskRules, $this->externalTaskRule($taskRule));
        }

        return [
            'uuid' => $taskRuleGroup->uuid,
            'task_type_uuid' => $taskRuleGroup->taskType->uuid,
            'external_service_uuid' => $externalService->meta['linked_external_service_uuid'] ?? null, //other side will set company
            'contract_id' => null, // Don't send
            'work_stream_id' => null, // Maybe send
            'work_package_id' => null, // Maybe send
            'department_id' => null, // Don't send
            'depot_id' => null, // Don't send
            'order_slug' => $taskRuleGroup->order_slug,
            'order' => $taskRuleGroup->order,
            'task_rules' => $formattedTaskRules
        ];
    }

    public function externalAssignmentGroupRuleGroup(AssignmentGroupRuleGroup $assignmentGroupRuleGroup, ExternalService $externalService) {
        if(!$assignmentGroupRuleGroup) {
            return null;
        }

        $formattedAssignmentGroupRules = [];

        foreach ($assignmentGroupRuleGroup->assignmentGroupRules as $assignmentGroupRule) {
            array_push($formattedAssignmentGroupRules, $this->externalAssignmentGroupRule($assignmentGroupRule));
        }

        return [
            'uuid' => $assignmentGroupRuleGroup->uuid,
            'external_service_uuid' => $externalService->meta['linked_external_service_uuid'] ?? null, //other side will set company
            'order' => $assignmentGroupRuleGroup->order,
            'order_slug' => $assignmentGroupRuleGroup->order_slug,
            'project_id' => null, // Don't send
            'survey_infrastructure_asset' => $assignmentGroupRuleGroup->survey_infrastructure_asset, // Maybe send
            'work_package_id' => null,
            'work_stream_id' => null,
            'work_type_uuid' => $assignmentGroupRuleGroup->workType->uuid,
            'assignment_group_rules' => $formattedAssignmentGroupRules
        ];
    }

    public function getModelUUID($model, $linkedTaskUUID) {
        if($model instanceof Task) {
            return $linkedTaskUUID;
        }

        return $model->uuid;
    }

    public function multiPartFlatten(array $data) {
        //turn array of arrays into a multipart format where the key indicates its array depth,

        //data ['a' => ['b' => 'c']]   =>  [ 'a[b]' => 'c' ]
        $flattenedData = [];

        $dotData = Arr::dot($data); //flattens array of arrays into dot notation

        foreach($dotData as $key => $value) {

            $key = str_replace('.', '][', $key);

            $key = Str::replaceFirst(']', '', $key);
            if(str_contains($key, '[')) {
                $key .= ']';
            }

            if(is_array($value) && empty($value)) {
                continue; //skip key as dot should have flattened it, can't send an array.
            }

            $flattenedData[$key] = $value;
        }

        return $flattenedData;
    }

    public function sensesFields($model, $resource = null) {
        return [
            'created_at' => $model->created_at?->format(Format::DATETIME->value),
            'hidden_at' => $model->hidden_at?->format(Format::DATETIME->value),
            'locked_at' => $model->locked_at?->format(Format::DATETIME->value),
            'lock_type' => $model->lock_type?->value,
            'external_creator' => $this->externalUser($model->creator, $resource),
            'external_updater' => $this->externalUser($model->updater, $resource),
            'external_locker' => $this->externalUser($model->locker, $resource),
            'external_deleter' => $this->externalUser($model->deleter, $resource),
            'external_hider' => $this->externalUser($model->hider, $resource),
        ];
    }

    public function formatGeometry($geom) {
        if($geom instanceof Point) {
            return [$geom->getX(), $geom->getY()];
        }
        else if($geom instanceof Point) {
            $points = [];
            foreach($geom->getArray()['points'] as $point) {
                array_push($points, [$point->getX(), $point->getY()]);
            }
            return $points;
        }

        return null;
    }

    public function externalAssignmentGroupStep($assignmentGroupStep, $taskContractor = null) {
        $requirements = [];

        foreach($assignmentGroupStep->assignmentGroupRequirements as $assignmentGroupRequirement) {
            if(isExternalUUID($assignmentGroupRequirement->formStructure->uuid)) {
                array_push($requirements, $this->externalAssignmentGroupRequirement($assignmentGroupRequirement));
            }
        }

        return [
            'uuid' => $assignmentGroupStep->uuid,
            'title' => $assignmentGroupStep->title,
            'order' => $assignmentGroupStep->order,
            'ordered' => $assignmentGroupStep->ordered,
            'show_on_app' => $assignmentGroupStep->show_on_app,
            'started_at' => $assignmentGroupStep->started_at?->format(Format::DATETIME->value),
            'completed_at' => $assignmentGroupStep->completed_at?->format(Format::DATETIME->value),
            'completed_by' => $this->externalUser($assignmentGroupStep->completedBy, $taskContractor),
            'create_infrastructure_asset' => $assignmentGroupStep->create_infrastructure_asset,
            'survey_infrastructure_asset' => $assignmentGroupStep->survey_infrastructure_asset,
            'requirements' => $requirements,
        ];
    }

    public function externalAssignmentGroupRequirement($assignmentGroupRequirement) {
        return [
            'uuid' => $assignmentGroupRequirement->uuid,
            'client_visible' => $assignmentGroupRequirement->client_visible,
            // 'customer_completable' => $assignmentGroupRequirement->customer_completable, //not a thing?
            'customer_visible' => $assignmentGroupRequirement->customer_visible,
            'form_structure_uuid' => $assignmentGroupRequirement->formStructure->uuid,
            'required' => $assignmentGroupRequirement->required,
            'show_on_app' => $assignmentGroupRequirement->show_on_app,
            'order' => $assignmentGroupRequirement->order,
            'visible' => $assignmentGroupRequirement->visible,
            'visible_criteria' => $assignmentGroupRequirement->visible_criteria,
        ];
    }

    public function externalTaskRule($taskRule)
    {
        return [
            'uuid' => $taskRule->uuid,
            'client_visible' => $taskRule->client_visible,
            'customer_changeable' => $taskRule->customer_changeable,
            'customer_visible' => $taskRule->customer_visible,
            'form_structure_uuid' => $taskRule->formStructure->uuid,
            'required' => $taskRule->required,
            'title' => $taskRule->title,
            'visible' => $taskRule->visible,
            'visible_criteria' => $taskRule->visible_criteria,
        ];
    }

    public function externalTag($tag)
    {
        return [
            "slug" => $tag->slug,
            "title" => $tag->title,
            "colour" => $tag->colour,
            "text_colour" => $tag->text_colour,
        ];
    }

    public function externalTagGroup($tagGroup)
    {
        return [
            "slug" => $tagGroup->slug,
            "title" => $tagGroup->title,
        ];
    }

    public function externalAssignmentGroupRule($assignmentGroupRule)
    {
        return [
            'uuid' => $assignmentGroupRule->uuid,
            'client_visible' => $assignmentGroupRule->client_visible,
            'customer_changeable' => $assignmentGroupRule->customer_changeable,
            'customer_visible' => $assignmentGroupRule->customer_visible,
            'form_structure_uuid' => $assignmentGroupRule->formStructure->uuid,
            'required' => $assignmentGroupRule->required,
            'title' => $assignmentGroupRule->title,
            'visible' => $assignmentGroupRule->visible,
            'visible_criteria' => $assignmentGroupRule->visible_criteria,
        ];
    }

    public function externalTaskRequirement($taskRequirement) {
        return [
            'title' => $taskRequirement->title,
            'order' => $taskRequirement->order,
            'required' => $taskRequirement->required,
            'client_visible' => $taskRequirement->client_visible,
            'customer_visible' => $taskRequirement->customer_visible,
            'visible' => $taskRequirement->visible,
            'visible_criteria' => $taskRequirement->visible_criteria,
            'form_structure_uuid' => $taskRequirement->formStructure?->uuid,
            'customer_completable' => $taskRequirement->customer_completable,
        ];
    }

    public function externalVenue($venue)
    {
        return [
            'uuid' => $venue->uuid,
            'name' => $venue->name,
            'title' => $venue->title,
            'address_1' => $venue->address_1,
            'town' => $venue->town,
            'street' => $venue->street,
            'city' => $venue->city,
            'county' => $venue->county,
            'country' => $venue->country,
            'postcode' => $venue->postcode,
            'description' => $venue->description,

            'lat' => (string)$venue->geom?->getX(),
            'lng' => (string)$venue->geom?->getY(),

            'station' => $venue->station ?? null,
            'lines_served' => $venue->lines_served ?? null,
        ];
    }

    public function externalComment($comment)
    {
        return [
            'uuid' => $comment->uuid,
            'content' => $comment->content,
            'show_on_app' => $comment->show_on_app,
            'type' => $comment->type,
            'sms' => $comment->sms,
            'email' => $comment->email,
            'files_count' => $comment->files_count,
        ];
    }

    public function externalClientApplicationType($clientApplicationType) {
        return [
            "uuid" => $clientApplicationType->uuid,
            "title" => $clientApplicationType->title,
            "slug" => $clientApplicationType->slug,
            "form_structure_uuid" => $clientApplicationType->formStructure?->uuid,
            "actions" => $clientApplicationType->actions,
            "pending_action" => $clientApplicationType->pending_action,
            "colour" => $clientApplicationType->colour
        ];
    }

    public function replaceFormContentIDsWithUUIDs($form) {


        $content = $form->content;
        $formattedContent = $form->formatted_content;

        foreach ($formattedContent as $page) {
            foreach ($page['blocks'] as $block) {
                foreach ($block['questions'] as $question) {


                    if (in_array($question['response_type'], ['file', 'signature'])) {
                        if(is_array($question['answer'])) {
                            $answer = [];
                            foreach ($question['answer'] as $index => $fileAnswer) {
                                if ($question['answer_per_engineer']) {
                                    $answer[$index] = $this->replaceModelAnswerWithUUID(File::class, $fileAnswer);
                                } else {
                                    array_push($answer, $this->replaceModelAnswerWithUUID(File::class, $fileAnswer));
                                }
                            }
                            $content[$question['slug']] = $answer;
                        }
                        else if(is_numeric($question['answer'])) {
                            if ($question['answer_per_engineer']) {
                                if (is_array($content[$question['slug']])) {
                                    $answer = [];
                                    foreach ($content[$question['slug']] as $index => $engineerAnswer) {
                                        $answer[$index] = $this->replaceModelAnswerWithUUID(File::class, $engineerAnswer);
                                    }
                                    $content[$question['slug']] = $answer;
                                }
                            } else {
                                $content[$question['slug']] = $this->replaceModelAnswerWithUUID(File::class, $question['answer']);
                            }
                        }
                    }
                    else if($question['response_type'] == 'controlled-document') {
                        if(is_array($question['answer'])) {
                            $answer = [];
                            foreach($question['answer'] as $subAnswer) {
                                array_push($answer, $this->replaceModelAnswerWithUUID(Document::class, $subAnswer));
                            }
                            $content[$question['slug']] = $answer;
                        }
                    }
                    else if($question['response_type'] == 'map-select') {
                        if(is_array($question['answer'])) {
                            $answer = [];
                            foreach($question['answer'] as $subKey => $subAnswer) {
                               $answer[$subKey] = $this->replaceModelAnswerWithUUID(InfrastructureAsset::class, $subAnswer, ignore:"Unknown Asset");
                            }
                            $content[$question['slug']] = $answer;
                        }
                    }
                    else if($question['response_type'] == 'floor-plan-select') {
                        if(is_array($question['answer'])) {
                            $answer = [];
                            foreach($question['answer'] as $subKey => $subAnswer) {
                               $answer[$subKey] = $this->replaceModelAnswerWithUUID(InfrastructureAsset::class, $subAnswer, ignore:"Unknown Asset");
                            }
                            $content[$question['slug']] = $answer;
                        }
                    }
                }
            }
        }

        return $content;
    }

    public function replaceModelAnswerWithUUID($model, $answer, $hidden = true, $trashed= true, $ignore = null) {
        if(!is_numeric($answer)) {
            return $answer;
        }

        if($ignore && $answer == $ignore) {
            return $answer;
        }

        return $model::query()
        ->when($hidden, fn($q) => $q->withHidden())
        ->when($trashed, fn($q) => $q->withTrashed())
        ->find($answer)?->uuid;
    }

    public function externalDeviceLocation($userActionLog) {
        if(!$userActionLog) {
            return null;
        }

        return [
            'accuracy' => $userActionLog->device_location_accuracy,
            'latitude' => $userActionLog->device_location?->getX(),
            'longitude' => $userActionLog->device_location?->getY(),
        ];
    }

    public function externalAssignmentGroup($taskContractor, $assignmentGroup, $steps = true) {

        $assets = [];
        $users = [];

        $assignmentGroup->loadMissing('assignments.assignable');
        foreach($assignmentGroup->assignments as $assignment) {
            if($assignment->assignable instanceof User) {
                array_push($users, $this->externalUser($assignment->assignable, $taskContractor));
            }
            else if($assignment->assignable instanceof Asset) {
                array_push($assets, $this->externalAsset($assignment->assignable));
            }
        }

        $externalAssignmentGroup = [
            'uuid'          => $assignmentGroup->uuid,
            'assignment_type' => $assignmentGroup->assignment_type,
            'task_uuid' => $taskContractor->linked_task_uuid, // external
            'work_type_uuid'  => $assignmentGroup->workType->uuid,
            'expected_start_date' => $assignmentGroup->expected_start_date?->format(Format::DATETIME->value),
            'expected_end_date' => $assignmentGroup->expected_end_date?->format(Format::DATETIME->value),
            'app_start_date' => $assignmentGroup->app_start_date?->format(Format::DATETIME->value),
            'app_end_date' => $assignmentGroup->app_end_date?->format(Format::DATETIME->value),
            'public_start_date' => $assignmentGroup->public_start_date?->format(Format::DATETIME->value),
            'public_end_date' => $assignmentGroup->public_end_date?->format(Format::DATETIME->value),
            'status_slug' => $assignmentGroup->status->slug,
            'ordered' => false, //just send as time mode, we'll just allow overlaps
            'assets' => $assets,
            'users' => $users,
            'steps' => $steps,
            "external_task_uuid" => $assignmentGroup->task->uuid //internal
        ];

        if($steps) {
            $steps = [];
            foreach($assignmentGroup->assignmentGroupSteps as $assignmentGroupStep) {
                array_push($steps, $this->externalAssignmentGroupStep($assignmentGroupStep, $taskContractor));
            }
            $externalAssignmentGroup['steps'] = $steps;
        }

        return $externalAssignmentGroup;
    }

    public function get($resource, $url, $data) {
        return $this->fetch($resource, 'get', $url, $data);
    }

    public function post($resource, $url, $data, File $file = null, bool $multiPartFlatten = false) {
        return $this->fetch($resource, 'post', $url, $data, $file, $multiPartFlatten);
    }

    public function patch($resource, $url, $data, File $file = null, bool $multiPartFlatten = false) {
        return $this->fetch($resource, 'patch', $url, $data, $file, $multiPartFlatten);
    }

    public function delete($resource, $url, $data) {
        return $this->fetch($resource, 'delete', $url, $data);
    }

    public function fetch($resource, $method, $url, $data, File $file = null, bool $multiPartFlatten = false) {

        logger(print_r([
            'method' => $method,
            'url' => str_replace('{+senses}', $this->getBaseUrl($resource), $url),
            'request' => json_encode($data),
            'file' => $file ? [ 'id' => $file->id, 'disk' => $file->disk, 'path' => $file->path] : null
        ], true));

        if($multiPartFlatten) {
            $data = $this->multiPartFlatten($data);
        }

        $client = $this->httpClient($resource);

        if($file && !$file->pending) {
            $client?->attach('file', Storage::disk($file->disk)->get($file->path), $file->name . '.' . $file->extension);
        }

        $response = $client?->$method($url, $data);

        $this->handleResponse($response);
        return $response;
    }

    public function resolveFileables($file, $resource)
    {
        $fileables = collect();
        $fileableRows = DB::table('fileables')->where('file_id', $file->id)->select('fileable_type', 'fileable_id')->get();

        $groupedFileableRows = $fileableRows->groupBy('fileable_type');
        foreach ($groupedFileableRows as $modelKey => $groupFileableRows) {

            $model = Relation::getMorphedModel($modelKey);

            $groupFileables = $model::whereIn('id', $groupFileableRows->pluck('fileable_id'))->pluck('uuid')->map(function ($uuid) use (&$modelKey, &$resource) {
                if ($modelKey == 'task' && !is_null($resource)) {
                    $uuid = $resource?->linked_task_uuid;
                }
                return ['fileable_type' => $modelKey, 'fileable_id' => $uuid];
            });
            $groupFileables = $groupFileables->filter(function ($fileable) {
                if (is_null($fileable['fileable_id'])) {
                    return false;
                }

                if ($fileable['fileable_type'] == 'document') {
                    return false;
                }

                return $fileable['fileable_type'] != 'floor-plan';
            });
            $fileables = $groupFileables->merge($groupFileables);
        }

        return $fileables;
    }

    public function convertFormStructure($structure) {

        if(isset($structure['pages'])) {
            foreach($structure['pages'] as $pageIndex => $page) {
                foreach($page['blocks'] as $blockIndex => $block) {
                    foreach($block['questions'] as $questionIndex => $question) {
                        if(isset($question['controlled_document_tags'])) {
                            $tagSlugs = [];
                            foreach($question['controlled_document_tags'] as $tagID) {
                                array_push($tagSlugs, $this->replaceTagIDWithSlug($tagID));
                            }
                            $structure['pages'][$pageIndex]['blocks'][$blockIndex]['questions'][$questionIndex]['controlled_document_tags'] = $tagSlugs;
                        }
                        if(isset($question['document_tags'])) {
                            $tagSlugs = [];
                            foreach($question['document_tags'] as $tagID) {
                                array_push($tagSlugs, $this->replaceTagIDWithSlug($tagID));
                            }
                            $structure['pages'][$pageIndex]['blocks'][$blockIndex]['questions'][$questionIndex]['document_tags'] = $tagSlugs;
                        }
                    }
                }
            }
        }


        return $structure;
    }

    public function replaceTagIDWithSlug($tagID, $hidden = true, $trashed= true, $ignore = null) {
        if(!is_numeric($tagID)) {
            return $tagID;
        }

        if($ignore && $tagID == $ignore) {
            return $tagID;
        }

        return Tag::query()
        ->when($hidden, fn($q) => $q->withHidden())
        ->when($trashed, fn($q) => $q->withTrashed())
        ->find($tagID)?->slug;
    }
}
