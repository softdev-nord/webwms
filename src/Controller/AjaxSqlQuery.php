<?php

namespace WebWMS\Controller;

use WebWMS\Controller\Requirements as Requirements;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\DBAL\Connection;

class AjaxSqlQuery extends AbstractController
{
    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    // Abfrage aller Artikel
    public function getArticle()
    {
        $numOfBoxArt = ! empty($_GET['numOfBoxArt']) ? $_GET['numOfBoxArt'] : '';
        $name = ! empty($_GET['art_nr']) ? strtolower(trim($_GET['art_nr'])) : '';

        $boxName = 'art_nr';

        switch ($numOfBoxArt) {
            case 1:
                $boxName = 'art_name';
                break;
            case 2:
                $boxName = 'art_id';
                break;
            case 3:
                $boxName = 'art_ean';
                break;
            case 4:
                $boxName = 'art_kat';
                break;
        }

        $data = [];
        if ( ! empty($_GET['name_art']))
        {
            $name = strtolower(trim($_GET['name_art']));

            $sql = "SELECT art_nr, art_name, art_id, art_ean, art_kat FROM artikel where LOWER($boxName) LIKE '" . $name . "%'";
            $this->connection->query($sql);
            while ($row = $this->connection->fetchAssoc($sql))
            {
                $name = $row['art_nr'] . '|' . $row['art_name'] . '|' . $row['art_id'] . '|' . $row['art_ean'] . '|' . $row['art_kat'];
                array_push($data, $name);
            }
        }
        return json_encode($data);
    }


    // Abfrage aller Aufträge
    public function getAllAftQuery()
    {
        $data = [];

        $sql = "SELECT aft.aft_id, aft.aft_nr, aft.aft_ref, kd.kd_nr, kd.kd_name, aft.aft_aft_dat, 
				aft.aft_bst_dat, ben.ben_log_name
				FROM wa_auftrag AS aft
					INNER JOIN wa_aft_pos AS pos
				ON pos.aft_id = aft.aft_id
					INNER JOIN kunde AS kd
				ON kd.kd_id = aft.kd_id
					INNER JOIN benutzer AS ben 
				ON aft.ben_id = ben.ben_id
				GROUP BY pos.aft_id;";

        $this->connection->query($sql);

        while ($row = $this->connection->fetchAssoc($sql))
        {
            $name =
                [
                    'aft_nr'			=>		$row['aft_nr'],
                    'aft_ref'			=>		$row['aft_ref'],
                    'kd_nr'				=>		$row['kd_nr'],
                    'kd_name'			=>		$row['kd_name'],
                    'aft_aft_dat'		=>		$row['aft_aft_dat'],
                    'aft_bst_dat'		=>		$row['aft_bst_dat'],
                    'ben_log_name'		=>		$row['ben_log_name'],
                    'aft_id'			=>		$row['aft_id'],
                ];
            array_push($data, $name);
        }
        return json_encode($data);

    }

    // Abfrage aller Auftragspositionen
    public function getAllAftPosQuery()
    {
        $data = [];

        $sql = "SELECT pos.aft_id, aft.aft_nr, art.art_nr, art.art_name, pos.aft_pos_menge, lbw.lbw_menge
				FROM wa_aft_pos AS pos
					INNER JOIN wa_auftrag AS aft
				ON pos.aft_id = aft.aft_id
					INNER JOIN artikel AS art 
				ON pos.art_id = art.art_id
					LEFT OUTER JOIN lagerbewegung lbw 
				ON pos.aft_pos_id = lbw.aft_pos_id
				ORDER BY  pos.aft_pos_id;";

        $this->connection->query($sql);

        while ($row = $this->connection->fetchAssoc($sql))
        {
            $name =
                [
                    'aft_nr'			=>		$row['aft_nr'],
                    'art_nr'			=>		$row['art_nr'],
                    'art_name'			=>		$row['art_name'],
                    'aft_pos_menge'		=>		$row['aft_pos_menge'],
                    'lbw_menge'			=>		$row['lbw_menge'],
                    'aft_id'			=>		$row['aft_id'],
                ];
            array_push($data, $name);
        }
        return json_encode($data);
    }

    // Abfrage aller Bestellungen
    public function getAllBstQuery()
    {
        $data = [];

        $sql = "SELECT bst.bst_id, bst.bst_nr, bst.bst_ref, lief.lief_nr, lief.lief_name, 
				bst.bst_bst_dat, ben.ben_log_name
				FROM we_bestellung AS bst
					INNER JOIN we_bst_pos AS pos
				ON pos.bst_id = bst.bst_id
					INNER JOIN lieferant AS lief
				ON lief.lief_id = bst.lief_id
					INNER JOIN benutzer AS ben 
				ON bst.ben_id = ben.ben_id
				GROUP BY pos.bst_id;";

        $this->connection->query($sql);

        while ($row = $this->connection->fetchAssoc($sql))
        {
            $name =
                [   'bst_nr'			=>		$row['bst_nr'],
                    'bst_ref'			=>		$row['bst_ref'],
                    'lief_nr'			=>		$row['lief_nr'],
                    'lief_name'			=>		$row['lief_name'],
                    'bst_bst_dat'		=>		$row['bst_bst_dat'],
                    'ben_log_name'		=>		$row['ben_log_name'],
                    'bst_id'			=>		$row['bst_id'],
                ];
            array_push($data, $name);
        }
        return json_encode($data);
    }

    // Abfrage aller Bestellungspositionen
    public function getAllBstPosQuery()
    {
        $data = [];

        $sql = "SELECT pos.bst_id, bst.bst_nr, art.art_nr, art.art_name, pos.bst_pos_menge, SUM(lbw.lbw_menge)
				FROM we_bst_pos AS pos
					INNER JOIN we_bestellung AS bst
				ON pos.bst_id = bst.bst_id
					INNER JOIN artikel AS art 
				ON pos.art_id = art.art_id
					LEFT OUTER JOIN lagerbewegung lbw 
				ON pos.bst_pos_id = lbw.bst_pos_id
				GROUP BY pos.bst_pos_id ORDER BY  pos.bst_pos_id;";

        $this->connection->query($sql);

        while ($row = $this->connection->fetchAssoc($sql))
        {
            $name =
                [
                    'bst_nr'			=>		$row['bst_nr'],
                    'art_nr'			=>		$row['art_nr'],
                    'art_name'			=>		$row['art_name'],
                    'bst_pos_menge'		=>		$row['bst_pos_menge'],
                    'lbw_menge'			=>		$row['SUM(lbw.lbw_menge)'],
                    'bst_id'			=>		$row['bst_id'],
                ];
            array_push($data, $name);
        }
        return json_encode($data);
    }

    // Abfrage aller Kunden
    public function getCustomers()
    {
        $numOfBoxKd = !empty($_GET['numOfBoxKd']) ? $_GET['numOfBoxKd'] : '';
        $nameKd = !empty($_GET['kd_nr']) ? strtolower(trim($_GET['kd_nr'])) : '';

        $boxName = 'kd_nr';

        switch ($numOfBoxKd)
        {
            case 1:
                $boxName = 'kd_name';
                break;
            case 2:
                $boxName = 'kd_ans_zu';
                break;
            case 3:
                $boxName = 'kd_str';
                break;
            case 4:
                $boxName = 'kd_hnr';
                break;
            case 5:
                $boxName = 'kd_land_krz';
                break;
            case 6:
                $boxName = 'kd_plz';
                break;
            case 7:
                $boxName = 'kd_ort';
                break;
            case 8:
                $boxName = 'kd_id';
                break;
        }

        $data = array();
        if (!empty($_GET['name_kd']))
        {
            $nameKd = strtolower(trim($_GET['name_kd']));
            //$this->db->connectDB->set_charset("utf8mb4");
            $sqlKd = "SELECT kd_nr, kd_name, kd_ans_zu, kd_str, kd_hnr, kd_land_krz, kd_plz, kd_ort, kd_id FROM kunde where LOWER($boxName) LIKE '" . $nameKd . "%'";
            $resultKd = $this->connection->query($sqlKd);
            while ($rowKd = $this->connection->fetchAssoc($resultKd))
            {
                $nameKd = $rowKd['kd_nr'] . '|'. $rowKd['kd_name'] . '|' . $rowKd['kd_ans_zu'] . '|' . $rowKd['kd_str'] . '|' . $rowKd['kd_hnr'] . '|' . $rowKd['kd_land_krz'] . '|' . $rowKd['kd_plz'] . '|' . $rowKd['kd_ort'] . '|' . $rowKd['kd_id'];
                array_push($data, $nameKd);
            }
        }
        return json_encode($data);

    }

    // Abfrage aller Lieferanten
    public function getSuppliers()
    {
        $numOfBoxLief = !empty($_GET['numOfBoxLief']) ? $_GET['numOfBoxLief'] : '';
        $nameLief = !empty($_GET['lief_nr']) ? strtolower(trim($_GET['lief_nr'])) : '';

        $boxName = 'lief_nr';

        switch ($numOfBoxLief)
        {
            case 1:
                $boxName = 'lief_name';
                break;
            case 2:
                $boxName = 'lief_ans_zu';
                break;
            case 3:
                $boxName = 'lief_str';
                break;
            case 4:
                $boxName = 'lief_hnr';
                break;
            case 5:
                $boxName = 'lief_land_krz';
                break;
            case 6:
                $boxName = 'lief_plz';
                break;
            case 7:
                $boxName = 'lief_ort';
                break;
            case 8:
                $boxName = 'lief_id';
                break;
        }

        $data = array();
        if (!empty($_GET['name_lief']))
        {
            $nameLief = strtolower(trim($_GET['name_lief']));
            //$this->db->connectDB->set_charset("utf8mb4");
            $sqlLief = "SELECT lief_nr, lief_name, lief_ans_zu, lief_str, lief_hnr, lief_land_krz, lief_plz, lief_ort, lief_id FROM lieferant where LOWER($boxName) LIKE '" . $nameLief . "%'";
            $resultLief = $this->connection->query($sqlLief);
            while ($rowLief = $this->connection->fetchAssoc($resultLief))
            {
                $nameLief = $rowLief['lief_nr'] . '|'. $rowLief['lief_name'] . '|' . $rowLief['lief_ans_zu'] . '|' . $rowLief['lief_str'] . '|' . $rowLief['lief_hnr'] . '|' . $rowLief['lief_land_krz'] . '|' . $rowLief['lief_plz'] . '|' . $rowLief['lief_ort'] . '|' . $rowLief['lief_id'];
                array_push($data, $nameLief);
            }
        }
        return json_encode($data);
    }

    public function index()
    {
        return $this->render('order/index.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Übersicht Bestellungen',
        ]);
    }
}