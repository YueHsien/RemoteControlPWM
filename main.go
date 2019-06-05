package main

import (
	"log"
	"fmt"
	"time"
	"./libs/wbc"
	"./libs/wsc"
	"net/http"
	"encoding/json"
	"golang.org/x/net/websocket"
	"strconv"
)

var hw_list []wsc.CLIENT	//上線裝置
var wb_list []wbc.CLIENT	//上線瀏覽器

func main() {
	go ws_server(1025)
	for {
		time.Sleep(1 * time.Second)
		//log.Printf("hw=%d, wb=%d", len(hw_list), len(wb_list))
	}
}

func ws_server(port int64) {
	http.HandleFunc("/",
	func (w http.ResponseWriter, req *http.Request) {
		s := websocket.Server{Handler: websocket.Handler(wsconn)}
		s.ServeHTTP(w, req)
	});
	//start
	port_scf := fmt.Sprintf(":%d", port)
	log.Printf("Server is Start on port : %d", port)
	err := http.ListenAndServe(port_scf, nil)
	if err != nil {
		log.Fatal("ListenAndServe: ", err)
	}
}

func wsconn(ws *websocket.Conn) {
	for {
		//40秒沒有回應就刪除LIST
		timeoutDuration := 40 * time.Second
		ws.SetReadDeadline(time.Now().Add(timeoutDuration))
		var data_byte []byte
		if (websocket.Message.Receive(ws, &data_byte) != nil) {
			//log.Printf("Server 斷線#1")
			defer ws.Close()
			break
		}
		if datahandle(ws, data_byte) != nil {
			//log.Printf("Server 斷線#2")
			defer ws.Close()
			break
		}
	}
	hw_list = wsc.DELETE(hw_list, ws)
	wb_list = wbc.DELETE(wb_list, ws)
	sendLIST()
}

func isJSON(s string) bool {
	var js map[string]interface{}
	return json.Unmarshal([]byte(s), &js) == nil
}

func sendSTATUS(uuid string, status string) {
	if len(wb_list) > 0 {
		for n := range wb_list {
			devs := wb_list[n].DEVS
			for o := range hw_list {
				for k := range devs {
					if hw_list[o].UUID == devs[k] && hw_list[o].UUID == uuid {
						rt := map[string]interface{} {
							"func": "send_wb_status",
							"uuid": uuid,
							"status": status,
						}
						wbc.SEND_JSON(wb_list, wb_list[n].UUID, rt)
						break;
					}
				}
			}
		}
	}
}

func sendLIST() {
	if len(wb_list) > 0 {
		for v := range wb_list {
			devs := wb_list[v].DEVS
			var m []map[string]interface{}
			for v := range hw_list {
				for k := range devs {
					if hw_list[v].UUID == devs[k] {
						m = append(m, map[string]interface{} {
							"uuid": hw_list[v].UUID,
							"status": hw_list[v].STATUS,
						})
						break;
					}
				}
			}
			if len(m) > 0 {
				rt := map[string]interface{} {
					"func": "send_wb_refrush",
					"data": m,
					"status": 100,
				}
				//log.Println(rt)
				wbc.SEND_JSON(wb_list, wb_list[v].UUID, rt)
			} else {
				rt := map[string]interface{} {
					"func": "send_wb_refrush",
					"status": 100,
				}
				//log.Println(rt)
				wbc.SEND_JSON(wb_list, wb_list[v].UUID, rt)
			}
			
		}
	}
}

func datahandle(ws *websocket.Conn, data_byte []byte) error {
	data_str := string(data_byte[:])
	if(isJSON(data_str)) {
		//is json string
		var data_json map[string]interface{}
		json.Unmarshal([]byte(data_str), &data_json)
		go json_handle(ws, data_json)
	} else {
		//is binary
		var data_hexstr string = fmt.Sprintf("%X", data_byte)
		//log.Printf("[%v] -> byte -> [%v]\n", session_id, data_hexstr)
		go hex_handle(ws, data_hexstr)
	}
	return nil
}

func json_handle(ws *websocket.Conn, j map[string]interface{}) {
	if j["func"] == "wb_register" {
		//fmt.Println(j["data"])
		devs := j["data"].([]interface{})
		//for k := range devs {
		//	log.Printf("key[%s] value[%s]\n", k, devs[k])
		//}
		//log.Printf("devs len = %d", len(devs))
		session_id := wbc.RANDID(8)
		wb_list = wbc.APPEND(wb_list, wbc.CLIENT{ws, session_id, devs})
		sendLIST()
	} else if j["func"] == "wb_switch" {
		target, _ := j["uuid"].(string)
		status, _ := j["status"].(string)
		i2, err := strconv.ParseInt(status, 10, 64)
		if err == nil {
			fmt.Println(i2)
		}
			wsc.TURN_ON(hw_list, target,i2)
			log.Printf("target=%s",target)
			log.Printf("status=%s",status)
			//go linebot("[APW]遠端關閉電源【" + target + "】")
		
		log.Printf("send switch uuid = %s, switch = %s", target, status)
	}
}

func hex_handle(ws *websocket.Conn, data_hexstr string) {
	if(len(data_hexstr) >= 30) {
		var head string = data_hexstr[0:8]
		var uuid string = data_hexstr[8:20]
		var data string = data_hexstr[20:22]
		var foot string = data_hexstr[22:30]
		if(head == "F0FCFFFF" && data == "FF" && foot == "F1FDFFFF") {
			log.Printf("註冊 [%s]\n", uuid)
			hw_list = wsc.APPEND(hw_list, wsc.CLIENT{ws, uuid, "none"})
			sendLIST()
		} else if(head == "F0FCFFFF" && data == "F0" && foot == "F1FDFFFF") {
			log.Printf("開啟 [%s]\n", uuid)
			log.Printf("data=%s",data)
			log.Printf("All data=",data_hexstr)
			hw_list = wsc.UPDATE(hw_list, wsc.CLIENT{ws, uuid, "on"})
			//sendLIST()
			
			sendSTATUS(uuid, "on")
			//go linebot("[APW]電燈被開啟！")
		} else if(head == "F0FCFFFF" && data == "F1" && foot == "F1FDFFFF") {
			log.Printf("關閉 [%s]\n", uuid)
			log.Printf("data=%s",data)
			hw_list = wsc.UPDATE(hw_list, wsc.CLIENT{ws, uuid, "off"})
			//sendLIST()
			log.Printf("All data=",data_hexstr)
			sendSTATUS(uuid, "off")
			//go linebot("[APW]電燈被關閉！")
		} else if(head == "F0FCFFFF" && data == "F4" && foot == "F1FDFFFF") {
			//收到來自裝置的PING
			//log.Printf("PING [%s]\n", uuid)
			wsc.PING(hw_list, uuid)
		}
	}
}
