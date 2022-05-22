<?php
/*
  File Name: profile.php
  Original Location: /users/profile.php
  Description: The profile for a user.
  Author: Mitchell (BlxckSky_959)
  Copyright (C) RED7 STUDIOS 2021
*/

include_once $_SERVER["DOCUMENT_ROOT"] . "/assets/common.php";

if (!isset($_SESSION)) {
    // Initialize the session
    session_start();
}

$data = file_get_contents($API_URL . '/clan.php?api=getbyid&id=' . htmlspecialchars($_GET['id']));

// Decode the json response.
if (!str_contains($data, "This clan doesn't exist or has been deleted")) {
    $json_a = json_decode($data, true);

    $isBanned = $json_a[0]['data'][0]['isBanned'];

    $id = htmlspecialchars($_GET['id']);
    $name = $json_a[0]['data'][0]['name'];

    $real_displayname = $json_a[0]['data'][0]['displayname'];
    $real_description = $json_a[0]['data'][0]['description'];
    $currency = $json_a[0]['data'][0]['currency'];

    if ($isBanned != 1) {
        $displayname = $filterwords($json_a[0]['data'][0]['displayname']);
        $description = $filterwords($json_a[0]['data'][0]['description']);
        $icon = $json_a[0]['data'][0]['icon'];
    } else {
        $displayname = "[ CONTENT REMOVED ]";
        $description = "[ CONTENT REMOVED ]";
        $icon = "https://www.gravatar.com/avatar/?s=180";
    }

    if ($description == "") {
        $description = "This clan has not set a description.";
    }

    $created_at = $json_a[0]['data'][0]['created_at'];
    $banReason = $json_a[0]['data'][0]['bannedReason'];
    $banDate = $json_a[0]['data'][0]['bannedDate'];
    $members = $json_a[0]['data'][0]['members'];
    $isVerified = $json_a[0]['data'][0]['isVerified'];
    $isSpecial = $json_a[0]['data'][0]['isSpecial'];
} else {
    $name = "Not Found";
}

if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 1;
};

if ($_SESSION['id'] != $owner)
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="The profile page for <?php echo htmlspecialchars($displayname) ?>.">
    <title><?php echo htmlspecialchars($displayname) ?> - <?php echo htmlspecialchars($site_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

    <link rel="stylesheet" href="/assets/css/style.css">

    <script src="/assets/js/fontawesome.js"></script>

    <script src="/assets/js/relation.js"></script>

    <style>
        .blok {
            margin: 10px;
        }

        .row-nav {
            width: 100%;
        }
    </style>
</head>

<body>
    <?php include_once $_SERVER["DOCUMENT_ROOT"] . "/account/navbar.php" ?>
    <?php
    if (isset($your_isBanned)) {
        if ($your_isBanned == 1) {
            echo "<script type='text/javascript'>location.href = '/errors/banned.php';</script>";
        }
    }

    if (isset($maintenanceMode)) {
        if ($maintenanceMode == "on") {
            echo "<script type='text/javascript'>location.href = '/errors/maintenance.php';</script>";
        }
    }
    ?>
    <main class="page-content-wrapper">
        <script type="text/javascript">
            var ajaxSubmit = function(formEl) {
                // fetch the data for the form
                var data = $(formEl).serializeArray();
                var url = $(formEl).attr('action');

                // setup the ajax request
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function(d) {
                        if (d.success) {
                            alert('Changed value successfully!');
                            document.location = document.location;
                        } else {
                            alert("An error occurred while changing value, please try again later.")
                            document.location = document.location;
                        }
                    }
                });

                // return false so the form does not actually
                // submit to the page
                return false;
            }
        </script>
        <section>
            <div class="blok">
                <div class="row-nav">
                    <div class="d-flex align-items-center border-bottom">
                        <?php
                        if ($name == "Not Found") {
                            echo "<h2>This user could not be found!</h2></div><p>This user could possibly not be found due to a bug/glitch or has been removed (not banned).";
                            exit;
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($icon) ?>" class="profile-picture"></img>
                        &nbsp;
                        <h2 class="<?php if ($isSpecial == 1) {
                                        echo 'title-rainbow-lr';
                                    } else {
                                    } ?>"><a href="/clans/profile.php?id=<?php echo $_GET['id']; ?>">
                                <?php if ($displayname != "" && $displayname != "[]" && !empty($displayname)) {
                                    echo $filterwords(htmlspecialchars($displayname));
                                } else {
                                    echo $filterwords(htmlspecialchars($name));
                                } ?></a>
                            <?php if ($isVerified == 1) {
                                echo '<img src="' . $verifiedIcon . '" class="verified-icon"></img>';
                            } ?>
                            <small style="font-size: 15px;"><b>(@<?php echo htmlspecialchars($name); ?>)</b></small>
                            <?php if ($isBanned == 1) {
                                echo '<p><strong class="banned-text">*BANNED*</strong></p>';
                            } ?>
                            <span>
                                <h6>By <a href="/users/profile.php?id=1">@RED7Community</a>
                                </h6>
                            </span>
                            <span>
                                <h6><b>Worth:</b> <a><?php echo number_format_short($currency) . " " . $currency_name; ?></a>
                                </h6>
                            </span>
                        </h2>
                    </div>

                    <ul class="nav tab-menu nav-pills">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#info">Info</a></li>
                        <li class="nav-item"><a class="nav-link" href="#payout" data-toggle="tab">Payout</a></li>
                        <li class="nav-item"><a class="nav-link" href="#addFunds" data-toggle="tab">Add Funds</a></li>
                    </ul>

                    <div class="tab-content col-sm-8">
                        <div class="tab-pane well active in active" id="info">
                            <div>
                                <form method="post" action="/ajax/process.php" onSubmit="return ajaxSubmit(this);">
                                    <h5>Display Name:</h5>
                                    <input maxlength="69420" type="text" name="displayname" class="moderate-input" value="<?php echo htmlspecialchars($displayname); ?>" />
                                    <h5>Description:</h5>
                                    <input maxlength="69420" type="text" name="description" class="moderate-input" value="<?php echo $description; ?>" />
                                    <input hidden type="text" name="action" value="updateClanSettings" />
                                    <input hidden type="text" name="id" value="<?php echo $_GET["id"]; ?>" />
                                    <input class="btn btn-success" type="submit" name="form_submit" value="Update Clan Settings" />
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane well fade" id="payout">
                            <div>
                                <form method="post" action="/ajax/process.php" onSubmit="return ajaxSubmit(this);">
                                    <h5>Username:</h5>
                                    <input type="text" name="username" class="moderate-input" />
                                    <h5>Amount:</h5>
                                    <input type="number" name="amount" class="moderate-input" />
                                    <input hidden type="text" name="action" value="payoutClan" />
                                    <input hidden type="text" name="id" value="<?php echo $_GET["id"]; ?>" />
                                    <input class="btn btn-success" type="submit" name="form_submit" value="Payout to User" />
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane well fade" id="addFunds">
                            <div>
                                <form method="post" action="/ajax/process.php" onSubmit="return ajaxSubmit(this);">
                                    <h5>Amount:</h5>
                                    <input type="number" name="amount" class="moderate-input" />
                                    <input hidden type="text" name="action" value="addFundsToClan" />
                                    <input hidden type="text" name="id" value="<?php echo $_GET["id"]; ?>" />
                                    <input class="btn btn-success" type="submit" name="form_submit" value="Add Funds" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous">
    </script>

    <script>
        // BS tabs hover (instead - hover write - click)
        $('.tab-menu a').click(function(e) {
            e.preventDefault()
            $(this).tab('show')
        })
    </script>
</body>

</html>