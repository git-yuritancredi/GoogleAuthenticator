<?php
	$installer = $this;
	$installer->startSetup();

	//Oath secret table
	$table = $installer->getConnection()
		->newTable($installer->getTable('google_oauth/secrets'))
		->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
			array(
				'identity'  => true,
				'unsigned'  => true,
				'nullable'  => false,
				'primary'   => true,
			),
			'Entity id')
		->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
			array(
				'nullable'  => false,
			),
			'User Entity ID')
		->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, null,
			array(
				'nullable'  => false,
			),
			'User Type ID')
		->addColumn('enabled', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 0, array(
			'nullable'  => false,
		), 'Enabled')
		->addColumn('secret', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'nullable'  => false,
		), 'Secret code')
		->addColumn('recovery', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'nullable'  => true,
		), 'Recovery temp code')
		->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
			'nullable'  => true,
		), 'Creation date')
		->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
			'nullable'  => true,
		), 'Update date');
	$installer->getConnection()->createTable($table);

	//Admin user table
	$installer->getConnection()
	->addColumn($installer->getTable('admin/user'), 'google_oauth_enabled', array(
		'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
		'nullable'  => false,
		'default'	=> 0,
		'comment'   => 'Google Authenticator Enabled'
	));

	//Customer attribute
	$setup 				= new Mage_Eav_Model_Entity_Setup('core_setup');
	$entityTypeId     	= $setup->getEntityTypeId('customer');
	$attributeSetId   	= $setup->getDefaultAttributeSetId($entityTypeId);
	$attributeGroupId 	= $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

	$setup->addAttribute("customer", "google_oauth_enabled",  array(
		"type"     		=> "int",
		"label"    		=> "Google Authentication",
		"input"    		=> "select",
		"source"		=> "eav/entity_attribute_source_boolean",
		"visible"  		=> true,
		"required" 		=> false,
		"default" 		=> '0',
		"unique"     	=> false,
	));

	$attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "google_oauth_enabled");

	$setup->addAttributeToGroup(
		$entityTypeId,
		$attributeSetId,
		$attributeGroupId,
		'google_oauth_enabled',
		'100'
	);

	$used_in_forms 		= array();
	$used_in_forms[] 	= "adminhtml_customer";

	$attribute->setData("used_in_forms", $used_in_forms)
	->setData("is_used_for_customer_segment", true)
	->setData("is_system", 0)
	->setData("is_user_defined", 1)
	->setData("is_visible", 1)
	->setData("sort_order", 100);

	$attribute->save();

	$installer->endSetup();