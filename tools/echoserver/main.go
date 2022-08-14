package main

import (
	"io"
	"log"
	"net/http"
	"net/http/httputil"
	"os"
	"strconv"
	"strings"
	"time"
)

func main() {
	http.HandleFunc("/", root)

	// Get desired port from environment variable
	port := os.Getenv("ECHOSERVER_PORT")
	if port == "" {
		port = "80"
	}

	log.Fatal(http.ListenAndServe(":"+port, nil))
}

// Listen all requests and produce desirable behavior
func root(w http.ResponseWriter, r *http.Request) {

	log.Println("Got request")
	req, err := httputil.DumpRequest(r, true)
	if err != nil {
		log.Fatal(err)
	}

	log.Println(string(req))

	// Sleeping if need so
	sleepFromQuery(r.URL.Query().Get("sleep"))

	// Find out what we need to return
	returnValue := resolveReturnValue(r.URL.Query().Get("return"), r.URL.Query().Get("payloadsize"))

	io.WriteString(w, returnValue)
	log.Println("Response sent")
}

// Resolve return value from query param values
func resolveReturnValue(value string, payloadSizeString string) string {
	payloadSize, err := strconv.Atoi(payloadSizeString)
	if err == nil {
		value = strings.Repeat("0", payloadSize)
	}

	return value
}

// Sleep if there is value for query's param
func sleepFromQuery(timeParam string) {
	sleepTime, err := strconv.Atoi(timeParam)
	if err == nil {
		log.Printf("Sleeping for %d", sleepTime)
		time.Sleep(time.Duration(sleepTime) * time.Second)
	}
}
