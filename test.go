package main

import (
    "fmt"
)

func main() {

    i := 255
	h := fmt.Sprintf("%X", i)
	fmt.Printf("HEX conv of '%d' is '%s'\n", i, h)
}