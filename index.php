<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="How much time have you wasted on Destiny?">
    <meta property="og:title" content="Time Wasted on Destiny" />
    <meta property="og:description" content="How much time have you wasted on Destiny?" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="http://wastedondestiny.com" />
    <meta property="og:image" content="http://wastedondestiny.com/background.jpg" />

    <link rel="icon" href="favicon.ico">
    <title>Time wasted on Destiny</title>

    <!-- CSS -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://bootswatch.com/cosmo/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-switch.min.css">
    <link rel="stylesheet" href="css/stylesheet.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <div class="container vert-middle">
        <div class="panel panel-default text-center">
            <div class="panel-body">
                <h1>Time Wasted on Destiny</h1>
                <h2><small>Find out just how much time have you wasted on this game by choosing your console and entering your username below.</small></h2>
                <form role="form" id="search">
                    <div class="row">
                        <div class="col-md-4">
                            <input id="console" type="checkbox" name="console" data-on-text="Xbox" data-on-color="success" data-off-text="Playstation" data-off-color="primary" data-size="large">
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input id="username" type="text" class="form-control" name="username" placeholder="Username">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">Go!</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="error"></div>
        <div class="row hide" id="fields">
            <div class="col-md-4">
                <div class="panel panel-fixed panel-primary text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title"><img src="img/playstation-icon.png" /> Playstation</h3>
                    </div>
                    <div class="panel-body">
                        <img class="icon" id="psn_icon" />
                        <h4 id="psn_name"></h4>
                        <span id="psn_time"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-fixed panel-info text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title"><img src="img/destiny-icon.png" /> Total time spent</h3>
                    </div>
                    <div class="panel-body">
                        <h4 id="display_name"></h4>
                        <span id="total_time"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-fixed panel-success text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title"><img src="img/xbox-icon.png" /> Xbox</h3>
                    </div>
                    <div class="panel-body">
                        <img class="icon" id="xbl_icon" />
                        <h4 id="xbl_name"></h4>
                        <span id="xbl_time"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="ads">
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-6332373031553935"
                             data-ad-slot="7124654099"
                             data-ad-format="auto"></ins>
                        <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-6332373031553935"
                             data-ad-slot="7124654099"
                             data-ad-format="auto"></ins>
                        <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="well text-center">
            <span>Made with <span class="glyphicon glyphicon-heart"></span> by <a href="https://www.facebook.com/BinarMorker">François Allard (BinarMorker)</a></span>
            <br/>
            <span class="text-muted">You can also use my simple API located <a href="request.php?help">here</a>.</span>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-switch.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-52347626-4', 'auto');
        ga('send', 'pageview');
    </script>
</body>

</html>