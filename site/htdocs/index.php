<?php
include(__DIR__.'/../init.php');
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors" />
    <meta name="generator" content="Jekyll v4.1.1" />
    <title>ESP-32 Devices</title>
    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.5.1.js"></script>

    <style>
        #clicks {
        font-size: .5rem;
        }
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="/grid.css" rel="stylesheet" />
</head>
<body class="py-4">

<div class="container">

  <h1>Devices</h1>
  <div class="row">
      <div class="col-lg-10">
    <?php
    // Active Devices:
    $stmt = $db->prepare("SELECT 
                d.*,
                IF(l.id IS NULL, 0, 1) AS online,
                ip as last_ip,
                relay_status,
                created AS last_seen
            FROM device d
            LEFT JOIN device_log l ON d.id = l.device_id
            WHERE
                l.created > NOW() - INTERVAL 5 SECOND
            GROUP BY d.id
            ORDER BY d.id
    ");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cssClass = "col-sm-12 themed-grid-col themed-grid-col";
        if ($row['online'] == '1') {
            $cssClass .= " themed-grid-col-online";
        }

        $relay = new Control($row['name'], 'relay', "Relay", $row['relay_status']);

        echo '
        <div class="row mb-1">
            <div class="'.$cssClass.'">
                <div class="row">
                    <div class="col-sm-4">
                        <h1>'.$row['name'].'</h1>
                        <pre id="'.$row['name'].'-txt"></pre>
                    </div>
                    <div class="col-sm-8">
                        <!-- Line -->
                        <!--
                        <div class="row"><div class="col-sm-12"> <hr/> </div></div>
                        -->
                        '.$relay->html().'
                    </div>
                </div>
            </div>
        </div>
        ';
    }
    ?>
      </div>
      <div class="col-lg-2">
        <pre>
        <code id="clicks">
        </code>
        </pre>
      </div>
  </div>
<script type="text/javascript">
function checkClicks() {
    var url = "http://iot.shttps.com/clicks.php";
    wget(
        url,
        function (text) {
            var obj = jQuery.parseJSON(text);
            var i, row;
            var txt = "\n";
            for (i = 0; i < obj.numRows; i++) {
                row = obj.rows[i];
                txt = txt + row + "\n";
            }
            $('#clicks').text(txt);
        },
        function (status, text) {
        }
    );
}
function checkStatus() {
    var url = "http://iot.shttps.com/status.php";
    wget(
        url,
        function (text) {
            var obj = jQuery.parseJSON(text);
            var i, row;
            // on
            for (i = 0; i < obj.numRows; i++) {
                row = obj.rows[i];

                mod = row.name + '-relay'; // esp32-123-relay button
                if (row.relay_on  == '1') {
                    switchOn(mod);
                } else {
                    switchOff(mod);
                }

                console.log(mod);
                // #esp32-123-txt
                $('#' +row.name + '-txt').text(
                    'Online: ' + row.online + "\n" +
                    'Last Seen: ' + row.last_seen + "\n" +
                    'Last IP: ' + row.last_ip + "\n" +
                    'Relay Enabled: ' + row.relay_on + "\n"
                );

            }
        },
        function (status, text) {
        }
    );
}
setInterval(checkClicks, 1000);
setInterval(checkStatus, 2000);
checkStatus();


function updateStatus(name, type, value) {
    var url = "http://iot.shttps.com/update-status.php?name=" + name + "&type=" + type + "&value=" + value;
    wget(
        url,
        function (text) {
            // on
            if (value == '1') {
                var mod = name + '-' + type; // esp32-123-[relay|pir]
                switchOn(mod);
            } else {
                var mod = name + '-' + type; // esp32-123-[relay|pir]
                switchOff(mod);
            }
        },
        function (status, text) {
            console.log(text);
        }
    );
}

function switchOn(id) {
    var offBtn = '#' + id + '-off';
    var onBtn  = '#' + id + '-on';

    $(offBtn).removeClass('btn-secondary');
    $(offBtn).addClass('btn-outline-secondary');

    $(onBtn).removeClass('btn-outline-danger');
    $(onBtn).addClass('btn-danger');
}

function switchOff(id) {
    var offBtn = '#' + id + '-off';
    var onBtn  = '#' + id + '-on';
    $(offBtn).removeClass('btn-outline-secondary');
    $(offBtn).addClass('btn-secondary');
    $(onBtn).removeClass('btn-danger');
    $(onBtn).addClass('btn-outline-danger');
}

function wget(url, success, err) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      success(this.responseText)
    } else if (this.readyState == 4 && this.status != 200) {
      err(this.status, this.responseText);
    }
  };
  xhttp.open("GET", url, true);
  xhttp.send();
}
</script>
</body>
</html>

<?php

class Control {
    private $name;
    private $type;
    private $label;
    private $state;
    public function __construct($name, $type, $label, $s) {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->state = $s;
    }

    public function getLabel() {
        return $this->label;
    }

    public function offCssClass() {
        return 'btn btn-outline-secondary btn-lg';
    }

    public function onCssClass() {
        return 'btn btn-outline-danger btn-lg';
    }

    public function offOnClick() {
        return "updateStatus('$this->name', '$this->type', 0)";
    }
    public function onOnClick() {
        return "updateStatus('$this->name', '$this->type', 1)";
    }


    public function html() {
        return '<div class="row">
            <div class="col-sm-6">
                <h3>'.$this->getLabel().'</h3>
            </div>
            <div class="col-sm-6">
                <div style="background-color:transparent;" class="btn-group float-right" role="group" aria-label="On/Off buttons">
                    <button type="button" id="'.$this->name.'-'.$this->type.'-off" onclick="'.$this->offOnClick().'" class="'.$this->offCssClass().'">Off</button>
                    <button type="button" id="'.$this->name.'-'.$this->type.'-on" onclick="'.$this->onOnClick().'" class="'.$this->onCssClass().'">On</button>
                </div>
            </div>
        </div>';
    }
}
?>
