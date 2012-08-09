<?php
class Generic_Ocr
{
	// PRIVATE VARS
	private $file = null;
	private $data = array();
	private $current_gironr = null;
	private $leading_zero = false;
	private $date_format = "Y-m-d";

	// PUBLIC VARS

	public function __construct( $options = null ){
		if(isset($options[0]) && $options[0] !="") $this->file = $options[0];
		if(isset($options[1]) && $options[1] !="") $this->leading_zero = $options[1];
		if(isset($options[2]) && $options[2] !="") $this->date_format = $options[2];
	}

	public function parseOrganizeData(){
		$lines = explode("\n",$this->file);
		$data["RECIVER_POST"] = array();
		$data["TREATMENT_POST"] = array();
		$data["TRANSACTION_POST"] = array();
		$data["SUBTOTAL_POST"] = array();
		foreach ($lines as $line){
			$line_data = preg_split("/[\s,]+/", $line);
			/**
			 * var $param may contain following:
			 * 00	=	OPENING POST
			 * 10	=	CUSTOMER POST
			 * 20	=	RECIVER POST
			 * 30	=	TREATMENT POST
			 * 40	=	TRANSACTION POST
			 * 50	=	SUBTOTAL POST
			 * 90	=	TOTAL POST
				*/
			switch ($line_data[0]){
				/**
				 *
				 */
				case "00" :{
					// IF $line_data[3] != "" omorganisera
					if(isset($line_data[3]) && $line_data[3] != ""){
						$line_data[2] = date($this->date_format,mktime(0,0,0,substr($line_data[2],2,2),substr($line_data[2],4,2),substr($line_data[2],0,2)));
						$data["OPENING_POST"]=array($line_data[1],$line_data[2],$line_data[3]);
					}
					else{
						$line_data[1] = date($this->date_format,mktime(0,0,0,substr($line_data[1],2,2),substr($line_data[1],4,2),substr($line_data[1],0,2)));
						$data["OPENING_POST"]=array("",$line_data[1],$line_data[2]);
					}
					break;
				}
				/**
				 * NOT USED YET
				 */
				case "10" :{
					break;
				}
				case "20" :{
					$this->current_gironr = $line_data[1];
					$data["RECIVER_POST"][]=array($line_data[1]);
					break;
				}
				case "30" :{
					$tmp_date=substr($line_data[1],(strlen($this->current_gironr)),6);
					$tmp_date=date($this->date_format,mktime(0,0,0,substr($tmp_date,2,2),substr($tmp_date,4,2),substr($tmp_date,0,2)));
					$data["TREATMENT_POST"][]=array(
					substr($line_data[1],0,strlen($this->current_gironr)),
					$tmp_date,
					(isset($line_data[2]) ? $line_data[2] : "")
					);
					break;
				}
				case "40" :{
					if(!$this->leading_zero) $amm = substr($line_data[1],12,13);
					else $amm = (int) $amm = substr($line_data[1],12,13);
					$data["TRANSACTION_POST"][$this->current_gironr][]=array(
					substr($line_data[1],0,11),
					$amm,
					(isset($line_data[3]) ? $line_data[2] : ""),
					(isset($line_data[3]) ? $line_data[3] : $line_data[2])
					);
					break;
				}
				case "50" :{
					$tmp_date=substr($line_data[1],(strlen($this->current_gironr)),6);
					$tmp_date=date($this->date_format,mktime(0,0,0,substr($tmp_date,2,2),substr($tmp_date,4,2),substr($tmp_date,0,2)));
					$nr_payments = substr($line_data[1],(strlen($this->current_gironr)+6),7);
					if($this->leading_zero) $nr_payments = (int) $nr_payments;
					$data["SUBTOTAL_POST"][$this->current_gironr][]=array(
					$this->current_gironr,
					$tmp_date,
					$nr_payments,
					(isset($line_data[2]) ? $line_data[2] : "")
					);
					break;
				}
				case "90" :{
					$tmp_date=substr($line_data[1],0,6);
					$tmp_date=date($this->date_format,mktime(0,0,0,substr($tmp_date,2,2),substr($tmp_date,4,2),substr($tmp_date,0,2)));
					$nr_payments = substr($line_data[1],6,7);
					$amm_total = substr($line_data[1],13,15);
					if($this->leading_zero) $nr_payments = (int) $nr_payments;
					if($this->leading_zero) $amm_total = (int) $amm_total;
					$data["TOTAL_POST"]=array(
					$tmp_date,
					$nr_payments,
					$amm_total
					);
					break;
				}
			}
		}
		$this->data = $data;
	}

	public function returnData(){
		return $this->data;
	}

    
    /*
    * Generate OCR with social security number, type and luhn check sum 
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	int
    */         
	public function generateOCR($ssn, $type, $typeId)
	{
	// give typeId right format: if 230 then 00230, if 10 then 00010.
		$zeros = '';
	for ($i = strlen($typeId); $i<=4; $i++){
	$zeros .= "0";
	}

	$typeId = $zeros.$typeId;

	// Check sum
		$ocr = $this->luhn($ssn.$type.$typeId);

		return $ocr;
	}
    
    /*
    * Calculate the luhn check sum and at it to the end of the input argument
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	int
    */
	public function luhn($number) {

		$stack = 0;
		$number = str_split(strrev($number));

		foreach ($number as $key => $value)
		{
			if ($key % 2 == 0)
			{
				$value = array_sum(str_split($value * 2));
			}
			$stack += $value;
		}
		$stack %= 10;

		if ($stack != 0)
		{
			$stack -= 10;     $stack = abs($stack);
		}


		$number = implode('', array_reverse($number));
		$number = $number . strval($stack);

		return $number;
	}
}