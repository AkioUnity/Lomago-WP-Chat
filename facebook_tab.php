<!--Messenger-TAB-facebook-->
<!--include "wp-content/plugins/whatsapp-payment/facebook_tab.php";-->

<p class='facebook_message'></p>
<a class='facebook_messenger' href="http://m.me/106704524351660/" style='display: none' target="_blank">
    facebook messenger
</a>
<script>
    function call_facebook(id, name, sbid, mobilenumber_1) {
        console.log("facebook  " + id + ":" + name);
        jQuery(".facebook_loading").show();
        let ajax_url ="<?php echo admin_url('admin-ajax.php'); ?>";
        jQuery.post(
            ajax_url,
            {
                action: "whatsapp_request",
                consultant_id: id,
                consultant_name: name,
                sbid: sbid,
                mobilenumber_1: mobilenumber_1,
                type: 'facebook'
            },
            function (res) {
                console.log(res);
                jQuery(".facebook_message").html(res.message);
                if (!res.error) {
                    jQuery(".facebook_messenger").show();
                }
                jQuery(".facebook_loading").hide();
            });
    }
</script>
