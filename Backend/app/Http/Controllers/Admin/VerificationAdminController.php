<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerificationAdminController extends Controller
{
    /**
     * كل طلبات التوثيق المعلقة (للأدمن)
     */
    public function pending()
    {
        $documents = VerificationDocument::with('user')
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return response()->json($documents);
    }

    public function approve(Request $request, VerificationDocument $document)
    {
        $document->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'تم قبول المستند', 'document' => $document]);
    }

    public function reject(Request $request, VerificationDocument $document)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $document->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json(['message' => 'تم رفض المستند', 'document' => $document]);
    }
}
