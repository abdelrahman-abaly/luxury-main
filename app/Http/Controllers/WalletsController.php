<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SalaryWallet;
use App\Models\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletsController extends Controller
{
    public function show() {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $balance_received_data = TransactionHistory::where("status", config("constants.Paid"))
            ->where("user_id", auth()->user()->user_id)
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->pluck("new_balance")
            ->all();

        $balance_pending_data = TransactionHistory::where("status", config("constants.Pending Approval"))
            ->where("user_id", auth()->user()->user_id)
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->pluck("new_balance")
            ->all();

        $borrowed_balance_data = TransactionHistory::where("type", config("constants.Borrowing Request"))
            ->where("user_id", auth()->user()->user_id)
            ->where("status", config("constants.Paid"))
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->pluck("new_balance")
            ->all();

        $balance_received = 0;
        $balance_pending = 0;
        $borrowed_balance = 0;

        foreach ($balance_received_data as $balance) {
            $balance_received += $balance;
        }

        foreach ($balance_pending_data as $balance) {
            $balance_pending += $balance;
        }

        foreach ($borrowed_balance_data as $borrowed) {
            $borrowed_balance += $borrowed;
        }


        // get the current employee salary
        $salary_data = SalaryWallet::where("user_id", auth()->user()->user_id)->get();

        // get the on Processing Data from Orders Related to current Employee ONLY !
        $onProcessingData = Order::where("employee_id", auth()->user()->user_id)->where("status", config("constants.PROCESSING"))
            ->whereYear('created_at', $currentYear)->whereMonth('created_at', $currentMonth)->get();

        // get the Pending Data from Orders Related to current Employee ONLY !
        $pendingData = Order::where("employee_id", auth()->user()->user_id)->where("status", config("constants.PENDING"))
            ->whereYear('created_at', $currentYear)->whereMonth('created_at', $currentMonth)->get();

        // get the Ready to Pay Data from Orders Related to current Employee ONLY !
        $readyToPayData = Order::where("employee_id", auth()->user()->user_id)->where("status", config("constants.DELIVERED"))
            ->whereYear('created_at', $currentYear)->whereMonth('created_at', $currentMonth)->get();

        // get the Completed Data from Orders Related to current Employee ONLY !
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $completedData = Order::where("employee_id", auth()->user()->user_id)->where("status", config("constants.DELIVERED"))->whereDate('updated_at', '>=', $thirtyDaysAgo)->get();

        $onProcessingOrdersCount = count($onProcessingData);
        $pendingOrdersCount = count($pendingData);
        $readyToPayOrdersCount = count($readyToPayData);
        $completedOrdersCount = count($completedData);

        $onProcessingTotalCommission = 0;
        $pendingTotalCommission = 0;
        $readyToPayTotalCommission = 0;
        $completedTotalCommission = 0;
        foreach ($onProcessingData as $onProcessing) {
            $onProcessingTotalCommission += $onProcessing->employee_commission;
        }
        foreach ($pendingData as $pending) {
            $pendingTotalCommission += $pending->employee_commission;
        }
        foreach ($readyToPayData as $readyToPay) {
            $readyToPayTotalCommission += $readyToPay->employee_commission;
        }
        foreach ($completedData as $completed) {
            $completedTotalCommission += $completed->employee_commission;
        }


        $transactions = TransactionHistory::where("user_id", auth()->user()->user_id)->get();


        return view('wallets.my-wallet', compact('balance_received',
            'balance_pending', 'borrowed_balance', 'salary_data', 'onProcessingTotalCommission', 'completedTotalCommission',
                        'readyToPayTotalCommission', 'pendingTotalCommission', 'onProcessingOrdersCount'
                         , 'pendingOrdersCount', 'readyToPayOrdersCount', 'completedOrdersCount', 'transactions'));
    }

    public function salaryCalculate() {
        $months = [
            "January", "February", "March",
            "April", "May", "June", "July",
            "August", "September", "October", "November", "December"
        ];

        $years = range(2020, 2040);

        return view('wallets.salary-calculate', compact('months', 'years'));
    }

    public function postSalaryCalculate(Request $request) {
        $user_id = $request->input('user_id');
        $year = $request->input('year');
        $month = $request->input('month');
        $days_worked = $request->input('days_worked');
        $salary_wallet = request('salary_wallet');


        $borrowed_balance_data = TransactionHistory::where("type", config("constants.Borrowing Request"))
            ->where("user_id", auth()->user()->user_id)
            ->where("status", config("constants.Paid"))
            ->whereYear('updated_at', $year)
            ->whereMonth('updated_at', $month)
            ->pluck("new_balance")
            ->all();
        $borrowed_balance = 0;
        foreach ($borrowed_balance_data as $borrowed) {
            $borrowed_balance += $borrowed;
        }

        $ready_salary = $salary_wallet - $borrowed_balance;

        $salary = new SalaryWallet();

        $salary->user_id = $user_id;
        $salary->year = $year;
        $salary->month = $month;
        $salary->days_worked = $days_worked;
        $salary->salary_wallet = $salary_wallet;
        $salary->borrowing_balance = $borrowed_balance;
        $salary->ready_salary = $ready_salary;

        try {
            $result = $salary->save();
            if($result) {
                return back()->with("success", "Salary Calculate Successfully");
            } else {
                return back()->with("error", "Salary Calculate Failed");
            }
        } catch (\Throwable $e) {
            return back()->with("error", $e->getMessage());
        }
    }

    public function borrowRequest(Request $request) {
        $user_id = $request->input('user_id');
        $transaction_id = "TX" . random_int(10000000, 99999999);
        $type = config("constants.Borrowing Request");
        $send_date = date("F j, Y");
        $status = config("constants.Pending Approval");
        $amount = $request->input('amount');

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $borrowed_balance_data = TransactionHistory::where("type", config("constants.Borrowing Request"))
            ->where("user_id", auth()->user()->user_id)
            ->where("status", config("constants.Paid"))
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->pluck("new_balance")
            ->all();

        $borrowed_balance = 0;

        foreach ($borrowed_balance_data as $borrowed) {
            $borrowed_balance += $borrowed;
        }

        $balance = $borrowed_balance;
        $new_balance = intval($amount) + intval($balance);
        if($new_balance > 10000) {
            return back()->with("error", "Borrowing Balance Exceeded");
        }

        $transaction_history = new TransactionHistory();

        $transaction_history->user_id = $user_id;
        $transaction_history->transaction_id = $transaction_id;
        $transaction_history->type = $type;
        $transaction_history->send_date = $send_date;
        $transaction_history->status = $status;
        $transaction_history->amount = $amount;
        $transaction_history->balance = $balance;
        $transaction_history->new_balance = $new_balance;

        try {
            $result = $transaction_history->save();
            if($result) {
                return back()->with("success", "Borrowing Request Submit Successfully");
            } else {
                return back()->with("error", "Borrowing Request Submit Failed");
            }
        } catch (\Throwable $e) {
            return back()->with("error", $e->getMessage());
        }
    }

    public function commissionWithdrawalRequest(Request $request) {
        $user_id = $request->input('user_id');
        $transaction_id = "TX" . random_int(10000000, 99999999);
        $type = config("constants.Commission Request");
        $send_date = date("F j, Y");
        $status = config("constants.Pending Approval");
        $amount = $request->input('amount');
        $balance = $request->input('balance');
        $new_balance =  intval($balance) - intval($amount);

        if($amount > $balance) {
            return back()->with("error", "You don't have enough balance");
        }

        $transaction_history = new TransactionHistory();

        $transaction_history->user_id = $user_id;
        $transaction_history->transaction_id = $transaction_id;
        $transaction_history->type = $type;
        $transaction_history->send_date = $send_date;
        $transaction_history->status = $status;
        $transaction_history->amount = $amount;
        $transaction_history->balance = $balance;
        $transaction_history->new_balance = $new_balance;

        try {
            $result = $transaction_history->save();
            if($result) {
                return back()->with("success", "Commission Withdrawal Request Submit Successfully");
            } else {
                return back()->with("error", "Commission Withdrawal Request Submit Failed");
            }
        } catch (\Throwable $e) {
            return back()->with("error", $e->getMessage());
        }
    }

    public function approveRequest(Request $request) {
        $transaction_id = $request->input('id');

        DB::table("transactions_history")->where("transaction_id", $transaction_id)->update(["status" => config("constants.Paid")]);

        return back()->with("success", "Request Approved Successfully");
    }

    public function rejectRequest(Request $request) {
        $transaction_id = $request->input('id');

        DB::table("transactions_history")->where("transaction_id", $transaction_id)->update(["status" => config("constants.Rejected")]);

        return back()->with("success", "Request Rejected Successfully");
    }
}
