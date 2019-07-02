#include <WebSocketsClient.h>

#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <ESP8266mDNS.h>
#include <EEPROM.h>
#include "gpio.h"
#include "ext.h"
#define USE_SERIAL Serial

WebSocketsClient webSocket;

//WEB Server
ESP8266WebServer server;

//AP模式的SSID跟密碼
const char* softap_ssid = "PAW_Config";
const char* softap_pass = "123456789";

//從EEPROM讀出來的SSID跟密碼
String eeprom_ssid;
String eeprom_pass;
int eeprom_encrypt;

//IP位置
String AP_IP;
String STA_IP;
String D_UUID;

//UUID
uint8_t MAC_ADDR[6];
char UUID[18];

//reboot flag
int reboot_flag = 0;
int reboot_try = 0;
int status = WL_IDLE_STATUS;
bool is_epass = false;

int Input_1 = 12;	//config button
int Input_2 = 12;	//toggle button
int Output_1 = 14;	//relay

int counter = 0;
int ping_return = 0;

char PING_DATA[15];	//ping server
char REG_DATA[15];	//send register
char ON_DATA[15];	//send on status
char OFF_DATA[15];	//send off status

WiFiClient client;

void assign_uuid(){
	char vPING_DATA[15] = {
		0xf0, 0xfc, 0xff, 0xff,
		MAC_ADDR[0], MAC_ADDR[1], MAC_ADDR[2], MAC_ADDR[3], MAC_ADDR[4], MAC_ADDR[5],
		0xf4, 
		0xf1, 0xfd, 0xff, 0xff,
	};
	char vREG_DATA[15] = {
		0xf0, 0xfc, 0xff, 0xff,
		MAC_ADDR[0], MAC_ADDR[1], MAC_ADDR[2], MAC_ADDR[3], MAC_ADDR[4], MAC_ADDR[5],
		0xff, 
		0xf1, 0xfd, 0xff, 0xff,
	};
	char vON_DATA[15] = {
		0xf0, 0xfc, 0xff, 0xff,
		MAC_ADDR[0], MAC_ADDR[1], MAC_ADDR[2], MAC_ADDR[3], MAC_ADDR[4], MAC_ADDR[5],
		0xf0, 
		0xf1, 0xfd, 0xff, 0xff,
	};
	char vOFF_DATA[15] = {
		0xf0, 0xfc, 0xff, 0xff,
		MAC_ADDR[0], MAC_ADDR[1], MAC_ADDR[2], MAC_ADDR[3], MAC_ADDR[4], MAC_ADDR[5],
		0xf1, 
		0xf1, 0xfd, 0xff, 0xff,
	};
	strncpy(PING_DATA, vPING_DATA, 15);
	strncpy(REG_DATA,  vREG_DATA,  15);
	strncpy(ON_DATA,   vON_DATA,   15);
	strncpy(OFF_DATA,  vOFF_DATA,  15);
}

void webSocketEvent(WStype_t type, uint8_t * payload, size_t length) {
	switch(type) {
		case WStype_DISCONNECTED:
			Serial.printf("\r\n[device->server] Disconnected!");
			ESP.reset();
			break;
		case WStype_CONNECTED:
			{
				Serial.printf("\r\n[device->server] Connected to url: %s",  payload);
				webSocket.sendTXT(REG_DATA, 15);
				Serial.printf("\r\n[device->server] send register [%s]", UUID);
				if(is_switch()){
					webSocket.sendTXT(ON_DATA, 15);
					Serial.printf("\r\n[device->server] send status on");
				} else {
					webSocket.sendTXT(OFF_DATA, 15);
					Serial.printf("\r\n[device->server] send status off");
				}
			}
			break;
		case WStype_TEXT:
			Serial.printf("\r\n[device<-server] get text: %s", payload);
			break;
		case WStype_BIN:
			{
				Serial.printf("\r\n[device<-server] get binary length: %u[%02X %02X %02X %02X]", length, payload[0], payload[1], payload[2], payload[3]);
				if(payload[0] == 0xff && payload[1] == 0xfc && payload[2] == 0x00 && payload[3] == 0xf0) {
					switch_on();
					webSocket.sendTXT(ON_DATA, 15);
					Serial.printf("\r\n[device->server] send status on");
				}
				if(payload[0] == 0xff && payload[1] == 0xfc && payload[2] == 0x00 && payload[3] == 0xf1) {
					switch_off();
					webSocket.sendTXT(OFF_DATA, 15);
					Serial.printf("\r\n[device->server] send status off");
				}
				if(payload[0] == 0xff && payload[1] == 0xfc && payload[2] == 0x00 && payload[3] == 0xf2){
					switch_toggle();
					if(is_switch()) {
						webSocket.sendTXT(ON_DATA, 15);
						Serial.printf("\r\n[device->server] send status on");
					} else {
						webSocket.sendTXT(OFF_DATA, 15);
						Serial.printf("\r\n[device->server] send status off");
					}
				}
				if(payload[0] == 0xff && payload[1] == 0xfc && payload[2] == 0x00 && payload[3] == 0xf3){
					if(is_switch()) {
						webSocket.sendTXT(ON_DATA, 15);
						Serial.printf("\r\n[device->server] send status on");
					} else {
						webSocket.sendTXT(OFF_DATA, 15);
						Serial.printf("\r\n[device->server] send status off");
					}
				}
				//PING
				if(payload[0] == 0xff && payload[1] == 0xfc && payload[2] == 0x00 && payload[3] == 0xf4){
					ping_return = 0;
					Serial.printf("\r\n[device->server] recive ping");
				}
				hexdump(payload, length);
			}
			break;
	}
}

void setup() {
	Serial.begin(9600);
	while (!Serial){}
	
	is_epass = is_epassf();
	read_ep_wifi();		//讀取EEPROM的STA帳號密碼
	get_uuid();			//取得裝置UUID
	assign_uuid();
	ESP.wdtDisable();
	ESP.wdtEnable(10000);
	
	Serial.printf("\r\n[BOOT] success.");
	pinMode(Output_1, OUTPUT);
	pinMode(Input_1, INPUT_PULLUP);
	pinMode(Input_2, INPUT_PULLUP);

	if(is_switch()){
		switch_on();
	} else {
		switch_off();
	}
	
	if(digitalRead(Input_1) == LOW) {
		//進入CONFIG MODE
		//byte send_btn[3] = { 0xAB, 0xAB, 0xAC };
		//Serial.write(send_btn, 3);
		setupAP();
		while(1) {
			server.handleClient();
		}
	} else {
		WiFi.mode(WIFI_STA);
		set_sta_wifi();
	}
	
	webSocket.begin("apw.cumi.co", 80, "/ws");
	webSocket.onEvent(webSocketEvent);
	webSocket.setReconnectInterval(1000);
}

void loop() {
	webSocket.loop();
	delay(100);
	counter++;
	//ping
	if(counter >= 300){
		webSocket.sendTXT(PING_DATA);
		Serial.printf("\r\n[device->server] send ping");
		counter = 0;
		ping_return++;
		if(ping_return >= 5) {
			ESP.reset();
		}
	}
	//實體按鈕
	if(digitalRead(Input_2) == LOW) {
		int btn_count = 0;
		while(digitalRead(Input_2) == LOW) {
			btn_count++;
			delay(2);
			if(btn_count > 5) {
				if(is_switch()){
					switch_off();
					webSocket.sendTXT(OFF_DATA, 15);
					Serial.printf("\r\n[device->server] send status off");
				} else {
					switch_on();
					webSocket.sendTXT(ON_DATA, 15);
					Serial.printf("\r\n[device->server] send status on");
				}
				break;
			}
		}
		while(digitalRead(Input_2) == LOW) {
			delay(100);
		}
	}
}
/***************** 以下都是繼電器控制  *****************/
bool is_switch() {
	if(EEPROM.read(101) == 0x00){		//已經開
		return true;
	} else {
		return false;
	}
}

void switch_on() {
	Serial.printf("\r\n[SWITCH] ON.");
	EEPROM.write(101, 0x00);	//flag
	EEPROM.commit();
	digitalWrite(Output_1, HIGH);
}

void switch_off() {
	Serial.printf("\r\n[SWITCH] OFF.");
	EEPROM.write(101, 0x01);	//flag
	EEPROM.commit();
	digitalWrite(Output_1, LOW);
}

void switch_toggle() {
	if(is_switch()) {
		switch_off();
	} else {
		switch_on();
	}
}

/***************** 以下都是WIFI SOFTAP *****************/
bool is_epassf() {
	if(EEPROM.read(100) == 0x00){		//已經設定完成
		return true;
	}
	return false;
}

bool set_sta_wifi(void) {
	if(reboot_try > 10){
		ESP.reset();
	}
	if ( eeprom_ssid.length() > 1) {
		eeprom_ssid = eeprom_ssid.c_str();
		eeprom_pass = eeprom_pass.c_str();
		//eeprom_encrypt = eeprom_encrypt.c_int();
		WiFi.disconnect();
		if(eeprom_encrypt == 8){
			WiFi.begin(eeprom_ssid.c_str());
		}else{
			WiFi.begin(eeprom_ssid.c_str(), eeprom_pass.c_str());
		}
		int y = 0;
		while(y < 100){
			y++;
			server.handleClient();
			delay(100);
			if (WiFi.status() == WL_CONNECTED) {
				//Serial.print("\r\n[STA] connect success.");
				reboot_try = 0;
				reboot_flag = 0;
				return true;
			}
		}
	}
	reboot_try++;
	//Serial.printf("\r\n[STA] connect fail(%d).", reboot_try);
	return false;
}

void read_ep_wifi(void) {
	EEPROM.begin(512);
	delay(10);
	eeprom_ssid = "";
	for (int i = 0; i < 32; ++i) {
		if(EEPROM.read(i) == 0x00){
			break;
		}
		eeprom_ssid += char(EEPROM.read(i));
	}
	eeprom_encrypt = EEPROM.read(99);
	
	eeprom_pass = "";
	for (int i = 32; i < 96; ++i) {
		if(EEPROM.read(i) == 0x00){
			break;
		}
		eeprom_pass += char(EEPROM.read(i));
	}
	delay(10);
	eeprom_ssid.trim();
	eeprom_pass.trim();
}

void get_uuid(void) {
	// 取得裝置SSID
	WiFi.macAddress(MAC_ADDR);
	for (int i = 0; i < sizeof(MAC_ADDR); ++i) {
		sprintf(UUID, "%s%02X", UUID, MAC_ADDR[i]);
	}
}

void renew_ip_addr(void) {
	//AP
	IPAddress AP_IP_IA = WiFi.softAPIP();
	AP_IP = String(AP_IP_IA[0]) + '.' + String(AP_IP_IA[1]) + '.' + String(AP_IP_IA[2]) + '.' + String(AP_IP_IA[3]);
	if(AP_IP != "0.0.0.0") {
		Serial.printf("\r\n[AP] connected, ip = %s", AP_IP.c_str());
	} else {
		Serial.print("\r\n[AP] not connect.");
	}
	
	//STA
	IPAddress STA_IP_IA = WiFi.localIP();
	STA_IP = String(STA_IP_IA[0]) + '.' + String(STA_IP_IA[1]) + '.' + String(STA_IP_IA[2]) + '.' + String(STA_IP_IA[3]);
	if(STA_IP != "0.0.0.0") {
		Serial.printf("\r\n[STA] connected, ip = %s", STA_IP.c_str());
	} else {
		Serial.print("\r\n[STA] not connect.");
	}
}

void launchWeb(void) {
	renew_ip_addr();
	createWebServer();
	server.begin();
}

void setupAP(void) {
	WiFi.mode(WIFI_AP);
	//開啟AP模式 - Default:WPA2
	WiFi.softAP(softap_ssid, softap_pass);
	IPAddress ip = WiFi.localIP();
	server = ESP8266WebServer(ip, 80);
	//MDNS
	if (!MDNS.begin("config")) {
		Serial.println("Error setting up MDNS responder!");
		while(1) { 
			delay(1000);
		}
	}
	MDNS.setInstanceName("Config Mode");
	MDNS.addService("http", "tcp", 80);
	//WEB
	launchWeb();
}

void createWebServer(void) {
	server.on("/f.js", []() {
		server.send(200, "application/javascript", SCRIPT);
	});
	server.on("/wifi.json", []() {
		int n = WiFi.scanNetworks();
		String content = "";
		content += "[\r\n";
		for (int i = 0; i < n; ++i) {
			// Print SSID and RSSI for each network found
			content += "\t{\"ssid\": \"";
			content += WiFi.SSID(i);
			content += "\", \"rssi\": ";
			content += WiFi.RSSI(i);
			content += ", \"encrypt\": ";
			content += WiFi.encryptionType(i);
			if(i == n - 1) {
				content += "}\r\n";
			} else {
				content += "},\r\n";
			}
		}
		content += "]";
		server.send(200, "text/plain", content);
	});
	server.on("/setting.json", []() {
		//更新IP
		renew_ip_addr();
		WiFi.macAddress(MAC_ADDR);
		String content = "";
		content += "{\r\n";
		content += "\t\"uuid\": \"" + String(UUID) + "\",\r\n";
		content += "\t\"apip\": \"" + AP_IP + "\",\r\n";
		content += "\t\"ssid\": \"" + eeprom_ssid + "\",\r\n";
		content += "\t\"pass\": \"" + eeprom_pass + "\",\r\n";
		if(eeprom_encrypt == 8){
			content += "\t\"encrypt\": \"8\"\r\n";
		}else{
			content += "\t\"encrypt\": \"4\"\r\n";
		}
		content += "}";
		server.send(200, "text/plain", content);
	});
	server.on("/style.css", []() {
		server.send(200, "text/css", STYLE);
	});
	server.on("/", []() {
		server.send(200, "text/html", INDEX);
	});
	server.on("/setting", []() {
		server.send(200, "text/html", SAVE);
		//儲存到EEPROM
		String qsid = server.arg("ssid");
		String qpass = server.arg("pass");
		int qenpt = server.arg("wifi_encrypt").toInt();
		eeprom_ssid = qsid;
		eeprom_pass = qpass;
		eeprom_encrypt = qenpt;
		if (qsid.length() > 0) {
			EEPROM.write(100, 0x01);	//flag
			//儲存加密方式
			if(qenpt == 8){
				EEPROM.write(99, 0x08);	//wifi encrypt open
			}else{
				EEPROM.write(99, 0x04);	//wifi encrypt wpa2-aes
			}
			//SSID
			for (int i = 0; i < qsid.length(); ++i) {
				EEPROM.write(i, qsid[i]);
			}
			EEPROM.write(qsid.length(),0x00);
			//密碼
			for (int i = 0; i < qpass.length(); ++i) {
				EEPROM.write(32 + i, qpass[i]);
			}
			EEPROM.write(32 + qpass.length(),0x00);
			EEPROM.write(100, 0x00);	//flag
			EEPROM.commit();
			is_epass = true;
		}
		//Serial.printf("\r\n[WEB] save setting success.");
		reboot_flag = 1;
		//重新開機
		ESP.reset();
		set_sta_wifi();
	});
	server.on("/reboot", []() {
		server.send(200, "text/html", REBOOT);
		//重新開機
		ESP.reset();
	});
}