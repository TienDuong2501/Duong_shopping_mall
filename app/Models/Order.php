<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\OrderDetail;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'email',
        'name',
        'address',
        'phone',
        'purchase_date',
        'deliver_date',
        'state',
    ];

    protected $appends = [
        'total_price',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function getTotalPriceAttribute()
    {
        $promotions = [];
        foreach ($this->orderDetails as $orderDetail) {
            $promotion = Product::find($orderDetail->product_id)->promotionDetail;
            if ($promotion) {
                array_push($promotions, $promotion->percent);
            } else {
                array_push($promotions, config('custom.defaultZero'));
            }
        }

        $totalOrder = 0;
        foreach ($this->orderDetails as $key => $item) {
            $totalOrder += ceil(($item['product']['price'] * $item->amount) * (100 - $promotions[$key]) / 100);
        }

        // return number_format($totalOrder, 0, '', '.');
        return $totalOrder;
    }


    public function scopeCountOrder($query)
    {
    	return $query->where('state', config('custom.defaultZero'))->count();
    }

    public function scopeAllOrders($query)
    {
        return $query->withTrashed()
                    ->orderBy('state')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function scopeFinishedOrders($query, $start_time, $end_time, $flag = 0)
    {
        if ($flag) {
            return $query->withTrashed()
                    ->with('orderDetails')
                    ->whereYear('deliver_date', $start_time)
                    ->orderBy('state')
                    ->orderBy('created_at', 'desc')
                    ->get();
        }

        return $query->withTrashed()
                    ->with('orderDetails')
                    ->whereBetween('deliver_date', [$start_time, $end_time])
                    ->orderBy('state')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function scopeOrderFind($query, $id)
    {
        return $query->find($id);
    }

    public function scopeOrderCheck($query, $email)
    {
        return $query->where('email', $email)
                    ->where('state', config('custom.defaultZero'));
    }

    public function scopeWithOrderDetail($query, $email)
    {
        return $query->with('orderDetails')->where('email', $email);
    }
}
