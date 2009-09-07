<?php
/**
 * Holds the queries inserting default data (not test data):
 * 
 * $oProc->query("sql_statement");
 * 
 */


// clean up from previous install
$GLOBALS['phpgw_setup']->oProc->query("SELECT app_id FROM phpgw_applications WHERE app_name = 'rental'");
$GLOBALS['phpgw_setup']->oProc->next_record();
$app_id = $GLOBALS['phpgw_setup']->oProc->f('app_id');

$GLOBALS['phpgw_setup']->oProc->query("SELECT location_id FROM phpgw_locations WHERE app_id = {$app_id} AND name != 'run'");

$locations = array();
while ($GLOBALS['phpgw_setup']->oProc->next_record())
{
	$locations[] = $GLOBALS['phpgw_setup']->oProc->f('location_id');
}

if(count($locations))
{
	$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_cust_choice WHERE location_id IN ('. implode (',',$locations) . ')');
	$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_cust_attribute WHERE location_id IN ('. implode (',',$locations). ')');
	$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_acl  WHERE location_id IN ('. implode (',',$locations) . ')');
}

$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_locations WHERE app_id = {$app_id} AND name != 'run'");


unset($locations);


//Create groups, users, add users to groups and set preferences


$GLOBALS['phpgw']->locations->add('.',				'Root',			'rental',false);

$GLOBALS['phpgw']->locations->add('.ORG',			'Locations for organisational units',				'rental',false);

$GLOBALS['phpgw']->locations->add('.ORG.BK',		'Organisational units in Bergen Kommune',			'rental',false);

$GLOBALS['phpgw']->locations->add('.ORG.BK.01',		'Byrådsleders avdeling',							'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK.02',		'Byrådsavd. for finans, konkurranse og omstilling',	'rental',false);
$loc_id_ba_helse =
$GLOBALS['phpgw']->locations->add('.ORG.BK.03',		'Byrådsavd. for helse og omsorg',					'rental',false);
$loc_id_ba_barnehage =
$GLOBALS['phpgw']->locations->add('.ORG.BK.04',		'Byrådsavd. for barnehage og skole',				'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK.05',		'Byrådsavd. for klima, miljø og byutvikling',		'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK.06',		'Byrådsavd. for byggesak og bydeler',				'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK.07',		'Byrådsavd. for kultur, næring og idrett',			'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK.08',		'Bystyrets organer',								'rental',false);

$GLOBALS['phpgw']->locations->add('.ORG.BK.01.30',		'Seksjon informasjon',							'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK.01.33',		'Byrådsleders avdeling, stab',					'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK.01.34',		'Kommuneadvokaten',								'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK.01.36',		'Etat for samfunnssikkerhet og beredskap',		'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK.01.37',		'Erstatningsutvalgets sekretariat',				'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK.01.38',		'Torget',										'rental',false);

$GLOBALS['phpgw']->locations->add('.RESPONSIBILIY',			'Fields of responsibilities',				'rental',false);

$loc_id_internal	= $GLOBALS['phpgw']->locations->add('.RESPONSIBILIY.INTERNAL',	'Field of responsibility: internleie',				'rental',false);
$loc_id_in		 	= $GLOBALS['phpgw']->locations->add('.RESPONSIBILIY.IN',			'Field of responsibility: innleie',					'rental',false);
$loc_id_out			= $GLOBALS['phpgw']->locations->add('.RESPONSIBILIY.OUT',			'Field of responsibility: utleie',					'rental',false);


// Default groups and users
$GLOBALS['phpgw']->accounts	= createObject('phpgwapi.accounts');
$GLOBALS['phpgw']->acl		= CreateObject('phpgwapi.acl');
$GLOBALS['phpgw']->acl->enable_inheritance = true;


$modules = array
(
	'manual',
	'preferences',
	'rental',
	'property'
);

$acls = array
(
	array
	(
		'appname'	=> 'preferences',
		'location'	=> 'changepassword',
		'rights'	=> 1
	),
	array
	(
		'appname'	=> 'rental',
		'location'	=> '.',
		'rights'	=> 1
	),
	array
	(
		'appname'	=> 'rental',
		'location'	=> 'run',
		'rights'	=> 1
	)
);

$aclobj =& $GLOBALS['phpgw']->acl;

if (!$GLOBALS['phpgw']->accounts->exists('rental_group') ) // no rental accounts already exists
{
	$account			= new phpgwapi_group();
	$account->lid		= 'rental_group';
	$account->firstname = 'Rental';
	$account->lastname	= 'Group';
	$rental_group		= $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);
}
else
{
	$rental_group		= $GLOBALS['phpgw']->accounts->name2id('rental_group');
}

$aclobj->set_account_id($rental_group, true);
$aclobj->add('rental', '.', 1);
$aclobj->add('rental', 'run', 1);
$aclobj->add('preferences', 'changepassword',1);
$aclobj->save_repository();

// Create new users: create ($account, $goups, $acls, $arrays)
// - Administrator
if (!$GLOBALS['phpgw']->accounts->exists('rental_admin') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'rental_admin';
	$account->firstname	= 'Rental';
	$account->lastname	= 'Administrator';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$rental_admin 		= $GLOBALS['phpgw']->accounts->create($account, array($rental_group), array(), array('admin'));
} 
else
{
	$rental_admin		= $GLOBALS['phpgw']->accounts->name2id('rental_admin');
}

$aclobj->set_account_id($rental_admin, true);
$aclobj->add('rental', '.', 31);
$aclobj->save_repository();
	


//- Field of responsibility: Internal
if (!$GLOBALS['phpgw']->accounts->exists('rental_internal') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'rental_internal';
	$account->firstname	= 'Rental';
	$account->lastname	= 'Internal';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$rental_internal 	= $GLOBALS['phpgw']->accounts->create($account, array($rental_group));
}
else
{
	$rental_internal	= $GLOBALS['phpgw']->accounts->name2id('rental_internal');
}


$aclobj->set_account_id($rental_internal,true);
$aclobj->add('rental', '.RESPONSIBILITY.INTERNAL', 15);
$aclobj->save_repository();

//- Field of responsibility: In
if (!$GLOBALS['phpgw']->accounts->exists('rental_in') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'rental_in';
	$account->firstname	= 'Rental';
	$account->lastname	= 'In';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$rental_in 			= $GLOBALS['phpgw']->accounts->create($account, array($rental_group));	
}
else
{
	$rental_in			= $GLOBALS['phpgw']->accounts->name2id('rental_in');
}

$aclobj->set_account_id($rental_in, true);
$aclobj->add('rental', '.RESPONSIBILITY.IN', 15);
$aclobj->save_repository();

//- Field of responsibility: Out
if (!$GLOBALS['phpgw']->accounts->exists('rental_out') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'rental_out';
	$account->firstname	= 'Rental';
	$account->lastname	= 'Out';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$rental_out 		= $GLOBALS['phpgw']->accounts->create($account, array($rental_group));
	
	
}
else
{
	$rental_out			= $GLOBALS['phpgw']->accounts->name2id('rental_out');
}

$aclobj->set_account_id($rental_out, true);
$aclobj->add('rental', '.RESPONSIBILITY.OUT', 15);
$aclobj->save_repository();

//- Manager
if (!$GLOBALS['phpgw']->accounts->exists('rental_manager') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'rental_manager';
	$account->firstname	= 'Rental';
	$account->lastname	= 'Manager';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$rental_manager 	= $GLOBALS['phpgw']->accounts->create($account, array($rental_group));
}
else
{
	$rental_manager		= $GLOBALS['phpgw']->accounts->name2id('rental_manager');
}

//Default rental composites
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Herdla fuglereservat','Pip pip')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Fløibanen','Tut tut')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Perle og Bruse','')");
$oProc->query("INSERT INTO rental_composite (name,description,is_active) VALUES ('Store Lungegårdsvannet','',false)");
$oProc->query("INSERT INTO rental_composite (name,description,address_1,address_2,house_number,postcode,place,has_custom_address) VALUES ('Beddingen','Der Bouvet e','Solheimsgaten','Inngang B','15','5058','BERGEN',true)");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Bystasjonen','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Åsane senter','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Byporten','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Ukjent sted','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Lots of levels','A rental composite that consists of areas from all levels.')");
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Vitalitetssenteret','')");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Gullstøltunet sykehjem','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Gullstøltunet sykehjem - Bosshus/Trafo','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Gullstøltunet sykehjem - Pumpehus','')");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Bergen Rådhus Nye','')");

$oProc->query("INSERT INTO rental_unit VALUES (1,'2711')");
$oProc->query("INSERT INTO rental_unit VALUES (1,'2712')");
$oProc->query("INSERT INTO rental_unit VALUES (1,'2717')");
$oProc->query("INSERT INTO rental_unit VALUES (1,'2721')");
$oProc->query("INSERT INTO rental_unit VALUES (2,'2714')");
$oProc->query("INSERT INTO rental_unit VALUES (2,'2716')");
$oProc->query("INSERT INTO rental_unit VALUES (3,'2717')");
$oProc->query("INSERT INTO rental_unit VALUES (3,'2721')");
$oProc->query("INSERT INTO rental_unit VALUES (4,'2726')");
$oProc->query("INSERT INTO rental_unit VALUES (4,'2730')");
$oProc->query("INSERT INTO rental_unit VALUES (5,'7179')");
$oProc->query("INSERT INTO rental_unit VALUES (5,'7183')");
$oProc->query("INSERT INTO rental_unit VALUES (6,'2104-02')"); // Level 2
$oProc->query("INSERT INTO rental_unit VALUES (7,'1101-01-02')"); // Level 3
$oProc->query("INSERT INTO rental_unit VALUES (8,'3409-01-02-01')"); // Level 4
$oProc->query("INSERT INTO rental_unit VALUES (9,'3409-01-02-01-201')"); // Level 5
$oProc->query("INSERT INTO rental_unit VALUES (10,'2711')"); // Level 1
$oProc->query("INSERT INTO rental_unit VALUES (10,'2104-02')"); // Level 2
$oProc->query("INSERT INTO rental_unit VALUES (10,'1101-01-02')"); // Level 3
$oProc->query("INSERT INTO rental_unit VALUES (10,'3409-01-02-01')"); // Level 4
$oProc->query("INSERT INTO rental_unit VALUES (10,'3409-01-02-01-201')"); // Level 5
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_unit VALUES (11,'5807-01')");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_unit VALUES (12,'3409-01')");
$oProc->query("INSERT INTO rental_unit VALUES (13,'3409-02')");
$oProc->query("INSERT INTO rental_unit VALUES (14,'3409-03')");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_unit VALUES (15,'1102-01')");

$oProc->query("INSERT INTO rental_contract_responsibility (location_id, title, notify_before) VALUES ({$loc_id_in},'rental_contract_type_innleie',90)");
$oProc->query("INSERT INTO rental_contract_responsibility (location_id, title, notify_before) VALUES ({$loc_id_internal},'rental_contract_type_internleie',90)");
$oProc->query("INSERT INTO rental_contract_responsibility (location_id, title, notify_before) VALUES ({$loc_id_out},'rental_contract_type_eksternleie',90)");

$oProc->query("INSERT INTO rental_billing_term (title, runs_a_year) VALUES ('rental_common_annually','1')");
$oProc->query("INSERT INTO rental_billing_term (title, runs_a_year) VALUES ('rental_common_half-year','2')");
$oProc->query("INSERT INTO rental_billing_term (title, runs_a_year) VALUES ('rental_common_quarterly','4')");
$oProc->query("INSERT INTO rental_billing_term (title, runs_a_year) VALUES ('rental_common_monthly','12')");
$oProc->query("INSERT INTO rental_billing_term (title, runs_a_year) VALUES ('rental_common_every_second_week','24')");

$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1230768000,1253491200,'2009-01-15',{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1230768000,1607731200,'2009-01-15',{$loc_id_internal},2,{$rental_internal}, 1250593658, {$rental_internal})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1199145600,1850169600,'2008-01-15',{$loc_id_in},2,{$rental_in}, 1250593658, {$rental_in})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1254355200,1886716800,'2009-10-15',{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1253491200,1886716800,'2009-09-15',{$loc_id_in},2,{$rental_in}, 1250593658, {$rental_in})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1233619200,1886716800,'2009-02-15',{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1250035200,1886716800,'2009-08-15',{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1245110400,1886716800,'2009-06-16',{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1243814400,1886716800,'2009-06-15',{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1075593600,1706832000,'2004-02-15',{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id) VALUES (1045008000,NULL,'2005-01-01',{$loc_id_internal},4,{$rental_internal}, 1250593658, {$rental_internal}, 'K00000659')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id) VALUES (1047945600,NULL,'2005-01-01',{$loc_id_internal},4,{$rental_internal}, 1250593658, {$rental_internal}, 'K00000660')");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id) VALUES (915148800,NULL,'2005-01-01',{$loc_id_internal},4,{$rental_internal}, 1250593658, {$rental_internal}, ' K00000585')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id) VALUES (915148800,NULL,'2005-01-01',{$loc_id_internal},4,{$rental_internal}, 1250593658, {$rental_internal}, ' K00000586')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id) VALUES (915148800,NULL,'2005-01-01',{$loc_id_internal},4,{$rental_internal}, 1250593658, {$rental_internal}, ' K00000587')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id) VALUES (1136073600,NULL,'2006-01-01',{$loc_id_internal},4,{$rental_internal}, 1250593658, {$rental_internal}, ' K00006497')");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id) VALUES (1199145600,NULL,'2005-01-01',{$loc_id_internal},4,{$rental_internal}, 1250593658, {$rental_internal}, ' K00000797')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id) VALUES (1104537600,NULL,'2005-01-01',{$loc_id_internal},4,{$rental_internal}, 1250593658, {$rental_internal}, ' K00000798')");
	
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (1,1)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (2,2)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (3,3)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (4,4)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (5,5)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (6,6)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (7,7)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (8,8)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (9,9)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (10,10)");
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (11,11)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (12,11)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (13,12)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (14,13)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (15,14)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (16,12)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (17,15)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (18,15)");
	
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('12345678901','Ola','Nordmann',true,'Bergensgt 5','5050','BERGEN')");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('23456789012','Kari','Nordmann',true,'Nordnesgt 7','5020','BERGEN')");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('34567890123','Per','Nordmann',true,'Solheimsviken 13','5008','BERGEN')");
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R0443','Åge','Nilssen','IDRETT Sentrum sør','Byrådsavdeling for oppvekst','ar564@bergen.kommune.no','R0443',true)");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R0956','Berit','Tande','Bergenhus og Årstad kulturkontor','Byrådsavd. for kultur, næring og idrett','wb902@bergen.kommune.no','R0956',true)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, department, email, account_number, is_active, location_id) VALUES ('R7552','Anna Milde','Thorbjørnsen','Gullstøltunet','Byrådsavd. for helse og omsorg','vk172@bergen.kommune.no','R7552',true,{$loc_id_ba_helse})");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, address_1, postal_code, place, phone, email, is_active) VALUES ('KF06','Øyvind','Berggreen','Gullstøltunet kjøkken','Øvre Kråkenes 111','5152','Bønes','55929846/48','vm152@bergen.kommune.no',true)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, department, email, is_active,location_id) VALUES ('R0401','Anne-Marit','Presterud','Gullstøltunet kjøkken','Byrådsavd. for barnehage og skole','jf684@bergen.kommune.no',true,{$loc_id_ba_barnehage})");
$oProc->query("INSERT INTO rental_party (personal_identification_number, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R0300','Jan-Petter','Stoutland','BHO - Kommunaldirektørens stab','Byrådsavd. for helse og omsorg','gs256@bergen.kommune.no','R0300',true)");

	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (11, 4, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (12, 5, true)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (13, 6, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (14, 6, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (15, 6, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (16, 7, true)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (17, 8, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (18, 9, true)");

$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Fellesareal', '123456789', true, 34.59)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Administrasjon', 'Y900', true, 23.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Parkeringsplass', '124246242', false, 50.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Forsikring', 'Y901', true, 10.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Kapitalkostnad', 'Y904', true, 700.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Kom.avg. uten renovasjon', 'Y902', true, 32.29)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Renovasjon', 'Y903', true, 10.94)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Vedlikehold', 'Y905', true, 98.23)");

	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 11, 'Administrasjon', 1712, 0, 'Y900', true, 23.98, 41053.76, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 11, 'Forsikring', 1712, 0, 'Y901', true, 10.57, 18095.84, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 11, 'Kapitalkostnad', 1712, 0, 'Y904', true, 759.85, 1300863.20, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 11, 'Kom.avg. uten renovasjon', 1712, 0, 'Y902', true, 32.29, 55280.48, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 11, 'Renovasjon', 1712, 0, 'Y903', true, 10.94, 18729.28, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 11, 'Vedlikehold', 1712, 0, 'Y905', true, 98.23, 168169.76, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 12, 'Administrasjon', 1158, 0, 'Y900', true, 23.98, 27768.84, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 12, 'Forsikring', 1158, 0, 'Y901', true, 10.57, 12240.06, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 12, 'Kapitalkostnad', 1158, 0, 'Y904', true, 702.34, 813309.72, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 12, 'Kom.avg. uten renovasjon', 1158, 0, 'Y902', true, 32.29, 37391.82, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 12, 'Renovasjon', 1158, 0, 'Y903', true, 10.94, 12668.52, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 12, 'Vedlikehold', 1158, 0, 'Y905', true, 98.23, 113750.34, '2009-01-01', NULL)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 13, 'Administrasjon', 7039, 0, 'Y900', true, 23.98, 168795.22, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 13, 'Forsikring', 7039, 0, 'Y901', true, 10.57, 74402.23, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 13, 'Kapitalkostnad', 7039, 0, 'Y904', true, 835.69, 5882421.91, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 13, 'Vedlikehold', 7039, 0, 'Y905', true, 98.23, 691440.97, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 14, 'Administrasjon', 53, 0, 'Y900', true, 23.98, 1270.94, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 14, 'Forsikring', 53, 0, 'Y901', true, 10.57, 560.21, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 14, 'Kapitalkostnad', 53, 0, 'Y904', true, 44291.57, 5882421.91, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 14, 'Vedlikehold', 53, 0, 'Y905', true, 98.23, 5206.19, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 15, 'Administrasjon', 13, 0, 'Y900', true, 23.98, 311.74, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 15, 'Forsikring', 13, 0, 'Y901', true, 10.57, 137.41, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 15, 'Kapitalkostnad', 13, 0, 'Y904', true, 10863.97, 5882421.91, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 15, 'Vedlikehold', 13, 0, 'Y905', true, 98.23, 1276.99, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 16, 'Administrasjon', 360, 0, 'Y900', true, 23.98, 8632.80, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 16, 'Forsikring', 360, 0, 'Y901', true, 10.57, 3805.20, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 16, 'Kapitalkostnad', 360, 0, 'Y904', true, 835.69, 300848.40, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 16, 'Vedlikehold', 360, 0, 'Y905', true, 98.23, 35362.80, '2009-01-01', NULL)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 17, 'Administrasjon', 792.3, 0, 'Y900', true, 23.27, 18436.82, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 17, 'Forsikring', 792.3, 0, 'Y901', true, 10.25, 8121.08, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 17, 'Kapitalkostnad', 792.3, 0, 'Y904', true, 1042.95, 826329.29, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 17, 'Kom.avg. uten renovasjon', 792.3, 0, 'Y902', true, 32.29, 25583.37, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 17, 'Renovasjon', 792.3, 0, 'Y903', true, 10.94, 8667.76, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 17, 'Vedlikehold', 792.3, 0, 'Y905', true, 95.28, 75490.34, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 18, 'Administrasjon', 1160.4, 0, 'Y900', true, 23.98, 27826.39, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 18, 'Forsikring', 1160.4, 0, 'Y901', true, 10.57, 12265.43, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 18, 'Kapitalkostnad', 1160.4, 0, 'Y904', true, 1075.18, 1247638.87, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 18, 'Kom.avg. uten renovasjon', 1160.4, 0, 'Y902', true, 32.29, 37469.32, '2005-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 18, 'Renovasjon', 1160.4, 0, 'Y903', true, 10.94, 12694.78, '2005-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 18, 'Vedlikehold', 1160.4, 0, 'Y905', true, 98.23, 113986.09, '2009-01-01', NULL)");

$oProc->query("INSERT INTO rental_notification (location_id, contract_id, message, date, recurrence) VALUES ({$loc_id_internal},11,'Oppdatér leietaker med ny postadresse.',1250593658,0)");
$oProc->query("INSERT INTO rental_notification (location_id, contract_id, message, date, recurrence) VALUES ({$loc_id_internal},13,'Leietaker tilbake fra ferie. Følg opp e-post sendt ut for to uker siden.',1250593658,0)");
$oProc->query("INSERT INTO rental_notification (location_id, contract_id, message, date, recurrence) VALUES ({$loc_id_internal},15,'Kontrollér at priselementer er i henhold.',1250593658,0)");
$oProc->query("INSERT INTO rental_notification (location_id, contract_id, message, date, recurrence) VALUES ({$loc_id_internal},17,'Oppdatér med ny postadresse.',1250593658,0)");
$oProc->query("INSERT INTO rental_notification (location_id, contract_id, message, date, recurrence) VALUES ({$loc_id_internal},18,'Oppdatér med ny postadresse.',1250593658,0)");

$oProc->query("INSERT INTO rental_notification_workbench (account_id, notification_id, date, dismissed) VALUES ({$rental_internal},1,1250593658, 'FALSE')");
$oProc->query("INSERT INTO rental_notification_workbench (account_id, notification_id, date, dismissed) VALUES ({$rental_internal},2,1250593658, 'FALSE')");

$oProc->query("INSERT INTO rental_contract_last_edited VALUES (2,{$rental_internal},1250593658)");
$oProc->query("INSERT INTO rental_contract_last_edited VALUES (1,{$rental_in},1250593658)");
$oProc->query("INSERT INTO rental_contract_last_edited VALUES (3,{$rental_out},1250593658)");

$asyncservice = CreateObject('phpgwapi.asyncservice');
$asyncservice->delete('rental_populate_workbench_notifications');
$asyncservice->set_timer(
	array('day' => "*/1"),
	'rental_populate_workbench_notifications',
	'rental.sonotification.populate_workbench_notifications',
	null 
	);


