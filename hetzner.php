<?php

require 'RobotRestClient.class.php';
require 'RobotClientException.class.php';
require 'RobotClient.class.php';

$msg = '';

if (isset($_POST['submit'])) {

    $apiUser = $_POST['api_user'];
    $apiKey = $_POST['api_key'];

    $robot = new RobotClient('https://robot-ws.your-server.de', $apiUser, $apiKey);

    $postData = $_POST['postData'];
    $postData = trim($postData);
    $postDataArr = explode("\n", $postData);

    foreach ($postDataArr as $data) {

        if (empty(trim($data))) continue;

        $dataArr = explode(',', $data);

        $ip = trim($dataArr[0]);
        $domain = trim($dataArr[1]);

        try {
            $results = $robot->rdnsGet($ip);
        } catch (RobotClientException $e) {
            $errorCode = $e->getCode();
        }

        if ($errorCode == 'UNAUTHORIZED') {
            $msg .= "<p>Login failed.</p>";
            break;
        }

        $msg .= "<p>";
        $msg .= "$ip, $domain: ";

        if ($errorCode == 'RDNS_NOT_FOUND') {
            $results = $robot->rdnsCreate($ip, $domain);
            $msg .= "created";
        } else if ($errorCode == 'IP_NOT_FOUND') {
            $msg .= "IP not found";
        } else {
            $ptr = $results->rdns->ptr;
            if ($ptr) {
                $results = $robot->rdnsUpdate($ip, $domain);
                $msg .= "updated";
            }
        }

        $msg .= "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>NameCheap</title>

    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        body{
            background: #f3f3f3;
            margin-top:3em;
        }

        .panel h3 {
            margin-top: 0px;
            margin-bottom: 0px;
        }

        pre {
            color:green;
        }

    </style>

</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-info">

                <div class ="panel-heading">
                    <h3>Hetzner RDNS</h3>
                </div>

                <div class="panel-body">
                    <form class="form-horizontal" action="" method="POST">
                        <fieldset>


                            <!-- Text input-->
                            <div class="form-group">
                                <label class="col-md-2 control-label" for="api_user">User</label>
                                <div class="col-md-10">
                                    <input id="api_user" name="api_user" type="text" placeholder="User" class="form-control input-md" value="" required>
                                </div>
                            </div>

                            <!-- Text input-->
                            <div class="form-group">
                                <label class="col-md-2 control-label" for="api_user">Password</label>
                                <div class="col-md-10">
                                    <input id="api_key" name="api_key" type="text" placeholder="Password" class="form-control input-md" value="" required>
                                </div>
                            </div>

                            <!-- Textarea -->
                            <div class="form-group">
                                <label class="col-md-2 control-label" for="textarea">Data</label>
                                <div class="col-md-10">
                                    <textarea class="form-control" id="textarea" name="postData" rows="20" required></textarea>
                                </div>
                            </div>

                            <!-- Button -->
                            <div class="form-group">
                                <div class="col-md-2"></div>
                                <div class="col-md-10">
                                    <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="well"><pre><?=$msg;?></pre></div>
        </div>
    </div>

</div>
</div>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>