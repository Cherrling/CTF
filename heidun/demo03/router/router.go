package router

import (
	"demo03/controller"
	"github.com/gin-gonic/gin"
)

func Router() *gin.Engine {
	r := gin.Default()

	enc := r.Group("/")
	{
		enc.GET("/encrypt", controller.EncryptController{}.Encrypt)
	}

	return r
}
