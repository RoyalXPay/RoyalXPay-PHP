<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{

    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'name', 'varvalue', 'st_type'];

    public function getData($searchArray = array(), $offset = '', $limit = '', $coutOnly = '', $resinarray = '')
    {
        if ($coutOnly) {
            $sql = "SELECT COUNT($this->primaryKey) as total_count FROM $this->table AS OT ";
        } else {
            $sql = "SELECT OT.*  FROM $this->table AS OT ";
        }
        $sql .= " WHERE ";

        if (isset($searchArray['txtsearch']) && $searchArray['txtsearch']) {
            $sql .= " AND ( OT.name LIKE '%" . $searchArray['txtsearch'] . "%' ) ";
        }

        if (isset($searchArray['id']) && $searchArray['id']) {
            $sql .= " AND  OT.id =" . $searchArray['id'];
        }

        $sql .= " ORDER BY " . $this->primaryKey . " DESC";

        if ($limit) {
            $sql .= " LIMIT $offset,$limit";
        }

        $query = $this->db->query($sql);
        if ($resinarray) {
            $result = $query->getResult('array');
        } else {
            $result = $query->getResult();
        }

        if ($coutOnly) {
            return $result[0]->total_count;
        }

        return $result;
    }

    public function getvaluebyname($name)
    {

        $arrResult =  $this->asArray()
            ->where(['name' => $name])
            ->first();
        return $arrResult;
    }

    public function getNameValuepair()
    {
        $newsetting = array();
        $settings = $this->getData('', '', '', '', 1);
        foreach ($settings as $detail) {
            $newsetting[$detail['name']] = $detail['varvalue'];
        }

        return $newsetting;
    }
}