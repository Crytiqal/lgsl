const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
var lgsl_server_id = urlParams.get('r');
var lgsl_streamlog = 0;
var lgsl_cmd = '';
var lgsl_rconpassword = '';
var url = 'lgsl_rcon/lgsl_rconbackend.php?r='+lgsl_server_id+'&l='+lgsl_streamlog+'&cmd='+lgsl_cmd+'&rconpassword='+lgsl_rconpassword;
var i = 1;

function parseTemplate(responseText, responseTime) {

    var server = responseText;

    if(document.getElementById("responseTime")) {
        document.getElementById("responseTime").innerText = responseTime;
    }

    if (server.b) {
        for (const [key, value] of Object.entries(server.b)) {
            //console.log(`${key}: ${value}`);
            if (document.getElementById(key) && (document.getElementById(key).innerText !== value)) {
                document.getElementById(key).innerText = value;
            }
        }
    }

    if (server.o) {
        for (const [key, value] of Object.entries(server.o)) {
            //console.log(`${key}: ${value}`);
            if (document.getElementById(key) && (document.getElementById(key).innerText !== value)) {
                document.getElementById(key).innerText = value;
            }
        }
    }

    if (server.s) {
        for (const [key, value] of Object.entries(server.s)) {
            //console.log(`${key}: ${value}`);
            if (document.getElementById(key) && (document.getElementById(key).innerText !== value)) {
                if(key == 'map') {
                    var str = document.getElementById('image_map').src;
                    var res = str.replace(document.getElementById(key).innerText, value);
                    document.getElementById('image_map').src = res;
                }
                document.getElementById(key).innerText = value;
            }
        }
    }

    if (server.e) {
        for (const [key, value] of Object.entries(server.e)) {
            //console.log(`${key}: ${value}`);
            if (document.getElementById(key) && (document.getElementById(key).innerText !== value)) {
                document.getElementById(key).innerText = value;
            }
        }
    }

    if (server.p != '') {

        document.getElementById('playerinfo').getElementsByTagName('thead')[0].getElementsByTagName('tr')[0].innerHTML = '';
        document.getElementById('playerinfo').getElementsByTagName('tbody')[0].innerHTML = '';

        for (var i = 0; i < Object.keys(server.p[0]).length; i++) {
            var th = document.createElement('th');
            th.innerHTML = `${Object.keys(server.p[0])[i]}`;
            document.getElementById('playerinfo').getElementsByTagName('thead')[0].getElementsByTagName('tr')[0].appendChild(th);
        }
        
        for (const [key, playerinfo] of Object.entries(server.p)) {
            var row = document.getElementById('playerinfo').getElementsByTagName('tbody')[0].insertRow(0);
            let properties = Object.keys(playerinfo);
            properties.forEach((value, key) => {
                var td = row.insertCell(key);
                td.innerHTML = playerinfo[value];

            })
        }

    } else {
        if(document.getElementById('playerinfo').getElementsByTagName('thead')[0].getElementsByTagName('tr')[0].innerHTML != '<th class="text-center">NO PLAYER INFO</th>')
        {
            document.getElementById('playerinfo').getElementsByTagName('thead')[0].getElementsByTagName('tr')[0].innerHTML = '<th class="text-center">NO PLAYER INFO</th>';
            document.getElementById('playerinfo').getElementsByTagName('tbody')[0].innerHTML = '';
        }
    }

    if (server.r && document.getElementById("response")) {
        if(document.getElementById("response").parentElement.style.zIndex != "1") {
            document.getElementById("response").parentElement.style.zIndex = "1";
            document.getElementById("response").parentElement.classList.replace("out", "in"); 
        }
        document.getElementById("response").innerText = "";
        if(server.r.response == "") {
            server.r.response[0] = 'No response..';
        }
        for (const [key, value] of Object.entries(server.r.response)) {
            //console.log(`${key}: ${value}`);
            document.getElementById("response").innerText += value+'\n';
        }
    } else {
        if(!sessionStorage.getItem("pinresponse")) {
            document.getElementById("response").parentElement.classList.replace("in", "out");
            document.getElementById("response").parentElement.style.zIndex = "-1";
        }        
    }

    if (server.log && document.getElementById("log")) {
        var logLine = '';
        for (const [key, value] of Object.entries(server.log)) {
            logLine += value + '<br>\n';
        }
        document.getElementById("log").innerHTML += logLine;
        document.getElementById("log").scrollTop = document.getElementById("log").scrollHeight;
    }
}

function callback(responseText='', responseTime='') {

    responseText = JSON.parse(responseText);
    console.log(responseText);

    lgsl_cmd = '';
    // lgsl_rconpassword = '';
    if(typeof responseText['filesize'] != "undefined") {
        sessionStorage.setItem("filesize", responseText['filesize']);
    } else {
        sessionStorage.setItem("filesize", 0);
    }

    url = 'lgsl_rcon/lgsl_rconbackend.php?r='+lgsl_server_id+'&l='+lgsl_streamlog+'&bytes='+sessionStorage.getItem("filesize")+'&cmd='+lgsl_cmd+'&rconpassword='+lgsl_rconpassword;
            
    // Todo: enable button when server responded
    document.getElementById("controls").getElementsByTagName("button")[1].disabled = false;

    parseTemplate(responseText, responseTime);

    if (responseText == "" || responseText.b.status == 0) {
        i = i * 2;
        console.log("No response!");
        if (i < 16) {
            console.log("Waiting " + (i * 5000) / 1000 + " seconds");
            setTimeout(() => {
                ajax(url, callback)
            }, i * 5000);
        } else {
            console.log("Stopped querying");
        }
    } else {
        setTimeout(() => {
            ajax(url, callback)
        }, 5000);
    }
}

function ajax(url, callback='') {

    var ajaxTime = Date.now();

    var xhttp = new XMLHttpRequest();
    // Send the proper header information along with the request
    // xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
                var responseTime = Date.now() - ajaxTime;
                callback(this.responseText, responseTime);
            } else {
                alert("Error! Request status is " + this.status);
            }
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();

}

function streamLog(element) {
    //element = document.getElementById(element);
    if (element.value === "1") {
        element.value = "0";
        lgsl_streamlog = 1;
        url = 'lgsl_rcon/lgsl_rconbackend.php?r='+lgsl_server_id+'&l='+lgsl_streamlog+'&cmd='+lgsl_cmd+'&rconpassword='+lgsl_rconpassword;
    } else {
        element.value = "1";
        lgsl_streamlog = 0;
        url = 'lgsl_rcon/lgsl_rconbackend.php?r='+lgsl_server_id+'&l='+lgsl_streamlog+'&cmd='+lgsl_cmd+'&rconpassword='+lgsl_rconpassword;
        var pause = "The log viewer has been paused. Click the Start Log button to unpause.";
        logDiv = document.getElementById("log");
        var newNode = document.createTextNode(pause);
        logDiv.replaceChild(newNode, logDiv.childNodes[0]);
    }
}

function sendCmd(element) {
    if(!sessionStorage.getItem('rconpassword') && (element.closest("form").elements[1].value == '') ) {
        window.alert("Set rcon password!");
    } else if(!sessionStorage.getItem('rconpassword') && (element.closest("form").elements[1].value !== '') ) {
        lgsl_cmd = element.closest("form").elements[1].value;
        sessionStorage.setItem('rconpassword', lgsl_cmd);
        document.getElementById("controls").getElementsByTagName("input")[0].placeholder = 'command...';
        document.getElementById("controls").getElementsByTagName("button")[4].classList.replace('btn-danger', 'btn-success');
        document.getElementById("controls").getElementsByTagName("button")[4].innerHTML = "<i class='glyphicon glyphicon-lock'></i>";
    } else if(sessionStorage.getItem('rconpassword') && element.closest("form").elements[1].value !== '') {
        lgsl_cmd = document.getElementById("controls").getElementsByTagName("input")[0].value;
        lgsl_rconpassword = sessionStorage.getItem('rconpassword');
        document.getElementById("controls").getElementsByTagName("button")[1].disabled = true;
        url = 'lgsl_rcon/lgsl_rconbackend.php?r='+lgsl_server_id+'&l='+lgsl_streamlog+'&cmd='+lgsl_cmd+'&rconpassword='+lgsl_rconpassword;
    }
    document.getElementById("controls").getElementsByTagName("input")[0].value = '';
    return false;
}

function pinResponse(element) {
    document.getElementById("response").parentElement.classList.toggle("out");
    document.getElementById("response").parentElement.classList.toggle("in");
    if(document.getElementById("response").parentElement.style.zIndex == "1") {
        document.getElementById("response").parentElement.style.zIndex = "-1";
        sessionStorage.removeItem("pinresponse");
    } else {
        document.getElementById("response").parentElement.style.zIndex = "1";
        sessionStorage.setItem("pinresponse", "1");
    }
}

function clearRconPasswd(element) {
    if(sessionStorage.getItem("rconpassword")) {
        sessionStorage.removeItem("rconpassword");
        document.getElementById("controls").getElementsByTagName("button")[4].classList.replace('btn-success', 'btn-danger');
        document.getElementById("controls").getElementsByTagName("button")[4].innerHTML = "<i class='glyphicon glyphicon-remove'></i>";
        document.getElementById("controls").getElementsByTagName("input")[0].value = '';
        document.getElementById("controls").getElementsByTagName("input")[0].placeholder = 'set rconpassword';
        document.getElementById("response").innerText = "";
        url = 'lgsl_rcon/lgsl_rconbackend.php?r='+lgsl_server_id+'&l='+lgsl_streamlog+'&cmd='+lgsl_cmd+'&rconpassword='+lgsl_rconpassword;
    }
}

function status(url) {
    ajax(url, callback);
}

if(sessionStorage.getItem('rconpassword')) {
    document.getElementById("controls").getElementsByTagName("button")[4].classList.replace('btn-danger', 'btn-success');
    document.getElementById("controls").getElementsByTagName("input")[0].placeholder = 'command...';
    document.getElementById("controls").getElementsByTagName("button")[4].innerHTML = "<i class='glyphicon glyphicon-lock'></i>";
}

status(url);