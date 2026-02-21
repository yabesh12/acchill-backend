<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title mb-0"><i class="ri-shopping-cart-2-line me-2"></i>{{ $pageTitle ?? 'User Carts' }}</h4>
                        </div>
                        <div>
                            <span class="badge bg-soft-primary text-primary" id="cart-count">Loading...</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="cart-table" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Service</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Added</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
    <script>
    $(document).ready(function() {
        var table = $('#cart-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("cart-admin.index_data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'user_name', name: 'user.first_name' },
                { data: 'service_name', name: 'service.name' },
                { data: 'service_price', name: 'service_price', orderable: false, searchable: false, className: 'text-center' },
                { data: 'quantity', name: 'quantity', className: 'text-center' },
                { data: 'total', name: 'total', orderable: false, searchable: false, className: 'text-end' },
                { data: 'updated_at', name: 'updated_at', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']],
            drawCallback: function(settings) {
                $('#cart-count').text(settings._iRecordsTotal + ' items');
            }
        });
    });

    function deleteCart(id) {
        if (confirm('Remove this cart item?')) {
            $.ajax({
                url: '/cart-admin/' + id,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function(res) {
                    $('#cart-table').DataTable().ajax.reload();
                }
            });
        }
    }
    </script>
    @endsection
</x-master-layout>
