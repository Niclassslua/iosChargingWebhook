<?php

define('SECONDS_PER_DAY', 86400);
define('SECONDS_PER_HOUR', 3600);
define('SECONDS_PER_MINUTE', 60);

function getNiceDuration($durationInSeconds) {
  $timePeriods = [
    'days' => SECONDS_PER_DAY,
    'hours' => SECONDS_PER_HOUR,
    'minutes' => SECONDS_PER_MINUTE,
    'seconds' => 1,
  ];

  $duration = '';
  foreach ($timePeriods as $unit => $value) {
    if ($durationInSeconds >= $value) {
      $num = floor($durationInSeconds / $value);
      $durationInSeconds -= $num * $value;
      $duration .= "$num $unit" . ($num > 1 ? 'n' : '') . ' ';
    }
  }

  return trim($duration);
}

$type = $_GET['type'];

$percent = filter_input(INPUT_GET, 'percent', FILTER_VALIDATE_INT, [
  'options' => [
    'default' => 0,
    'min_range' => 0,
    'max_range' => 100
  ]
]);

if ($percent === false || $percent === null) {
  die('Invalid percentage value.');
}


$webhookurl = "YOUR WEBHOOK";

$timestamp = date("c", strtotime("now"));

if ($type == "plug") {
  $title = "⚡️ iPhone is being charged";
  $color = hexdec("0ddd14");
  $name = "Current battery level:";
  $name2 = "Last charged:";
  $value = getNiceDuration(time() - file_get_contents('cache.txt'));
} elseif ($type == "unplug") {
  $title = "🔋 iPhone is no longer charging!";
  $color = hexdec("ddb40d");
  $name = "Current battery level:";
  $name2 = "Charged for:";
  $value = getNiceDuration(time() - file_get_contents('cache.txt'));
}

$json_data = json_encode([
  "username" => "iPhone - Charging status",
  "avatar_url" => "YOUR WEBHOOK AVATAR URL",
  "tts" => false,
  "embeds" => [
    [
      "title" => $title,
      "type" => "rich",
      "timestamp" => $timestamp,
      "color" => $color,
      "footer" => [
        "text" => "GitHub.com/Niclassslua",
        "icon_url" => "https://avatars.githubusercontent.com/u/78554432?v=4"
      ],
      "fields" => [
        [
          "name" => $name,
          "value" => $percent . "%",
          "inline" => true
        ],
        [
          "name" => $name2,
          "value" => $value,
          "inline" => true
        ]
      ]
    ]
  ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$ch = curl_init($webhookurl);
curl_setopt_array($ch, [
  CURLOPT_HTTPHEADER => ['Content-type: application/json'],
  CURLOPT_POST => 1,
  CURLOPT_POSTFIELDS => $json_data,
  CURLOPT_FOLLOWLOCATION => 1,
  CURLOPT_HEADER => 0,
  CURLOPT_RETURNTRANSFER => 1,
  CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);

if ($response === false) {
  $error = curl_error($ch);
  curl_close($ch);
  logError($error); //TODO: Implement a logError Function
  die("An error occurred: $error");
}

curl_close($ch);

if (file_put_contents('cache.txt', time()) === false) {
  logError("Error writing to the cache file.");
  die("An error occurred while updating the cache file.");
}

?>
