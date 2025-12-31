<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'user_id';
    protected $protectFields    = true;
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['username','otp','otp_expire_time', 'name', 'email', 'phone', 'alt_mobile_number', 'password', 'gender', 'otp', 'status', 'address', 'city', 'pincode', 'user_type', 'department', 'designation', 'notes', 'wallet','permissions','module_access', 'created_at', 'updated_at'];

    public function authenticateUser($username, $password)
    {
        // Initialize response as false
        $authStatus = false;

        // Retrieve user record based on username
        $userRecord = $this->where("email", $username)->first();

        // Check if user record exists
        if ($userRecord) {
            // Verify password
            if (password_verify($password, $userRecord["password"])) {
                // Check if user's status is active
                if ($userRecord["status"] === "active") {
                    // Prepare session data
                    $sessionData = [
                        "user_id"    => $userRecord["user_id"],
                        "name"       => $userRecord["name"],
                        "email"      => $userRecord["email"],
                        "phone"      => $userRecord["phone"],
                        "status"     => $userRecord["status"],
                        "user_type"  => $userRecord["user_type"],
                        "permissions"  => json_decode($userRecord['permissions'], true),
                        "logged_in"  => true,
                    ];

                    // Set session data
                    $session = \Config\Services::session();
                    $session->set($sessionData);

                    // Return true indicating successful login
                    $authStatus = true;
                } else {
                    // Return 'inactive' indicating inactive user
                    $authStatus = 'inactive';
                }
            } else {
                // Return 'invalid_password' indicating invalid password
                $authStatus = 'invalid_password';
            }
        } else {
            // Return 'user_not_found' indicating user not found
            $authStatus = 'user_not_found';
        }

        return $authStatus;
    }

    public function getUsersDetailsold($searchArray = [],$user_type='', $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table($this->table . ' as t');

        // Join with the address table
        $builder->join('address as a', 'a.user_id = t.user_id', 'left');

        // Select the necessary columns
        if ($countOnly) {
            $builder->select("COUNT(t.{$this->primaryKey}) as total_count");
        } else {
            // Select user fields and address fields
            $builder->select('t.*, a.address, a.city, a.state, a.postal_code, a.country, a.address_type');
        }

        // Exclude user users
        $builder->where('t.user_type', $user_type);

        // Add search filters
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('t.username', $searchTerm)
                ->orLike('t.name', $searchTerm)
                ->orLike('t.email', $searchTerm)
                ->orLike('t.phone', $searchTerm)
                ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy("t.{$this->primaryKey}", 'DESC');

        // Limit the results if limit and offset are provided
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query
        $query = $builder->get();

        // Return the count or the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }

    public function getUsersDetails($searchArray = [], $user_type = '', $offset = '', $limit = '', $countOnly = '')
{
    // Initialize the Query Builder
    $builder = $this->db->table($this->table . ' as t');

    // Join with the address table
    $builder->join('address as a', 'a.user_id = t.user_id', 'left');

    // Select the necessary columns
    if ($countOnly) {
        $builder->select("COUNT(t.{$this->primaryKey}) as total_count");
    } else {
        $builder->select('t.*, a.address, a.city, a.state, a.postal_code, a.country, a.address_type');
    }

    // Filter by user type
    $builder->where('t.user_type', $user_type);

    // Filter: Name, email, phone, etc.
    if (!empty($searchArray['txtsearch'])) {
        $searchTerm = $searchArray['txtsearch'];
        $builder->groupStart()
            ->like('t.username', $searchTerm)
            ->orLike('t.name', $searchTerm)
            ->orLike('t.email', $searchTerm)
            ->orLike('t.phone', $searchTerm)
            ->groupEnd();
    }

    // ✅ Filter: Start Date
    if (!empty($searchArray['startDate'])) {
        $builder->where('t.created_at >=', $searchArray['startDate'] . ' 00:00:00');
    }

    // ✅ Filter: End Date
    if (!empty($searchArray['endDate'])) {
        $builder->where('t.created_at <=', $searchArray['endDate'] . ' 23:59:59');
    }

    // ✅ Filter: Wallet Amount
    if (!empty($searchArray['amount'])) {
        $builder->where('t.wallet >=', floatval($searchArray['amount']));
    }

    // Order by user_id descending
    $builder->orderBy("t.{$this->primaryKey}", 'DESC');

    // Apply limit & offset
    if (!empty($limit) && $offset !== '') {
        $builder->limit($limit, $offset);
    }

    $query = $builder->get();

    // Return result
    if ($countOnly) {
        return $query->getRow()->total_count;
    }

    return $query->getResult();
}


    public function getEmployeeDetails($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table($this->table . ' as t');

        // Join with the address table
        $builder->join('address as a', 'a.user_id = t.user_id', 'left');

        // Select the necessary columns
        if ($countOnly) {
            $builder->select("COUNT(t.{$this->primaryKey}) as total_count");
        } else {
            // Select user fields and address fields
            $builder->select('t.*, a.address, a.city, a.state, a.postal_code, a.country, a.address_type');
        }

        // Exclude superadmin users
        $builder->where('t.user_type', 'employee');

        // Add search filters
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('t.username', $searchTerm)
                ->orLike('t.name', $searchTerm)
                ->orLike('t.email', $searchTerm)
                ->orLike('t.phone', $searchTerm)
                ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy("t.{$this->primaryKey}", 'DESC');

        // Limit the results if limit and offset are provided
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query
        $query = $builder->get();

        // Return the count or the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }
}
