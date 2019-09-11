from bs4 import BeautifulSoup
import requests
import pymysql

def get_content(url):
    headers = {
        'User_Agent':'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
        'Referer':'http://www.xicidaili.com/nn/1',
        'Host':'hm.baidu.com',
    }
    html = requests.get(url = url,headers = headers).content.decode('utf-8')
    print(html)

if __name__ == '__main__':
    for i in range(1,10):
        url = 'http://www.xicidaili.com/nn/%d' % (i)
        get_content(url)
        break