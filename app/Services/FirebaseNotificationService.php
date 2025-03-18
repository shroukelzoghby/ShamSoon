<?php

namespace App\Services;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;

class FirebaseNotificationService
{

    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/shamsoon-f7732-565282bc1e72.json'));

        $this->messaging = $factory->createMessaging();
    }

    public function sendMulticastNotification(array $tokens, $title, $body, $data = [])
    {
        try {
            $messages = [];
            foreach ($tokens as $token) {
                $messages[] = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create($title, $body))
                    ->withData($data);
            }

            $this->messaging->sendAll($messages);
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to send notification: " . $e->getMessage());
        }
    }
}
