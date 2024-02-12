<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AsanaController extends Controller
{
    public static string $token = "2/1206237134658753/1206523070023323:0620093d69059b8e9f5e72d8e961251c";
    public static string $baseUrl = "https://app.asana.com/api/1.0/projects";

    public static function syncProjects (){
        $user = Auth::user();
        $projects = Http::withToken(self::$token)->get(self::$baseUrl);
        $projects = json_decode($projects);
        $projects = $projects->data;
        foreach ($projects as $project) {
            $existingProject = Project::where('project_name', $project->name)->first();
            if (!$existingProject) {
                $newProject = new Project();
                $newProject->gid = $project->gid;
                $newProject->project_name = $project->name;
                $newProject->status = "in_progress";
                $newProject->project_image = "asana.png";
                $newProject->created_by = $user->id;
                $newProject->start_date = now();
                $newProject->end_date = "2025-01-01";
                $newProject->budget = 0;
                $newProject->save();
                $newProjectUser = new ProjectUser();
                $newProjectUser->project_id = $newProject->id;
                $newProjectUser->user_id = $user->id;
                $newProjectUser->save();
            }
        }
    }
}

