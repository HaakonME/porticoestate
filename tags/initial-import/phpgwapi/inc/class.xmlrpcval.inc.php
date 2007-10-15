<?php
	/**
	* XMLRPC value
	* @author Edd Dumbill <edd@usefulinc.com>
	* @copyright Copyright (C) 1999-2001 Edd Dumbill
	* @package phpgwapi
	* @subpackage xml
	* @version $Id: class.xmlrpcval.inc.php,v 1.12 2006/09/17 11:18:31 skwashd Exp $
	*/

	// License is granted to use or modify this software ("XML-RPC for PHP")
	// for commercial or non-commercial use provided the copyright of the author
	// is preserved in any distributed or derivative work.

	// THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESSED OR
	// IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
	// OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
	// IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
	// INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
	// NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
	// DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
	// THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	// (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
	// THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


	/**
	* XMLRPC value
	* 
	* @package phpgwapi
	* @subpackage xml
	*/
	class xmlrpcval
	{
		var $me=array();
		var $mytype=0;
		var $_php_class=null;

		/**
		* @param mixed $val
		* @param string $type any valid xmlrpc type name (lowercase). If null, 'string' is assumed
		*/
		function xmlrpcval($val=-1, $type='')
		{
			/// @todo: optimization creep - do not call addXX, do it all inline.
			/// downside: booleans will not be coerced anymore
			if($val!==-1 || $type!='')
			{
				// optimization creep: inlined all work done by constructor
				switch($type)
				{
					case '':
						$this->mytype=1;
						$this->me['string']=$val;
						break;
					case 'i4':
					case 'int':
					case 'double':
					case 'string':
					case 'boolean':
					case 'dateTime.iso8601':
					case 'base64':
					case 'null':
						$this->mytype=1;
						$this->me[$type]=$val;
						break;
					case 'array':
						$this->mytype=2;
						$this->me['array']=$val;
						break;
					case 'struct':
						$this->mytype=3;
						$this->me['struct']=$val;
						break;
					default:
						error_log("XML-RPC: xmlrpcval::xmlrpcval: not a known type ($type)");
				}
				/*if($type=='')
				{
					$type='string';
				}
				if($GLOBALS['xmlrpcTypes'][$type]==1)
				{
					$this->addScalar($val,$type);
				}
				elseif($GLOBALS['xmlrpcTypes'][$type]==2)
				{
					$this->addArray($val);
				}
				elseif($GLOBALS['xmlrpcTypes'][$type]==3)
				{
					$this->addStruct($val);
				}*/
			}
		}

		/**
		* Add a single php value to an (unitialized) xmlrpcval
		* @param mixed $val
		* @param string $type
		* @return int 1 or 0 on failure
		*/
		function addScalar($val, $type='string')
		{
			$typeof=@$GLOBALS['xmlrpcTypes'][$type];
			if($typeof!=1)
			{
				error_log("XML-RPC: xmlrpcval::addScalar: not a scalar type ($type)");
				return 0;
			}

			// coerce booleans into correct values
			// NB: we should iether do it for datetimes, integers and doubles, too,
			// or just plain remove this check, implemnted on booleans only...
			if($type==$GLOBALS['xmlrpcBoolean'])
			{
				if(strcasecmp($val,'true')==0 || $val==1 || ($val==true && strcasecmp($val,'false')))
				{
					$val=true;
				}
				else
				{
					$val=false;
				}
			}

			switch($this->mytype)
			{
				case 1:
					error_log('XML-RPC: xmlrpcval::addScalar: scalar xmlrpcval can have only one value');
					return 0;
				case 3:
					error_log('XML-RPC: xmlrpcval::addScalar: cannot add anonymous scalar to struct xmlrpcval');
					return 0;
				case 2:
					// we're adding a scalar value to an array here
					//$ar=$this->me['array'];
					//$ar[]=&new xmlrpcval($val, $type);
					//$this->me['array']=$ar;
					// Faster (?) avoid all the costly array-copy-by-val done here...
					$this->me['array'][]=&new xmlrpcval($val, $type);
					return 1;
				default:
					// a scalar, so set the value and remember we're scalar
					$this->me[$type]=$val;
					$this->mytype=$typeof;
					return 1;
			}
		}

		/**
		* Add an array of xmlrpcval objects to an xmlrpcval
		* @param array $vals
		* @return int 1 or 0 on failure
		* @access public
		*
		* @todo add some checking for $vals to be an array of xmlrpcvals?
		*/
		function addArray($vals)
		{
			if($this->mytype==0)
			{
				$this->mytype=$GLOBALS['xmlrpcTypes']['array'];
				$this->me['array']=$vals;
				return 1;
			}
			elseif($this->mytype==2)
			{
				// we're adding to an array here
				$this->me['array'] = array_merge($this->me['array'], $vals);
				return 1;
			}
			else
			{
				error_log('XML-RPC: xmlrpcval::addArray: already initialized as a [' . $this->kindOf() . ']');
				return 0;
			}
		}

		/**
		* Add an array of named xmlrpcval objects to an xmlrpcval
		* @param array $vals
		* @return int 1 or 0 on failure
		* @access public
		*
		* @todo add some checking for $vals to be an array?
		*/
		function addStruct($vals)
		{
			if($this->mytype==0)
			{
				$this->mytype=$GLOBALS['xmlrpcTypes']['struct'];
				$this->me['struct']=$vals;
				return 1;
			}
			elseif($this->mytype==3)
			{
				// we're adding to a struct here
				$this->me['struct'] = array_merge($this->me['struct'], $vals);
				return 1;
			}
			else
			{
				error_log('XML-RPC: xmlrpcval::addStruct: already initialized as a [' . $this->kindOf() . ']');
				return 0;
			}
		}

		// poor man's version of print_r ???
		// DEPRECATED!
		function dump($ar)
		{
			foreach($ar as $key => $val)
			{
				echo "$key => $val<br />";
				if($key == 'array')
				{
					while(list($key2, $val2) = each($val))
					{
						echo "-- $key2 => $val2<br />";
					}
				}
			}
		}

		/**
		* Returns a string containing "struct", "array" or "scalar" describing the base type of the value
		* @return string
		* @access public
		*/
		function kindOf()
		{
			switch($this->mytype)
			{
				case 3:
					return 'struct';
					break;
				case 2:
					return 'array';
					break;
				case 1:
					return 'scalar';
					break;
				default:
					return 'undef';
			}
		}

		/**
		* @access private
		*/
		function serializedata($typ, $val, $charset_encoding='')
		{
			$rs='';
			switch(@$GLOBALS['xmlrpcTypes'][$typ])
			{
				case 1:
					switch($typ)
					{
						case $GLOBALS['xmlrpcBase64']:
							$rs.="<${typ}>" . base64_encode($val) . "</${typ}>";
							break;
						case $GLOBALS['xmlrpcBoolean']:
							$rs.="<${typ}>" . ($val ? '1' : '0') . "</${typ}>";
							break;
						case $GLOBALS['xmlrpcString']:
							// G. Giunta 2005/2/13: do NOT use htmlentities, since
							// it will produce named html entities, which are invalid xml
							$rs.="<${typ}>" . xmlrpc_encode_entitites($val, $GLOBALS['xmlrpc_internalencoding'], $charset_encoding). "</${typ}>";
							break;
						case $GLOBALS['xmlrpcInt']:
						case $GLOBALS['xmlrpcI4']:
							$rs.="<${typ}>".(int)$val."</${typ}>";
							break;
						case $GLOBALS['xmlrpcDouble']:
							$rs.="<${typ}>".(double)$val."</${typ}>";
							break;
						case $GLOBALS['xmlrpcNull']:
							$rs.="<nil/>";
							break;
						default:
							// no standard type value should arrive here, but provide a possibility
							// for xmlrpcvals of unknown type...
							$rs.="<${typ}>${val}</${typ}>";
					}
					break;
				case 3:
					// struct
					if ($this->_php_class)
					{
						$rs.='<struct php_class="' . $this->_php_class . "\">\n";
					}
					else
					{
						$rs.="<struct>\n";
					}
					foreach($val as $key2 => $val2)
					{
						$rs.='<member><name>'.xmlrpc_encode_entitites($key2, $GLOBALS['xmlrpc_internalencoding'], $charset_encoding)."</name>\n";
						//$rs.=$this->serializeval($val2);
						$rs.=$val2->serialize($charset_encoding);
						$rs.="</member>\n";
					}
					$rs.='</struct>';
					break;
				case 2:
					// array
					$rs.="<array>\n<data>\n";
					for($i=0; $i<count($val); $i++)
					{
						//$rs.=$this->serializeval($val[$i]);
						$rs.=$val[$i]->serialize($charset_encoding);
					}
					$rs.="</data>\n</array>";
					break;
				default:
					break;
			}
			return $rs;
		}

		/**
		* Returns xml representation of the value. XML prologue not included
		* @param string $charset_encoding the charset to be used for serialization. if null, US-ASCII is assumed
		* @return string
		* @access public
		*/
		function serialize($charset_encoding='')
		{
			// add check? slower, but helps to avoid recursion in serializing broken xmlrpcvals...
			//if (is_object($o) && (get_class($o) == 'xmlrpcval' || is_subclass_of($o, 'xmlrpcval')))
			//{
				reset($this->me);
				list($typ, $val) = each($this->me);
				return '<value>' . $this->serializedata($typ, $val, $charset_encoding) . "</value>\n";
			//}
		}

		// DEPRECATED
		function serializeval($o)
		{
			// add check? slower, but helps to avoid recursion in serializing broken xmlrpcvals...
			//if (is_object($o) && (get_class($o) == 'xmlrpcval' || is_subclass_of($o, 'xmlrpcval')))
			//{
				$ar=$o->me;
				reset($ar);
				list($typ, $val) = each($ar);
				return '<value>' . $this->serializedata($typ, $val) . "</value>\n";
			//}
		}

		/**
		* Checks wheter a struct member with a given name is present.
		* Works only on xmlrpcvals of type struct.
		* @param string $m the name of the struct member to be looked up
		* @return boolean
		* @access public
		*/
		function structmemexists($m)
		{
			return array_key_exists($m, $this->me['struct']);
		}

		/**
		* Returns the value of a given struct member (an xmlrpcval object in itself).
		* Will raise a php warning if struct member of given name does not exist
		* @param string $m the name of the struct member to be looked up
		* @return xmlrpcval
		* @access public
		*/
		function structmem($m)
		{
			return $this->me['struct'][$m];
		}

		/**
		* Reset internal pointer for xmlrpcvals of type struct.
		* @access public
		*/
		function structreset()
		{
			reset($this->me['struct']);
		}

		/**
		* Return next member element for xmlrpcvals of type struct.
		* @return xmlrpcval
		* @access public
		*/
		function structeach()
		{
			return each($this->me['struct']);
		}

		// DEPRECATED! this code looks like it is very fragile and has not been fixed
		// for a long long time. Shall we remove it for 2.0?
		function getval()
		{
			// UNSTABLE
			reset($this->me);
			list($a,$b)=each($this->me);
			// contributed by I Sofer, 2001-03-24
			// add support for nested arrays to scalarval
			// i've created a new method here, so as to
			// preserve back compatibility

			if(is_array($b))
			{
				@reset($b);
				while(list($id,$cont) = @each($b))
				{
					$b[$id] = $cont->scalarval();
				}
			}

			// add support for structures directly encoding php objects
			if(is_object($b))
			{
				$t = get_object_vars($b);
				@reset($t);
				while(list($id,$cont) = @each($t))
				{
					$t[$id] = $cont->scalarval();
				}
				@reset($t);
				while(list($id,$cont) = @each($t))
				{
					@$b->$id = $cont;
				}
			}
			// end contrib
			return $b;
		}

		/**
		* Returns the value of a scalar xmlrpcval
		* @return mixed
		* @access public
		*/
		function scalarval()
		{
			reset($this->me);
			list(,$b)=each($this->me);
			return $b;
		}

		/**
		* Returns the type of the xmlrpcval.
		* For integers, 'int' is always returned in place of 'i4'
		* @return string
		* @access public
		*/
		function scalartyp()
		{
			reset($this->me);
			list($a,)=each($this->me);
			if($a==$GLOBALS['xmlrpcI4'])
			{
				$a=$GLOBALS['xmlrpcInt'];
			}
			return $a;
		}

		/**
		* Returns the m-th member of an xmlrpcval of struct type
		* @param integer $m the index of the value to be retrieved (zero based)
		* @return xmlrpcval
		* @access public
		*/
		function arraymem($m)
		{
			return $this->me['array'][$m];
		}

		/**
		* Returns the number of members in an xmlrpcval of array type
		* @return integer
		* @access public
		*/
		function arraysize()
		{
			return count($this->me['array']);
		}

		/**
		* Returns the number of members in an xmlrpcval of struct type
		* @return integer
		* @access public
		*/
		function structsize()
		{
			return count($this->me['struct']);
		}
	}
?>
