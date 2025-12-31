<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractModel extends Model
{
    protected $table         = 'contracts';
    protected $primaryKey    = 'contract_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['user_id', 'contract_code', 'contract_type', 'total_amount', 'vat', 'discount', 'delivery_amount', 'final_payable_amount', 'start_date', 'end_date', 'contract_date', 'citylight_logo', 'citylight_address', 'contact_details', 'citylight_email', 'citylight_website', 'bank_details', 'delivery_time', 'stamp', 'signature', 'return_policies', 'terms_conditions', 'status', 'notes', 'created_at', 'updated_at'];

    /**
     * Retrieves available contracts with their associated products.
     *
     * @param array $searchArray The search filters (optional)
     * @param string $offset The offset for pagination (optional)
     * @param string $limit The limit for pagination (optional)
     * @param string $countOnly If true, returns only the count of records, otherwise returns the full record set (optional)
     * @return array|int Returns either the list of contracts with products or count of records
     */
    public function getRentContractDetails(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        $builder = $this->db->table($this->table);

        if ($countOnly) {
            $builder->select("COUNT(*) as total_count");
        } else {
            $builder->select('contracts.*, users.name as customer_name');
        }

        $builder->join('users', 'users.user_id = contracts.user_id', 'left');

        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('contracts.contract_code', $searchTerm)
                ->orLike('users.name', $searchTerm)
                ->groupEnd();
        }

        $builder->where('contracts.contract_type', 'rent');
        $builder->orderBy("contracts.contract_id", 'DESC');

        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        $builder->groupBy('contracts.contract_id');
        $query = $builder->get();

        if ($countOnly) {
            $row = $query->getRow();
            if ($row) {
                return $row->total_count;
            } else {
                return 0;
            }
        }

        return $query->getResult();
    }

    // Retrieve contract details for sale contracts
    public function getSaleContractDetails(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        $builder = $this->db->table($this->table);

        // Count query or select fields
        if ($countOnly) {
            $builder->select("COUNT(*) as total_count");
        } else {
            $builder->select('contracts.*, users.name as customer_name');
        }

        // Join users table
        $builder->join('users', 'users.user_id = contracts.user_id', 'left');

        // Apply search filter
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('contracts.contract_code', $searchTerm)
                ->orLike('users.name', $searchTerm)
                ->groupEnd();
        }

        // Filter for sale contracts only
        $builder->where('contracts.contract_type', 'sale');

        // Order by contract ID descending
        $builder->orderBy("contracts.contract_id", 'DESC');

        // Apply limit and offset if provided
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query
        $query = $builder->get();

        if ($countOnly) {
            $row = $query->getRow();
            if ($row) {
                return $row->total_count;
            } else {
                return 0;
            }
        }

        return $query->getResult();
    }

    // Generate contract code for both rent and sale contracts
    public function generateContractCode($contractType)
    {
        $prefix = $contractType === 'rent' ? 'CL-RC-' : 'CL-SC-';
        $currentYear = date('y');

        // Get the last used contract code (excluding deleted ones)
        $lastContract = $this->where('contract_code LIKE', $prefix . '%/' . $currentYear)
            ->orderBy('contract_code', 'desc')
            ->first();

        $lastNumber = $lastContract
            ? (int)substr($lastContract['contract_code'], 7, 4)
            : 0;

        do {
            $nextNumber = $lastNumber + 1;
            $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $contractCode = $prefix . $formattedNumber . '/' . $currentYear;

            // Check if the code exists in active or deleted contracts
            $existingContract = $this->where('contract_code', $contractCode)->first();
            $deletedContract = model('DeletedContract')->where('contract_code', $contractCode)->first();

            $lastNumber++;
        } while ($existingContract || $deletedContract);

        return $contractCode;
    }

    public function getContractWithDetails($contractId)
    {
        return $this->select('contracts.*, users.name as customer_name, users.phone')
            ->join('users', 'users.user_id = contracts.user_id', 'left')
            ->where('contracts.contract_id', $contractId)
            ->first();
    }

    // Remove a product from a contract
    public function removeProductFromContract($contractId, $productId)
    {
        return $this->db->table('contract_products')
            ->where('contract_id', $contractId)
            ->where('product_master_id', $productId)
            ->delete();
    }

    public function contractReport(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        // Base SQL Query for selecting the count or results
        if ($countOnly) {
            $sql = "SELECT COUNT(contracts.{$this->primaryKey}) as total_count FROM {$this->table} AS contracts ";
        } else {
            $sql = "SELECT
                    contracts.*,
                    users.name AS customer_name,
                    contract_products.product_id,
                    contract_products.product_master_id,
                    contract_products.quantity,
                    contract_products.price,
                    contract_products.discount,
                    product_master.product_name,
                    product_master.description,
                    product_master.product_images,
                    sizes.size_in_inches,
                    colors.name AS color_name,
                    touch_types.name AS touch_name,
                    glass_types.name AS glass_name,
                    resolutions.resolution_type
                FROM {$this->table} AS contracts ";
        }

        // Adding joins
        $sql .= "LEFT JOIN contract_products ON contract_products.contract_id = contracts.contract_id ";
        $sql .= "LEFT JOIN users ON users.user_id = contracts.user_id ";
        $sql .= "LEFT JOIN products ON products.product_id = contract_products.product_id ";
        $sql .= "LEFT JOIN product_master ON product_master.product_master_id = IFNULL(contract_products.product_master_id, products.product_master_id) ";
        $sql .= "LEFT JOIN sizes ON sizes.size_id = product_master.size_id ";
        $sql .= "LEFT JOIN colors ON colors.color_id = product_master.color_id ";
        $sql .= "LEFT JOIN touch_types ON touch_types.touch_id = product_master.touch_id ";
        $sql .= "LEFT JOIN glass_types ON glass_types.glass_id = product_master.glass_id ";
        $sql .= "LEFT JOIN resolutions ON resolutions.resolution_id = product_master.resolution_id ";

        $whereConditions = [];

        // Applying search filters
        if (!empty($searchArray)) {
            if (isset($searchArray['txtsearch']) && $searchArray['txtsearch']) {
                $searchTerm = $this->db->escapeLikeString($searchArray['txtsearch']);
                $whereConditions[] = "(users.name LIKE '%$searchTerm%' OR users.phone LIKE '%$searchTerm%')";
            }
            if (isset($searchArray['contract_code']) && $searchArray['contract_code']) {
                $contractCode = $this->db->escapeLikeString($searchArray['contract_code']);
                $whereConditions[] = "contracts.contract_code = '$contractCode'";
            }
            if (isset($searchArray['product_name']) && $searchArray['product_name']) {
                $productName = $this->db->escapeLikeString($searchArray['product_name']);
                $whereConditions[] = "product_master.product_name LIKE '%$productName%'";
            }
            if (isset($searchArray['start_date']) && $searchArray['start_date']) {
                $startDate = $this->db->escapeString($searchArray['start_date']);
                $whereConditions[] = "contracts.created_at >= '$startDate'";
            }
            if (isset($searchArray['end_date']) && $searchArray['end_date']) {
                $endDate = $this->db->escapeString($searchArray['end_date']);
                $whereConditions[] = "contracts.created_at <= '$endDate'";
            }
            if (isset($searchArray['size']) && $searchArray['size']) {
                $size = $this->db->escapeLikeString($searchArray['size']);
                $whereConditions[] = "sizes.size_id = '$size'";
            }
            if (isset($searchArray['color']) && $searchArray['color']) {
                $color = $this->db->escapeLikeString($searchArray['color']);
                $whereConditions[] = "colors.color_id = '$color'";
            }
            if (isset($searchArray['touch_type']) && $searchArray['touch_type']) {
                $touchType = $this->db->escapeLikeString($searchArray['touch_type']);
                $whereConditions[] = "touch_types.touch_id = '$touchType'";
            }
            if (isset($searchArray['glass_type']) && $searchArray['glass_type']) {
                $glassType = $this->db->escapeLikeString($searchArray['glass_type']);
                $whereConditions[] = "glass_types.glass_id = '$glassType'";
            }
            if (isset($searchArray['resolution_type']) && $searchArray['resolution_type']) {
                $resolutionType = $this->db->escapeLikeString($searchArray['resolution_type']);
                $whereConditions[] = "resolutions.resolution_id = '$resolutionType'";
            }
        }

        // Adding WHERE conditions to the query
        if (!empty($whereConditions)) {
            $sql .= "WHERE " . implode(' AND ', $whereConditions) . " ";
        }

        // Ordering by the primary key in descending order
        $sql .= "GROUP BY contracts.contract_id ORDER BY contracts.{$this->primaryKey} DESC ";

        // Pagination (Limit and Offset)
        if ($limit && $offset) {
            $sql .= "LIMIT $offset, $limit ";
        }

        // Executing the query
        $query = $this->db->query($sql);
        $result = $query->getResult();

        // Return either count or the result set
        if ($countOnly) {
            if (empty($result)) {
                return 0;
            } else {
                return $result[0]->total_count;
            }
        }

        return $result;
    }

    public function rentContractReport(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        // Base SQL Query for selecting the count or results
        if ($countOnly) {
            $sql = "SELECT COUNT(contracts.{$this->primaryKey}) as total_count FROM {$this->table} AS contracts ";
        } else {
            $sql = "SELECT
                contracts.*,
                users.name AS customer_name,
                contract_products.product_id,
                contract_products.product_master_id,
                contract_products.quantity,
                contract_products.price,
                contract_products.discount,
                product_master.product_name,
                product_master.description,
                product_master.product_images,
                sizes.size_in_inches,
                colors.name AS color_name,
                touch_types.name AS touch_name,
                glass_types.name AS glass_name,
                resolutions.resolution_type
            FROM {$this->table} AS contracts ";
        }

        // Adding joins
        $sql .= "LEFT JOIN contract_products ON contract_products.contract_id = contracts.contract_id ";
        $sql .= "LEFT JOIN users ON users.user_id = contracts.user_id ";
        $sql .= "LEFT JOIN products ON products.product_id = contract_products.product_id ";
        $sql .= "LEFT JOIN product_master ON product_master.product_master_id = IFNULL(contract_products.product_master_id, products.product_master_id) ";
        $sql .= "LEFT JOIN sizes ON sizes.size_id = product_master.size_id ";
        $sql .= "LEFT JOIN colors ON colors.color_id = product_master.color_id ";
        $sql .= "LEFT JOIN touch_types ON touch_types.touch_id = product_master.touch_id ";
        $sql .= "LEFT JOIN glass_types ON glass_types.glass_id = product_master.glass_id ";
        $sql .= "LEFT JOIN resolutions ON resolutions.resolution_id = product_master.resolution_id ";

        $whereConditions = ["contracts.contract_type = 'rent'"]; // Default rent filter

        // Applying search filters
        if (!empty($searchArray)) {
            if (isset($searchArray['txtsearch']) && $searchArray['txtsearch']) {
                $searchTerm = $this->db->escapeLikeString($searchArray['txtsearch']);
                $whereConditions[] = "(users.name LIKE '%$searchTerm%' OR users.phone LIKE '%$searchTerm%')";
            }
            if (isset($searchArray['contract_code']) && $searchArray['contract_code']) {
                $contractCode = $this->db->escapeLikeString($searchArray['contract_code']);
                $whereConditions[] = "contracts.contract_code = '$contractCode'";
            }
            if (isset($searchArray['product_name']) && $searchArray['product_name']) {
                $productName = $this->db->escapeLikeString($searchArray['product_name']);
                $whereConditions[] = "product_master.product_name LIKE '%$productName%'";
            }
            if (isset($searchArray['start_date']) && $searchArray['start_date']) {
                $startDate = $this->db->escapeString($searchArray['start_date']);
                $whereConditions[] = "contracts.created_at >= '$startDate'";
            }
            if (isset($searchArray['end_date']) && $searchArray['end_date']) {
                $endDate = $this->db->escapeString($searchArray['end_date']);
                $whereConditions[] = "contracts.created_at <= '$endDate'";
            }
            if (isset($searchArray['size']) && $searchArray['size']) {
                $size = $this->db->escapeLikeString($searchArray['size']);
                $whereConditions[] = "sizes.size_id = '$size'";
            }
            if (isset($searchArray['color']) && $searchArray['color']) {
                $color = $this->db->escapeLikeString($searchArray['color']);
                $whereConditions[] = "colors.color_id = '$color'";
            }
            if (isset($searchArray['touch_type']) && $searchArray['touch_type']) {
                $touchType = $this->db->escapeLikeString($searchArray['touch_type']);
                $whereConditions[] = "touch_types.touch_id = '$touchType'";
            }
            if (isset($searchArray['glass_type']) && $searchArray['glass_type']) {
                $glassType = $this->db->escapeLikeString($searchArray['glass_type']);
                $whereConditions[] = "glass_types.glass_id = '$glassType'";
            }
            if (isset($searchArray['resolution_type']) && $searchArray['resolution_type']) {
                $resolutionType = $this->db->escapeLikeString($searchArray['resolution_type']);
                $whereConditions[] = "resolutions.resolution_id = '$resolutionType'";
            }
        }

        // Adding WHERE conditions to the query
        if (!empty($whereConditions)) {
            $sql .= "WHERE " . implode(' AND ', $whereConditions) . " ";
        }

        // Ordering by the primary key in descending order
        $sql .= "GROUP BY contracts.contract_id ORDER BY contracts.{$this->primaryKey} DESC ";

        // Pagination (Limit and Offset)
        if ($limit && $offset) {
            $sql .= "LIMIT $offset, $limit ";
        }

        // Executing the query
        $query = $this->db->query($sql);
        $result = $query->getResult();

        // Return either count or the result set
        if ($countOnly) {
            if (empty($result)) {
                return 0;
            } else {
                return $result[0]->total_count;
            }
        }

        return $result;
    }

    public function saleContractReport(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        // Base SQL Query for selecting the count or results
        if ($countOnly) {
            $sql = "SELECT COUNT(contracts.{$this->primaryKey}) as total_count FROM {$this->table} AS contracts ";
        } else {
            $sql = "SELECT
                contracts.*,
                users.name AS customer_name,
                contract_products.product_id,
                contract_products.product_master_id,
                contract_products.quantity,
                contract_products.price,
                contract_products.discount,
                product_master.product_name,
                product_master.description,
                product_master.product_images,
                sizes.size_in_inches,
                colors.name AS color_name,
                touch_types.name AS touch_name,
                glass_types.name AS glass_name,
                resolutions.resolution_type
            FROM {$this->table} AS contracts ";
        }

        // Adding joins
        $sql .= "LEFT JOIN contract_products ON contract_products.contract_id = contracts.contract_id ";
        $sql .= "LEFT JOIN users ON users.user_id = contracts.user_id ";
        $sql .= "LEFT JOIN products ON products.product_id = contract_products.product_id ";
        $sql .= "LEFT JOIN product_master ON product_master.product_master_id = IFNULL(contract_products.product_master_id, products.product_master_id) ";
        $sql .= "LEFT JOIN sizes ON sizes.size_id = product_master.size_id ";
        $sql .= "LEFT JOIN colors ON colors.color_id = product_master.color_id ";
        $sql .= "LEFT JOIN touch_types ON touch_types.touch_id = product_master.touch_id ";
        $sql .= "LEFT JOIN glass_types ON glass_types.glass_id = product_master.glass_id ";
        $sql .= "LEFT JOIN resolutions ON resolutions.resolution_id = product_master.resolution_id ";

        $whereConditions = ["contracts.contract_type = 'sale'"]; // Default sale filter

        // Applying search filters
        if (!empty($searchArray)) {
            if (isset($searchArray['txtsearch']) && $searchArray['txtsearch']) {
                $searchTerm = $this->db->escapeLikeString($searchArray['txtsearch']);
                $whereConditions[] = "(users.name LIKE '%$searchTerm%' OR users.phone LIKE '%$searchTerm%')";
            }
            if (isset($searchArray['contract_code']) && $searchArray['contract_code']) {
                $contractCode = $this->db->escapeLikeString($searchArray['contract_code']);
                $whereConditions[] = "contracts.contract_code = '$contractCode'";
            }
            if (isset($searchArray['product_name']) && $searchArray['product_name']) {
                $productName = $this->db->escapeLikeString($searchArray['product_name']);
                $whereConditions[] = "product_master.product_name LIKE '%$productName%'";
            }
            if (isset($searchArray['start_date']) && $searchArray['start_date']) {
                $startDate = $this->db->escapeString($searchArray['start_date']);
                $whereConditions[] = "contracts.created_at >= '$startDate'";
            }
            if (isset($searchArray['end_date']) && $searchArray['end_date']) {
                $endDate = $this->db->escapeString($searchArray['end_date']);
                $whereConditions[] = "contracts.created_at <= '$endDate'";
            }
            if (isset($searchArray['size']) && $searchArray['size']) {
                $size = $this->db->escapeLikeString($searchArray['size']);
                $whereConditions[] = "sizes.size_id = '$size'";
            }
            if (isset($searchArray['color']) && $searchArray['color']) {
                $color = $this->db->escapeLikeString($searchArray['color']);
                $whereConditions[] = "colors.color_id = '$color'";
            }
            if (isset($searchArray['touch_type']) && $searchArray['touch_type']) {
                $touchType = $this->db->escapeLikeString($searchArray['touch_type']);
                $whereConditions[] = "touch_types.touch_id = '$touchType'";
            }
            if (isset($searchArray['glass_type']) && $searchArray['glass_type']) {
                $glassType = $this->db->escapeLikeString($searchArray['glass_type']);
                $whereConditions[] = "glass_types.glass_id = '$glassType'";
            }
            if (isset($searchArray['resolution_type']) && $searchArray['resolution_type']) {
                $resolutionType = $this->db->escapeLikeString($searchArray['resolution_type']);
                $whereConditions[] = "resolutions.resolution_id = '$resolutionType'";
            }
        }

        // Adding WHERE conditions to the query
        if (!empty($whereConditions)) {
            $sql .= "WHERE " . implode(' AND ', $whereConditions) . " ";
        }

        // Ordering by the primary key in descending order
        $sql .= "GROUP BY contracts.contract_id ORDER BY contracts.{$this->primaryKey} DESC ";

        // Pagination (Limit and Offset)
        if ($limit && $offset) {
            $sql .= "LIMIT $offset, $limit ";
        }

        // Executing the query
        $query = $this->db->query($sql);
        $result = $query->getResult();

        // Return either count or the result set
        if ($countOnly) {
            if (empty($result)) {
                return 0;
            } else {
                return $result[0]->total_count;
            }
        }

        return $result;
    }
}
