<?php

namespace App;

use Illuminate\Support\Collection;
use League\Csv\Writer;
use Symfony\Component\DomCrawler\Crawler;

class DomainsDnsOnlyDnsScraper
{

    /**
     * @param string $html
     * @return int
     * @throws \League\Csv\CannotInsertRecord
     */
    public function scrape(string $html)
    {
        $title = $this->crawlTitle($html) ?? 'dns-records'.time();

        $websiteDnsCollection = $this->crawlItemsFromSettings('Websettings', $html);
        $emailDnsCollection = $this->crawlItemsFromSettings('Emailsettings', $html);
        $otherDnsCollection = $this->crawlItemsFromSettings('Anderesettings', $html);

        $csv = Writer::createFromFileObject(new \SplTempFileObject);
        $csv->insertOne($websiteDnsCollection[0]->keys()->toArray());

        foreach ($websiteDnsCollection as $dnsItem) {
            $csv->insertOne($dnsItem->toArray());
        }

        foreach ($emailDnsCollection as $dnsItem) {
            $csv->insertOne($dnsItem->toArray());
        }

        foreach ($otherDnsCollection as $dnsItem) {
            $csv->insertOne($dnsItem->toArray());
        }

        return $csv->output($title.'-dns-records.csv');
    }

    /**
     * @param string $html
     * @return string
     */
    protected function crawlTitle(string $html): string
    {
        $crawler = new Crawler($html);
        return $crawler->filter('h1')->first()->text();
    }

    /**
     * @param string $id
     * @param string $html
     * @return Collection
     */
    protected function crawlItemsFromSettings(string $id, string $html): Collection
    {
        $crawler = new Crawler($html);
        $dnsCollection = new Collection();
        $crawler
            ->filter('#'.$id.' .row .row')->each(function (Crawler $node, $i) use($dnsCollection) {
                $dnsItemHtml = new Crawler($node->html());

                $dnsItem = new Collection();
                $dnsCollection->put($i, $dnsItem);

                $dnsItemHtml->filter('div')->each(function (Crawler $node, $itemRow) use ($dnsCollection, $i){
                    if($itemRow === 0) {
                        $dnsCollection[$i]->put('title', $node->text());
                    }
                    if($itemRow === 1) {
                        $dnsCollection[$i]->put('type', $node->text());
                    }
                    if($itemRow === 2) {
                        $dnsCollection[$i]->put('value', $node->text());
                    }

                });
            });

        return $dnsCollection->filter(function ($dnsItem){
            return $dnsItem->get('value');
        });
    }

}