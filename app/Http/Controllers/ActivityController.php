<?php

namespace App\Http\Controllers;

use App\Http\Helpers\RequestHelper;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    //
    public function getActivity(Request $request)
    {
        $subject_id = $request->route('subject_id');
        $audits = ActivityLog::where('subject_id', '=', $subject_id)->orderBy('created_at');
        return ActivityLogResource::collection($audits->paginate(RequestHelper::limit($request)));
    }
}
