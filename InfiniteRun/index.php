<!DOCTYPE HTML>
<html>
<head>
<!--
/*////////////////////////////////////////////////////////*/
//               SCRIPTED BY MIRO RUOPSA                 //
/*//////////////////////////////////////////////////////*/
//Powered by html5, html, css, javascript, ajax and php//
////////////////////////////////////////////////////////
-->
<style type="text/css">
body
{
	background-color:#000000;
}
@font-face
{
	font-family: bt1982;
	src: url('bt1982.ttf');
}
</style>
<script type="text/javascript">
//Define objects
obj_control = { keycode:-1, g_total:8, g_spd_x:-5, star_spd_inc:0, hundreths:0, mseconds:0, seconds:0, minutes:0, warning_show:-1, timelastframe:new Date(), timenow:0, deltatime:0, timesincestart:0, hscores:"" }
obj_player = { x:150, y:260, width:35, height:50, spd_y:0, spd_x:0, image_index:1, death:-1 }

//Send highscore to server
//I DO NOT RECOMMEND USING THIS METHOD ON A PUBLIC DOMAIN
function loadXMLDoc()
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
	  	xmlhttp=new XMLHttpRequest();
	  	
	  	xmlhttp.onreadystatechange=function()
  		{
  			if (xmlhttp.readyState==4 && xmlhttp.status==200)
    			{
    				obj_control.hscores = xmlhttp.responseText;
   			}
  		}
		var blaa = document.getElementById("playername").value;
		xmlhttp.open("GET","highscores.php?min="+obj_control.minutes+"&s="+obj_control.seconds+"&h="+obj_control.hundreths+"&n="+blaa,true);
		xmlhttp.send();
	}
}

//Set up the objects
var obj_blood = new Array();
var b_index = 0;
while ( b_index < 30 )
{
	obj_blood[b_index] = { x:15, y:20, spd_x:0, spd_y:0 }
	b_index += 1;
}

var obj_star = new Array();
var s_index = 0;
while ( s_index < 10 )
{
	obj_star[s_index] = { x:Math.floor(800*Math.random()), y:Math.floor(450*Math.random()), width:Math.floor(50+80*Math.random()), height:2, spd_x:-Math.floor(2+8*Math.random()) }
	s_index += 1;
}


var obj_ground = new Array();
var g_index = 0;
while ( g_index < obj_control.g_total ) //0-19 objects == 20
{
	obj_ground[g_index] = { x:(g_index*100), y:330, width:140, height:20, spd_y:0, spd_x:0 }
	g_index += 1;
}

//Define functions
function main_loop()
{
	if ( obj_player.death == -1 )  //If player is alive
	{
		timer();
		ground_control();
		player_move();
	}
	else if ( obj_player.death == 1 ) //If player is dead
	{
		loadXMLDoc()
		////////////////////////////////////////////////////////////////////////////////////
		obj_player.death = 0; //Only draw screen and other necessary crap
	}
	else if ( obj_player.death == 0 && obj_control.keycode == 13 ) //If player is dead and the "restart screen" is on
	{
		restartgame();
	}
	draw_screen_1();
	draw_screen_hs(); //Scoreboard
}

function restartgame() //Restart the game and thus all the variables
{
	var g_index = 0;
	while ( g_index < obj_control.g_total ) //0-19 objects == 20
	{
		obj_ground[g_index] = { x:(g_index*100), y:330, width:140, height:20, spd_y:0, spd_x:0 }
		g_index += 1;
	}
	
	var b_index = 0;
	while ( b_index < 30 )
	{
		obj_blood[b_index] = { x:15, y:20, spd_x:0, spd_y:0 }
		b_index += 1;
	}
	
	obj_player.y = 260;
	obj_player.spd_y = 0;
	obj_player.spd_x = 0;
	obj_player.death = -1;
	
	obj_control.g_spd_x = -5;
	obj_control.star_spd_inc = 0;
	obj_control.hundreths = 0;
	obj_control.mseconds = 0;
	obj_control.seconds = 0;
	obj_control.minutes = 0;
	obj_control.warning_show = -1;
	obj_control.timelastframe = new Date();
	obj_control.timenow = 0;
	obj_control.deltatime = 0;
	obj_control.timesincestart = 0;	
}

function draw_screen_1()
{
	var cv0 = document.getElementById("view_0");
	can_view_0 = cv0.getContext("2d");
	//Clear screen (...or "draw background")
	can_view_0.fillStyle = "#000000";
	can_view_0.fillRect(0, 0, 600, 450);
	
	//Draw stars
	if ( obj_player.death == -1 )
	{
		can_view_0.fillStyle = "#657383";
		var i = 0;
		while ( i < 10 )
		{
			can_view_0.fillRect(obj_star[i].x, obj_star[i].y, obj_star[i].width, obj_star[i].height);
			obj_star[i].x += obj_star[i].spd_x;

			if ( obj_star[i].x < -obj_star[i].width ) //if outside canvas
			{
				obj_star[i].x = Math.floor(600+200*Math.random());
				obj_star[i].y = Math.floor(450*Math.random());
				obj_star[i].width = Math.floor(50+80*Math.random());
				obj_star[i].spd_x = obj_control.star_spd_inc-Math.floor(2+8*Math.random());
			}
			i += 1;
		}
	}

	//Draw text
	can_view_0.fillStyle = "#FFFFFF";
	can_view_0.font = "20px bt1982";
	can_view_0.fillText("Time: " + obj_control.minutes + ":" + obj_control.seconds + ":" + obj_control.hundreths, 400, 20);
	
	if ( obj_control.warning_show == 1 )
	{
		can_view_0.fillStyle = "#F9B7FF";
		can_view_0.font = "20px bt1982";
		if ( obj_control.seconds < 20 ) { can_view_0.fillText("Running faster in " + (20-obj_control.seconds) + "!", 5, 20); }
		else if ( obj_control.seconds < 40 ) { can_view_0.fillText("Running faster in " + (40-obj_control.seconds) + "!", 5, 20); }
	}

	//Draw player
	if ( obj_player.death == -1 )
	{
		var img = new Image();
		img.src = "player sprites/man_"+obj_player.image_index+".png";
		can_view_0.drawImage(img, obj_player.x, obj_player.y);
		obj_player.image_index += 1-Math.floor((5+obj_control.g_spd_x)*Math.random());
		if ( obj_player.image_index > 30 ) { obj_player.image_index = 1; }
	}
	else //draw blood & info crap
	{
		var i = 0;
		while ( i < 30 )
		{
			can_view_0.fillStyle = "#FF0000";
			can_view_0.beginPath();
			can_view_0.arc(obj_player.x+obj_blood[i].x, obj_player.y+obj_blood[i].y, 2, 0, 2*Math.PI, 1);
			can_view_0.fill();
			can_view_0.closePath();
			if ( obj_blood[i].spd_x == 0 && obj_blood[i].spd_y == 0 )
			{	
				obj_blood[i].spd_x = Math.floor(11*Math.random()-5);
				obj_blood[i].spd_y = Math.floor(11*Math.random()-5);
			}
			obj_blood[i].x += obj_blood[i].spd_x;
			obj_blood[i].y += obj_blood[i].spd_y;
			i += 1;
		}
		can_view_0.fillStyle = "#FFFFFF";
		can_view_0.fillRect(242,150,150,62);
		can_view_0.strokeRect(243,151,148,60);
		can_view_0.font = "14px bt1982";
		can_view_0.fillStyle = "#000000";
		can_view_0.fillText("Press enter", 250, 174);
		can_view_0.fillText("to restart.", 255, 195);
	}
	
	//Draw ground
	if ( obj_player.death == -1 )
	{
		can_view_0.fillStyle = "#FFFFFF";
		var g_index = 0;
		while ( g_index < obj_control.g_total )
		{
			can_view_0.fillRect(obj_ground[g_index].x, obj_ground[g_index].y, obj_ground[g_index].width, obj_ground[g_index].height);
			g_index += 1;
		}	
	}
}


function draw_screen_hs()
{
	var cv1 = document.getElementById("view_1");
	can_view_1 = cv1.getContext("2d");
	//Clear screen
	can_view_1.fillStyle = "#000000";
	can_view_1.font = "23px bt1982";
	can_view_1.fillRect(0, 0, 200, 450);

	//draw text
	can_view_1.fillStyle = "#FFFFFF";
	can_view_1.fillText("Top 3:", 5, 25);
	
	//Split highscores to separate strings and show them
	var topscore = new Array(3);
	//for (var i = 0; i < 3; i++) { topscore[i] = [' ', ' ']; }
	topscore = obj_control.hscores.split("-");
	var ind = 0;
	while ( ind < 3 )
	{
		topscore[ind] = topscore[ind].split("*");
		ind++;
	}
	var first = 70;
	var second = 130;
	var third = 190;
	
	var i = 0;
	var a = 0;
	var b = 0;
	can_view_1.font = "20px bt1982";
	while ( i < 3 )
	{
		var minute = Math.floor(topscore[i][1]/6000);
		var sec = Math.floor((topscore[i][1]/6000 - minute)*60);
		var hun = Math.floor(((topscore[i][1]/6000 - minute)*60 - sec)*100);
		if ( i == 0 ) { a = 1; b = 2; } else if ( i == 1 ) { a = 0; b = 2 } else { a = 0; b = 1; }
		if ( parseInt(topscore[i][1]) >= parseInt(topscore[a][1]) )
		{
			if ( parseInt(topscore[i][1]) >= parseInt(topscore[b][1]) )
			{
				can_view_1.font = "20px bt1982";
				can_view_1.fillStyle = "#FFFF33";
				can_view_1.fillText(topscore[i][0], 5, first);
				can_view_1.fillText(minute + ":" + sec + ":" + hun, 5, first+25);
			}
			else
			{
				can_view_1.font = "18px bt1982";
				can_view_1.fillStyle = "#CCCCFF";
				can_view_1.fillText(topscore[i][0], 5, second);
				can_view_1.fillText(minute + ":" + sec + ":" + hun, 5, second+25);
			}
		}
		else if ( parseInt(topscore[i][1]) >= parseInt(topscore[b][1]) )
		{
			can_view_1.font = "18px bt1982";
			can_view_1.fillStyle = "#CCCCFF";
			can_view_1.fillText(topscore[i][0], 5, second);
			can_view_1.fillText(minute + ":" + sec + ":" + hun, 5, second+25);
		}
		else
		{
			can_view_1.font = "16px bt1982";
			can_view_1.fillStyle = "#996633";
			can_view_1.fillText(topscore[i][0], 5, third);
			can_view_1.fillText(minute + ":" + sec + ":" + hun, 5, third+25);	
		}
		i++;
	}
	
}

function timer()
{
	//Timer crap
	obj_control.mseconds += 10;
	if ( obj_control.mseconds == 10 )
	{
		obj_control.hundreths += 1;
		obj_control.mseconds = 0;
	}
	if ( obj_control.hundreths == 100 )
	{
		obj_control.seconds += 1;
		obj_control.hundreths = 0;
	}
	if ( obj_control.seconds == 60 )
	{
		obj_control.minutes += 1;
		obj_control.seconds = 0;
	}

	//increase platform speed
	if ( obj_control.seconds == 17 && obj_control.minutes == 0 )
	{
		obj_control.warning_show = 1;
	}
	else if ( obj_control.seconds == 20 && obj_control.minutes == 0 )
	{
		obj_control.g_spd_x = -6;
		obj_control.star_spd_inc = -1;
		obj_control.warning_show = -1;
	}
	if ( obj_control.seconds == 37 && obj_control.minutes == 0 )
	{
		obj_control.warning_show = 1;
	}
	else if ( obj_control.seconds == 40 && obj_control.minutes == 0 )
	{
		obj_control.g_spd_x = -7;
		obj_control.star_spd_inc = -2;
		obj_control.warning_show = -1;
	}
	
	obj_control.timesincestart += 1;	
	obj_control.timenow = new Date();
	obj_control.deltatime = obj_control.timenow - obj_control.timelastframe;	
	obj_control.timelastframe = obj_control.timenow;
}

function player_move()
{
	obj_player.spd_y += 1*(obj_control.deltatime/10);
	
	var g = 0;
	while ( g < obj_control.g_total )
	{
		if ( obj_control.keycode == 38 && obj_player.y+obj_player.height+1 == obj_ground[g].y && obj_player.x >= obj_ground[g].x-obj_player.width && obj_player.x <= obj_ground[g].x+obj_ground[g].width ) //JUMP
		{
			obj_player.spd_y = -15;
			break;
		}
		g += 1;
	}
	
	//if ( obj_control.keycode == 13 ) { obj_player.death = -1; }

	if ( obj_player.spd_y != 0 ) { var sign = (obj_player.spd_y)/Math.abs(obj_player.spd_y); }
	else { var sign = 0; }
	var i = 0;
	while ( i <= Math.abs(obj_player.spd_y) )
	{
		var g = 0;
		while ( g != -1 )
		{
			if ( obj_player.y+obj_player.height+sign >= obj_ground[g].y && obj_player.y+sign <= obj_ground[g].y+obj_ground[g].height && obj_player.x >= obj_ground[g].x-obj_player.width && obj_player.x <= obj_ground[g].x+obj_ground[g].width )
			{
				obj_player.spd_y = 0;
				i = 999;
				break;
			}
			g += 1;
			if ( g == obj_control.g_total ) { g = -1; }
		}
		if ( g == -1 ) { obj_player.y += sign; }
		i += 1;
	}

	

	//collision with ground ---> death
	g = 0;
	while ( g != -1 )
	{
		if ( obj_player.y+obj_player.height >= obj_ground[g].y && obj_player.y <= obj_ground[g].y+obj_ground[g].height && obj_player.x >= obj_ground[g].x-obj_player.width && obj_player.x <= obj_ground[g].x+obj_ground[g].width )
		{
			obj_player.death = 1;
		}
		g += 1;
		if ( g == obj_control.g_total ) { g = -1; }
	}
	if ( obj_player.y > 450 ) { obj_player.death = 1; } //450 == canvas height
}

function ground_control()
{
	var i = 0;
	while ( i < obj_control.g_total )
	{
		if ( obj_control.timesincestart > 100 ) { obj_ground[i].x += obj_control.g_spd_x*(obj_control.deltatime/10); }
		else { obj_ground[i].x += obj_control.g_spd_x }
		
		if ( obj_ground[i].x < -obj_ground[i].width ) //if outside screen
		{
			var xx = Math.floor(5*Math.random());
			if ( xx == 0 ) { xx = Math.floor(30 + 51*Math.random())-(5+obj_control.g_spd_x)*10; }  // xx is random integer from 40 to 80
			else { xx = 0; }

			var yy = Math.floor(6*Math.random());
			if ( yy == 0 ) { yy = Math.floor(-101*Math.random()); }  // yy is random integer from -100 to 100
			else if ( yy == 1 ) { yy = Math.floor(-100 + (401)*Math.random()); }  // yy is random integer from -100 to 400
			else { yy = 0; }
			if ( yy <= 20 && yy >= -20 && yy != 0 ) { yy = (yy/Math.abs(yy))*20; }

			if ( i == 0 )
			{
				obj_ground[i].x = obj_ground[obj_control.g_total-1].x+obj_ground[obj_control.g_total-1].width+xx+obj_control.g_spd_x;
				obj_ground[i].y = obj_ground[obj_control.g_total-1].y+yy;
			}
			else
			{
				obj_ground[i].x = obj_ground[i-1].x+obj_ground[i-1].width+xx;
				obj_ground[i].y = obj_ground[i-1].y+yy;
			}
			if ( obj_ground[i].y > 419 ) { obj_ground[i].y = 429; }
			if ( obj_ground[i].y < obj_player.height+10 ) { obj_ground[i].y = obj_player.height; }
		}	
		i += 1;
	}
}
</script>
</head>
<body onload="loadXMLDoc()">

<img src="logo.png" style="border:groove 2px #FFFFFF; position:absolute; left:200px; top:3px;" />
<canvas id="view_0" width="600" height="450" tabindex="1" style="border:groove 1px #FFFFFF; position:absolute; left:198px; top:60px;">No canvas support. :(</canvas>
<canvas id="view_1" width="200" height="450" style="border:groove 1px #FFFFFF; position:absolute; left:804px; top:60px;">No canvas support. :(</canvas>

<form style="position:absolute; left:200px; top:530px; color:#FFFFFF; ">
Type your name here: <input type="text" id="playername"> <br />
Make sure you dont have the name form focused other than when you type your name. <br />
(ie. click on the canvas/game screen after typing your name)
</form>
<div style="position:absolute; left:520px; top:530px; color:#FFFFFF; font-size:20px;">
Press up key to jump and run as far as you can! </br>
</div>


<script type="text/javascript">
document.getElementById("view_0").focus();

document.getElementById("view_0").onkeydown = function(i)
{
	var i = window.event || i;
	obj_control.keycode = i.keyCode;
}
document.getElementById("view_0").onkeyup = function() { obj_control.keycode = -1; }


setInterval("main_loop()", 10);
</script>

</body>
</html>
