<?php

class OpportunitiesController extends SugarController
{

    public function action_convert_opp()
    {
        if(isset($_REQUEST['record']) && $_REQUEST['record'] != ""){
            $opp = BeanFactory::getBean("Opportunities", $_REQUEST['record']);
            $opp->sales_stage = "Closed Won";
            $opp->probability = 100;
            $opp->save();
        }
    }
}