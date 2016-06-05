<?php
if ($_POST['form'] == "Submit") {
    $npsso        = null;
    $access_token = null;
    $grant_code   = null;
    $friends_from = $_POST['formFrom'];
    $email        = $_POST['formEmail'];
    $password     = $_POST['formPassword'];
    $limit        = $_POST['formLimit'];
    function login() {
        global $email, $password;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://auth.api.sonyentertainmentnetwork.com/2.0/ssocookie");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "authentication_type=password&username=" . $email . "&password=" . $password . "&client_id=71a7beb8-f21a-47d9-a604-2e71bee24fe0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $json          = json_decode($server_output, true);
        global $npsso;
        $npsso = $json['npsso'];
        curl_close($ch);
        authCheck();
    }
    function authCheck() {
        global $npsso;
        $data        = array(
            "client_id" => "b0d0d7ad-bb99-4ab1-b25e-afa0c76577b0",
            "npsso" => $npsso,
            "scope" => "psn:sceapp,user:account.get,user:account.settings.privacy.get,user:account.settings.privacy.update,user:account.realName.get,user:account.realName.update,kamaji:get_account_hash,kamaji:ugc:distributor,oauth:manage_device_usercodes,kamaji:game_list,capone:report_submission",
            "service_entity" => "urn:service-entity:psn"
        );
        $data_string = json_encode($data);
        $ch          = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/authorizeCheck");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=UTF-8'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_close($ch);
        getGrantCode();
    }
    function getToken() {
        global $grant_code;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic YjBkMGQ3YWQtYmI5OS00YWIxLWIyNWUtYWZhMGM3NjU3N2IwOlpvNHk4ZUdJYTNvYXpJRXA='
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&client_id=b0d0d7ad-bb99-4ab1-b25e-afa0c76577b0&client_secret=Zo4y8eGIa3oazIEp&redirect_uri=com.scee.psxandroid.scecompcall://redirect&scope=psn:sceapp,user:account.get,user:account.settings.privacy.get,user:account.settings.privacy.update,user:account.realName.get,user:account.realName.update,kamaji:get_account_hash,kamaji:ugc:distributor,oauth:manage_device_usercodes,kamaji:game_list,capone:report_submission&code=" . $grant_code . "&service_entity=urn:service-entity:psn&duid=00000007000201283836373638363032313136393036333a487561776569202020203a616e676c657220202020");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $json          = json_decode($server_output, true);
        global $access_token;
        $access_token = $json['access_token'];
        curl_close($ch);
        getFriendsList();
    }
    function getFriendsList() {
        global $access_token, $limit, $friends_from;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://us-prof.np.community.playstation.net/userProfile/v1/users/".$friends_from."/friends/profiles2?fields=onlineId%2CavatarUrls%2Cplus%2CtrophySummary(%40default)%2CisOfficiallyVerified%2CpersonalDetail(%40default%2CprofilePictureUrls)%2Cpresences(%40titleInfo%2ChasBroadcastData)&sort=name-onlineId&avatarSizes=m&profilePictureSizes=m&offset=0&limit=" . $limit);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $access_token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        echo $server_output;
        curl_close($ch);
    }
    function getGrantCode() {
        global $npsso, $grant_code;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/authorize?state=247568009&duid=00000007000201283836373638363032313136393036333a487561776569202020203a616e676c657220202020&ui=pr&app_context=inapp_aos&client_id=b0d0d7ad-bb99-4ab1-b25e-afa0c76577b0&device_base_font_size=14.35&device_profile=mobile&redirect_uri=com.scee.psxandroid.scecompcall%3A%2F%2Fredirect&response_type=code&scope=psn%3Asceapp%2Cuser%3Aaccount.get%2Cuser%3Aaccount.settings.privacy.get%2Cuser%3Aaccount.settings.privacy.update%2Cuser%3Aaccount.realName.get%2Cuser%3Aaccount.realName.update%2Ckamaji%3Aget_account_hash%2Ckamaji%3Augc%3Adistributor%2Coauth%3Amanage_device_usercodes%2Ckamaji%3Agame_list%2Ccapone%3Areport_submission&service_entity=urn%3Aservice-entity%3Apsn&service_logo=ps&smcid=psapp%3Asignin&support_scheme=sneiprls");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Requested-With: com.scee.psxandroid',
            'Cookie: s_cc=true; npsso=' . $npsso . '; s_fid=558DC57E7FBF7922-0E3B0357AF46DDB6; s_prepagename=android%3Apdr%3Asignin%3Aentrance%20signin; s_sq=%5B%5BB%5D%5D'
        ));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        list($headers, $response) = explode("\r\n\r\n", $server_output, 2);
        $headers = explode("\n", $headers);
        foreach ($headers as $header) {
            if (stripos($header, 'Location:') !== false) {
                $grant_code = strtok(str_replace("Location: com.scee.psxandroid.scecompcall://redirect?code=", "", $header), '&');
            }
        }
        getToken();
    }
    login();
}
?>
<h1>PSN friends list</h1>
<form action="index.php" method="post">
    Email
    <input type="text" name="formEmail" value="<?=$email;?>">
 
    Password
    <input type="password" name="formPassword" value="<?=$password;?>">

    Limit
    <input type="text" name="formLimit" value="<?=$limit;?>">

    Friends list from?
    <input type="text" name="formFrom" value="<?=$friends_from;?>">
 
    <input type="submit" name="form" value="Submit">
</form>