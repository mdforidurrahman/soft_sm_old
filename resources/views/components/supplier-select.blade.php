{{-- resources/views/components/supplier-select.blade.php --}}
@props(['selected' => null, 'suppliers' => []])

<div x-data="supplierSelect()">
    <select
        name="supplier_id"
        id="supplier_id"
        x-ref="select"
        class="form-select"
        required>
        <option value="">Please Select</option>
        @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}"
                {{ $selected && $selected->id == $supplier->id ? 'selected' : '' }}>
                {{ $supplier->name }}
            </option>
        @endforeach
    </select>
</div>

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('supplierSelect', () => ({
                init() {
                    // Initialize Select2
                    $(this.$refs.select).select2({
                        placeholder: 'Please Select',
                        allowClear: true,
                        ajax: {
                            url: '{{ route("api.suppliers.search") }}',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    search: params.term,
                                    page: params.page || 1
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data.data.map(supplier => ({
                                        id: supplier.id,
                                        text: supplier.name
                                    })),
                                    pagination: {
                                        more: data.next_page_url !== null
                                    }
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 2
                    });

                    // Handle Select2 events with Alpine
                    $(this.$refs.select).on('select2:select', (e) => {
                        this.$dispatch('supplier-selected', {
                            id: e.params.data.id,
                            name: e.params.data.text
                        });
                    });
                }
            }));
        });
    </script>
@endpush
