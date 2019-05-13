package wbc

import (
	"fmt"
	"encoding/hex"
	"strconv"
)

func DoubleHexstrToHexstr (data_str string) string {
	//把雙符號合併成單符號 [F0 C0 F0 A0] = [FCFA]
	data_hexstr_origin := fmt.Sprintf("%X", data_str)
	var data_hexstr string = ""
	for i:=0; i<len(data_hexstr_origin); i++ {
		if i %2 != 0 {
			data_hexstr = data_hexstr + string(data_hexstr_origin[i])
		}
	}
	return data_hexstr
}
func HEXSTR_TO_BYTE (hexstr string) []byte {
	//HEX STRING 轉 []byte
	bs,_ := hex.DecodeString(hexstr)
	return bs
}
func HEXSTR_TO_INT32 (hexstr string) int32 {
	n, err := strconv.ParseUint(hexstr, 16, 32)
	if err != nil {
		panic(err)
	}
	return int32(n)
}