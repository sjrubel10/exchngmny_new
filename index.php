<?php
require  "main/init.php";
$conn = Db_connect();

$filename = 'managesite/siteInfo.json';
$siteInfoData = getDataFromJsonFile( $filename );
// var_test_die( $siteInfoData );
$buysale_rate = array(
        0=>array('method'=>'Skrill', 'buy_rate'=>$siteInfoData['todayDollerBuyRateskrill'], 'sale_rate'=> $siteInfoData['todayDollerSellRateskrill']),
        1=>array('method'=>'Neteller', 'buy_rate'=>$siteInfoData['todayDollerBuyRateneteller'], 'sale_rate'=> $siteInfoData['todayDollerSellRateneteller']),
        2=>array('method'=>'Perfect money', 'buy_rate'=>$siteInfoData['todayDollerBuyRateperfectMoney'], 'sale_rate'=> $siteInfoData['todayDollerSellRateperfectMoney']),
);

$today_buySell_Rate = array(
    "todayDollerBuyRateskrill" => $siteInfoData['todayDollerBuyRateskrill'],
    "todayDollerBuyRateneteller" => $siteInfoData['todayDollerBuyRateneteller'],
    "todayDollerBuyRateperfectMoney" => $siteInfoData['todayDollerBuyRateperfectMoney'],
    "todayDollerSellRateskrill" => $siteInfoData['todayDollerSellRateskrill'],
    "todayDollerSellRateneteller" => $siteInfoData['todayDollerSellRateneteller'],
    "todayDollerSellRateperfectMoney" =>  $siteInfoData['todayDollerSellRateperfectMoney']
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteInfoData['siteTitle']?></title>
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/buysellstyles.css">
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/popupmessage.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</head>
<body>
    <?php include_once "views/header.php"?>

    <!--<div id="popupContainer" class="popup-container">
        <div class="popup-card">
            <span class="close-btn" id="closePopupBtn">&times;</span>
            <h2>Popup Card Title</h2>
            <p>This is the content of the popup card.</p>
            <div class="button-container">
                <button id="btnYes">Yes</button>
                <button id="btnNo">No</button>
            </div>
        </div>
    </div>-->

    <div class="indexContentHolder">
        <div class="banner-text">
            <h1><?php echo $siteInfoData['siteTitle']?></h1>
            <p><?php echo $siteInfoData['siteDescription']?></p>
        </div>

        <div class="buySellButtonHolder" id="buySellButtonHolder">
            <div class="buysellBtnContainer">
                <div class="buyButton getBtnTypeBacground getBtnType" id="buyButton">Buy Dollar</div>
                <div class="sellButton getBtnTypeBacground getBtnType" id="sellButton">Sell Dollar</div>
            </div>
            <div class="buySellCardHolder" id="buySellCardHolder"></div>
        </div>

        <div class="dollerRateHolder">
            <h2 class="dollerRateTitleText">Todays Dollar Rate</h2>
            <div class="buySellRate">
                <table class="buyRatetable">
                    <tbody class="buyrateBody">
                        <tr class="ratetableColumnHolder">
                            <th class="ratetableColumn">Number</th>
                            <th class="ratetableColumn">Method</th>
                            <th class="ratetableColumn">Buy Rate</th>
                        </tr>
                        <?php foreach ( $buysale_rate as $key => $rate){
                            $number = $key+1;
                            ?>
                        <tr class="ratetableColumnHolder">
                            <td class="ratetableColumn"><?php echo $number;?></td>
                            <td class="ratetableColumn"><?php echo $rate['method'];?></td>
                            <td class="ratetableColumn"><?php echo $rate['buy_rate'];?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
                <table class="saleRatetable">
                    <tbody class="salerateBody">
                        <tr>
                            <th>Number</th>
                            <th>Method</th>
                            <th>Sale Rate</th>
                        </tr>
                        <?php foreach ( $buysale_rate as $key => $rate){
                            $number = $key+1;
                            ?>
                        <tr>
                            <td class=""><?php echo $number;?></td>
                            <td class=""><?php echo $rate['method'];?></td>
                            <td class=""><?php echo $rate['sale_rate'];?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>

<script src="assets/js/common.js"></script>
<script src="assets/js/index.js"></script>
<script src="assets/js/popupmessage.js"></script>
<script>
    $(document).ready(function(){

        var todatBuySellRate = <?php echo json_encode( $today_buySell_Rate )?>;
        // console.log( todatBuySellRate );
       
        
        var butSellType = '';
        let is_logged_in = <?php echo json_encode( $is_logged_in )?>;
        $("#buySellButtonHolder").on("click", ".getBtnType", function () {

            if( is_logged_in ){
                let clickedId = $(this).attr('id');
                $("#"+clickedId).addClass("selectedBuySell");
                $("#"+clickedId).removeClass("getBtnTypeBacground");
                $("#"+clickedId).siblings().removeClass("selectedBuySell");
                $("#"+clickedId).siblings().addClass("getBtnTypeBacground");
                 butSellType = $("#"+clickedId).text().trim();
                let buySellForm = get_money_exchange_form( butSellType );
                $("#buySellCardHolder").html( buySellForm );
            }else{
                let message = "Please log in before initiating any buy or sell transactions. Your account security is important to us, and logging in ensures a safe and seamless experience. Thank you!";
                showPopUpMessage( message, false , false, false, false );
                // alert( "For Buy Or Sale Any Type Of Transaction Please Logged IN First ");
            }
            
        });


        $("body").on("click", ".submitBuySell", function ( event ) {
            event.preventDefault();
            var formData = $("#transaction-form").serialize();
            var queryParams = formData.split("&");
            var isOk = true;
            $.each(queryParams, function(index, param) {
                var keyValue = param.split("=");
                var value = $.trim(keyValue[1]);
                if (value === "") {
                    isOk = false;
                }
            });

            if( isOk ){
                $.ajax({
                    type: "POST",
                    url: 'main/jsvalidation/buysellvalidate.php',
                    data: formData,
                    success: function(response) {
                        var returnedData = JSON.parse(response);
                        $("#buySellCardHolder").fadeOut();
                        showPopUpMessage( returnedData['message'], needTitle=false , neededConfirmButton= false, confirmButtonId=false, confirmButtonClass=false );
                        setTimeout( hidePopUpMessage, 1000);
                    }
                });
            }else{
                alert( "Please Fill up all required Fields ");
            }


        });

        $("body").on("click", ".cancelBuySell", function () {
            $("#buySellCardHolder").fadeOut();
        });

        // $("#myInput").keypress(function(event) { send_amount
        $("body").on("keyup", "#send_amount", function () {

            let currentValue = $(this).val();
            if( butSellType.trim() === 'Buy Dollar' ){
                var receive_method_val = $("#receive_method").val();
                var getMethodRate = 'todayDollerBuyRate'+receive_method_val;
                var isBuy = true;
            }else{
                receive_method_val = $("#send_method").val();
                getMethodRate = 'todayDollerSellRate'+receive_method_val;
                isBuy = false;
            }       
            let todaysExchangeRate = todatBuySellRate[getMethodRate];
            if( isBuy ){
                var sendValue = currentValue/todaysExchangeRate;
            }else{
                 sendValue = currentValue*todaysExchangeRate;
            }

            $("#receive_amount").val( sendValue.toFixed(3) );
        });

//        if( butSellType.trim() === 'Buy Dollar' ) {
        $("body").on("change", "#send_method", function () {
            if( butSellType.trim() === 'Buy Dollar' ) {
                let selectedValue = $(this).val();
                let mobileBankingText = selectedValue + ' Number';
                $("#mobileBankingName").text(mobileBankingText);
            }
        });
        $("body").on("change", "#receive_method", function () {
            if( butSellType.trim() !== 'Buy Dollar' ) {
                let selectedValue = $(this).val();
                let mobileBankingText = selectedValue + ' Number';
                $("#mobileBankingName").text(mobileBankingText);
            }
        });

        $("body").on("change", "#receive_method", function () {
            if( butSellType.trim() === 'Buy Dollar' ) {
                let selectedValue = $(this).val();
                let mobileBankingText = selectedValue + ' Email';
                $("#digitalWalletBrands").text(mobileBankingText);
            }
        });
        $("body").on("change", "#send_method", function () {
            if( butSellType.trim() !== 'Buy Dollar' ) {
                let selectedValue = $(this).val();
                let mobileBankingText = selectedValue + ' Email';
                $("#digitalWalletBrands").text(mobileBankingText);
            }
        });

//        setTimeout(hidePopUpMessage, 1000); digitalWalletBrands

});
</script>