package controller

import (
	"crypto/rand"
	_ "embed"
	"github.com/gin-gonic/gin"
	"github.com/tjfoc/gmsm/x509"
	"net/http"
)

type EncryptController struct {
}

func Encrypt(plainText []byte) []byte {

	publicKeyFromPem, err := x509.ReadPublicKeyFromPem(pub)
	if err != nil {
		panic(err)
	}
	cipherText, err := publicKeyFromPem.EncryptAsn1(plainText, rand.Reader)
	if err != nil {
		panic(err)
	}
	return cipherText
}

var pub = []byte(`
-----BEGIN PUBLIC KEY-----
MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAE3xqu+AwSgmeQnsVflwUSDnjxPkjC
SiD+xllUCJ3UkfGmLII/LZ2FS3gJe4o6PGXZEWiIZz4eb4brd1xlXkrleQ==
-----END PUBLIC KEY-----
`)
var pri = []byte(`
-----BEGIN PRIVATE KEY-----
MIGTAgEAMBMGByqGSM49AgEGCCqBHM9VAYItBHkwdwIBAQQglNntSZVhLqSWzuKw
Z2CwSfSCNI8lQm0sS0Kvh8dOxG+gCgYIKoEcz1UBgi2hRANCAATfGq74DBKCZ5Ce
xV+XBRIOePE+SMJKIP7GWVQIndSR8aYsgj8tnYVLeAl7ijo8ZdkRaIhnPh5vhut3
XGVeSuV5
-----END PRIVATE KEY-----
`)

func (e EncryptController) Encrypt(r *gin.Context) {
	flag := []byte{48, 125, 2, 33, 0, 238, 212, 154, 134, 255, 91, 109, 210, 231, 242, 184, 9, 103, 26, 30, 241, 93, 242, 68, 119, 148, 9, 21, 5, 241, 175, 203, 3, 152, 63, 85, 82, 2, 32, 2, 156, 154, 131, 146, 194, 242, 200, 19, 109, 209, 151, 90, 252, 165, 49, 247, 141, 208, 219, 117, 226, 91, 113, 225, 0, 33, 162, 19, 87, 49, 68, 4, 32, 213, 16, 18, 177, 119, 110, 74, 6, 147, 235, 85, 0, 61, 4, 1, 43, 107, 207, 249, 37, 195, 141, 141, 23, 244, 159, 235, 159, 169, 243, 160, 37, 4, 20, 179, 67, 236, 205, 121, 146, 216, 75, 168, 197, 214, 34, 63, 138, 237, 247, 166, 117, 246, 210}
	r.JSON(http.StatusOK, flag)
}
