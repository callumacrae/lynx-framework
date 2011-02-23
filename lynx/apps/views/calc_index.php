<?php

if (!IN_LYNX)
{
        exit;
}

?>
<!DOCTYPE html>
<html>
        <head>
                <title>My Site: Calculator</title>
        <head>
        <body>
		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>/results">
                        <input type="text" name="num1" />
                        <input type="text" name="num2" />
                        <input type="submit" value="Add!" />
                </form>
        </body>
</html>
