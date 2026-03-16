<?php
// app/Traits/HandlesBulkActions.php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Http\Requests\Review\BulkActionRequest;

trait HandlesBulkActions
{
    /**
     * Handle bulk approval
     */
    protected function bulkApprove(BulkActionRequest $request, $model)
    {
        $status = $request->input('status', 1);
        $count = $model->whereIn('id', $request->review_ids)->update(['approved' => $status]);
        
        $action = $status ? 'approved' : 'disapproved';
        
        return $this->handleBulkResponse($request, $count, "{$count} reviews {$action} successfully.");
    }

    /**
     * Handle bulk delete
     */
    protected function bulkDelete(BulkActionRequest $request, $model)
    {
        $count = $model->whereIn('id', $request->review_ids)->delete();
        
        return $this->handleBulkResponse($request, $count, "{$count} reviews deleted successfully.");
    }

    /**
     * Handle bulk response
     */
    protected function handleBulkResponse(Request $request, int $count, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => $message
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}