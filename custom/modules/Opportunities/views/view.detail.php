<?php

require_once 'modules/Opportunities/views/view.detail.php';

class CustomOpportunitiesViewDetail extends OpportunitiesViewDetail
{


    function preDisplay()
    {
        if($this->bean->sales_stage == "Closed Won"){
            $this->ss->assign('DISABLE_CONVERT_ACTION', TRUE);
        }
        parent::preDisplay();
    }

}