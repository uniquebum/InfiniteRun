<?php
$myFile = "highscores.txt";
$fh = fopen($myFile, 'r+') or die("can't open file");
$minutes = $_GET["min"];
$seconds = $_GET["s"];
$hundreths = $_GET["h"];
$playername = $_GET["n"];
$time = (int)$minutes*6000 + (int)$seconds*100 + $hundreths;

//Get scores from file
$alltimes = fgets($fh);
$namesandscores = explode("-", $alltimes);
fclose($fh);

$i = 0;
while ( $i < 3 )
{
	$score[$i] = explode("*", $namesandscores[$i]);
	$i++;
}

//Check whether we have new topscore
$i = 0;
while ( $i < 3 )
{
	if ( $time > $score[$i][1] )
	{
		$ii = 0;
		while ( $ii < 3 )
		{
			if ( $score[$i][1] > $score[$ii][1] )
			{
				$ii = 3;
			}
			if ( $ii == 2 )
			{
				$score[$i][0] = $playername;
				$score[$i][1] = $time;
				$i = 3;
			}
			$ii++;
		}
	}
	$i++;
}
//Put the scores together and save
$i = 0;
while ( $i < 3 )
{
	$namesandscores[$i] = implode("*", $score[$i]);
	$i++;
}
$alltimes = implode("-", $namesandscores);

$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $alltimes);
fclose($fh);

//Echo the scores to browser
echo $alltimes;
?>