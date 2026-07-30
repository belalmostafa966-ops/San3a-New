<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdentityFingerprint;
use App\Models\VerificationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    /**
     * رفع مستند (بطاقة / فيش وتشبيه / صورة liveness)
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doc_type' => 'required|in:national_id,criminal_record,liveness_selfie',
            'file' => 'required|file|max:10240', // 10MB
            'national_id_number' => 'required_if:doc_type,national_id|string',
            'card_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        // كشف الحسابات المكررة قبل قبول البطاقة
        if ($request->doc_type === 'national_id') {
            $duplicate = $this->checkDuplicateIdentity(
                $request->national_id_number,
                $request->card_number
            );

            if ($duplicate) {
                $user->update(['status' => 'suspended']);

                return response()->json([
                    'message' => 'تم اكتشاف حساب مسجل مسبقًا بنفس البيانات، تم إيقاف الحساب مؤقتًا',
                ], 403);
            }

            IdentityFingerprint::create([
                'user_id' => $user->id,
                'hashed_national_id' => IdentityFingerprint::hash($request->national_id_number),
                'hashed_card_number' => IdentityFingerprint::hash($request->card_number ?? $request->national_id_number),
            ]);
        }

        $path = $request->file('file')->store('verification-documents', 'private');

        $document = VerificationDocument::create([
            'user_id' => $user->id,
            'doc_type' => $request->doc_type,
            'file_path' => $path,
            'status' => 'pending',
        ]);

        // TODO: لو doc_type == liveness_selfie، هنا تنادي الـ Liveness API الخارجي
        // وتحدثي status على أساس الرد (approved/rejected) بدل ما تسيبيها pending دايمًا

        return response()->json([
            'message' => 'تم رفع المستند، في انتظار المراجعة',
            'document' => $document,
        ]);
    }

    private function checkDuplicateIdentity(?string $nationalId, ?string $cardNumber): bool
    {
        if (! $nationalId) {
            return false;
        }

        $hashedNationalId = IdentityFingerprint::hash($nationalId);
        $hashedCardNumber = IdentityFingerprint::hash($cardNumber ?? $nationalId);

        return IdentityFingerprint::where('hashed_national_id', $hashedNationalId)
            ->orWhere('hashed_card_number', $hashedCardNumber)
            ->exists();
    }
}
