<!--include "wp-content/plugins/whatsapp-payment/tabs_pro.php";-->
<!--include "wp-content/plugins/whatsapp-payment/whatsapp_tab.php";-->
<!--Messenger-TAB-4-script-->
<!--https://www.lamoga.de/popup_chatten_02/?fl_builder-->
<?php
global $pts_settings0;
?>

<script>
    let credit_setting="<?php echo $pts_settings0['chatanfrage_mindestguthaben'];?>";
    let isChecked=false;
    setInterval(function(){
        jQuery(".status_message").hide();
        jQuery(".credit_message").hide();
        let credit=document.getElementById("kontostand_1");
        if (credit==null)
            return;
        credit=parseInt(credit.innerHTML.replace(",",""));        //97480
        let status=document.getElementById("beraterstatus_xx_1").textContent; //OFFLINE
        if (credit_setting>credit){
            jQuery(".credit_message").show();
            jQuery(".whatsapp-button").addClass('button_disabled');
        }
        if (status=='OFFLINE'){
            jQuery(".status_message").show();
            jQuery(".whatsapp-button").addClass('button_disabled');
        }
        if (!isChecked)
            jQuery(".whatsapp-button").addClass('button_disabled');
    }, 3000);

    jQuery(document).ready(function($){
        // now you can use jQuery code here with $ shortcut formatting
        // this will execute after the document is fully loaded
        // anything that interacts with your html should go here
        let url = 'https://www.lomago.io/whatsapp/api/wp/check';
        console.log(url);
        jQuery.get(url,
            function (response) {
                console.log(response);
                if (response.whatsapp==1){
                    jQuery(".check_message").hide();
                    isChecked=true;
                }
            });
    });

</script>

<style>
    .button_disabled {
        -webkit-filter: grayscale(100%);
        -moz-filter: grayscale(100%);
        -o-filter: grayscale(100%);
        -ms-filter: grayscale(100%);
        filter: grayscale(100%);
        pointer-events: none;
    }
</style>

