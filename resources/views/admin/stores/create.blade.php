@extends('layouts.admin')

@section('title', 'Add Stores')

@section('content')
    <x-breadcumb title="Add Project"/>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-text">Add New Project</h5>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="needs-validation" novalidate=""
                              action="{{ route($role . 'projects.store') }}"
                              method="POST" enctype="multipart/form-data" id="projectForm">
                            @csrf
                            @method('POST')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Project Name</label>
                                    <input class="form-control" type="text" id="name" name="name"
                                           placeholder="Project Name" required>
                                    <div class="invalid-feedback">
                                        Please provide a project name.
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label for="description" class="form-label">Project Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"
                                              required></textarea>
                                    <div class="invalid-feedback">
                                        Please provide a project description.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="staff" class="form-label">Assign Project to Staff</label>
                                    <select class="form-select" id="staff" name="staff_id" required>
                                        <option value="">Select staff member</option>
                                        @foreach($staffMembers as $key=>$staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a staff member.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label">Project Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" style="color: green;">Active</option>
                                        <option value="inactive" style="color: red;">Inactive</option>
                                        <option value="hold" style="color: orange;">On Hold</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a project status.
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label for="file-dropzone" class="form-label">File Upload</label>
                                    <div class="dropzone" id="file-dropzone"></div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Create Project</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css"/>
    <style>
        .dropzone {
            border: 2px dashed #0087F7;
            border-radius: 5px;
            background: white;
        }
        .dropzone .dz-message {
            font-weight: 400;
        }
        .dropzone .dz-message .note {
            font-size: 0.8em;
            font-weight: 200;
            display: block;
            margin-top: 1.4rem;
        }
    </style>
@endpush

@push('script')
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        let myDropzone = new Dropzone("#file-dropzone", {
            url: "{{ route($role . 'projects.store') }}",
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 5,
            maxFiles: 5,
            maxFilesize: 5,
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf,.doc,.docx",
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            init: function() {
                let submitButton = document.querySelector("#projectForm button[type=submit]");
                let myDropzone = this;

                submitButton.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (validateForm()) {
                        if (myDropzone.getQueuedFiles().length > 0) {
                            myDropzone.processQueue();
                        } else {
                            document.querySelector("#projectForm").submit();
                        }
                    }
                });

                this.on("sendingmultiple", function(files, xhr, formData) {
                    let form = document.querySelector("#projectForm");
                    let data = new FormData(form);

                    for (let pair of data.entries()) {
                        formData.append(pair[0], pair[1]);
                    }
                });

                this.on("successmultiple", function(files, response) {
                    window.location.href = "{{ route($role . 'projects.index') }}";
                });

                this.on("errormultiple", function(files, response) {
                    console.error(response);
                });
            }
        });

        function validateForm() {
            let form = document.querySelector("#projectForm");
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                return false;
            }
            return true;
        }
    </script>
@endpush
