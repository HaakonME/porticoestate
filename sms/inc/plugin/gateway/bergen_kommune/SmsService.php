<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage communication
	* @category core
 	* @version $Id: SmsService.php 4237 2009-11-27 23:17:21Z sigurd $
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
	 * UserContextRest
	 */
	class UserContextRest
	{
		/**
		 * @access public
		 * @var UserContext
		 */
		public $userContext;
	}

	/**
	 * UserContext
	 */
	class UserContext
	{
		/**
		 * @access public
		 * @var string
		 */
		public $userid;
		/**
		 * @access public
		 * @var string
		 */
		public $onBehalfOfId;
		/**
		 * @access public
		 * @var string
		 */
		public $appid;
		/**
		 * @access public
		 * @var string
		 */
		public $transactionid;
	}

	/**
	 * MeldingsStatus
	 */
	class MeldingsStatus
	{
		/**
		 * @access public
		 * @var integer
		 */
		public $id;
		/**
		 * @access public
		 * @var tnsSendingsStatus
		 */
		public $status;
		/**
		 * @access public
		 * @var string
		 */
		public $feiltekst;
	}


	/**
	 * Melding
	 */
	class Melding
	{
		/**
		 * @access public
		 * @var string
		 */
		public $tlfmottaker;
		/**
		 * @access public
		 * @var string
		 */
		public $tekst;
		/**
		 * @access public
		 * @var string
		 */
		public $orgnr;
	}

	/**
	 * InnkommendeMelding
	 */
	class InnkommendeMelding
	{
		/**
		 * @access public
		 * @var string
		 */
		public $tlfavsender;
		/**
		 * @access public
		 * @var string
		 */
		public $tekst;
		/**
		 * @access public
		 * @var string
		 */
		public $kodeord;
	}

	/**
	 * SendingsStatus
	 */
	class SendingsStatus
	{
	}

	/**
	 * hentStatuser
	 */
	class hentStatuser
	{
		/**
		 * @access public
		 * @var UserContext
		 */
		public $userContext;
		/**
		 * @access public
		 * @var MeldingsStatus[]
		 */
		public $statuser;
	}


	/**
	 * hentStatuserResponse
	 */
	class hentStatuserResponse
	{
		/**
		 * @access public
		 * @var MeldingsStatus[]
		 */
		public $return;
	}

	/**
	 * hentStatus
	 */
	class hentStatus
	{
		/**
		 * @access public
		 * @var UserContext
		 */
		public $userContext;
		/**
		 * @access public
		 * @var MeldingsStatus
		 */
		public $status;
	}

	/**
	 * hentStatusResponse
	 */
	class hentStatusResponse
	{
		/**
		 * @access public
		 * @var MeldingsStatus
		 */
		public $return;
	}

	/**
	 * sendMelding
	 */
	class sendMelding
	{
		/**
		 * @access public
		 * @var UserContext
		 */
		public $userContext;
		/**
		 * @access public
		 * @var Melding
		 */
		public $melding;
	}

	/**
	 * sendMeldingResponse
	 */
	class sendMeldingResponse
	{
		/**
		 * @access public
		 * @var MeldingsStatus
		 */
		public $return;
	}

	/**
	 * sendMeldinger
	 */
	class sendMeldinger
	{
		/**
		 * @access public
		 * @var UserContext
		 */
		public $userContext;
		/**
		 * @access public
		 * @var Melding[]
		 */
		public $meldinger;
	}

	/**
	 * sendMeldingerResponse
	 */
	class sendMeldingerResponse
	{
		/**
		 * @access public
		 * @var MeldingsStatus[]
		 */
		public $return;
	}

	/**
	 * getNyeInnkommendeMeldinger
	 */
	class getNyeInnkommendeMeldinger
	{
		/**
		 * @access public
		 * @var UserContext
		 */
		public $userContext;
		/**
		 * @access public
		 * @var string
		 */
		public $kodeord;
	}

	/**
	 * getNyeInnkommendeMeldingerResponse
	 */
	class getNyeInnkommendeMeldingerResponse
	{
		/**
		 * @access public
		 * @var InnkommendeMelding[]
		 */
		public $return;
	}

	/**
	 * getInnkommendeMeldinger
	 */
	class getInnkommendeMeldinger
	{
		/**
		 * @access public
		 * @var UserContext
		 */
		public $userContext;
		/**
		 * @access public
		 * @var string
		 */
		public $kodeord;
		/**
		 * @access public
		 * @var dateTime
		 */
		public $fra;
		/**
		 * @access public
		 * @var dateTime
		 */
		public $til;
	}

	/**
	 * getInnkommendeMeldingerResponse
	 */
	class getInnkommendeMeldingerResponse
	{
		/**
		 * @access public
		 * @var InnkommendeMelding[]
		 */
		public $return;
	}

	/**
	 * SmsService
	 * @author WSDLInterpreter
	 */
	class SmsService extends SoapClient
	{
		/**
		 * Default class map for wsdl=>php
		 * @access private
		 * @var array
		 */
		private static $classmap = array
		(
			"UserContextRest" => "UserContextRest",
			"UserContext" => "UserContext",
			"MeldingsStatus" => "MeldingsStatus",
			"Melding" => "Melding",
			"InnkommendeMelding" => "InnkommendeMelding",
			"SendingsStatus" => "SendingsStatus",
			"hentStatuser" => "hentStatuser",
			"hentStatuserResponse" => "hentStatuserResponse",
			"hentStatus" => "hentStatus",
			"hentStatusResponse" => "hentStatusResponse",
			"sendMelding" => "sendMelding",
			"sendMeldingResponse" => "sendMeldingResponse",
			"sendMeldinger" => "sendMeldinger",
			"sendMeldingerResponse" => "sendMeldingerResponse",
			"getNyeInnkommendeMeldinger" => "getNyeInnkommendeMeldinger",
			"getNyeInnkommendeMeldingerResponse" => "getNyeInnkommendeMeldingerResponse",
			"getInnkommendeMeldinger" => "getInnkommendeMeldinger",
			"getInnkommendeMeldingerResponse" => "getInnkommendeMeldingerResponse",
		);

		/**
		 * Constructor using wsdl location and options array
		 * @param string $wsdl WSDL location for this service
		 * @param array $options Options for the SoapClient
		 */
		public function __construct($wsdl="/home/sn5607/Documents/sms_gateway/SmsService-v1.xml", $options=array())
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
			foreach ($arguments as $arg)
			{
			    $type = gettype($arg);
			    if ($type == "object")
			    {
			        $type = get_class($arg);
			    }
			    $variables .= "(".$type.")";
			}
			if (!in_array($variables, $validParameters))
			{
			    throw new Exception("Invalid parameter types: ".str_replace(")(", ", ", $variables));
			}
			return true;
		}

		/**
		 * Service Call: hentStatuser
		 * Parameter options:
		 * (hentStatuser) parameters
		 * @param mixed,... See function description for parameter options
		 * @return hentStatuserResponse
		 * @throws Exception invalid function signature message
		 */
		public function hentStatuser($mixed = null)
		{
			$validParameters = array
			(
				"(hentStatuser)",
			);
			$args = func_get_args();
			$this->_checkArguments($args, $validParameters);
			return $this->__soapCall("hentStatuser", $args);
		}

		/**
		 * Service Call: hentStatus
		 * Parameter options:
		 * (hentStatus) parameters
		 * @param mixed,... See function description for parameter options
		 * @return hentStatusResponse
		 * @throws Exception invalid function signature message
		 */
		public function hentStatus($mixed = null)
		{
			$validParameters = array
			(
				"(hentStatus)",
			);
			$args = func_get_args();
			$this->_checkArguments($args, $validParameters);
			return $this->__soapCall("hentStatus", $args);
		}

		/**
		 * Service Call: sendMelding
		 * Parameter options:
		 * (sendMelding) parameters
		 * @param mixed,... See function description for parameter options
		 * @return sendMeldingResponse
		 * @throws Exception invalid function signature message
		 */
		public function sendMelding($mixed = null)
		{
			$validParameters = array
			(
				"(sendMelding)",
			);
			$args = func_get_args();
			$this->_checkArguments($args, $validParameters);
			return $this->__soapCall("sendMelding", $args);
		}


		/**
		 * Service Call: sendMeldinger
		 * Parameter options:
		 * (sendMeldinger) parameters
		 * @param mixed,... See function description for parameter options
		 * @return sendMeldingerResponse
		 * @throws Exception invalid function signature message
		 */
		public function sendMeldinger($mixed = null)
		{
			$validParameters = array
			(
				"(sendMeldinger)",
			);
			$args = func_get_args();
			$this->_checkArguments($args, $validParameters);
			return $this->__soapCall("sendMeldinger", $args);
		}


		/**
		 * Service Call: getNyeInnkommendeMeldinger
		 * Parameter options:
		 * (getNyeInnkommendeMeldinger) parameters
		 * @param mixed,... See function description for parameter options
		 * @return getNyeInnkommendeMeldingerResponse
		 * @throws Exception invalid function signature message
		 */
		public function getNyeInnkommendeMeldinger($mixed = null)
		{
			$validParameters = array
			(
				"(getNyeInnkommendeMeldinger)",
			);
			$args = func_get_args();
			$this->_checkArguments($args, $validParameters);
			return $this->__soapCall("getNyeInnkommendeMeldinger", $args);
		}


		/**
		 * Service Call: getInnkommendeMeldinger
		 * Parameter options:
		 * (getInnkommendeMeldinger) parameters
		 * @param mixed,... See function description for parameter options
		 * @return getInnkommendeMeldingerResponse
		 * @throws Exception invalid function signature message
		 */
		public function getInnkommendeMeldinger($mixed = null)
		{
			$validParameters = array
			(
				"(getInnkommendeMeldinger)",
			);
			$args = func_get_args();
			$this->_checkArguments($args, $validParameters);
			return $this->__soapCall("getInnkommendeMeldinger", $args);
		}
	}
