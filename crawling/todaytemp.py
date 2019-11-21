from bs4 import BeautifulSoup
from urllib.request import urlopen
from urllib import parse
from time import sleep
import requests
import urllib.request
 

url="https://m.search.naver.com/search.naver?query=%EC%84%B8%EC%A2%85+%EB%82%A0%EC%94%A8&sm=mtb_hty.top&where=m&oquery=%EC%A1%B0%EC%B9%98%EC%9B%90+%EB%82%A0%EC%94%A8&tqi=Ujrbmlp0JWlss6C48Z0ssssss28-353701"
req=urlopen(url)
html=req.read()
soup=BeautifulSoup(html,'html.parser')
temp=soup.find('em','figure degree_code')
print(temp.get_text())
