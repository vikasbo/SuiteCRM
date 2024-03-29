<?php
$viewdefs['Opportunities'] = array(
    'DetailView' => array(
        'templateMeta' => array(
            'form' => array(
                'buttons' => array(
                    0 => 'EDIT',
                    1 => 'DUPLICATE',
                    2 => 'DELETE',
                    3 => 'FIND_DUPLICATES',
                    4 => array(
                        'customCode' => '{if $bean->aclAccess("edit") && !$DISABLE_CONVERT_ACTION}<input title="{$MOD.LBL_CONVERTOPP_TITLE}" type="button" class="button" name="convert" value="{$MOD.LBL_CONVERTOPP}">{/if}',
                        'sugar_html' => array(
                            'type' => 'button',
                            'value' => '{$MOD.LBL_CONVERTOPP}',
                            'htmlOptions' => array(
                                'title' => '{$MOD.LBL_CONVERTOPP_TITLE}',
                                'class' => 'button',
                                'name' => 'convert',
                                'id' => 'convert_opp_button'
                            ),
                            'template' => '{if $bean->aclAccess("edit") && !$DISABLE_CONVERT_ACTION}[CONTENT]{/if}'
                        )
                    )
                )
            ),
            'maxColumns' => '2',
            'widths' => array(
                0 => array(
                    'label' => '10',
                    'field' => '30'
                ),
                1 => array(
                    'label' => '10',
                    'field' => '30'
                )
            ),
            'includes' => array(
                0 => array(
                    'file' => 'custom/modules/Opportunities/js/Opportunity.js'
                ),
            ),

            'useTabs' => true,
            'tabDefs' => array(
                'DEFAULT' => array(
                    'newTab' => true,
                    'panelDefault' => 'expanded'
                ),
                'LBL_PANEL_ASSIGNMENT' => array(
                    'newTab' => true,
                    'panelDefault' => 'expanded'
                )
            )
        ),
        'panels' => array(
            'default' => array(
                0 => array(
                    0 => 'name',
                    1 => 'account_name'
                ),
                1 => array(
                    0 => array(
                        'name' => 'amount',
                        'label' => '{$MOD.LBL_AMOUNT} ({$CURRENCY})'
                    ),
                    1 => 'date_closed'
                ),
                2 => array(
                    0 => 'sales_stage',
                    1 => 'opportunity_type'
                ),
                3 => array(
                    0 => 'probability',
                    1 => 'lead_source'
                ),
                4 => array(
                    0 => 'next_step',
                    1 => 'campaign_name'
                ),
                5 => array(
                    0 => array(
                        'name' => 'description',
                        'nl2br' => true
                    )
                ),
                6 => array(
                    0 => array(
                        'name' => 'assigned_user_name',
                        'label' => 'LBL_ASSIGNED_TO'
                    )
                )
            ),
            'LBL_PANEL_ASSIGNMENT' => array(
                0 => array(
                    0 => array(
                        'name' => 'date_modified',
                        'label' => 'LBL_DATE_MODIFIED',
                        'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}'
                    ),
                    1 => array(
                        'name' => 'date_entered',
                        'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}'
                    )
                )
            )
        )
    )
);