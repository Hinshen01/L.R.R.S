<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Requirement;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->requirements()->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_name' => 'required|string',
            'status' => 'required|in:submitted,missing,expiring_soon',
            'expiration_date' => 'nullable|date',
        ]);
        return $request->user()->requirements()->create($request->all());
    }

    public function update(Request $request, Requirement $requirement)
    {
        $requirement->update($request->all());
        return $requirement;
    }

    public function destroy(Requirement $requirement)
    {
        $requirement->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
