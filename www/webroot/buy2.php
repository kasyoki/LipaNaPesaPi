<?php
//This page is here for the user to input an Mpesa receipt number
//This number will be used to confirm whether he has carried out an Mpesa transaction 
//and that indeed the Mpesa transaction amount conforms with the package chosen
$path = 'F:\Dropbox\Projects\LipaNaPesaPi\pesaPi\php\include';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once("F:\Dropbox\Projects\LipaNaPesaPi\pesaPi\php\include\PLUSPEOPLE\autoload.php");
//require_once("hotspot.php");
$pesa = new PLUSPEOPLE\PesaPi\PesaPi();

//retrieve the package chosen from the hostpot.php page
if (isset($_POST["package"])) {
	$package_amount = $_POST["package"];
}
//draw the receipt input box
?>
<html>
	<body>
		<form method="POST"	action="buy2.php">
			<input type="text" name="receipt" value=""><br>
			<input type= submit id="confirmation" value="Confirm">
		</form>
	</body>
</html>
<?php
//check whether the receipt matches with database and conforms with the selected package
if (isset($_POST["receipt"])) {
	$transactions = $pesa->locateByReceipt($_POST["receipt"],"9876");
		if (count($transactions)>0) {
			//connect to ticket database & give username and password based on the chosen package
			$con = mysqli_connect('localhost','root','','tickets');
			//check connection
			if (mysqli_connect_errno()) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error();	
				}
			//read receipt
			$package_receipt = mysqli_real_escape_string($con,$_POST['receipt']);
			//confirm that the package selected conforms with the amount received
			$package_query = "SELECT amount FROM pesapi_payment 
								WHERE pesapi_payment.receipt = '$package_receipt'";
			$result_amount = mysqli_query($con,$package_query);

			if ($package_amount == $result_amount) {
				//retrieve username &password from tickets database
				$ticket_query = "SELECT ticketnumber from issue_ticket
									WHERE issue_ticket.issue = 0";
				$username = mysqli_query($con,$ticket_query);
				$num = mysqli_num_rows($username);
				$ticket_number = mysqli_result($username,$num[1],"issue");
				//set ticket as used
				$set_used_query = "UPDATE issue_ticket SET issue_ticket.issue = 1 
									WHERE ticketnumber = $username";

			//give username and password if it does						
?>
				<html>
				<body> 
					<input id = "username" name = "username" value = "<?php echo "ticket_number";
					// while($row = mysqli_fetch_array($username)) { echo $row['ticketnumber'];}
					?>"><br>
				</body>
				</html>
<?php
					}
				}
			}
//tell the user to confirm mpesa receipt if it doesn't
?>