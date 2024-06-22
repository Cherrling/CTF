package main

import "demo03/router"

func main() {
	r := router.Router()

	err := r.Run(":8081")
	if err != nil {
		return
	}

}
