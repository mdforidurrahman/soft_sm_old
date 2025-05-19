{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="editId" name="id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editContact_id" class="form-label">Contact Id</label>
                                <input type="text" class="form-control" id="editContact_id" name="contact_id" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFatherName" class="form-label">Father Name</label>
                                <input type="text" class="form-control" id="editFatherName" name="father_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editRole" class="form-label">Role</label>
                                <select name="role" class="form-control" id="editRole">
                                    <option value="customer">Customer</option>
                                    <option value="supplier">Supplier</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editStore_id" class="form-label">Store Name*</label>
                            <div class="input-group">
                                <select name="store_id" class="form-select" id="editStore_id" required>
                                    <option value="">Please Select</option>
                                    @forelse($storeName as $key => $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="editCategory_id" class="form-label">Category Name*</label>
                            <div class="input-group">
                                <select name="category_id" class="form-select" id="editCategory_id" required>
                                    <option value="">Please Select</option>
                                    @forelse($category as $key => $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editNid" class="form-label">Nid</label>
                                <input type="text" class="form-control" id="editNid" name="nid" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editPhone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="editPhone" name="phone" required>
                            </div>
                        </div>
                        <!-- District -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editDistrict" class="form-label">District</label>
                                <select class="form-control" id="editDistrict" name="district" required>
                                    <option value="">Select District</option>
                                    <option value="Naogaon">Naogaon</option>
                                    <option value="Rajshahi">Rajshahi</option>
                                    <option value="Chapainawabganj">Chapainawabganj</option>
                                    <option value="Rangpur">Rangpur</option>
                                    <option value="Bogura">Bogura</option>
                                    <option value="Joypurhat">Joypurhat</option>
                                    <option value="Natore">Natore</option>
                                    <option value="Pabna">Pabna</option>
                                    <option value="Sirajganj">Sirajganj</option>
                                </select>
                            </div>
                        </div>

                        <!-- Thana -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editThana" class="form-label">Thana/Upazila</label>
                                <select class="form-control" id="editThana" name="thana" required>
                                    <option value="">Select Thana</option>
                                </select>
                            </div>
                        </div>

                        <!-- Post Office -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editPostOffice" class="form-label">Post Office</label>
                                <input type="text" class="form-control" id="editPostOffice" name="post_office" required>
                            </div>
                        </div>

                        <!-- Village -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editVillage" class="form-label">Village</label>
                                <input type="text" class="form-control" id="editVillage" name="village" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editStatus" class="form-label">Status</label>
                                <select class="form-select" id="editStatus" name="status" required>
                                    <option value="0">Inactive</option>
                                    <option value="1">Active</option>
                                </select>
                            </div>
                        </div>

                        <!-- Image Upload Fields with Previews -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editImage" class="form-label">Customer Image</label>
                                <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
                                <!-- Preview Image -->
                                <div class="mt-2">
                                    <img id="editPreviewImage" src="" alt="Preview" class="img-thumbnail" style="width: 150px; height: 150px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFingerPrint" class="form-label">Customer Fingerprint</label>
                                <input type="file" class="form-control" id="editFingerPrint" name="finger_print" accept="image/*">
                                <!-- Preview Fingerprint -->
                                <div class="mt-2">
                                    <img id="editPreviewFingerprint" src="" alt="Preview" class="img-thumbnail" style="width: 150px; height: 150px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editSignature" class="form-label">Customer Signature</label>
                                <input type="file" class="form-control" id="editSignature" name="signature" accept="image/*">
                                <!-- Preview Signature -->
                                <div class="mt-2">
                                    <img id="editPreviewSignature" src="" alt="Preview" class="img-thumbnail" style="width: 150px; height: 150px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateContact()">Update Contact</button>
            </div>
        </div>
    </div>
</div>

<script>
    // District-Thana mapping
    const thanaLists = {
        Naogaon: [
            'Naogaon Sadar', 'Atrai', 'Badalgachhi', 'Dhamoirhat', 'Manda',
            'Mahadebpur', 'Niamatpur', 'Patnitala', 'Porsha', 'Raninagar', 'Sapahar'
        ],
        Bogura: [
            'Adamdighi', 'Dhunat', 'Gabtali', 'Kahalu', 'Shibganj', 'Sariakandi', 'Shahjahanpur', 'Kahaloo'
        ],
        Rajshahi: [
            'Boalia', 'Paba', 'Godagari', 'Charghat', 'Bagha', 'Mohanpur', 'Tanore'
        ],
        Chapainawabganj: [
            'Gomastapur', 'Nachole', 'Bholahat', 'Shibganj', 'Chapai Nawabganj Sadar', 'Shibganj', 'Gomastapur'
        ],
        Rangpur: [
            'Rangpur Sadar', 'Badarganj', 'Kaunia', 'Gangachara', 'Pirganj', 'Mithapukur', 'Taraganj', 'Kurigram'
        ],
        Joypurhat: [
            'Akkelpur', 'Atrai', 'Kalai', 'Khetlal', 'Panchbibi'
        ],
        Natore: [
            'Bagatipara', 'Gurudaspur', 'Lalpur', 'Natore Sadar', 'Singra', 'Baraigram', 'Naldanga'
        ],
        Pabna: [
            'Atghoria', 'Ishwardi', 'Chatmohar', 'Faridpur', 'Bera', 'Bhangura', 'Santhia', 'Sujanagar', 'Pabna Sadar'
        ],
        Sirajganj: [
            'Belkuchi', 'Chauhali', 'Kamarkhanda', 'Kazipur', 'Raiganj', 'Shahjadpur', 'Sirajganj Sadar', 'Tarash', 'Ullahpara'
        ]
    };

    // Function to populate thanas based on selected district
    function populateThanas(district, selectedThana = '') {
        const thanaSelect = $('#editThana');
        thanaSelect.empty().append('<option value="">Select Thana</option>');

        if (district && thanaLists[district]) {
            thanaLists[district].forEach(thana => {
                thanaSelect.append(new Option(thana, thana));
            });
            if (selectedThana) {
                thanaSelect.val(selectedThana);
            }
        }
    }

    // Open Edit Modal
    function openEditModal(editUrl) {
        showLoader();

        $.ajax({
            url: editUrl,
            method: 'GET',
            success: function (response) {
                if (response && typeof response === 'object') {
                    // Fill form fields
                    $('#editId').val(response.id);
                    $('#editContact_id').val(response.contact_id);
                    $('#editStore_id').val(response.store_id);
                    $('#editCategory_id').val(response.product_category_id);
                    $('#editName').val(response.name);
                    $('#editFatherName').val(response.father_name);
                    $('#editRole').val(response.role);
                    $('#editPhone').val(response.phone);
                    $('#editNid').val(response.nid);

                    // District and Thana handling
                    $('#editDistrict').val(response.district);
                    populateThanas(response.district, response.thana);

                    $('#editPostOffice').val(response.post_office);
                    $('#editVillage').val(response.village);
                    $('#editStatus').val(response.status.toString());

                    // Image previews
                    if (response.image) {
                        $('#editPreviewImage').attr('src', window.location.origin + '/' + response.image).show();
                    }
                    if (response.finger_print) {
                        $('#editPreviewFingerprint').attr('src', window.location.origin + '/' + response.finger_print).show();
                    }
                    if (response.signature) {
                        $('#editPreviewSignature').attr('src', window.location.origin + '/' + response.signature).show();
                    }

                    // Set up district change handler
                    $('#editDistrict').off('change').on('change', function() {
                        populateThanas(this.value);
                    });

                    $('#editModal').modal('show');
                } else {
                    console.error('Invalid response format:', response);
                    alert('Received invalid data from the server.');
                }
                hideLoader();
            },
            error: function (xhr, status, error) {
                console.error('Error fetching contact data:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                hideLoader();
                alert('Error fetching contact data. Please check the console for more information.');
            }
        });
    }

    // Update Contact Function
    function updateContact() {
        showLoader();
        const contactId = $('#editId').val();
        const updateUrl = "{{ route($role . 'contact.update', '') }}/" + contactId;
        const formData = new FormData($('#editForm')[0]);

        $.ajax({
            url: updateUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function (response) {
                $('#editModal').modal('hide');
                loadTable();
                AjaxNotifications.success(response.message);
                hideLoader();
            },
            error: function (xhr) {
                let response = xhr.responseText;
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    response = { message: 'An error occurred' };
                }

                if (xhr.status === 422) {
                    let errorMessages = [];
                    for (let field in response.errors) {
                        errorMessages = errorMessages.concat(response.errors[field]);
                    }
                    AjaxNotifications.error(errorMessages.join('<br>'));
                } else {
                    AjaxNotifications.error(response.message || 'An error occurred');
                }
                console.error('Error updating contact:', response);
                hideLoader();
            }
        });
    }

    // Image preview handlers for edit modal
    $('#editImage').on('change', function() {
        showPreview(this, 'editPreviewImage');
    });

    $('#editFingerPrint').on('change', function() {
        showPreview(this, 'editPreviewFingerprint');
    });

    $('#editSignature').on('change', function() {
        showPreview(this, 'editPreviewSignature');
    });

    // Reuse the showPreview function from your add modal
    function showPreview(input, previewId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    }
</script>