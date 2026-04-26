<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\StudentReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class StudentReviewController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'review_text' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        // // اختياري: إذا بدك تمنعي أكثر من review لنفس الطالب على نفس المادة
        // $existingReview = StudentReview::where('user_id', $user->id)
        //     ->where('course_id', $course->id)
        //     ->first();

        // if ($existingReview) {
        //     throw ValidationException::withMessages([
        //         'review_text' => 'You have already submitted a review for this course.',
        //     ]);
        // }

        $apiResponse = Http::timeout(30)->post('http://127.0.0.1:8001/predict', [
            'text' => $request->review_text,
        ]);

        if (! $apiResponse->successful()) {
            throw ValidationException::withMessages([
                'review_text' => 'AI service is currently unavailable. Please try again later.',
            ]);
        }

        $prediction = $apiResponse->json();

        StudentReview::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'review_text' => $request->review_text,
            'predicted_label' => $prediction['prediction_label'] ?? 'unknown',
            'confidence_score' => $prediction['confidence'] ?? null,
        ]);

        return back()->with('success', 'Your review has been submitted successfully.');
    }
}
