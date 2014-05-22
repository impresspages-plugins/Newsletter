$('.ipWidget-Newsletter form').on('ipSubmitResponse', function(e, response) {
    var $this = $(this);
    var $widget = $this.closest('.ipWidget-Newsletter');

    if (response.status == 'ok') {
        $widget.find('.ipsNewsletterForm').addClass('hidden');
        $widget.find('.ipsThankYou').removeClass('hidden');
    }
});


var NewsletterAdmin = new function() {
    "use strict";
    this.init = function() {
        $('.ipsPreview').off('click').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var data  = $this.data();
            preview(data);
        });
    }


    var preview = function (data) {

//        newsletterPreviewTemplate.find('modal-body');

        var id = data.id;
        var emailSubject = data.emailsubject;
        var emailText = data.emailtext;

        var $modal = $('#newsletterPreview');
        if (!$modal.length) {
            $("body").append($(newsletterPreviewTemplate));
            $modal = $('#newsletterPreview');
        }

        $modal.find('.modal-body').html(emailText);
        $modal.modal();
    }


};

$( document ).ready(function() {
    $(document).on('init.ipGrid.Newsletter', function() {
        NewsletterAdmin.init();
    });


});