import subprocess
import schedule
import time

def run_js_file():
    try:
        subprocess.run(["python", "adres-cookie.py"], check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        print("Cookiler kayıt edildi")
        
    except subprocess.CalledProcessError:
        print("Hata oluştu, işlem başarısız")


schedule.every(15).minutes.do(run_js_file)

while True:
    schedule.run_pending()
    time.sleep(1)
