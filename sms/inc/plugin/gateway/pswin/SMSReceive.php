<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2011 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage communication
	* @category core
 	* @version $Id: SMSReceive.php 4237 2009-11-27 23:17:21Z sigurd $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * soap client for pswin.com SMS service
	 * this code is generated by the http://code.google.com/p/wsdl2php-interpreter/ 
	 *
	 * @package phpgroupware
	 * @subpackage sms
	 */
	
	/**
	 * ReceiveSMSMessage
	 */
	class ReceiveSMSMessage
	{
		/**
		 * @access public
		 * @var IncomingSMSMessage
		 */
		public $m;
	}
	
	/**
	 * IncomingSMSMessage
	 */
	class IncomingSMSMessage
	{
		/**
		 * @access public
		 * @var sstring
		 */
		public $ReceiverNumber;
		/**
		 * @access public
		 * @var sstring
		 */
		public $SenderNumber;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Text;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Network;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Address;
		/**
		 * @access public
		 * @var GSMPosition
		 */
		public $Position;
	}
	
	/**
	 * GSMPosition
	 */
	class GSMPosition
	{
		/**
		 * @access public
		 * @var sstring
		 */
		public $Longitude;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Lattitude;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Radius;
		/**
		 * @access public
		 * @var sstring
		 */
		public $County;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Council;
		/**
		 * @access public
		 * @var sstring
		 */
		public $CouncilNumber;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Place;
		/**
		 * @access public
		 * @var sstring
		 */
		public $SubPlace;
		/**
		 * @access public
		 * @var sstring
		 */
		public $ZipCode;
		/**
		 * @access public
		 * @var sstring
		 */
		public $City;
	}
	
	/**
	 * ReceiveSMSMessageResponse
	 */
	class ReceiveSMSMessageResponse
	{
		/**
		 * @access public
		 * @var ReturnValue
		 */
		public $ReceiveSMSMessageResult;
	}
	
	/**
	 * ReturnValue
	 */
	class ReturnValue
	{
		/**
		 * @access public
		 * @var sint
		 */
		public $Code;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Description;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Reference;
	}
	
	/**
	 * ReceiveDeliveryReport
	 */
	class ReceiveDeliveryReport
	{
		/**
		 * @access public
		 * @var DeliveryReport
		 */
		public $dr;
	}
	
	/**
	 * DeliveryReport
	 */
	class DeliveryReport
	{
		/**
		 * @access public
		 * @var sstring
		 */
		public $State;
		/**
		 * @access public
		 * @var sstring
		 */
		public $ReceiverNumber;
		/**
		 * @access public
		 * @var sstring
		 */
		public $DeliveryTime;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Reference;
	}
	
	/**
	 * ReceiveDeliveryReportResponse
	 */
	class ReceiveDeliveryReportResponse
	{
		/**
		 * @access public
		 * @var ReturnValue
		 */
		public $ReceiveDeliveryReportResult;
	}
	
	/**
	 * ReceiveMMSMessage
	 */
	class ReceiveMMSMessage
	{
		/**
		 * @access public
		 * @var IncomingMMSMessage
		 */
		public $m;
	}
	
	/**
	 * IncomingMMSMessage
	 */
	class IncomingMMSMessage
	{
		/**
		 * @access public
		 * @var sstring
		 */
		public $ReceiverNumber;
		/**
		 * @access public
		 * @var sstring
		 */
		public $SenderNumber;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Subject;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Network;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Address;
		/**
		 * @access public
		 * @var GSMPosition
		 */
		public $Position;
		/**
		 * @access public
		 * @var sbase64Binary
		 */
		public $Data;
	}
	
	/**
	 * ReceiveMMSMessageResponse
	 */
	class ReceiveMMSMessageResponse
	{
		/**
		 * @access public
		 * @var ReturnValue
		 */
		public $ReceiveMMSMessageResult;
	}
	
	/**
	 * SMSReceive
	 * @author WSDLInterpreter
	 */
	class SMSReceive extends SoapClient
	{
		/**
		 * Default class map for wsdl=>php
		 * @access private
		 * @var array
		 */
		private static $classmap = array(
			"ReceiveSMSMessage" => "ReceiveSMSMessage",
			"IncomingSMSMessage" => "IncomingSMSMessage",
			"GSMPosition" => "GSMPosition",
			"ReceiveSMSMessageResponse" => "ReceiveSMSMessageResponse",
			"ReturnValue" => "ReturnValue",
			"ReceiveDeliveryReport" => "ReceiveDeliveryReport",
			"DeliveryReport" => "DeliveryReport",
			"ReceiveDeliveryReportResponse" => "ReceiveDeliveryReportResponse",
			"ReceiveMMSMessage" => "ReceiveMMSMessage",
			"IncomingMMSMessage" => "IncomingMMSMessage",
			"ReceiveMMSMessageResponse" => "ReceiveMMSMessageResponse",
		);
	
		/**
		 * Constructor using wsdl location and options array
		 * @param string $wsdl WSDL location for this service
		 * @param array $options Options for the SoapClient
		 */
		public function __construct($wsdl='', $options=array())
		{
			foreach(self::$classmap as $wsdlClassName => $phpClassName)
			{
			    if(!isset($options['classmap'][$wsdlClassName]))
			    {
			        $options['classmap'][$wsdlClassName] = $phpClassName;
			    }
			}
			parent::__construct($wsdl, $options);
		}
	
		/**
		 * Checks if an argument list matches against a valid argument type list
		 * @param array $arguments The argument list to check
		 * @param array $validParameters A list of valid argument types
		 * @return boolean true if arguments match against validParameters
		 * @throws Exception invalid function signature message
		 */
		public function _checkArguments($arguments, $validParameters)
		{
			$variables = "";
			foreach ($arguments as $arg) {
			    $type = gettype($arg);
			    if ($type == "object") {
			        $type = get_class($arg);
			    }
			    $variables .= "(".$type.")";
			}
			if (!in_array($variables, $validParameters)) {
			    throw new Exception("Invalid parameter types: ".str_replace(")(", ", ", $variables));
			}
			return true;
		}
	
		/**
		 * Service Call: ReceiveSMSMessage
		 * Parameter options:
		 * (ReceiveSMSMessage) parameters
		 * (ReceiveSMSMessage) parameters
		 * @param mixed,... See function description for parameter options
		 * @return ReceiveSMSMessageResponse
		 * @throws Exception invalid function signature message
		 */
		public function ReceiveSMSMessage($mixed = null)
		{
			$validParameters = array(
				"(ReceiveSMSMessage)",
				"(ReceiveSMSMessage)",
			);
			$args = func_get_args();
			$this->_checkArguments($args, $validParameters);
			$result =  $this->__soapCall("ReceiveSMSMessage", $args);

/*
			echo("<H1>Dumping request headers:</H1></br>" .$this->__getLastRequestHeaders());
			echo("</br><H1>Dumping request:</H1></br>".$this->__getLastRequest());
			echo("</br><H1>Dumping response headers:</H1></br>"	.$this->__getLastResponseHeaders());
			echo("</br><H1>Dumping response:</H1></br>".$this->__getLastResponse());
*/
			return $result;
		}
	
	
		/**
		 * Service Call: ReceiveDeliveryReport
		 * Parameter options:
		 * (ReceiveDeliveryReport) parameters
		 * (ReceiveDeliveryReport) parameters
		 * @param mixed,... See function description for parameter options
		 * @return ReceiveDeliveryReportResponse
		 * @throws Exception invalid function signature message
		 */
		public function ReceiveDeliveryReport($mixed = null)
		{
			$validParameters = array(
				"(ReceiveDeliveryReport)",
				"(ReceiveDeliveryReport)",
			);
			$args = func_get_args();
			$this->_checkArguments($args, $validParameters);
			return $this->__soapCall("ReceiveDeliveryReport", $args);
		}
	
	
		/**
		 * Service Call: ReceiveMMSMessage
		 * Parameter options:
		 * (ReceiveMMSMessage) parameters
		 * (ReceiveMMSMessage) parameters
		 * @param mixed,... See function description for parameter options
		 * @return ReceiveMMSMessageResponse
		 * @throws Exception invalid function signature message
		 */
		public function ReceiveMMSMessage($mixed = null)
		{
			$validParameters = array(
				"(ReceiveMMSMessage)",
				"(ReceiveMMSMessage)",
			);
			$args = func_get_args();
			$this->_checkArguments($args, $validParameters);
			return $this->__soapCall("ReceiveMMSMessage", $args);
		}
	}
