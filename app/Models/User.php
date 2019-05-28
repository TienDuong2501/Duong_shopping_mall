<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Image;
use App\Models\Review;
use App\Models\Order;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
        'address',
        'phone',
        'point',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

     protected $appends = [
        'discount',
    ];

    public function getDiscountAttribute()
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
        if ($totalMoney >= 10000000 && $totalMoney <= 20000000) {
            return 5;
        } else if ($totalMoney >= 20000000 && $totalMoney <= 50000000) {
            return  10;
        } else if ($totalMoney >= 50000000) {
            return 15;
        } else {
            return 0;
        }
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'email', 'email');
    }

    public function scopeCountUser($query)
    {
        return $query->count();
    }

    public function scopeAllUser($query)
    {
        return $query->withTrashed()
                    ->orderBy('deleted_at')
                    ->orderBy('level', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function scopeAllActiveUser($query, $start_time, $end_time, $flag = 0)
    {
        if (!$flag) {
            return User::with(['orders' => function ($query) use ($start_time, $end_time) {
                $query->whereBetween('deliver_date', [$start_time, $end_time]);
            }])->orderBy('deleted_at')
                        ->orderBy('level', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        return User::with(['orders' => function ($query) use ($start_time) {
            $query->whereMonth('deliver_date', $start_time);
        }])->orderBy('deleted_at')
                    ->orderBy('level', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function scopeUserId($query, $email)
    {
        return $query->where('email', $email)->value('id');;
    }

    public function scopeUserWithImage($query, $id)
    {
        return $query->where('id', $id)->with('image')->get();
    }
}
