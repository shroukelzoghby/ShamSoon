<?php

namespace App\Services;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Exception;

class FirebaseNotificationService
{

    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/shamsoon-5d661-76564d8307a7.json'));

        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification($token, $title, $body, $data = [], $userId = null)
    {
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(FirebaseNotification::create($title, $body))
                ->withData($data);

            $this->messaging->send($message);


            if ($userId) {
                Notification::create([
                    'user_id' => $userId,
                    'title' => $title,
                    'body' => $body,
                    'data' => $data,
                ]);
            }

            return true;
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Requested entity was not found') && $userId) {
                User::where('id', $userId)->update(['fcm_token' => null]);
            }
            throw new Exception("Failed to send notification: " . $e->getMessage());
        }
    }

    public function sendMulticastNotification($tokens, $title, $body, $data = [], $userIds = [])
    {
        try {
            if (empty($tokens) || empty($userIds)) {
                Log::warning("No valid tokens or user IDs provided for multicast notification");
                return false;
            }

            if (count($tokens) !== count($userIds)) {
                Log::error("Mismatch between tokens and user IDs", [
                    'token_count' => count($tokens),
                    'user_id_count' => count($userIds)
                ]);
                throw new Exception("Mismatch between tokens and user IDs");
            }

            $message = CloudMessage::new()
                ->withNotification(FirebaseNotification::create($title, $body))
                ->withData($data);

            $response = $this->messaging->sendMulticast($message, $tokens);

            foreach ($userIds as $index => $userId) {
                Notification::create([
                    'user_id' => $userId,
                    'title' => $title,
                    'body' => $body,
                    'data' => $data,
                ]);
                Log::info("Notification stored for user $userId: $title");
            }

            Log::info("Multicast notification sent to " . count($tokens) . " tokens: $title", ['response' => $response]);
            return true;
        } catch (Exception $e) {
            Log::error("Failed to send multicast notification: {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            throw new Exception("Failed to send multicast notification: " . $e->getMessage());
        }
    }
}
