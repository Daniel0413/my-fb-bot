


<?php
// CONFIGURATION
// ⚠️ WARNING: You posted your token publicly again. 
// Please Generate a NEW Page Access Token immediately in the FB Developer Dashboard.
$access_token = 'EAAbrMgvNBJABQD71Kpe2HRZCvXmqrzoYKSPM4GelWIBbnZByejCNmqfBFYDqjILtHd2JO91YcZAelUwOcXFsjLgE0k7WUY2jx6SZCAyglRT43DYumcFOLxIZCrtTKeEUzj5If8tINqmi3HVBP0BdP0GwpaDuY4nuQQ75x2ZChyZBo43GsJiVqVXAKxr5x1KU4aIlndZBeQZDZD'; 
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
    
    if (isset($event['sender']['id']) && isset($event['message']['text'])) {
        $sender_id = $event['sender']['id'];
        
        // --- SAFE NAME FETCHING ---
        // Default to "Friend" first
        $chatter_name = "Friend";
        
        // We use '@' to suppress the error if Facebook blocks the request
        $user_info_json = @file_get_contents("https://graph.facebook.com/$sender_id?fields=first_name&access_token=$access_token");
        
        // Only update the name if the request was actually successful
        if ($user_info_json) {
            $user_info = json_decode($user_info_json, true);
            if (isset($user_info['first_name'])) {
                $chatter_name = $user_info['first_name'];
            }
        }
        // --------------------------

        $reply = "Hi $chatter_name! I'm Renz Daniel A. Rafael."; 

        // Send Reply
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => json_encode([
                    'recipient' => ['id' => $sender_id],
                    'message' => ['text' => $reply]
                ]),
                'ignore_errors' => true // Prevent crashing on send error
            ]
        ];
        
        @file_get_contents("https://graph.facebook.com/v18.0/me/messages?access_token=$access_token", false, stream_context_create($options));
    }
}
http_response_code(200);
?>
