<?php
/**
 * NewLead controller
 */
namespace app\Controllers\Hook;

use App\Jobs\NewLeadJob;

class CreateLeadHook extends \Core\Controllers\Controller
{
    public function newLead()
    {
        //Получили хук и залогировали данные
        $input_data = $this->request->input();
        $l = logger('webhooks/newLead.log');
        $l->log('webHookData',$input_data);

        //Обработка хука в очереди
        $job = new NewLeadJob($input_data);
        $queue = queue()->push($job, 'dev6');
        $queue->setState(0);

        ajaxSuccess(true);
    }
}
