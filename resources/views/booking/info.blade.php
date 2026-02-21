@php
$sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
$datetime = $sitesetup ? json_decode($sitesetup->value) : null;
$extraCharges = $bookingdata->bookingExtraCharge->count() > 0 ? $bookingdata->getExtraChargeValue() : 0;
$addonTotalPrice = $bookingdata->bookingAddonService->count() > 0 ? $bookingdata->bookingAddonService->sum('price') : 0;
@endphp
{{ html()->hidden('id',$bookingdata->id ?? null) }}

<div class="container-fluid">
    {{-- ===== TOP HEADER BAR ===== --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="mb-0 text-primary fw-bold">Booking #{{ $bookingdata->id }}</h3>
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'accept' => 'info',
                            'ongoing' => 'primary',
                            'in_progress' => 'primary',
                            'hold' => 'secondary',
                            'cancelled' => 'danger',
                            'rejected' => 'danger',
                            'completed' => 'success',
                        ];
                        $badgeColor = $statusColors[$bookingdata->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $badgeColor }} fs-6 px-3 py-2">{{ ucfirst(str_replace('_', ' ', $bookingdata->status)) }}</span>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if($bookingdata->handymanAdded->count() == 0 && $bookingdata->status !== 'cancelled')
                        @hasanyrole('admin|demo_admin|provider')
                        <a href="{{ route('booking.assign_form',['id'=> $bookingdata->id ]) }}" class="btn btn-outline-primary btn-sm loadRemoteModel">
                            <i class="ri-user-add-line me-1"></i>Assign Handyman
                        </a>
                        @endhasanyrole
                    @endif
                    @if($bookingdata->payment_id !== null)
                        <a href="{{ route('invoice_pdf',$bookingdata->id) }}" class="btn btn-primary btn-sm" target="_blank">
                            <i class="ri-file-text-line me-1"></i>Download Invoice
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- ===== LEFT COLUMN ===== --}}
        <div class="col-lg-8">
            {{-- Service Details Card --}}
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-service-line me-2"></i>Service Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Service</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end pe-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($bookingdata->bookingServiceItems) && $bookingdata->bookingServiceItems->count() > 0)
                                    {{-- Multi-service (cart) booking --}}
                                    @php $cartTotal = 0; @endphp
                                    @foreach($bookingdata->bookingServiceItems as $item)
                                    @php $cartTotal += $item->total_price; @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($item->service && $item->service->getFirstMedia('service_attachment'))
                                                    <img src="{{ $item->service->getFirstMedia('service_attachment')->getUrl() }}"
                                                        alt="{{ $item->service_name }}" class="rounded"
                                                        style="width: 36px; height: 36px; object-fit: cover;">
                                                @endif
                                                <strong>{{ $item->service_name }}</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ getPriceFormat($item->unit_price) }}</td>
                                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->quantity }}</span></td>
                                        <td class="text-end pe-3"><strong>{{ getPriceFormat($item->total_price) }}</strong></td>
                                    </tr>
                                    @endforeach
                                @else
                                    {{-- Single service booking --}}
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($bookingdata->service && $bookingdata->service->getFirstMedia('service_attachment'))
                                                    <img src="{{ $bookingdata->service->getFirstMedia('service_attachment')->getUrl() }}"
                                                        alt="{{ optional($bookingdata->service)->name }}" class="rounded"
                                                        style="width: 36px; height: 36px; object-fit: cover;">
                                                @endif
                                                <strong>{{ optional($bookingdata->service)->name ?? '-' }}</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ getPriceFormat($bookingdata->amount) }}</td>
                                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $bookingdata->quantity ?? 1 }}</span></td>
                                        <td class="text-end pe-3"><strong>{{ getPriceFormat($bookingdata->amount * ($bookingdata->quantity ?? 1)) }}</strong></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Billing Summary Card --}}
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-bill-line me-2"></i>Billing Summary</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            @if($bookingdata->bookingPackage == null && $bookingdata->discount > 0)
                            <tr>
                                <td class="text-muted">Discount <span class="text-success">({{ $bookingdata->discount }}% off)</span></td>
                                <td class="text-end text-success">- {{ getPriceFormat($bookingdata->final_discount_amount) }}</td>
                            </tr>
                            @endif
                            @if($bookingdata->couponAdded != null)
                            <tr>
                                <td class="text-muted">Coupon <span class="badge bg-success-subtle text-success">{{ $bookingdata->couponAdded->code }}</span></td>
                                <td class="text-end text-success">- {{ getPriceFormat($bookingdata->final_coupon_discount_amount) }}</td>
                            </tr>
                            @endif
                            @if($extraCharges > 0)
                            <tr>
                                <td class="text-muted">Extra Charges</td>
                                <td class="text-end">+ {{ getPriceFormat($extraCharges) }}</td>
                            </tr>
                            @endif
                            @if($addonTotalPrice > 0)
                            <tr>
                                <td class="text-muted">Add-ons</td>
                                <td class="text-end">+ {{ getPriceFormat($addonTotalPrice) }}</td>
                            </tr>
                            @endif
                            @if($bookingdata->final_total_tax > 0)
                            <tr>
                                <td class="text-muted">Tax</td>
                                <td class="text-end text-danger">+ {{ getPriceFormat($bookingdata->final_total_tax) }}</td>
                            </tr>
                            @endif
                            <tr class="border-top">
                                <td><strong class="fs-5">Total Amount</strong></td>
                                <td class="text-end"><strong class="fs-5 text-primary">{{ getPriceFormat($bookingdata->total_amount) }}</strong></td>
                            </tr>
                            @if($bookingdata->service->is_enable_advance_payment == 1)
                            <tr>
                                <td class="text-muted">Advance Paid ({{ $bookingdata->service->advance_payment_amount }}%)</td>
                                <td class="text-end">{{ getPriceFormat($bookingdata->advance_paid_amount) }}</td>
                            </tr>
                            @if($bookingdata->status !== 'cancelled')
                            <tr>
                                <td class="text-muted">
                                    Remaining Amount
                                    @if($payment != null && $payment->payment_status !== 'paid')
                                    <span class="badge bg-warning-subtle text-warning">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($payment != null && $payment->payment_status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        {{ getPriceFormat($bookingdata->total_amount - $bookingdata->advance_paid_amount) }}
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Extra Charges Table --}}
            @if(count($bookingdata->bookingExtraCharge) > 0)
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-add-circle-line me-2"></i>Extra Charges</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Title</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end pe-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookingdata->bookingExtraCharge as $charge)
                                <tr>
                                    <td class="ps-3">{{ $charge->title }}</td>
                                    <td class="text-center">{{ getPriceFormat($charge->price) }}</td>
                                    <td class="text-center">{{ $charge->qty }}</td>
                                    <td class="text-end pe-3"><strong>{{ getPriceFormat($charge->price * $charge->qty) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Service Add-ons Table --}}
            @if($bookingdata->bookingAddonService->count() > 0)
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-puzzle-line me-2"></i>Service Add-ons</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Add-on Name</th>
                                    <th class="text-end pe-3">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookingdata->bookingAddonService as $addonservice)
                                <tr>
                                    <td class="ps-3"><strong>{{ $addonservice->name }}</strong></td>
                                    <td class="text-end pe-3">{{ getPriceFormat($addonservice->price) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- ===== RIGHT COLUMN ===== --}}
        <div class="col-lg-4">
            {{-- Booking Info Card --}}
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-information-line me-2"></i>Booking Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Booking Date</label>
                        <p class="mb-0 fw-semibold">{{ date("$datetime->date_format $datetime->time_format", strtotime($bookingdata->date)) ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Placed On</label>
                        <p class="mb-0">{{ date("$datetime->date_format $datetime->time_format", strtotime($bookingdata->created_at)) ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Booking Status</label>
                        @hasanyrole('admin|demo_admin')
                        <select class="form-select form-select-sm bookingstatus" data-id="{{ $bookingdata->id }}">
                            @foreach(App\Models\BookingStatus::whereIn('value', ['pending', 'accept', 'completed', 'cancelled'])->get() as $status)
                                <option value="{{ $status->value }}" {{ $bookingdata->status == $status->value ? 'selected' : '' }}>{{ $status->label }}</option>
                            @endforeach
                        </select>
                        @else
                        <p class="mb-0 text-primary fw-semibold">{{ App\Models\BookingStatus::bookingStatus($bookingdata->status) }}</p>
                        @endhasanyrole
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Payment Status</label>
                        @hasanyrole('admin|demo_admin')
                        <select class="form-select form-select-sm paymentStatus" data-id="{{ $bookingdata->id }}">
                            <option value="pending" {{ (isset($payment) && $payment->payment_status == 'pending') ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ (isset($payment) && $payment->payment_status == 'paid') ? 'selected' : '' }}>Paid</option>
                        </select>
                        @else
                        @if(isset($payment) && $payment->payment_status)
                            @php
                                $statusClass = match($payment->payment_status) {
                                    'paid', 'advanced_paid' => 'text-success',
                                    'Advanced Refund' => 'text-warning',
                                    default => 'text-danger',
                                };
                            @endphp
                            <p class="mb-0 {{ $statusClass }} fw-semibold">{{ str_replace('_', ' ', ucfirst($payment->payment_status)) }}</p>
                        @else
                            <p class="mb-0 text-danger">Pending</p>
                        @endif
                        @endhasanyrole
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small">Payment Method</label>
                        <p class="mb-0 fw-semibold">{{ isset($payment) ? ucfirst($payment->payment_type) : '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Customer Card --}}
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-user-3-line me-2"></i>Customer</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ getSingleMedia($bookingdata->customer,'profile_image', null) }}"
                            alt="Customer" class="rounded-circle"
                            style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0">{{ optional($bookingdata->customer)->display_name ?? '-' }}</h6>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center mb-2">
                            <i class="ri-phone-line me-2 text-muted"></i>
                            <a href="tel:{{ optional($bookingdata->customer)->contact_number }}" class="text-body">
                                {{ optional($bookingdata->customer)->contact_number ?? '-' }}
                            </a>
                        </li>
                        @if($bookingdata->phone_number)
                        <li class="d-flex align-items-center mb-2">
                            <i class="ri-smartphone-line me-2 text-muted"></i>
                            <a href="tel:{{ $bookingdata->phone_number }}" class="text-body">
                                {{ $bookingdata->phone_number }} <small class="text-muted">(Booking)</small>
                            </a>
                        </li>
                        @endif
                        @if($bookingdata->address)
                        <li class="d-flex align-items-start mb-0">
                            <i class="ri-map-pin-line me-2 text-muted mt-1"></i>
                            <span class="text-wrap">{{ $bookingdata->address }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Cancellation Info --}}
            @if($bookingdata->status === 'cancelled')
            <div class="card mb-3 border-danger">
                <div class="card-header bg-danger-subtle">
                    <h5 class="mb-0 text-danger"><i class="ri-close-circle-line me-2"></i>Cancellation</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $bookingdata->reason ?? 'No reason provided' }}</p>
                    @if($bookingdata->service->is_enable_advance_payment == 1 && $bookingdata->advance_paid_amount > 0)
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Cancellation Charge ({{ $bookingdata->cancellation_charge }}%)</span>
                            <strong>{{ getPriceFormat($bookingdata->cancellation_charge_amount) }}</strong>
                        </div>
                        @php $refundamount = $bookingdata->advance_paid_amount - $bookingdata->cancellation_charge_amount; @endphp
                        @if($refundamount > 0)
                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-muted">Refund Amount</span>
                            <strong class="text-success">{{ getPriceFormat($refundamount) }}</strong>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script>
    $(document).on('change', '.bookingstatus', function() {
        var status = $(this).val();
        var id = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('bookingStatus.update') }}",
            data: { 'status': status, 'bookingId': id },
            success: function(data) { window.location.reload(); }
        });
    });

    $(document).on('change', '.paymentStatus', function() {
        var status = $(this).val();
        var id = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('bookingStatus.update') }}",
            data: { 'status': status, 'bookingId': id, 'type': 'payment' },
            success: function(data) { window.location.reload(); }
        });
    });
</script>
