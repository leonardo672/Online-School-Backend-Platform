<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HandlesCourseResponse
{
    /**
     * Return success response with redirect.
     */
    protected function successResponse(string $message, string $route)
    {
        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Return error response.
     */
    protected function errorResponse(string $message, string $route = null)
    {
        if ($route) {
            return redirect()->route($route)->with('error', $message);
        }
        
        return back()->with('error', $message);
    }

    /**
     * Determine if request expects JSON response.
     */
    protected function expectsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->wantsJson();
    }

    /**
     * Return JSON response.
     */
    protected function jsonResponse($data, int $status = 200)
    {
        return response()->json($data, $status);
    }
}