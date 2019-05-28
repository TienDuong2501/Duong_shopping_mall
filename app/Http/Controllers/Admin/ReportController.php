<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function getReport() {
        $now = Carbon::parse(Carbon::now());
        $start = $now->year - 1;
        $currentYear = $now->year;

        $products = Product::getAllProduct($currentYear, $start, 1);
        $pricipal = 0;
        $formatPrincipal = 0;
        $NoInputProduct = 0;

        if ($products) {
            foreach ($products as $product) {
                $NoInputProduct += $product->amount;
                $pricipal += (int)$product->price * $product->amount;
            }

            $formatPrincipal = number_format($pricipal, 0, '', '.');
        }

        $orders = Order::finishedOrders($currentYear, $start, 1);
        $revenue = 0;
        $formatRevenue = 0;
        $NoOutputProduct = 0;
        $soldProduct = [];
        if ($orders) {
            foreach ($orders as $order) {
                foreach ($order->orderDetails as  $orderDetail) {
                    if (array_key_exists($orderDetail->product_id, $soldProduct)) {
                        $soldProduct[$orderDetail->product_id] += (int)$orderDetail->amount;
                    } else {
                        $soldProduct[$orderDetail->product_id] = (int)$orderDetail->amount;
                    }
                    $NoOutputProduct += $orderDetail->amount;
                }
                $revenue += (int)$order->total_price;
            }

            $formatRevenue = number_format($revenue, 0, '', '.');

        }
        if (count($soldProduct)) {
            $highestAmount = max($soldProduct);
            $id = array_keys($soldProduct, $highestAmount);
            $product = Product::find($id[0])->name;
        } else {
            $highestAmount = 0;
            $product = null;
        }

        return view('admin.report.revenue', compact('start', 'currentYear', 'formatRevenue', 'formatPrincipal', 'NoOutputProduct', 'NoInputProduct', 'product', 'highestAmount'));
    }

    public function ShowRevenue(Request $request) {
        $start = $this->createDate($request->revenueRange, 0, 19);
        $end = $this->createDate($request->revenueRange, 21, 30);

        $products = Product::getAllProduct($start, $end);
        $pricipal = 0;
        $formatPrincipal = 0;
        $NoInputProduct = 0;

        if ($products) {
            foreach ($products as $product) {
                $NoInputProduct += $product->amount;
                $pricipal += (int)$product->price * $product->amount;
            }

            $formatPrincipal = number_format($pricipal, 0, '', '.');
        }

        $orders = Order::finishedOrders($start, $end);
        $revenue = 0;
        $formatRevenue = 0;
        $NoOutputProduct = 0;
        $soldProduct = [];
        if($orders) {
            foreach ($orders as $order) {
                foreach ($order->orderDetails as  $orderDetail) {
                    if (array_key_exists($orderDetail->product_id, $soldProduct)) {
                        $soldProduct[$orderDetail->product_id] += (int)$orderDetail->amount;
                    } else {
                        $soldProduct[$orderDetail->product_id] = (int)$orderDetail->amount;
                    }
                    $NoOutputProduct += $orderDetail->amount;
                }
                $revenue += (int)$order->total_price;
            }

            $formatRevenue = number_format($revenue, 0, '', '.');

        }
        // $highestAmount = max($soldProduct);
        // $id = array_keys($soldProduct, $highestAmount);
        // $product = Product::find($id[0]);

        if (count($soldProduct)) {
            $highestAmount = max($soldProduct);
            $id = array_keys($soldProduct, $highestAmount);
            $product = Product::find($id[0])->name;
        } else {
            $highestAmount = 0;
            $product = null;
        }

        return view('admin.report.revenue', compact('start', 'end', 'formatRevenue', 'formatPrincipal', 'NoOutputProduct', 'NoInputProduct', 'product', 'highestAmount'));
    }

    public function getCustomers()
    {
        $now = Carbon::parse(Carbon::now());
        $start = $now->year - 1;
        $currentMonth = $now->month;
        $monthFormat = $now->englishMonth;
        $maxNumber = 0;
        $totalMoney = 0;
        $potential = null;

        $users = User::AllActiveUser($currentMonth, $start, 1);
        $count = [];
        foreach ($users as $key => $user) {
            $count[$key] = $user->orders->count();
        }
        $maxNumber = max($count);

        foreach ($users as $key => $user) {

            if ($user->orders->count() == $maxNumber) {
                $potential = $user;
                foreach ($user->orders as $key => $order) {
                    $totalMoney += $order->total_price;
                }
            }
        }
        $formatTotalMoney = number_format($totalMoney, 0, '', '.');

        return view('admin.report.potentialCustomer', compact('start', 'monthFormat', 'formatTotalMoney', 'maxNumber', 'potential'));
    }

    public function showCustomers(Request $request)
    {
        $start = $this->createDate($request->revenueRange, 0, 19);
        $end = $this->createDate($request->revenueRange, 21, 30);
        $maxNumber = 0;
        $totalMoney = 0;
        $potential = null;

        $users = User::AllActiveUser($start, $end, 0);
        $count = [];
        foreach ($users as $key => $user) {
            $count[$key] = $user->orders->count();
        }
        $maxNumber = max($count);

        foreach ($users as $key => $user) {

            if ($user->orders->count() == $maxNumber && $maxNumber != 0) {
                $potential = $user;
                foreach ($user->orders as $key => $order) {
                    $totalMoney += $order->total_price;
                }
            }
        }
        $formatTotalMoney = number_format($totalMoney, 0, '', '.');

        return view('admin.report.potentialCustomer', compact('start', 'end', 'formatTotalMoney', 'maxNumber', 'potential'));
    }

    public function createDate($string, $start, $end)
    {
        $sub = substr((string) $string, $start, $end);
        $date = date('Y-m-d', strtotime($sub));

        return $date;
    }
}
