<?php

namespace App\Libraries;

/* * ***************************************************************************\
  +-----------------------------------------------------------------------------+
  | Project        : fluid9                                           		  |
  | FileName       : sitevariables.php                                           |
  | Version        : 1.0                                                      |
  | Developer      : subedar Yadav                                            |
  | Created On     : 15-03-2021                                               |
  | Modified On    :                                                          |
  | Modified   By  :                                                          |
  | Authorised By  :  subedar Yadav                                           |
  | Comments       :  This class used for site message		  		          |
  | Email          : subedar2507@gmail.com                                    |
  +-----------------------------------------------------------------------------+
  \**************************************************************************** */

class SiteVariables
{

    private $arrMessage = array();

    public function getVariable($key)
    {

        $this->arrMessage['accountype'] =
            array(
                'superdistributer' => 'Super Distributer',
                'distributer' => 'Distributer',
                'retailer' => 'Retailer',
                'employee' => 'Employee'
            );

        $this->arrMessage['qualification'] =
            array(
                '1' => 'Undergraduate',
                '2' => 'Graduate',
                '3' => 'Postgraduate'
            );

        $this->arrMessage['ur_ownerships'] =
            array(
                '1' => 'Owned',
                '2' => 'Rented',
                '3' => 'Parental'
            );

        $this->arrMessage['ur_residingwith'] =
            array(
                '1' => 'Family',
                '2' => 'Friends',
                '3' => 'Alone'
            );

        $this->arrMessage['ur_noofyears'] =
            array(
                '1' => 'Less than Six Months',
                '2' => 'Six Months to Two Year',
                '3' => 'Two Year to Five Year',
                '4' => 'More than Five Year'
            );

        $this->arrMessage['ue_salaryaccount'] =
            array(
                '1' => 'Direct Account Transfer',
                '2' => 'Cheque',
                '3' => 'Cash'
            );

        $this->arrMessage['ue_emptype'] =
            array(
                '1' => 'Salaried',
                '2' => 'UnEmployed',
                '3' => 'Self-Employed'
            );

        if (array_key_exists($key, $this->arrMessage)) {
            return $this->arrMessage[$key];
        }
    }
}
