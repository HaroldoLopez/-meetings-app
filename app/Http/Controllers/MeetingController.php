<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Services\MeetingService;
use App\Models\Meeting;


class MeetingController extends BaseController
{

    public function index()
    {
        $meetings = Meeting::all();

        return response()->json(['meetings' => $meetings], 200);
    }

    public function store(Request $request)
    {
        // Valida los datos de entrada (puedes personalizar las reglas de validación)
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'meeting_name' => 'required|max:255',
        ]);

        // Crea una nueva reunión
        $meeting = Meeting::create([
            'user_id' => $request->input('user_id'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'meeting_name' => $request->input('meeting_name'),
        ]);

        // Devuelve una respuesta de éxito
        return response()->json(['message' => 'The meeting has been successfully booked.', 'meeting' => $meeting], 201);
    }

    public function destroy($id)
    {
        $meeting = Meeting::find($id);

        if (!$meeting) {
            return response()->json(['message' => 'Meeting not Found'], 404);
        }

        $meeting->delete();

        return response()->json(['message' => 'Meeting deleted successfully'], 200);
    }

    public function booking(Request $request)
    {
        $date_service = new MeetingService();

        $meeting_name = $request->input('meeting_name');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $users = $request->input('users');

        $meetings = $date_service->scheduleMeeting($meeting_name, $start_time, $end_time, $users);
        return response()->json($meetings);
    }
}
