<?php

require_once 'PopbillFax.php';

$PartnerID = 'TESTER';
$SecretKey = 'okH3G1/WZ3w1PMjHDLaWdcWIa/dbTX3eGuqMZ5AvnDE=';


$FaxService = new FaxService($PartnerID,$SecretKey);

$FaxService->IsTest(true);

try {
	echo $FaxService->GetUnitCost('1231212312');
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}
echo chr(10);


try {
	
	$Receivers = array();
	
	$Receivers[] = array(
		'rcv' => '11112222',
		'rcvnm' => '수신자성명'
	);
	
	$Receivers[] = array(
		'rcv' => '11112222',
		'rcvnm' => '수신자성명'
	);
	
	
	$Files = array('./uploadtest.jpg','./uploadtest.jpg');
	
	$ReserveDT = null; //예약전송시 예약시간 yyyyMMddHHmmss 형식
	$UserID = 'userid'; //팝빌 사용자 아이디
	
	echo $FaxService->SendFAX('1231212312','07075106766',$Receivers,$Files,$ReserveDT,$UserID);
}
catch(PopbillException $pe) {
	echo $pe->getCode().' : '.$pe->getMessage();
}
echo chr(10);

$ReceiptNum = '014042117224400001';

try {
	$result = $FaxService->GetFaxDetail('1231212312',$ReceiptNum,'userid');
	echo json_encode($result,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}
echo chr(10);


try {
	$result = $FaxService->CancelReserve('1231212312',$ReceiptNum,'userid');
	echo $result;
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}
echo chr(10);

?>
