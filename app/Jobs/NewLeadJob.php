<?php

namespace App\Jobs;

class NewLeadJob
{
    public array $data;
    private const CF_CITY = 787590; // город(студия)

    //Получаем данные для обработки
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $l = logger('jobs/newLead.log');

        //Получаем клиента
        $amoClient = app('client')->crmIntegration();

        //Проверка данных
        if (isset($this->data['auth_key'])) {
            $lead_id = $this->data['id_amo'];
            $lead = $amoClient->leads()->find($lead_id);
            $lead->setStatus($this->data['status_id']);
        } else {
            $lead_id = $this->data['leads']['add']['0']['id'];
            $lead = $amoClient->leads()->find($lead_id);
            $contact = $lead->contact;
            if (!$contact) return true;
            $note = $lead->notes->last();
            $dataToSend = [
                "key" => "4d1571c9-051e-4d78-8a23-cc74d53f0a6f",
                "data" => [
                    "phone" => $contact->cf('Телефон')->getValue(),
                    "name" => $contact->name,
                    "misc" => [
                        "API_Id_AMO" => $lead_id,
                        "API_City" => $lead->cf()->byId(static::CF_CITY)->getValue(),
//                        "API_Offer" => "",
                        "API_Note" => $note->text,
                    ]
                ]
            ];
            $l->log('DataToSend', $dataToSend);
            $response = json_decode(static::sendLead($dataToSend));
            $l->log('Responce', $response);
            // изменение статуса
//            $arrayResponse = json_decode($response, true);
//            $leadStatusId = $arrayResponse['status_id'];
//            $lead->status_id = $leadStatusId;
        }
    }

    private static function sendLead($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.crmagency.ru:4443',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}