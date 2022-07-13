<?php


namespace App\Integrations\GLS;


use App\Entities\OrderPackage;
use App\Integrations\GLS\soap\PackageObjectBuilder;
use Illuminate\Support\Facades\Storage;
use SoapClient;
use stdClass;

class GLSClient
{

    private $client;
    private $session;

    public function auth()
    {
        $login = env("GLS_LOGIN");
        $password = env("GLS_PASSWORD");
        $url = env('GLS_API_URL');
        if (empty($url)) {
            throw new \Exception('Brak zdefiniowanego adresu url API ' . __class__);
        }
        if (empty($login) || empty($password)) {
            throw new \Exception('Brak danych logowania API ' . __class__);
        }
        $options = [];
        
        $this->client = new SoapClient($url, $options);
        try {
            $oCredentials = new stdClass();
            $oCredentials->user_name = $login;
            $oCredentials->user_password = $password;
            $oResponse = $this->client->adeLogin($oCredentials);
            $this->session = $oResponse->return->session;
        } catch (\Exception $e) {
            \Log::error('Problem autentykacji GLS ',
                ['soapDebug' => 'Code: ' . $e->faultcode ?? 'none' . ', FaultString: ' . $e->faultstring ?? 'none',
                    'message' => $e->getMessage(),
                    'stack' => $e->getTraceAsString()]);
        }
    }

    public function logout()
    {
        try {
            $oSession = new stdClass();
            $oSession->session = $this->session;
            $this->client->adeLogout($oSession);
        } catch (\Exception $e) {
            \Log::error('Problem z wylogowaniem się z GLS ', ['message' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
        }
    }

    /**
     * @param OrderPackage $package
     * @return array
     */
    public function createNewPackage(OrderPackage $package)
    {
        try {
            $oPackage = PackageObjectBuilder::preparePackageObject($package, $this->session);
            $oClient = $this->client->adePreparingBox_Insert($oPackage);
            return ['error' => false, 'content' => $oClient->return->id];
        } catch (\Exception $e) {
            \Log::error('Problem z utworzeniem nowej paczki GLS ',
                ['soapDebug' => 'Code: ' . $e->faultcode ?? 'none' . ', FaultString: ' . $e->faultstring ?? 'none',
                    'message' => $e->getMessage(),
                    'stack' => $e->getTraceAsString()]);

            return ['error' => true, 'content' => 'Code: ' . $e->faultcode ?? 'none' . ', FaultString: ' . $e->faultstring ?? 'none'];
        }
    }

    public function getLetterForPackage($number)
    {
        try {
            $oInput = new stdClass();
            $oInput->session = $this->session;
            $oInput->id = $number;
            $oInput->mode = 'roll_160x100_pdf';
            $oClient = $this->client->adePreparingBox_GetConsignLabels($oInput);
            $szLabels = base64_decode($oClient->return->labels);
            Storage::disk('private')->put('labels/gls/' . $number . '.pdf', $szLabels);
        } catch (\Exception $e) {
            \Log::error('Problem z pobraniem naklejki GLS ',
                ['soapDebug' => 'Code: ' . $e->faultcode ?? 'none' . ', FaultString: ' . $e->faultstring ?? 'none',
                    'message' => $e->getMessage(),
                    'stack' => $e->getTraceAsString()]);
        }
    }

    public function getPackageNumer($number)
    {
        try {
            $oInput = new stdClass();
            $oInput->session = $this->session;
            $oInput->id = $number;
            $oClient = $this->client->adePreparingBox_GetConsign($oInput);
            return $oClient->return->parcels->items->number;
        } catch (\Exception $e) {
            \Log::error('Problem ze pobraniem numeru paczki GLS ',
                ['soapDebug' => 'Code: ' . $e->faultcode ?? 'none' . ', FaultString: ' . $e->faultstring ?? 'none',
                    'message' => $e->getMessage(),
                    'stack' => $e->getTraceAsString()]);
        }

    }

    public function confirmSending($ids)
    {
        try {
            $oInput = new stdClass();
            $oInput->session = $this->session;
            $oInput->consigns_ids = new stdClass();
            $oInput->consigns_ids->items = $ids;
            $oInput->desc = 'Potwierdzenie nadania z dn. ' . \Carbon\Carbon::now()->toDateString();
            $oClient = $this->client->adePickup_Create($oInput);
            return $oClient->return->id;
        } catch (\Exception $e) {
            \Log::error('Problem ze potwierdzeniem przesyłek GLS ',
                ['soapDebug' => 'Code: ' . $e->faultcode ?? 'none' . ', FaultString: ' . $e->faultstring ?? 'none',
                    'message' => $e->getMessage(),
                    'stack' => $e->getTraceAsString()]);
        }
    }

    public function deletePackage($id)
    {
        try {
            $oInput = new stdClass();
            $oInput->session = $this->session;
            $oInput->id = $id;
            $oClient = $this->client->adePreparingBox_DeleteConsign($oInput);
            return true;
        } catch (\Exception $e) {
            \Log::error('Problem ze usunięciem przesyłki GLS ',
                ['soapDebug' => 'Code: ' . $e->faultcode ?? 'none' . ', FaultString: ' . $e->faultstring ?? 'none',
                    'message' => $e->getMessage(),
                    'stack' => $e->getTraceAsString()]);
            return false;
        }
    }


}
