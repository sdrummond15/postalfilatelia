<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('layerslider'))
    ->addColumn('slide_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Slide ID')
    ->addColumn('slide_title', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
		'nullable'  => false,
        ), 'Slide Title')
	->addColumn('slide_url', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
		), 'Slide URL')
	->addColumn('slide_img', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
		'nullable'  => false,
        ), 'Slide Image')
	->addColumn('slide_caption1', Varien_Db_Ddl_Table::TYPE_TEXT, 500, array(
        ), 'Slide Caption1')
	->addColumn('slide_caption2', Varien_Db_Ddl_Table::TYPE_TEXT, 500, array(
        ), 'Slide Caption2')
	->addColumn('slide_caption3', Varien_Db_Ddl_Table::TYPE_TEXT, 500, array(
        ), 'Slide Caption3')
	->addColumn('slide_caption4', Varien_Db_Ddl_Table::TYPE_TEXT, 500, array(
        ), 'Slide Caption4')
	->addColumn('slide_caption5', Varien_Db_Ddl_Table::TYPE_TEXT, 500, array(
        ), 'Slide Caption5')
	->addColumn('slide_captionimg1', Varien_Db_Ddl_Table::TYPE_TEXT, 500, array(
        ), 'Slide CaptionImg1')
	->addColumn('slide_captionimg2', Varien_Db_Ddl_Table::TYPE_TEXT, 500, array(
        ), 'Slide CaptionImg2')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Enabled')
    ->addColumn('active_from', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Active From Time')
    ->addColumn('active_to', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Active To Time');

$installer->getConnection()->createTable($table);

$installer->endSetup();