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
                            <span class="badge bg-soft-primary text-primary fs-6">
                                {{ $groupedCarts->sum('item_count') }} items from {{ $groupedCarts->count() }} users
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($groupedCarts->count() == 0)
                            <div class="text-center py-5">
                                <i class="ri-shopping-cart-line" style="font-size:48px;color:#ccc"></i>
                                <p class="text-muted mt-3">No cart items yet</p>
                            </div>
                        @endif

                        @foreach($groupedCarts as $userId => $group)
                        <div class="card border mb-4 shadow-sm" id="user-card-{{ $userId }}">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    @if($group->user && $group->user->getFirstMedia('profile_image'))
                                        <img src="{{ $group->user->getFirstMedia('profile_image')->getUrl() }}" class="rounded-circle me-3" style="width:40px;height:40px;object-fit:cover">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;font-size:16px">
                                            {{ $group->user ? strtoupper(substr($group->user->first_name, 0, 1)) : '?' }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $group->user ? $group->user->first_name . ' ' . $group->user->last_name : 'Unknown User' }}</h6>
                                        <small class="text-muted">{{ $group->user ? $group->user->email : '' }}</small>
                                        @if($group->user && $group->user->contact_number)
                                            <small class="text-muted ms-2"><i class="ri-phone-line"></i> {{ $group->user->contact_number }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary rounded-pill">{{ $group->item_count }} {{ $group->item_count == 1 ? 'item' : 'items' }}</span>
                                    <h5 class="mb-0 mt-1 text-primary" id="user-total-{{ $userId }}">{{ getPriceFormat($group->total) }}</h5>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:50%">Service</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center" style="width:140px">Qty</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-center" style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group->items as $item)
                                        <tr id="cart-row-{{ $item->id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->service)
                                                        @php $media = $item->service->getFirstMedia('service_attachment'); @endphp
                                                        @if($media)
                                                            <img src="{{ $media->getUrl() }}" class="rounded me-2" style="width:45px;height:45px;object-fit:cover">
                                                        @endif
                                                        <div>
                                                            <strong>{{ $item->service->name }}</strong>
                                                            @if($item->discount > 0)
                                                                <br><small class="text-success">{{ $item->discount }}% off</small>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Deleted service</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($item->discount > 0)
                                                    <small class="text-decoration-line-through text-muted">{{ getPriceFormat($item->price) }}</small><br>
                                                @endif
                                                {{ getPriceFormat($item->effective_price) }}
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <button class="btn btn-sm btn-outline-primary rounded-circle" style="width:30px;height:30px;padding:0" onclick="updateQty({{ $item->id }}, {{ $item->quantity - 1 }}, {{ $userId }})">
                                                        <i class="fas fa-minus" style="font-size:10px"></i>
                                                    </button>
                                                    <span class="fw-bold mx-2" id="qty-{{ $item->id }}" style="min-width:20px;display:inline-block;text-align:center">{{ $item->quantity }}</span>
                                                    <button class="btn btn-sm btn-outline-primary rounded-circle" style="width:30px;height:30px;padding:0" onclick="updateQty({{ $item->id }}, {{ $item->quantity + 1 }}, {{ $userId }})">
                                                        <i class="fas fa-plus" style="font-size:10px"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-end align-middle">
                                                <strong class="text-primary" id="line-total-{{ $item->id }}">{{ getPriceFormat($item->line_total) }}</strong>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCart({{ $item->id }}, {{ $userId }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
    <script>
    function updateQty(cartId, qty, userId) {
        if (qty < 1) {
            deleteCart(cartId, userId);
            return;
        }
        $.ajax({
            url: '/cart-admin/' + cartId,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', _method: 'PUT', quantity: qty },
            success: function(res) {
                if (res.status) {
                    location.reload();
                }
            },
            error: function() {
                alert('Failed to update quantity');
            }
        });
    }

    function deleteCart(cartId, userId) {
        if (confirm('Remove this item from cart?')) {
            $.ajax({
                url: '/cart-admin/' + cartId,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function(res) {
                    if (res.status) {
                        if (res.remaining == 0) {
                            $('#user-card-' + userId).fadeOut(300, function() { $(this).remove(); });
                        } else {
                            location.reload();
                        }
                    }
                },
                error: function() {
                    alert('Failed to delete item');
                }
            });
        }
    }
    </script>
    @endsection
</x-master-layout>
