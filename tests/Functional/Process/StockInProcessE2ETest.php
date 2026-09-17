<?php

declare(strict_types=1);

namespace WebWMS\Tests\Functional\Process;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class StockInProcessE2ETest extends WebTestCase
{
    /**
     * @test SI102: Wareneingang Workflow
     */
    public function testStockInFromGoodsReceiptEndToEnd(): void
    {
        $client = static::createClient();

        // 1. Login
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sign in')->form([
            'username' => 'admin',
            'password' => 'admin',
        ]);
        $client->submit($form);

        // 2. Navigiere zu Stock-In Formular
        $crawler = $client->request('GET', '/stock_in_from_goods_receipt');
        $this->assertResponseIsSuccessful();

        // 3. Fülle Formular aus
        $form = $crawler->selectButton('Speichern')->form([
            'stock_in[articleNr]' => '123456',
            'stock_in[quantity]' => '100',
            'stock_in[leQuantity]' => '10',
            'stock_in[standardLoadingEquipment]' => 'PALETTE',
            'stock_in[charge]' => 'CHARGE-001',
        ]);
        $crawler = $client->submit($form);

        // 4. Prüfe Bestätigungs-Modal
        $this->assertStringContainsString('Bestätigungslagerplätze', $client->getResponse()->getContent());

        // 5. Bestätige Einlagerung
        $form = $crawler->selectButton('Finalisieren')->form();
        $client->submit($form);

        // 6. Prüfe TA-Erzeugung
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('erfolgreich', $client->getResponse()->getContent());
    }

    /**
     * @test Workflow: TA-Lifecycle
     */
    public function testTransportRequestWorkflow(): void
    {
        $client = static::createClient();
        $this->login($client);

        // Ziel: TA durchläuft Workflow open -> in_progress -> done -> archived

        // 1. TA in "open" Status prüfen
        $crawler = $client->request('GET', '/transport_request/1/status');
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('open', $response['state']);
        $this->assertContains('start', $response['enabled_transitions']);

        // 2. Start TA
        $client->request('POST', '/transport_request/1/start');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // 3. Prüfe State wechselte zu in_progress
        $crawler = $client->request('GET', '/transport_request/1/status');
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('in_progress', $response['state']);

        // 4. Beende TA
        $client->request('POST', '/transport_request/1/complete');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // 5. Prüfe State wechselte zu done
        $crawler = $client->request('GET', '/transport_request/1/status');
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('done', $response['state']);

        // 6. Archiviere TA
        $client->request('POST', '/transport_request/1/archive');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @test Inventur: Start -> Count -> Complete
     */
    public function testInventoryProcess(): void
    {
        $client = static::createClient();
        $this->login($client);

        // 1. Starte Inventur
        $client->request('POST', '/inventory/start', [
            'scope' => 'full',
        ]);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $response = json_decode($client->getResponse()->getContent(), true);
        $inventoryId = $response['inventory_id'];
        $this->assertEquals('open', $response['status']);

        // 2. Prüfe Zählpositionen
        $crawler = $client->request('GET', "/inventory/{$inventoryId}/counts");
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($response['counts']);

        // 3. Zähle erste Position
        $count = $response['counts'][0];
        $client->request('POST', "/inventory/{$count['id']}", [
            'counted_quantity' => $count['expected_quantity'] + 5, // 5er Differenz
        ]);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // 4. Zähle restliche Positionen
        foreach (array_slice($response['counts'], 1) as $count) {
            $client->request('POST', "/inventory/{$count['id']}", [
                'counted_quantity' => $count['expected_quantity'],
            ]);
        }

        // 5. Beende Inventur
        $client->request('POST', "/inventory/{$inventoryId}/complete");
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // 6. Prüfe Status auf completed
        $crawler = $client->request('GET', "/inventory/{$inventoryId}/status");
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('completed', $response['status']);
    }

    private function login($client, $username = 'admin', $password = 'admin'): void
    {
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'username' => $username,
            'password' => $password,
        ]);
        $client->submit($form);
    }
}

