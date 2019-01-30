<?php


$hook_array['after_save'][] = array(
    1,
    'hook to save uploaded document drive url under parent document',
    'custom/logichooks/modules/Documents/documentsHooks.php',
    'documentsHooks',
    'afterSave'
);


$hook_array['after_relationship_delete'][] = array(1,
    'hook to delete uploaded document from google drive when a revision is unlinked',
    'custom/logichooks/modules/Documents/documentsHooks.php', 'documentsHooks', 'afterRelationshipDelete'
);