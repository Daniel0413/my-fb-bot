
<?php
// CONFIGURATION
// Replace these with your actual tokens later
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
    $sender_id = $event['sender']['id'];

    if (isset($event['message']['text'])) {
        // Get Name
        $user_info = json_decode(file_get_contents("https://graph.facebook.com/$sender_id?fields=first_name,last_name&access_token=$access_token"), true);
        
        $name = isset($user_info['first_name']) ? $user_info['first_name'] . " " . $user_info['last_name'] : "Friend";
        $reply = "Hello $name! ";

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
        file_get_contents("https://graph.facebook.com/v18.0/me/messages?access_token=$access_token", false, stream_context_create($options));
    }
}
http_response_code(200);
?>
