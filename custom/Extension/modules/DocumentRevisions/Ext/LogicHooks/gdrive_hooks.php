<?php

$hook_array['after_save'][]   = array(
    1,
    'hook to save uploaded document to google drive',
    'custom/logichooks/modules/DocumentRevisions/GdriveHook.php',
    'GdriveHookClass',
    'afterSave'
);
$hook_array['before_save'][]   = array(
    1,
    'hook to decide whether record is new or not',
    'custom/logichooks/modules/DocumentRevisions/GdriveHook.php',
    'GdriveHookClass',
    'beforeSave'
);
$hook_array['after_delete'][] = array(
    1,
    'hook to delete uploaded document from google drive',
    'custom/logichooks/modules/DocumentRevisions/GdriveHook.php',
    'GdriveHookClass',
    'afterDelete'
);