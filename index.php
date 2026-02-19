<?php
// CONFIGURATION
// 1. REGENERATE your token in Facebook Developers and paste the NEW one here.
$access_token = 'REPLACE_THIS_WITH_YOUR_NEW_ACCESS_TOKEN'; 
$verify_token = 'Moz1304'; 

// 1. WEBHOOK VERIFICATION
if (isset($_GET['hub_mode']) && isset($_GET['hub_verify_token'])) {
    if ($_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === $verify_token) {
        echo $_GET['hub_challenge'];
        http_response_code(200);
        exit;
    } else {
        http_response_code(403);
        exit;
    }
}

// 2. RECEIVE MESSAGES
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data && isset($data['entry'][0]['messaging'][0])) {
    $event = $data['entry'][0]['messaging'][0];
    
    // Check if it is a text message and has a sender
    if (isset($event['sender']['id']) && isset($event['message']['text'])) {
        $sender_id = $event['sender']['id'];
        
        // Get the Chatter's Name
        // Note: This requires your App to be in "Live" mode to work for the public.
        $user_info_json = file_get_contents("https://graph.facebook.com/$sender_id?fields=first_name,last_name&access_token=$access_token");
        $user_info = json_decode($user_info_json, true);
        
        // If we can't get the name (e.g., app in dev mode), default to "Friend"
        $chatter_name = isset($user_info['first_name']) ? $user_info['first_name'] : "Friend";
        
        // --- THIS IS THE SECTION YOU WANTED TO CHANGE ---
        // It greets the chatter, then introduces you.
        $reply = "Hi $chatter_name! I'm Renz Daniel A. Rafael."; 
        // ------------------------------------------------

        // Send Reply
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => json_encode([
                    'recipient' => ['id' => $sender_id],
                    'message' => ['text' => $reply]
                ])
            ]
        ];
        
        // Use @ to suppress warnings if the HTTP request fails
        @file_get_contents("https://graph.facebook.com/v18.0/me/messages?access_token=$access_token", false, stream_context_create($options));
    }
}
http_response_code(200);
?>
