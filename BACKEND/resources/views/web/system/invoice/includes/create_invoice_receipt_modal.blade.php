<div id="post-invoice-payment-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Post Invoice <span
                        class="text-info">{{ $invoice->invoice_number }}</span> Payments</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('invoices.post_payments', $invoice->uuid) }}" id="postInvoicePaymentForm"
                name="postInvoicePaymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to post invoice payment</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="payment_mode" class="form-label">Payment Mode</label>
                                        <select class="form-control select2" id="payment_mode" name="payment_mode"
                                            data-toggle="select2">
                                            <optgroup label="Payment Mode">
                                                <option>Select type</option>
                                                @foreach ($paymentTypes as $type)
                                                    <option value="{{ $type['id'] }}">
                                                        {{ $type['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_payment_mode"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="invoice_amount" class="form-label">Invoice Amount</label>
                                        <input type="number" min="1" id="invoice_amount"
                                            value="{{ $invoice->total_amount - ($invoice->payments->sum('amount') ?? 0) }}" name="invoice_amount"
                                            placeholder="Enter invoice amount" class="form-control" required disabled>
                                        <small class="text-danger" id="error_invoice_amount"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="receipt_date" class="form-label">Receipt Date</label>
                                        <input type="date" id="receipt_date" name="receipt_date"
                                            placeholder="Enter receipt date" class="form-control" required>
                                        <small class="text-danger" id="error_receipt_date"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12" id="bank_sec" style="display: none;">
                                    <div class="mb-2">
                                        <label for="bank" class="form-label">Bank</label>
                                        <select class="form-control select2" id="bank" name="bank"
                                            data-toggle="select2">
                                            <optgroup label="bank">
                                                <option>Select bank</option>
                                                @foreach ($banks as $bank)
                                                    <option value="{{ $bank['id'] }}">
                                                        {{ $bank['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_bank"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6" id="cheque_sec" style="display: none;">
                                    <div class="mb-2">
                                        <label for="cheque_no" class="form-label">Cheque No</label>
                                        <input type="text" id="cheque_no" name="cheque_no"
                                            placeholder="Enter cheque no" class="form-control" required>
                                        <small class="text-danger" id="error_cheque_no"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6" id="cheque_date_sec" style="display: none;">
                                    <div class="mb-2">
                                        <label for="cheque_date" class="form-label">Cheque Date</label>
                                        <input type="date" id="cheque_date" name="cheque_date"
                                            placeholder="Enter cheque date" class="form-control" required>
                                        <small class="text-danger" id="error_cheque_date"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="amount" class="form-label">Amount Paid</label>
                                        <input type="number" min="1" id="amount" name="amount"
                                            placeholder="Enter amount" class="form-control" required>
                                        <small class="text-danger" id="error_amount"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="currency" class="form-label">Currency</label>
                                        <input type="text" id="currency"
                                            value="{{ $invoice->currency->short_name }}" name="currency"
                                            placeholder="Enter invoice currency" class="form-control" disabled
                                            required>
                                        <small class="text-danger" id="error_currency"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-2">
                                        <label for="remarks" class="form-label">Remarks</label>
                                        <textarea rows="3" id="remarks" name="remarks" placeholder="Enter remarks" class="form-control" required></textarea>
                                        <small class="text-danger" id="error_remarks"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('postInvoicePaymentForm')"
                        class="btn btn-success saveBtn">Create Receipt</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
