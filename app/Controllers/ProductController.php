<?php

namespace App\Controllers;

use App\Models\SizesModel;
use App\Models\ColorsModel;
use App\Libraries\Pagination;
use App\Models\ResolutionModel;
use App\Models\GlassTypesModel;
use App\Models\TouchTypesModel;
use App\Models\ProductMasterModel;
use App\Controllers\BaseController;

class ProductController extends BaseController
{
    protected $pagination;
    protected $sizesModel;
    protected $colorsModel;
    protected $glassTypesModel;
    protected $touchTypesModel;
    protected $resolutionModel;
    protected $productMasterModel;

    public function __construct()
    {
        // Initialize models
        $this->pagination = new Pagination();
        $this->sizesModel = new SizesModel();
        $this->colorsModel = new ColorsModel();
        $this->glassTypesModel = new GlassTypesModel();
        $this->touchTypesModel = new TouchTypesModel();
        $this->resolutionModel = new ResolutionModel();
        $this->productMasterModel = new ProductMasterModel();
    }

    public function index()
    {
        set_title('Product List | ' . SITE_NAME);

        $data = [
            'action' => "product-master",
            'pageTitle' => "Product List",
            'startLimit' => 0,
            'reverse' => 0,
            'pagination' => '',
            'results' => [],
            'searchArray' => []
        ];

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            if ($field) {
                $data['searchArray'][$field] = $searchValue;
            }
        }

        $data['sizes'] = $this->sizesModel->findAll();
        $data['colors'] = $this->colorsModel->findAll();
        $data['touchTypes'] = $this->touchTypesModel->findAll();
        $data['glassTypes'] = $this->glassTypesModel->findAll();
        $data['resolutions'] = $this->resolutionModel->findAll();

        // Pagination logic
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 200;
        $totalRecord = $this->productMasterModel->getProducts($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $this->pagination->getPaginate($totalRecord, $page, $Limit);

        // Fetching results for the current page
        $data['results'] = $this->productMasterModel->getProducts($data['searchArray'], $startLimit, $Limit);

        return view('admin/products/index', $data);
    }

    public function create()
    {
        $validation = \Config\Services::validation();

        // Validate input
        if (!$this->validate([
            'product_name' => 'required',
            'product_price' => 'required',
            'description' => 'required',
            'size_inch' => 'required',
            'glass_type' => 'required',
            'color' => 'required',
            'touch_type' => 'required',
            'resolution_type' => 'required',
            'serial_number' => 'permit_empty|alpha_numeric',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ]);
        }

        // calculate the product final price
        $productPrice = (float)$this->request->getPost('product_price');
        $discount = (float)$this->request->getPost('discount');
        $finalProductPrice = $productPrice - ($productPrice * ($discount / 100));

        $data = [
            'product_name' => $this->request->getPost('product_name'),
            'product_price' => $productPrice,
            'description' => $this->request->getPost('description'),
            'serial_number' => $this->request->getPost('serial_number'),
            'size_id' => $this->request->getPost('size_inch'),
            'color_id' => $this->request->getPost('color'),
            'touch_id' => $this->request->getPost('touch_type'),
            'resolution_id' => $this->request->getPost('resolution_type'),
            'glass_id' => $this->request->getPost('glass_type'),
            'discount_percentage' => $discount,
            'final_product_price' => $finalProductPrice,
            'additional_information' => $this->request->getPost('additional_information'),
        ];

        // Handle image uploads
        $imageFiles = $this->request->getFiles('images');
        if (empty($imageFiles['images'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => 'No images uploaded.',
            ]);
        }

        $imageNames = [];
        // Create the directory if it doesn't exist
        $uploadPath = FCPATH . 'uploads/products';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        foreach ($imageFiles['images'] as $imageFile) {
            if ($imageFile->isValid() && !$imageFile->hasMoved()) {
                // Generate a unique name for the image
                $imageName = time() . '_' . $imageFile->getRandomName();
                if ($imageFile->move($uploadPath, $imageName)) {
                    $imageNames[] = $imageName;
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'errors' => 'Failed to move the uploaded file.',
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => 'Invalid file upload.',
                ]);
            }
        }

        // Join image names to store in the database
        $data['product_images'] = implode(',', $imageNames);

        // Save the data to the database
        if ($this->productMasterModel->save($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Product added successfully',
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => 'Failed to save product. Please try again.',
            ]);
        }
    }

    public function edit($id)
    {
        $product = $this->productMasterModel->find($id);

        if ($product) {
            return $this->response->setJSON($product);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Product not found']);
        }
    }

    public function update()
    {
        $productId = $this->request->getPost('product_master_id');

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'product_name' => 'required',
            'product_price' => 'required',
            'description' => 'required',
            'size_inch' => 'required',
            'glass_type' => 'required',
            'color' => 'required',
            'touch_type' => 'required',
            'resolution_type' => 'required',
            'serial_number' => 'permit_empty|alpha_numeric',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        // calculate the product final price
        $productPrice = (float)$this->request->getPost('product_price');
        $discount = (float)$this->request->getPost('discount');
        $finalProductPrice = $productPrice - ($productPrice * ($discount / 100));

        $data = [
            'product_name' => $this->request->getPost('product_name'),
            'product_price' => $productPrice,
            'description' => $this->request->getPost('description'),
            'serial_number' => $this->request->getPost('serial_number'),
            'size_id' => $this->request->getPost('size_inch'),
            'color_id' => $this->request->getPost('color'),
            'touch_id' => $this->request->getPost('touch_type'),
            'resolution_id' => $this->request->getPost('resolution_type'),
            'glass_id' => $this->request->getPost('glass_type'),
            'discount_percentage' => $discount,
            'final_product_price' => $finalProductPrice,
            'additional_information' => $this->request->getPost('additional_information'),
        ];

        // Fetch existing product details
        $existingProduct = $this->productMasterModel->find($productId);
        if (!$existingProduct) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => "Product not found.",
            ]);
        }

        // Handle image uploads only if new images are provided
        $imageFiles = $this->request->getFiles('images');
        if (!empty($imageFiles['images'])) {
            $newImagesUploaded = false;

            // Check if at least one valid image was uploaded
            foreach ($imageFiles['images'] as $imageFile) {
                if ($imageFile->isValid()) {
                    $newImagesUploaded = true;
                    break; // Exit loop after finding the first valid image
                }
            }

            if ($newImagesUploaded) {
                // Remove old images from filesystem
                $oldImages = explode(',', $existingProduct['product_images']);
                foreach ($oldImages as $oldImage) {
                    $oldImagePath = FCPATH . 'uploads/products/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Handle the new image uploads
                $imageNames = [];
                foreach ($imageFiles['images'] as $imageFile) {
                    if ($imageFile->isValid() && !$imageFile->hasMoved()) {
                        // Generate a unique name for the image
                        $imageName = time() . '_' . $imageFile->getRandomName();
                        if ($imageFile->move(FCPATH . 'uploads/products', $imageName)) {
                            $imageNames[] = $imageName;
                        } else {
                            return $this->response->setJSON([
                                'status' => 'error',
                                'errors' => 'Failed to move the uploaded file.',
                            ]);
                        }
                    }
                }

                // Join image names to store in the database
                $data['product_images'] = implode(',', $imageNames);
            } else {
                // If no new images are uploaded, retain the old image names
                $data['product_images'] = $existingProduct['product_images'];
            }
        } else {
            // If no new files were uploaded, keep the existing images
            $data['product_images'] = $existingProduct['product_images'];
        }

        // Update the product in the database
        if ($this->productMasterModel->update($productId, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Product updated successfully',
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => 'Failed to update product. Please try again.',
            ]);
        }
    }

    public function showDetails($id)
    {
        $data = array();
        $data['pageTitle'] = "Product Details";
        $data['record'] = $this->productMasterModel->getProductWithDetails($id);
        if (!$data['record']) {
            return redirect()->to('/admin/products')->with('error', 'Product not found.');
        }

        return view('admin/products/preview', $data);
    }

    public function delete($id)
    {
        if (is_numeric($id) && !empty($id)) {
            try {
                $product = $this->productMasterModel->find($id);

                // Check if product exists
                if (!$product) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Product not found.',
                    ]);
                }

                // Get product images
                $oldImages = explode(',', $product['product_images']);

                // Loop through the old images and delete them from the server
                $uploadPath = FCPATH . 'uploads/products/';
                foreach ($oldImages as $oldImage) {
                    $oldImagePath = $uploadPath . $oldImage;

                    // Check if the image exists and then delete it
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Delete the product record from the database
                $this->productMasterModel->where('product_master_id', $id)->delete();
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Product and associated images deleted successfully.',
                ]);
            } catch (\Exception $e) {
                // Catch any exception and return an error
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'An unexpected error occurred. Please try again.',
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid product ID.',
            ]);
        }
    }

    public function getProductsRows()
    {
        // Get the requested productSource from GET data
        $productSource = $this->request->getGet('product_source');

        // Validate the productSource to ensure it's either 'products' or 'sale_products'
        if (!in_array($productSource, ['master', 'purchase_products'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid product specified.'
            ]);
        }

        // Initialize models and pagination
        $data = [];

        try {
            if ($productSource === 'master') {
                // Fetch product details
                $data['product_source'] = "master";
                $data['products'] = $this->productMasterModel->getProducts();
                if (empty($data['products'])) {
                    $data['message'] = 'No products found.';
                }
            } else if ($productSource === 'purchase_products') {
                // Fetch sale product details
                $data['product_source'] = "purchase_products";
                $data['purchaseProducts'] = $this->productMasterModel->getPurchaseProductsDetails();
                if (empty($data['purchaseProducts'])) {
                    $data['message'] = 'No sale products found.';
                }
            }

            // If we have any data, return it as JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
