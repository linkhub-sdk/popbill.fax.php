<?php
/**
* =====================================================================================
* Class for base module for Popbill API SDK. It include base functionality for
* RESTful web service request and parse json result. It uses Linkhub module
* to accomplish authentication APIs.
*
* This module uses curl and openssl for HTTPS Request. So related modules must
* be installed and enabled.
*
* http://www.linkhub.co.kr
* Author : Kim Seongjun (pallet027@gmail.com)
* Written : 2014-04-15
*
* Thanks for your interest.
* We welcome any suggestions, feedbacks, blames or anything.
* ======================================================================================
*/
require_once 'Popbill/popbill.php';

class FaxService extends PopbillBase {
	
	public function __construct($PartnerID,$SecretKey) {
    	parent::__construct($PartnerID,$SecretKey);
    	$this->AddScope('160');
    }
    
    //발행단가 확인
    public function GetUnitCost($CorpNum) {
    	return $this->executeCURL('/FAX/UnitCost', $CorpNum)->unitCost;
    }

	/* 팩스 전송 요청
    *	$CorpNum => 발송사업자번호
    *	$Sender	=> 발신번호
    *	$Receviers => 수신처 목록
    *		'rcv'	=> 수신번호
    *		'rcvnm'	=> 수신자 명칭
    *	$FilePaths	=> 전송할 파일경로 문자열 목록, 최대 5개.
    *	$ReserveDT	=> 예약전송을 할경우 전송예약시간 yyyyMMddHHmmss 형식
    *	$UserID	=> 팝빌 회원아이디
    */
	public function SendFAX($CorpNum,$Sender,$Receivers = array(),$FilePaths = array(),$ReserveDT = null,$UserID = null) {
		if(empty($Receivers)) {
			throw new PopbillException('수신처 목록이 입력되지 않았습니다.');
		}
		
		if(empty($FilePaths)) {
			throw new PopbillException('발신파일 목록이 입력되지 않았습니다.');
		}
		
		$RequestForm = array();
		
		$RequestForm['snd'] = $Sender;
		if(empty($ReserveDT)) $RequestForm['sndDT'] = $ReserveDT;
		$RequestForm['fCnt'] = count($FilePaths);
		
		$RequestForm['rcvs'] = $Receivers;
	
    	$postdata = array();
    	$postdata['form'] = json_encode($RequestForm);
    	
    	$i = 0;
    	
    	foreach($FilePaths as $FilePath) {
    		$postdata['file['.$i++.']'] = '@'.$FilePath;
    	}
    	
    	return $this->executeCURL('/FAX', $CorpNum, $UserID, true,null,$postdata,true)->receiptNum;
 		
	}
	
	/* 팩스 전송 내역 확인
    *	$CorpNum => 발송사업자번호
    *	$ReceiptNum	=> 접수번호
    *	$UserID	=> 팝빌 회원아이디
    */
	public function GetFaxDetail($CorpNum,$ReceiptNum,$UserID) {
		if(empty($ReceiptNum)) {
    		throw new PopbillException('확인할 접수번호를 입력하지 않았습니다.'); 
    	}
    	return $this->executeCURL('/FAX/'.$ReceiptNum, $CorpNum,$UserID);	
	}
	
    /* 예약전송 취소
    *	$CorpNum => 발송사업자번호
    *	$ReceiptNum	=> 접수번호
    *	$UserID	=> 팝빌 회원아이디
    */
    public function CancelReserve($CorpNum,$ReceiptNum,$UserID) {
    	if(empty($ReceiptNum)) {
    		throw new PopbillException('취소할 접수번호를 입력하지 않았습니다.'); 
    	}
    	return $this->executeCURL('/FAX/'.$ReceiptNum.'/Cancel', $CorpNum,$UserID);
    }
}
?>
