<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Http\Resources\ScheduleTaskResource;
use App\Models\Lead;
use App\Models\Role;
use App\Models\ScheduledTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class LeadsController extends Controller
{
    public function index() {
        if(auth()->user()->role_code==="super-admin") {
            $leads = Lead::all();
            $leads_collection = LeadResource::collection($leads);
            $formated_leads = $leads_collection->toArray(request());
            $warm_leads_count = Lead::where("degree_of_interest","Warm")->count();
            $hot_leads_count = Lead::where("degree_of_interest","Hot")->count();
            $cold_leads_count = Lead::where("degree_of_interest","Cold")->count();
            $converted_to_customers_count = Lead::where("is_customer","1")->count();
        } else {
            $leads = Lead::where("assigned_to",auth()->user()->user_id)->get();
            $leads_collection = LeadResource::collection($leads);
            $formated_leads = $leads_collection->toArray(request());
            $warm_leads_count = Lead::where("assigned_to",auth()->user()->user_id)->where("degree_of_interest","Warm")->count();
            $hot_leads_count = Lead::where("assigned_to",auth()->user()->user_id)->where("degree_of_interest","Hot")->count();
            $cold_leads_count = Lead::where("assigned_to",auth()->user()->user_id)->where("degree_of_interest","Cold")->count();
            $converted_to_customers_count = Lead::where("assigned_to",auth()->user()->user_id)->where("is_customer","1")->count();
        }


        return view('leads.leads-list', compact('formated_leads', 'warm_leads_count', 'hot_leads_count', 'cold_leads_count', 'converted_to_customers_count'));
    }

    public function create() {
        return view('leads.create');
    }

    // public function store(Request $request) {

    //     try {
    //         $lead = new Lead();

    //         $lead->name = $request->input("name");
    //         $lead->phone_numbers = $request->input("phone");
    //         $lead->email = $request->input("email");
    //         $lead->governorate = $request->input("governorate");
    //         $lead->interested_categories = implode(", ",$request->input("categories"));
    //         $lead->interested_products_skus = $request->input("skus");
    //         $lead->lead_id = Str::uuid()->toString();
    //         $lead->source = $request->input("source");
    //         $lead->degree_of_interest = $request->input("interest");
    //         $lead->next_follow_up_period = $request->input("next_follow_up");
    //         $lead->potential = "0";
    //         $lead->added_by = auth()->user()->user_id;
    //         $lead->assigned_to = auth()->user()->user_id;
    //         $lead->notes = $request->input("notes");
    //         $lead->is_customer = "0";

    //         $lead->save();


    //         return redirect()->route('leads.index')->with('success', 'Lead Created!');
    //     } catch (Throwable $th) {
    //         return redirect()->back()->with('error', $th->getMessage());
    //     }
    // }


    public function store(Request $request)
{
    // ✅ التحقق من البيانات الأساسية
    $validated = $request->validate([
        'name'        => 'required|string|max:255',
        'phone'       => 'required|string|max:20',
        'email'       => 'nullable|email',
        'governorate' => 'nullable|string|max:255',
    ]);

    try {
        // ✅ منع تكرار الـ Lead بناءً على رقم الهاتف
        $exists = Lead::where('phone_numbers', $validated['phone'])->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'هذا العميل موجود بالفعل ولا يمكن إضافته مرتين.');
        }

        // ✅ إنشاء Lead جديد
        $lead = new Lead();
        $lead->name                     = $validated['name'];
        $lead->phone_numbers            = $validated['phone'];
        $lead->email                    = $validated['email'] ?? '';
        $lead->governorate              = $request->input('governorate', '');
        $lead->interested_categories    = implode(', ', $request->input('categories', []));
        $lead->interested_products_skus = $request->input('skus', '');
        $lead->lead_id                  = \Illuminate\Support\Str::uuid()->toString();
        $lead->source                   = $request->input('source', '');
        $lead->degree_of_interest       = $request->input('interest', '');
        $lead->next_follow_up_period    = $request->input('next_follow_up', '');
        $lead->potential                = '0';
        $lead->added_by                 = auth()->user()->user_id;
        $lead->assigned_to              = auth()->user()->user_id;
        $lead->notes                    = $request->input('notes', '');
        $lead->is_customer              = '0';
        $lead->save();

        return redirect()
            ->route('leads.index')
            ->with('success', 'تم إنشاء العميل بنجاح.');
    } catch (\Throwable $th) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $th->getMessage());
    }
}


    public function edit($id) {
        $lead_id = $id;

        $user = Lead::where('lead_id', $lead_id)->first();
        $roles = Role::all();

        return view('leads.edit', compact("user", "roles"));
    }

    public function update(Request $request) {
        $user_id = $request->input('user_id');
        $name = $request->input('name');
        $email = $request->input('email');
        $role = $request->input('role');
        $password = bcrypt($request->input('password'));


        try {
            $uploadFolder = 'users';
            $image_uploaded_path = "";
            if($request->file('avatar')) {
                $image = $request->file('avatar');
                $image_uploaded_path = $image->store($uploadFolder, 'public');
            }
            DB::table("users")->where('user_id', $user_id)->update(
                [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'role_id' => $role,
                    'avatar' => $image_uploaded_path
                ]
            );


            return redirect()->route('users.index')->with('success', 'User updated!');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function destroy(Request $request) {
        $user_id = $request->input('user_id');
        $user = User::where('user_id', $user_id)->first();

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    public function tasks() {
        if(auth()->user()->role_code==="super-admin") {
            $tasks = ScheduledTask::all();
            $tasks_collection = ScheduleTaskResource::collection($tasks);
            $formated_tasks = $tasks_collection->toArray(request());
            $warm_leads_count = Lead::where("degree_of_interest","Warm")->count();
            $hot_leads_count = Lead::where("degree_of_interest","Hot")->count();
            $today_tasks_count = ScheduledTask::whereDate('task_date', today())->count();
            $tomorrow_tasks_count = ScheduledTask::whereDate('task_date', today()->addDay())->count();
        } else {
            $tasks = ScheduledTask::where("user_id",auth()->user()->user_id)->get();
            $tasks_collection = ScheduleTaskResource::collection($tasks);
            $formated_tasks = $tasks_collection->toArray(request());
            $warm_leads_count = Lead::where("assigned_to",auth()->user()->user_id)->where("degree_of_interest","Warm")->count();
            $hot_leads_count = Lead::where("assigned_to",auth()->user()->user_id)->where("degree_of_interest","Hot")->count();
            $today_tasks_count = ScheduledTask::whereDate('task_date', today())->where("user_id",auth()->user()->user_id)->count();
            $tomorrow_tasks_count = ScheduledTask::whereDate('task_date', today()->addDay())->where("user_id",auth()->user()->user_id)->count();
        }

        return view("leads.scheduled-tasks", compact('formated_tasks', 'warm_leads_count', 'hot_leads_count', 'today_tasks_count', 'tomorrow_tasks_count'));
    }

    public function reports() {
        return view("leads.reports");
    }

    public function taskDone(Request $request) {
        $task_id = $request->input("task_id");

        if(DB::table("scheduled_tasks")->where("id", $task_id)->exists()) {
            DB::table("scheduled_tasks")->where("id", $task_id)->update([
                "task_done" => "1",
                "complete_date" => now(),
            ]);

            return back()->with("success", "Task done successfully!");
        } else {
            return back()->with("error", "Task not found!");
        }
    }

    public function createTask() {
        $leads = Lead::where("assigned_to",auth()->user()->user_id)->get();
        return view("leads.create-task", compact("leads"));
    }

    public function storeTask(Request $request) {
        $lead_id = $request->input("lead");
        $user_id = $request->input("user_id");
        $task_date = $request->input("date");
        $task_done = "0";
        $complete_date = null;

        $task = new ScheduledTask();

        $task->lead_id = $lead_id;
        $task->user_id = $user_id;
        $task->task_date = $task_date;
        $task->task_done = $task_done;
        $task->complete_date = $complete_date;

        try {
            $result = $task->save();
            if($result) {
                return redirect()->route('leads.tasks')->with('success', 'Task created successfully!');
            } else {
                return back()->with("error", "Task not created!");
            }
        } catch (Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
