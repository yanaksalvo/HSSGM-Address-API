import requests
import time
import json
import base64

api_key = "" # capmonster api key

image_path = "hss.jpg"

def create_captcha_task(api_key, image_path):
    url = "https://api.capmonster.cloud/createTask"
    headers = {"Content-Type": "application/json"}

    with open(image_path, "rb") as image_file:
        image_data = image_file.read()
        image_base64 = base64.b64encode(image_data).decode("utf-8")

    data = {
        "clientKey": api_key,
        "task": {
            "type": "ImageToTextTask",
            "body": image_base64
        }
    }

    response = requests.post(url, headers=headers, json=data)

    if response.status_code == 200:
        result = response.json()
        if result.get("errorId") == 0:
            return result.get("taskId")
        else:
            print("Hata: ", result.get("errorDescription"))
    else:
        print("HTTP Hatası: ", response.status_code)
    return None

def get_captcha_result(api_key, task_id):
    url = "https://api.capmonster.cloud/getTaskResult"
    data = {
        "clientKey": api_key,
        "taskId": task_id
    }

    while True:
        response = requests.post(url, json=data)
        if response.status_code == 200:
            result = response.json()
            if result.get("status") == "ready":
                return {
                    "status": "SOLVED",
                    "solver": result["solution"]["text"]
                }
            elif result.get("status") == "processing":
                time.sleep(5)  
        else:
            print("Sonuç alma hatası: ", response.status_code)
            break

task_id = create_captcha_task(api_key, image_path)

if task_id:
    result = get_captcha_result(api_key, task_id)
    if result:
        print(json.dumps(result, indent=4))
