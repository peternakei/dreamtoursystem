<script>
    jQuery(document).ready(function() {
        jQuery('#category').select2({
            dropdownParent: $("#create-faq-modal")
        });
    });

    function getEditDetails(faq) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('faqs.edit', ':id') }}";
        url = urlB.replace(':id', faq);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: faq
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('faqs.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-faq-modal').modal('show');
                    jQuery('#faq_question').val(result.data.question);
                    jQuery('#faq_answer').val(result.data.answer);
                    jQuery('#faq_order').val(result.data.order);
                    jQuery('#hidden_input').val(result.html);

                    let selectedCategory = result.data.faq_category_id;
                    let dataArrCategory = result.categories;
                    if (dataArrCategory.length) {
                        $.each(result.categories, function(index, value) {

                            let category = value.name;
                            let categoryID = value.id;

                            if (jQuery('#faq_category').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#faq_category').append(
                                    new Option(category, categoryID)
                                );

                                jQuery('#faq_category').find("option[value='" + selectedCategory + "']").prop("selected",true)
                            }
                        });
                    } else {
                        jQuery('#faq_category').find('option')
                            .remove()
                            .end().append(new Option('No category', ''));
                    }

                    document.getElementById('editFaqDetailsForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(faq) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('faqs.edit', ':id') }}";
        url = urlB.replace(':id', faq);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: faq
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('faqs.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-faq-modal').modal('show');
                    jQuery('#delete_faq_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteFaqForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
