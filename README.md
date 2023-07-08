# notes

Super simple note taking web "app" (= web site) for you who have your own web server with PHP on it.

HOW TO USE IT:
1. Have your own web server with PHP installed on it. If you have this I assume you know how to set up a website too. So, put index.php where you want it.
2. Create the file "data.txt" in the same directory as index.php, and make it able to write to (in Linux: chmod 777 data.txt - very unsecure because it gives EVERYONE permission to do ANYTHING, but it works).
3. Set your own password in index.php ($password = "password").

BUGS: it may be difficult/impossible to put "+" and ";" in the notes right now, due to how the data storage works.

LICENSE: kbrecordzz public domain license = use it however you want without needing to credit me (basically CC0 but much shorter legal text).

/kbrecordzz
