<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\Repository\System\MatheusGontijoSystemConfigHistory\Api;

use Doctrine\DBAL\Connection;
use MatheusGontijo\SystemConfigHistory\Repository\System\MatheusGontijoSystemConfigHistory\Api\MatheusGontijoSystemConfigHistoryRouteRepository;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;

class MatheusGontijoSystemConfigHistoryRouteRepositoryIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function setUp(): void
    {
        parent::setUp();
        $this->populateTableWithData();
    }

    public function testConfigurationKeyColumnFilterAndSort(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": false}',
                'configuration_value_new' => '{"_value": true}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ce8942b6a5da4d04a43f8f9c1acf8629'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": true}',
                'configuration_value_new' => '{"_value": false}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": false}',
                'configuration_value_new' => '{"_value": true}',
                'sales_channel_id' => null,
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $rows = $this->getRows(['configuration_key' => 'foo.bar.enabled'], 'configuration_key');

        static::assertCount(3, $rows);

        static::assertSame([
            'id' => 'b424c12e1a5d405988436037b5a48713',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => 'false',
            'configuration_value_new' => 'true',
            'sales_channel_name' => 'Default',
            'username' => null,
            'user_data' => null,
            'created_at' => '2022-01-03 00:00:00.000',
        ], $rows[0]);

        static::assertSame([
            'id' => 'ce8942b6a5da4d04a43f8f9c1acf8629',
            'configuration_key' => 'foo.bar.enabled2',
            'configuration_value_old' => 'true',
            'configuration_value_new' => 'false',
            'sales_channel_name' => 'English Sales Channel',
            'username' => null,
            'user_data' => null,
            'created_at' => '2022-01-02 00:00:00.000',
        ], $rows[1]);

        static::assertSame([
            'id' => 'fc162568816f4c2c8940d24d66d9c305',
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => 'false',
            'configuration_value_new' => 'true',
            'sales_channel_name' => 'German Sales Channel',
            'username' => null,
            'user_data' => null,
            'created_at' => '2022-01-01 00:00:00.000',
        ], $rows[2]);
    }

    public function testConfigurationValueOldColumnFilterAndSort(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": "mycustomvalue_789"}',
                'configuration_value_new' => '{"_value": "mycustomvalue_111"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ce8942b6a5da4d04a43f8f9c1acf8629'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": "mycustomvalue_456"}',
                'configuration_value_new' => '{"_value": "mycustomvalue_111"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": "mycustomvalue_123"}',
                'configuration_value_new' => '{"_value": "mycustomvalue_111"}',
                'sales_channel_id' => null,
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $rows = $this->getRows(['configuration_value_old' => 'mycustomvalue_'], 'configuration_value_old');

        static::assertCount(3, $rows);

        static::assertSame([
            'id' => 'b424c12e1a5d405988436037b5a48713',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => 'mycustomvalue_123',
            'configuration_value_new' => 'mycustomvalue_111',
            'sales_channel_name' => 'Default',
            'username' => null,
            'user_data' => null,
            'created_at' => '2022-01-03 00:00:00.000',
        ], $rows[0]);

        static::assertSame([
            'id' => 'ce8942b6a5da4d04a43f8f9c1acf8629',
            'configuration_key' => 'foo.bar.enabled2',
            'configuration_value_old' => 'mycustomvalue_456',
            'configuration_value_new' => 'mycustomvalue_111',
            'sales_channel_name' => 'English Sales Channel',
            'username' => null,
            'user_data' => null,
            'created_at' => '2022-01-02 00:00:00.000',
        ], $rows[1]);

        static::assertSame([
            'id' => 'fc162568816f4c2c8940d24d66d9c305',
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => 'mycustomvalue_789',
            'configuration_value_new' => 'mycustomvalue_111',
            'sales_channel_name' => 'German Sales Channel',
            'username' => null,
            'user_data' => null,
            'created_at' => '2022-01-01 00:00:00.000',
        ], $rows[2]);
    }

    private function populateTableWithData()
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('91f1885093b03215a08cddedce106c87'),
                'configuration_key' => 'solutaCorporis.praesentium.enim',
                'configuration_value_old' => '{"_value":"iusto"}',
                'configuration_value_new' => null,
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '1997-11-05 01:46:02.188',
                'updated_at' => '2004-04-15 01:15:02.219',
            ],
            [
                'id' => Uuid::fromHexToBytes('6a970f58aa4f3647a9720ea62d40f940'),
                'configuration_key' => 'minus.ad.doloreIn',
                'configuration_value_old' => '{"_value":"laborum"}',
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '1991-03-31 13:14:27.250',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b4e98aeeb51531efb308e0b0dd35403f'),
                'configuration_key' => 'ducimusEst.velitAssumenda.at',
                'configuration_value_old' => '{"_value":"doloribus"}',
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '2014-10-15 03:11:14.180',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('31a66b39840a3c15863e1a4e8f110579'),
                'configuration_key' => 'autemNumquam.nemo.velit',
                'configuration_value_old' => '{"_value":[]}',
                'configuration_value_new' => '{"_value":[31,20,82]}',
                'sales_channel_id' => null,
                'created_at' => '1986-09-15 23:11:21.175',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('2322d5a38ff1374a8aa40f81901d560e'),
                'configuration_key' => 'omnis.eos.eos',
                'configuration_value_old' => '{"_value":"excepturi"}',
                'configuration_value_new' => null,
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '1995-09-18 10:01:11.259',
                'updated_at' => '2002-09-24 18:45:50.190',
            ],
            [
                'id' => Uuid::fromHexToBytes('b1ca4b3d19593cd0b2ccf71feb04d423'),
                'configuration_key' => 'aliasDolorem.fugit.officiaSit',
                'configuration_value_old' => '{"_value":[67,173,193]}',
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '2006-03-04 22:20:34.246',
                'updated_at' => '2007-05-30 16:45:29.208',
            ],
            [
                'id' => Uuid::fromHexToBytes('16d9f138977d354bb7bdb52f966b02c3'),
                'configuration_key' => 'est.veniam.providentConsequatur',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":"molestias"}',
                'sales_channel_id' => null,
                'created_at' => '1997-12-10 01:20:18.264',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('8d3608aa8a033b159f2b8ab755a7502f'),
                'configuration_key' => 'aut.alias.commodiNisi',
                'configuration_value_old' => null,
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '2001-08-07 20:43:43.206',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('1a714535006039ff88cd20595353b8a9'),
                'configuration_key' => 'providentSuscipit.beatae.officiisVoluptas',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":true}',
                'sales_channel_id' => null,
                'created_at' => '2011-08-06 15:16:27.213',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('77d9bd61b2b834c7bb1f0b690f14790d'),
                'configuration_key' => 'sapiente.molestiasProvident.illum',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => null,
                'created_at' => '1993-07-07 09:00:42.228',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('e6bb1274e14d36b79c680f7d4dc2aea5'),
                'configuration_key' => 'perferendisUt.accusamusNeque.quo',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '2002-08-25 22:14:00.240',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('0decf9fe704e35259885b7603f62cfe0'),
                'configuration_key' => 'aut.incidunt.eum',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":563978}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '1993-05-06 03:09:57.228',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('3934071726623b0a8075c741354f9424'),
                'configuration_key' => 'adipisciSoluta.eaFugit.quos',
                'configuration_value_old' => null,
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '1988-12-03 14:24:30.230',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('268e81e12a003c93b8279d0b3c7c6fe5'),
                'configuration_key' => 'magni.rem.explicabo',
                'configuration_value_old' => '{"_value":true}',
                'configuration_value_new' => null,
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '1992-09-25 01:16:21.189',
                'updated_at' => '2009-02-03 05:10:42.173',
            ],
            [
                'id' => Uuid::fromHexToBytes('a64b3bbcce493eda980dd4382b45187d'),
                'configuration_key' => 'magni.sit.qui',
                'configuration_value_old' => '{"_value":"nulla"}',
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '1995-03-02 20:28:45.261',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('70bd0204f17a375c865edf846ce8d896'),
                'configuration_key' => 'hic.soluta.maxime',
                'configuration_value_old' => '{"_value":[]}',
                'configuration_value_new' => '{"_value":"et"}',
                'sales_channel_id' => null,
                'created_at' => '1992-06-14 13:49:24.196',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b70707d4c6d538b4812eafe343f010b3'),
                'configuration_key' => 'sit.veritatis.eum',
                'configuration_value_old' => '{"_value":"dolore"}',
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '2010-10-21 12:30:03.196',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('875c1c0885e831f7a8617241be49f5a9'),
                'configuration_key' => 'officiisOdit.sapiente.minimaQui',
                'configuration_value_old' => '{"_value":"consequatur"}',
                'configuration_value_new' => '{"_value":[120,96,193]}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '1999-04-24 00:02:10.202',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('d07cbd7297103037bf08bcb0db7d8ffd'),
                'configuration_key' => 'aspernatur.unde.rerum',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '2013-12-31 23:57:50.209',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('f2df24705ebe3bd29f122db77f6356ee'),
                'configuration_key' => 'excepturi.minus.enim',
                'configuration_value_old' => '{"_value":"explicabo"}',
                'configuration_value_new' => '{"_value":true}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2004-12-19 00:58:34.233',
                'updated_at' => '2015-01-27 22:04:13.226',
            ],
            [
                'id' => Uuid::fromHexToBytes('87c6d2cb2dae367a933957c877aff531'),
                'configuration_key' => 'ipsam.aut.velEt',
                'configuration_value_old' => '{"_value":363699}',
                'configuration_value_new' => '{"_value":"est"}',
                'sales_channel_id' => null,
                'created_at' => '1992-08-11 02:27:54.204',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('d9679865bad03f5c9672998e2df9c9f9'),
                'configuration_key' => 'quos.sed.maiores',
                'configuration_value_old' => '{"_value":"consequatur"}',
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '1999-08-10 08:56:32.266',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('bd523a3c023a32f58604adc5271ac528'),
                'configuration_key' => 'errorQui.modi.quae',
                'configuration_value_old' => '{"_value":"rerum"}',
                'configuration_value_new' => '{"_value":"sit"}',
                'sales_channel_id' => null,
                'created_at' => '1992-09-03 22:40:52.204',
                'updated_at' => '2006-08-13 15:44:04.250',
            ],
            [
                'id' => Uuid::fromHexToBytes('793b4a2eaec63554ba7cb0cac804f19c'),
                'configuration_key' => 'inventore.delectus.etDolor',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":[56,140,111]}',
                'sales_channel_id' => null,
                'created_at' => '2000-03-04 11:02:32.224',
                'updated_at' => '2008-12-28 12:03:38.181',
            ],
            [
                'id' => Uuid::fromHexToBytes('31930bc8f87034a29862dc86d052a87a'),
                'configuration_key' => 'similiqueCumque.libero.est',
                'configuration_value_old' => '{"_value":[57,173,42]}',
                'configuration_value_new' => '{"_value":"dolores"}',
                'sales_channel_id' => null,
                'created_at' => '2002-11-04 20:38:18.179',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('0283a79db7a43e5f903a6a6f503790da'),
                'configuration_key' => 'distinctioEst.laboriosamUt.cupiditateNemo',
                'configuration_value_old' => '{"_value":799693}',
                'configuration_value_new' => '{"_value":50923}',
                'sales_channel_id' => null,
                'created_at' => '2002-09-04 02:24:20.211',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('9316f707dc4335cd94209ea4bd8f06c9'),
                'configuration_key' => 'nobisPraesentium.ipsum.repellat',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":"commodi"}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2000-07-09 22:08:38.245',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ed3689166c3c3de38de89fa3ba9bf9cf'),
                'configuration_key' => 'voluptas.sequi.rem',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":610077}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2008-01-29 13:39:34.237',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('3bf493eac4e4354d8dc52b638e21adaf'),
                'configuration_key' => 'sapienteVero.voluptatemHarum.inventore',
                'configuration_value_old' => null,
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '1994-12-03 06:52:27.171',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('5b4731ec8c7a3df4964f86366422b256'),
                'configuration_key' => 'solutaNatus.repellendus.quia',
                'configuration_value_old' => '{"_value":"sapiente"}',
                'configuration_value_new' => '{"_value":"voluptatum"}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2013-05-16 03:16:33.210',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('0f410e89054d3a4d812cfdc0c7d7ab30'),
                'configuration_key' => 'accusamus.sunt.ducimus',
                'configuration_value_old' => '{"_value":"quidem"}',
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '1998-12-21 13:56:24.253',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('1037da060c053523b0417a312b4cb541'),
                'configuration_key' => 'amet.nihil.solutaRem',
                'configuration_value_old' => '{"_value":"vero"}',
                'configuration_value_new' => '{"_value":[159,138,137]}',
                'sales_channel_id' => null,
                'created_at' => '1994-04-13 11:38:27.249',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('93e5f680c72d3bee9383ac2af642d242'),
                'configuration_key' => 'vero.eos.quisPraesentium',
                'configuration_value_old' => '{"_value":[124,193,103]}',
                'configuration_value_new' => '{"_value":[199,101,149]}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2005-06-24 08:13:18.251',
                'updated_at' => '2013-05-01 17:29:26.208',
            ],
            [
                'id' => Uuid::fromHexToBytes('1fdeb55424a03295ba18b786b84a3025'),
                'configuration_key' => 'harumEnim.sit.aliquid',
                'configuration_value_old' => '{"_value":13611}',
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '2003-10-23 03:01:35.174',
                'updated_at' => '2013-07-03 11:11:48.261',
            ],
            [
                'id' => Uuid::fromHexToBytes('fd5cd1725fe935ae873b9f46037e6c18'),
                'configuration_key' => 'ducimus.dolorem.esse',
                'configuration_value_old' => '{"_value":"nesciunt"}',
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '1991-07-03 20:05:00.212',
                'updated_at' => '2012-06-25 21:00:44.188',
            ],
            [
                'id' => Uuid::fromHexToBytes('8b0e69260d4c3eccb8b560572eb4d464'),
                'configuration_key' => 'voluptatem.tempore.ratione',
                'configuration_value_old' => '{"_value":true}',
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '2010-03-22 03:25:37.219',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('2fc722c2c50a36919037737f931eeabd'),
                'configuration_key' => 'et.quiSimilique.similique',
                'configuration_value_old' => null,
                'configuration_value_new' => null,
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2007-07-12 01:14:21.179',
                'updated_at' => '2013-10-29 07:04:24.179',
            ],
            [
                'id' => Uuid::fromHexToBytes('2885f39f6e8b38f88fce2b3be18152a0'),
                'configuration_key' => 'temporaVel.consequaturCorrupti.aut',
                'configuration_value_old' => null,
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '1998-11-11 10:18:25.240',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ad66b69f4e9d3e32937dd3a738d08a73'),
                'configuration_key' => 'totam.quidem.sed',
                'configuration_value_old' => '{"_value":"dolores"}',
                'configuration_value_new' => '{"_value":"aut"}',
                'sales_channel_id' => null,
                'created_at' => '2014-07-09 14:03:03.267',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('60728a48988336d6ab8e32d0bb76939a'),
                'configuration_key' => 'eos.velit.aperiamRepudiandae',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '2015-06-22 21:55:26.184',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('79db5a3739e8300fb74a7fde900a5859'),
                'configuration_key' => 'vero.sedInventore.et',
                'configuration_value_old' => '{"_value":51700}',
                'configuration_value_new' => '{"_value":"soluta"}',
                'sales_channel_id' => null,
                'created_at' => '2001-09-24 00:19:16.212',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('3a5a3d1613aa3b3394e6d1723542c9c9'),
                'configuration_key' => 'aut.doloresIure.odio',
                'configuration_value_old' => '{"_value":"sunt"}',
                'configuration_value_new' => '{"_value":"a"}',
                'sales_channel_id' => null,
                'created_at' => '2004-06-02 02:01:03.170',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('82b4c3e7f37335389e22b2d5032d23ca'),
                'configuration_key' => 'reiciendis.modiQuia.distinctio',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":"porro"}',
                'sales_channel_id' => null,
                'created_at' => '1994-05-09 15:58:05.196',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('1e0aa04bbb813bbbb09e3b1c508cdd37'),
                'configuration_key' => 'cupiditate.adipisci.unde',
                'configuration_value_old' => '{"_value":[25,79,84]}',
                'configuration_value_new' => '{"_value":"laborum"}',
                'sales_channel_id' => null,
                'created_at' => '2005-09-16 18:01:08.220',
                'updated_at' => '2011-05-02 23:06:04.265',
            ],
            [
                'id' => Uuid::fromHexToBytes('5773f28e576332b4a4d3a825472c0a58'),
                'configuration_key' => 'repellendus.minima.voluptas',
                'configuration_value_old' => '{"_value":942112}',
                'configuration_value_new' => '{"_value":360845}',
                'sales_channel_id' => null,
                'created_at' => '2003-11-30 13:46:40.226',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('a781a815ae633c1bb429f2719c9fc451'),
                'configuration_key' => 'maiores.magnam.sitQuis',
                'configuration_value_old' => '{"_value":"dolorem"}',
                'configuration_value_new' => '{"_value":[141,70,59]}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '1998-02-26 09:40:40.230',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b114016e647138409b5650765aaec765'),
                'configuration_key' => 'dolores.enim.quia',
                'configuration_value_old' => null,
                'configuration_value_new' => null,
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2000-01-31 00:04:09.196',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('a8423aff7e8e38a0bd653b5d3cf575c7'),
                'configuration_key' => 'itaque.rerum.culpaDoloribus',
                'configuration_value_old' => '{"_value":"sed"}',
                'configuration_value_new' => '{"_value":"porro"}',
                'sales_channel_id' => null,
                'created_at' => '1987-09-15 20:11:09.176',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('c284e70c690f31229ff070638db9e4ac'),
                'configuration_key' => 'laboriosamImpedit.nesciunt.pariatur',
                'configuration_value_old' => '{"_value":[192,109,77]}',
                'configuration_value_new' => '{"_value":"natus"}',
                'sales_channel_id' => null,
                'created_at' => '1987-10-25 09:37:46.184',
                'updated_at' => '1992-11-18 11:47:33.244',
            ],
            [
                'id' => Uuid::fromHexToBytes('99b1efd20afa3b068027f1873d557507'),
                'configuration_key' => 'minusBlanditiis.delectusFugit.non',
                'configuration_value_old' => '{"_value":"ut"}',
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => null,
                'created_at' => '1993-04-11 14:22:21.213',
                'updated_at' => '2000-12-09 15:45:05.262',
            ],
            [
                'id' => Uuid::fromHexToBytes('0313b63fbe123825a1ceaecd2dbadd10'),
                'configuration_key' => 'molestiae.rerumEarum.architecto',
                'configuration_value_old' => '{"_value":"qui"}',
                'configuration_value_new' => '{"_value":"necessitatibus"}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '1989-06-07 22:30:59.204',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('d547ecba5a08309b82381b28ef3459dd'),
                'configuration_key' => 'itaque.sit.est',
                'configuration_value_old' => '{"_value":false}',
                'configuration_value_new' => '{"_value":229582}',
                'sales_channel_id' => null,
                'created_at' => '2020-04-21 10:10:03.217',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('a7b1c1fd7e1c3d7aa7d564d86e106022'),
                'configuration_key' => 'deseruntPorro.culpa.deleniti',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => null,
                'created_at' => '1995-03-07 14:12:11.183',
                'updated_at' => '2005-02-22 23:04:31.257',
            ],
            [
                'id' => Uuid::fromHexToBytes('57e3f52eae883a97a87f151970c1e031'),
                'configuration_key' => 'reprehenderit.sintVoluptatem.est',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => null,
                'created_at' => '1994-05-31 01:23:28.191',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('e6272d6652ca3e96965395b89efe6e9b'),
                'configuration_key' => 'sitAspernatur.voluptatibusAliquid.labore',
                'configuration_value_old' => '{"_value":"voluptatem"}',
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '2006-08-02 17:59:27.172',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('330d9a439c2236069514c201f952ed32'),
                'configuration_key' => 'quia.delectusDolorem.eum',
                'configuration_value_old' => '{"_value":true}',
                'configuration_value_new' => '{"_value":"ex"}',
                'sales_channel_id' => null,
                'created_at' => '1986-06-29 21:34:18.205',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('51382ee15389352da7efe01460e24e22'),
                'configuration_key' => 'distinctio.assumendaDignissimos.suscipit',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":[102,87,145]}',
                'sales_channel_id' => null,
                'created_at' => '1991-10-15 12:37:49.204',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('450915e5bbba3b99b5dade3c29d71f7d'),
                'configuration_key' => 'ab.quas.sintError',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":"totam"}',
                'sales_channel_id' => null,
                'created_at' => '1994-03-08 15:44:50.201',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('0f54c0383da43b70afe03d9a1031100c'),
                'configuration_key' => 'architecto.veroEt.qui',
                'configuration_value_old' => '{"_value":[]}',
                'configuration_value_new' => null,
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '2012-12-07 09:01:04.191',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('4ab2fd36258d35279f7a4b3365a38260'),
                'configuration_key' => 'voluptatem.temporibusEa.aQuis',
                'configuration_value_old' => '{"_value":"saepe"}',
                'configuration_value_new' => '{"_value":true}',
                'sales_channel_id' => null,
                'created_at' => '1994-06-05 11:20:43.238',
                'updated_at' => '2003-06-01 00:42:35.188',
            ],
            [
                'id' => Uuid::fromHexToBytes('978a2974c2773a1e935bd841440d943a'),
                'configuration_key' => 'nihilEt.fuga.reprehenderit',
                'configuration_value_old' => '{"_value":438713}',
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2009-11-19 15:39:35.188',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('8e3244fbf7813a529430f79826a5f1f1'),
                'configuration_key' => 'excepturi.nullaBlanditiis.molestiaeIste',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":337866}',
                'sales_channel_id' => null,
                'created_at' => '1989-03-03 08:58:11.260',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('3702953634c533f3baddc8ec9eb5e707'),
                'configuration_key' => 'doloremReprehenderit.minus.vero',
                'configuration_value_old' => '{"_value":"dolor"}',
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '1990-02-22 04:29:31.232',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('f56bc79ff2ec39458f5313660b8b8fbf'),
                'configuration_key' => 'atqueVoluptatem.est.a',
                'configuration_value_old' => '{"_value":"illo"}',
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => null,
                'created_at' => '2004-08-09 06:35:33.202',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('7cadd8644d01341a88cd39a99ba9b5d7'),
                'configuration_key' => 'hic.laboriosamHarum.autem',
                'configuration_value_old' => '{"_value":[]}',
                'configuration_value_new' => '{"_value":"sit"}',
                'sales_channel_id' => null,
                'created_at' => '2006-03-04 17:43:41.183',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('5bbe8e050a6e3782a47ea643ce46252d'),
                'configuration_key' => 'dolor.expedita.quibusdam',
                'configuration_value_old' => '{"_value":"veritatis"}',
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => null,
                'created_at' => '1993-12-12 19:13:33.186',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b75c1700f5d23221ba52646982d92b9a'),
                'configuration_key' => 'dicta.autConsequatur.eveniet',
                'configuration_value_old' => '{"_value":"enim"}',
                'configuration_value_new' => '{"_value":true}',
                'sales_channel_id' => null,
                'created_at' => '2009-03-07 18:52:10.176',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('4710cf8f30323cf1b36a85e41992fb66'),
                'configuration_key' => 'oditEst.voluptatem.inEa',
                'configuration_value_old' => '{"_value":[]}',
                'configuration_value_new' => '{"_value":"quia"}',
                'sales_channel_id' => null,
                'created_at' => '1992-07-01 10:49:23.211',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ef2f3945c9d6345286845dde4b6fad49'),
                'configuration_key' => 'excepturiFacere.qui.magni',
                'configuration_value_old' => '{"_value":false}',
                'configuration_value_new' => null,
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '1989-09-14 21:44:18.240',
                'updated_at' => '1996-08-29 14:25:46.211',
            ],
            [
                'id' => Uuid::fromHexToBytes('24b11772d90737d5bf95779c6a65d037'),
                'configuration_key' => 'libero.eos.atqueIn',
                'configuration_value_old' => '{"_value":[70,191,238]}',
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => null,
                'created_at' => '2000-11-30 18:35:10.210',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('8e3aee9b20373b639a32a1f3d2ea881a'),
                'configuration_key' => 'sapienteAccusantium.autem.magnam',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":"ad"}',
                'sales_channel_id' => null,
                'created_at' => '1991-03-14 23:03:09.217',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('585aa3645b933b7aa7dbd565cf546e81'),
                'configuration_key' => 'assumendaPossimus.enim.laborumNam',
                'configuration_value_old' => '{"_value":"debitis"}',
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '2005-04-18 20:07:20.227',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('d6b42b5b925a36fa81fcf1aac354d844'),
                'configuration_key' => 'quosLaudantium.inventore.incidunt',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => null,
                'created_at' => '1985-11-25 15:38:01.244',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('5aeee35ebd973ec282ea71dad6334468'),
                'configuration_key' => 'porro.odit.eveniet',
                'configuration_value_old' => '{"_value":362545}',
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '1998-07-03 23:09:57.212',
                'updated_at' => '2014-07-12 00:23:16.232',
            ],
            [
                'id' => Uuid::fromHexToBytes('5e4e8a0a4b4938179fb81ee3b49133e9'),
                'configuration_key' => 'minima.eumMagni.incidunt',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":"modi"}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2000-11-10 22:54:13.265',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('c8d30f2477243f06b4439c0345afca28'),
                'configuration_key' => 'tempora.voluptas.sit',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":"tempora"}',
                'sales_channel_id' => null,
                'created_at' => '2003-01-13 12:08:07.245',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('9f214f4936a836e495607a25ff5afb21'),
                'configuration_key' => 'voluptatemAssumenda.ad.eos',
                'configuration_value_old' => '{"_value":"aperiam"}',
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '2019-05-23 08:52:51.187',
                'updated_at' => '2020-06-01 06:10:36.184',
            ],
            [
                'id' => Uuid::fromHexToBytes('128fb2a539cd36fb9ea6d65983146ee8'),
                'configuration_key' => 'sint.eum.dolores',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '2011-12-14 19:48:06.180',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('dcb3c0d358e63a8d9840b22a35aaecaf'),
                'configuration_key' => 'velVoluptatem.officia.aliquid',
                'configuration_value_old' => '{"_value":"numquam"}',
                'configuration_value_new' => '{"_value":419444}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '1998-11-15 14:42:46.242',
                'updated_at' => '2011-04-02 11:18:22.239',
            ],
            [
                'id' => Uuid::fromHexToBytes('83308ce1e0cb32909cca3d51950e8af0'),
                'configuration_key' => 'esseEa.sapiente.ea',
                'configuration_value_old' => '{"_value":"dolorum"}',
                'configuration_value_new' => '{"_value":"soluta"}',
                'sales_channel_id' => null,
                'created_at' => '2005-11-26 10:48:19.259',
                'updated_at' => '2013-01-06 11:20:56.231',
            ],
            [
                'id' => Uuid::fromHexToBytes('c44f31be86e8376d890a88b6ed9e958b'),
                'configuration_key' => 'voluptatemInventore.idSit.ipsaUt',
                'configuration_value_old' => '{"_value":true}',
                'configuration_value_new' => '{"_value":"ab"}',
                'sales_channel_id' => null,
                'created_at' => '1992-07-21 18:33:40.197',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('fc7b800352cb326bbfe05842e13315d2'),
                'configuration_key' => 'numquam.amet.sit',
                'configuration_value_old' => '{"_value":"ut"}',
                'configuration_value_new' => null,
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '1999-01-04 10:44:44.215',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('5a7d793bfd9a39bca818f36aa2f18bd9'),
                'configuration_key' => 'vitae.velit.id',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":"veritatis"}',
                'sales_channel_id' => null,
                'created_at' => '2012-11-05 19:38:05.200',
                'updated_at' => '2015-04-30 10:45:51.232',
            ],
            [
                'id' => Uuid::fromHexToBytes('67b367f165c43e4ba12960277849dbe7'),
                'configuration_key' => 'soluta.incidunt.dolores',
                'configuration_value_old' => '{"_value":"quisquam"}',
                'configuration_value_new' => '{"_value":[138,85,180]}',
                'sales_channel_id' => null,
                'created_at' => '1992-07-05 19:52:38.239',
                'updated_at' => '2012-01-22 11:44:22.244',
            ],
            [
                'id' => Uuid::fromHexToBytes('140c6cdf33543183b9d662646a00df32'),
                'configuration_key' => 'vero.est.aliquid',
                'configuration_value_old' => '{"_value":"impedit"}',
                'configuration_value_new' => '{"_value":"qui"}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2011-05-13 06:43:43.213',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('9325219340e233c99f64750fa21f6f75'),
                'configuration_key' => 'velitEsse.excepturiQuod.officia',
                'configuration_value_old' => '{"_value":true}',
                'configuration_value_new' => '{"_value":"non"}',
                'sales_channel_id' => null,
                'created_at' => '1995-11-18 09:07:55.228',
                'updated_at' => '2013-10-14 16:08:08.192',
            ],
            [
                'id' => Uuid::fromHexToBytes('fba37de9d899312489586d8ee57eeb4b'),
                'configuration_key' => 'aperiam.in.voluptas',
                'configuration_value_old' => '{"_value":"ut"}',
                'configuration_value_new' => '{"_value":"iusto"}',
                'sales_channel_id' => null,
                'created_at' => '1997-03-26 03:26:18.244',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ffa71f591cdb3166828593011577acf1'),
                'configuration_key' => 'est.at.dolores',
                'configuration_value_old' => '{"_value":[196,44,166]}',
                'configuration_value_new' => '{"_value":682668}',
                'sales_channel_id' => null,
                'created_at' => '2006-11-11 10:10:33.195',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('291f62a9cac938f9a372d670c0985a3c'),
                'configuration_key' => 'quos.sapiente.occaecati',
                'configuration_value_old' => '{"_value":[]}',
                'configuration_value_new' => '{"_value":"tenetur"}',
                'sales_channel_id' => null,
                'created_at' => '2009-04-06 22:42:01.253',
                'updated_at' => '2020-06-20 19:01:05.240',
            ],
            [
                'id' => Uuid::fromHexToBytes('e02c68436ac23bdb954207227d1df9fa'),
                'configuration_key' => 'possimus.non.autemA',
                'configuration_value_old' => '{"_value":[]}',
                'configuration_value_new' => '{"_value":"sed"}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '1997-10-19 22:01:05.177',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('de792107171f3d37ab50f9f20969b01d'),
                'configuration_key' => 'nihil.quiAd.quiaAccusamus',
                'configuration_value_old' => null,
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '2018-05-05 20:20:47.261',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('6c45841b7b37312eb5f77e418780f555'),
                'configuration_key' => 'perferendis.velTempora.inPariatur',
                'configuration_value_old' => '{"_value":"ut"}',
                'configuration_value_new' => '{"_value":"unde"}',
                'sales_channel_id' => null,
                'created_at' => '2016-12-20 19:36:24.202',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('9e4917359fca3f99bfbc54406cbe7c8a'),
                'configuration_key' => 'atqueDoloribus.quas.dolorem',
                'configuration_value_old' => '{"_value":[]}',
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => null,
                'created_at' => '1997-05-18 15:20:36.245',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('91954d95f13a3efa9b3539cd87c060bc'),
                'configuration_key' => 'aliquamRepellendus.eum.sint',
                'configuration_value_old' => '{"_value":[207,209,147]}',
                'configuration_value_new' => '{"_value":true}',
                'sales_channel_id' => null,
                'created_at' => '2005-11-05 12:05:51.227',
                'updated_at' => '2009-07-08 07:38:59.253',
            ],
            [
                'id' => Uuid::fromHexToBytes('d495891ec2333ab39dd933bdb470ea19'),
                'configuration_key' => 'delectus.eum.est',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":false}',
                'sales_channel_id' => null,
                'created_at' => '1987-11-15 08:29:07.200',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('e960b203dc2a3e208ad09955e35f986a'),
                'configuration_key' => 'iste.magni.aspernaturQuia',
                'configuration_value_old' => '{"_value":833611}',
                'configuration_value_new' => null,
                'sales_channel_id' => null,
                'created_at' => '2002-04-12 03:21:31.252',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b2d9945edd6138a48b2c12bb049ce299'),
                'configuration_key' => 'repellendus.ratione.minus',
                'configuration_value_old' => '{"_value":477027}',
                'configuration_value_new' => '{"_value":[]}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2011-01-17 12:53:35.218',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('7aaadb7c8bc238428d31b414c43d3250'),
                'configuration_key' => 'voluptatem.rerumPerspiciatis.accusamusNostrum',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":"aliquid"}',
                'sales_channel_id' => Uuid::fromHexToBytes('d235f6b8ff854574bc4ef7ee5369b6e6'),
                'created_at' => '2004-06-30 21:19:08.215',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('9e32fa48312f3eeaaf0111f3698090a5'),
                'configuration_key' => 'explicabo.fugitHarum.saepeIpsa',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":"nesciunt"}',
                'sales_channel_id' => Uuid::fromHexToBytes('3401944de62d41ffb1f686c8ada7870e'),
                'created_at' => '2010-11-19 20:48:55.229',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('46fb9a1489143504b4fd76f8e718ec62'),
                'configuration_key' => 'errorIure.voluptatem.quidem',
                'configuration_value_old' => '{"_value":"laborum"}',
                'configuration_value_new' => '{"_value":594570}',
                'sales_channel_id' => null,
                'created_at' => '2013-08-10 02:38:11.249',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('fcfdec4bd66c3784b821d89a4e331fb9'),
                'configuration_key' => 'aut.quisquamQuae.exRepellendus',
                'configuration_value_old' => '{"_value":"natus"}',
                'configuration_value_new' => '{"_value":"maiores"}',
                'sales_channel_id' => null,
                'created_at' => '2005-03-31 18:15:10.174',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }
    }

    private function getRows(array $filters, string $sortBy): array
    {
        $matheusGontijoSystemConfigHistoryRouteRepository = $this->getContainer()->get(
            MatheusGontijoSystemConfigHistoryRouteRepository::class
        );

        \assert($matheusGontijoSystemConfigHistoryRouteRepository instanceof MatheusGontijoSystemConfigHistoryRouteRepository);

        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $qb = $connection->createQueryBuilder();
        $qb->select(['id']);
        $qb->from('locale');
        $qb->where('code = \'en-GB\'');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();

        $defaultEnGbLocaleIdBin = $executeResult->fetchOne();

        $defaultEnGbLocaleId = Uuid::fromBytesToHex($defaultEnGbLocaleIdBin);

        $defaultFilters = [
            'configuration_key' => null,
            'configuration_value_old' => null,
            'configuration_value_new' => null,
            'username' => null,
            'sales_channel_name' => null,
        ];

        $filtersKeys = array_keys($filters);

        foreach ($defaultFilters as $defaultFilterKey => $defaultFilter) {
            if (in_array($defaultFilterKey, $filtersKeys, true)) {
                continue;
            }

            $filters[$defaultFilterKey] = $defaultFilter;
        }

        return $matheusGontijoSystemConfigHistoryRouteRepository->getRows(
            $defaultEnGbLocaleId,
            'Default',
            $filters,
            $sortBy,
            'ASC',
            1,
            100
        );
    }
}
