<?php

namespace App\Actions\Leads;

use Goutte\Client;

class GetOtherSitesLinkAction
{
    public function councilTax(string $postCode)
    {
        $client = new Client();

        $crawler = $client->request('GET', 'https://www.tax.service.gov.uk/check-council-tax-band/search');

        $form = $crawler->selectButton('Search')->form();
        $form['postcode'] = $postCode;

        $crawler = $client->submit($form);

        return $crawler->getUri();
    }
}
