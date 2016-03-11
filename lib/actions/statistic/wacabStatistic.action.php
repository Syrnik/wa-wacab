<?php

class wacabStatisticAction extends waViewAction
{

    public function execute()
    {
        $model = new waModel;
        $payment_model = new wacabPaymentModel();
        $payments = $payment_model->getAll();
        
        $apps_model = new wacabAppsModel();
        $apps = $apps_model->getByField('stat', 1, true);
        
        $total = $model->query('SELECT SUM(pay) FROM wacab_payment WHERE `apps_id` is not null')->fetchAll();

        $plugins_stat = array();
        
        foreach($apps as $app){
            $app_total = $model->query('SELECT SUM(pay) FROM wacab_payment WHERE `apps_id` = '.$app['id'])->fetch();
            $app_count = $model->query('SELECT COUNT(*) FROM wacab_payment WHERE `pay` >= 0 AND `apps_id` = '.$app['id'])->fetch();
            $app_return = $model->query('SELECT COUNT(*) FROM wacab_payment WHERE `pay` < 0 AND `apps_id` = '.$app['id'])->fetch();
            $names = json_decode($app['name'], true);
            if($app['parent'] == 'no_parent' || $app['parent'] == ''){
            	$pname = $names[0];
            }else{
            	$pname = $names[0]." (".$app['parent'].")";
            }
            $plugins_stat[] = array(
                'id' => $app['app_id'],
                'total' => $app_total[0],
                'name' => $pname,
                'count' => $app_count[0],
                'return' => $app_return[0],
            );
            
        }
        
        $this->view->assign('total', $total);
        $this->view->assign('apps', $plugins_stat);
        $this->setTemplate(wacabHelper::getAppPath() . '/templates/actions/statistic/stat_page.html');
    }

}
