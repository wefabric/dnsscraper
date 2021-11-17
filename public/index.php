<?php

if(!file_exists(__DIR__.'/../vendor/autoload.php')) {
    throw new Exception('Run composer install in root');
}

require __DIR__.'/../vendor/autoload.php';


if(!isset($_POST['html'])) {
    ?>

   <h1>Domains.dnsonly.nl DNS scraper</h1>
    <ol>
        <li>Zoek het betreffende domein op bij <a href="https://domains.dnsonly.nl/">https://domains.dnsonly.nl/</a>.</li>
        <li>Klik op onze diensten</li>
        <li>Rechtermuisknop op pagina en dan 'Element inspecteren'</li>
        <li>Kopieer de complete HTML inhoud</li>
        <li>Plak in onderstaande veld</li>
        <li>Klik op DNS gegevens ophalen</li>
    </ol>
    <form action="index.php" method="post">
        <label for="html">HTML</label> <br />
        <textarea id="html" name="html" rows="10" cols="100"></textarea> <br />
        <button type="submit" style="margin-top: 1rem; padding: 1rem 0.5rem; cursor: pointer;">DNS gegevens ophalen</button>
    </form>

<?php
    return;
}

if(isset($_POST['html'])) {
    $domainsDnsOnlyScraper = new \App\DomainsDnsOnlyDnsScraper();
    return $domainsDnsOnlyScraper->scrape($_POST['html']);
}


