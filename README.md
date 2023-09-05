
## About this proyect

This application is a technical test for scheduling meetings. It was developed in Laravel and PHP, creating a REST API for its better implementation and testing.

## Testing application

The application has different endpoints to test, an included file is a Postman collection called 'Meeting collection.postman_collection.json' for your testing. The endpoints are described below:

## Meetings

### Create a Meeting (Only for tests)
- **Route**: /meetings
- **HTTP Method**: POST
- **Controller**: MeetingController@store
- **Description**: Create a new meeting.

### List Meetings  (Only for tests)
- **Route**: /meetings
- **HTTP Method**: GET
- **Controller**: MeetingController@index
- **Description**: Get a list of meetings.

### Delete a Meeting  (Only for tests)
- **Route**: /meetings/{id}
- **HTTP Method**: DELETE
- **Controller**: MeetingController@destroy
- **Description**: Delete a meeting by its ID.

### Book a Meeting (This is the endpoint where the required function for the technical test is included)
- **Route**: /meetings/booking
- **HTTP Method**: POST
- **Controller**: MeetingController@booking
- **Description**: Book a meeting.

## Users

### Create a User
- **Route**: /users
- **HTTP Method**: POST
- **Controller**: UserController@store
- **Description**: Create a new user.

### List Users
- **Route**: /users
- **HTTP Method**: GET
- **Controller**: UserController@index
- **Description**: Get a list of users.

### Get User by ID
- **Route**: /users/{id}
- **HTTP Method**: GET
- **Controller**: UserController@show
- **Description**: Get user details by ID.

### Update User
- **Route**: /users/{id}
- **HTTP Method**: PUT
- **Controller**: UserController@update
- **Description**: Update user information by ID.

### Delete User
- **Route**: /users/{id}
- **HTTP Method**: DELETE
- **Controller**: UserController@destroy
- **Description**: Delete user by ID.

## Solving test

An endpoint was created to visualize the result of the function; you should include the following body parameters for example:

```
{
  "meeting_name":"meetingName",
  "start_time":"2023-09-10 11:00:00",
  "end_time":"2023-09-11 08:30:00",
  "users":[1,2]
}
```

Within the endpoint's controller, a function called 'booking' was included, which calls a service named 'MeetingService' where the function for the technical test named 'scheduleMeeting' exists. This function includes another function called 'overlappingMeetings' that performs the validations discussed for scheduling meetings, resolving meetings scheduled within a user-defined time frame and user assignments.

```
public static function overlappingMeetings($startDateTime, $endDateTime, $userId) {
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
```

The validations that were included are:

* The user-entered end date cannot be earlier than the user-entered start date.
* If there are one or more meetings already scheduled within the date range.
* If there is a meeting that starts before the initial date but ends within the specified date range.
* If there is a meeting that starts within the specified date range but ends after the user-indicated end date.
* If there is already a meeting scheduled at the exact time the user is scheduling the meeting.
* If there is a meeting that starts or ends right at the time the user needs to schedule.
* If there is already a meeting, and the user enters a date range shorter than the duration of the scheduled session.
