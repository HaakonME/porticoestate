<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage setup
 	* @version $Id$
	*/

	/**
	* Update property version from 0.9.17.500 to 0.9.17.501
	*/

	$test[] = '0.9.17.500';
	function property_upgrade0_9_17_500()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_origin', array(
				'fd' => array(
					'origin' => array('type' => 'varchar','precision' => '12','nullable' => False),
					'origin_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'destination' => array('type' => 'varchar','precision' => '12','nullable' => False),
					'destination_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('origin','origin_id','destination','destination_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_request_origin");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$origin[]=array(
				'origin'	=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'	=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination'=> 'request',
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('request_id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date'),
			);
		}


		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_project_origin");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$origin[]=array(
				'origin'	=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'	=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination'=> 'project',
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('project_id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date'),
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_origin");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$origin[]=array(
				'origin'	=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'	=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination'=> 'entity_' . $GLOBALS['phpgw_setup']->oProc->f('entity_id') . '_' . $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'entry_date'	=> $GLOBALS['phpgw_setup']->oProc->f('entry_date'),
			);
		}

		$rec_count = count($origin);


		for($i=0;$i<$rec_count;$i++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_origin(origin,origin_id,destination,destination_id,entry_date) "
				. "VALUES('"
				.$origin[$i]['origin']."','"
				.$origin[$i]['origin_id']."','"
				.$origin[$i]['destination']."','"
				.$origin[$i]['destination_id']."','"
				.$origin[$i]['entry_date']."')");
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_request_origin');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_project_origin');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_entity_origin');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.501';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.501 to 0.9.17.502
	*/

	$test[] = '0.9.17.501';
	function property_upgrade0_9_17_501()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','descr',array('type' => 'text','nullable' => True));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.502';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.502 to 0.9.17.503
	*/

	$test[] = '0.9.17.502';
	function property_upgrade0_9_17_502()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_acl_location','id',array('type' => 'varchar','precision' => '20','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.tenant_claim', 'Tenant claim')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tenant_claim_category', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '255','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_claim_category (id, descr) VALUES (1, 'Type 1')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_claim_category (id, descr) VALUES (2, 'Type 2')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tenant_claim', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'project_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'tenant_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'amount' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True),
					'b_account_id' => array('type' => 'int','precision' => '4','nullable' => True),
					'category' => array('type' => 'int','precision' => '4','nullable' => False),
					'status' => array('type' => 'varchar','precision' => '8','nullable' => True),
					'remark' => array('type' => 'text','nullable' => True),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','claim_issued',array('type' => 'int','precision' => 2,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.503';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.503 to 0.9.17.504
	*/

	$test[] = '0.9.17.503';
	function property_upgrade0_9_17_503()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type','pk',array('type' => 'text','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type','ix',array('type' => 'text','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type','uc',array('type' => 'text','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_attrib','custom',array('type' => 'int','precision' => 4,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET custom = 1");

		$GLOBALS['phpgw_setup']->oProc->query("SELECT count(*) as cnt FROM fm_location_type");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$locations = $GLOBALS['phpgw_setup']->oProc->f('cnt');

		for ($location_type=1; $location_type<($locations+1); $location_type++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as id FROM fm_location_attrib WHERE type_id = $location_type");
			$GLOBALS['phpgw_setup']->oProc->next_record();
			$id = $GLOBALS['phpgw_setup']->oProc->f('id');
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'location_code';
			$default_attrib['type'][]='V';
			$default_attrib['precision'][] =4*$location_type;
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'loc' . $location_type . '_name';
			$default_attrib['type'][]='V';
			$default_attrib['precision'][] =50;
			$default_attrib['nullable'][] ='True';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'entry_date';
			$default_attrib['type'][]='I';
			$default_attrib['precision'][] =4;
			$default_attrib['nullable'][] ='True';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'category';
			$default_attrib['type'][]='I';
			$default_attrib['precision'][] =4;
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'user_id';
			$default_attrib['type'][]='I';
			$default_attrib['precision'][] =4;
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			$default_attrib['id'][]= $id;
			$default_attrib['column_name'][]= 'remark';
			$default_attrib['type'][]='T';
			$default_attrib['precision'][] = 'NULL';
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['custom'][] ='NULL';
			$id++;

			for ($i=1; $i<$location_type+1; $i++)
			{
				$pk[$i-1]= 'loc' . $i;

				$default_attrib['id'][]= $id;
				$default_attrib['column_name'][]= 'loc' . $i;
				$default_attrib['type'][]='V';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='False';
				$default_attrib['input_text'][] ='dummy';
				$default_attrib['statustext'][] ='dummy';
				$default_attrib['custom'][] ='NULL';
				$id++;
			}

			if ($location_type==1)
			{
				$default_attrib['id'][]= $id;
				$default_attrib['column_name'][]= 'mva';
				$default_attrib['type'][]='I';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='True';
				$default_attrib['input_text'][] ='mva';
				$default_attrib['statustext'][] ='mva';
				$default_attrib['custom'][] = 1;
				$id++;

				$default_attrib['id'][]= $id;
				$default_attrib['column_name'][]= 'kostra_id';
				$default_attrib['type'][]='I';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='True';
				$default_attrib['input_text'][] ='kostra_id';
				$default_attrib['statustext'][] ='kostra_id';
				$default_attrib['custom'][] = 1;
				$id++;

				$default_attrib['id'][]= $id;
				$default_attrib['column_name'][]= 'part_of_town_id';
				$default_attrib['type'][]='I';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='True';
				$default_attrib['input_text'][] ='dummy';
				$default_attrib['statustext'][] ='dummy';
				$default_attrib['custom'][] ='NULL';
				$id++;

				$default_attrib['id'][]= $id;
				$default_attrib['column_name'][]= 'owner_id';
				$default_attrib['type'][]='I';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='True';
				$default_attrib['input_text'][] ='dummy';
				$default_attrib['statustext'][] ='dummy';
				$default_attrib['custom'][] ='NULL';
				$id++;
			}

			if($location_type>1)
			{
				$fk_table='fm_location'. ($location_type-1);

				for ($i=1; $i<$standard['id']; $i++)
				{
					$fk['loc' . $i]	= $fk_table . '.loc' . $i;
				}
			}

			$ix = array('location_code');

			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type SET "
				. "pk ='" . implode(',',$pk) . "',"
				. "ix ='" . implode(',',$ix) . "' WHERE id = $location_type");


			for ($i=0;$i<count($default_attrib['id']);$i++)
			{
				$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,'"
					. $default_attrib['id'][$i] . "','"
					. $default_attrib['column_name'][$i] . "','"
					. $default_attrib['type'][$i] . "',"
					. $default_attrib['precision'][$i] . ",'"
					. $default_attrib['input_text'][$i] . "','"
					. $default_attrib['statustext'][$i] . "','"
					. $default_attrib['nullable'][$i] . "',"
					. $default_attrib['custom'][$i] . ")");
			}

			unset($pk);
			unset($ix);
			unset($default_attrib);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.504';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.504 to 0.9.17.505
	*/

	$test[] = '0.9.17.504';
	function property_upgrade0_9_17_504()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET custom = 1, input_text = 'Remark', statustext='Remark' WHERE column_name = 'remark'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET input_text = column_name, statustext = column_name WHERE custom IS NULL");

		$datatype_precision = array(
			'R' => 4,
			'LB' => 4,
			'AB' => 4,
			'VENDOR' => 4,
			'email' => 64
			);

		$datatype_text = array(
			'V' => 'varchar',
			'I' => 'int',
			'C' => 'char',
			'N' => 'decimal',
			'D' => 'timestamp',
			'T' => 'text',
			'R' => 'int',
			'CH' => 'text',
			'LB' => 'int',
			'AB' => 'int',
			'VENDOR' => 'int',
			'email' => 'varchar'
			);

		$datatype_text[$datatype];

		$GLOBALS['phpgw_setup']->oProc->query("SELECT count(*) as cnt FROM fm_location_type");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$locations = $GLOBALS['phpgw_setup']->oProc->f('cnt');

		for ($location_type=1; $location_type<($locations+1); $location_type++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(attrib_sort) as attrib_sort FROM fm_location_attrib WHERE type_id = $location_type AND column_name = 'remark' AND attrib_sort IS NOT NULL");

			$GLOBALS['phpgw_setup']->oProc->next_record();
			$attrib_sort = $GLOBALS['phpgw_setup']->oProc->f('attrib_sort')+1;


			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET attrib_sort = $attrib_sort WHERE type_id = $location_type AND column_name = 'remark'");

			if($location_type==1)
			{
				$attrib_sort++;

				$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET attrib_sort = $attrib_sort WHERE type_id = $location_type AND column_name = 'mva'");
				$attrib_sort++;

				$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib SET attrib_sort = $attrib_sort WHERE type_id = $location_type AND column_name = 'kostra_id'");
			}

			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location' . $location_type,'change_type',array('type' => 'int','precision' => 4,'nullable' => True));

			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as attrib_id FROM fm_location_attrib WHERE type_id = $location_type");

			$GLOBALS['phpgw_setup']->oProc->next_record();
			$attrib_id = $GLOBALS['phpgw_setup']->oProc->f('attrib_id')+1;

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,$attrib_id, 'change_type', 'I', 4, 'change_type','change_type','True',NULL)");

			if($location_type==4)
			{
				$attrib_id++;
				$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,$attrib_id, 'street_id', 'I', 4, 'street_id','street_id','True',NULL)");


				$attrib_id++;
				$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,$attrib_id, 'street_number', 'V', 10, 'street_number','street_number','True',NULL)");

				$attrib_id++;
				$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,nullable,custom)"
					. " VALUES ( $location_type,$attrib_id, 'tenant_id', 'I', 4, 'tenant_id','tenant_id','True',NULL)");
			}

			$metadata = $GLOBALS['phpgw_setup']->db->metadata('fm_location'.$location_type);

			if(isset($GLOBALS['phpgw_setup']->db->adodb))
			{
				$i = 0;
				foreach($metadata as $key => $val)
				{
					$metadata_temp[$i]['name'] = $key;
					$i++;
				}
				$metadata = $metadata_temp;
				unset ($metadata_temp);
			}

			for ($i=0; $i<count($metadata); $i++)
			{
				$sql = "SELECT * FROM fm_location_attrib WHERE type_id=$location_type AND column_name = '" . $metadata[$i]['name'] . "'";

				$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
				if($GLOBALS['phpgw_setup']->oProc->next_record())
				{
					if(!$precision = $GLOBALS['phpgw_setup']->oProc->f('precision_'))
					{
						$precision = $datatype_precision[$GLOBALS['phpgw_setup']->oProc->f('datatype')];
					}

					if($GLOBALS['phpgw_setup']->oProc->f('nullable')=='True')
					{
						$nullable=True;
					}

					$fd[$metadata[$i]['name']] = array(
					 		'type' => $datatype_text[$GLOBALS['phpgw_setup']->oProc->f('datatype')],
					 		'precision' => $precision,
					 		'nullable' => $nullable,
					 		'default' => stripslashes($GLOBALS['phpgw_setup']->oProc->f('default_value')),
					 		'scale' => $GLOBALS['phpgw_setup']->oProc->f('scale')
					 		);
					unset($precision);
					unset($nullable);
				}
			}

			$fd['exp_date'] = array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp');

			$GLOBALS['phpgw_setup']->oProc->CreateTable(
				'fm_location' . $location_type . '_history', array(
					'fd' => $fd,
					'pk' => array(),
					'fk' => array(),
					'ix' => array(),
					'uc' => array()
				)
			);

			unset($fd);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.505';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.505 to 0.9.17.506
	*/

	$test[] = '0.9.17.505';
	function property_upgrade0_9_17_505()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_wo_hours','category',array('type' => 'int','precision' => 4,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_wo_hours_category', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.506';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.506 to 0.9.17.507
	*/

	$test[] = '0.9.17.506';
	function property_upgrade0_9_17_506()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_safety',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_aesthetics',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_indoor_climate',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_consequential_damage',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_user_gratification',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','d_residential_environment',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_safety',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_aesthetics',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_indoor_climate',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_consequential_damage',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_user_gratification',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','p_residential_environment',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_safety',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_aesthetics',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_indoor_climate',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_consequential_damage',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_user_gratification',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','c_residential_environment',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','authorities_demands',array('type' => 'int','precision' => '2','default' => '0','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','score',array('type' => 'int','precision' => '4','default' => '0','nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_safety = 0 WHERE d_safety IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_aesthetics = 0 WHERE d_aesthetics IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_indoor_climate = 0 WHERE d_indoor_climate IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_consequential_damage = 0 WHERE d_consequential_damage IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_user_gratification = 0 WHERE d_user_gratification IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET d_residential_environment = 0 WHERE d_residential_environment IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_safety = 0 WHERE p_safety IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_aesthetics = 0 WHERE p_aesthetics IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_indoor_climate = 0 WHERE p_indoor_climate IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_consequential_damage = 0 WHERE p_consequential_damage IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_user_gratification = 0 WHERE p_user_gratification IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET p_residential_environment = 0 WHERE p_residential_environment IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_safety = 0 WHERE c_safety IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_aesthetics = 0 WHERE c_aesthetics IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_indoor_climate = 0 WHERE c_indoor_climate IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_consequential_damage = 0 WHERE c_consequential_damage IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_user_gratification = 0 WHERE c_user_gratification IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET c_residential_environment = 0 WHERE c_residential_environment IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET authorities_demands = 0 WHERE authorities_demands IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET score = 0 WHERE score IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_workorder SET act_mtrl_cost = 0 WHERE act_mtrl_cost IS NULL ");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_workorder SET act_vendor_cost = 0 WHERE act_vendor_cost IS NULL ");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.507';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.507 to 0.9.17.508
	*/

	$test[] = '0.9.17.507';
	function property_upgrade0_9_17_507()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_request_condition_type', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'priority_key' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_request_condition', array(
				'fd' => array(
					'request_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'condition_type' => array('type' => 'int','precision' => '4','nullable' => False),
					'degree' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
					'probability' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
					'consequence' => array('type' => 'int','precision' => '4','default' => '0','nullable' => True),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => True),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('request_id','condition_type'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (1, 'safety', 10)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (2, 'aesthetics', 2)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (3, 'indoor climate', 5)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (4, 'consequential damage', 5)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (5, 'user gratification', 4)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition_type (id, descr, priority_key) VALUES (6, 'residential environment', 6)");


		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_request");

		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$condition[] = array(
				'request_id' => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'user_id' => (int)$GLOBALS['phpgw_setup']->oProc->f('owner'),
				'entry_date' => (int)$GLOBALS['phpgw_setup']->oProc->f('entry_date'),
				'd_safety' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_safety'),
				'd_aesthetics' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_aesthetics'),
				'd_indoor_climate' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_indoor_climate'),
				'd_consequential_damage' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_consequential_damage'),
				'd_user_gratification' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_user_gratification'),
				'd_residential_environment' => (int)$GLOBALS['phpgw_setup']->oProc->f('d_residential_environment'),
				'p_safety' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_safety'),
				'p_aesthetics' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_aesthetics'),
				'p_indoor_climate' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_indoor_climate'),
				'p_consequential_damage' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_consequential_damage'),
				'p_user_gratification' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_user_gratification'),
				'p_residential_environment' => (int)$GLOBALS['phpgw_setup']->oProc->f('p_residential_environment'),
				'c_safety' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_safety'),
				'c_aesthetics' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_aesthetics'),
				'c_indoor_climate' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_indoor_climate'),
				'c_consequential_damage' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_consequential_damage'),
				'c_user_gratification' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_user_gratification'),
				'c_residential_environment' => (int)$GLOBALS['phpgw_setup']->oProc->f('c_residential_environment')
			);
		}

		while (is_array($condition) && list(,$value) = each($condition))
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 1 . "',"
				. $value['d_safety']. ","
				. $value['p_safety']. ","
				. $value['c_safety']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 2 . "',"
				. $value['d_aesthetics']. ","
				. $value['p_aesthetics']. ","
				. $value['c_aesthetics']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 3 . "',"
				. $value['d_indoor_climate']. ","
				. $value['p_indoor_climate']. ","
				. $value['c_indoor_climate']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 4 . "',"
				. $value['d_consequential_damage']. ","
				. $value['p_consequential_damage']. ","
				. $value['c_consequential_damage']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 5 . "',"
				. $value['d_user_gratification']. ","
				. $value['p_user_gratification']. ","
				. $value['c_user_gratification']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
				. "VALUES ('"
				. $value['request_id']. "','"
				. 6 . "',"
				. $value['d_residential_environment']. ","
				. $value['p_residential_environment']. ","
				. $value['c_residential_environment']. ","
				. $value['user_id']. ","
				. $value['entry_date']. ")",__LINE__,__FILE__);

			$id = $value['request_id'];



			$sql = "SELECT sum(priority_key * ( degree * probability * ( consequence +1 ))) AS score FROM fm_request_condition"
			 . " JOIN fm_request_condition_type ON (fm_request_condition.condition_type = fm_request_condition_type.id) WHERE request_id = $id";

			$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->next_record();
			$score = $GLOBALS['phpgw_setup']->oProc->f('score');
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET score = $score WHERE id = $id",__LINE__,__FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_request_priority_key');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.508';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.508 to 0.9.17.509
	*/

	$test[] = '0.9.17.508';
	function property_upgrade0_9_17_508()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_custom_function', array(
				'fd' => array(
					'acl_location' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'text','nullable' => True),
					'file_name ' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'active' => array('type' => 'int','precision' => '2','nullable' => True),
					'custom_sort' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('acl_location','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.509';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.509 to 0.9.17.510
	*/

	$test[] = '0.9.17.509';
	function property_upgrade0_9_17_509()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag','item_type',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag','item_id',array('type' => 'varchar','precision' => 20,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf','item_type',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf','item_id',array('type' => 'varchar','precision' => 20,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.510';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.510 to 0.9.17.511
	*/

	$test[] = '0.9.17.510';
	function property_upgrade0_9_17_510()
	{
		$table_def = array(
			'fm_custom' => array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'name' => array('type' => 'varchar','precision' => '100','nullable' => False),
					'sql_text' => array('type' => 'text','nullable' => False),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->m_aTables = $table_def;

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_custom','sql','sql_text');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.511';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.511 to 0.9.17.512
	*/

	$test[] = '0.9.17.511';
	function property_upgrade0_9_17_511()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_attribute','history',array('type' => 'int','precision' => 2,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_entity_history', array(
				'fd' => array(
					'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
					'history_entity_attrib_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
					'history_new_value' => array('type' => 'text','nullable' => False),
					'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
				),
				'pk' => array('history_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.512';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.512 to 0.9.17.513
	*/

	$test[] = '0.9.17.512';
	function property_upgrade0_9_17_512()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'customer_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'customer_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
					'name' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
					'descr' => array('type' => 'text','nullable' => True),
					'status' => array('type' => 'varchar', 'precision' => 10,'nullable' => True),
					'category' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'member_of' => array('type' => 'text','nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'start_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'end_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'termination_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'actual_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'account_id' => array('type' => 'varchar', 'precision' => 20,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_attribute', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'attrib_detail' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0'),
					'list' => array('type' => 'int', 'precision' => 2,'nullable' => True),
					'location_form' => array('type' => 'int', 'precision' => 2,'nullable' => True),
					'lookup_form' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'column_name' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
					'input_text' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
					'statustext' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
					'size' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'datatype' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
					'attrib_sort' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'precision_' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'scale' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'default_value' => array('type' => 'varchar', 'precision' => 18,'nullable' => True),
					'nullable' => array('type' => 'varchar', 'precision' => 5,'nullable' => False,'default' => 'True'),
					'search' => array('type' => 'int', 'precision' => 2,'nullable' => True)
				),
				'pk' => array('id','attrib_detail'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_category', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'descr' => array('type' => 'varchar', 'precision' => 50,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_choice', array(
				'fd' => array(
					'attrib_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'value' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
					'attrib_detail' => array('type' => 'int', 'precision' => 2,'nullable' => False,'default' => '0')
				),
				'pk' => array('attrib_id','id','attrib_detail'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_item', array(
				'fd' => array(
					'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'location_code' => array('type' => 'varchar', 'precision' => 30,'nullable' => True),
					'address' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
					'p_num' => array('type' => 'varchar', 'precision' => 15,'nullable' => True),
					'p_entity_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
					'p_cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
					'descr' => array('type' => 'text','nullable' => True),
					'unit' => array('type' => 'varchar', 'precision' => 10,'nullable' => True),
					'quantity' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'frequency' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'test' => array('type' => 'text','nullable' => True),
					'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'rental_type_id' => array('type' => 'int', 'precision' => 4,'nullable' => True)
				),
				'pk' => array('agreement_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_item_history', array(
				'fd' => array(
					'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'item_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'current_index' => array('type' => 'int', 'precision' => 2,'nullable' => True),
					'this_index' => array('type' => 'decimal', 'precision' => 20, 'scale' => 4,'nullable' => True),
					'cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'index_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'from_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'to_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'tenant_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				),
				'pk' => array('agreement_id','item_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_common', array(
				'fd' => array(
					'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'b_account' => array('type' => 'varchar', 'precision' => 30,'nullable' => True),
					'remark' => array('type' => 'text','nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				),
				'pk' => array('agreement_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_r_agreement_c_history', array(
				'fd' => array(
					'agreement_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'c_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
					'from_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'to_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'current_record' => array('type' => 'int', 'precision' => 2,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'budget_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'actual_cost' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'fraction' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
					'override_fraction' => array('type' => 'decimal', 'precision' => 20, 'scale' => 2,'nullable' => True),
				),
				'pk' => array('agreement_id','c_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.r_agreement', 'Rental agreement')");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.513';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.513 to 0.9.17.514
	*/

	$test[] = '0.9.17.513';
	function property_upgrade0_9_17_513()
	{
		$sql = "SELECT app_version from phpgw_applications WHERE app_name = 'property'";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$version = $GLOBALS['phpgw_setup']->oProc->f('app_version');

		if($version =='0.9.17.513')
		{
			$soadmin_location	= CreateObject('property.soadmin_location','property');

			for ($i=1; $i<=4; $i++)
			{
				$attrib= array(
					'column_name' => 'rental_area',
					'input_text' => 'Rental area',
					'statustext' => 'Rental area',
					'type_id' => $i,
					'lookup_form' => False,
					'list' => False,
					'column_info' => array('type' =>'N',
								'precision' => 20,
								'scale' => 2,
								'default' => '0.00',
								'nullable' => 'True')
					);
				$soadmin_location->add_attrib($attrib);
			}
		}

		$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.514';
		return $GLOBALS['setup_info']['property']['currentver'];
	}

	/**
	* Update property version from 0.9.17.514 to 0.9.17.515
	*/

	$test[] = '0.9.17.514';
	function property_upgrade0_9_17_514()
	{
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_owner_attribute (id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES (1, 1, 'abid', 'Contact', 'Contakt person', NULL, 'AB', 1, 4, NULL, NULL, 'True', NULL)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_owner_attribute (id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES (2, 1, 'org_name', 'Name', 'The name of the owner', NULL, 'V', 2, 50, NULL, NULL, 'True', 1)");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_owner_attribute (id, list, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable, search) VALUES (3, 1, 'remark', 'remark', 'remark', NULL, 'T', 3, NULL, NULL, NULL, 'True', NULL)");

		$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.515';
		return $GLOBALS['setup_info']['property']['currentver'];
	}

	/**
	* Update property version from 0.9.17.515 to 0.9.17.516
	*/

	$test[] = '0.9.17.515';
	function property_upgrade0_9_17_515()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_wo_hours','cat_per_cent',array('type' => 'int','precision' => 4,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.516';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.516 to 0.9.17.517
	*/

	$test[] = '0.9.17.516';
	function property_upgrade0_9_17_516()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.budget', 'Budget')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.budget.obligations', 'Obligations')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_budget_basis', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'b_group' => array('type' => 'varchar','precision' => '4','nullable' => False),
					'district_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'revision' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'budget_cost' => array('type' => 'int', 'precision' => 4,'default' => '0','nullable' => True),
					'remark' => array('type' => 'text','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('year','b_group','district_id','revision')
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_budget', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'b_account_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'district_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'revision' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'access' => array('type' => 'varchar','precision' => '7','nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'budget_cost' => array('type' => 'int', 'precision' => 4,'default' => '0','nullable' => True),
					'remark' => array('type' => 'text','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('year','b_account_id','district_id','revision')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_budget_period', array(
				'fd' => array(
					'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'month' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'b_account_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'percent' => array('type' => 'int','precision' => 4,'default' => '0','nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'remark' => array('type' => 'text','nullable' => True)
				),
				'pk' => array('year','month','b_account_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_budget_cost', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'year' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'month' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'b_account_id' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'amount' => array('type' => 'decimal','precision' => '20','scale' => '2','default' => '0','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('year','month','b_account_id')
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.517';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.517 to 0.9.17.518
	*/

	$test[] = '0.9.17.517';
	function property_upgrade0_9_17_517()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_b_account_category', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account','category',array('type' => 'int','precision' => 4,'nullable' => True));

		$sql = "SELECT id, grouping from fm_b_account";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$grouping[]=array(
				'id' => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'grouping' => $GLOBALS['phpgw_setup']->oProc->f('grouping')
			);
		}

		if (is_array($grouping))
		{
			foreach ($grouping as $entry)
			{
				if((int)$entry['grouping']>0)
				{
					$grouping2[]=$entry['grouping'];

					$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_b_account set category = ". (int)$entry['grouping'] . " WHERE id = " . $entry['id'],__LINE__,__FILE__);
				}

			}
			$grouping2 = array_unique($grouping2);
			foreach ($grouping2 as $entry)
			{
					$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_b_account_category (id, descr) VALUES (" . (int)$entry . ",'" . $entry . "')",__LINE__,__FILE__);
			}

		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.518';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.518 to 0.9.17.519
	*/

	$test[] = '0.9.17.518';
	function property_upgrade0_9_17_518()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_template_hours','entry_date',array('type' => 'int','precision' => 4,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.519';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.519 to 0.9.17.520
	*/

	$test[] = '0.9.17.519';
	function property_upgrade0_9_17_519()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request','start_date',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request','end_date',array('type' => 'int','precision' => 4,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.520';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.520 to 0.9.17.521
	*/

	$test[] = '0.9.17.520';
	function property_upgrade0_9_17_520()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget_basis','distribute_year',array('type' => 'text','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.521';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.521 to 0.9.17.522
	*/

	$test[] = '0.9.17.521';
	function property_upgrade0_9_17_521()
	{
//		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin(); transaction have problem with nested db-objects

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','combined_cost', array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'));

		$sql = "SELECT app_version from phpgw_applications WHERE app_name = 'property'";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$version = $GLOBALS['phpgw_setup']->oProc->f('app_version');

		if($version =='0.9.17.521')
		{
			$db2 = clone($GLOBALS['phpgw_setup']->oProc->m_odb);
			$sql = "SELECT id, budget, calculation from fm_workorder";
			$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
			while($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				if ($GLOBALS['phpgw_setup']->oProc->f('calculation') > 0)
				{
					$combined_cost = ($GLOBALS['phpgw_setup']->oProc->f('calculation') * 1.25); // tax included
				}
				else
				{
					$combined_cost = $GLOBALS['phpgw_setup']->oProc->f('budget');
				}

				if($combined_cost > 0)
				{

					$db2->query("UPDATE fm_workorder SET combined_cost = '$combined_cost' WHERE id = " . (int)$GLOBALS['phpgw_setup']->oProc->f('id'),__LINE__,__FILE__);
				}
			}
		}

//		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.522';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.522 to 0.9.17.523
	*/

	$test[] = '0.9.17.522';
	function property_upgrade0_9_17_522()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','paid', array('type' => 'int','precision' => '2','nullable' => True,'default' => '1'));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.523';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.523 to 0.9.17.524
	*/

	$test[] = '0.9.17.523';
	function property_upgrade0_9_17_523()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.admin', 'Admin')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.admin.entity', 'Admin entity')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_acl_location (id, descr) VALUES ('.admin.location', 'Admin location')");
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.524';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.524 to 0.9.17.525
	*/

	$test[] = '0.9.17.524';
	function property_upgrade0_9_17_524()
	{
//		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin(); transaction have problem with nested db-objects

		$GLOBALS['phpgw_setup']->oProc->query("delete from phpgw_acl where acl_appname = 'property' AND acl_location !='run' ");

		$db2 = clone($GLOBALS['phpgw_setup']->oProc->m_odb);
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_acl_location ");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$db2->query("INSERT INTO phpgw_acl_location (appname,id, descr,allow_grant) VALUES ("
			. " 'property','"
			. $GLOBALS['phpgw_setup']->oProc->f('id') . "','"
			. $GLOBALS['phpgw_setup']->oProc->f('descr') . "',"
			. (int)$GLOBALS['phpgw_setup']->oProc->f('allow_grant') . ")");

		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_acl2 ");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$grantor = 'NULL';
			if($GLOBALS['phpgw_setup']->oProc->f('grantor')>0)
			{
				$grantor = $GLOBALS['phpgw_setup']->oProc->f('grantor');
			}

			$db2->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights, acl_grantor,acl_type) VALUES ("
			. "'property','" 
			. $GLOBALS['phpgw_setup']->oProc->f('acl_location') . "','"
			. $GLOBALS['phpgw_setup']->oProc->f('acl_account') . "','"
			. $GLOBALS['phpgw_setup']->oProc->f('acl_rights') . "',"
			. $grantor . ",'"
			. (int) $GLOBALS['phpgw_setup']->oProc->f('acl_type') . "')");

			unset($grantor);
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_acl_location');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_acl2');

//		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.525';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.525 to 0.9.17.526
	*/

	$test[] = '0.9.17.525';
	function property_upgrade0_9_17_525()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tenant_attribute','input_text',array('type' => 'varchar','precision' => '50','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_vendor_attribute','input_text',array('type' => 'varchar','precision' => '50','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location_attrib','input_text',array('type' => 'varchar','precision' => '50','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_owner_attribute','input_text',array('type' => 'varchar','precision' => '50','nullable' => False));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.526';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.526 to 0.9.17.527
	*/

	$test[] = '0.9.17.526';
	function property_upgrade0_9_17_526()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_attribute','disabled', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_attribute','helpmsg', array('type' => 'text','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.527';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.527 to 0.9.17.528
	*/

	$test[] = '0.9.17.527';
	function property_upgrade0_9_17_527()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_gab_location','location_code',array('type' => 'varchar','precision' => '20','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_gab_location','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location1','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location1_history','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location2','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location2_history','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location3','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location3_history','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location4','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_location4_history','loc1',array('type' => 'varchar','precision' => '6','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tts_tickets','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_project','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_investment','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_document','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_1_1','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_1_2','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_1_3','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_2_1','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_2_2','loc1',array('type' => 'varchar','precision' => '6','nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_attrib set precision_ = '6' where column_name = 'loc1'");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.528';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.528 to 0.9.17.529
	*/

	$test[] = '0.9.17.528';
	function property_upgrade0_9_17_528()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl_location set id = '.agreement', descr = 'Agreement' where id = '.pricebook' AND appname = 'property'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl set acl_location = '.agreement' where acl_location = '.pricebook' AND acl_appname = 'property'");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.529';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.529 to 0.9.17.530
	*/

	$test[] = '0.9.17.529';
	function property_upgrade0_9_17_529()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname, id, descr) VALUES ('property', '.ticket.external', 'Helpdesk External user')");
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','phpgw_account_lid', array('type' => 'varchar','precision' => '25','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','account_lid', array('type' => 'varchar','precision' => '25','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','account_pwd', array('type' => 'varchar','precision' => '32','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','account_status', array('type' => 'char','precision' => '1','nullable' => True,'default' => 'A'));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.530';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.530 to 0.9.17.531
	*/

	$test[] = '0.9.17.530';
	function property_upgrade0_9_17_530()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$fm_tenant = array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'member_of' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'first_name' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'last_name' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'phpgw_account_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'account_lid' => array('type' => 'varchar','precision' => '25','nullable' => True),
				'account_pwd' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'account_status' => array('type' => 'char','precision' => '1','nullable' => True,'default' => 'A')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$fm_tenant2 = array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'member_of' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'first_name' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'last_name' => array('type' => 'varchar','precision' => '30','nullable' => True),
				'contact_phone' => array('type' => 'varchar','precision' => '20','nullable' => True),
				'category' => array('type' => 'int','precision' => '4','nullable' => True),
				'phpgw_account_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'account_lid' => array('type' => 'varchar','precision' => '25','nullable' => True),
				'account_pwd' => array('type' => 'varchar','precision' => '32','nullable' => True),
				'account_status' => array('type' => 'int','precision' => '4','nullable' => True,'default' => '1')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_tenant',$fm_tenant,'phpgw_account_lid');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_tenant',$fm_tenant2,'account_status');
		unset($fm_tenant);
		unset($fm_tenant2);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','phpgw_account_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','account_status', array('type' => 'int','precision' => '4','nullable' => True,'default' => '1'));

		$GLOBALS['phpgw_setup']->oProc->query("SELECT max(id) as id, max(attrib_sort) as attrib_sort FROM fm_tenant_attribute");

		$GLOBALS['phpgw_setup']->oProc->next_record();
		$id = $GLOBALS['phpgw_setup']->oProc->f('id') + 1;
		$attrib_sort = $GLOBALS['phpgw_setup']->oProc->f('attrib_sort') +1;

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_attribute (id, list, search, lookup_form, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($id, NULL, NULL, NULL, 'phpgw_account_id', 'Mapped User', 'Mapped User', NULL, 'user', $attrib_sort, 4, NULL, NULL, 'True')");
		$id++;
		$attrib_sort++;
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_attribute (id, list, search, lookup_form, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($id, NULL, NULL, NULL, 'account_lid', 'User Name', 'User name for login', NULL, 'V', $attrib_sort, 25, NULL, NULL, 'True')");
		$id++;
		$attrib_sort++;
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_attribute (id, list, search, lookup_form, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($id, NULL, NULL, NULL, 'account_pwd', 'Password', 'Users Password', NULL, 'pwd', $attrib_sort, 32, NULL, NULL, 'True')");
		$id++;
		$attrib_sort++;
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_attribute (id, list, search, lookup_form, column_name, input_text, statustext, size, datatype, attrib_sort, precision_, scale, default_value, nullable) VALUES ($id, NULL, NULL, NULL, 'account_status', 'account status', 'account status', NULL, 'LB', $attrib_sort, NULL, NULL, NULL, 'True')");

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_choice (attrib_id, id, value) VALUES ($id, 1, 'Active')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_tenant_choice (attrib_id, id, value) VALUES ($id, 2, 'Banned')");
		unset($id);
		unset($attrib_sort);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.531';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.531 to 0.9.17.532
	*/

	$test[] = '0.9.17.531';
	function property_upgrade0_9_17_531()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','owner_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_owner','owner_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_vendor','owner_id', array('type' => 'int','precision' => '4','nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tenant set owner_id = 6");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_owner set owner_id = 6");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_vendor set owner_id = 6");

		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM fm_cache");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl WHERE acl_appname = 'property' AND acl_location = '.tenant' AND acl_grantor IS NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights, acl_grantor, acl_type) VALUES ('property', '.tenant', '1', '1', '6', '0')");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl WHERE acl_appname = 'property' AND acl_location = '.owner' AND acl_grantor IS NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights, acl_grantor, acl_type) VALUES ('property', '.owner', '1', '1','6', '0')");
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl WHERE acl_appname = 'property' AND acl_location = '.vendor' AND acl_grantor IS NOT NULL");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights, acl_grantor, acl_type) VALUES ('property', '.vendor', '1', '1', '6', '0')");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.532';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.532 to 0.9.17.533
	*/

	$test[] = '0.9.17.532';
	function property_upgrade0_9_17_532()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_template_hours','hours_descr',array('type' => 'text','nullable' => True));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.533';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.533 to 0.9.17.534
	*/

	$test[] = '0.9.17.533';
	function property_upgrade0_9_17_533()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type','list_info', array('type' => 'varchar','precision' => '255','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_location_type','list_address', array('type' => 'int','precision' => '2','nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:1:{i:1;s:1:"1";}' ."' WHERE id = '1'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:2:{i:1;s:1:"1";i:2;s:1:"2";}' ."' WHERE id = '2'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:3:{i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";}' ."' WHERE id = '3'");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_location_type set list_info = '" . 'a:1:{i:1;s:1:"1";}' ."' WHERE id = '4'");
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.534';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.534 to 0.9.17.535
	*/

	$test[] = '0.9.17.534';
	function property_upgrade0_9_17_534()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname, id, descr) VALUES ('property', '.location.1', 'Property')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname, id, descr) VALUES ('property', '.location.2', 'Building')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname, id, descr) VALUES ('property', '.location.3', 'Entrance')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname, id, descr) VALUES ('property', '.location.4', 'Apartment')");
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.535';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.535 to 0.9.17.536
	*/

	$test[] = '0.9.17.535';
	function property_upgrade0_9_17_535()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$table_def = array(
			'fd' => array(
				'id' => array('type' => 'int','precision' => '4','nullable' => False),
				'descr' => array('type' => 'varchar','precision' => '25','nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_dim_d');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecodimd','name','descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecodimd','descr',array('type' => 'varchar','precision' => '25','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecodimd',$table_def,'description');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecodimb','name','descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecodimb','descr',array('type' => 'varchar','precision' => '25','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecodimb',$table_def,'description');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecomva','name','descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecomva','descr',array('type' => 'varchar','precision' => '25','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecomva',$table_def,'description');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecobilagtype','name','descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecobilagtype','descr',array('type' => 'varchar','precision' => '25','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilagtype',$table_def,'description');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('fm_ecobilagtype', 'fm_ecobilag_category');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecoart','name','descr');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecoart','descr',array('type' => 'varchar','precision' => '25','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecoart',$table_def,'description');

		unset($table_def);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.536';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.536 to 0.9.17.537
	*/

	$test[] = '0.9.17.536';
	function property_upgrade0_9_17_536()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_project','end_date',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => 'True'
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.537';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.537 to 0.9.17.538
	*/

	$test[] = '0.9.17.537';
	function property_upgrade0_9_17_537()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_s_agreement_attribute','history',array('type' => 'int','precision' => 2,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_s_agreement_history', array(
				'fd' => array(
					'history_id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'history_record_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_appname' => array('type' => 'varchar','precision' => '64','nullable' => False),
					'history_detail_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_attrib_id' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_owner' => array('type' => 'int','precision' => '4','nullable' => False),
					'history_status' => array('type' => 'char','precision' => '2','nullable' => False),
					'history_new_value' => array('type' => 'text','nullable' => False),
					'history_timestamp' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp')
				),
				'pk' => array('history_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.538';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.538 to 0.9.17.539
	*/

	$test[] = '0.9.17.538';
	function property_upgrade0_9_17_538()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_entity_history','history_entity_attrib_id','history_attrib_id');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.539';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.539 to 0.9.17.540
	*/

	$test[] = '0.9.17.539';
	function property_upgrade0_9_17_539()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','start_ticket',array('type' => 'int','precision' => 2,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.540';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.540 to 0.9.17.541
	*/

	$test[] = '0.9.17.540';
	function property_upgrade0_9_17_540()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.s_agreement.detail', 'Service agreement detail', 'property', $allow_grant = false, $custom_tbl = 'fm_s_agreement_detail', $c_function = false);
		$GLOBALS['phpgw']->locations->add('.r_agreement.detail', 'Rental agreement detail', 'property', $allow_grant = false, $custom_tbl = 'fm_r_agreement_detail', $c_function = false);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_agreement_attribute");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'location_id'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.agreement':'.agreement.detail',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_r_agreement_attribute");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'location_id'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.r_agreement':'.r_agreement.detail',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_s_agreement_attribute");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'location_id'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.s_agreement':'.s_agreement.detail',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_owner_attribute");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'location_id'	=> '.owner',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_tenant_attribute");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'location_id'	=> '.tenant',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_vendor_attribute");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'location_id'	=> '.vendor',
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'custom'		=> 1
 			);
		}

		foreach ($attrib as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_attribute (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_agreement_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'location_id'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.agreement':'.agreement.detail',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_r_agreement_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'location_id'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.r_agreement':'.r_agreement.detail',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_s_agreement_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'location_id'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_detail') == 1 ? '.s_agreement':'.s_agreement.detail',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_owner_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'location_id'	=> '.owner',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_tenant_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'location_id'	=> '.tenant',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_vendor_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'location_id'	=> '.vendor',
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		foreach ($choice as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_choice (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_agreement_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_s_agreement_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_owner_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_tenant_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_vendor_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_agreement_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_s_agreement_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_owner_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_tenant_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_vendor_choice');

//---------------entity
		$attrib = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_attribute");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'location_id'	=> '.entity.' . $GLOBALS['phpgw_setup']->oProc->f('entity_id') . '.' . $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'helpmsg'		=> $GLOBALS['phpgw_setup']->oProc->f('helpmsg'),
					'custom'		=> 1
 			);
		}

		foreach ($attrib as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_attribute (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$choice = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'location_id'	=> '.entity.' . $GLOBALS['phpgw_setup']->oProc->f('entity_id') . '.' . $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		foreach ($choice as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_choice (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$location = array();

		$app_id = $GLOBALS['phpgw']->applications->name2id('property');
		$GLOBALS['phpgw_setup']->oProc->query("SELECT location_id,name FROM phpgw_locations WHERE app_id = {$app_id} AND name LIKE '.entity.%'");

		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$location[]= array
			(
				'location_id'	=> $GLOBALS['phpgw_setup']->oProc->f('location_id'),
				'name'			=> $GLOBALS['phpgw_setup']->oProc->f('name')
			);
		}

		foreach ($location as $entry)
		{
			if (strlen($entry['name'])>10)
			{
				$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_locations SET allow_c_attrib=1 ,c_attrib_table ='fm" . str_replace('.','_', $entry['name']) ."' WHERE location_id = {$entry['location_id']}");
			}
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_entity_attribute');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_entity_choice');

//---------------
//--------------- custom functions
		$custom = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_custom_function"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$custom[]=array(
					'location_id'	=> $GLOBALS['phpgw_setup']->oProc->f('acl_location'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'descr'			=> $GLOBALS['phpgw_setup']->oProc->f('descr'),
					'file_name'		=> $GLOBALS['phpgw_setup']->oProc->f('file_name'),
					'active'		=> $GLOBALS['phpgw_setup']->oProc->f('active'),
					'custom_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('custom_sort')
			);
		}

		foreach ($custom as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_function (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_custom_function');
//----------------

//--------------- locations

		$attrib = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_location_attrib");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$attrib[]=array(
					'location_id'		=> '.location.' . $GLOBALS['phpgw_setup']->oProc->f('type_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'column_name'	=> $GLOBALS['phpgw_setup']->oProc->f('column_name'),
					'input_text'	=> $GLOBALS['phpgw_setup']->oProc->f('input_text'),
					'statustext'	=> $GLOBALS['phpgw_setup']->oProc->f('statustext'),
					'datatype'		=> $GLOBALS['phpgw_setup']->oProc->f('datatype'),
					'search'		=> $GLOBALS['phpgw_setup']->oProc->f('search'),
					'history'		=> $GLOBALS['phpgw_setup']->oProc->f('history'),
					'list'			=> $GLOBALS['phpgw_setup']->oProc->f('list'),
					'attrib_sort'	=> $GLOBALS['phpgw_setup']->oProc->f('attrib_sort'),
					'size'			=> $GLOBALS['phpgw_setup']->oProc->f('size'),
					'precision_'	=> $GLOBALS['phpgw_setup']->oProc->f('precision_'),
					'scale'			=> $GLOBALS['phpgw_setup']->oProc->f('scale'),
					'default_value'	=> $GLOBALS['phpgw_setup']->oProc->f('default_value'),
					'nullable'		=> $GLOBALS['phpgw_setup']->oProc->f('nullable'),
					'helpmsg'		=> $GLOBALS['phpgw_setup']->oProc->f('helpmsg'),
					'lookup_form'	=> $GLOBALS['phpgw_setup']->oProc->f('lookup_form'),
					'custom'		=> $GLOBALS['phpgw_setup']->oProc->f('custom'),
 			);
		}

		foreach ($attrib as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_attribute (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$choice = array();
		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_location_choice"); 
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$choice[]=array(
					'location_id'	=> '.location.' . $GLOBALS['phpgw_setup']->oProc->f('type_id'),
					'attrib_id'		=> $GLOBALS['phpgw_setup']->oProc->f('attrib_id'),
					'id'			=> $GLOBALS['phpgw_setup']->oProc->f('id'),
					'value'			=> $GLOBALS['phpgw_setup']->oProc->f('value')
			);
		}

		foreach ($choice as & $entry)
		{
			$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $entry['location_id']);
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_cust_choice (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_location_attrib');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_location_choice');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.541';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.541 to 0.9.17.542
	* 'percent' is reserved for mssql
	*/

	$test[] = '0.9.17.541';
	function property_upgrade0_9_17_541()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_budget_period','percent','per_cent');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.542';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.542 to 0.9.17.543
	* Move files from 'home' to 'property'
 	*/

	$test[] = '0.9.17.542';
	function property_upgrade0_9_17_542()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$change = array
		(
			'/home/document'			=> '/property/document',
			'/home/fmticket'			=> '/property/fmticket',
			'/home/request'				=> '/property/request',
			'/home/workorder'			=> '/property/workorder',
			'/home/service_agreement'	=> '/property/service_agreement',
			'/home/rental_agreement'	=> '/property/rental_agreement',
			'/home/agreement'			=> '/property/agreement'
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_entity_category");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$entity = "entity_{$GLOBALS['phpgw_setup']->oProc->f('entity_id')}_{$GLOBALS['phpgw_setup']->oProc->f('id')}";
			$change["/home/{$entity}"] = "/property/{$entity}";
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT config_value FROM phpgw_config WHERE config_app = 'phpgwapi' AND config_name = 'files_dir'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$files_dir = $GLOBALS['phpgw_setup']->oProc->f('config_value');

		@mkdir($files_dir . '/property', 0770);

		foreach($change as $change_from => $change_to)
		{
			@rename($files_dir . $change_from, $files_dir . $change_to);
		}

		$change_from = array_keys($change); 
        $change_to = array_values($change); 

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM phpgw_vfs WHERE app = 'property'");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$files[]=array(
				'file_id'	=> $GLOBALS['phpgw_setup']->oProc->f('file_id'),
				'directory'	=> str_ireplace($change_from, $change_to, $GLOBALS['phpgw_setup']->oProc->f('directory')),
			);
		}

		foreach($files as $file)
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_vfs SET directory ='{$file['directory']}' WHERE file_id = {$file['file_id']}");
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.543';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.543 to 0.9.17.544
	* FIXME: Figure out the correct conversion of categories that comply with interlink
 	*/

	$test[] = '0.9.17.543';
	function property_upgrade0_9_17_543()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		// Need account_repository, accounts, acl and hooks to use categories
		$GLOBALS['phpgw_setup']->oProc->query("SELECT config_value FROM phpgw_config WHERE config_app = 'phpgwapi' AND config_name = 'account_repository'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$GLOBALS['phpgw_info']['server']['account_repository'] = $GLOBALS['phpgw_setup']->oProc->f('config_value');

		$GLOBALS['phpgw']->accounts		= createObject('phpgwapi.accounts');

		$GLOBALS['phpgw']->db = & $GLOBALS['phpgw_setup']->oProc->m_odb;
		$GLOBALS['phpgw']->acl = CreateObject('phpgwapi.acl');
		$GLOBALS['phpgw']->hooks = CreateObject('phpgwapi.hooks', $GLOBALS['phpgw_setup']->oProc->m_odb);
		$cats = CreateObject('phpgwapi.categories', -1, 'property.ticket');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_tts_category");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[$GLOBALS['phpgw_setup']->oProc->f('id')]=array(
				'name'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'descr'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'parent' => 'none',
				'old_parent' => 0,
				'access' => 'public'
			);
		}

		foreach ($categories as $old => $values)
		{
			$cat_id = $cats->add($values);
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET cat_id = $cat_id WHERE cat_id = $old");
		}

		$cats->set_appname('property.project');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_workorder_category");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[$GLOBALS['phpgw_setup']->oProc->f('id')]=array(
				'name'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'descr'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'parent' => 'none',
				'old_parent' => 0,
				'access' => 'public'
			);
		}

		foreach ($categories as $old => $values)
		{
			$cat_id = $cats->add($values);
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_project SET category = $cat_id WHERE category = $old");
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_request SET category = $cat_id WHERE category = $old");
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_tts_category');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_workorder_category');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_request_category');

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tts_tickets','status',array('type' => 'varchar','precision' => '2','nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_responsibility', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
					'descr' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
					'active' => array('type' => 'int','precision' => 2,'nullable' => True),
					'cat_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'created_on' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'created_by' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(
					'phpgw_categories' => array('cat_id' => 'cat_id')
				),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_responsibility_contact', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'responsibility_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'contact_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'location_code' => array('type' => 'varchar', 'precision' => 20,'nullable' => True),
					'p_num' => array('type' => 'varchar', 'precision' => 15,'nullable' => True),
					'p_entity_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
					'p_cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => True,'default' => '0'),
					'priority' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'active_from' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'active_to' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'created_on' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'created_by' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'expired_on' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'expired_by' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'remark' => array('type' => 'text','nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(
					'fm_responsibility' => array('responsibility_id' => 'id'),
					'phpgw_contact' => array('contact_id' => 'contact_id')
				),
				'ix' => array('location_code'),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_tts_status', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'color' => array('type' => 'varchar','precision' => '10','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		unset($GLOBALS['phpgw']->accounts);
		unset($GLOBALS['phpgw']->acl);
		$GLOBALS['phpgw']->hooks->register_all_hooks(); //get the menus
		unset($GLOBALS['phpgw']->hooks);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.544';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.544 to 0.9.17.545
	* Move interlink data from property to API
 	*/

	$test[] = '0.9.17.544';
	function property_upgrade0_9_17_544()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM fm_cache');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_wo_hours','hours_descr',array('type' => 'text', 'nullable' => True));

		$GLOBALS['phpgw']->locations->add('.project.workorder', 'Workorder', 'property', $allow_grant = true, $custom_tbl = null, $c_function = true);
		$GLOBALS['phpgw']->locations->add('.project.request', 'Request', 'property', $allow_grant = true, $custom_tbl = null, $c_function = true);
		$GLOBALS['phpgw_setup']->oProc->query('SELECT * FROM fm_origin');
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$interlink[] = array
			(
				'origin'			=> $GLOBALS['phpgw_setup']->oProc->f('origin'),
				'origin_id'			=> $GLOBALS['phpgw_setup']->oProc->f('origin_id'),
				'destination'		=> $GLOBALS['phpgw_setup']->oProc->f('destination'),
				'destination_id'	=> $GLOBALS['phpgw_setup']->oProc->f('destination_id'),
				'user_id'			=> $GLOBALS['phpgw_setup']->oProc->f('user_id'),
				'entry_date'		=> $GLOBALS['phpgw_setup']->oProc->f('entry_date')
			);
		}

		foreach ($interlink as $entry)
		{
			if($entry['origin'] == 'workorder')
			{
				$entry['origin'] = 'project.workorder';
			}
			if($entry['origin'] == 'request')
			{
				$entry['origin'] = 'project.request';
			}
			if($entry['destination'] == 'request')
			{
				$entry['destination'] = 'project.request';
			}
			if($entry['destination'] == 'tenant_claim')
			{
				$entry['destination'] = 'tenant&claim';
			}

			$location1_id = $GLOBALS['phpgw']->locations->get_id('property', '.' . str_replace('_', '.', $entry['origin']=='tts' ? 'ticket' : $entry['origin']));
			$location2_id = $GLOBALS['phpgw']->locations->get_id('property', '.' . str_replace(array('_','&'), array('.','_'), $entry['destination']=='tts' ? 'ticket' : $entry['destination']));
			$account_id = $entry['user_id'] ? $entry['user_id'] : -1;
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_interlink (location1_id,location1_item_id,location2_id,location2_item_id,account_id,entry_date,is_private,start_date,end_date) '
				.'VALUES('
				.$location1_id . ','
				.$entry['origin_id'] . ','
				.$location2_id . ','
				.$entry['destination_id'] . ','
				.$account_id . ','
				.$entry['entry_date'] . ',-1,-1,-1)');
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_origin');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.545';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.545 to 0.9.17.546
	* Add table for a common unified location-mapping for use with interlink
 	*/

	$test[] = '0.9.17.545';
	function property_upgrade0_9_17_545()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		//old table that may exist
		if ($GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_location'))
		{
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_location');
		}

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_locations', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'level' => array('type' => 'int','precision' => '4','nullable' => False),
					'location_code' => array('type' => 'varchar','precision' => '50','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('location_code')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query('SELECT max(id) as levels FROM fm_location_type');
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$levels =  $GLOBALS['phpgw_setup']->oProc->f('levels');

		//perform an update on all location_codes on all levels to make sure they are consistent and unique
		$locations = array();
		for ($level=1;$level<($levels+1);$level++)
		{
			$sql = "SELECT * from fm_location{$level}";
			$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
			$i = 0;
			while($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$location_code = array();
				$where = 'WHERE';
				$locations[$level][$i]['condition'] = '';
				for ($j=1;$j<($level+1);$j++)
				{
					$loc = $GLOBALS['phpgw_setup']->oProc->f("loc{$j}");
					$location_code[] = $loc;
					$locations[$level][$i]['condition'] .= "$where loc{$j}='{$loc}'";
					$where = 'AND';
				}
				$locations[$level][$i]['new_values']['location_code'] = implode('-', $location_code);
				$i++;
			}

		}

		foreach($locations as $level => $location_at_leve)
		{
			foreach($location_at_leve as $location )
			{
				$sql = "UPDATE fm_location{$level} SET location_code = '{$location['new_values']['location_code']}' {$location['condition']}";
				$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
			}
		}

		$locations = array();
		for ($i=1;$i<($levels+1);$i++)
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT * from fm_location{$i}");
			while($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$locations[] = array
				(
					'level' 		=> $i,
					'location_code' => $GLOBALS['phpgw_setup']->oProc->f('location_code')
				);
			}
		}

		foreach ($locations as $location)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_locations (level, location_code) VALUES ({$location['level']}, '{$location['location_code']}')");
		}

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl set acl_grantor = -1 WHERE acl_grantor IS NULL",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_cache_user",__LINE__,__FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.546';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.546 to 0.9.17.547
	* Udate missing information on table for custom fields for owner, tenant and vendor
 	*/

	$test[] = '0.9.17.546';
	function property_upgrade0_9_17_546()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.owner');
		$sql = "UPDATE phpgw_locations SET allow_c_attrib = 1, c_attrib_table = 'fm_owner' WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.tenant');
		$sql = "UPDATE phpgw_locations SET allow_c_attrib = 1, c_attrib_table = 'fm_tenant' WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.vendor');
		$sql = "UPDATE phpgw_locations SET allow_c_attrib = 1, c_attrib_table = 'fm_vendor' WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.547';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.547 to 0.9.17.548
	* Drop some old tables and add custom attribute groups if this was missed during api-upgrade
 	*/

	$test[] = '0.9.17.547';
	function property_upgrade0_9_17_547()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$metadata = $GLOBALS['phpgw_setup']->db->metadata('fm_equipment');
		if($metadata)
		{
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment_attrib');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment_status');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment_type');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment_type_attrib');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_equipment_type_choice');
		}

		$metadata = $GLOBALS['phpgw_setup']->db->metadata('fm_meter');
		if($metadata)
		{
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_meter');
			$GLOBALS['phpgw_setup']->oProc->DropTable('fm_meter_category');
		}


		$GLOBALS['phpgw_setup']->oProc->m_odb->query("SELECT count(*) as found_some FROM phpgw_cust_attribute_group");
		$GLOBALS['phpgw_setup']->oProc->m_odb->next_record();
		if( !$GLOBALS['phpgw_setup']->oProc->f('found_some') )
		{
			$GLOBALS['phpgw_setup']->oProc->m_odb->query("SELECT DISTINCT location_id FROM phpgw_cust_attribute");
			$locations = array();
			while ($GLOBALS['phpgw_setup']->oProc->m_odb->next_record())
			{
				$locations[] = $GLOBALS['phpgw_setup']->oProc->f('location_id');
			}

			foreach ($locations as $location_id)
			{
				$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO phpgw_cust_attribute_group (location_id, id, name, group_sort, descr)"
				." VALUES ({$location_id}, 1, 'Default group', 1, 'Auto created from db-update')", __LINE__, __FILE__);
			}
		}


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.548';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.548 to 0.9.17.549
	* Add new table for project_group
 	*/

	$test[] = '0.9.17.548';
	function property_upgrade0_9_17_548()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_project_group', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project','project_group',array('type' => 'int','precision' => 4,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.549';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.549 to 0.9.17.550
	* FIXME: Figure out the correct conversion of categories that comply with interlink
 	*/

	$test[] = '0.9.17.549';
	function property_upgrade0_9_17_549()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		// Need account_repository, accounts, acl and hooks to use categories
		$GLOBALS['phpgw_setup']->oProc->query("SELECT config_value FROM phpgw_config WHERE config_app = 'phpgwapi' AND config_name = 'account_repository'");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$GLOBALS['phpgw_info']['server']['account_repository'] = $GLOBALS['phpgw_setup']->oProc->f('config_value');

		$GLOBALS['phpgw']->accounts		= createObject('phpgwapi.accounts');

		$GLOBALS['phpgw']->db = & $GLOBALS['phpgw_setup']->oProc->m_odb;
		$GLOBALS['phpgw']->acl = CreateObject('phpgwapi.acl');
		$GLOBALS['phpgw']->hooks = CreateObject('phpgwapi.hooks', $GLOBALS['phpgw_setup']->oProc->m_odb);
		$cats = CreateObject('phpgwapi.categories', -1, 'property.document');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_document_category");
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$categories[$GLOBALS['phpgw_setup']->oProc->f('id')]=array(
				'name'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'descr'	=> $GLOBALS['phpgw_setup']->oProc->f('descr', true),
				'parent' => 'none',
				'old_parent' => 0,
				'access' => 'public'
			);
		}

		foreach ($categories as $old => $values)
		{
			$cat_id = $cats->add($values);
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_document SET category = $cat_id WHERE category = $old");
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_document_category');

		unset($GLOBALS['phpgw']->accounts);
		unset($GLOBALS['phpgw']->acl);
		unset($GLOBALS['phpgw']->hooks);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.550';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.550 to 0.9.17.551
	*/

	$test[] = '0.9.17.550';
	function property_upgrade0_9_17_550()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request_history','history_old_value',array('type' => 'text','nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_history','history_old_value',array('type' => 'text','nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_history','history_old_value',array('type' => 'text','nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_history','history_old_value',array('type' => 'text','nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_document_history','history_old_value',array('type' => 'text','nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_history','history_old_value',array('type' => 'text','nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_s_agreement_history','history_old_value',array('type' => 'text','nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.551';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.551 to 0.9.17.552
	* Reorganise documents
	*/

	$test[] = '0.9.17.551';
	function property_upgrade0_9_17_551()
	{
		set_time_limit(1800);
		$next_version = '0.9.17.552';

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_document");
		$files = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$files[]=array
			(
				'document_name'	=> $GLOBALS['phpgw_setup']->oProc->f('document_name'),
				'location_code'	=> $GLOBALS['phpgw_setup']->oProc->f('location_code'),
				'loc1'			=> $GLOBALS['phpgw_setup']->oProc->f('loc1'),
				'category'		=> $GLOBALS['phpgw_setup']->oProc->f('category'),
				'p_num'			=> $GLOBALS['phpgw_setup']->oProc->f('p_num'),
				'p_entity_id'	=> $GLOBALS['phpgw_setup']->oProc->f('p_entity_id'),
				'p_cat_id'		=> $GLOBALS['phpgw_setup']->oProc->f('p_cat_id'),
			);
		}

		$sql = 'SELECT config_name,config_value FROM phpgw_config'
					. " WHERE config_name = 'files_dir'"
					. " OR config_name = 'file_repository'";

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		while ( $GLOBALS['phpgw_setup']->oProc->next_record() )
		{
			$GLOBALS['phpgw_info']['server'][$GLOBALS['phpgw_setup']->oProc->f('config_name', true)] = $GLOBALS['phpgw_setup']->oProc->f('config_value', true);
		}
		$GLOBALS['phpgw']->db = & $GLOBALS['phpgw_setup']->oProc->m_odb;
		$acl = CreateObject('phpgwapi.acl');

		$admins = $acl->get_ids_for_location('run', 1, 'admin');
		$GLOBALS['phpgw_info']['user']['account_id'] = $admins[0];

		//used in vfs
		define('PHPGW_ACL_READ',1);
		define('PHPGW_ACL_ADD',2);
		define('PHPGW_ACL_EDIT',4);
		define('PHPGW_ACL_DELETE',8);

		$GLOBALS['phpgw']->session		= createObject('phpgwapi.sessions');
		$vfs 			= CreateObject('phpgwapi.vfs');
		$vfs->fakebase 	= '/property';
		$vfs->override_acl = 1;


		if(!is_dir("{$vfs->basedir}{$vfs->fakebase}"))
		{
			$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_abort();
			$GLOBALS['setup_info']['property']['currentver'] = $next_version;
			return $GLOBALS['setup_info']['property']['currentver'];
		}


		$to_dir = array();
		foreach ($files as $entry)
		{
			 if($entry['p_num'])
			 {
				continue;
			 }
			 else
			 {
			 	$to_dir["{$vfs->basedir}{$vfs->fakebase}/document/{$entry['location_code']}"] = true;
			 	$to_dir["{$vfs->basedir}{$vfs->fakebase}/document/{$entry['location_code']}/{$entry['category']}"] = true;
			 }
		}

		foreach ($to_dir as $dir => $dummy)
		{
			if(!is_dir($dir))
			{
				mkdir($dir, 0770);
			}
		}

		reset($files);
		$error = array();
		foreach ($files as $entry)
		{
			 if($entry['p_num'])
			 {
				continue;
			 }
			 else
			 {
			 	$from_file = "{$vfs->fakebase}/document/{$entry['loc1']}/{$entry['document_name']}";
			 	$to_file = "{$vfs->fakebase}/document/{$entry['location_code']}/{$entry['category']}/{$entry['document_name']}";
			 }

			if(!$vfs->mv (array (
				'from'		=> $from_file,
				'to'		=> $to_file,
				'relatives'	=> array (RELATIVE_ALL, RELATIVE_ALL))))
			{
				$error[] = lang('Failed to move file') . " {$from_file}";
			}
		}

		$vfs->override_acl = 0;
		if($error)
		{
			_debug_array($error);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = $next_version;
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.552 to 0.9.17.553
	* 
	*/

	$test[] = '0.9.17.552';
	function property_upgrade0_9_17_552()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.invoice.dimb', 'A dimension for accounting', 'property');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','ecodimb',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget','ecodimb',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget_basis','ecodimb',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget','category',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_budget_basis','category',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_category','name',array('type' => 'varchar','precision' => '100','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_entity_category','descr',array('type' => 'text','nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_budget','district_id',array('type' => 'int','precision' => 4,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','p_num', array('type' => 'varchar','precision' => 15,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','p_entity_id', array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','p_cat_id', array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','location_code', array('type' => 'varchar','precision' => 20,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','address', array('type' => 'varchar','precision' => 150,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','tenant_id', array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','contact_phone', array('type' => 'varchar','precision' => 20,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project','planned_cost', array('type' => 'int','precision' => 4,'nullable' => True, 'default' => '0'));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_s_agreement_budget', array(
				'fd' => array(
					'agreement_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'year' => array('type' => 'int','precision' => 4,'nullable' => False),
					'budget_account' =>  array('type' => 'varchar','precision' => 15,'nullable' => False),
					'ecodimb' => array('type' => 'int','precision' => 4,'nullable' => True),
					'category' => array('type' => 'int','precision' => 4,'nullable' => True),
					'budget' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
					'actual_cost' => array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
					'user_id' => array('type' => 'int','precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int','precision' => 4,'nullable' => True),
					'modified_date' => array('type' => 'int','precision' => 4,'nullable' => True)
				),
				'pk' => array('agreement_id','year'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.553';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.553 to 0.9.17.554
	* 
	*/

	$test[] = '0.9.17.553';
	function property_upgrade0_9_17_553()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_workorder');

		if(!isset($metadata['paid_percent']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','paid_percent', array('type' => 'int','precision' => 4,'nullable' => True,'default' => 0));
		}

		if(!isset($metadata['category']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','category', array('type' => 'int','precision' => 4,'nullable' => True));
		}

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project','account_id', array('type' => 'varchar','precision' => '20','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project','ecodimb', array('type' => 'int','precision' => 4,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.554';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.554 to 0.9.17.555
	* 
	*/

	$test[] = '0.9.17.554';
	function property_upgrade0_9_17_554()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM fm_cache');
		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_cache_user');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_responsibility_contact','ecodimb', array('type' => 'int','precision' => 4,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.555';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.555 to 0.9.17.556
	* Scheduling capabilities by custom fields and asyncservice
	* 
	*/

	$test[] = '0.9.17.555';
	function property_upgrade0_9_17_555()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_event_action', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'name' =>  array('type' => 'varchar','precision' => 100,'nullable' => False),
					'action' =>  array('type' => 'varchar','precision' => 100,'nullable' => False),
					'data' => array('type' => 'text','nullable' => True),
					'descr' => array('type' => 'text','nullable' => True),
					'user_id' => array('type' => 'int','precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int','precision' => 4,'nullable' => True),
					'modified_date' => array('type' => 'int','precision' => 4,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_event', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
					'location_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'location_item_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'attrib_id' => array('type' => 'int','precision' => 4,'default' => '0','nullable' => true),
					'responsible_id' => array('type' => 'int','precision' => 4,'nullable' => true),
					'action_id' => array('type' => 'int','precision' => 4,'nullable' => true),
					'descr' => array('type' => 'text','nullable' => True),
					'start_date' => array('type' => 'int','precision' => 4,'nullable' => false),
					'end_date' => array('type' => 'int','precision' => 4,'nullable' => true),
					'repeat_type' => array('type' => 'int','precision' => 4,'nullable' => true),
					'repeat_day' => array('type' => 'int','precision' => 4,'nullable' => true),
					'repeat_interval' => array('type' => 'int','precision' => 4,'nullable' => true),
					'enabled' => array('type' => 'int','precision' => 2,'nullable' => true),
					'user_id' => array('type' => 'int','precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int','precision' => 4,'nullable' => True),
					'modified_date' => array('type' => 'int','precision' => 4,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('location_id', 'location_item_id', 'attrib_id')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_responsibility');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_responsibility', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
					'descr' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
					'active' => array('type' => 'int','precision' => 2,'nullable' => True),
					'cat_id' => array('type' => 'int','precision' => 4,'nullable' => True),
					'location_id' => array('type' => 'int','precision' => 4,'nullable' => True),
					'created_on' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'created_by' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.556';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.556 to 0.9.17.557
	* Scheduling capabilities by custom fields and asyncservice
	* 
	*/

	$test[] = '0.9.17.556';
	function property_upgrade0_9_17_556()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_event_exception', array(
				'fd' => array(
					'event_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'exception_time' => array('type' => 'int','precision' => 4,'nullable' => False),
					'descr' => array('type' => 'text','nullable' => True),
					'user_id' => array('type' => 'int','precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int','precision' => 4,'nullable' => True),
					'modified_date' => array('type' => 'int','precision' => 4,'nullable' => True)
				),
				'pk' => array('event_id', 'exception_time'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.557';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.557 to 0.9.17.558
	* Rename reserved fieldname (mysql)
	* 
	*/

	$test[] = '0.9.17.557';
	function property_upgrade0_9_17_557()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_event');

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		if(isset($metadata['interval']))
		{
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_event','interval','repeat_interval');
		}
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.558';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.558 to 0.9.17.559
	* change the priority for the helpdest (from 10-1 to 1-3)
	* 
	*/

	$test[] = '0.9.17.558';
	function property_upgrade0_9_17_558()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 11 WHERE priority IN (8,9,10)");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 12 WHERE priority IN (4,5,6,7)");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 13 WHERE priority IN (1,2,3)");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 1 WHERE priority = 11");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 2 WHERE priority = 12");
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_tts_tickets SET priority = 3 WHERE priority = 13");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.559';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.559 to 0.9.17.560
	* Add location to the budget.basis
	* 
	*/

	$test[] = '0.9.17.559';
	function property_upgrade0_9_17_559()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw']->locations->add('.budget.basis', 'Basis for high level lazy budgeting', 'property');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.560';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.560 to 0.9.17.561
	* Add ability to upload jasper reports
	* 
	*/

	$test[] = '0.9.17.560';
	function property_upgrade0_9_17_560()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','jasperupload',array('type' => 'int','precision' => 2,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.561';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.561 to 0.9.17.562
	* Add variants of closed-status for tickets
	* 
	*/

	$test[] = '0.9.17.561';
	function property_upgrade0_9_17_561()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status','closed',array('type' => 'int','precision' => 2,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.562';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.562 to 0.9.17.563
	* Separate project status from workorder status
	* 
	*/

	$test[] = '0.9.17.562';
	function property_upgrade0_9_17_562()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_project_status', array(
				'fd' => array(
					'id' => array('type' => 'varchar','precision' => '20','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '255','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM fm_workorder_status");
		$status = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$status[] = array
			(
				'id'	=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'descr'	=> $GLOBALS['phpgw_setup']->oProc->f('descr')
			);
		}

		foreach($status as $entry)
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO fm_project_status (' . implode(',',array_keys($entry)) . ') VALUES (' . $GLOBALS['phpgw_setup']->oProc->validate_insert(array_values($entry)) . ')');
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.563';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.563 to 0.9.17.564
	* Add area information as standard fields to each level in the location hierarchy
	* 
	*/

	$test[] = '0.9.17.563';
	function property_upgrade0_9_17_563()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$db =& $GLOBALS['phpgw_setup']->oProc->m_odb;

		$db->query('DELETE FROM fm_cache');

		$cust = array
		(
			'datatype'		=> 'N',
			'precision_'	=> 20,
			'scale'			=> 2,
			'default_value'	=> '0.00',
			'nullable'		=> 'True',
			'custom'		=> 1
		);

		$area_fields = array();

		$area_fields[] = array
		(
			'name' => 'area_gross',
			'descr'=> 'gross area',
			'statustext' => 'Sum of the areas included within the outside face of the exterior walls of a building.',
			'cust'	=> $cust
		);
		$area_fields[] = array
		(
			'name' => 'area_net',
			'descr'=> 'net area',
			'statustext' => 'The wall-to-wall floor area of a room.',
			'cust'	=> $cust
		);
		$area_fields[] = array
		(
			'name' => 'area_usable',
			'descr'=> 'usable area',
			'statustext'=> 'generally measured from "paint to paint" inside the permanent walls and to the middle of partitions separating rooms',
			'cust'	=> $cust
		);

		$db->query("SELECT count(*) as levels FROM fm_location_type");

		$db->next_record();
		$levels = $db->f('levels');

		for($i = 1; $i < $levels +1; $i++)
		{
			$metadata = $GLOBALS['phpgw_setup']->db->metadata("fm_location{$i}");
			foreach($area_fields as & $field )
			{
				if(!isset($metadata[$field['name']]))
				{
					$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_location{$i}", $field['name'], array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'));
					$GLOBALS['phpgw_setup']->oProc->AddColumn("fm_location{$i}_history", $field['name'], array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'));
				}

				$field['cust']['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$i}");
				$db->query("SELECT max(id) as id FROM phpgw_cust_attribute WHERE location_id = {$field['cust']['location_id']}");
				$db->next_record();
				$id = (int)$db->f('id');
				$db->query("SELECT max(attrib_sort) as attrib_sort FROM phpgw_cust_attribute WHERE id = {$id} AND location_id = {$field['cust']['location_id']}");
				$db->next_record();

				$field['cust']['id']			= $id + 1;
				$field['cust']['attrib_sort']	= $db->f('attrib_sort') +1;
				$field['cust']['column_name']	= $field['name'];
				$field['cust']['input_text']	= $field['descr'];
				$field['cust']['statustext']	= $field['statustext'];

				$sql = 'INSERT INTO phpgw_cust_attribute(' . implode(',',array_keys($field['cust'])) . ') '
					 . ' VALUES (' . $db->validate_insert($field['cust']) . ')';
				$db->query($sql, __LINE__, __FILE__);
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.564';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.564 to 0.9.17.565
	* alter datatype for spvend_code
	* 
	*/

	$test[] = '0.9.17.564';
	function property_upgrade0_9_17_564()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$db =& $GLOBALS['phpgw_setup']->oProc->m_odb;

		$metadata = $GLOBALS['phpgw_setup']->db->metadata('fm_ecobilag');

		if($metadata['spvend_code']->type == 'varchar')
		{
			echo 'oppdaterer..</br>';
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag','spvend_code_tmp',array('type' => 'int','precision' => 4,'nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf','spvend_code_tmp',array('type' => 'int','precision' => 4,'nullable' => True));
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecoavvik','spvend_code_tmp',array('type' => 'int','precision' => 4,'nullable' => True));

			$db->query('UPDATE fm_ecobilag SET spvend_code_tmp = CAST ( spvend_code AS integer )',__LINE__,__FILE__);
			$db->query('UPDATE fm_ecobilagoverf SET spvend_code_tmp = CAST ( spvend_code AS integer )',__LINE__,__FILE__);
			$db->query('UPDATE fm_ecoavvik SET spvend_code_tmp = CAST ( spvend_code AS integer )',__LINE__,__FILE__);

			$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilag',array(),'spvend_code');
			$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecobilagoverf',array(),'spvend_code');
			$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_ecoavvik',array(),'spvend_code');

			$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecobilag','spvend_code_tmp','spvend_code');
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecobilagoverf','spvend_code_tmp','spvend_code');
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_ecoavvik','spvend_code_tmp','spvend_code');
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.565';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.565 to 0.9.17.566
	* Add field to reference origin of invoices if imported from external system
	* 
	*/

	$test[] = '0.9.17.565';
	function property_upgrade0_9_17_565()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag', 'external_ref', array('type' => 'varchar','precision' => '30','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf',  'external_ref', array('type' => 'varchar','precision' => '30','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.566';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.566 to 0.9.17.567
	* Add a general approval scheme for items across the system
	* 
	*/

	$test[] = '0.9.17.566';
	function property_upgrade0_9_17_566()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_approval', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => 8,'nullable' => False),
					'location_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'account_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'requested' => array('type' => 'int','precision' => 4,'nullable' => True),//timestamp
					'approved' => array('type' => 'int','precision' => 4,'nullable' => True),//timestamp
					'reminder' => array('type' => 'int','precision' => 4,'nullable' => True,'default' => '1'),
					'created_on' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'created_by' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'modified_date' => array('type' => 'int','precision' => 4,'nullable' => True),
					'modified_by' => array('type' => 'int','precision' => 4,'nullable' => True),
				),
				'pk' => array('id', 'location_id', 'account_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.567';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.567 to 0.9.17.568
	* Extend the approval scheme to include general actions
	* 
	*/

	$test[] = '0.9.17.567';
	function property_upgrade0_9_17_567()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_approval');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_action_pending', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'item_id' => array('type' => 'int','precision' => 8,'nullable' => False),
					'location_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'responsible' => array('type' => 'int','precision' => 4,'nullable' => False),
					'responsible_type' => array('type' => 'varchar','precision' => 20,'nullable' => False),
					'action_category'	=> array('type' => 'int','precision' => 4,'nullable' => False),
					'action_requested' => array('type' => 'int','precision' => 4,'nullable' => True),//timestamp
					'action_deadline' => array('type' => 'int','precision' => 4,'nullable' => True),//timestamp
					'action_performed' => array('type' => 'int','precision' => 4,'nullable' => True),//timestamp
					'reminder' => array('type' => 'int','precision' => 4,'nullable' => True,'default' => '1'),
					'created_on' => array('type' => 'int', 'precision' => 4,'nullable' => False),//timestamp
					'created_by' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'expired_on' => array('type' => 'int','precision' => 4,'nullable' => True),//timestamp
					'expired_by' => array('type' => 'int','precision' => 4,'nullable' => True),
					'remark' => array('type' => 'text','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_action_pending_category', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => '4','nullable' => False),
					'num' => array('type' => 'varchar', 'precision' => 25,'nullable' => True),
					'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => True),
					'descr' => array('type' => 'text','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('num')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_action_pending_category (num, name, descr) VALUES ('approval', 'Approval', 'Please approve the item requested')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_action_pending_category (num, name, descr) VALUES ('remind', 'Remind', 'This is a reminder of task assigned')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_action_pending_category (num, name, descr) VALUES ('accept_delivery', 'Accept delivery', 'Please accept delivery on this item')");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.568';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.568 to 0.9.17.569
	* Add variants of closed and approved-status for projects and workorders
	* 
	*/

	$test[] = '0.9.17.568';
	function property_upgrade0_9_17_568()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_status','approved',array('type' => 'int','precision' => 2,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project_status','closed',array('type' => 'int','precision' => 2,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_status','approved',array('type' => 'int','precision' => 2,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_status','in_progress',array('type' => 'int','precision' => 2,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_status','delivered',array('type' => 'int','precision' => 2,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder_status','closed',array('type' => 'int','precision' => 2,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.569';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.569 to 0.9.17.570
	* Add custom fields to projects, workorders and tickets
	* 
	*/

	$test[] = '0.9.17.569';
	function property_upgrade0_9_17_569()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$location_id_project = $GLOBALS['phpgw']->locations->get_id('property', '.project');
		$location_id_workorder = $GLOBALS['phpgw']->locations->get_id('property', '.project.workorder');
		$location_id_ticket = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');

		$sql = "UPDATE phpgw_locations SET allow_c_function = 1, allow_c_attrib = 1, c_attrib_table = 'fm_project' WHERE location_id = {$location_id_project}";
		$GLOBALS['phpgw_setup']->oProc->query($sql);
		$sql = "UPDATE phpgw_locations SET allow_c_function = 1, allow_c_attrib = 1, c_attrib_table = 'fm_workorder' WHERE location_id = {$location_id_workorder}";
		$GLOBALS['phpgw_setup']->oProc->query($sql);
		$sql = "UPDATE phpgw_locations SET allow_c_function = 1, allow_c_attrib = 1, c_attrib_table = 'fm_tts_tickets' WHERE location_id = {$location_id_ticket}";
		$GLOBALS['phpgw_setup']->oProc->query($sql);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.570';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.570 to 0.9.17.571
	* Add custom fields to projects, workorders and tickets
	* 
	*/

	$test[] = '0.9.17.570';
	function property_upgrade0_9_17_570()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project','contact_id',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','contact_id',array('type' => 'int','precision' => 4,'nullable' => True));
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.571';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.571 to 0.9.17.572
	* Add event workorders
	* 
	*/

	$test[] = '0.9.17.571';
	function property_upgrade0_9_17_571()
	{
		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_workorder');
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		if(!isset($metadata['event_id']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','event_id',array('type' => 'int','precision' => 4,'nullable' => True));
		}
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_event','attrib_id_', array('type' => 'varchar','precision' => 50,'default' => '0','nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->query('UPDATE fm_event SET attrib_id_ = attrib_id');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_event',array(),'attrib_id');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_event','attrib_id_','attrib_id');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.572';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.572 to 0.9.17.573
	* Add ticket order - an ad hock order without using the project module
	* 
	*/

	$test[] = '0.9.17.572';
	function property_upgrade0_9_17_572()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw']->locations->add('.ticket.order', 'Helpdesk ad hock order', 'property');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','order_id',array('type' => 'int','precision' => 8,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','vendor_id',array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','order_descr',array('type' => 'text','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','b_account_id',array('type' => 'varchar','precision' => '20','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','ecodimb',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','budget',array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','actual_cost',array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.573';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.573 to 0.9.17.574
	* Alter field definition
	* 
	*/

	$test[] = '0.9.17.573';
	function property_upgrade0_9_17_573()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tts_history','history_status',array('type' => 'varchar','precision' => '3','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_request','title',array('type' => 'varchar','precision' => '100','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_document','title',array('type' => 'varchar','precision' => '100','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.574';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.574 to 0.9.17.575
	* Add variants of closed and approved-status for tickets
	* 
	*/

	$test[] = '0.9.17.574';
	function property_upgrade0_9_17_574()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_tts_tickets','status',array('type' => 'varchar','precision' => '3','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status','approved',array('type' => 'int','precision' => 2,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status','in_progress',array('type' => 'int','precision' => 2,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status','delivered',array('type' => 'int','precision' => 2,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.575';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.575 to 0.9.17.576
	* Add contact_email to tickets
	* 
	*/

	$test[] = '0.9.17.575';
	function property_upgrade0_9_17_575()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant','contact_email',array('type' => 'varchar','precision' => '64','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','contact_email',array('type' => 'varchar','precision' => '64','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.576';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.576 to 0.9.17.577
	* Add sorting to ticket status
	* 
	*/

	$test[] = '0.9.17.576';
	function property_upgrade0_9_17_576()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_status','sorting',array('type' => 'int','precision' => 4,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.577';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.577 to 0.9.17.578
	* Add order categories to ticket ad hoc orders
	* 
	*/

	$test[] = '0.9.17.577';
	function property_upgrade0_9_17_577()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','order_cat_id',array('type' => 'int','precision' => 4,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.578';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.578 to 0.9.17.579
	* Add custom dimension for orders
	* 
	*/

	$test[] = '0.9.17.578';
	function property_upgrade0_9_17_578()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','building_part',array('type' => 'varchar','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','order_dim1',array('type' => 'int','precision' => 4,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_order_dim1', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
					'num' => array('type' => 'varchar','precision' => 20,'nullable' => False),
					'descr' => array('type' => 'varchar','precision' => 255,'nullable' => False)
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.579';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.579 to 0.9.17.580
	* Add optional publishing flag on ticket notes
	* 
	*/

	$test[] = '0.9.17.579';
	function property_upgrade0_9_17_579()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_tickets','publish_note',array('type' => 'varchar','precision' => 2,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tts_history','publish',array('type' => 'int','precision' => 2,'nullable' => True));


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.580';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.580 to 0.9.17.581
	* Add optional hierarchy on entities
	* 
	*/

	$test[] = '0.9.17.580';
	function property_upgrade0_9_17_580()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','parent_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','level', array('type' => 'int','precision' => '4','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.581';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.581 to 0.9.17.582
	* Add templates to Ad Hoc Orders
	* 
	*/

	$test[] = '0.9.17.581';
	function property_upgrade0_9_17_581()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_order_template', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4,'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 200,'nullable' => False),
					'content' => array('type' => 'text','nullable' => True),
					'public' => array('type' => 'int', 'precision' => 2,'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'modified_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.582';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.582 to 0.9.17.583
	* Grant rights on actors
	* 
	*/

	$test[] = '0.9.17.582';
	function property_upgrade0_9_17_582()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.owner');
		$sql = "UPDATE phpgw_locations SET allow_grant = 1 WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.tenant');
		$sql = "UPDATE phpgw_locations SET allow_grant = 1 WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		$location_id	= $GLOBALS['phpgw']->locations->get_id('property', '.vendor');
		$sql = "UPDATE phpgw_locations SET allow_grant = 1 WHERE location_id = {$location_id}";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.583';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.583 to 0.9.17.584
	* Add schedule to event
	* 
	*/

	$test[] = '0.9.17.583';
	function property_upgrade0_9_17_583()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_event_schedule', array(
				'fd' => array(
					'event_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'schedule_time' => array('type' => 'int','precision' => 4,'nullable' => False),
					'descr' => array('type' => 'text','nullable' => True),
					'user_id' => array('type' => 'int','precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int','precision' => 4,'nullable' => True),
					'modified_date' => array('type' => 'int','precision' => 4,'nullable' => True)
				),
				'pk' => array('event_id', 'schedule_time'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_event_receipt');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_event_receipt', array(
				'fd' => array(
					'event_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'receipt_time' => array('type' => 'int','precision' => 4,'nullable' => False),
					'descr' => array('type' => 'text','nullable' => True),
					'user_id' => array('type' => 'int','precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int','precision' => 4,'nullable' => True),
					'modified_date' => array('type' => 'int','precision' => 4,'nullable' => True)
				),
				'pk' => array('event_id', 'receipt_time'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw']->locations->add('.scheduled_events', 'Scheduled events', 'property');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.584';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.583 to 0.9.17.584
	* Use locations for categories
	* 
	*/

	$test[] = '0.9.17.584';
	function property_upgrade0_9_17_584()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$locations = array
		(
			'property.ticket'	=> '.ticket',
			'property.project'	=> '.project',
			'property.document' => '.document',
			'fm_vendor'			=> '.vendor',
			'fm_tenant'			=> '.tenant',
			'fm_owner'			=> '.owner'
		);


		foreach($locations as $dummy => $location)
		{
			$GLOBALS['phpgw']->locations->add("{$location}.category", 'Categories', 'property');
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM phpgw_categories");
		$categories = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			if(in_array($GLOBALS['phpgw_setup']->oProc->f('cat_appname',true),array_keys($locations)))
			{
				$categories[] = array
				(
					'id'		=> $GLOBALS['phpgw_setup']->oProc->f('cat_id'),
					'appname'	=> $GLOBALS['phpgw_setup']->oProc->f('cat_appname',true),
					'name'		=> $GLOBALS['phpgw_setup']->oProc->f('cat_name',true)
				);
			}
		}

		foreach($categories as $category)
		{
			$location = $locations[$category['appname']];
			$location_id	= $GLOBALS['phpgw']->locations->get_id('property', $location);	
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_categories SET cat_appname = 'property', location_id = {$location_id} WHERE cat_id = {$category['id']}",__LINE__,__FILE__);

			$GLOBALS['phpgw']->locations->add("{$location}.category.{$category['id']}", $category['name'], 'property');
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT file_id, mime_type, name FROM  phpgw_vfs WHERE mime_type != 'Directory' AND mime_type != 'journal' AND mime_type != 'journal-deleted'",__LINE__,__FILE__);

		$mime = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$mime[] = array
			(
				'file_id'		=> $GLOBALS['phpgw_setup']->oProc->f('file_id'),
				'mime_type'		=> $GLOBALS['phpgw_setup']->oProc->f('mime_type'),
				'name'			=> $GLOBALS['phpgw_setup']->oProc->f('name'),
			);
		}

		$mime_magic = createObject('phpgwapi.mime_magic');

		foreach($mime as $entry)
		{
			if(!$entry['mime_type'])
			{
				$mime_type = $mime_magic->filename2mime($entry['name']);
				$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_vfs SET mime_type = '{$mime_type}' WHERE file_id = {$entry['file_id']}",__LINE__,__FILE__);
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.585';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.585 to 0.9.17.586
	* Use budget account groups on project level
	* 
	*/

	$test[] = '0.9.17.585';
	function property_upgrade0_9_17_585()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project','account_group', array('type' => 'int','precision' => '4','nullable' => true));
		$sql = "SELECT DISTINCT fm_project.account_id, fm_b_account.category as account_group FROM fm_project JOIN fm_b_account ON fm_project.account_id = fm_b_account.id";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		$accounts = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$accounts[] = array
			(
				'account_id'		=> $GLOBALS['phpgw_setup']->oProc->f('account_id'),
				'account_group'		=> $GLOBALS['phpgw_setup']->oProc->f('account_group'),
			);
		}
		foreach ($accounts as $entry)
		{
			$sql = "UPDATE fm_project SET account_group = {$entry['account_group']} WHERE account_id = '{$entry['account_id']}'";

			$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		}

//		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_project',array(),'account_id');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.586';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.586 to 0.9.17.587
	* restore field
	* 
	*/

	$test[] = '0.9.17.586';
	function property_upgrade0_9_17_586()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('fm_project');

		if(!isset($metadata['account_id']))
		{
			$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_project','account_id',array('type' => 'varchar','precision' => '20','nullable' => True));
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.587';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.587 to 0.9.17.588
	* add billable_hours to workorders
	* 
	*/

	$test[] = '0.9.17.587';
	function property_upgrade0_9_17_587()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_workorder','billable_hours',array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.588';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.588 to 0.9.17.589
	* Better precision to period (month) for payment-info
	* 
	*/

	$test[] = '0.9.17.588';
	function property_upgrade0_9_17_588()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecobilag','periode',array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('fm_ecobilagoverf','periode',array('type' => 'int','precision' => '4','nullable' => True));
//		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf','periode_old',array('type' => 'int','precision' => 4,'nullable' => True));
//		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag','periode_old',array('type' => 'int','precision' => 4,'nullable' => True));

		$db =& $GLOBALS['phpgw_setup']->oProc->m_odb;

		$tables = array('fm_ecobilag', 'fm_ecobilagoverf');
	
		foreach($tables as $table)
		{
			//Backup
//			$sql = "UPDATE {$table} SET periode_old = periode";
//			$db->query($sql,__LINE__,__FILE__);

			$sql = 'SELECT count (*), bilagsnr, EXTRACT(YEAR from fakturadato ) as aar ,' 
			. ' EXTRACT(MONTH from fakturadato ) as month, periode'
			. " FROM {$table} "
			. ' GROUP BY bilagsnr, EXTRACT(YEAR from fakturadato ), EXTRACT(MONTH from fakturadato ), periode'
			. ' ORDER BY aar, month, periode';

			$db->query($sql,__LINE__,__FILE__);

			$result = array();
			while ($db->next_record())
			{
				$aar = $db->f('aar');
				$month = $db->f('month');
				$periode = $db->f('periode');
				$periode_ny = $aar . sprintf("%02d",$periode);
				$periode_old = $aar . sprintf("%02d",$month);

				if($periode_old != $periode_ny && $month == 1)
				{
					$periode_korrigert = ($aar-1) . sprintf("%02d",$periode);
				}
				else
				{
					$periode_korrigert = $periode_ny;
				}

				$result[] = array
				(
    		   	    'bilagsnr'			=> $db->f('bilagsnr'),
    	//	   	    'aar'				=> $aar,
    	//	   		'month'				=> $month,
    	//	   	    'periode'			=> $periode,
    	//	   	    'periode_ny'		=> $periode_ny,
    		   	    'periode_korrigert'	=> $periode_korrigert
				);
			}

			foreach ($result as $entry)
			{
				$sql = "UPDATE {$table} SET periode = {$entry['periode_korrigert']} WHERE bilagsnr = {$entry['bilagsnr']}";
				$db->query($sql,__LINE__,__FILE__);
			}
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.589';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.589 to 0.9.17.590
	* add generic support for JasperReport
	* 
	*/

	$test[] = '0.9.17.589';
	function property_upgrade0_9_17_589()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.jasper', 'JasperReport', 'property', $allow_grant = true);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_jasper', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => 4, 'nullable' => false),
					'location_id' => array('type' => 'int','precision' => 4,'nullable' => false),
					'title' => array('type' => 'varchar','precision' => 100,'nullable' => true),
					'descr' => array('type' => 'varchar','precision' => 255,'nullable' => true),
					'formats' => array('type' => 'varchar','precision' => 255,'nullable' => true),
					'version' => array('type' => 'varchar','precision' => 10,'nullable' => true),
					'access' => array('type' => 'varchar','precision' => 7,'nullable' => true),
					'user_id' => array('type' => 'int','precision' => 4,'nullable' => true),
					'entry_date' => array('type' => 'int','precision' => 4,'nullable' => true),
					'modified_by' => array('type' => 'int','precision' => 4,'nullable' => true),
					'modified_date' => array('type' => 'int','precision' => 4,'nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_jasper_input_type', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => 4, 'nullable' => false),
					'name' => array('type' => 'varchar','precision' => 20,'nullable' => false), // i.e: date/ integer
					'descr' => array('type' => 'varchar','precision' => 255,'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('integer', 'Integer')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('float', 'Float')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('text', 'Text')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('date', 'Date')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_jasper_format_type', array(
				'fd' => array(
					'id' => array('type' => 'varchar','precision' => 20,'nullable' => false), // i.e: pdf/xls
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('PDF')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('CSV')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('XLS')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('XHTML')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_format_type (id) VALUES ('DOCX')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_jasper_input', array(
				'fd' => array(
					'id' => array('type' => 'auto','precision' => 4, 'nullable' => false),
					'jasper_id' => array('type' => 'int','precision' => 4,'nullable' => false),
					'input_type_id' => array('type' => 'int','precision' => 4, 'nullable' => false),
					'is_id' => array('type' => 'int','precision' => 2, 'nullable' => true),
					'name' => array('type' => 'varchar','precision' => 50,'nullable' => false),
					'descr' => array('type' => 'varchar','precision' => 255,'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(
					'fm_jasper_input_type' => array('input_type_id' => 'id'),
					'fm_jasper' => array('jasper_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.590';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.590 to 0.9.17.591
	* Add datatypes for user input at JasperReport
	* 
	*/

	$test[] = '0.9.17.590';
	function property_upgrade0_9_17_590()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('timestamp', 'timestamp')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('AB', 'Address book')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('VENDOR', 'Vendor')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_jasper_input_type (name, descr) VALUES ('user', 'system user')");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.591';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.591 to 0.9.17.592
	* Add integration settings on entities
	* 
	*/

	$test[] = '0.9.17.591';
	function property_upgrade0_9_17_591()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','integration_tab', array('type' => 'varchar','precision' => 50,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','integration_url', array('type' => 'varchar','precision' => 255,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','integration_paramtres', array('type' => 'text','nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.592';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.592 to 0.9.17.593
	* More on integration settings on entities
	* 
	*/

	$test[] = '0.9.17.592';
	function property_upgrade0_9_17_592()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_entity_category','integration_paramtres','integration_parametres');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','integration_action', array('type' => 'varchar','precision' => 50,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','integration_action_view', array('type' => 'varchar','precision' => 50,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_entity_category','integration_action_edit', array('type' => 'varchar','precision' => 50,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.593';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.593 to 0.9.17.594
	* Convert integration settings to generic config on locations
	* 
	*/

	$test[] = '0.9.17.593';
	function property_upgrade0_9_17_593()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category',array(),'integration_tab');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category',array(),'integration_url');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category',array(),'integration_parametres');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category',array(),'integration_action');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category',array(),'integration_action_view');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_entity_category',array(),'integration_action_edit');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.594';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	/**
	* Update property version from 0.9.17.594 to 0.9.17.595
	* Add custom dimension for orders
	* 
	*/

	$test[] = '0.9.17.594';
	function property_upgrade0_9_17_594()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_request','building_part',array('type' => 'varchar','precision' => 4,'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.595';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.595 to 0.9.17.596
	* Alter datatype
	* 
	*/

	$test[] = '0.9.17.595';
	function property_upgrade0_9_17_595()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT id, b_account_id FROM fm_tenant_claim");
		$claims = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$claims[] = array
			(
				'id'			=> (int)$GLOBALS['phpgw_setup']->oProc->f('id'),
				'b_account_id'	=> $GLOBALS['phpgw_setup']->oProc->f('b_account_id')
			);
		}

		$GLOBALS['phpgw_setup']->oProc->DropColumn('fm_tenant_claim',array(),'b_account_id');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_tenant_claim','b_account_id',array('type' => 'varchar','precision' => 20,'nullable' => True));

		foreach($claims as $claim)
		{
			$sql = "UPDATE fm_tenant_claim SET b_account_id = {$claim['b_account_id']} WHERE id = {$claim['id']}";

			$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		}


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.596';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}


	/**
	* Update property version from 0.9.17.596 to 0.9.17.597
	* Add responsibility roles
	* 
	*/

	$test[] = '0.9.17.596';
	function property_upgrade0_9_17_596()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_responsibility_role', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4,'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 200,'nullable' => False),
					'remark' => array('type' => 'text','nullable' => True),
					'location' => array('type' => 'varchar', 'precision' => 200,'nullable' => False),
					'responsibility' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'modified_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array('fm_responsibility' => array('responsibility' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.597';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.597 to 0.9.17.598
	* Rename column
	* 
	*/

	$test[] = '0.9.17.597';
	function property_upgrade0_9_17_597()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('fm_responsibility_role','responsibility','responsibility_id');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.598';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.598 to 0.9.17.599
	* Add columns to fm_b_account
	* 
	*/

	$test[] = '0.9.17.598';
	function property_upgrade0_9_17_598()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account','active', array('type' => 'int','precision' => '2','nullable' => True,'default' => '0'));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account','user_id', array('type' => 'int', 'precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account','entry_date', array('type' => 'int', 'precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_b_account','modified_date', array('type' => 'int', 'precision' => 4,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query('UPDATE fm_b_account SET active = 1',__LINE__,__FILE__);

		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_category');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_item');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_item_history');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_common');
		$GLOBALS['phpgw_setup']->oProc->DropTable('fm_r_agreement_c_history');

		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM fm_cache',__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilag','currency', array('type' => 'varchar','precision' => '3','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_ecobilagoverf','currency', array('type' => 'varchar','precision' => '3','nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_ecobilag SET currency = 'NOK'",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE fm_ecobilagoverf SET currency = 'NOK'",__LINE__,__FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.599';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}

	/**
	* Update property version from 0.9.17.599 to 0.9.17.600
	* Add responsibility roles
	* 
	*/

	$test[] = '0.9.17.599';
	function property_upgrade0_9_17_599()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_custom_menu_items', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4,'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 200,'nullable' => False),
					'url' => array('type' => 'text','nullable' => True),
					'location' => array('type' => 'varchar', 'precision' => 200,'nullable' => False),
					'local_files' => array('type' => 'int', 'precision' => 2,'nullable' => true),
					'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'modified_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.600';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	
	/**
	* Update property version from 0.9.17.600 to 0.9.17.601
	* Add BIM tables
	*
	*/

	$test[] = '0.9.17.600';
	function property_upgrade0_9_17_600()
	{
		$GLOBALS['phpgw']->locations->add('.admin.item', 'Items administration', 'property');

		$tables = array
		(
            'fm_attr_data_type' => array
            (
                'fd' => array(
                    'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
                    'display_name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
                    'function_name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false)
                ),
                'pk' => array('id'),
                'fk' => array(),
                'ix' => array(),
				'uc' => array('display_name', 'function_name')
            ),


            'fm_item_catalog' => array
			(
				'fd' => array
				(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
					'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
					'description' => array('type' => 'text', 'nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('name')
			),

			'fm_attr_group' => array
            (
                    'fd' => array(
                        'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
                        'name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
                        'sort' => array('type' => 'int', 'precision' => 4, 'nullable' => false, 'default' => 5)
                    ),
                    'pk' => array('id'),
                    'fk' => array(),
                    'ix' => array(),
                    'uc' => array('name')
            ),
            'fm_attr_def' => array
			(
				'fd' => array
				(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
					'name' => array('type' => 'varchar', 'precision' => 10, 'nullable' => false),
					'display_name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
					'description' => array('type' => 'text', 'nullable' => true),
					'data_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'unit_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
					'attr_group_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
				),
				'pk' => array('id'),
				'fk' => array('fm_attr_data_type' => array('data_type_id' => 'id') ,
								'fm_standard_unit' => array('unit_id' => 'id'),
								'fm_attr_group' => array('attr_group_id' => 'id')),
				'ix' => array(),
				'uc' => array('name')
			),

            'fm_attr_value' => array
			(
				'fd' => array
				(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
					'val_num' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
					'val_str' => array('type' => 'text', 'nullable' => true),
					'created_at' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
					'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
					'expired_at' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
					'expired_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			),


            'fm_item_group' => array
			(
				'fd' => array
				(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
					'name' => array('type' => 'varchar', 'precision' => 10, 'nullable' => false),
					'nat_group_no' => array('type' => 'varchar', 'precision' => 5, 'nullable' => false),
					'bpn' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
                    'parent_group' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
                    'catalog_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
				),
				'pk' => array('id'),
				'fk' => array('fm_item_group' => array('parent_group' => 'id'), 'fm_item_catalog' => array('catalog_id' => 'id')),
				'ix' => array(),
				'uc' => array('name')
			),


			'fm_item' => array
			(
				'fd' => array
				(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
					'group_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'vendor_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'installed' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array('fm_item_group' => array('group_id' => 'id'),
                              'fm_locations' => array('location_id' => 'id'),
                              'fm_vendor' => array('vendor_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			),


            'fm_item_attr' => array
			(
				'fd' => array
				(
					'item_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'attr_def_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
                    'value_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'active' => array('type' => 'int', 'precision' => 4, 'nullable' => false, 'default' => 1)
				),
				'pk' => array('item_id', 'attr_def_id'),
				'fk' => array('fm_item' => array('item_id' => 'id'),
                              'fm_attr_def' => array('attr_def_id' => 'id'),
                              'fm_attr_value' => array('value_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			),


			'fm_item_group_attr' => array
			(
				'fd' => array
				(
					'group_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'attr_def_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
                    'value_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'active' => array('type' => 'int', 'precision' => 4, 'nullable' => false, 'default' => 1)
				),
				'pk' => array('group_id', 'attr_def_id'),
				'fk' => array('fm_item_group' => array('group_id' => 'id'),
                              'fm_attr_def' => array('attr_def_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			),
                'fm_attr_choice' => array
                (
                    'fd' => array(
                        'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
                        'value_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
                        'attr_def_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
                    ),
                    'pk' => array('id'),
                    'fk' => array('fm_attr_def' => array('attr_def_id' => 'id')),
                    'ix' => array(),
                    'uc' => array()
                )
		);

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		foreach ( $tables as $table => $def )
		{
			$GLOBALS['phpgw_setup']->oProc->CreateTable($table, $def);
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.601';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	$test[] = '0.9.17.601';
	function property_upgrade0_9_17_601()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_bim_type', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 64,'nullable' => False),
					'description' => array('type' => 'varchar', 'precision' => 512,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('name')
			)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_bim_model', array(
				'fd' => array(
					'id' => array('type' => 'auto','nullable' => True),
					'name' => array('type' => 'varchar', 'precision' => 128,'nullable' => False),
					'vfs_file_id' => array('type' => 'int', 'nullable' => False),
					'authorization_value' => array('type' => 'varchar', 'precision' => 200,'nullable' => False),
					'author' => array('type' => 'varchar', 'precision' => 256,'nullable' => True),
					'changedate' => array('type' => 'timestamp','nullable' => True),
					'description' => array('type' => 'varchar', 'precision' => 512,'nullable' => True),
					'organization' => array('type' => 'varchar', 'precision' => 256,'nullable' => True),
					'originatingsystem' => array('type' => 'varchar', 'precision' => 256,'nullable' => True),
					'preprocessor' => array('type' => 'varchar', 'precision' => 256,'nullable' => True),
					'valdate' => array('type' => 'timestamp','nullable' => True),
					'nativeschema' => array('type' => 'varchar', 'precision' => 256,'nullable' => True),
				),
				'pk' => array('id'),
				'fk' => array('phpgw_vfs' => array('vfs_file_id' => 'file_id')),
				'ix' => array(),
				'uc' => array('')
			)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'fm_bim_item', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => False),
					'type' => array('type' => 'int','nullable' => False),
					'guid' => array('type' => 'varchar', 'precision' => 24,'nullable' => False),
					'xml_representation' => array('type' => 'xml','nullable' => False),
					'model' => array('type' => 'int','nullable' => False),
				),
				'pk' => array('id'),
				'fk' => array('fm_bim_model' => array('model' => 'id'),
								'fm_bim_type' => array('type' => 'id')),
				'ix' => array(),
				'uc' => array('guid')
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['property']['currentver'] = '0.9.17.601';
			return $GLOBALS['setup_info']['property']['currentver'];
		}
	}
	
	

