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
                                <input type="text" class="form-control" id="contact_id" name="contact_id" readonly required>
                            </div>
                        </div>
                      
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name*</label>
                                <input type="text" class="form-control" id="name" name="name"  placeholder="Example: Md. Abdur Rahman"  required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="father_name" class="form-label">Father Name*</label>
                                <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Example: Md. Abdur Rahmin" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role*</label>
                                <select name="role" class="form-control " id="role">
                                    <option value="customer" selected>Customer</option>
                                    <option value="supplier">Supplier</option>
                                </select>
                            </div>
                        </div>
                      
                      <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sales_type" class="form-label">Sales Type*</label>
                                <select name="sales_type" class="form-control " id="sales_type">
                                    <option value="">Please Select*</option>
                                    <option value="cash">Cash Sale</option>
                                    <option value="credit">Credit Sale</option>
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
                                    @forelse($categories as $key => $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nid" class="form-label">NID*</label>
                                <input type="number" class="form-control" id="nid" name="nid"  placeholder="Example: 5892450147" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone*</label>
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="Example: 01774346103" required>
                            </div>
                        </div>
                      
                     	<div class="col-md-6">
                            <div class="mb-3">
                                <label for="installment" class="form-label">Installment Amount (TK)</label>
                                <input type="number" class="form-control" id="installment" name="installment" value="0" >
                            </div>
                        </div>
                        <!-- District -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="district" class="form-label">District*</label>
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
                                <label for="thana" class="form-label">Thana/Upazila*</label>
                                <select class="form-control" id="thana" name="thana" required>
                                    <option value="">Select Thana</option>
                                </select>
                            </div>
                        </div>

                        <!-- Post Office -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="post_office" class="form-label">Post Office*</label>
                                <input type="text" class="form-control" id="post_office" name="post_office" placeholder="Example: Mirat" required>
                            </div>
                        </div>

                        <!-- Village -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="village" class="form-label">Village*</label>
                                <input type="text" class="form-control" id="village" name="village" placeholder="Example: Vabaninagar" required>
                            </div>
                        </div>
                      
                        <!-- Media Name -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="media_name" class="form-label">Media Name ( if have )</label>
                                <input type="text" class="form-control" id="media_name" name="media_name" value="N/A" >
                            </div>
                        </div>
                      
                       <!-- Media Phone Number --> 
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="media_number" class="form-label">Media Phone Number ( if have )</label>
                                <input type="text" class="form-control" id="media_number" name="media_number" value="N/A">
                            </div>
                        </div> 

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status*</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="0">Inactive</option>
                                    <option value="1" selected>Active</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Customer Leadger Image*</label>
                                <input type="file" class="form-control" id="image" name="image"
                                    accept="image/*, video/*, .pdf, .doc, .docx"   capture="environment" required>
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
                                    NID Picture*</label>
                                <input type="file" class="form-control" id="finger_print"
                                    name="finger_print" accept="image/*, video/*, .pdf, .doc, .docx" capture="environment" required>
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
                                    Picture*</label>
                                <input type="file" class="form-control" id="signature"
                                    name="signature" accept="image/*, video/*, .pdf, .doc, .docx"  capture="environment" required>
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
// Global function declaration
function submitContactForm() {
    const form = document.getElementById('contactForm');
    const formData = new FormData(form);
    const saveButton = document.querySelector('#contactModal .btn-primary'); // Get save button

    // Disable and hide save button immediately
    saveButton.disabled = true;
    saveButton.style.display = 'none';

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
            
            // Reset preview images
            document.getElementById('preview_image').style.display = 'none';
            document.getElementById('preview_fingerprint').style.display = 'none';
            document.getElementById('preview_signature').style.display = 'none';
        },
        error: function (xhr) {
            // Re-enable and show save button on error
            saveButton.disabled = false;
            saveButton.style.display = 'block';

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
        },
        complete: function() {
            // This runs after success or error
        }
    });
}

// Show modal event handler to reset button state
$('#contactModal').on('show.bs.modal', function () {
    const saveButton = document.querySelector('#contactModal .btn-primary');
    saveButton.disabled = false;
    saveButton.style.display = 'block';
});


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
            'Adamdighi', 'Dhunat', 'Gabtali', 'Kahalu', 'Shibganj', 'Sariakandi', 'Shahjahanpur', 'Dubchacia'
        ],

        Rajshahi: [
            'Boalia', 'Baghmara', 'Paba', 'Godagari', 'Charghat', 'Bagha', 'Mohanpur', 'Tanore'
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

