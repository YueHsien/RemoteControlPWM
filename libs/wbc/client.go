package wbc

import (
	"math/rand"
	"golang.org/x/net/websocket"
)

//MAP結構
type CLIENT struct {
	WS *websocket.Conn
	UUID string
	DEVS []interface{}
}

//MAP新增
func APPEND(clients []CLIENT, client CLIENT) []CLIENT {
	var exist int = 0
	for v := range clients {
		if clients[v].UUID == client.UUID || clients[v].WS == client.WS {
			clients = UPDATE(clients, client)
			exist = 1
			break
		}
	}
	if exist == 0 {
		return append(clients, client)
	} else {
		return clients
	}
}

//MAP刪除
func DELETE(clients []CLIENT, WS *websocket.Conn) []CLIENT {
	for v := range clients {
		if clients[v].WS == WS {
			clients = clients[:v+copy(clients[v:], clients[v+1:])]
			break
		}
	}
	return clients
}

//MAP更新
func UPDATE(clients []CLIENT, client CLIENT) []CLIENT {
	for v := range clients {
		if clients[v].UUID == client.UUID || clients[v].WS == client.WS{
			clients[v]= client
			break
		}
	}
	return clients
}

//JSON傳送給用戶
func SEND_JSON(clients[]CLIENT, UUID string, MM map[string]interface{}) bool {
	for v := range clients {
		if clients[v].UUID == UUID {
			websocket.JSON.Send(clients[v].WS, MM)
			break
		}
	}
	return false
}

//JSON傳送給ALL用戶
func SEND_ALL_JSON(clients[]CLIENT, MM map[string]interface{}) bool {
	for v := range clients {
		websocket.JSON.Send(clients[v].WS, MM)
	}
	return false
}

//BYTE傳送給用戶
func SEND_BYTE(clients[]CLIENT, UUID string, HH string) bool {
	for v := range clients {
		if clients[v].UUID == UUID {
			websocket.Message.Send(clients[v].WS, HH)
			break
		}
	}
	return false
}

func TURN_ON(clients []CLIENT, UUID string) {
	hexstr := "FFFC00F0"
	hex := HEXSTR_TO_BYTE(hexstr)
	for v := range clients {
		if clients[v].UUID == UUID {
			websocket.Message.Send(clients[v].WS, hex)
			break
		}
	}
}
func TURN_OFF(clients []CLIENT, UUID string) {
	hexstr := "FFFC00F1"
	hex := HEXSTR_TO_BYTE(hexstr)
	for v := range clients {
		if clients[v].UUID == UUID {
			websocket.Message.Send(clients[v].WS, hex)
			break
		}
	}
}

//SESSION ID產生
func RANDID(len_t int) string {
	letters := []rune("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_")
	b := make([]rune, len_t)
	for i := range b {
		b[i] = letters[rand.Intn(len(letters))]
	}
	return string(b)
}