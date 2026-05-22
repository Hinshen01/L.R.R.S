<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'status' => 'nullable|in:submitted,missing,expiring_soon',
            'expiration_date' => 'nullable|date',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('requirements/' . $request->user()->id, 'public');
        }

        return $request->user()->requirements()->create([
            'document_name' => $request->document_name,
            'status' => $request->status ?? 'missing',
            'file_path' => $filePath,
            'expiration_date' => $request->expiration_date,
        ]);
    }

    public function update(Request $request, Requirement $requirement)
    {
        $request->validate([
            'document_name' => 'required|string',
            'status' => 'nullable|in:submitted,missing,expiring_soon',
            'expiration_date' => 'nullable|date',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif',
        ]);

        $data = [
            'document_name' => $request->document_name,
            'status' => $request->status ?? $requirement->status,
            'expiration_date' => $request->expiration_date ?? $requirement->expiration_date,
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if ($requirement->file_path && Storage::disk('public')->exists($requirement->file_path)) {
                Storage::disk('public')->delete($requirement->file_path);
            }

            // Store new file
            $file = $request->file('file');
            $data['file_path'] = $file->store('requirements/' . $request->user()->id, 'public');
        }

        $requirement->update($data);
        return $requirement;
    }

    public function destroy(Requirement $requirement)
    {
        // Delete file
        if ($requirement->file_path && Storage::disk('public')->exists($requirement->file_path)) {
            Storage::disk('public')->delete($requirement->file_path);
        }

        $requirement->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
