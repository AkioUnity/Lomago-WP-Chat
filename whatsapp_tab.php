<script>
    function call_whatsapp(id, name,sbid,mobilenumber_1,price) {
        // alert ("CREDIT "+kontostand+ "  STATUS ");
        console.log("whatsapp " + id + ":" + name);
        jQuery(".whatsapp_loading").show();
        let ajax_url ="<?php echo admin_url('admin-ajax.php'); ?>";
        jQuery.post(
            ajax_url,
            {
                action: "whatsapp_request",
                consultant_id: id,
                consultant_name: name,
                sbid:sbid,
                mobilenumber_1:mobilenumber_1
            },
            function (res) {
                console.log(res);
                jQuery(".whatsapp_message").html(res.message);
                if (!res.error){
                    res.data.step = 1;
                    res.data.price=price;
                    // call_bot(res.data);
                }
                jQuery(".whatsapp_loading").hide();
            });
    }

    function call_bot(data) {
        let url = 'https://www.lomago.io/whatsapp/api/users/wp';
        console.log(data.step);
        jQuery.post(url, data,
            function (response) {
                console.log(response);
            });
        if (data.step < 3) {
            data.step++;
            setTimeout(function(){call_bot(data)}, 3000);
        }
    }

</script>
