<?php

$curl = curl_init('https://api.pushbullet.com/v2/pushes');

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer EO1f1ykNWSU7wCGafCs0NwwWfcrv19YC']);
curl_setopt($curl, CURLOPT_POSTFIELDS, ["email" => $email, "type" => "link", "title" => "Demo Pushbullet Notification", "body" => "You have new comment(s)!", "url" => "http://demo.example.com/comments"]);

// UN-COMMENT TO BYPASS THE SSL VERIFICATION IF YOU DON'T HAVE THE CERT BUNDLE (NOT RECOMMENDED).
// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($curl);

print_r($response);

/*
if (!function_exists('curl_setopt_array')) {
   function curl_setopt_array(&$ch, $curl_options)
   {
       foreach ($curl_options as $option => $value) {
           if (!curl_setopt($ch, $option, $value)) {
               return false;
           } 
       }
       return true;
   }
}
$fre="u8yYHf161k4efVssgUqVjZZw69VJDb";
$steven="uX9fA2pnpkTfYt2sY92jP2dswx1wkb";
curl_setopt_array($ch = curl_init(), array(
  CURLOPT_URL => "https://api.pushover.net/1/messages.json",
  CURLOPT_POSTFIELDS => array(
  "token" => "axFT1fMUou6au8nxQfdtAQNS1SyCyH",
  "user" => $steven,
  "message" => "RAF COF cleanup done. Gr,Fre",
)));
curl_exec($ch);
curl_close($ch);


/*
curl_setopt_array(
	$chpush = curl_init(),
	array(
		CURLOPT_URL => "https://new.boxcar.io/api/notifications",
		CURLOPT_POSTFIELDS => array(
			"user_credentials" => '2vbcUsPHC9hFCI9YCS',
			"notification[title]" => 'RAF COF cleanup',
			"notification[long_message]" => '<b>Cleanup done. Gr,Fre</b>',
			"notification[sound]" => "bird-1",
			"notification[icon_url]" => "http://new.boxcar.io/images/rss_icons/p1-64.png"
		)));
$ret = curl_exec($chpush);
curl_close($chpush);
echo "\n";

*/
?>