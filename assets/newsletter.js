$('.ipWidget-Newsletter form').on('ipSubmitResponse.Newsletter', function(e, response) {
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

        $('.ipsSend').off('click').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var data  = $this.data();
            openSendDialog(data);
        });

    }


    var preview = function (data) {


        var id = data.id;
        var emailSubject = data.emailsubject;
        var emailText = data.emailtext;

        var $modal = $('#newsletterPreview');
        if (!$modal.length) {
            $("body").append($(newsletterPreviewTemplate));
            $modal = $('#newsletterPreview');
        }

        $modal.find('.modal-body').html(emailText);

        $modal.find('.ipsPreviewSend').data(data);

        $('.ipsPreviewSend').off('click').on('click', function(e) {

            e.preventDefault();
            var $this = $(this);
            var data  = $this.data();
            openSendDialog(data);
        });

        $modal.modal();
    }

    var openSendDialog = function (data){

        var emailSubject = data.emailsubject;

        var confirmed=window.confirm("Do you want to send email messages?\n\nNewsletter subject: \n" + data.emailsubject);

        if (confirmed){
            send(data);
        }else{
            alert("Sending canceled");
        }
    }

    var send = function (data) {

        var id = data.id;
        var emailSubject = data.emailsubject;
        var emailText = data.emailtext;
        var langCode= data.langCode;

        $.ajax({
            url: ip.baseUrl, //we assume that for already has m, g, a parameters which will lead this request to required controller
            dataType: 'json',
            type : 'POST',
            data: {
                aa: 'Newsletter.send',
                id: id,
                emailSubject: emailSubject,
                emailText: emailText,
                langCode: langCode,
                securityToken: ip.securityToken
            },
            success: function (response) {
                //do nothing
                alert(response.message);
            }
        });

    }


};

$( document ).ready(function() {
    $(document).on('init.ipGrid.Newsletter', function() {
        NewsletterAdmin.init();
    });


});
