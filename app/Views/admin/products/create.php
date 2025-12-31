<!-- Start Modal -->
<div class="modal fade" id="newProductModal" tabindex="-1" aria-labelledby="newProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newProductModalLabel">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalMessages"></div>
                <form id="productForm" autocomplete="off" enctype="multipart/form-data" action="<?= site_url('product-master/save') ?>" method="POST">
                    <input type="hidden" id="productId" name="product_master_id">

                    <!-- Product Name -->
                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name <span class="mandatory-field">*</span></label>
                        <input type="text" class="form-control" id="productName" name="product_name" required placeholder="Enter product name">
                    </div>

                    <div class="mb-3">
                        <label for="serialNumber" class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="serialNumber" name="serial_number" placeholder="Enter serial number">
                    </div>

                    <div class="mb-3">
                        <label for="productPriceSale" class="form-label">Product Price <span class="mandatory-field">*</span></label>
                        <input type="number" class="form-control" id="productPriceSale" name="product_price" required placeholder="Enter product price" min="0" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label for="discountPercentage" class="form-label">Discount (%)</label>
                        <input type="number" class="form-control" id="discountPercentage" name="discount" placeholder="Enter discount percentage" min="0" max="100">
                    </div>

                    <div class="mb-3">
                        <label for="finalProductPrice" class="form-label">Final Product Price</label>
                        <input type="number" class="form-control" id="finalProductPrice" placeholder="Product Final Price" name="final_product_price" readonly>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Description <span class="mandatory-field">*</span></label>
                        <textarea class="form-control" id="productDescription" name="description" rows="3" required placeholder="Enter product description"></textarea>
                    </div>

                    <!-- Size -->
                    <div class="mb-3">
                        <label for="sizeInch" class="form-label">Size (inch) <span class="mandatory-field">*</span></label>
                        <select id="sizeInch" class="form-select" name="size_inch" required>
                            <option value="">Select Size</option>
                            <?php foreach ($sizes as $size): ?>
                                <option value="<?= $size['size_id'] ?>"><?= $size['size_in_inches'] ?> inches</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Color -->
                    <div class="mb-3">
                        <label for="color" class="form-label">Colour <span class="mandatory-field">*</span></label>
                        <select id="color" class="form-select" name="color" required>
                            <option value="">Select Colour</option>
                            <?php foreach ($colors as $color): ?>
                                <option value="<?= $color['color_id'] ?>"><?= $color['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Touch Type -->
                    <div class="mb-3">
                        <label for="touchType" class="form-label">Touch Type <span class="mandatory-field">*</span></label>
                        <select id="touchType" class="form-select" name="touch_type" required>
                            <option value="">Select Touch Type</option>
                            <?php foreach ($touchTypes as $touchType): ?>
                                <option value="<?= $touchType['touch_id'] ?>"><?= $touchType['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Resolution Type -->
                    <div class="mb-3">
                        <label for="resolutions" class="form-label">Resolution Type <span class="mandatory-field">*</span></label>
                        <select id="resolutions" class="form-select" name="resolution_type" required>
                            <option value="">Select Resolution</option>
                            <?php foreach ($resolutions as $resolution): ?>
                                <option value="<?= $resolution['resolution_id'] ?>"><?= $resolution['resolution_type'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Glass Type -->
                    <div class="mb-3">
                        <label for="glassType" class="form-label">Glass Type <span class="mandatory-field">*</span></label>
                        <select id="glassType" class="form-select" name="glass_type" required>
                            <option value="">Select Glass Type</option>
                            <?php foreach ($glassTypes as $glassType): ?>
                                <option value="<?= $glassType['glass_id'] ?>"><?= $glassType['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="productImages" class="form-label">Upload Images <span class="mandatory-field">*</span></label>
                        <input type="file" class="form-control" id="productImages" name="images[]" multiple accept="image/*" required onchange="previewImages(event, 'imagePreviews')">
                    </div>

                    <div id="imagePreviews" style="display: flex; flex-wrap: wrap;">
                        <!-- Image previews will be displayed here -->
                    </div>

                    <!-- Additional Information -->
                    <div class="mb-3">
                        <label for="additionalInformation" class="form-label">Additional Information</label>
                        <textarea class="form-control" id="additionalInformation" name="additional_information" rows="3" placeholder="Enter additional information"></textarea>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="submitButton" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>