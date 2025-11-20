<?php

namespace App\Http\Controllers;

use App\Services\TelegramAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TelegramWebhookController extends Controller
{
    protected TelegramAssignmentService $assignmentService;

    public function __construct(TelegramAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * REST API endpoint - Dashboard form မှ user assignment
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function assignUserFromTelegram(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'uid' => 'required|string|size:6',
            'telegram_username' => 'required|string|min:2|max:100',
        ]);

        $result = $this->assignmentService->assignUserToAdminByTelegram(
            $validated['uid'],
            $validated['telegram_username']
        );

        return response()->json(
            $result,
            $result['success'] ? 200 : 400
        );
    }

    /**
     * User အား admin မှ ဖြုတ်ခြင်း
     */
    public function unassignUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'uid' => 'required|string|size:6',
        ]);

        $result = $this->assignmentService->unassignUserFromAdmin($validated['uid']);

        return response()->json(
            $result,
            $result['success'] ? 200 : 400
        );
    }
}
