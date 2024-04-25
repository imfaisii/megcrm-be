<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use Exception;
use Goutte\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

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

    public function getAddressLink($crawler, string $address): array|null
    {
        $result = null;
        $links = $crawler->filter('a.govuk-link');

        $links->each(function (Crawler $node, $i) use (&$result, $address) {
            $link = $node->attr('href');
            $text = $node->text();

            $textAddress = Str::replace("  ", " ", Str::replace(",", " ", $text));

            if (Str::contains($textAddress, $address, true)) {
                $result = [
                    'link' => $link,
                    'text' => $text
                ];
            }
        });

        return $result;
    }

    public function getEpcDetails(Lead $lead)
    {
        try {
            $baseUrl = "https://find-energy-certificate.service.gov.uk";
            $data = [];

            $client = new Client();
            $crawler = $client->request('GET', "{$baseUrl}/find-a-certificate/search-by-postcode?postcode={$lead->post_code}");

            $addressLink = $this->getAddressLink($crawler, $lead['raw_api_response']['address'][0]);

            if (!is_null($addressLink)) {
                $crawler = $client->request('GET', "{$baseUrl}{$addressLink['link']}");

                // epc energy grade
                $epcEnergyClass = "p.epc-rating-result";
                $epcEnergyNode = $crawler->filter("$epcEnergyClass");

                $data['epc_energy'] = null;
                if ($epcEnergyNode->count() > 0) {
                    $epcEnergyData = $epcEnergyNode->text();
                    $data['epc_energy'] = $epcEnergyData;
                }

                // find all elements with class "govuk-summary-list__row"
                $crawler->filter('.govuk-summary-list__row')->each(function ($row) use (&$data) {
                    $key = $row->filter('.govuk-summary-list__key')->text();
                    $value = trim($row->filter('.govuk-summary-list__value')->text());

                    // Add the key-value pair to the results array
                    $data[Str::snake(Str::replace("’", "", $key))] = $value;
                });

                // find the table element
                $data['features'] = [];
                $tableNode = $crawler->filter('table.govuk-table');
                if ($epcEnergyNode->count() > 0) {
                    $tableNode->filter('tbody.govuk-table__body tr')->each(function ($row) use (&$data) {
                        $data['features'][] = [
                            'feature' => $row->filter('th.govuk-table__cell')->eq(0)->text(),
                            'description' => $row->filter('td.govuk-table__cell')->eq(0)->text(),
                            'rating' => $row->filter('td.govuk-table__cell')->eq(1)->text()
                        ];
                    });
                }

                // rating current and potential
                $data['rating_current'] = null;
                $data['rating_potential'] = null;
                $ratingCurrentSelector = "svg.rating-current.rating-label";
                $ratingPotentialSelector = "svg.rating-potential.rating-label";
                try {
                    $ratingCurrentNode = $crawler->filter($ratingCurrentSelector);
                    if ($ratingCurrentNode->count() > 0) {
                        $data['rating_current'] = $ratingCurrentNode->text();
                    }

                    $ratingPotentialNode = $crawler->filter($ratingPotentialSelector);
                    if ($ratingPotentialNode->count() > 0) {
                        $data['rating_potential'] = $ratingPotentialNode->text();
                    }
                } catch (Exception $e) {
                    Log::error("Potential Current error at line " . $e->getLine() . ": " . $e->getMessage());
                }

                // change can make ( improvements )
                $data['improvements'] = [];
                $improvements = $crawler->filter('.epb-recommended-improvements');
                if ($improvements->count() > 0) {
                    $improvements->filter('h3')->each(function ($h3) use (&$data) {
                        $data['improvements'][] = $h3->text();
                    });
                }

                $lead->update([
                    'epc_details' => $data
                ]);
            } else {
                Log::error("Skipping {$lead->post_code} {$lead->raw_api_response['address'][0]} as no address link is found.");
            }
        } catch (Exception $e) {
            Log::error("Parent EPC scrap error at line " . $e->getLine() . ": " . $e->getMessage());
        }
    }
}
