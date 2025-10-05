<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use App\Exports\OrdersExport;
use App\Exports\LeadsExport;
use App\Exports\UsersExport;
use App\Exports\ScheduledTasksExport;
use App\Imports\ProductsImport;
use App\Imports\OrdersImport;
use App\Imports\LeadsImport;
use App\Imports\UsersImport;
use App\Imports\ScheduledTasksImport;
use Illuminate\Support\Facades\Storage;

class ImportExportController extends Controller
{
    /**
     * Export data to Excel
     */
    public function export(Request $request)
    {
        $type = $request->get('type');
        $filters = $request->except(['type', '_token']);

        switch ($type) {
            case 'products':
                return Excel::download(new ProductsExport($filters), 'products_' . date('Y-m-d_H-i-s') . '.xlsx');

            case 'orders':
                return Excel::download(new OrdersExport($filters), 'orders_' . date('Y-m-d_H-i-s') . '.xlsx');

            case 'leads':
                return Excel::download(new LeadsExport($filters), 'leads_' . date('Y-m-d_H-i-s') . '.xlsx');

            case 'users':
                return Excel::download(new UsersExport($filters), 'users_' . date('Y-m-d_H-i-s') . '.xlsx');

            case 'scheduled_tasks':
                return Excel::download(new ScheduledTasksExport($filters), 'scheduled_tasks_' . date('Y-m-d_H-i-s') . '.xlsx');

            default:
                return redirect()->back()->with('error', 'Invalid export type.');
        }
    }

    /**
     * Import data from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'type' => 'required|in:products,orders,leads,users,scheduled_tasks'
        ]);

        $type = $request->get('type');
        $file = $request->file('file');

        try {
            switch ($type) {
                case 'products':
                    Excel::import(new ProductsImport, $file);
                    break;

                case 'orders':
                    Excel::import(new OrdersImport, $file);
                    break;

                case 'leads':
                    Excel::import(new LeadsImport, $file);
                    break;

                case 'users':
                    Excel::import(new UsersImport, $file);
                    break;

                case 'scheduled_tasks':
                    Excel::import(new ScheduledTasksImport, $file);
                    break;

                default:
                    return redirect()->back()->with('error', 'Invalid import type.');
            }

            return redirect()->back()->with('success', ucfirst($type) . ' imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download sample template
     */
    public function downloadTemplate(Request $request)
    {
        $type = $request->get('type');

        switch ($type) {
            case 'products':
                return Excel::download(new ProductsExport([]), 'products_template.xlsx');

            case 'orders':
                return Excel::download(new OrdersExport([]), 'orders_template.xlsx');

            case 'leads':
                return Excel::download(new LeadsExport([]), 'leads_template.xlsx');

            case 'users':
                return Excel::download(new UsersExport([]), 'users_template.xlsx');

            case 'scheduled_tasks':
                return Excel::download(new ScheduledTasksExport([]), 'scheduled_tasks_template.xlsx');

            default:
                return redirect()->back()->with('error', 'Invalid template type.');
        }
    }
}
