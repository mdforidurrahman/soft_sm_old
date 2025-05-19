{{-- Add Modal --}}
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">Add New contacts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="contactForm" enctype="multipart/form-data" method="POST">

                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_id" class="form-label">Contact Id</label>
                                <input type="text" class="form-control" id="contact_id" name="contact_id" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="father_name" class="form-label">Father Name</label>
                                <input type="text" class="form-control" id="father_name" name="father_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role </label>
                                <select name="role" class="form-control " id="role">
                                    <option value="customer">Customer</option>
                                    <option value="supplier">Supplier</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="store_id" class="form-label">Store Name*</label>
                            <div class="input-group">
                                <select name="store_id" class="form-select" id="store_id" required>
                                    <option value="">Please Select</option>
                                    @forelse($storeName as $key => $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category Name*</label>
                            <div class="input-group">
                                <select name="category_id" class="form-select" id="category_id" required>
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
                                <label for="nid" class="form-label">Nid</label>
                                <input type="text" class="form-control" id="nid" name="nid" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>

                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                        <!-- District -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="district" class="form-label">District</label>
                                <select class="form-control" id="district" name="district" required>
                                    <option value="">Select District</option>
                                    <option value="Naogaon">Naogaon</option>
                                    <option value="Rajshahi">Rajshahi</option>
                                    <option value="Chapainawabganj">Chapainawabganj</option>
                                    <option value="Rangpur">Rangpur</option>
                                    <option value="Bogura">Bogura</option>
                                    <option value="Joypurhat">Joypurhat</option>
                                    <option value="Natore">Natore </option>
                                    <option value="Pabna">Pabna </option>
                                    <option value="Sirajganj">Sirajganj </option>
                                </select>
                            </div>
                        </div>

                        <!-- Thana -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="thana" class="form-label">Thana/Upazila</label>
                                <select class="form-control" id="thana" name="thana" required>
                                    <option value="">Select Thana</option>
                                </select>
                            </div>
                        </div>

                        <!-- Post Office -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="post_office" class="form-label">Post Office</label>
                                <input type="text" class="form-control" id="post_office" name="post_office" required>
                            </div>
                        </div>

                        <!-- Village -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="village" class="form-label">Village</label>
                                <input type="text" class="form-control" id="village" name="village" required>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="0">Inactive</option>
                                    <option value="1">Active</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Customer Image</label>
                                <input type="file" class="form-control" id="image" name="image"
                                    accept="image/*" required>
                                <!-- Preview Image -->
                                <div class="mt-2">
                                    <img id="preview_image" src="" alt="Preview" class="img-thumbnail"
                                        style="width: 150px; height: 150px; display: none;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="finger_print" class="form-label">Customer
                                    Fingerprint</label>
                                <input type="file" class="form-control" id="finger_print"
                                    name="finger_print" accept="image/*" required>
                                <!-- Preview Fingerprint -->
                                <div class="mt-2">
                                    <img id="preview_fingerprint" src="" alt="Preview" class="img-thumbnail"
                                        style="width: 150px; height: 150px; display: none;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="signature" class="form-label">Customer
                                    Signature</label>
                                <input type="file" class="form-control" id="signature"
                                    name="signature" accept="image/*" required>
                                <!-- Preview Signature -->
                                <div class="mt-2">
                                    <img id="preview_signature" src="" alt="Preview" class="img-thumbnail"
                                        style="width: 150px; height: 150px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitContactForm()">Save Store</button>
            </div>
        </div>
    </div>
</div>


<script>
    // Global function declaration
function submitContactForm() {
    const form = document.getElementById('contactForm');
    const formData = new FormData(form);

    $.ajax({
        url: "{{ route($role . 'contact.store') }}",
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#contactModal').modal('hide');
            $('#contactForm')[0].reset();
            loadTable();
            AjaxNotifications.success(response.message);
        },
        error: function (xhr) {
            let response = xhr.responseText;
            try {
                response = JSON.parse(response);
            } catch (e) {
                response = {
                    message: 'An error occurred'
                };
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
            console.error('Error creating contact:', response);
        }
    });
}


    //image preview
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

    // Event listeners for each input
    document.getElementById('image').addEventListener('change', function () {
        showPreview(this, 'preview_image');
    });

    document.getElementById('finger_print').addEventListener('change', function () {
        showPreview(this, 'preview_fingerprint');
    });

    document.getElementById('signature').addEventListener('change', function () {
        showPreview(this, 'preview_signature');
    });
    //IMAGE PREVIEW END



    // Event binding for form submission
    $('#contactForm').on('submit', function (e) {
        e.preventDefault();
        submitContactForm();
    });

    const thanaList = {

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

    document.getElementById('district').addEventListener('change', function () {
        const district = this.value;
        const thanaSelect = document.getElementById('thana');

        // Reset
        thanaSelect.innerHTML = '<option value="">Select Thana</option>';


        if (thanaList[district]) {
            thanaList[district].forEach(function (thana) {
                let option = new Option(thana, thana);
                thanaSelect.appendChild(option);
            });
        }
    });
</script>