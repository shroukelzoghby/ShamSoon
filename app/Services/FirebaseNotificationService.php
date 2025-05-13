<?php

namespace App\Services;
use App\Models\Notification;
use App\Models\User;
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

    public function sendMulticastNotification(array $tokens, $title, $body, $data = [], $userIds = [])
    {
        try {
            $messages = [];
            foreach ($tokens as $index => $token) {
                $messages[] = CloudMessage::withTarget('token', $token)
                    ->withNotification(FirebaseNotification::create($title, $body))
                    ->withData($data);


                if (isset($userIds[$index])) {
                    Notification::create([
                        'user_id' => $userIds[$index],
                        'title' => $title,
                        'body' => $body,
                        'data' => $data,
                    ]);
                }
            }

            $this->messaging->sendAll($messages);
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to send notification: " . $e->getMessage());
        }
    }
}
