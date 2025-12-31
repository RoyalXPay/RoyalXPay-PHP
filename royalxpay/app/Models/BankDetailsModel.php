<?php

namespace App\Models;

use CodeIgniter\Model;

class BankDetailsModel extends Model
{

    protected $table = 'bank';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'bank_id', 'bank_name', 'created'];

    public function getData($searchArray = array(), $offset = '', $limit = '', $coutOnly = '')
    {

        if ($coutOnly) {

            $sql = "SELECT COUNT($this->primaryKey) as total_count FROM $this->table AS ad ";

           
        } else {
            $sql = "SELECT ad.* FROM $this->table AS ad ";
        }
        $sql .= " ";

        $sql .= " WHERE 1 ";

        if (isset($searchArray['txtsearch']) && $searchArray['txtsearch']) {

            // $sql .= " AND ( ad.bank_id LIKE '".$searchArray['txtsearch']."%' ";

            $sql .= " AND ( ad.bank_id LIKE '%" . $searchArray['txtsearch'] . "' ";
            $sql .= " OR ad.bank_name LIKE '" . $searchArray['txtsearch'] . "%' ";
            $sql .= " ) ";
            // print_r($sql);
            // exit;
            // $sql .= " OR ad.admin_type LIKE '".$searchArray['txtsearch']."%' )";

        }


        if ((isset($searchArray['startDate'])) && (isset($searchArray['endDate']))) {
            $sql .= " AND ( DATE_FORMAT(ad.created, '%Y-%m-%d') >= '" . $searchArray['startDate'] . "' ";
            $sql .= " AND DATE_FORMAT(ad.created, '%Y-%m-%d') <= '" . $searchArray['endDate'] . "' ) ";
        }

        $sql .= " ORDER BY ad." . $this->primaryKey . " ASC";

        // $sql .= " AND  ad.bank_name != 'bank' ";
        // $sql .= " ORDER BY ".$this->primaryKey." ASC";

        if ($limit) {
            $sql .= " LIMIT $offset,$limit";
        }

        $query = $this->db->query($sql);
        $result = $query->getResult();

        if ($coutOnly) {
            return $result[0]->total_count;
        }
        return $result;
    }

    // public function getSubAdmindetail($id)
    // {
    //     $arrResult =  $this->asArray()
    //                 ->where(['id' => $id])
    //                 ->first();

    //     return $arrResult;
    // }

    // public function getchilds($userid)
    // {

    //         $sql = "SELECT ad.id FROM $this->table AS ad ";

    //         $sql .= " ";
    //         $sql .= " WHERE 1 ";

    //         $sql .= " AND  ad.created_by =".$userid;
    //         $sql .= " AND  ad.admin_type = 'salesexecutive' ";

    //         $query = $this->db->query($sql);
    //         $result = $query->getResult('array');
    //     return  $result ;
    // }

}
