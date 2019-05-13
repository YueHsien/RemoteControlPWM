const char REBOOT[] PROGMEM = R"=====(
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html;charset=UTF-8'/>
		<meta http-equiv='Cache-Control' content='no-cache'/>
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'/>
		<link href='./style.css' rel='stylesheet'>
		<title>網路連線設定</title>
	</head>
	<body>
		<p style='text-align:center;font-size:12px;color:red;'>重開機中，請稍後。 <a href='/'>[回設定]</a></p>
		<script type='text/javascript'>alert('重開機中，請稍後。');setTimeout(\"window.location='http://google.com'\",5000);</script>
	</body>
</html>
)=====";
const char SAVE[] PROGMEM = R"=====(
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html;charset=UTF-8'/>
		<meta http-equiv='Cache-Control' content='no-cache'/>
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'/>
		<link href='./style.css' rel='stylesheet'>
		<title>網路連線設定</title>
	</head>
	<body>
		<p style='text-align:center;font-size:12px;color:red;'>設定已儲存完成，並於重開機後生效，請手動重開機。 <a href='/'>[回設定]</a></p>
		<script type='text/javascript'>alert('設定已儲存完成，並於重開機後生效，請重新開機。');setTimeout(\"window.location='http://google.com'\",5000);</script>
	</body>
</html>
)=====";
const char INDEX[] PROGMEM = R"=====(
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html;charset=UTF-8'/>
		<meta http-equiv='Cache-Control' content='no-cache'/>
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'/>
		<script src='./f.js'></script>
		<link href='./style.css' rel='stylesheet'>
		<title>網路連線設定</title>
	</head>
	<body onload='fn()'>
		<form method='get' action='setting' style='padding-top:20px;'>
			<table align='center' style='max-width:450px;' width='90%'>
				<tr bgcolor='#fcc'>
					<td colspan='2' align='center'><h2>網路連線設定</h2></td>
				</tr>
				<tr bgcolor='#fcc'>
					<td align='right'><p>UUID</p></td>
					<td align='left'>
						<p id='uuid'>------------</p>
					</td>
				</tr>
				<tr bgcolor='#fcc'>
					<td align='right'><p>AP IP Address</p></td>
					<td align='left'>
						<p id='apip'>---.---.---.---</p>
					</td>
				</tr>
				<tr>
					<td align='right'><p>WiFi SSID</p></td>
					<td align='left'>
						<p><select class='input_text' id='ssid_scan' onchange='c()'><option value='none'>搜尋中...</option></select> <a href='#' onclick='o();return false;'>(重新整理)</a></p>
						<p><input class='input_text' id='ssid' name='ssid' type='input' value='---------'></p>
					</td>
				</tr>
				<tr>
					<td align='right'><p>WiFi Security</p></td>
					<td align='left'>
						<p><select class='input_text' id='wifi_encrypt' name='wifi_encrypt' onchange='encrypt_hidden()'>
							<option value='8'>開放無安全性</option>
							<option value='4'>WPA2-AES</option>
						</select></p>
					</td>
				</tr>
				<tr id='wifi_pass'>
					<td align='right'><p>WiFi Password</p></td>
					<td align='left'><p><input class='input_text' id='pass' name='pass' type='input' value='---------'></p></td>
				</tr>
				<tr bgcolor='#fcc'>
					<td colspan='2' align='center'>
						<p><input type='submit' value='儲存設定'> <input type='button' onclick="location.href='/reboot';" value='取消'></p>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>
)=====";
const char STYLE[] PROGMEM = R"=====(
a{text-decoration: none;}
table{border-collapse: collapse;}
table,th,td{border: 1px solid #000;}
p{margin:10px !important;font-size:12px;}
.input_text{width:120px;}
*{font-family: PMingLiU;}
)=====";
const char SCRIPT[] PROGMEM = R"=====(
function loadJSON(path, success, error)
{
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                if (success)
                    success(JSON.parse(xhr.responseText));
            } else {
                if (error)
                    error(xhr);
            }
        }
    };
    xhr.open("GET", path, true);
    xhr.send();
}
function encrypt_hidden(){
	var select = document.getElementById("wifi_encrypt").value;
	if(select == 8){
		document.getElementById("wifi_pass").style.display = 'none';
	}else{
		document.getElementById("wifi_pass").style.display = '';
	}
}
function read_setting(){
	loadJSON('./setting.json',
		function(data) {
			document.getElementById('uuid').innerHTML = data.uuid;
			document.getElementById('apip').innerHTML = data.apip;
			document.getElementById('ssid').value = data.ssid;
			document.getElementById('pass').value = data.pass;
			document.getElementById('wifi_encrypt').value = data.encrypt;
			encrypt_hidden();
		},function(xhr) {
			console.error(xhr); 
		}
	);
}
function fn(){
	read_setting();
	o();
	encrypt_hidden();
}
function clear_ssid(){
	var select = document.getElementById("ssid_scan");
	var i;
    for(i = select.options.length - 1 ; i >= 0 ; i--){
        select.remove(i);
    }
}
function add_wait(){
	var select = document.getElementById("ssid_scan");
	var option_none = document.createElement("option");
	option_none.text = '搜尋中...';
	option_none.value = 'none';
	select.appendChild(option_none);
	select.disabled = true;
}
function add_select(nums){
	var select = document.getElementById("ssid_scan");
	var option_none = document.createElement("option");
	option_none.text = '選擇周邊網路(' + nums + ')';
	option_none.value = 'none';
	select.appendChild(option_none);
	select.disabled = false;
}
function o(){
	clear_ssid();
	add_wait();
	loadJSON('./wifi.json',
		function(data) {
			clear_ssid();
			add_select(data.length);
			if(data.length>0){
				var select = document.getElementById('ssid_scan');
				for (var i = 0; i < data.length; i++){
					var obj = data[i];
					//console.log(i, data[i].ssid);
					var option = document.createElement('option');
					option.text = data[i].ssid;
					option.value = data[i].ssid;
					option.setAttribute('encrypt', data[i].encrypt);
					select.appendChild(option);
				}
			}
		},function(xhr) {
			console.error(xhr); 
		}
	);
}
function c(){
	var en_idx = document.getElementById('ssid_scan').selectedIndex;
	var encrypt = document.getElementById("ssid_scan").options[en_idx].getAttribute('encrypt');
	var select = document.getElementById('ssid_scan').value;
	//console.log('ssid_scan encrypt', en_idx, select, encrypt);
	if(encrypt == 8){
		document.getElementById("wifi_encrypt").value = 8;
	}else if(encrypt == 7){
		document.getElementById("wifi_encrypt").value = 8;
	}else{
		document.getElementById("wifi_encrypt").value = 4;
	}
	if(select != 'none'){
		document.getElementById('ssid_scan').value = encrypt;
		document.getElementById('ssid').value = select;
		document.getElementById('ssid_scan').value = 'none';
		encrypt_hidden();
	}
}
)=====";