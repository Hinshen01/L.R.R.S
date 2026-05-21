<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Interview;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->interviews()->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'interview_date' => 'required|date',
            'company_name' => 'required|string',
            'venue' => 'required|string',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);
        return $request->user()->interviews()->create($request->all());
    }

    public function update(Request $request, Interview $interview)
    {
        $interview->update($request->all());
        return $interview;
    }

    public function destroy(Interview $interview)
    {
        $interview->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
