<?php
// Company Profile Edit Page
$pageTitle = 'Update Company Profile';
$currentAction = 'company';

// Get user ID from URL parameter
$userId = $_GET['id'] ?? null;

if (!$userId) {
    $_SESSION['error'] = 'Company ID is required.';
    header('Location: index.php?action=company');
    exit;
}

// Get user data
$userController = new \App\Controllers\UserController();
$user = $userController->getUserById($userId);

if (!$user || $user['role'] !== 'business') {
    $_SESSION['error'] = 'Company not found or invalid company.';
    header('Location: index.php?action=company');
    exit;
}

// Sample company data (실제로는 데이터베이스에서 가져와야 함)
$companyData = [
                'company_name' => $user['company_name'] ?? $user['name'],
    'company_address' => $user['company_address'] ?? '123 Business Street, Makati City, Philippines',
    'date_of_incorporation' => $user['date_of_incorporation'] ?? '2020-01-15',
    'tin_number' => $user['tin_number'] ?? '123-456-789-000',
    'business_permit' => $user['business_permit'] ?? 'BP-2024-001234',
    'email_address' => $user['email'],
    'contact_number' => $user['contact_number'] ?? '+63 2 1234 5678',
    'website_url' => $user['website_url'] ?? 'https://www.phitsol.com'
];

$contactData = [
                'authorized_representative' => $user['name'],
    'position_title' => $user['position'] ?? 'Chief Executive Officer',
    'representative_contact' => $user['contact_number'] ?? '+63 917 123 4567',
    'representative_email' => $user['email'],
    'secondary_contact_name' => $user['secondary_contact_name'] ?? 'Jane Smith',
    'secondary_contact_position' => $user['secondary_contact_position'] ?? 'Chief Operating Officer',
    'secondary_contact_number' => $user['secondary_contact_number'] ?? '+63 917 987 6543',
    'secondary_contact_email' => $user['secondary_contact_email'] ?? 'jane.smith@phitsol.com'
];

$documents = [
    'company_profile' => $user['documents']['company_profile'] ?? false,
    'business_permit' => $user['documents']['business_permit'] ?? false,
    'bir_2303' => $user['documents']['bir_2303'] ?? false,
    'gis' => $user['documents']['gis'] ?? false,
    'audited_financial' => $user['documents']['audited_financial'] ?? false,
    'proof_of_payment' => $user['documents']['proof_of_payment'] ?? false,
    'valid_id' => $user['documents']['valid_id'] ?? false,
    'corporate_secretary' => $user['documents']['corporate_secretary'] ?? false,
    'credit_investigation' => $user['documents']['credit_investigation'] ?? false,
    'peza_certification' => $user['documents']['peza_certification'] ?? false
];
?>

<!-- Content Header -->
<div class="content-header">
    <div class="header-info">
        <h1 class="page-title">
            <i class="fas fa-edit"></i>
            Update Company Profile
        </h1>
        <div class="stats-info">
            <span class="stat-item">Edit company information for <?php echo htmlspecialchars($companyData['company_name']); ?></span>
        </div>
    </div>
    <div class="header-actions">
        <a href="index.php?action=company" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to List
        </a>
    </div>
</div>

<!-- Content Body -->
<div class="content-body">

    <div class="row">
        <!-- Company Profile Section -->
        <div class="col-lg-6 mb-4">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">
                        <i class="fas fa-building"></i>
                        Company Profile
                    </h5>
                    <div class="admin-card-actions">
                        <button type="button" class="btn btn-primary btn-sm" id="updateCompanyBtn">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                    </div>
                </div>
                <div class="admin-card-body">
                    <form id="companyProfileForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="<?php echo htmlspecialchars($companyData['company_name']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_incorporation" class="form-label">Date of Incorporation <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_of_incorporation" name="date_of_incorporation" 
                                       value="<?php echo $companyData['date_of_incorporation']; ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="company_address" class="form-label">Company Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="company_address" name="company_address" rows="3" readonly><?php echo htmlspecialchars($companyData['company_address']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tin_number" class="form-label">TIN / Tax ID Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tin_number" name="tin_number" 
                                       value="<?php echo htmlspecialchars($companyData['tin_number']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="business_permit" class="form-label">Business Permit Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="business_permit" name="business_permit" 
                                       value="<?php echo htmlspecialchars($companyData['business_permit']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email_address" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email_address" name="email_address" 
                                       value="<?php echo htmlspecialchars($companyData['email_address']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                       value="<?php echo htmlspecialchars($companyData['contact_number']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="website_url" class="form-label">Website URL</label>
                            <input type="url" class="form-control" id="website_url" name="website_url" 
                                   value="<?php echo htmlspecialchars($companyData['website_url']); ?>" readonly>
                        </div>
                        
                        <div class="d-flex gap-2" id="companyFormActions" style="display: none;">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i>
                                Save
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancelCompanyBtn">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Authorized Contact Persons Section -->
        <div class="col-lg-6 mb-4">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">
                        <i class="fas fa-users"></i>
                        Authorized Contact Persons
                    </h5>
                    <div class="admin-card-actions">
                        <button type="button" class="btn btn-primary btn-sm" id="updateContactBtn">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                    </div>
                </div>
                <div class="admin-card-body">
                    <form id="contactPersonsForm">
                        <!-- Primary Contact -->
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-user-tie"></i>
                            Primary Contact Person
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="authorized_representative" class="form-label">Authorized Representative <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="authorized_representative" name="authorized_representative" 
                                       value="<?php echo htmlspecialchars($contactData['authorized_representative']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="position_title" class="form-label">Position/Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="position_title" name="position_title" 
                                       value="<?php echo htmlspecialchars($contactData['position_title']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="representative_contact" class="form-label">Representative Contact No. <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="representative_contact" name="representative_contact" 
                                       value="<?php echo htmlspecialchars($contactData['representative_contact']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="representative_email" class="form-label">Representative Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="representative_email" name="representative_email" 
                                       value="<?php echo htmlspecialchars($contactData['representative_email']); ?>" readonly>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Secondary Contact -->
                        <h6 class="text-secondary mb-3">
                            <i class="fas fa-user"></i>
                            Secondary Contact Person
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="secondary_contact_name" class="form-label">Secondary Contact Person Name</label>
                                <input type="text" class="form-control" id="secondary_contact_name" name="secondary_contact_name" 
                                       value="<?php echo htmlspecialchars($contactData['secondary_contact_name']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="secondary_contact_position" class="form-label">Secondary Contact Position</label>
                                <input type="text" class="form-control" id="secondary_contact_position" name="secondary_contact_position" 
                                       value="<?php echo htmlspecialchars($contactData['secondary_contact_position']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="secondary_contact_number" class="form-label">Secondary Contact Number</label>
                                <input type="tel" class="form-control" id="secondary_contact_number" name="secondary_contact_number" 
                                       value="<?php echo htmlspecialchars($contactData['secondary_contact_number']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="secondary_contact_email" class="form-label">Secondary Contact Email</label>
                                <input type="email" class="form-control" id="secondary_contact_email" name="secondary_contact_email" 
                                       value="<?php echo htmlspecialchars($contactData['secondary_contact_email']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2" id="contactFormActions" style="display: none;">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i>
                                Save
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancelContactBtn">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Required Documents Section -->
    <div class="row">
        <div class="col-12">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">
                        <i class="fas fa-file-alt"></i>
                        Required Documents List
                    </h5>
                    <div class="admin-card-actions">
                        <button type="button" class="btn btn-primary btn-sm" id="updateDocumentsBtn">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                    </div>
                </div>
                <div class="admin-card-body">
                    <form id="documentsForm">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Required Documents</h6>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="company_profile" name="company_profile" 
                                           <?php echo $documents['company_profile'] ? 'checked' : ''; ?> disabled>
                                    <label class="form-check-label" for="company_profile">
                                        Company Profile <span class="text-danger">*</span>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="business_permit" name="business_permit" 
                                           <?php echo $documents['business_permit'] ? 'checked' : ''; ?> disabled>
                                    <label class="form-check-label" for="business_permit">
                                        Mayor or Business Permit <span class="text-danger">*</span>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="bir_2303" name="bir_2303" 
                                           <?php echo $documents['bir_2303'] ? 'checked' : ''; ?> disabled>
                                    <label class="form-check-label" for="bir_2303">
                                        BIR 2303 <span class="text-danger">*</span>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="gis" name="gis" 
                                           <?php echo $documents['gis'] ? 'checked' : ''; ?> disabled>
                                    <label class="form-check-label" for="gis">
                                        GIS <span class="text-danger">*</span>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="audited_financial" name="audited_financial" 
                                           <?php echo $documents['audited_financial'] ? 'checked' : ''; ?> disabled>
                                    <label class="form-check-label" for="audited_financial">
                                        Audited Financial Statement <span class="text-danger">*</span>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="proof_of_payment" name="proof_of_payment" 
                                           <?php echo $documents['proof_of_payment'] ? 'checked' : ''; ?> disabled>
                                    <label class="form-check-label" for="proof_of_payment">
                                        Proof of Payment (3 Months Office Address) <span class="text-danger">*</span>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="valid_id" name="valid_id" 
                                           <?php echo $documents['valid_id'] ? 'checked' : ''; ?> disabled>
                                    <label class="form-check-label" for="valid_id">
                                        Valid ID of Authorized Person (Government/Business ID) <span class="text-danger">*</span>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="corporate_secretary" name="corporate_secretary" 
                                           <?php echo $documents['corporate_secretary'] ? 'checked' : ''; ?> disabled>
                                    <label class="form-check-label" for="corporate_secretary">
                                        Corporate Secretary Certificate (Notarized) <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-secondary mb-3">Optional Documents</h6>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="credit_investigation" name="credit_investigation" 
                                           <?php echo $documents['credit_investigation'] ? 'checked' : ''; ?> disabled>
                                    <label class="form-check-label" for="credit_investigation">
                                        Credit Investigation Form
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="peza_certification" name="peza_certification" 
                                           <?php echo $documents['peza_certification'] ? 'checked' : ''; ?> disabled>
                                    <label class="form-check-label" for="peza_certification">
                                        PEZA Certification (if Zero Rated Tax)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2" id="documentsFormActions" style="display: none;">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i>
                                Save
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancelDocumentsBtn">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <i class="fas fa-check-circle me-2"></i>
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ✅ Successfully updated
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store original values for restoration
    let originalCompanyData = {};
    let originalContactData = {};
    let originalDocumentData = {};
    
    // Company Profile Form
    const updateCompanyBtn = document.getElementById('updateCompanyBtn');
    const companyFormActions = document.getElementById('companyFormActions');
    const cancelCompanyBtn = document.getElementById('cancelCompanyBtn');
    const companyForm = document.getElementById('companyProfileForm');
    const companyInputs = companyForm.querySelectorAll('input, textarea');
    
    // Store original values
    companyInputs.forEach(input => {
        originalCompanyData[input.name] = input.value;
    });
    
    updateCompanyBtn.addEventListener('click', function() {
        companyInputs.forEach(input => {
            input.readOnly = false;
            input.classList.add('form-control-active');
        });
        companyFormActions.style.display = 'flex';
        updateCompanyBtn.style.display = 'none';
    });
    
    cancelCompanyBtn.addEventListener('click', function() {
        // Restore original values
        companyInputs.forEach(input => {
            input.value = originalCompanyData[input.name] || '';
            input.readOnly = true;
            input.classList.remove('form-control-active');
        });
        companyFormActions.style.display = 'none';
        updateCompanyBtn.style.display = 'block';
    });
    
    companyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        
        
        // Collect form data
        const formData = new FormData();
        formData.append('action', 'update_company_profile');
        formData.append('user_id', '<?php echo $userId; ?>');
        
        companyInputs.forEach(input => {
            formData.append(input.name, input.value);
            
        });
        
        
        
        // Send data to server
        fetch('index.php?action=company&method=update_company_profile', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            
            try {
                const data = JSON.parse(text);
                
                
                if (data.success) {
                    showSuccessToast('Company profile updated successfully');
                    
                    // Update original values
                    companyInputs.forEach(input => {
                        originalCompanyData[input.name] = input.value;
                    });
                    
                    // Reset form to read-only
                    companyInputs.forEach(input => {
                        input.readOnly = true;
                        input.classList.remove('form-control-active');
                    });
                    companyFormActions.style.display = 'none';
                    updateCompanyBtn.style.display = 'block';
                } else {
                    alert('Error: ' + (data.message || 'Failed to save company profile'));
                }
            } catch (error) {
                
                alert('Error: Invalid response from server');
            }
        })
        .catch(error => {
            
            alert('Error: Failed to save company profile - ' + error.message);
        });
    });
    
    // Contact Persons Form
    const updateContactBtn = document.getElementById('updateContactBtn');
    const contactFormActions = document.getElementById('contactFormActions');
    const cancelContactBtn = document.getElementById('cancelContactBtn');
    const contactForm = document.getElementById('contactPersonsForm');
    const contactInputs = contactForm.querySelectorAll('input');
    
    // Store original values
    contactInputs.forEach(input => {
        originalContactData[input.name] = input.value;
    });
    
    updateContactBtn.addEventListener('click', function() {
        contactInputs.forEach(input => {
            input.readOnly = false;
            input.classList.add('form-control-active');
        });
        contactFormActions.style.display = 'flex';
        updateContactBtn.style.display = 'none';
    });
    
    cancelContactBtn.addEventListener('click', function() {
        // Restore original values
        contactInputs.forEach(input => {
            input.value = originalContactData[input.name] || '';
            input.readOnly = true;
            input.classList.remove('form-control-active');
        });
        contactFormActions.style.display = 'none';
        updateContactBtn.style.display = 'block';
    });
    
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        
        
        // Collect form data
        const formData = new FormData();
        formData.append('action', 'update_contact_persons');
        formData.append('user_id', '<?php echo $userId; ?>');
        
        contactInputs.forEach(input => {
            formData.append(input.name, input.value);
            
        });
        
        
        
        // Send data to server
        fetch('index.php?action=company&method=update_contact_persons', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            
            try {
                const data = JSON.parse(text);
                
                
                if (data.success) {
                    showSuccessToast('Contact information updated successfully');
                    
                    // Update original values
                    contactInputs.forEach(input => {
                        originalContactData[input.name] = input.value;
                    });
                    
                    // Reset form to read-only
                    contactInputs.forEach(input => {
                        input.readOnly = true;
                        input.classList.remove('form-control-active');
                    });
                    contactFormActions.style.display = 'none';
                    updateContactBtn.style.display = 'block';
                } else {
                    alert('Error: ' + (data.message || 'Failed to save contact information'));
                }
            } catch (error) {
                
                alert('Error: Invalid response from server');
            }
        })
        .catch(error => {
            
            alert('Error: Failed to save contact information - ' + error.message);
        });
    });
    
    // Documents Form
    const updateDocumentsBtn = document.getElementById('updateDocumentsBtn');
    const documentsFormActions = document.getElementById('documentsFormActions');
    const cancelDocumentsBtn = document.getElementById('cancelDocumentsBtn');
    const documentsForm = document.getElementById('documentsForm');
    const documentCheckboxes = documentsForm.querySelectorAll('input[type="checkbox"]');
    
    // Store original values
    documentCheckboxes.forEach(checkbox => {
        originalDocumentData[checkbox.name] = checkbox.checked;
    });
    
    updateDocumentsBtn.addEventListener('click', function() {
        documentCheckboxes.forEach(checkbox => {
            checkbox.disabled = false;
        });
        documentsFormActions.style.display = 'flex';
        updateDocumentsBtn.style.display = 'none';
    });
    
    cancelDocumentsBtn.addEventListener('click', function() {
        // Restore original values
        documentCheckboxes.forEach(checkbox => {
            checkbox.checked = originalDocumentData[checkbox.name] || false;
            checkbox.disabled = true;
        });
        documentsFormActions.style.display = 'none';
        updateDocumentsBtn.style.display = 'block';
    });
    
    documentsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Collect form data
        const formData = new FormData();
        formData.append('action', 'update_documents');
        formData.append('user_id', '<?php echo $userId; ?>');
        
        // Add all checkbox values
        documentCheckboxes.forEach(checkbox => {
            formData.append(checkbox.name, checkbox.checked ? '1' : '0');
        });
        
        // Send data to server
        fetch('index.php?action=company&method=update_documents', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Documents updated successfully');
                
                // Update original values
                documentCheckboxes.forEach(checkbox => {
                    originalDocumentData[checkbox.name] = checkbox.checked;
                });
                
                // Reset form to read-only
                documentCheckboxes.forEach(checkbox => {
                    checkbox.disabled = true;
                });
                documentsFormActions.style.display = 'none';
                updateDocumentsBtn.style.display = 'block';
            } else {
                alert('Error: ' + (data.message || 'Failed to save documents'));
            }
        })
        .catch(error => {
            
            alert('Error: Failed to save documents');
        });
    });
    
    // Success Toast Function
    function showSuccessToast(message = '✅ Successfully updated') {
        const toastElement = document.getElementById('successToast');
        if (toastElement) {
            const toastBody = toastElement.querySelector('.toast-body');
            if (toastBody) {
                toastBody.textContent = message;
            }
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    }
});
</script>

<style>
/* Existing styles */
.form-control-active {
    background-color: #fff !important;
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
}

.form-check-input:disabled {
    opacity: 0.6;
}

.form-check-input:not(:disabled):checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.toast {
    z-index: 1055;
}

/* Admin Card Actions */
.admin-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa;
}

.admin-card-actions {
    display: flex;
    gap: 0.5rem;
}

.admin-card-actions .btn {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    transition: all 0.15s ease-in-out;
}

/* Form Actions Buttons */
.d-flex.gap-2 .btn {
    min-width: 100px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    transition: all 0.15s ease-in-out;
    white-space: nowrap;
}

.d-flex.gap-2 .btn i {
    margin-right: 0.5rem;
    font-size: 0.875rem;
}

/* Readonly Input Styling */
input[readonly], textarea[readonly] {
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
    color: #6c757d !important;
    cursor: not-allowed;
}

/* Disabled Checkbox Styling */
input[type="checkbox"]:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .admin-card-actions {
        width: 100%;
        justify-content: flex-end;
    }
    
    .d-flex.gap-2 .btn {
        min-width: 80px;
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
}
</style> 