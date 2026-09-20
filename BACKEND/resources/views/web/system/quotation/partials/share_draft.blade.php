@php
    $share = $share ?? null;
    if (!is_array($share)) {
        return;
    }

    $publicLinkEnabled = !empty($share['public_link_enabled']);
    $publicProposalUrl = $share['public_proposal_url'] ?? '';
    $publicPdfUrl = $share['public_pdf_url'] ?? '';
    $guestEmail = $share['guest_email'] ?? '';
    $guestName = $share['guest_name'] ?? 'Guest';
    $emailSubject = $share['email_subject'] ?? '';
    $referenceNumber = $share['reference_number'] ?? '';
    $proposalTitle = $share['proposal_title'] ?? 'Safari proposal';
@endphp

<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(0, 151, 220, 0.1); color: #0097DC;">
                    <i class="uil uil-share-alt" style="font-size: 1.3rem;"></i>
                </div>
                <div>
                    <h6 class="mb-1">Share this proposal</h6>
                    @if ($publicLinkEnabled)
                        <p class="mb-0 small text-muted">Choose what to include, then send by email or WhatsApp.</p>
                    @else
                        <p class="mb-0 small text-muted">Turn on the public link in the builder to share guest URLs.</p>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if ($publicLinkEnabled)
                    <span class="badge rounded-pill" style="background: rgba(0, 151, 220, 0.12); color: #0097DC;">Public link is on</span>
                @else
                    <a href="{{ route('quotation_versions.edit', $version->uuid) }}" class="btn btn-sm btn-outline-secondary">Enable in builder</a>
                @endif

                <div class="position-relative" id="quote-share-wrap">
                    <button type="button" class="btn text-white d-inline-flex align-items-center gap-2 @if (!$publicLinkEnabled) disabled @endif" id="quote-share-toggle" style="background: #AC5526; border-radius: 10px;" @if (!$publicLinkEnabled) disabled @endif>
                        <i class="uil uil-share-alt"></i> Share Proposal
                    </button>

                    @if ($publicLinkEnabled)
                        <div class="card shadow border-0 d-none position-absolute end-0 mt-2" id="quote-share-menu" style="min-width: 320px; z-index: 30; border-radius: 14px; overflow: hidden;">
                            <div class="p-3 border-bottom" style="background: #f8fafb;">
                                <div class="small fw-semibold text-muted text-uppercase mb-2" style="letter-spacing: 0.05em; font-size: 0.7rem;">Include in message</div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" id="share-include-link" checked data-share-toggle>
                                    <label class="form-check-label small" for="share-include-link">Public proposal link</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="share-include-pdf" checked data-share-toggle>
                                    <label class="form-check-label small" for="share-include-pdf">Direct PDF link</label>
                                </div>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3" id="quote-share-email" data-share-channel="email">
                                    <i class="uil uil-envelope" style="font-size: 1.1rem; color: #AC5526;"></i>
                                    <span>
                                        <span class="d-block fw-semibold">Send by Email</span>
                                        <span class="small text-muted">Opens your default mail app</span>
                                    </span>
                                </a>
                                <a href="#" target="_blank" rel="noopener noreferrer" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3" id="quote-share-whatsapp" data-share-channel="whatsapp">
                                    <i class="uil uil-whatsapp" style="font-size: 1.1rem; color: #25D366;"></i>
                                    <span>
                                        <span class="d-block fw-semibold">Send on WhatsApp</span>
                                        <span class="small text-muted">Opens WhatsApp with prefilled text</span>
                                    </span>
                                </a>
                                <button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3" id="quote-share-copy">
                                    <i class="uil uil-link" style="font-size: 1.1rem; color: #0097DC;"></i>
                                    <span class="fw-semibold">Copy selected link(s)</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if ($publicLinkEnabled)
    <script>
        (function () {
            var toggle = document.getElementById('quote-share-toggle');
            var menu = document.getElementById('quote-share-menu');
            var wrap = document.getElementById('quote-share-wrap');
            if (!toggle || !menu || !wrap) return;

            var ctx = {
                guestName: @json($guestName),
                guestEmail: @json($guestEmail),
                subject: @json($emailSubject),
                reference: @json($referenceNumber),
                title: @json($proposalTitle),
                publicUrl: @json($publicProposalUrl),
                pdfUrl: @json($publicPdfUrl)
            };

            var includeLink = document.getElementById('share-include-link');
            var includePdf = document.getElementById('share-include-pdf');

            function selectedLinks() {
                var links = [];
                if (includeLink && includeLink.checked && ctx.publicUrl) {
                    links.push({ label: 'Interactive proposal', url: ctx.publicUrl });
                }
                if (includePdf && includePdf.checked && ctx.pdfUrl) {
                    links.push({ label: 'Direct PDF download', url: ctx.pdfUrl });
                }
                return links;
            }

            function buildEmailBody() {
                var links = selectedLinks();
                var lines = [
                    'Hi ' + ctx.guestName + ',',
                    '',
                    'Your ' + ctx.title + ' proposal (reference ' + ctx.reference + ') is ready.',
                    ''
                ];
                links.forEach(function (l) {
                    lines.push(l.label + ':');
                    lines.push(l.url);
                    lines.push('');
                });
                if (links.length === 0) {
                    lines.push('Please reply to this email and we will share the proposal links shortly.');
                    lines.push('');
                }
                lines.push('If anything needs adjusting, just reply to this email.');
                lines.push('');
                lines.push('Kind regards.');
                return lines.join('\n');
            }

            function buildWhatsappText() {
                var links = selectedLinks();
                var lines = ['Hi ' + ctx.guestName + ' — your ' + ctx.title + ' proposal (' + ctx.reference + ') is ready.'];
                links.forEach(function (l) {
                    lines.push(l.url);
                });
                if (links.length === 0) {
                    lines.push('We will share the link shortly.');
                }
                return lines.join('\n');
            }

            function emailHref() {
                return 'mailto:' + (ctx.guestEmail || '')
                    + '?subject=' + encodeURIComponent(ctx.subject)
                    + '&body=' + encodeURIComponent(buildEmailBody());
            }

            function whatsappHref() {
                return 'https://wa.me/?text=' + encodeURIComponent(buildWhatsappText());
            }

            function copyText(text) {
                if (!text) return;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function () {
                        alert('Copied to clipboard.');
                    }).catch(function () {
                        window.prompt('Copy:', text);
                    });
                } else {
                    window.prompt('Copy:', text);
                }
            }

            toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                menu.classList.toggle('d-none');
            });
            document.addEventListener('click', function (event) {
                if (!wrap.contains(event.target)) {
                    menu.classList.add('d-none');
                }
            });
            menu.addEventListener('click', function (event) {
                event.stopPropagation();
            });

            var emailBtn = document.getElementById('quote-share-email');
            if (emailBtn) {
                emailBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    window.location.href = emailHref();
                    menu.classList.add('d-none');
                });
            }

            var whatsappBtn = document.getElementById('quote-share-whatsapp');
            if (whatsappBtn) {
                whatsappBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    window.open(whatsappHref(), '_blank', 'noopener,noreferrer');
                    menu.classList.add('d-none');
                });
            }

            var copyBtn = document.getElementById('quote-share-copy');
            if (copyBtn) {
                copyBtn.addEventListener('click', function () {
                    var links = selectedLinks();
                    if (links.length === 0) {
                        alert('Pick at least one link to copy.');
                        return;
                    }
                    copyText(links.map(function (l) { return l.url; }).join('\n'));
                    menu.classList.add('d-none');
                });
            }
        })();
    </script>
@endif
