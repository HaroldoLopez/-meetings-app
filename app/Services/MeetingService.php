<?php

namespace App\Services;

use App\Models\Meeting;

class MeetingService
{
    public function scheduleMeeting($meetingName, $startDateTime, $endDateTime, $userIds)
    {
        $response = "";
        $flagMeeting = true;

        if(strtotime($startDateTime) < strtotime($endDateTime)){
            foreach($userIds as $userId) {
                $overlappingMeetings = Meeting::overlappingMeetings($startDateTime, $endDateTime, $userId);
                if(!$overlappingMeetings->isEmpty()){
                    $flagMeeting = false;
                    $response .= "User $userId has a conflicting meeting: $meetingName<br/>";
                }
            }

            if(!$flagMeeting){
                return $response."The meeting has not been booked.";
            }else {
                return "The meeting has been successfully booked.";
            }
        }else
            return "The meeting has not been booked. Please validate the dates";
    }
}
