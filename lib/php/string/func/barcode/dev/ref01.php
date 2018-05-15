<?php

require_once "../code.php";
$bcode = array();
$bcode['c128']	= array('name' => 'Code128', 'obj' => new emberlabs\Barcode\Code128());

function bcode_img64($b64str)
{
	echo "<img src='data:image/png;base64,$b64str' /><br />";
}

?>
<html>
<head>

<title>Barcode Tester</title>

</head>
<body>

<form action="ref01.php" method="post">

Enter Data to encode: <input type="text" name="encode" value="<?php echo htmlspecialchars($_POST['encode']); ?>" /><br />
<input type="submit" value="Encode" name="submit" />

</form>

<hr />

<?php

if (isset($_POST['submit'])) {

?>
Data to be encoded: <strong><?php echo htmlspecialchars($_POST['encode']); ?></strong><br />

<?php
	foreach($bcode as $k => $value)
	{
		try
		{
			$bcode[$k]['obj']->setData($_POST['encode']);
			$bcode[$k]['obj']->setDimensions(200, 100);
			$bcode[$k]['obj']->draw();
			$b64 = $bcode[$k]['obj']->base64();
			bcode_img64($b64);
		}
		catch (Exception $e)
		{

		}
	}
?>

<?php } ?>

</body>
</html>