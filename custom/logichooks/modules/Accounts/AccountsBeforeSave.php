<?php


require_once "custom/src/SimpleBillingApi.php";

/**
 * Class AccountsBeforeSave
 */
class AccountsBeforeSave
{

    /**
     * @param $bean
     * @param $arguments
     * @param $event
     */
    public function callBeforeSave($bean, $arguments, $event)
    {
        if(empty($bean->fetched_row['id']) && isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == "Opportunities" && isset($_REQUEST['return_action']) && $_REQUEST['return_action'] == "DetailView" && isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != ""){

            //link opp to newly created account
            $opp = BeanFactory::getBean("Opportunities", $_REQUEST['return_id']);
            $opp->account_id = $bean->id;
            $opp->save(FALSE);

            //check for any contacts related to the opp, if found link them to account

            if($opp->load_relationship('contacts')){
                foreach($opp->contacts->getBeans() as $contact){
                    $contact->account_id = $bean->id;
                    $contact->save(FALSE);
                }
            }
        }
    }

    /**
     * @param $bean
     * @param $arguments
     * @param $event
     * Calls Billing API when a new Account is created in CRM
     */
    public function callBillingApi($bean, $arguments, $event){
        if(empty($bean->fetched_row['id'])){

            global $sugar_config;

            $requestDynamicKeys = $sugar_config['simpleBilling']['customerRequestDynamicKeys'];

            //static parameters needs to be added in config_override
            $data = $sugar_config['simpleBilling']['customerRequestParams'];
            $date = new DateTime('first day of this month');

            //setting dynamic values from account bean
            $data[$requestDynamicKeys['invoiceDate']] = $date->format('m/d/Y');
            $data[$requestDynamicKeys['userName']] = $bean->name;
            $data[$requestDynamicKeys['email']] = $bean->email1;

            try{
                $apiClient = new SimpleBillingApi($sugar_config['simpleBilling']['apiURL'], $sugar_config['simpleBilling']['authToken']);

                $response = $apiClient->post('/customer',$data);
                if(isset($response['data']->data->billingUserId)){
                    $bllingId = $response['data']->data->billingUserId;
                }else{
                    $bllingId = preg_replace('/[^0-9]/', '', $response['data']->message);
                }
                $bean->billing_id = $bllingId;

            }catch (Exception $e){
                $GLOBALS['log']->fatal("Customer POST request failed : ".$e->getMessage());
            }

        }

    }
}