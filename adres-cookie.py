import time
import json
import requests
import subprocess
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def create_driver():
    options = webdriver.ChromeOptions()
    #options.add_argument('--headless')  
    return webdriver.Chrome(options=options)

while True:
    driver = create_driver()
    
    try:
        driver.get("https://interaktif.hssgm.gov.tr/Login.aspx")

        username_input = driver.find_element(By.NAME, "txtKullaniciAdi")
        password_input = driver.find_element(By.NAME, "txtSifre")

        username_input.send_keys("") # username
        password_input.send_keys("") # password

        captcha_image = driver.find_element(By.XPATH, '//*[@src="img/captcha.png"]')
        captcha_image.screenshot('hss.jpg') 

        result = subprocess.run(['python', 'cap.py'], capture_output=True, text=True)

        captcha_result = json.loads(result.stdout)
        captcha_solution = captcha_result.get('solver')

        captcha_input = driver.find_element(By.NAME, "txtDogrulama")
        captcha_input.send_keys(captcha_solution)

        login_button = driver.find_element(By.ID, "btnGiris")
        login_button.click()

        time.sleep(2)

        try:
            error_message = driver.find_element(By.XPATH, "//*[contains(text(), 'Doğrulama Kodu Yanlış')]")
            print("Doğrulama Kodu Yanlış, tekrar denenecek...")
            driver.quit() 

        except Exception as e:
            print("Giriş başarılı.")
            gemi = driver.find_element(By.ID, "rptModulListesi_lnkSideButton_2")
            gemi.click()
            
            time.sleep(2)

            cookies = driver.get_cookies()
            with open('cookies.json', 'w', encoding='utf-8') as cookie_file:
                json.dump(cookies, cookie_file, ensure_ascii=False, indent=4)

            break  

    except Exception as e:
        print(f"Hata: {e}")
        driver.quit()  

