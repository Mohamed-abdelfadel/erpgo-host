<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectUser;
use App\Models\User;
use AWS\CRT\HTTP\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use function Aws\flatmap;
use function Sodium\add;

class AsanaTaskController extends Controller
{
    public static string $token = "2/1206237134658753/1206523070023323:0620093d69059b8e9f5e72d8e961251c";
    public static string $baseUrl = "https://app.asana.com/api/1.0";
    public static function syncTasks()
    {
        $projects = Project::query()->get();
        foreach ($projects as $project){
            $tasks =  self::getTasksFromAsanaWithProjectGid($project->gid);
            foreach ($tasks as $task)
                if ($task){
                    self::createNewTask($task,$project->id);
                }
        }

    }
    public static function syncTasksForProject($project){
           $tasks =  self::getTasksFromAsanaWithProjectGid($project->gid);
           foreach ($tasks as $task)
           if ($task){
               self::createNewTask($task,$project->id);
           }

    }
    public static function checkTaskSync($project)
    {
        $currentLastTask = ProjectTask::query()->where('project_id', $project->id)->orderBy('gid', 'desc')->first();
        $lastTask = self::getLastTask($project['gid']);
        if ($currentLastTask){
            return $lastTask['gid'] != $currentLastTask->gid;
        }
        else{
           return true;
        }
    }
    public static function getLastTask($project)
    {
        $lastTask = Http::withToken(self::$token)->get(self::$baseUrl . "/tasks?limit=1&project=$project&opt_fields=name,assignee,completed_at,due_on,created_at,permalink_url");
    return $lastTask['data'][0];
    }
    public static function getTasksFromAsanaWithProjectGid($project)
    {
        if ($project){
            $tasks = Http::withToken(self::$token)->get(self::$baseUrl . "/tasks?project=$project&opt_fields=name,assignee,completed_at,due_on,created_at,permalink_url");
            $tasksData = $tasks['data'];
            usort($tasksData, function($a, $b) {
                return strcmp($a['gid'], $b['gid']);
            });
            return $tasksData;
        }
    }
    public static function createNewTask($task,$project_id)
    {
        $existingTask = ProjectTask::where('gid', $task['gid'])->first();
        $user = null;
        if ($task['assignee']){
            $user = User::query()->where('gid',$task['assignee']['gid'])->first();
        }
        if (!$existingTask) {
            $newTask = new ProjectTask();
            $newTask->gid = $task['gid'];
            $newTask->name = $task['name'];
            $newTask->description = "This task imported from asana";
            $newTask->start_date = $task['created_at'];
            $newTask->end_date = $task['due_on'];
            $newTask->completed_at =$task['completed_at'];
            $newTask->is_complete = !$task['completed_at'];
            $newTask->priority = "low";
            $newTask->assign_to = optional($user)->id;
            $newTask->project_id = $project_id;
            $newTask->stage_id = 1;
            $newTask->save();
        }
    }
}

