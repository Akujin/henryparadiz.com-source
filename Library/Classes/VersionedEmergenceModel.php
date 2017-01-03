<?php
class VersionedEmergenceModel extends VersionedRecord
{
	static public $fields = array(
        'ID' => array(
            'type' => 'integer'
            ,'autoincrement' => true
            ,'unsigned' => true
        )
        ,'Class' => array(
            'type' => 'enum'
            ,'notnull' => true
            ,'values' => array()
        )
        ,'Created' => array(
            'type' => 'timestamp'
            ,'default' => 'CURRENT_TIMESTAMP'
        )
        ,'CreatorID' => array(
            'type' => 'integer'
            ,'notnull' => false
        )
    );
}