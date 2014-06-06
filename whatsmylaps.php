<?php
include 'whatsapi/whatsprot.class.php';
include 'config.inc.php';

if ($argc < 3)
{
	echo "USAGE: " . $_SERVER['argv'][0] . "[whatsappNr] [transponderNr]";
	exit(1);
}


$receiver 		= $_SERVER['argv'][1];
$transponder 	= $_SERVER['argv'][2];

sendMessage("WhatsMYLAPS TEST\nGeactiveerd voor $receiver met transponder $transponder.\nU ontvangt nu uw nieuwe rondetijden, veel plezier!\n\n-Team BAMMES-");

$terminate = false;
$lastSize = 30; // Skip first title row
$countdown = 10;

while ($terminate == false)
{
	if ($countdown > 0)
	{
		print("Next update in $countdown seconds...\r");
		$countdown--;
		sleep(1);
		continue;
	}
	print("                             \r");
	$countdown = 10;

	$data = rtrim(file_get_contents("http://practice.mylaps.com/practice/GetCSVFile.jsp?transponder=$transponder&tid=387"));

	$currSize = strlen($data);

	//print("currSize $currSize \nlastSize $lastSize\n");

	if ($currSize > $lastSize)
	{
	
		$data = trim(substr($data, $lastSize));
		//print($data . "\n");
		
		$data = explode("\n", $data);
		$count = count($data);
		print("Got $count new line" . ($count > 1 ? "s" : "") . "! Filtering...\n");

		$msg = "";

		foreach ($data as $line)
		{
			$lapTime = str_getcsv(trim($line), ';');
			
			if ($lapTime[3] != "First Passing")
				$msg .= $lapTime[3] . "\n";
		}

		print($msg);

		sendMessage($msg);

		$lastSize = $currSize;
	}
	else
		print("No new laps detected... $currSize > $lastSize\n");
}

function sendMessage($msg)
{
	global $nickname, $sender, $imei, $password, $receiver;
	echo "[] Logging in as '$nickname' ($sender)\n";
	$wa = new WhatsProt($sender, $imei, $nickname, FALSE);

	$wa->connect();
	$wa->loginWithPassword($password);

	echo "[] Sending message to $receiver\n";
	$wa->sendMessage($receiver, trim($msg));
	$wa->pollMessages();
}
/*
$headers = array();

for ($i = 0; $i < 4; $i++)
	$headers[] = array_shift($times);

print_r($times);


/*
print_r($_SERVER['argv']);





while (true)
{

}

while (TRUE) {
	$wa->pollMessages();
	$buff = $wa->getMessages();
	
	if (!empty($buff)) 
	{
	    print_r($buff);
	}
	$line = fgets_u(STDIN);
	if ($line != "") {
	    if (strrchr($line, " ")) {
	        // needs PHP >= 5.3.0
	        $command = trim(strstr($line, ' ', TRUE));
	    } else {
	        $command = $line;
	    }
	    switch ($command) {
	        case "/query":
	            $dst = trim(strstr($line, ' ', FALSE));
	            echo "[] Interactive conversation with $dst:\n";
	            break;
	        case "/lastseen":
	            echo "[] Request last seen $dst: ";
	            $wa->sendGetRequestLastSeen($dst);
	            break;
	        default:
	            echo "[] Send message to $dst: $line\n";
	            $wa->sendMessage($dst , $line);
	            break;
	    }
	}





echo "[] Sending $message to $receiver\n";
$wa->sendMessage($receiver, "Hello there, Frank");
$wa->pollMessages();

//$times = str_getcsv(file_get_contents('http://practice.mylaps.com/practice/GetCSVFile.jsp?transponder=2406063&tid=387'), ';');

*/