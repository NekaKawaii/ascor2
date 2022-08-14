package main

import (
	"bytes"
	"encoding/json"
	"io/ioutil"
	"log"
	"math/rand"
	"net/http"
	"net/url"
	"strconv"
	"strings"
	"time"
)

var rng *rand.Rand

func main() {
	rng = rand.New(rand.NewSource(time.Now().Unix())) // initialize local pseudorandom generator
	// Номер итерации для дифференцирования при построении запросов
	iter := 1

	for {
		// Запускаем запрос в горутине, чтобы время его выполнения не влияло на паузы между запросами
		// Или для создания множества одновременных запросов
		go doRequest(iter)
		iter = iter + 1

		// Спим немного между созданием запросов
		time.Sleep(500 * time.Millisecond)
	}
}

func doRequest(blankId int) {
	// OTP
	bankSlug := "otp"
	// Случайный банк из списка
	//bankSlug := banks[rng.Intn(len(banks))]

	bankWouldReturn := "<root><Opty_Id>" + strconv.Itoa(blankId) + "</Opty_Id></root>"

	// Тело запроса на Async Scoring
	values := map[string]interface{}{
		"bank":    bankSlug,
		"blankId": blankId,
		"request": map[string]interface{}{
			"url":  "http://echoserver/?return=" + url.QueryEscape(bankWouldReturn),
			"body": strings.Repeat("0", 1024*1024*30),
			"metadata": map[string]interface{}{
				"action": "send",
			},
		},
		"callback": map[string]interface{}{
			"url": "http://",
		},
	}

	// Формируем JSON строку
	jsonData, err := json.Marshal(values)

	if err != nil {
		log.Fatal(err)
	}

	// Начинаем замер времени
	startTime := time.Now()

	// Делаем запрос на async scoring
	response, err := http.Post("http://nginx:80/request/create", "application/json", bytes.NewBuffer(jsonData))

	// Замеряем
	elapsedTime := time.Since(startTime).Seconds()

	if err != nil {
		log.Fatal(err)
	}

	// Читаем тело ответа
	defer response.Body.Close()
	body, err := ioutil.ReadAll(response.Body)

	if err != nil {
		log.Fatal(err)
	}

	log.Printf("Got response (%v s) for %d: %s", elapsedTime, blankId, body)
}
