<?php

$password = "password";

//include "extra.php";

// for mobile layout
echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";

if ($_COOKIE["notes_loggedin"] !== $password)
{
	// title
	echo "<head><title>notes</title></head>";

	echo "<form method=\"POST\" action=\"index.php?login\">";
	echo "<input type=\"password\" name=\"password\" placeholder=\"password\"> ";
	echo "<input type=\"submit\" value=\"log in\">";
	echo "</form>";

	if (isset($_GET["login"]))
	{
		if ($_POST["password"] == $password)
		{
			setcookie("notes_loggedin", $password, time()+60*60*24*365*1000);
			echo "<script>location.href = \"index.php\"</script>";
		}
	}
}
else
{
	// skapar en tom BitchFile-rad
	if (file_get_contents("data.txt") == "") file_put_contents("data.txt", "B;F\n");

	if (isset($_GET["logout"]))
	{
		setcookie("notes_loggedin", '', time()-3000);
	}
	else if (isset($_GET["add"]))
	{
		$text = str_replace("\r", "", str_replace("\n", "<br>", $_POST["text"]));

		$adata = file_get_contents("data.txt");
		if (strpos($adata, "\n" . str_replace(";","",$_POST["category"]) . ";" . str_replace(";","",$text) . "\n") === false)	// if the exact line (hence thou "\n") already exists
		{
			file_put_contents("data.txt", $adata . str_replace(";","",$_POST["category"]) . ";" . str_replace(";","",$text) . "\n");
		}
		else
		{
			// delete old line. add new.??
		}
		if (isset($_POST["category"])) echo "<script>location.href = \"index.php?category=" . urlencode($_POST["category"]) . "\";</script>";
		else echo "<script>location.href = \"index.php\";</script>";
	}
	else if (isset($_GET["del"]))
	{
		file_put_contents("data.txt", str_replace("\n" . $_GET["category_text"] . "\n", "\n" . "!" . $_GET["category_text"] . "!" . "\n", file_get_contents("data.txt")));	// only delete that exact line (hence thou "\n") and nothing else
		if (isset($_GET["category"])) echo "<script>location.href = \"index.php?category=" . $_GET["category"] . "\";</script>";
		else echo "<script>location.href = \"index.php\";</script>";
	}
	else if (isset($_GET["change"]))
	{
		// se till att str_replace ";" ?!!
		$category_text_old = explode(";", urldecode($_POST["category_text_old"]));
		$category_text_new = $_POST["category_new"] . ";" . $category_text_old[1];
		file_put_contents("data.txt", str_replace(urldecode($_POST["category_text_old"]) . "\n", $category_text_new . "\n", file_get_contents("data.txt")));
		if (isset($_POST["category"])) echo "<script>location.href = \"index.php?category=" . $_POST["category"] . "\";</script>";
		else echo "<script>location.href = \"index.php\";</script>";
	}
	else if (isset($_GET["edit_send"]))
	{
		$text_new = str_replace("\r", "", str_replace("\n", "<br>", $_POST["text_new"]));

		$edata = file_get_contents("data.txt");
	//	if (strpos("\n" . $edata, str_replace(";","",$_POST["category"]) . ";" . str_replace(";","",$text_new) . "\n") === false)
	//	{
			file_put_contents("data.txt", str_replace($_POST["category"] . ";" . urldecode($_POST["text_old"]) . "\n", str_replace(";","",$_POST["category"]) . ";" . str_replace(";","",$text_new) . "\n", $edata));
	//	}
		echo "<script>location.href = \"index.php?edit&data=" . urlencode($_POST["category"] . ";" . $text_new) . "\";</script>";
	}
	else if (isset($_GET["edit"]))
	{
		// title
		echo "<head><title>notes</title></head>";

		// style
		echo "<style>";
		echo "* { box-sizing: border-box; }";
		echo "body { margin: 0 auto !important; float: none !important; }";
		echo "textarea { width: 100% }";
		echo "div { max-width: 360px; padding-left: 10px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; margin: 10px; background-color: #F2F2F2; }";
		echo ".column { float: left; width: 100%; padding: 10px; }";
		echo "</style>";

		$line_columns = explode(";", $_GET["data"]);

		// note
		echo "<div class=column>";

		if (isset($_GET["category"])) echo "<b><a href=\"index.php?category=" . $_GET["category"] . "\">&lt;&lt;</a></b>";
		else echo "<b><a href=\"index.php\">&lt;&lt;</a></b>";

		echo "<p>";
		$text_1 = preg_replace("/https:\/\/(\S+)\b/", "<a target=_blank href=https://$1>https://$1</a>", urldecode(str_replace("<br>", "\n", $line_columns[1])));
		$text_2 = preg_replace("/http:\/\/(\S+)\b/", "<a target=_blank href=http://$1>http://$1</a>", $text_1);
		$text_3 = preg_replace("/\<a target=_blank href=https:\/\/(\S+)\b\.(\w\wg)\>(\S+)\<\/a\>/", "<a target=_blank href=https://$1.$2><img width=100% src=https://$1.$2></img></a>", $text_2);
		$text_4 = preg_replace("/\<a target=_blank href=http:\/\/(\S+)\b\.(\w\wg)\>(\S+)\<\/a\>/", "<a target=_blank href=http://$1.$2><img width=100% src=https://$1.$2></img></a>", $text_3);
		echo str_replace("\n", "<br>", $text_4);
		echo "</p>";

		echo "</div>";

		// edit
		echo "<div class=column>";
		echo "<form action=\"index.php?edit_send\" method=\"POST\">";
		echo "<input type=\"hidden\" name=\"category\" value=\"" . $line_columns[0] . "\">";
		echo "<input type=\"hidden\" name=\"text_old\" value=\"" . urlencode($line_columns[1]) . "\"><br>";
		echo "<textarea name=\"text_new\" rows=30 cols=40>" . str_replace("<br>", "\n", $line_columns[1]) . "</textarea>";	// $text_3
		echo "<input type=\"submit\" value=\"save\">";
		echo "</form>";
		echo "</div>";
	}
	else
	{
		// title
		echo "<head><title>notes</title></head>";

		// style
		echo "<style>";
		echo "* { box-sizing: border-box; }";
		echo "body { margin: 0 auto !important; float: none !important; }";
		echo "textarea { width: 100% }";
		echo "div { max-width: 360px; padding-left: 10px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; margin: 10px; background-color: #F2F2F2; }";
		echo ".column { float: left; width: 100%; padding: 10px; }";
		echo "h2 { margin: 4px; }";
		echo "</style>";

		// get data
		$lines = explode("\n", file_get_contents("data.txt"));
		$lines_count = count($lines)-2;
		$limit = 30;
		if (isset($_GET["category"]) || isset($_GET["all"]) || isset($_GET["s"])) $limit = 999999;
		$phrase = "";
		if (isset($_GET["s"])) $phrase = $_GET["s"];

		// panel //
		echo "<div class=\"column\">";
		echo "<center>";
		echo "<h2><b><a href=\"index.php\">notes</a></b></h2> ";
		echo "<form action=\"index.php\" method=\"GET\">";
		if (isset($_GET["category"])) echo "<input type=\"hidden\" name=\"category\" value=\"" . $_GET["category"] . "\">";
		echo "<input type=\"textbox\" name=\"s\" style=\"width: 30%;\">";
		echo "<input type=\"submit\" value=\"search\">";
		echo "</form>";
		echo "</center>";

		// category list
		echo "<p>";

		$categorylist[0] = "";
		$l = 1;
		for ($k = 1; $k < $lines_count+1; $k++)	// 1 för BitchFile-rad
		{
			$c = explode(";", $lines[$k]);

			$exists = 0;
			for ($m = 0; $m < count($categorylist); $m++)
			{
				if ($categorylist[$m] === $c[0])
				{
					$exists = 1;
					$number[$m]++;
				}
			}
			if ($exists == 0 && strpos($c[0], "!") !== 0 && $c[1] !== "")
			{
				$categorylist[$l] = $c[0];
				$number[$m] = 0;
				$l++;
			}
		}
		for ($l = 0; $l < count($categorylist); $l++)
		{
			echo "<font size=" . (sqrt(sqrt($number[$l]))+1) . ">";
			echo "<a href=\"index.php?category=" . urlencode($categorylist[$l]) . "\">(" . $categorylist[$l] . ")</a> ";
			echo "</font>";
		}

		echo "</p>";

		echo "<form action=\"index.php?add\" method=\"POST\">";
		echo "<textarea id=\"add\" name=\"text\" rows=10></textarea><br>";
		if (isset($_GET["category"])) echo "<input type=\"hidden\" name=\"category\" value=\"" . $_GET["category"] . "\">";
		echo "<input type=\"submit\" value=\"add\">";
		echo "</form>";

		echo "</div>";

		// notes
		$i = $lines_count;
		$ii = 0;
		while ($ii < $limit && $i >= 1)	// 1 för BitchFile-rad
		{
			if (isset($_GET["random"])) $i = rand(0, $lines_count);

			if ($phrase == "" || strpos(strtolower($lines[$i]), strtolower($phrase)) !== false)
			{
				if ((!isset($_GET["category"]) || isset($_GET["category"]) && strpos($lines[$i], $_GET["category"] . ";") === 0) && strpos($lines[$i], "!") !== 0)
				{
					$line_columns = explode(";", $lines[$i]);

					if ($line_columns[1] !== "")
					{
						echo "<div class=column>";

						if ($line_columns[0] == "")
						{
							echo "<form action=\"index.php?change\" method=\"POST\">";
							if (isset($_GET["category"])) echo "<input type=\"hidden\" name=\"category\" value=\"" . $_GET["category"] . "\">";
							echo "<input type=\"hidden\" name=\"category_text_old\" value=\"" . urlencode($lines[$i]) . "\">";
							echo "<input type=\"textbox\" name=\"category_new\">";
							echo "<input type=\"submit\" value=\"new category\">";
							echo "</form>";
						}

						/* skriv ut text */
						$x = (412.917284*ord($line_columns[0]) - floor(412.917284*ord($line_columns[0])));
						if ($x > 0.88) { $red = 255; $green = 17; $blue = 17; }
						else if ($x > 0.77) { $red = 17; $green = 17; $blue = 255; }
						else if ($x > 0.66) { $red = 17; $green = 150; $blue = 17; }
						else if ($x > 0.55) { $red = 200; $green = 17; $blue = 255; }
						else if ($x > 0.44) { $red = 255; $green = 110; $blue = 17; }
						else if ($x > 0.33) { $red = 17; $green = 120; $blue = 120; }
						else if ($x > 0.22) { $red = 150; $green = 17; $blue = 150; }
						else if ($x > 0.11) { $red = 140; $green = 70; $blue = 17; }
						else { $red = 17; $green = 17; $blue = 17; }

						echo "<p style=\"color:#" . dechex($red) . dechex($green) . dechex($blue) . "\">";

						if (isset($_GET["category"])) echo "<a href=\"index.php?edit&data=" . urlencode($line_columns[0] . ";" . $line_columns[1]) . "&category=" . $_GET["category"] . "\">(e)</a> ";
						else echo "<a href=\"index.php?edit&data=" . urlencode($line_columns[0] . ";" . $line_columns[1]) . "\">(e)</a> ";

						$text_1 = preg_replace("/https:\/\/(\S+)\b/", "<a target=_blank href=https://$1>https://$1</a>", urldecode(str_replace("<br>", "\n", $line_columns[1])));	// byter ut \n mot <br> för att bearbeta här
						$text_2 = preg_replace("/http:\/\/(\S+)\b/", "<a target=_blank href=http://$1>http://$1</a>", $text_1);
						$text_3 = preg_replace("/\<a target=_blank href=https:\/\/(\S+)\b\.(\w\wg)\>(\S+)\<\/a\>/", "<a target=_blank href=https://$1.$2><img width=100% src=https://$1.$2></img></a>", $text_2);
					//	echo str_replace("\n", "<br>", substr(str_replace("<br>", "\n", $text_3), 0, 800));
						echo str_replace("\n", "<br>", substr($text_3, 0, 800));	// här byter jag tillbaka \n till <br>, efter bearbetningen ovanför

						if (isset($_GET["category"])) echo " <a href=\"index.php?del&category_text=" . urlencode($line_columns[0] . ";" . $line_columns[1]) . "&category=" . urlencode($_GET["category"]) . "\">&#128465;</a>";
						else echo " <a href=\"index.php?del&category_text=" . urlencode($line_columns[0] . ";" . $line_columns[1]) . "\">&#128465;</a>";

						echo "</p>";

						echo "</div>";

						$ii++;
					}
				}
			}
			$i--;
		}
	}
}

?>
