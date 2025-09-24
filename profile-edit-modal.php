<?php
/**
 * Profile Edit Modal
 * Modal for editing user profile information
 */
?>

<!-- Profile Edit Modal -->
<div class="modal fade" id="profileEditModal" tabindex="-1" aria-labelledby="profileEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileEditModalLabel">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="profileEditForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="edit-username" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit-email" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">+252</span>
                                    <input type="tel" class="form-control" id="edit-contact" name="contact_number" placeholder="XXX XXX XXX" maxlength="9">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-gender" class="form-label">Gender</label>
                                <select class="form-select" id="edit-gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-region" class="form-label">Region</label>
                                <select class="form-select" id="edit-region" name="region">
                                    <option value="">Select Region</option>
                                    <option value="banadir">Banadir</option>
                                    <option value="bari">Bari</option>
                                    <option value="bay">Bay</option>
                                    <option value="galguduud">Galguduud</option>
                                    <option value="gedo">Gedo</option>
                                    <option value="hiran">Hiran</option>
                                    <option value="jubbada-dhexe">Jubbada Dhexe</option>
                                    <option value="jubbada-hoose">Jubbada Hoose</option>
                                    <option value="mudug">Mudug</option>
                                    <option value="nugaal">Nugaal</option>
                                    <option value="sanaag">Sanaag</option>
                                    <option value="shabeellaha-dhexe">Shabeellaha Dhexe</option>
                                    <option value="shabeellaha-hoose">Shabeellaha Hoose</option>
                                    <option value="sool">Sool</option>
                                    <option value="togdheer">Togdheer</option>
                                    <option value="woqooyi-galbeed">Woqooyi Galbeed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-city" class="form-label">City</label>
                                <select class="form-select" id="edit-city" name="city" disabled>
                                    <option value="">Select Region First</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveProfileBtn">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div class="modal fade" id="passwordChangeModal" tabindex="-1" aria-labelledby="passwordChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordChangeModalLabel">
                    <i class="fas fa-lock"></i> Change Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="passwordChangeForm">
                    <div class="mb-3">
                        <label for="current-password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current-password" name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current-password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new-password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new-password" name="new_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new-password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Password must be at least 6 characters long.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm-password" name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm-password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="savePasswordBtn">
                    <i class="fas fa-save"></i> Change Password
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Profile Edit Modal JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const profileEditModal = new bootstrap.Modal(document.getElementById('profileEditModal'));
    const passwordChangeModal = new bootstrap.Modal(document.getElementById('passwordChangeModal'));
    
    // Cities for each region
    const citiesByRegion = {
        'banadir': ['Mogadishu', 'Afgooye', 'Marka', 'Wanlaweyn'],
        'bari': ['Bosaso', 'Qardho', 'Caluula', 'Iskushuban', 'Bandarbeyla'],
        'bay': ['Baidoa', 'Burdhubo', 'Dinsor', 'Qansaxdheere'],
        'galguduud': ['Dhusamareb', 'Adado', 'Abudwaq', 'Galgadud'],
        'gedo': ['Garbahaarrey', 'Bardhere', 'Luuq', 'El Wak', 'Dolow'],
        'hiran': ['Beledweyne', 'Buloburde', 'Jalalaqsi', 'Mahas'],
        'jubbada-dhexe': ['Bu\'aale', 'Jilib', 'Sakow', 'Dujuma'],
        'jubbada-hoose': ['Kismayo', 'Jamame', 'Badhaadhe', 'Afmadow'],
        'mudug': ['Galkayo', 'Hobyo', 'Harardhere', 'Jariiban'],
        'nugaal': ['Garowe', 'Eyl', 'Burtinle', 'Dangorayo'],
        'sanaag': ['Erigavo', 'Badhan', 'Laasqoray', 'Dhahar'],
        'shabeellaha-dhexe': ['Jowhar', 'Balcad', 'Adale', 'Warsheikh'],
        'shabeellaha-hoose': ['Merca', 'Baraawe', 'Kurtunwaarey', 'Qoryooley'],
        'sool': ['Laascaanood', 'Taleex', 'Xudun', 'Caynabo'],
        'togdheer': ['Burao', 'Oodweyne', 'Sheikh', 'Buhoodle'],
        'woqooyi-galbeed': ['Hargeisa', 'Berbera', 'Borama', 'Gabiley', 'Baki']
    };
    
    // Region change handler
    document.getElementById('edit-region').addEventListener('change', function() {
        const selectedRegion = this.value;
        const citySelect = document.getElementById('edit-city');
        
        citySelect.innerHTML = '<option value="">Select city</option>';
        citySelect.disabled = true;
        
        if (selectedRegion && citiesByRegion[selectedRegion]) {
            citiesByRegion[selectedRegion].forEach(city => {
                const option = document.createElement('option');
                option.value = city.toLowerCase().replace(/\s+/g, '-');
                option.textContent = city;
                citySelect.appendChild(option);
            });
            citySelect.disabled = false;
        }
    });
    
    // Load user data into form
    function loadUserData() {
        fetch('dashboard-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_user_info'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.data;
                document.getElementById('edit-username').value = user.username || '';
                document.getElementById('edit-email').value = user.email || '';
                document.getElementById('edit-contact').value = user.contact_number ? user.contact_number.replace('+252', '') : '';
                document.getElementById('edit-gender').value = user.gender || '';
                document.getElementById('edit-region').value = user.region || '';
                
                // Trigger region change to load cities
                if (user.region) {
                    document.getElementById('edit-region').dispatchEvent(new Event('change'));
                    setTimeout(() => {
                        document.getElementById('edit-city').value = user.city || '';
                    }, 100);
                }
            }
        })
        .catch(error => {
            console.error('Error loading user data:', error);
        });
    }
    
    // Save profile
    document.getElementById('saveProfileBtn').addEventListener('click', function() {
        const form = document.getElementById('profileEditForm');
        const formData = new FormData(form);
        
        // Add +252 prefix to contact number
        const contactNumber = formData.get('contact_number');
        if (contactNumber) {
            formData.set('contact_number', '+252' + contactNumber);
        }
        
        // Convert FormData to URL-encoded string
        const params = new URLSearchParams();
        for (let [key, value] of formData.entries()) {
            params.append(key, value);
        }
        params.append('action', 'update_profile');
        
        fetch('dashboard-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Profile updated successfully!', 'success');
                profileEditModal.hide();
                // Reload page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'Failed to update profile', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            showNotification('An error occurred while updating profile', 'error');
        });
    });
    
    // Save password
    document.getElementById('savePasswordBtn').addEventListener('click', function() {
        const form = document.getElementById('passwordChangeForm');
        const formData = new FormData(form);
        
        // Validate passwords match
        const newPassword = formData.get('new_password');
        const confirmPassword = formData.get('confirm_password');
        
        if (newPassword !== confirmPassword) {
            showNotification('New passwords do not match', 'error');
            return;
        }
        
        if (newPassword.length < 6) {
            showNotification('Password must be at least 6 characters long', 'error');
            return;
        }
        
        // Convert FormData to URL-encoded string
        const params = new URLSearchParams();
        for (let [key, value] of formData.entries()) {
            params.append(key, value);
        }
        params.append('action', 'change_password');
        
        fetch('dashboard-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Password changed successfully!', 'success');
                passwordChangeModal.hide();
                form.reset();
            } else {
                showNotification(data.message || 'Failed to change password', 'error');
            }
        })
        .catch(error => {
            console.error('Error changing password:', error);
            showNotification('An error occurred while changing password', 'error');
        });
    });
    
    // Show profile edit modal
    window.editProfile = function() {
        loadUserData();
        profileEditModal.show();
    };
    
    // Show password change modal
    window.changePassword = function() {
        passwordChangeModal.show();
    };
    
    // Toggle password visibility
    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    };
    
    // Notification function
    function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
});
</script>
