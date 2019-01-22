<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobController extends AbstractController
{
    /*
    ** Auth informations
    */
    private $client_id = "PAR_jobitest_17f14565b15c6a532d263872a445518d2a5b3848068a11f994597b62eefb25df";
    private $client_secret = "7960d2794c515338debcd8c22d103064f2ed974cb04511ef45d79ee6fe97e326";
    private $scopes = [
        "application_PAR_jobitest_17f14565b15c6a532d263872a445518d2a5b3848068a11f994597b62eefb25df",
        "o2dsoffre", 
        "api_offresdemploiv2"
    ];
    

    private $grant_type = "client_credentials";
    private $access_token;
    
    private $NO_ACCESS = 0;
    private $ACCESS_GRANTED = 1;

    private function getScopes(){
        $scopes_to_return = "";
        foreach ($this->scopes as $scope) {
            $scopes_to_return .= "$scope ";
        }
        return $scopes_to_return;
    }

    private function getAccessToken(){
        
        $route = "https://entreprise.pole-emploi.fr/connexion/oauth2/access_token?realm=%2Fpartenaire";
        $headers = array(
            'Accept' => 'application/json',
            "Content-Type" => 'application/x-www-form-urlencoded'
        );

        $client = new \GuzzleHttp\Client();
        
        try {
            $response = $client->request('POST', $route, [
                'form_params' => [
                    'grant_type' => $this->grant_type,
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'scope' => $this->getScopes(),
                ]
            ]);
            $response_body = json_decode($response->getBody());
            $this->access_token = $response_body->access_token;
            return [
                "access" => $this->ACCESS_GRANTED,
                "error" => ""
            ];
        } catch (\Exception $e) {
            // write exception in logs for better follow
            return [
                "access" => $this->NO_ACCESS,
                "error" => $e->getResponse()->getBody(true)
            ];
        }
    }

    private function getQueryParameters($params){
        $count = count($params);
        $counter = 0;
        $query_params = "?";
        foreach ($params as $key => $value){
            $query_params .= "$key=$value"; 
            $counter++;
            if ($counter != $count) {
                $query_params .= "&";
            }
        }
        return ($query_params);
    }

    /**
     * @Route("/jobs", name="get_list_of_jobs")
     */
    public function index()
    {
        $accessInformations = $this->getAccessToken();
        if ($accessInformations['access'] == $this->ACCESS_GRANTED) {

            $route = "https://api.emploi-store.fr/partenaire/offresdemploi/v2/offres/search";
            $parameters = array(
                "commune" => "33063",
                "publieeDepuis" => "1"
            );
            $headers = array(
                "Authorization" => "Bearer " . $this->access_token 
            );

            // l'intitulé
            // la description
            // le lieu
            // le type de contrat
            // l'entreprise

            $client = new \GuzzleHttp\Client();
            try {
                $response = $client->request('GET', $route.$this->getQueryParameters($parameters), ['headers' => $headers]);
                $response_body = json_decode($response->getBody());
                $propositions = array();
                $counter = 0;
                foreach($response_body->resultats as $resultat){
                    array_push($propositions, [
                        "name" => $resultat->intitule,
                        "description" => $resultat->description,
                        "location" => $resultat->lieuTravail->libelle,
                        "type" => $resultat->typeContrat,
                        "enterprise" => $resultat->entreprise->nom
                    ]);
                    $counter++;
                    if ($counter == 10){
                        //var_dump($propositions);
                        return $this->render('propositions.html.twig', [
                            'propositions' => $propositions,
                        ]);
                    }
                }                
            } catch (\Exception $e) {
                return new Response(
                    "<html><body>Access granted but mistakes were made<br>".$e."</body></html>"
                );
            }

            
        }
        else 
        {
            return new Response(
                "<html><body>Can't authenticate to the API<br>".$accessInformations['error']."</body></html>"
            );

        }
    }
}