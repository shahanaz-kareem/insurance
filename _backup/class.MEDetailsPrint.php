<?php
/*
  File Name   : class.MEDetailsPrint.php
  Class Name  : MEDetailsPrint
  Created On  : 17-11-2011
  Author(s)   : ManojKumar B, Tina George, Anil V.S, Meera
  Version     : 1.0
  Purpose     : Handling of ME Details Print
  Owner       : Sree Hari S
  Company     : iANTZ IT Solutions, Trivandrum, S-INDIA
  Web         : http://www.iantz.in/
  Copyright   : iWorkSync & iANTZ IT Solutions
*/

if(basename($_SERVER['PHP_SELF']) == basename(__FILE__))
{
	$redirect = 'http://' . $_SERVER['SERVER_NAME'] . '/';
	header("location: {$redirect}");
	exit;
}

class MEDetailsPrint
{
	protected $mDBConnection;
	protected $mSessionHandler;
	protected $mValidationHandler;

	private static $instance	=	false;
	private static $property	=	array();
	private $mErrorArray			=	array();
	private $mTableName				=	'';
	private $mFieldPrefix			=	'mdp_';
	private $mTablePKey				=	'mdp_id';
	private $mTableRKeyMEOID	=	'meo_id';
	private $mTableRKeyMEID		=	'me_id';
	public $blnReady					=	TRUE;
	public $mFormFieldArray		=	array();

	private function __construct()
	{
		$this->mTableName					=	DB_TBL_PREFIX . 'me_details_print';
		$this->mDBConnection 			= Singleton::getInstance('MysqlProd');
		$this->mSessionHandler		=	$GLOBALS['SessionHandler'];
		$this->mValidationHandler	=	ValidationHandler::getInstance();
	}

	public static function getInstance()
	{
		if(self::$instance === false)
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}

	public function __set($key, $value)
	{
		self::$property[$key] = $value;
	}

	public function &__get($key)
	{
		if(array_key_exists($key, self::$property))
		{
			return self::$property[$key];
		}
		else
		{
			throw new Exception("Property '{$key}' does not exist");
		}
	}

	public function __isset($key)
	{
		return isset(self::$property[$key]);
	}

	public function __unset($key)
	{
		if ($this->__isset(self::$property[$key]))
		{
			unset(self::$property[$key]);
		}
	}

	/* [Access] public
	 * [purpose] set fields values
	 * [return] boolean
	*/
	public function SetFormFieldValues($arr = array(), $blnStripSlashes = false)
	{
		global $Utility;
			$arr																				=	(true === $blnStripSlashes)											?	array_map('stripslashes', $arr)					:	$arr;
			$this->mFormFieldArray['id']   							= isset($arr["{$this->mTablePKey}"])      				? $arr["{$this->mTablePKey}"]		      		: (isset($arr['id'])      							? $arr['id']		      					: -999);
			$this->mFormFieldArray[$this->mTableRKeyMEOID]		  				= isset($arr["{$this->mTableRKeyMEOID}"])      	? $arr["{$this->mTableRKeyMEOID}"]		    : (isset($arr['mcr_id'])  		   					? $arr['mcr_id']		      				: null);
		for($i=1;$i<=$_POST['opt_count'];$i++)
		{
			$this->mFormFieldArray['publications_'.$i]		  				= isset($arr["{$this->mFieldPrefix}publications"])      	? $arr["{$this->mFieldPrefix}publications"]		    : (isset($arr['publications_'.$i])  		   					? $arr['publications_'.$i]		      				: null);
			$this->mFormFieldArray['editions_'.$i]		  				= isset($arr["{$this->mFieldPrefix}editions"])      	? $arr["{$this->mFieldPrefix}editions"]		    : (isset($arr['editions_'.$i])  		   					? $arr['editions_'.$i]		      				: null);
			$this->mFormFieldArray['circulation_'.$i]		  				= isset($arr["{$this->mFieldPrefix}circulation"])      	? $arr["{$this->mFieldPrefix}circulation"]		    : (isset($arr['circulation_'.$i])  		   					? $arr['circulation_'.$i]		      				: null);
			$this->mFormFieldArray['type_'.$i]		  				= isset($arr["{$this->mFieldPrefix}type"])      	? $arr["{$this->mFieldPrefix}type"]		    : (isset($arr['type_'.$i])  		   					? $arr['type_'.$i]		      				: null);
			$this->mFormFieldArray['positions_'.$i]		  				= isset($arr["{$this->mFieldPrefix}positions"])      	? $arr["{$this->mFieldPrefix}positions"]		    : (isset($arr['positions_'.$i])  		   					? $arr['positions_'.$i]		      				: null);
			$this->mFormFieldArray['size_'.$i]		  				= isset($arr["{$this->mFieldPrefix}size"])      	? $arr["{$this->mFieldPrefix}size"]		    : (isset($arr['size_'.$i])  		   					? $arr['size_'.$i]		      				: null);
			$this->mFormFieldArray['width_'.$i]		  				= isset($arr["{$this->mFieldPrefix}width"])      	? $arr["{$this->mFieldPrefix}width"]		    : (isset($arr['width_'.$i])  		   					? $arr['width_'.$i]		      				: null);
			$this->mFormFieldArray['height_'.$i]		  				= isset($arr["{$this->mFieldPrefix}height"])      	? $arr["{$this->mFieldPrefix}height"]		    : (isset($arr['height_'.$i])  		   					? $arr['height_'.$i]		      				: null);
			$this->mFormFieldArray['rate_'.$i]		  				= isset($arr["{$this->mFieldPrefix}rate"])      	? $arr["{$this->mFieldPrefix}rate"]		    : (isset($arr['rate_'.$i])  		   					? $arr['rate_'.$i]		      				: null);
			$this->mFormFieldArray['rate_per_insertion_'.$i]		  				= isset($arr["{$this->mFieldPrefix}rate_per_insertion"])      	? $arr["{$this->mFieldPrefix}rate_per_insertion"]		    : (isset($arr['rate_per_insertion_'.$i])  		   					? $arr['rate_per_insertion_'.$i]		      				: null);
			$this->mFormFieldArray['no_of_insertions_'.$i]		  				= isset($arr["{$this->mFieldPrefix}no_of_insertions"])      	? $arr["{$this->mFieldPrefix}no_of_insertions"]		    : (isset($arr['no_of_insertions_'.$i])  		   					? $arr['no_of_insertions_'.$i]		      				: null);
			$this->mFormFieldArray['amount_'.$i]		  				= isset($arr["{$this->mFieldPrefix}amount"])      	? $arr["{$this->mFieldPrefix}amount"]		    : (isset($arr['amount_'.$i])  		   					? $arr['amount_'.$i]		      				: null);
			$this->mFormFieldArray['ro_rate_'.$i]		  				= isset($arr["{$this->mFieldPrefix}ro_rate"])      	? $arr["{$this->mFieldPrefix}ro_rate"]		    : (isset($arr['ro_rate_'.$i])  		   					? $arr['ro_rate_'.$i]		      				: null);
			$this->mFormFieldArray['percentage_revenue_'.$i]		  				= isset($arr["{$this->mFieldPrefix}percentage_revenue"])      	? $arr["{$this->mFieldPrefix}percentage_revenue"]		    : (isset($arr['percentage_revenue_'.$i])  		   					? $arr['percentage_revenue_'.$i]		      				: null);
			$this->mFormFieldArray['cost_'.$i]		  				= isset($arr["{$this->mFieldPrefix}cost"])      	? $arr["{$this->mFieldPrefix}cost"]		    : (isset($arr['cost_'.$i])  		   					? $arr['cost_'.$i]		      				: null);
			$this->mFormFieldArray['other_remarks_'.$i]		  				= isset($arr["{$this->mFieldPrefix}other_remarks"])      	? $arr["{$this->mFieldPrefix}other_remarks"]		    : (isset($arr['other_remarks_'.$i])  		   					? $arr['other_remarks_'.$i]		      				: null);
			$this->mFormFieldArray['insert_dates_'.$i]		  				= isset($arr["{$this->mFieldPrefix}insert_dates"])      	? $arr["{$this->mFieldPrefix}insert_dates"]		    : (isset($arr['insert_dates_'.$i])  		   					? $arr['insert_dates_'.$i]		      				: null);
		}
		$this->mFormFieldArray['deleted']		  				= isset($arr["{$this->mFieldPrefix}deleted"])      	? $arr["{$this->mFieldPrefix}deleted"]		    : (isset($arr['deleted'])  		   					? $arr['deleted']		      				: null);
		$this->mFormFieldArray['deleted_on']	   			= PHP_DATETIME;
		$this->mFormFieldArray['deleted_by']	   			= $this->mSessionHandler->GetSessionData('u_id');
	}

	/* [Access] public
	 * [purpose] insert new ME Print
	 * [return] boolean
	*/
	public function AddMEPrint($arr = array(),$i)
	{
		global $Security;

		$FORM_VALUES_ARRAY	=	$arr;
		/*********** [START] SETTING UP OF FORM FIELD VALUES ***********/
		$this->SetFormFieldValues($arr);
		$arr			=	$this->mFormFieldArray;
		/*********** [END] SETTING UP OF FORM FIELD VALUES ***********/

		/*********** [START] VALIDATION OF FORM FIELDS ***********/
		$this->mValidationHandler->SetDBInfo($this->mTableName, $this->mFieldPrefix, $this->mTablePKey);

		$rules		=	array();
		//$rules[]	=	"required,channel_{$i},Required.";

		$this->mErrorArray	=	$this->mValidationHandler->ValidateFormValues($arr, $rules);
		$this->mValidationHandler->ReSetDBInfo();
		unset($rules);
		$this->blnReady			=	!empty($this->mErrorArray)	?	FALSE	:	TRUE;
		/*********** [END] VALIDATION OF FORM FIELDS ***********/
		if(TRUE === $this->blnReady)
		{
			for($k=0;$k<=count($_POST['med_pk_'.$i]);$k++)
			{
				if("" != $arr['publications_'.$i][$k])
				{
					$query	= "INSERT INTO `{$this->mTableName}`
					(
						`{$this->mTableRKeyMEOID}`, `{$this->mFieldPrefix}option`, `{$this->mFieldPrefix}publications`, `{$this->mFieldPrefix}editions`, `{$this->mFieldPrefix}circulation`, `{$this->mFieldPrefix}type`, `{$this->mFieldPrefix}positions`, `{$this->mFieldPrefix}size`, `{$this->mFieldPrefix}width`, `{$this->mFieldPrefix}height`, `{$this->mFieldPrefix}rate`, `{$this->mFieldPrefix}rate_per_insertion`, `{$this->mFieldPrefix}no_of_insertions`, `{$this->mFieldPrefix}amount`, `{$this->mFieldPrefix}percentage_revenue`, `{$this->mFieldPrefix}cost`, `{$this->mFieldPrefix}other_remarks` , `{$this->mFieldPrefix}insert_dates` , `{$this->mFieldPrefix}ro_rate`
					)
					VALUES (^1^, ^2^, ^3^, ^4^, ^5^, ^6^, ^7^, ^8^, ^9^, ^10^, ^11^, ^12^, ^13^, ^14^, ^15^, ^16^, ^17^, ^18^, ^19^)";
					$record = $this->mDBConnection->Prepare($query, __FILE__, __LINE__)->Execute
					(
						$arr[$this->mTableRKeyMEOID] , $i , $arr['publications_'.$i][$k] , $arr['editions_'.$i][$k] , $arr['circulation_'.$i][$k] , $arr['type_'.$i][$k] , $arr['positions_'.$i][$k] , $arr['size_'.$i][$k] , $arr['width_'.$i][$k] , $arr['height_'.$i][$k] , $arr['rate_'.$i][$k] , $arr['rate_per_insertion_'.$i][$k] , $arr['no_of_insertions_'.$i][$k] , $arr['amount_'.$i][$k] , $arr['percentage_revenue_'.$i][$k] , $arr['cost_'.$i][$k] , $arr['other_remarks_'.$i][$k] , $arr['insert_dates_'.$i][$k] , $arr['ro_rate_'.$i][$k]
					);
				}
			}
			return true;
		}
		$this->mSessionHandler->SetSessionData('ERR_MSG_ARRAY', $this->mErrorArray);
		$this->mSessionHandler->SetSessionData('ERR_FORM_ARRAY', $FORM_VALUES_ARRAY);
		return false;
	}

	/* [Access] public
	 * [purpose] update ME Print
	 * [return] boolean
	*/
	public function UpdateMEPrint($arr = array())
	{
		//print_r($arr);
		//die;
		global $Security;

		$FORM_VALUES_ARRAY				=	$arr;
		/*********** [START] SETTING UP OF FORM FIELD VALUES ***********/
		$this->SetFormFieldValues($arr);
		$arr			=	$this->mFormFieldArray;
		/*********** [END] SETTING UP OF FORM FIELD VALUES ***********/

		/*********** [START] VALIDATION OF FORM FIELDS ***********/
		$this->mValidationHandler->SetDBInfo($this->mTableName, $this->mFieldPrefix, $this->mTablePKey);

		$rules		=	array();
		//$rules[]	=	"required,name,Required.";

		$this->mErrorArray	=	$this->mValidationHandler->ValidateFormValues($arr, $rules);
		$this->mValidationHandler->ReSetDBInfo();
		unset($rules);
		$this->blnReady			=	!empty($this->mErrorArray)	?	FALSE	:	TRUE;
		/*********** [END] VALIDATION OF FORM FIELDS ***********/

		if(TRUE === $this->blnReady)
		{
			for($i=1;$i<=$_POST['opt_count'];$i++)
			{
				for($k=0;$k<=count($_POST['med_pk_'.$i]);$k++)
				{
					if($_POST['med_pk_'.$i][$k]=='NEW' && $_POST['meo_pk_'.$i] > 0)
					{
						$query = "INSERT INTO `{$this->mTableName}`
						(
							`{$this->mTableRKeyMEOID}`, `{$this->mFieldPrefix}option`, `{$this->mFieldPrefix}publications`, `{$this->mFieldPrefix}editions`, `{$this->mFieldPrefix}circulation`, `{$this->mFieldPrefix}type`, `{$this->mFieldPrefix}positions`, `{$this->mFieldPrefix}size`, `{$this->mFieldPrefix}width`, `{$this->mFieldPrefix}height`, `{$this->mFieldPrefix}rate`, `{$this->mFieldPrefix}rate_per_insertion`, `{$this->mFieldPrefix}no_of_insertions`, `{$this->mFieldPrefix}amount`, `{$this->mFieldPrefix}percentage_revenue`, `{$this->mFieldPrefix}cost`, `{$this->mFieldPrefix}other_remarks`, `{$this->mFieldPrefix}insert_dates` , `{$this->mFieldPrefix}ro_rate`
						)
						VALUES (^1^, ^2^, ^3^, ^4^, ^5^, ^6^, ^7^, ^8^, ^9^, ^10^, ^11^, ^12^, ^13^, ^14^, ^15^, ^16^, ^17^, ^18^, ^19^)";
						$record = $this->mDBConnection->Prepare($query, __FILE__, __LINE__)->Execute
						(
							$_POST['meo_pk_'.$i] , $i , $arr['publications_'.$i][$k] , $arr['editions_'.$i][$k] , $arr['circulation_'.$i][$k] , $arr['type_'.$i][$k] , $arr['positions_'.$i][$k] , $arr['size_'.$i][$k] , $arr['width_'.$i][$k] , $arr['height_'.$i][$k] , $arr['rate_'.$i][$k] , $arr['rate_per_insertion_'.$i][$k] , $arr['no_of_insertions_'.$i][$k] , $arr['amount_'.$i][$k] , $arr['percentage_revenue_'.$i][$k] , $arr['cost_'.$i][$k] , $arr['other_remarks_'.$i][$k] , $arr['insert_dates_'.$i][$k] , $arr['ro_rate_'.$i][$k]
						);
					}
					else
					{
						$query	=	"UPDATE
							`{$this->mTableName}` SET `{$this->mTableRKeyMEOID}`= ^2^, `{$this->mFieldPrefix}option` = ^3^, `{$this->mFieldPrefix}publications` = ^4^, `{$this->mFieldPrefix}editions` = ^5^, `{$this->mFieldPrefix}circulation` = ^6^, `{$this->mFieldPrefix}type` = ^7^, `{$this->mFieldPrefix}positions` = ^8^, `{$this->mFieldPrefix}size` = ^9^, `{$this->mFieldPrefix}width` = ^10^, `{$this->mFieldPrefix}height` = ^11^, `{$this->mFieldPrefix}rate` = ^12^, `{$this->mFieldPrefix}rate_per_insertion` = ^13^, `{$this->mFieldPrefix}no_of_insertions` = ^14^, `{$this->mFieldPrefix}amount` = ^15^, `{$this->mFieldPrefix}percentage_revenue` = ^16^, `{$this->mFieldPrefix}cost` = ^17^, `{$this->mFieldPrefix}other_remarks` = ^18^  , `{$this->mFieldPrefix}insert_dates` = ^19^  , `{$this->mFieldPrefix}ro_rate` = ^20^
							WHERE `{$this->mTablePKey}` = ^1^";
						$record	=	$this->mDBConnection->Prepare($query, __FILE__, __LINE__)->Execute
						(
							$_POST['med_pk_'.$i][$k] , $_POST['meo_pk_'.$i] , $i , $arr['publications_'.$i][$k] , $arr['editions_'.$i][$k] , $arr['circulation_'.$i][$k] , $arr['type_'.$i][$k] , $arr['positions_'.$i][$k] , $arr['size_'.$i][$k] , $arr['width_'.$i][$k] , $arr['height_'.$i][$k] , $arr['rate_'.$i][$k] , $arr['rate_per_insertion_'.$i][$k] , $arr['no_of_insertions_'.$i][$k] , $arr['amount_'.$i][$k] , $arr['percentage_revenue_'.$i][$k] , $arr['cost_'.$i][$k] , $arr['other_remarks_'.$i][$k] , $arr['insert_dates_'.$i][$k] , $arr['ro_rate_'.$i][$k]
						);
					}
				}
			}
			return true;
		}

		$this->mSessionHandler->SetSessionData('ERR_MSG_ARRAY', $this->mErrorArray);
		$this->mSessionHandler->SetSessionData('ERR_FORM_ARRAY', $FORM_VALUES_ARRAY);

		return false;
	}

	/* [Access] public
	 * [purpose] fetch Client Option By MEID
	 * [return] record
	*/
	public function GetClientOptionByMEID($me_id=0 , $inWords = false)
	{
		$filter		=	($me_id > 0)	?	" AND `{$this->mTableRKeyMEID}` = ^1^ "	:	'';
		$query		=	"SELECT DISTINCT({$this->mFieldPrefix}option) AS 'OPT' FROM `{$this->mTableName}` WHERE `{$this->mTableRKeyMEOID}` IN (SELECT `{$this->mTableRKeyMEOID}` FROM ".DB_TBL_PREFIX."me_options WHERE meo_option_selected='1' {$filter})";
		$record		=	$this->mDBConnection->Prepare($query, __FILE__ , __LINE__ )->Execute($me_id)->FetchAllAssoc();
		return ($inWords)	?	NumToAlpha($record[0]['OPT']) :	$record[0]['OPT'];
	}

	/* [Access] public
	 * [purpose] fetch ME Details By ID
	 * [return] record
	*/
	public function GetMEDetailsById($id)
	{
		$query		=	"SELECT * FROM `{$this->mTableName}` WHERE `{$this->mTablePKey}` = ^1^";
		$record		=	$this->mDBConnection->Prepare($query, __FILE__ , __LINE__ )->Execute($id)->FetchAllAssoc();
		return $record;
	}

	/* [Access] public
	 * [purpose] fetch ME Details By ME Option ID
	 * [return] record
	*/
	public function GetMEDetailsByMEOId($id= 0 ,$status = -999 ,$str = '' ,$bln_cnt = false)
	{
		$filter		=	($id > 0)	?	" AND `{$this->mTableRKeyMEOID}` = ^1^ "	:	'';
		$filter		=$filter.((0 <= $status)	?	" AND `{$this->mFieldPrefix}deleted` = ^2^ "	:	'');
		$filter		=$filter.(($str!='')	?	" AND `{$this->mFieldPrefix}publications` = ^3^ "	:	'');
		if($bln_cnt)
			$query		=	"SELECT COUNT(*) AS 'CNT' FROM `{$this->mTableName}` WHERE `{$this->mTablePKey}` = `{$this->mTablePKey}` {$filter}";
		else
		$query		=	"SELECT * FROM `{$this->mTableName}` WHERE `{$this->mTablePKey}` = `{$this->mTablePKey}` {$filter}";

		$record		=	$this->mDBConnection->Prepare($query, __FILE__ , __LINE__ )->Execute($id,$status,$str)->FetchAllAssoc();
		//print_r($record);

		return (false === $bln_cnt)? $record : $record[0]['CNT'];
	}

	/* [Access] public
	 * [purpose] Delete ME Detail Print [UPDATION ONLY]
	 * [return] boolean
	*/
	public function DeleteMEDetails($id)
	{
		$query	=	"UPDATE `{$this->mTableName}` SET `{$this->mFieldPrefix}deleted` = 1 WHERE `{$this->mTablePKey}` = ^1^";
		$record	=	$this->mDBConnection->Prepare($query, __FILE__, __LINE__)->Execute($id);
	}

	/* [Access] public
	 * [purpose] fetch Distinct Publication List By ME Option ID
	 * [return] record
	*/
	public function GetPublicationsByMEOID($status=-999,$id=-999)
	{
		$filter		=(0 <= $status)	?	" AND `{$this->mFieldPrefix}deleted` = ^1^ "	:	'';
		$filter		=$filter.(($id > 0)	?	" AND `{$this->mTableRKeyMEOID}` = ^2^ "	:	'');
		$query		=	"SELECT DISTINCT({$this->mFieldPrefix}publications),{$this->mFieldPrefix}option FROM `{$this->mTableName}` WHERE `{$this->mTablePKey}` = `{$this->mTablePKey}` {$filter}";
		$record		=	$this->mDBConnection->Prepare($query, __FILE__ , __LINE__ )->Execute($status,$id)->FetchAllAssoc();
		return $record;
	}

	/* [Access] public
	 * [purpose] fetch Publication By ID
	 * [return] record
	*/
	public function GetPublicationByID($id=-999)
	{
		$filter		=(0 <= $id)	?	" AND `{$this->mTablePKey}` = ^1^ "	:	'';
		$query		=	"SELECT {$this->mFieldPrefix}publications as 'RES' FROM `{$this->mTableName}` WHERE `{$this->mTablePKey}` = `{$this->mTablePKey}` {$filter}";
		$record		=	$this->mDBConnection->Prepare($query, __FILE__ , __LINE__ )->Execute($id)->FetchAllAssoc();
		return $record[0]['RES'];
	}

	/* [Access] public
	 * [purpose] fetch Proceeded ME Options By MEID
	 * [return] record
	*/
	public function GetProceededMEOptionsByMEId($id= 0 ,$bln_cnt = false)
	{
		$filter		=	($id > 0)	?	" AND `{$this->mTableRKeyMEID}` = ^1^ AND {$this->mFieldPrefix}provisional_approved = 1 "	:	'';
		if($bln_cnt)
			$query		=	"SELECT COUNT(*) AS 'CNT' FROM `{$this->mTableName}` WHERE `{$this->mTablePKey}` = `{$this->mTablePKey}` {$filter}";
		else
		// $query		=	"SELECT * FROM `{$this->mTableName}` WHERE `{$this->mTablePKey}` = `{$this->mTablePKey}` {$filter}";

		// $query		=	"SELECT DISTINCT iwsa_me_options.meo_id, iwsa_me_details_print.mdp_option FROM iwsa_me_options,iwsa_me_details_print
								 // WHERE iwsa_me_options.meo_id = iwsa_me_options.meo_id AND iwsa_me_details_print.meo_id = iwsa_me_options.meo_id
								 // AND iwsa_me_options.me_id	=	^1^ AND iwsa_me_options.meo_provisional_approved	=	1 ";

		$query		=	"SELECT DISTINCT iwsa_me_options.meo_id, iwsa_me_details_print.mdp_option, iwsa_me_master.me_client_approved
								 FROM iwsa_me_options,iwsa_me_details_print , iwsa_me_master
								 WHERE iwsa_me_options.meo_id = iwsa_me_options.meo_id AND iwsa_me_details_print.meo_id = iwsa_me_options.meo_id
								 AND iwsa_me_options.me_id = iwsa_me_master.me_id AND iwsa_me_options.me_id	=	^1^
								 AND iwsa_me_options.meo_provisional_approved	=	1 AND iwsa_me_master.me_client_approved = 0 ";

		$record		=	$this->mDBConnection->Prepare($query, __FILE__ , __LINE__ )->Execute($id)->FetchAllAssoc();
		return (false === $bln_cnt)? $record : $record[0]['CNT'];
	}

	/* [Access] public
	 * [purpose] fetch Distinct Publication List By ME Option ID
	 * [return] record
	*/
	public function CheckIfRaised($detailID = -999)
	{
		$query		=	" SELECT rdp_raised AS 'RAISED' FROM iwsa_ro_details_print WHERE rdp_deleted = '0' AND rdpm_id IN ( SELECT rdpm_id FROM iwsa_ro_details_print_master WHERE rdpm_deleted = '0' AND mdp_id IN (^1^) ) ";
		$record		=	$this->mDBConnection->Prepare($query, __FILE__ , __LINE__ )->Execute($detailID)->FetchAllAssoc();
		return (((int)$record[0]['RAISED']) > 0)	?	true	:	false;
	}

}

?>
