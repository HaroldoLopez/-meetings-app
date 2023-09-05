<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Meeting extends Model
{
    protected $fillable = ['user_id', 'start_time', 'end_time', 'meeting_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function overlappingMeetings($startDateTime, $endDateTime, $userId)
    {
        $overlappingMeetings = DB::table('meetings as m')
            ->where('m.user_id', $userId)
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('m.start_time', [$startDateTime, $endDateTime])
                    ->whereBetween('m.end_time', [$startDateTime, $endDateTime]);
            })
            ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('m.start_time', '<', $startDateTime)
                    ->where('m.end_time', '<=', $endDateTime)
                    ->where('m.end_time', '>=', $startDateTime);
            })

            ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('m.start_time', '>=', $startDateTime)
                    ->where('m.end_time', '>', $endDateTime)
                    ->where('m.start_time', '<', $endDateTime)
                    ->where('m.start_time', '>', $startDateTime);
            })

            ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('m.start_time', '<', $startDateTime)
                    ->where('m.end_time', '>', $endDateTime);
            })->get();

        return $overlappingMeetings;
    }
}
