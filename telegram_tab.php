<p class='telegram_message'></p>
<a class='telegram_messenger' href="https://t.me/LAMOGA_Service_BOT" style='display: none' target="_blank">
    telegram messenger
</a>
<script>
    function call_telegram(id, name,sbid,mobilenumber_1) {
        console.log("telegram" + id + ":" + name);
        jQuery(".telegram_loading").show();
        let ajaxscript = {ajax_url: '//www.lamoga.de/wp-admin/admin-ajax.php'};
        jQuery.post(
            ajaxscript.ajax_url,
            {
                action: "whatsapp_request",
                consultant_id: id,
                consultant_name: name,
                sbid: sbid,
                mobilenumber_1: mobilenumber_1,
                type: 'telegram'
            },
            function (res) {
                console.log(res);
                jQuery(".telegram_message").html(res.message);
                if (!res.error) {
                    jQuery(".telegram_messenger").show();
                }
                jQuery(".telegram_loading").hide();
            });
    }

</script>
